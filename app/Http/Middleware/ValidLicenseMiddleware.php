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

            // Check if merchant has a valid license
            if (!$merchant->hasValidLicense()) {
                $message = match($merchant->license_status) {
                    'checking' => 'Your license is currently under review. Please wait for admin approval before adding products or services.',
                    'expired' => 'Your license has expired. Please upload a new license to continue adding products or services.',
                    'rejected' => 'Your license was rejected. Please upload a new license to continue adding products or services.',
                    default => 'Your license is outdated. Please upgrade your license to add products or services.'
                };

                // For AJAX requests, return JSON response
                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => true,
                        'message' => $message,
                        'redirect' => route('merchant.settings.global')
                    ], 403);
                }

                // For regular requests, redirect with error message
                return redirect()->route('merchant.settings.global')
                    ->with('error', $message);
            }
        }

        return $next($request);
    }
}
