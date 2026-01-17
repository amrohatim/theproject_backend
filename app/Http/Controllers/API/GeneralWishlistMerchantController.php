<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\GeneralWishlistMerchant;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GeneralWishlistMerchantController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $merchantIds = GeneralWishlistMerchant::where('user_id', $userId)
            ->pluck('merchant_id')
            ->toArray();

        if (empty($merchantIds)) {
            return response()->json([
                'success' => true,
                'merchants' => [],
            ]);
        }

        $merchants = Merchant::whereIn('id', $merchantIds)->get();

        return response()->json([
            'success' => true,
            'merchants' => $merchants,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'merchant_id' => ['required', 'integer', 'exists:merchants,id'],
        ]);

        $userId = Auth::id();
        $merchantId = $validated['merchant_id'];

        GeneralWishlistMerchant::firstOrCreate([
            'user_id' => $userId,
            'merchant_id' => $merchantId,
        ]);

        $merchant = Merchant::find($merchantId);

        if (!$merchant) {
            return response()->json([
                'success' => false,
                'message' => 'Merchant not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'merchant' => $merchant,
        ]);
    }

    public function destroy($merchantId)
    {
        $userId = Auth::id();

        GeneralWishlistMerchant::where('user_id', $userId)
            ->where('merchant_id', $merchantId)
            ->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}
