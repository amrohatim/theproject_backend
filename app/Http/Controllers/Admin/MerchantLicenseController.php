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
            // Redirect to admin choice page instead of directly back to index
            return redirect()->route('admin.merchant-licenses.post-rejection-choice', [
                'merchant' => $merchant->id,
                'user' => $merchant->user_id
            ])->with('success', "License rejected for {$merchant->business_name}. Email notification sent to the merchant.");
        } else {
            return redirect()->back()
                ->with('error', 'Failed to reject license. Please try again.');
        }
    }

    /**
     * Show post-rejection choice page for admin to decide user fate.
     */
    public function postRejectionChoice(Request $request, $merchant, $user)
    {
        $merchantModel = Merchant::findOrFail($merchant);
        $userModel = User::findOrFail($user);

        // Ensure this is a merchant
        if ($userModel->role !== 'merchant') {
            abort(404, 'Merchant not found.');
        }

        return view('admin.merchant-licenses.post-rejection-choice', [
            'merchant' => $merchantModel,
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
            'merchant_id' => 'required|exists:merchants,id'
        ]);

        $user = User::findOrFail($request->user_id);
        $merchant = Merchant::findOrFail($request->merchant_id);

        if ($request->action === 'remove') {
            try {
                $result = $licenseService->deleteUserAndAssociatedData($user);

                if ($result) {
                    return redirect()->route('admin.merchant-licenses.index')
                        ->with('success', "User {$user->name} and all associated data have been permanently deleted.");
                } else {
                    return redirect()->back()
                        ->with('error', 'Failed to delete user. Please try again.');
                }
            } catch (\Exception $e) {
                Log::error('Error deleting user after merchant license rejection: ' . $e->getMessage());
                return redirect()->back()
                    ->with('error', 'An error occurred while deleting the user.');
            }
        } else {
            // Keep user - just redirect back to index
            return redirect()->route('admin.merchant-licenses.index')
                ->with('success', "User {$user->name} has been kept. They can resubmit their license application.");
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

    /**
     * View a merchant image in full-screen mode.
     */
    public function viewImage($id, $type)
    {
        $merchant = Merchant::with(['user'])->findOrFail($id);

        // Validate image type
        $validTypes = ['uae_front', 'uae_back', 'logo'];
        if (!in_array($type, $validTypes)) {
            abort(404, 'Invalid image type.');
        }

        // Get the image path based on type
        $imagePath = null;
        $imageTitle = '';

        switch ($type) {
            case 'uae_front':
                $imagePath = $merchant->getRawOriginal('uae_id_front');
                $imageTitle = 'UAE ID Front';
                break;
            case 'uae_back':
                $imagePath = $merchant->getRawOriginal('uae_id_back');
                $imageTitle = 'UAE ID Back';
                break;
            case 'logo':
                $imagePath = $merchant->getRawOriginal('logo');
                $imageTitle = 'Business Logo';
                break;
        }

        // Check if image exists
        if (!$imagePath) {
            abort(404, 'No image found for this type.');
        }

        // Get full image URL
        $imageUrl = \App\Helpers\ImageHelper::getFullImageUrl($imagePath);

        // Get image metadata if possible
        $imageMetadata = $this->getImageMetadata($imagePath);

        return view('admin.merchant-licenses.image-view', compact(
            'merchant',
            'type',
            'imagePath',
            'imageTitle',
            'imageUrl',
            'imageMetadata'
        ));
    }

    /**
     * Get image metadata for display.
     */
    private function getImageMetadata($imagePath)
    {
        $metadata = [
            'filename' => basename($imagePath),
            'size' => null,
            'dimensions' => null,
        ];

        try {
            // Try to get file from storage
            $fullPath = storage_path('app/public/' . $imagePath);

            if (file_exists($fullPath)) {
                // Get file size
                $metadata['size'] = $this->formatFileSize(filesize($fullPath));

                // Get image dimensions
                $imageInfo = getimagesize($fullPath);
                if ($imageInfo) {
                    $metadata['dimensions'] = $imageInfo[0] . ' × ' . $imageInfo[1] . ' pixels';
                }
            }
        } catch (\Exception $e) {
            // If we can't get metadata, that's okay - we'll just show what we can
        }

        return $metadata;
    }

    /**
     * Format file size in human readable format.
     */
    private function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}
