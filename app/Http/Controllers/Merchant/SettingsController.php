<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Services\LicenseManagementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    /**
     * Show the personal settings form.
     */
    public function personal()
    {
        $user = Auth::user();
        $merchant = $user->merchantRecord;
        
        return view('merchant.settings.personal', compact('user', 'merchant'));
    }

    /**
     * Update the personal settings.
     */
    public function updatePersonal(Request $request)
    {
        $user = Auth::user();
        $merchant = $user->merchantRecord;

        // Determine which form was submitted based on the presence of password fields
        $isPasswordUpdate = $request->filled('password') || $request->filled('current_password');

        if ($isPasswordUpdate) {
            // Password update form validation
            $request->validate([
                'current_password' => 'required|string',
                'password' => ['required', 'confirmed', Password::defaults()],
            ]);

            // Verify current password
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }

            // Update password
            $user->update([
                'password' => Hash::make($request->password)
            ]);

            return redirect()->route('merchant.settings.personal')
                ->with('success', 'Password updated successfully.');
        } else {
            // Personal information update form validation
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'phone' => 'nullable|string|max:20',
            ]);

            // Update user information
            $userData = $request->only(['name', 'email', 'phone']);
            $user->update($userData);

            return redirect()->route('merchant.settings.personal')
                ->with('success', 'Personal information updated successfully.');
        }
    }

    /**
     * Show the global settings form.
     */
    public function global()
    {
        $user = Auth::user();
        $merchant = $user->merchantRecord;
        
        return view('merchant.settings.global', compact('user', 'merchant'));
    }

    /**
     * Update the global settings.
     */
    public function updateGlobal(Request $request)
    {
        $user = Auth::user();
        $merchant = $user->merchantRecord;

        // Determine which form was submitted based on the presence of specific fields
        $isDeliverySettingsUpdate = $request->has('delivery_capability') &&
                                   !$request->has('business_name') &&
                                   !$request->hasFile('logo');

        if ($isDeliverySettingsUpdate) {
            // Validate only delivery-related fields
            $request->validate([
                'delivery_capability' => 'boolean',
                'delivery_fees' => 'nullable|array',
            ]);

            if ($merchant) {
                $updateData = $request->only([
                    'delivery_capability',
                    'delivery_fees'
                ]);

                $merchant->update($updateData);
            }

            return redirect()->route('merchant.settings.global')
                ->with('success', 'Delivery settings updated successfully.');
        } else {
            // Validate business information fields
            $request->validate([
                'business_name' => 'required|string|max:255',
                'business_type' => 'nullable|string|max:100',
                'description' => 'nullable|string',
                'website' => 'nullable|url|max:255',
                'address' => 'nullable|string|max:500',
                'city' => 'nullable|string|max:100',
                'emirate' => 'nullable|string|max:100',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'delivery_capability' => 'boolean',
                'delivery_fees' => 'nullable|array',
            ]);

            if ($merchant) {
                $updateData = $request->only([
                    'business_name',
                    'business_type',
                    'description',
                    'website',
                    'address',
                    'city',
                    'emirate',
                    'delivery_capability',
                    'delivery_fees'
                ]);

                // Handle logo upload
                if ($request->hasFile('logo')) {
                    // Delete old logo if exists
                    if ($merchant->logo && Storage::exists('public/' . $merchant->logo)) {
                        Storage::delete('public/' . $merchant->logo);
                    }

                    // Store new logo
                    $logoPath = $request->file('logo')->store('merchant-logos', 'public');
                    $updateData['logo'] = $logoPath;
                }

                $merchant->update($updateData);
            }

            return redirect()->route('merchant.settings.global')
                ->with('success', 'Business information updated successfully.');
        }
    }

    /**
     * Update the merchant's license.
     */
    public function updateLicense(Request $request, LicenseManagementService $licenseService)
    {
        \Log::info('=== LICENSE UPLOAD STARTED ===');
        \Log::info('Request method: ' . $request->method());
        \Log::info('Request URL: ' . $request->fullUrl());
        \Log::info('Content-Type: ' . $request->header('Content-Type'));
        \Log::info('Content-Length: ' . $request->header('Content-Length'));
        \Log::info('Request size: ' . strlen($request->getContent()) . ' bytes');

        $user = Auth::user();
        \Log::info('User ID: ' . ($user ? $user->id : 'null'));

        $merchant = $user->merchantRecord;
        \Log::info('Merchant ID: ' . ($merchant ? $merchant->id : 'null'));

        if (!$merchant) {
            \Log::error('Merchant profile not found for user: ' . $user->id);
            return redirect()->route('merchant.settings.global')
                ->with('error', 'Merchant profile not found.');
        }

        // Log request data
        \Log::info('Request data:', [
            'has_file' => $request->hasFile('license_file'),
            'file_info' => $request->hasFile('license_file') ? [
                'name' => $request->file('license_file')->getClientOriginalName(),
                'size' => $request->file('license_file')->getSize(),
                'mime' => $request->file('license_file')->getMimeType(),
                'error' => $request->file('license_file')->getError(),
            ] : null,
            'expiry_date' => $request->license_expiry_date,
            'all_files' => $request->allFiles(),
            'all_input' => $request->all(),
        ]);

        // Validate the license upload
        \Log::info('Starting validation...');
        $request->validate([
            'license_file' => 'required|file|mimes:pdf|max:5120', // 5MB max
            'license_expiry_date' => 'required|date|after:today',
        ], [
            'license_file.required' => 'Please select a license file.',
            'license_file.mimes' => 'License file must be a PDF.',
            'license_file.max' => 'License file size must not exceed 5MB.',
            'license_expiry_date.required' => 'License expiry date is required.',
            'license_expiry_date.date' => 'Please enter a valid date.',
            'license_expiry_date.after' => 'License expiry date must be in the future.',
        ]);
        \Log::info('Validation passed successfully');

        // Use the license service to handle the upload
        \Log::info('Calling LicenseManagementService->uploadLicense...');
        try {
            $uploadResult = $licenseService->uploadLicense($merchant, $request->file('license_file'), $request->license_expiry_date);
            \Log::info('Upload service result: ' . ($uploadResult ? 'true' : 'false'));

            if ($uploadResult) {
                \Log::info('License upload successful, redirecting with success message');
                return redirect()->route('merchant.settings.global')
                    ->with('success', 'License uploaded successfully! Your license is now under review by our admin team.');
            } else {
                \Log::warning('License upload failed, service returned false');
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Failed to upload license. Please try again.');
            }
        } catch (\Exception $e) {
            \Log::error('License upload exception:', [
                'merchant_id' => $merchant->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while uploading the license. Please try again.');
        }
    }

    /**
     * View the merchant's current license file in the browser.
     */
    public function viewLicense()
    {
        $user = Auth::user();
        $merchant = $user->merchantRecord;

        if (!$merchant || !$merchant->license_file) {
            abort(404, 'No license file found.');
        }

        $filePath = storage_path('app/public/' . $merchant->license_file);

        if (!file_exists($filePath)) {
            abort(404, 'License file not found.');
        }

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($merchant->license_file) . '"'
        ]);
    }
}
