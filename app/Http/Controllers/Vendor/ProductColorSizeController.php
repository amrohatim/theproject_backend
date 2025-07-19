<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\ProductColor;
use App\Models\ProductSize;
use App\Models\ProductColorSize;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

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

        $color = ProductColor::find($request->color_id);
        
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

        $color = ProductColor::find($request->color_id);
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

        $color = ProductColor::find($request->color_id);

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
     * Uses upsert logic to preserve existing records while handling updates and new additions.
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

        $color = ProductColor::find($request->color_id);

        // Validate total allocation
        $totalAllocated = collect($request->size_allocations)->sum('stock');
        if ($totalAllocated > $color->stock) {
            return response()->json([
                'success' => false,
                'message' => 'Total stock allocation exceeds color stock limit.',
            ], 422);
        }

        // Use database transaction to ensure data consistency
        \DB::transaction(function () use ($request) {
            // Update or create combinations for provided sizes
            foreach ($request->size_allocations as $allocation) {
                if ($allocation['stock'] > 0) {
                    // Update existing or create new combination
                    ProductColorSize::updateOrCreate(
                        [
                            'product_id' => $request->product_id,
                            'product_color_id' => $request->color_id,
                            'product_size_id' => $allocation['size_id'],
                        ],
                        [
                            'stock' => $allocation['stock'],
                            'price_adjustment' => $allocation['price_adjustment'] ?? 0,
                            'is_available' => $allocation['is_available'] ?? true,
                        ]
                    );
                } else {
                    // If stock is 0, remove the combination if it exists
                    ProductColorSize::where('product_color_id', $request->color_id)
                        ->where('product_size_id', $allocation['size_id'])
                        ->delete();
                }
            }

            // Note: We intentionally do NOT delete combinations for sizes not in the request
            // This preserves existing size-color combinations that weren't part of this update
        });

        return response()->json([
            'success' => true,
            'message' => 'Color-size combinations saved successfully.',
            'remaining_stock' => $color->getRemainingStock(),
        ]);
    }
}
