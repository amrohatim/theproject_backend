<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MerchantLicenseAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Only apply to merchants
        if (!$user || $user->role !== 'merchant') {
            return $next($request);
        }

        $merchant = $user->merchantRecord;

        if (!$merchant) {
            return redirect()->route('merchant.dashboard')
                ->with('error', 'Merchant profile not found. Please contact support.');
        }

        // Check if merchant has active license status
        if ($merchant->status !== 'active') {
            // Redirect based on license status
            $message = $this->getLicenseStatusMessage($merchant->license_status, $merchant->license_rejection_reason);
            
            return redirect()->route('merchant.license.status', ['status' => $merchant->license_status])
                ->with('license_message', $message)
                ->with('license_status', $merchant->license_status);
        }

        // Check if license is expired (additional check)
        if ($merchant->license_status === 'verified' && $merchant->isLicenseExpired()) {
            return redirect()->route('merchant.license.status', ['status' => 'expired'])
                ->with('license_message', 'Your license has expired. Please upload a renewed license to continue using the platform.')
                ->with('license_status', 'expired');
        }

        return $next($request);
    }

    /**
     * Get appropriate message based on license status.
     */
    private function getLicenseStatusMessage(string $licenseStatus, ?string $rejectionReason = null): string
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
