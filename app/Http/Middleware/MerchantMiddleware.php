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
            $message = match($user->registration_step) {
                'pending' => 'Your merchant registration is pending. Please complete your registration.',
                'info_completed' => 'Please complete your company information.',
                'company_completed' => 'Please upload your license documents.',
                'license_completed' => 'Your registration is pending admin verification.',
                default => 'Please complete your merchant registration.'
            };

            return redirect()->route('merchant.registration.status')->with('error', $message);
        }

        // Check if email and phone are verified
        if (!$user->email_verified_at) {
            return redirect()->route('merchant.email.verify', ['user_id' => $user->id])
                ->with('error', 'Please verify your email address to continue.');
        }

        if (!$user->phone_verified_at) {
            return redirect()->route('merchant.otp.verify', ['user_id' => $user->id])
                ->with('error', 'Please verify your phone number to continue.');
        }

        // Check if merchant record exists and is verified by admin
        $merchant = $user->merchantRecord;
        if (!$merchant) {
            return redirect('/')->with('error', 'Merchant profile not found. Please contact support.');
        }

        if (!$merchant->is_verified) {
            return redirect()->route('merchant.verification.pending')
                ->with('error', 'Your merchant account is pending admin verification. Please wait for approval.');
        }

        return $next($request);
    }
}
