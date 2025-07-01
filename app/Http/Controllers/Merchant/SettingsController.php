<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
            'business_type' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'delivery_capability' => 'boolean',
            'delivery_fees' => 'nullable|array',
        ]);

        if ($merchant) {
            $merchant->update($request->only([
                'business_name',
                'business_type', 
                'description',
                'website',
                'delivery_capability',
                'delivery_fees'
            ]));
        }

        return redirect()->route('merchant.settings.global')
            ->with('success', 'Global settings updated successfully.');
    }
}
