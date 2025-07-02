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

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'current_password' => 'nullable|required_with:password',
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        // Update user information
        $userData = $request->only(['name', 'email', 'phone']);
        
        // Update password if provided
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        return redirect()->route('merchant.settings.personal')
            ->with('success', 'Personal settings updated successfully.');
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
            ->with('success', 'Global settings updated successfully.');
    }
}
