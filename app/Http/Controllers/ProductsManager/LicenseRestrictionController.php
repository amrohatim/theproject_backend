<?php

namespace App\Http\Controllers\ProductsManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LicenseRestrictionController extends Controller
{
    /**
     * Show the license restriction page for products managers.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function show(Request $request)
    {
        $user = Auth::user();
        
        // Ensure user is authenticated and is a products manager
        if (!$user || $user->role !== 'products_manager') {
            return redirect()->route('login')->withErrors(['error' => 'Please log in as a products manager to access this page.']);
        }

        $productsManager = $user->productsManager;
        
        if (!$productsManager) {
            return redirect('/')->with('error', 'Products manager profile not found.');
        }

        // Get license status and message from session or defaults
        $licenseStatus = session('license_status') ?? $productsManager->getVendorLicenseStatus();
        $message = session('license_message') ?? $this->getDefaultMessage($licenseStatus);

        $data = [
            'user' => $user,
            'products_manager' => $productsManager,
            'license_status' => $licenseStatus,
            'message' => $message,
            'vendor' => $productsManager->getVendorUser(),
        ];

        return view('products-manager.license.restriction', $data);
    }

    /**
     * Get default message based on license status.
     *
     * @param string|null $licenseStatus
     * @return string
     */
    private function getDefaultMessage(?string $licenseStatus): string
    {
        return match($licenseStatus) {
            'pending' => 'The vendor license is currently under review. Product creation will be available once the license is approved.',
            'expired' => 'The vendor license has expired. Product creation is restricted until the license is renewed.',
            'rejected' => 'The vendor license was rejected. Product creation is restricted until a new license is approved.',
            default => 'Store status is not active. Product creation is restricted until the vendor license is active.'
        };
    }
}
