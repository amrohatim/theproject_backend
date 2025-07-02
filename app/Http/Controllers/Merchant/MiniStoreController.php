<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            'store_location_lat' => 'nullable|numeric|between:-90,90',
            'store_location_lng' => 'nullable|numeric|between:-180,180',
            'store_location_address' => 'nullable|string|max:500',
        ]);

        // Only handle location-related data
        $data = $request->only([
            'store_location_lat',
            'store_location_lng',
            'store_location_address'
        ]);

        // Create or update merchant record
        if ($merchant) {
            $merchant->update($data);
        } else {
            // Create new merchant record if it doesn't exist
            $data['user_id'] = $user->id;
            $data['business_name'] = $user->name . "'s Store";
            $data['status'] = 'pending';
            $data['is_verified'] = false;
            $merchant = \App\Models\Merchant::create($data);
        }

        return redirect()->route('merchant.mini-store')
            ->with('success', 'Store location updated successfully.');
    }
}
