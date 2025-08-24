<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ValidLicenseMiddleware
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
        if ($user && $user->role === 'merchant') {
            $merchant = $user->merchantRecord;

            if (!$merchant) {
                return redirect()->route('merchant.dashboard')
                    ->with('error', 'Merchant profile not found.');
            }

            // Check registration step access control
            $registrationStepBlocked = !in_array($user->registration_step, ['license_completed', 'verified']);

            // Check license status access control
            $licenseStatusBlocked = $merchant->license_status !== 'verified';

            // If either condition blocks access
            if ($registrationStepBlocked || $licenseStatusBlocked) {
                $message = $this->getAccessDeniedMessage($user->registration_step, $merchant->license_status, $merchant->license_rejection_reason);
                $redirectRoute = $this->getRedirectRoute($user->registration_step, $merchant->license_status);

                // For AJAX requests, return JSON response with modal data
                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => true,
                        'message' => $message,
                        'redirect' => $redirectRoute,
                        'show_modal' => true,
                        'modal_title' => 'Access Restricted',
                        'modal_message' => $message,
                        'license_status' => $merchant->license_status,
                        'registration_step' => $user->registration_step
                    ], 403);
                }

                // For regular requests, redirect with modal session data
                return redirect($redirectRoute)
                    ->with('show_access_modal', true)
                    ->with('modal_title', 'Access Restricted')
                    ->with('modal_message', $message)
                    ->with('license_status', $merchant->license_status)
                    ->with('registration_step', $user->registration_step);
            }
        }

        return $next($request);
    }

    /**
     * Get appropriate access denied message based on registration step and license status.
     */
    private function getAccessDeniedMessage(string $registrationStep, string $licenseStatus, ?string $rejectionReason = null): string
    {
        // Priority: Registration step issues first, then license status issues
        if (!in_array($registrationStep, ['license_completed', 'verified'])) {
            return match($registrationStep) {
                'phone_verified' => 'Please upload your business license to access product and service management features.',
                'pending' => 'Please complete your merchant registration to access this feature.',
                'info_completed' => 'Please complete your company information to access this feature.',
                'email_verification_pending' => 'Please verify your email address to access this feature.',
                'email_verified' => 'Please verify your phone number to access this feature.',
                'phone_verification_pending' => 'Please verify your phone number to access this feature.',
                'company_completed' => 'Please upload your license documents to access this feature.',
                default => 'Please complete your merchant registration to access this feature.'
            };
        }

        // License status issues
        return match($licenseStatus) {
            'checking' => 'Your license is currently under review. Please wait for admin approval before managing products or services.',
            'expired' => 'Your license has expired. Please upload a new license to continue managing products or services.',
            'rejected' => $rejectionReason
                ? "Your license was rejected: {$rejectionReason}. Please upload a new license to continue."
                : 'Your license was rejected. Please upload a new license to continue managing products or services.',
            default => 'Your license status does not allow access to this feature. Please contact support or upload a valid license.'
        };
    }

    /**
     * Get appropriate redirect route based on registration step and license status.
     */
    private function getRedirectRoute(string $registrationStep, string $licenseStatus): string
    {
        // If registration step is incomplete, redirect to appropriate page
        if (!in_array($registrationStep, ['license_completed', 'verified'])) {
            if ($registrationStep === 'phone_verified') {
                return route('merchant.license.upload');
            }
            return route('merchant.registration.status');
        }

        // If license status is the issue, redirect to license management
        if (in_array($licenseStatus, ['checking', 'expired', 'rejected'])) {
            return route('merchant.license.status', ['status' => $licenseStatus]);
        }

        // Default fallback
        return route('merchant.dashboard');
    }
}
