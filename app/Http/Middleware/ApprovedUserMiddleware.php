<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApprovedUserMiddleware
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

        // Admin users always have access
        if ($user->role === 'admin') {
            return $next($request);
        }

        // Check if user registration is approved
        if ($user->registration_status !== 'approved') {
            $message = match($user->registration_status) {
                'pending' => 'Your registration is pending approval. Please wait for admin approval.',
                'rejected' => 'Your registration has been rejected. Please contact support for more information.',
                default => 'Your account status does not allow access to this area.'
            };

            // Redirect to appropriate waiting/status page
            $redirectRoute = match($user->role) {
                'vendor' => 'vendor.registration.status',
                'provider' => 'provider.registration.status',
                default => 'home'
            };

            return redirect()->route($redirectRoute)->with('error', $message);
        }

        // Check if email and phone are verified
        if (!$user->email_verified_at) {
            $redirectRoute = match($user->role) {
                'vendor' => 'vendor.email.verify',
                'provider' => 'provider.email.verify',
                default => 'verification.notice'
            };

            return redirect()->route($redirectRoute, ['user_id' => $user->id])
                ->with('error', 'Please verify your email address to continue.');
        }

        if (!$user->phone_verified_at) {
            $redirectRoute = match($user->role) {
                'vendor' => 'vendor.otp.verify',
                'provider' => 'provider.otp.verify',
                default => 'home'
            };

            return redirect()->route($redirectRoute, ['user_id' => $user->id])
                ->with('error', 'Please verify your phone number to continue.');
        }

        return $next($request);
    }
}