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

        // Allow dashboard access for all merchants - license validation will be handled at product/service level
        // This removes the centralized license blocking and allows merchants to access their dashboard
        // Individual product/service creation will still be restricted by ValidLicenseMiddleware

        return $next($request);
    }


}
