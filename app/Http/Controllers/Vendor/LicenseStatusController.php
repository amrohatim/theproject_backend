<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LicenseStatusController extends Controller
{
    /**
     * Show vendor license status page.
     */
    public function show(Request $request, $status = null)
    {
        $user = Auth::user();
        
        // Ensure user is authenticated and is a vendor
        if (!$user || $user->role !== 'vendor') {
            return redirect()->route('login')->withErrors(['error' => 'Please log in as a vendor to access this page.']);
        }

        // Use the status from URL parameter or user's current license status
        $licenseStatus = $status ?? $user->getLicenseStatus();
        
        // Get the message from session or generate default
        $message = session('license_message') ?? $this->getDefaultMessage($licenseStatus);

        $data = [
            'user' => $user,
            'license_status' => $licenseStatus,
            'message' => $message,
            'license' => $user->latestLicense,
            'can_upload_new' => in_array($licenseStatus, ['rejected', 'expired']),
        ];

        return view('vendor.license.status', $data);
    }

    /**
     * Get default message for license status.
     */
    private function getDefaultMessage(string $licenseStatus): string
    {
        return match($licenseStatus) {
            'pending' => 'Your license is currently under review. This process typically takes 1-3 business days.',
            'rejected' => 'Your license has been rejected. Please contact support or upload corrected license documentation.',
            'expired' => 'Your license has expired. Please upload a renewed license to continue using the platform.',
            'active' => 'Your license is active and verified. You have full access to all vendor dashboard features.',
            default => 'Your license status does not allow access to the vendor dashboard. Please contact support.'
        };
    }
}
