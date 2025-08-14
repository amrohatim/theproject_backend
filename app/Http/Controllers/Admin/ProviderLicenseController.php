<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\User;
use App\Services\LicenseManagementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProviderLicenseController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // Middleware is applied in the routes file, not in the controller
    }

    /**
     * Display a listing of provider licenses pending review.
     */
    public function index(Request $request)
    {
        $query = License::with(['user', 'user.provider'])
            ->whereHas('user', function($q) {
                $q->where('role', 'provider');
            })
            ->orderBy('created_at', 'desc');

        // Filter by status
        $status = $request->get('status', 'pending');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $licenses = $query->paginate(15);

        return view('admin.provider-licenses.index', compact('licenses', 'status'));
    }

    /**
     * Show the details of a specific provider license.
     */
    public function show($id)
    {
        try {
            $license = License::with(['user', 'user.provider'])->findOrFail($id);

            // Log for debugging
            Log::info('License found', [
                'license_id' => $license->id,
                'user_id' => $license->user_id,
                'user_role' => $license->user->role ?? 'no_role',
                'user_name' => $license->user->name ?? 'no_name'
            ]);

            // Ensure this is a provider license
            if ($license->user->role !== 'provider') {
                Log::warning('License user is not a provider', [
                    'license_id' => $id,
                    'user_role' => $license->user->role
                ]);
                abort(404, 'Provider license not found.');
            }

            return view('admin.provider-licenses.show', compact('license'));
        } catch (\Exception $e) {
            Log::error('Error showing provider license', [
                'license_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            abort(404, 'License not found.');
        }
    }

    /**
     * Approve a provider license.
     */
    public function approve(Request $request, $id, LicenseManagementService $licenseService)
    {
        $request->validate([
            'admin_message' => 'nullable|string|max:1000',
        ]);

        $license = License::findOrFail($id);

        // Ensure this is a provider license
        if ($license->user->role !== 'provider') {
            abort(404, 'Provider license not found.');
        }

        if ($license->status === 'active') {
            return redirect()->back()->with('error', 'This license has already been approved.');
        }

        if ($licenseService->approveProviderLicense($license, Auth::user(), $request->admin_message)) {
            return redirect()->route('admin.provider-licenses.index')
                ->with('success', "License approved successfully for {$license->user->name}!");
        } else {
            return redirect()->back()
                ->with('error', 'Failed to approve license. Please try again.');
        }
    }

    /**
     * Reject a provider license.
     */
    public function reject(Request $request, $id, LicenseManagementService $licenseService)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $license = License::findOrFail($id);

        // Ensure this is a provider license
        if ($license->user->role !== 'provider') {
            abort(404, 'Provider license not found.');
        }

        if ($license->status === 'rejected') {
            return redirect()->back()->with('error', 'This license has already been rejected.');
        }

        if ($licenseService->rejectProviderLicense($license, Auth::user(), $request->rejection_reason)) {
            // Redirect to admin choice page instead of directly back to index
            return redirect()->route('admin.provider-licenses.post-rejection-choice', [
                'license' => $license->id,
                'user' => $license->user_id
            ])->with('success', "License rejected for {$license->user->name}. Email notification sent to the provider.");
        } else {
            return redirect()->back()
                ->with('error', 'Failed to reject license. Please try again.');
        }
    }

    /**
     * Bulk approve multiple licenses.
     */
    public function bulkApprove(Request $request, LicenseManagementService $licenseService)
    {
        $request->validate([
            'license_ids' => 'required|array',
            'license_ids.*' => 'exists:licenses,id',
            'admin_message' => 'nullable|string|max:1000',
        ]);

        $results = $licenseService->bulkApproveProviderLicenses(
            $request->license_ids,
            Auth::user(),
            $request->admin_message
        );

        if ($results['approved'] > 0) {
            $message = "Successfully approved {$results['approved']} license(s).";
            if ($results['failed'] > 0) {
                $message .= " {$results['failed']} license(s) failed to approve.";
            }
            return redirect()->route('admin.provider-licenses.index')->with('success', $message);
        } else {
            return redirect()->back()->with('error', 'Failed to approve any licenses. Please try again.');
        }
    }

    /**
     * Show post-rejection choice page for admin to decide user fate.
     */
    public function postRejectionChoice(Request $request, $license, $user)
    {
        $licenseModel = License::findOrFail($license);
        $userModel = User::findOrFail($user);

        // Ensure this is a provider license
        if ($licenseModel->user->role !== 'provider') {
            abort(404, 'Provider license not found.');
        }

        return view('admin.provider-licenses.post-rejection-choice', [
            'license' => $licenseModel,
            'user' => $userModel,
            'successMessage' => $request->session()->get('success')
        ]);
    }

    /**
     * Handle admin choice after rejection - keep or remove user.
     */
    public function handlePostRejectionChoice(Request $request, LicenseManagementService $licenseService)
    {
        $request->validate([
            'action' => 'required|in:keep,remove',
            'user_id' => 'required|exists:users,id',
            'license_id' => 'required|exists:licenses,id'
        ]);

        $user = User::findOrFail($request->user_id);
        $license = License::findOrFail($request->license_id);

        // Ensure this is a provider license
        if ($license->user->role !== 'provider') {
            abort(404, 'Provider license not found.');
        }

        if ($request->action === 'keep') {
            return redirect()->route('admin.provider-licenses.index')
                ->with('success', "User {$user->name} has been kept. They can resubmit their license application.");
        } else {
            // Remove user and all associated data
            if ($licenseService->deleteUserAndAssociatedData($user)) {
                return redirect()->route('admin.provider-licenses.index')
                    ->with('success', "User {$user->name} and all associated data have been permanently removed from the system.");
            } else {
                return redirect()->back()
                    ->with('error', 'Failed to remove user. Please try again or contact system administrator.');
            }
        }
    }
}
