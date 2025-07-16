<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\User;
use App\Services\LicenseManagementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VendorLicenseController extends Controller
{
    /**
     * Display a listing of vendor licenses pending review.
     */
    public function index(Request $request)
    {
        $query = License::with(['user', 'user.company'])
            ->whereHas('user', function($q) {
                $q->where('role', 'vendor');
            })
            ->orderBy('created_at', 'desc');

        // Filter by status
        $status = $request->get('status', 'pending');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $licenses = $query->paginate(15);

        return view('admin.vendor-licenses.index', compact('licenses', 'status'));
    }

    /**
     * Show the details of a specific vendor license.
     */
    public function show($id)
    {
        $license = License::with(['user', 'user.company'])->findOrFail($id);

        // Ensure this is a vendor license
        if ($license->user->role !== 'vendor') {
            abort(404, 'Vendor license not found.');
        }

        return view('admin.vendor-licenses.show', compact('license'));
    }

    /**
     * Approve a vendor license.
     */
    public function approve(Request $request, $id, LicenseManagementService $licenseService)
    {
        $request->validate([
            'admin_message' => 'nullable|string|max:1000',
        ]);

        $license = License::findOrFail($id);

        // Ensure this is a vendor license
        if ($license->user->role !== 'vendor') {
            abort(404, 'Vendor license not found.');
        }

        if ($license->status === 'active') {
            return redirect()->back()->with('error', 'This license has already been approved.');
        }

        /** @var User $admin */
        $admin = Auth::user();
        if ($licenseService->approveVendorLicense($license, $admin, $request->admin_message)) {
            return redirect()->route('admin.vendor-licenses.index')
                ->with('success', "License approved successfully for {$license->user->name}!");
        } else {
            return redirect()->back()
                ->with('error', 'Failed to approve license. Please try again.');
        }
    }

    /**
     * Reject a vendor license.
     */
    public function reject(Request $request, $id, LicenseManagementService $licenseService)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $license = License::findOrFail($id);

        // Ensure this is a vendor license
        if ($license->user->role !== 'vendor') {
            abort(404, 'Vendor license not found.');
        }

        if ($license->status === 'rejected') {
            return redirect()->back()->with('error', 'This license has already been rejected.');
        }

        /** @var User $admin */
        $admin = Auth::user();
        if ($licenseService->rejectVendorLicense($license, $admin, $request->rejection_reason)) {
            return redirect()->route('admin.vendor-licenses.index')
                ->with('success', "License rejected for {$license->user->name}.");
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

        /** @var User $admin */
        $admin = Auth::user();
        $results = $licenseService->bulkApproveVendorLicenses(
            $request->license_ids,
            $admin,
            $request->admin_message
        );

        if ($results['approved'] > 0) {
            $message = "Successfully approved {$results['approved']} license(s).";
            if ($results['failed'] > 0) {
                $message .= " {$results['failed']} license(s) failed to approve.";
            }
            return redirect()->back()->with('success', $message);
        } else {
            return redirect()->back()->with('error', 'Failed to approve any licenses.');
        }
    }

    /**
     * Download a vendor license file.
     */
    public function downloadLicense($id)
    {
        $license = License::findOrFail($id);

        // Ensure this is a vendor license
        if ($license->user->role !== 'vendor') {
            abort(404, 'Vendor license not found.');
        }

        if (!$license->license_file_path) {
            abort(404, 'No license file found.');
        }

        $filePath = storage_path('app/public/' . $license->license_file_path);

        if (!file_exists($filePath)) {
            abort(404, 'License file not found.');
        }

        return response()->download($filePath, $license->license_file_name);
    }

    /**
     * View a vendor license file in the browser.
     */
    public function viewLicense($id)
    {
        $license = License::findOrFail($id);

        // Ensure this is a vendor license
        if ($license->user->role !== 'vendor') {
            abort(404, 'Vendor license not found.');
        }

        if (!$license->license_file_path) {
            abort(404, 'No license file found.');
        }

        $filePath = storage_path('app/public/' . $license->license_file_path);

        if (!file_exists($filePath)) {
            abort(404, 'License file not found.');
        }

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($license->license_file_path) . '"'
        ]);
    }

    /**
     * View license image with full preview functionality.
     */
    public function viewImage($id)
    {
        $license = License::findOrFail($id);

        // Ensure this is a vendor license
        if ($license->user->role !== 'vendor') {
            abort(404, 'Vendor license not found.');
        }

        if (!$license->license_file_path) {
            abort(404, 'No license file found.');
        }

        return view('admin.vendor-licenses.image-preview', compact('license'));
    }
}
