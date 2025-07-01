<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MiniStoreController extends Controller
{
    /**
     * Display the mini store management page.
     */
    public function index()
    {
        $user = Auth::user();
        $merchant = $user->merchantRecord;

        // If no merchant record exists, create a basic one
        if (!$merchant) {
            $merchant = $user->merchantRecord()->create([
                'business_name' => $user->name . "'s Store",
                'status' => 'pending',
                'is_verified' => false,
            ]);
        }

        return view('merchant.mini-store.index', compact('user', 'merchant'));
    }

    /**
     * Update the mini store information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $merchant = $user->merchantRecord;

        $request->validate([
            'business_name' => 'required|string|max:255',
            'business_type' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'emirate' => 'nullable|string|max:100',
            'store_location_lat' => 'nullable|numeric|between:-90,90',
            'store_location_lng' => 'nullable|numeric|between:-180,180',
            'store_location_address' => 'nullable|string|max:500',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'delivery_capability' => 'boolean',
            'delivery_fees' => 'nullable|array',
        ]);

        $data = $request->except(['logo']);

        // Convert delivery_capability checkbox to boolean
        $data['delivery_capability'] = $request->has('delivery_capability');

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($merchant && $merchant->logo && Storage::disk('public')->exists($merchant->logo)) {
                Storage::disk('public')->delete($merchant->logo);
            }

            $logo = $request->file('logo');
            $logoName = time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $logo->getClientOriginalExtension();
            $logoPath = $logo->storeAs('merchants/logos', $logoName, 'public');
            $data['logo'] = $logoPath;
        }

        // Create or update merchant record
        if ($merchant) {
            $merchant->update($data);
        } else {
            // Create new merchant record if it doesn't exist
            $data['user_id'] = $user->id;
            $data['status'] = 'pending';
            $data['is_verified'] = false;
            $merchant = \App\Models\Merchant::create($data);
        }

        return redirect()->route('merchant.mini-store')
            ->with('success', 'Mini store information updated successfully.');
    }
}
