<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BranchLicense;
use App\Models\Branch;
use App\Models\User;
use App\Mail\BranchLicenseApproved;
use App\Mail\BranchLicenseRejected;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;

class BranchLicenseController extends Controller
{
    /**
     * Display a listing of branch licenses.
     */
    public function index(Request $request)
    {
        $query = BranchLicense::with(['branch.user', 'branch.company'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        $status = $request->get('status', 'pending');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Filter by vendor
        $vendorId = $request->get('vendor_id');
        if ($vendorId) {
            $query->whereHas('branch.user', function($q) use ($vendorId) {
                $q->where('id', $vendorId);
            });
        }

        // Filter by business type
        $businessType = $request->get('business_type');
        if ($businessType) {
            $query->whereHas('branch', function($q) use ($businessType) {
                $q->where('business_type', $businessType);
            });
        }

        $licenses = $query->paginate(15);

        // Get filter options
        $vendors = User::where('role', 'vendor')
            ->whereHas('branches.licenses')
            ->orderBy('name')
            ->get();

        $businessTypes = Branch::whereHas('licenses')
            ->distinct()
            ->pluck('business_type')
            ->filter()
            ->sort()
            ->values();

        return view('admin.branch-licenses.index', compact('licenses', 'status', 'vendors', 'businessTypes', 'vendorId', 'businessType'));
    }

    /**
     * Show the details of a specific branch license.
     */
    public function show($id)
    {
        $license = BranchLicense::with(['branch.user', 'branch.company'])->findOrFail($id);

        return view('admin.branch-licenses.show', compact('license'));
    }

    /**
     * Approve a branch license.
     */
    public function approve(Request $request, $id)
    {
        $request->validate([
            'admin_message' => 'nullable|string|max:1000',
        ]);

        $license = BranchLicense::with(['branch.user', 'branch.company'])->findOrFail($id);

        if ($license->status === 'active') {
            return redirect()->back()->with('error', 'This license has already been approved.');
        }

        DB::beginTransaction();
        try {
            // Update license status
            $license->update([
                'status' => 'active',
                'verified_at' => now(),
            ]);

            // Send approval email
            Mail::to($license->branch->user->email)->send(
                new BranchLicenseApproved($license, $request->admin_message)
            );

            DB::commit();

            return redirect()->route('admin.branch-licenses.index')
                ->with('success', "Branch license approved successfully for {$license->branch->name}!");

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to approve license. Please try again.');
        }
    }

    /**
     * Reject a branch license.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $license = BranchLicense::with(['branch.user', 'branch.company'])->findOrFail($id);

        if ($license->status === 'rejected') {
            return redirect()->back()->with('error', 'This license has already been rejected.');
        }

        DB::beginTransaction();
        try {
            // Update license status
            $license->update([
                'status' => 'rejected',
            ]);

            // Send rejection email
            Mail::to($license->branch->user->email)->send(
                new BranchLicenseRejected($license, $request->rejection_reason)
            );

            DB::commit();

            return redirect()->route('admin.branch-licenses.index')
                ->with('success', "Branch license rejected for {$license->branch->name}.");

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to reject license. Please try again.');
        }
    }

    /**
     * Bulk approve branch licenses.
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'license_ids' => 'required|array',
            'license_ids.*' => 'exists:branches_licenses,id',
        ]);

        $licenses = BranchLicense::with(['branch.user', 'branch.company'])
            ->whereIn('id', $request->license_ids)
            ->where('status', 'pending')
            ->get();

        if ($licenses->isEmpty()) {
            return redirect()->back()->with('error', 'No pending licenses found to approve.');
        }

        DB::beginTransaction();
        try {
            foreach ($licenses as $license) {
                $license->update([
                    'status' => 'active',
                    'verified_at' => now(),
                ]);

                // Send approval email
                Mail::to($license->branch->user->email)->send(
                    new BranchLicenseApproved($license)
                );
            }

            DB::commit();

            return redirect()->route('admin.branch-licenses.index')
                ->with('success', "Successfully approved {$licenses->count()} branch licenses!");

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to approve licenses. Please try again.');
        }
    }

    /**
     * Download the license file.
     */
    public function downloadLicense($id)
    {
        $license = BranchLicense::findOrFail($id);

        if (!$license->license_file_path || !Storage::disk('public')->exists($license->license_file_path)) {
            abort(404, 'License file not found.');
        }

        $fileName = "branch_license_{$license->branch->name}_{$license->id}.pdf";
        
        return Storage::disk('public')->download($license->license_file_path, $fileName);
    }

    /**
     * View the license file in browser.
     */
    public function viewLicense($id)
    {
        $license = BranchLicense::findOrFail($id);

        if (!$license->license_file_path || !Storage::disk('public')->exists($license->license_file_path)) {
            abort(404, 'License file not found.');
        }

        $file = Storage::disk('public')->get($license->license_file_path);
        
        return Response::make($file, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="branch_license_' . $license->id . '.pdf"'
        ]);
    }
}
