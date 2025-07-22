<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProviderMiddleware
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

        // Check if user is a provider
        if ($user->role !== 'provider') {
            return redirect('/')->with('error', 'You do not have provider access.');
        }

        // Allow access to settings page even without active license (for license management)
        if ($request->routeIs('provider.settings.*')) {
            // Still check if phone is verified for settings access
            if (!$user->phone_verified_at) {
                return redirect()->route('provider.otp.verify', ['user_id' => $user->id])
                    ->with('error', 'Please verify your phone number to continue.');
            }
            return $next($request);
        }

        // Check license status for provider access
        if (!$user->hasLicense()) {
            // No license record - redirect to license upload
            return redirect()->route('provider.license.upload')->with('error', 'Please upload your license documentation to continue.');
        }

        $licenseStatus = $user->getLicenseStatus();

        if ($licenseStatus !== 'active') {
            $message = match($licenseStatus) {
                'pending' => 'Your license is under review. Please wait for approval before accessing the provider dashboard.',
                'expired' => 'Your license has expired. Please renew your license to continue accessing the provider area.',
                'rejected' => 'Your license application was rejected. Please contact support or reapply with updated documentation.',
                default => 'Your license status does not allow access to the provider area.'
            };

            return redirect()->route('provider.license.status')->with('error', $message);
        }

        // Check if phone is verified
        if (!$user->phone_verified_at) {
            return redirect()->route('provider.otp.verify', ['user_id' => $user->id])
                ->with('error', 'Please verify your phone number to continue.');
        }

        return $next($request);
    }
}
