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

        // For vendors, enforce license checks; for products managers, skip license restrictions
        if ($user->role === 'vendor') {
            // Allow access to license management routes regardless of license status
            $licenseRoutes = [
                'vendor.license.index',
                'vendor.license.renewal',
                'vendor.license.renewal.store',
                'vendor.license.view',
                'vendor.license.preview'
            ];

            if (in_array($request->route()->getName(), $licenseRoutes)) {
                return $next($request);
            }

            // Check registration step and license status for access control
            if ($user->registration_step === 'company_completed' && !$user->hasLicense()) {
                // Vendor completed company registration but has no license - redirect to license upload
                return redirect()->route('vendor.license.upload')
                    ->with('error', 'Please upload your license documentation to access the dashboard.');
            }

            // If user has a license, check its status
            if ($user->hasLicense()) {
                $licenseStatus = $user->getLicenseStatus();

                switch ($licenseStatus) {
                    case 'pending':
                        return redirect()->route('vendor.license.status', ['status' => 'pending'])
                            ->with('license_message', 'Your license is currently under review. This process typically takes 1-3 business days.');

                    case 'expired':
                        return redirect()->route('vendor.license.status', ['status' => 'expired'])
                            ->with('license_message', 'Your license has expired. Please upload a renewed license to continue using the platform.');

                    case 'rejected':
                        return redirect()->route('vendor.license.status', ['status' => 'rejected'])
                            ->with('license_message', 'Your license has been rejected. Please contact support or upload corrected license documentation.');

                    case 'active':
                        // License is active, allow access
                        break;

                    default:
                        return redirect()->route('vendor.license.status')
                            ->with('license_message', 'Your license status does not allow access to the vendor dashboard. Please contact support.');
                }
            }
        }

        // products_manager: pass through without vendor license checks
        return $next($request);
    }
}
