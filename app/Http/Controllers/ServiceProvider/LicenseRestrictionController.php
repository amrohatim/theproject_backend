<?php

namespace App\Http\Controllers\ServiceProvider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LicenseRestrictionController extends Controller
{
    /**
     * Show the license restriction page for service providers.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function show(Request $request)
    {
        $user = Auth::user();
        
        // Ensure user is authenticated and is a service provider
        if (!$user || $user->role !== 'service_provider') {
            return redirect()->route('login')->withErrors(['error' => 'Please log in as a service provider to access this page.']);
        }

        $serviceProvider = $user->serviceProvider;
        
        if (!$serviceProvider) {
            return redirect('/')->with('error', 'Service provider profile not found.');
        }

        // Get license status and message from session or defaults
        $licenseStatus = session('license_status') ?? $serviceProvider->getVendorLicenseStatus();
        $message = session('license_message') ?? $this->getDefaultMessage($licenseStatus);

        $data = [
            'user' => $user,
            'service_provider' => $serviceProvider,
            'license_status' => $licenseStatus,
            'message' => $message,
            'vendor' => $serviceProvider->getVendorUser(),
        ];

        return view('service-provider.license.restriction', $data);
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
            'pending' => 'The vendor license is currently under review. Service creation will be available once the license is approved.',
            'expired' => 'The vendor license has expired. Service creation is restricted until the license is renewed.',
            'rejected' => 'The vendor license was rejected. Service creation is restricted until a new license is approved.',
            default => 'Store status is not active. Service creation is restricted until the vendor license is active.'
        };
    }
}
