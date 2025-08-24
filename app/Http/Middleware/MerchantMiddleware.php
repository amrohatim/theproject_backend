<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MerchantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to access this area.');
        }

        $user = Auth::user();

        // Check if user is a merchant
        if ($user->role !== 'merchant') {
            return redirect('/')->with('error', 'You do not have merchant access.');
        }

        // Check if merchant registration is completed
        if ($user->registration_step !== 'verified') {
            // Special handling for phone_verified users - redirect to license upload
            if ($user->registration_step === 'phone_verified') {
                return redirect()->route('merchant.license.upload')
                    ->with('info', 'Complete your registration by uploading your business license.');
            }

            $message = match($user->registration_step) {
                'pending' => 'Your merchant registration is pending. Please complete your registration.',
                'info_completed' => 'Please complete your company information.',
                'email_verification_pending' => 'Please verify your email address.',
                'email_verified' => 'Please verify your phone number.',
                'phone_verification_pending' => 'Please verify your phone number.',
                'company_completed' => 'Please upload your license documents.',
                'license_completed' => 'Your registration is pending admin verification.',
                default => 'Please complete your merchant registration.'
            };

            return redirect()->route('merchant.registration.status')->with('error', $message);
        }

        // Check if phone is verified (email verification removed as emails are pre-verified)
        if (!$user->phone_verified_at) {
            return redirect()->route('merchant.otp.verify', ['user_id' => $user->id])
                ->with('error', 'Please verify your phone number to continue.');
        }

        // Check if merchant record exists
        $merchant = $user->merchantRecord;
        if (!$merchant) {
            return redirect('/')->with('error', 'Merchant profile not found. Please contact support.');
        }

        // Check merchant status based on license verification
        if ($merchant->status !== 'active') {
            // Get appropriate message based on license status
            $message = $this->getLicenseStatusMessage($merchant->license_status, $merchant->license_rejection_reason);

            return redirect()->route('merchant.license.status', ['status' => $merchant->license_status])
                ->with('license_message', $message)
                ->with('license_status', $merchant->license_status);
        }

        // Additional check for expired licenses
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
