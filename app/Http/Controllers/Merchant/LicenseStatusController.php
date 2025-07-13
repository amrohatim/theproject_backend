<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LicenseStatusController extends Controller
{
    /**
     * Show license status page based on current status.
     */
    public function show(Request $request, $status = null)
    {
        $user = Auth::user();
        $merchant = $user->merchantRecord;

        if (!$merchant) {
            return redirect()->route('merchant.dashboard')
                ->with('error', 'Merchant profile not found. Please contact support.');
        }

        // Use the status from URL parameter or merchant's current license status
        $licenseStatus = $status ?? $merchant->license_status;
        
        // Get the message from session or generate default
        $message = session('license_message') ?? $this->getDefaultMessage($licenseStatus, $merchant->license_rejection_reason);

        $data = [
            'merchant' => $merchant,
            'license_status' => $licenseStatus,
            'message' => $message,
            'rejection_reason' => $merchant->license_rejection_reason,
            'license_expiry_date' => $merchant->license_expiry_date,
            'can_upload_new' => in_array($licenseStatus, ['rejected', 'expired']),
        ];

        return view('merchant.license.status', $data);
    }

    /**
     * Get default message for license status.
     */
    private function getDefaultMessage(string $licenseStatus, ?string $rejectionReason = null): string
    {
        return match($licenseStatus) {
            'checking' => 'Your license is currently under review. You will receive an email notification once approved.',
            'rejected' => 'Your license has been rejected. Please contact support or upload a new license.' . 
                         ($rejectionReason ? ' Reason: ' . $rejectionReason : ''),
            'expired' => 'Your license has expired. Please upload a renewed license to continue using the platform.',
            default => 'Your license status does not allow access to the merchant dashboard. Please contact support.'
        };
    }
}
