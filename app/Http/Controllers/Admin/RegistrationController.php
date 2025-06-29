<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\License;
use App\Mail\RegistrationApproved;
use App\Mail\RegistrationRejected;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class RegistrationController extends Controller
{
    /**
     * Display a listing of registrations.
     */
    public function index()
    {
        $registrations = User::whereIn('role', ['vendor', 'provider'])
            ->with(['company', 'provider', 'licenses'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $totalCount = User::whereIn('role', ['vendor', 'provider'])->count();
        $vendorCount = User::where('role', 'vendor')->count();
        $providerCount = User::where('role', 'provider')->count();
        $pendingCount = User::whereIn('role', ['vendor', 'provider'])
            ->where('registration_status', 'pending')
            ->count();

        return view('admin.registrations.index', compact(
            'registrations',
            'totalCount',
            'vendorCount',
            'providerCount',
            'pendingCount'
        ));
    }

    /**
     * Display the specified registration.
     */
    public function show($id)
    {
        $user = User::with(['company', 'provider', 'licenses'])
            ->whereIn('role', ['vendor', 'provider'])
            ->findOrFail($id);

        return view('admin.registrations.show', compact('user'));
    }

    /**
     * Approve a registration.
     */
    public function approve(Request $request, $id)
    {
        $user = User::whereIn('role', ['vendor', 'provider'])->findOrFail($id);

        $user->update([
            'registration_status' => 'approved',
            'approved_at' => now(),
            'approved_by' => Auth::id()
        ]);

        // Send approval email
        try {
            Mail::to($user->email)->send(new RegistrationApproved($user));
        } catch (\Exception $e) {
            Log::error('Failed to send approval email: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Registration approved successfully!'
        ]);
    }

    /**
     * Reject a registration.
     */
    public function reject(Request $request, $id)
    {
        $user = User::whereIn('role', ['vendor', 'provider'])->findOrFail($id);

        $reason = $request->input('reason');

        $user->update([
            'registration_status' => 'rejected',
            'rejected_at' => now(),
            'rejected_by' => Auth::id(),
            'rejection_reason' => $reason
        ]);

        // Send rejection email
        try {
            Mail::to($user->email)->send(new RegistrationRejected($user, $reason));
        } catch (\Exception $e) {
            Log::error('Failed to send rejection email: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Registration rejected successfully!'
        ]);
    }

    /**
     * Download license document.
     */
    public function downloadLicense($id)
    {
        $license = License::findOrFail($id);

        // Check if the license belongs to a vendor or provider registration
        $user = $license->user;
        if (!in_array($user->role, ['vendor', 'provider'])) {
            abort(403, 'Unauthorized');
        }

        if (!Storage::exists($license->file_path)) {
            abort(404, 'License file not found');
        }

        return Storage::download($license->file_path, $license->original_filename);
    }
}