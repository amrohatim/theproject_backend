<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\ProductColor;
use App\Models\ProductSize;
use App\Models\ProductColorSize;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ProductColorSizeController extends Controller
{
    /**
     * Get sizes available for a specific color.
     */
    public function getSizesForColor(Request $request): JsonResponse
    {
        $request->validate([
            'color_id' => 'required|exists:product_colors,id',
            'product_id' => 'required|exists:products,id',
        ]);

        // Verify the product belongs to the authenticated merchant
        $product = Product::where('user_id', Auth::id())->findOrFail($request->product_id);
        
        $color = ProductColor::where('product_id', $product->id)->findOrFail($request->color_id);
        
        // Get all sizes for the product
        $allSizes = ProductSize::where('product_id', $request->product_id)
            ->orderBy('display_order')
            ->get();

        // Get existing color-size combinations for this color
        $existingCombinations = ProductColorSize::where('product_color_id', $request->color_id)
            ->get()
            ->keyBy('product_size_id');

        $sizesData = $allSizes->map(function ($size) use ($existingCombinations) {
            $combination = $existingCombinations->get($size->id);
            
            return [
                'id' => $size->id,
                'name' => $size->name,
                'value' => $size->value,
                'additional_info' => $size->additional_info,
                'display_order' => $size->display_order,
                'allocated_stock' => $combination ? $combination->stock : 0,
                'is_available' => $combination ? $combination->is_available : true,
                'price_adjustment' => $combination ? $combination->price_adjustment : 0,
            ];
        });

        return response()->json([
            'success' => true,
            'color' => [
                'id' => $color->id,
                'name' => $color->name,
                'total_stock' => $color->stock,
                'allocated_stock' => $color->getAllocatedStock(),
                'remaining_stock' => $color->getRemainingStock(),
            ],
            'sizes' => $sizesData,
        ]);
    }

    /**
     * Validate stock allocation for a color.
     */
    public function validateStockAllocation(Request $request): JsonResponse
    {
        $request->validate([
            'color_id' => 'required|exists:product_colors,id',
            'size_allocations' => 'required|array',
            'size_allocations.*.size_id' => 'required|exists:product_sizes,id',
            'size_allocations.*.stock' => 'required|integer|min:0',
        ]);

        $color = ProductColor::findOrFail($request->color_id);
        
        // Verify the color belongs to a product owned by the authenticated merchant
        $product = Product::where('user_id', Auth::id())->findOrFail($color->product_id);
        
        $totalAllocated = collect($request->size_allocations)->sum('stock');
        
        // Get current allocated stock (excluding the sizes being updated)
        $currentAllocated = $color->getAllocatedStock();
        $sizeIds = collect($request->size_allocations)->pluck('size_id');
        $existingAllocations = ProductColorSize::where('product_color_id', $request->color_id)
            ->whereIn('product_size_id', $sizeIds)
            ->sum('stock');
        
        $netNewAllocation = $totalAllocated - $existingAllocations;
        $finalTotalAllocation = $currentAllocated + $netNewAllocation;

        $isValid = $finalTotalAllocation <= $color->stock;
        $remainingStock = max(0, $color->stock - $finalTotalAllocation);

        return response()->json([
            'success' => true,
            'is_valid' => $isValid,
            'color_total_stock' => $color->stock,
            'current_allocated' => $currentAllocated,
            'new_allocation' => $totalAllocated,
            'final_total_allocation' => $finalTotalAllocation,
            'remaining_stock' => $remainingStock,
            'message' => $isValid 
                ? 'Stock allocation is valid' 
                : "Stock allocation exceeds available stock. Maximum allowed: {$color->stock}",
        ]);
    }

    /**
     * Get color stock information.
     */
    public function getColorStockInfo(Request $request): JsonResponse
    {
        $request->validate([
            'color_id' => 'required|exists:product_colors,id',
        ]);

        $color = ProductColor::findOrFail($request->color_id);
        
        // Verify the color belongs to a product owned by the authenticated merchant
        $product = Product::where('user_id', Auth::id())->findOrFail($color->product_id);

        return response()->json([
            'success' => true,
            'color' => [
                'id' => $color->id,
                'name' => $color->name,
                'total_stock' => $color->stock,
                'allocated_stock' => $color->getAllocatedStock(),
                'remaining_stock' => $color->getRemainingStock(),
            ],
        ]);
    }

    /**
     * Save color-size combinations.
     */
    public function saveColorSizeCombinations(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'color_id' => 'required|exists:product_colors,id',
            'size_allocations' => 'required|array',
            'size_allocations.*.size_id' => 'required|exists:product_sizes,id',
            'size_allocations.*.stock' => 'required|integer|min:0',
            'size_allocations.*.price_adjustment' => 'nullable|numeric',
            'size_allocations.*.is_available' => 'nullable|boolean',
        ]);

        // Verify the product belongs to the authenticated merchant
        $product = Product::where('user_id', Auth::id())->findOrFail($request->product_id);
        
        $color = ProductColor::where('product_id', $product->id)->findOrFail($request->color_id);
        
        // Validate total allocation
        $totalAllocated = collect($request->size_allocations)->sum('stock');
        if ($totalAllocated > $color->stock) {
            return response()->json([
                'success' => false,
                'message' => 'Total stock allocation exceeds color stock limit.',
            ], 422);
        }

        // Delete existing combinations for this color
        ProductColorSize::where('product_color_id', $request->color_id)->delete();

        // Create new combinations
        foreach ($request->size_allocations as $allocation) {
            if ($allocation['stock'] > 0) {
                ProductColorSize::create([
                    'product_id' => $request->product_id,
                    'product_color_id' => $request->color_id,
                    'product_size_id' => $allocation['size_id'],
                    'stock' => $allocation['stock'],
                    'price_adjustment' => $allocation['price_adjustment'] ?? 0,
                    'is_available' => $allocation['is_available'] ?? true,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Color-size combinations saved successfully.',
            'remaining_stock' => $color->getRemainingStock(),
        ]);
    }

    /**
     * Create a new size for a color variant.
     */
    public function createSize(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'color_id' => 'required|exists:product_colors,id',
            'name' => 'required|string|max:255',
            'value' => 'nullable|string|max:255',
            'additional_info' => 'nullable|string|max:255',
            'price_adjustment' => 'nullable|numeric',
            'stock' => 'nullable|integer|min:0',
            'is_available' => 'nullable|boolean',
        ]);

        // Verify the product belongs to the authenticated merchant
        $product = Product::where('user_id', Auth::id())->findOrFail($request->product_id);

        // Verify the color belongs to this product
        $color = ProductColor::where('product_id', $product->id)->findOrFail($request->color_id);

        // Check if size name already exists for this product
        $existingSize = ProductSize::where('product_id', $product->id)
            ->where('name', $request->name)
            ->first();

        // If size doesn't exist, create it
        if (!$existingSize) {
            $maxOrder = ProductSize::where('product_id', $product->id)->max('display_order') ?? 0;

            $existingSize = ProductSize::create([
                'product_id' => $product->id,
                'name' => $request->name,
                'value' => $request->value,
                'additional_info' => $request->additional_info,
                'price_adjustment' => 0, // Base size has no price adjustment
                'stock' => 0, // Base size has no stock
                'display_order' => $maxOrder + 1,
                'is_default' => false,
            ]);
        }

        // Check if color-size combination already exists
        $existingCombination = ProductColorSize::where('product_color_id', $color->id)
            ->where('product_size_id', $existingSize->id)
            ->first();

        if ($existingCombination) {
            return response()->json([
                'success' => false,
                'message' => 'This size already exists for this color variant.',
            ], 422);
        }

        // Create the color-size combination
        $colorSize = ProductColorSize::create([
            'product_id' => $product->id,
            'product_color_id' => $color->id,
            'product_size_id' => $existingSize->id,
            'stock' => $request->stock ?? 0,
            'price_adjustment' => $request->price_adjustment ?? 0,
            'is_available' => $request->is_available ?? true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Size created successfully.',
            'size' => [
                'id' => $existingSize->id,
                'name' => $existingSize->name,
                'value' => $existingSize->value,
                'additional_info' => $existingSize->additional_info,
                'stock' => $colorSize->stock,
                'price_adjustment' => $colorSize->price_adjustment,
                'is_available' => $colorSize->is_available,
                'display_order' => $existingSize->display_order,
            ],
        ]);
    }

    /**
     * Update an existing color-size combination.
     */
    public function updateSize(Request $request): JsonResponse
    {
        $request->validate([
            'size_id' => 'required|exists:product_sizes,id',
            'color_id' => 'required|exists:product_colors,id',
            'name' => 'required|string|max:255',
            'value' => 'nullable|string|max:255',
            'additional_info' => 'nullable|string|max:255',
            'price_adjustment' => 'nullable|numeric',
            'stock' => 'nullable|integer|min:0',
            'is_available' => 'nullable|boolean',
        ]);

        $size = ProductSize::findOrFail($request->size_id);
        $color = ProductColor::findOrFail($request->color_id);

        // Verify the size and color belong to a product owned by the authenticated merchant
        $product = Product::where('user_id', Auth::id())->findOrFail($size->product_id);

        if ($color->product_id !== $product->id) {
            return response()->json([
                'success' => false,
                'message' => 'Color does not belong to this product.',
            ], 422);
        }

        // Find the color-size combination
        $colorSize = ProductColorSize::where('product_color_id', $color->id)
            ->where('product_size_id', $size->id)
            ->first();

        if (!$colorSize) {
            return response()->json([
                'success' => false,
                'message' => 'Color-size combination not found.',
            ], 404);
        }

        // Check if size name change would conflict with existing sizes
        if ($size->name !== $request->name) {
            $existingSize = ProductSize::where('product_id', $product->id)
                ->where('name', $request->name)
                ->where('id', '!=', $size->id)
                ->first();

            if ($existingSize) {
                return response()->json([
                    'success' => false,
                    'message' => 'A size with this name already exists for this product.',
                ], 422);
            }

            // Update the base size information
            $size->update([
                'name' => $request->name,
                'value' => $request->value,
                'additional_info' => $request->additional_info,
            ]);
        }

        // Update the color-size combination
        $colorSize->update([
            'stock' => $request->stock ?? 0,
            'price_adjustment' => $request->price_adjustment ?? 0,
            'is_available' => $request->is_available ?? true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Size updated successfully.',
            'size' => [
                'id' => $size->id,
                'name' => $size->name,
                'value' => $size->value,
                'additional_info' => $size->additional_info,
                'stock' => $colorSize->stock,
                'price_adjustment' => $colorSize->price_adjustment,
                'is_available' => $colorSize->is_available,
                'display_order' => $size->display_order,
            ],
        ]);
    }

    /**
     * Delete a color-size combination.
     */
    public function deleteSize(Request $request): JsonResponse
    {
        $request->validate([
            'size_id' => 'required|exists:product_sizes,id',
            'color_id' => 'required|exists:product_colors,id',
        ]);

        $size = ProductSize::findOrFail($request->size_id);
        $color = ProductColor::findOrFail($request->color_id);

        // Verify the size and color belong to a product owned by the authenticated merchant
        $product = Product::where('user_id', Auth::id())->findOrFail($size->product_id);

        if ($color->product_id !== $product->id) {
            return response()->json([
                'success' => false,
                'message' => 'Color does not belong to this product.',
            ], 422);
        }

        // Find and delete the color-size combination
        $colorSize = ProductColorSize::where('product_color_id', $color->id)
            ->where('product_size_id', $size->id)
            ->first();

        if (!$colorSize) {
            return response()->json([
                'success' => false,
                'message' => 'Color-size combination not found.',
            ], 404);
        }

        $colorSize->delete();

        // Check if this size is no longer used by any color variants
        $otherColorSizes = ProductColorSize::where('product_size_id', $size->id)->exists();

        // If no other color variants use this size, we could optionally delete the base size
        // For now, we'll keep the base size for potential reuse

        return response()->json([
            'success' => true,
            'message' => 'Size removed from color variant successfully.',
        ]);
    }
}
