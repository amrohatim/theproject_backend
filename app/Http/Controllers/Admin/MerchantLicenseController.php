<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\User;
use App\Services\LicenseManagementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MerchantLicenseController extends Controller
{
    /**
     * Display a listing of merchant licenses pending review.
     */
    public function index(Request $request)
    {
        $query = Merchant::with(['user'])
            ->whereNotNull('license_file')
            ->orderBy('license_uploaded_at', 'desc');

        // Filter by status
        $status = $request->get('status', 'checking');
        if ($status !== 'all') {
            $query->where('license_status', $status);
        }

        $merchants = $query->paginate(15);

        return view('admin.merchant-licenses.index', compact('merchants', 'status'));
    }

    /**
     * Show the details of a specific merchant license.
     */
    public function show($id)
    {
        $merchant = Merchant::with(['user', 'licenseApprovedBy'])->findOrFail($id);

        return view('admin.merchant-licenses.show', compact('merchant'));
    }

    /**
     * Approve a merchant license.
     */
    public function approve(Request $request, $id, LicenseManagementService $licenseService)
    {
        $request->validate([
            'admin_message' => 'nullable|string|max:1000',
        ]);

        $merchant = Merchant::findOrFail($id);

        if ($merchant->license_status === 'verified') {
            return redirect()->back()->with('error', 'This license has already been approved.');
        }

        if ($licenseService->approveLicense($merchant, Auth::user(), $request->admin_message)) {
            return redirect()->route('admin.merchant-licenses.index')
                ->with('success', "License approved successfully for {$merchant->business_name}!");
        } else {
            return redirect()->back()
                ->with('error', 'Failed to approve license. Please try again.');
        }
    }

    /**
     * Reject a merchant license.
     */
    public function reject(Request $request, $id, LicenseManagementService $licenseService)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $merchant = Merchant::findOrFail($id);

        if ($merchant->license_status === 'rejected') {
            return redirect()->back()->with('error', 'This license has already been rejected.');
        }

        if ($licenseService->rejectLicense($merchant, Auth::user(), $request->rejection_reason)) {
            return redirect()->route('admin.merchant-licenses.index')
                ->with('success', "License rejected for {$merchant->business_name}.");
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
            'merchant_ids' => 'required|array',
            'merchant_ids.*' => 'exists:merchants,id',
            'admin_message' => 'nullable|string|max:1000',
        ]);

        $results = $licenseService->bulkApproveLicenses(
            $request->merchant_ids,
            Auth::user(),
            $request->admin_message
        );

        if ($results['approved'] > 0) {
            $message = "Successfully approved {$results['approved']} license(s).";
            if ($results['failed'] > 0) {
                $message .= " {$results['failed']} failed to approve.";
            }
            return redirect()->route('admin.merchant-licenses.index')->with('success', $message);
        } else {
            return redirect()->back()->with('error', 'Failed to approve any licenses. Please try again.');
        }
    }

    /**
     * Download a merchant license file.
     */
    public function downloadLicense($id)
    {
        $merchant = Merchant::findOrFail($id);

        if (!$merchant->license_file) {
            return redirect()->back()->with('error', 'No license file found.');
        }

        $filePath = storage_path('app/public/' . $merchant->license_file);

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'License file not found.');
        }

        return response()->download($filePath, basename($merchant->license_file));
    }

    /**
     * View a merchant license file in the browser.
     */
    public function viewLicense($id)
    {
        $merchant = Merchant::findOrFail($id);

        if (!$merchant->license_file) {
            abort(404, 'No license file found.');
        }

        $filePath = storage_path('app/public/' . $merchant->license_file);

        if (!file_exists($filePath)) {
            abort(404, 'License file not found.');
        }

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($merchant->license_file) . '"'
        ]);
    }
}
