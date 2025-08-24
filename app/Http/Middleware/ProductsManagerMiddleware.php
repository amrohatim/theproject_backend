<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductsManagerMiddleware
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

        // Check if user is a products manager
        if ($user->role !== 'products_manager') {
            return redirect('/')->with('error', 'You do not have products manager access.');
        }

        // Check if user account is active
        if ($user->status !== 'active') {
            return redirect('/')->with('error', 'Your account is not active. Please contact your administrator.');
        }

        // Check if user has a products manager profile
        if (!$user->productsManager) {
            return redirect('/')->with('error', 'Products manager profile not found. Please contact your administrator.');
        }

        // Check if the products manager's company is active
        $productsManager = $user->productsManager;
        if (!$productsManager->company) {
            return redirect('/')->with('error', 'Company profile not found. Please contact your administrator.');
        }

        // Check vendor license status for product creation routes
        $productCreationRoutes = [
            'products-manager.products.create',
            'products-manager.products.store',
            'products-manager.products.create.data'
        ];

        if (in_array($request->route()->getName(), $productCreationRoutes)) {
            if (!$productsManager->hasActiveVendorLicense()) {
                $licenseStatus = $productsManager->getVendorLicenseStatus();

                return redirect()->route('products-manager.license.restriction')
                    ->with('license_status', $licenseStatus)
                    ->with('license_message', 'Store status is not active. Product creation is restricted until the vendor license is active.');
            }
        }

        return $next($request);
    }
}
