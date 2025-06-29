<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RegistrationApproval;
use App\Models\User;
use App\Models\Company;
use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class RegistrationApprovalController extends Controller
{
    /**
     * Display a listing of pending registrations.
     */
    public function index(Request $request)
    {
        $query = RegistrationApproval::with(['user', 'reviewer'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'pending'); // Default to pending
        }

        // Filter by user type
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('user_type', $request->type);
        }

        $registrations = $query->paginate(15);

        return view('admin.registrations.index', compact('registrations'));
    }

    /**
     * Display the specified registration for review.
     */
    public function show($id)
    {
        $registration = RegistrationApproval::with(['user', 'reviewer'])
            ->findOrFail($id);

        // Load related data based on user type
        if ($registration->user_type === 'vendor') {
            $registration->load(['company']);
        } else {
            $registration->load(['provider']);
        }

        return view('admin.registrations.show', compact('registration'));
    }

    /**
     * Approve a registration.
     */
    public function approve(Request $request, $id)
    {
        $request->validate([
            'admin_message' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $registration = RegistrationApproval::findOrFail($id);

            if ($registration->status !== 'pending') {
                return redirect()->back()->with('error', 'This registration has already been reviewed.');
            }

            // Update user status to active
            $user = $registration->user;
            $user->update(['status' => 'active']);

            // Update company/provider status to active
            if ($registration->user_type === 'vendor') {
                $company = Company::where('user_id', $user->id)->first();
                if ($company) {
                    $company->update(['status' => 'active']);
                }
            } else {
                $provider = Provider::where('user_id', $user->id)->first();
                if ($provider) {
                    $provider->update(['status' => 'active', 'is_verified' => true]);
                }
            }

            // Update registration approval record
            $registration->update([
                'status' => 'approved',
                'admin_message' => $request->admin_message,
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            DB::commit();

            // Send approval email
            $this->sendApprovalEmail($registration, 'approved');

            return redirect()->route('admin.registrations.index')
                ->with('success', 'Registration approved successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Registration approval failed: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to approve registration. Please try again.');
        }
    }

    /**
     * Decline a registration.
     */
    public function decline(Request $request, $id)
    {
        $request->validate([
            'admin_message' => 'required|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $registration = RegistrationApproval::findOrFail($id);

            if ($registration->status !== 'pending') {
                return redirect()->back()->with('error', 'This registration has already been reviewed.');
            }

            // Update user status to declined
            $user = $registration->user;
            $user->update(['status' => 'declined']);

            // Update company/provider status to declined
            if ($registration->user_type === 'vendor') {
                $company = Company::where('user_id', $user->id)->first();
                if ($company) {
                    $company->update(['status' => 'declined']);
                }
            } else {
                $provider = Provider::where('user_id', $user->id)->first();
                if ($provider) {
                    $provider->update(['status' => 'declined']);
                }
            }

            // Update registration approval record
            $registration->update([
                'status' => 'declined',
                'admin_message' => $request->admin_message,
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            DB::commit();

            // Send decline email
            $this->sendApprovalEmail($registration, 'declined');

            return redirect()->route('admin.registrations.index')
                ->with('success', 'Registration declined successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Registration decline failed: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to decline registration. Please try again.');
        }
    }

    /**
     * Download license file.
     */
    public function downloadLicense($id)
    {
        $registration = RegistrationApproval::findOrFail($id);

        if (!$registration->license_file_path) {
            return redirect()->back()->with('error', 'No license file found.');
        }

        $filePath = storage_path('app/public/' . $registration->license_file_path);

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'License file not found.');
        }

        return response()->download($filePath);
    }

    /**
     * Send approval/decline email notification.
     */
    private function sendApprovalEmail($registration, $decision)
    {
        try {
            $user = $registration->user;
            $userType = ucfirst($registration->user_type);

            $subject = $decision === 'approved'
                ? "Your {$userType} Registration Has Been Approved!"
                : "Your {$userType} Registration Update";

            $emailData = [
                'user' => $user,
                'registration' => $registration,
                'decision' => $decision,
                'userType' => $userType,
                'adminMessage' => $registration->admin_message,
                'reviewedBy' => $registration->reviewer->name ?? 'Admin Team',
            ];

            // Send email using Laravel's Mail facade
            Mail::send('emails.registration-decision', $emailData, function ($message) use ($user, $subject) {
                $message->to($user->email, $user->name)
                        ->subject($subject);
            });

        } catch (\Exception $e) {
            Log::error('Failed to send registration email: ' . $e->getMessage());
        }
    }

    /**
     * Get registration statistics for dashboard.
     */
    public function stats()
    {
        $stats = [
            'pending' => RegistrationApproval::pending()->count(),
            'approved' => RegistrationApproval::approved()->count(),
            'declined' => RegistrationApproval::declined()->count(),
            'vendors_pending' => RegistrationApproval::pending()->vendors()->count(),
            'providers_pending' => RegistrationApproval::pending()->providers()->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Bulk approve registrations.
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'registration_ids' => 'required|array',
            'registration_ids.*' => 'exists:registration_approvals,id',
            'admin_message' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $registrations = RegistrationApproval::whereIn('id', $request->registration_ids)
                ->where('status', 'pending')
                ->get();

            foreach ($registrations as $registration) {
                // Update user status
                $registration->user->update(['status' => 'active']);

                // Update company/provider status
                if ($registration->user_type === 'vendor') {
                    $company = Company::where('user_id', $registration->user_id)->first();
                    if ($company) {
                        $company->update(['status' => 'active']);
                    }
                } else {
                    $provider = Provider::where('user_id', $registration->user_id)->first();
                    if ($provider) {
                        $provider->update(['status' => 'active', 'is_verified' => true]);
                    }
                }

                // Update registration record
                $registration->update([
                    'status' => 'approved',
                    'admin_message' => $request->admin_message,
                    'reviewed_by' => Auth::id(),
                    'reviewed_at' => now(),
                ]);

                // Send email
                $this->sendApprovalEmail($registration, 'approved');
            }

            DB::commit();

            return redirect()->route('admin.registrations.index')
                ->with('success', count($registrations) . ' registrations approved successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk approval failed: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to approve registrations. Please try again.');
        }
    }
}
