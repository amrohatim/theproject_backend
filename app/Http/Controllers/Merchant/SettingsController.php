<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
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
}
