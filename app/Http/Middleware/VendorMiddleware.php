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

        // Allow vendor or products manager
        if (!in_array($user->role, ['vendor', 'products_manager'])) {
            return redirect('/')->with('error', 'You do not have vendor access.');
        }

        // Check if email and phone are verified first (only for vendors, not products managers)
        if ($user->role === 'vendor') {
            if (!$user->email_verified_at) {
                return redirect()->route('vendor.email.verify', ['user_id' => $user->id])
                    ->with('error', 'Please verify your email address to continue.');
            }

            if (!$user->phone_verified_at) {
                return redirect()->route('vendor.otp.verify', ['user_id' => $user->id])
                    ->with('error', 'Please verify your phone number to continue.');
            }
        }

        // For vendors, no license restrictions - they can access dashboard after completing company registration
        if ($user->role === 'vendor') {
            // Allow access for vendors who have completed company registration or beyond
            $validSteps = ['company_completed', 'license_completed', 'verified'];
            if (!in_array($user->registration_step, $validSteps)) {
                return redirect()->route('register.vendor')
                    ->with('error', 'Please complete your registration to access the dashboard.');
            }
        }

        // products_manager: pass through without vendor license checks
        return $next($request);
    }
}
