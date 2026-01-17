<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\GeneralWishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GeneralWishlistController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $productIds = GeneralWishlist::where('user_id', $userId)
            ->pluck('product_id')
            ->toArray();

        if (empty($productIds)) {
            return response()->json([
                'success' => true,
                'products' => [],
            ]);
        }

        $products = Product::whereIn('id', $productIds)->get();

        return response()->json([
            'success' => true,
            'products' => $products,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
        ]);

        $userId = Auth::id();
        $productId = $validated['product_id'];

        GeneralWishlist::firstOrCreate([
            'user_id' => $userId,
            'product_id' => $productId,
        ]);

        $product = Product::find($productId);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'product' => $product,
        ]);
    }

    public function destroy($productId)
    {
        $userId = Auth::id();

        GeneralWishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->delete();

        return response()->json([
            'success' => true,
        ]);
    }

    public function clear()
    {
        $userId = Auth::id();
        GeneralWishlist::where('user_id', $userId)->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}
