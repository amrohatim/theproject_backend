<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ProviderProduct;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProviderWishlistController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $productIds = Wishlist::where('user_id', $userId)
            ->pluck('product_id')
            ->toArray();

        if (empty($productIds)) {
            return response()->json([
                'success' => true,
                'products' => [],
            ]);
        }

        $providerProducts = ProviderProduct::with(['category'])
            ->whereIn('id', $productIds)
            ->get();

        $products = $providerProducts->map(function ($providerProduct) {
            return $this->transformProviderProduct($providerProduct);
        })->values();

        return response()->json([
            'success' => true,
            'products' => $products,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:provider_products,id'],
        ]);

        $userId = Auth::id();
        $productId = $validated['product_id'];

        Wishlist::firstOrCreate([
            'user_id' => $userId,
            'product_id' => $productId,
        ]);

        $providerProduct = ProviderProduct::with(['category'])
            ->find($productId);

        if (!$providerProduct) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'product' => $this->transformProviderProduct($providerProduct),
        ]);
    }

    public function destroy($productId)
    {
        $userId = Auth::id();

        Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->delete();

        return response()->json([
            'success' => true,
        ]);
    }

    private function transformProviderProduct(ProviderProduct $providerProduct)
    {
        Log::info("Transforming wishlist provider product {$providerProduct->id}");

        $categoryId = $providerProduct->category_id ?? 1;
        $categoryName = null;
        if ($providerProduct->category) {
            $categoryName = $providerProduct->category->name;
        }

        return [
            'id' => $providerProduct->id,
            'category_id' => $categoryId,
            'name' => $providerProduct->product_name ?? 'Unknown Product',
            'product_name_arabic' => $providerProduct->product_name_arabic,
            'product_description_arabic' => $providerProduct->product_description_arabic,
            'price' => $providerProduct->price ?? 0,
            'original_price' => $providerProduct->original_price,
            'stock' => $providerProduct->stock ?? 0,
            'min_order' => $providerProduct->min_order,
            'description' => $providerProduct->description,
            'image' => $providerProduct->image,
            'is_available' => $providerProduct->is_active ?? true,
            'rating' => null,
            'featured' => false,
            'has_discount' => false,
            'category_name' => $categoryName,
        ];
    }
}
