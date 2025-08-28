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

        // Allow dashboard access for all merchants - license validation will be handled at product/service level
        // This removes the centralized license blocking and allows merchants to access their dashboard
        // Individual product/service creation will still be restricted by ValidLicenseMiddleware

        return $next($request);
    }


}
