<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorMiddleware
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

        // Check if user is a vendor
        if ($user->role !== 'vendor') {
            return redirect('/')->with('error', 'You do not have vendor access.');
        }

        // Check if vendor registration is approved
        if ($user->registration_status !== 'approved') {
            $message = match($user->registration_status) {
                'pending' => 'Your vendor registration is pending approval. Please wait for admin approval.',
                'rejected' => 'Your vendor registration has been rejected. Please contact support for more information.',
                default => 'Your account status does not allow access to the vendor area.'
            };

            return redirect()->route('vendor.registration.status')->with('error', $message);
        }

        // Check if email and phone are verified
        if (!$user->email_verified_at) {
            return redirect()->route('vendor.email.verify', ['user_id' => $user->id])
                ->with('error', 'Please verify your email address to continue.');
        }

        if (!$user->phone_verified_at) {
            return redirect()->route('vendor.otp.verify', ['user_id' => $user->id])
                ->with('error', 'Please verify your phone number to continue.');
        }

        return $next($request);
    }
}
