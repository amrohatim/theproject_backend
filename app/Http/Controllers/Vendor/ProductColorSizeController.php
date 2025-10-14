<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductSize;
use App\Models\ProductColorSize;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductColorSizeController extends Controller
{
    /**
     * Resolve the acting vendor user ID for queries and ownership checks.
     * If current user is a products manager, use their company's vendor owner user_id.
     */
    private function getActingVendorUserId(): int
    {
        $user = Auth::user();
        if ($user && $user->role === 'products_manager' && method_exists($user, 'productsManager') && $user->productsManager) {
            $company = $user->productsManager->company ?? null;
            if ($company && isset($company->user_id)) {
                return (int) $company->user_id;
            }
        }
        return (int) Auth::id();
    }

    /**
     * Get sizes available for a specific color.
     */
    public function getSizesForColor(Request $request): JsonResponse
    {
        $request->validate([
            'color_id' => 'required|integer',
            'product_id' => 'required|integer',
            'only_allocated' => 'nullable|boolean', // New parameter to control behavior
        ]);

        $color = ProductColor::findOrFail($request->color_id);

        // Verify the color belongs to a product owned by the authenticated vendor's company (branch-based ownership)
        $actingVendorUserId = $this->getActingVendorUserId();

        // Get branches that belong to the vendor's company
        $userBranches = Branch::whereHas('company', function ($query) use ($actingVendorUserId) {
            $query->where('user_id', $actingVendorUserId);
        })->pluck('id')->toArray();

        // Find the product and verify it belongs to one of the vendor's branches
        $product = Product::findOrFail($request->product_id);

        if (!in_array($product->branch_id, $userBranches)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to access this product.',
            ], 403);
        }

        if ($color->product_id !== $product->id) {
            return response()->json([
                'success' => false,
                'message' => 'Color does not belong to this product.',
            ], 422);
        }

        // Get existing color-size combinations for this color
        $existingCombinations = ProductColorSize::where('product_color_id', $request->color_id)
            ->get()
            ->keyBy('product_size_id');

        if ($request->only_allocated) {
            // Only get sizes that have actual allocations for this color
            $sizeIds = $existingCombinations->where('stock', '>', 0)->keys();

            $allSizes = ProductSize::where('product_id', $request->product_id)
                ->whereIn('id', $sizeIds)
                ->with('sizeCategory')
                ->orderBy('display_order')
                ->get();
        } else {
            // Get all sizes for the product (original behavior) with size category relationship
            $allSizes = ProductSize::where('product_id', $request->product_id)
                ->with('sizeCategory')
                ->orderBy('display_order')
                ->get();
        }

        $sizesData = $allSizes->map(function ($size) use ($existingCombinations) {
            $combination = $existingCombinations->get($size->id);

            return [
                'id' => $size->id,
                'name' => $size->name,
                'value' => $size->value,
                'category' => $size->sizeCategory ? $size->sizeCategory->name : 'clothes', // Add category field
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
        DB::transaction(function () use ($request) {
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
            'category' => 'nullable|string|in:clothes,shoes,hats',
            'additional_info' => 'nullable|string|max:255',
            'price_adjustment' => 'nullable|numeric',
            'stock' => 'nullable|integer|min:0',
            'is_available' => 'nullable|boolean',
        ]);

        // Verify the product belongs to the authenticated vendor's company (branch-based ownership)
        $actingVendorUserId = $this->getActingVendorUserId();

        // Get branches that belong to the vendor's company
        $userBranches = Branch::whereHas('company', function ($query) use ($actingVendorUserId) {
            $query->where('user_id', $actingVendorUserId);
        })->pluck('id')->toArray();

        // Find the product and verify it belongs to one of the vendor's branches
        $product = Product::findOrFail($request->product_id);

        if (!in_array($product->branch_id, $userBranches)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to access this product.',
            ], 403);
        }

        // Verify the color belongs to this product
        $color = ProductColor::where('product_id', $product->id)->findOrFail($request->color_id);

        // Check if size name already exists for this product
        $existingSize = ProductSize::where('product_id', $product->id)
            ->where('name', $request->name)
            ->first();

        try {
            DB::beginTransaction();

            // Create or get the base size
            if (!$existingSize) {
                $maxOrder = ProductSize::where('product_id', $product->id)->max('display_order') ?? 0;

                $existingSize = ProductSize::create([
                    'product_id' => $product->id,
                    'size_category_id' => 1, // Default to clothes category
                    'name' => $request->name,
                    'value' => $request->value,
                    'additional_info' => $request->additional_info,
                    'price_adjustment' => 0, // Base size has no price adjustment
                    'stock' => 0, // Base size has no stock
                    'display_order' => $maxOrder + 1,
                    'is_default' => false,
                ]);
            }

            // Create or update the color-size combination
            $colorSize = ProductColorSize::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'product_color_id' => $color->id,
                    'product_size_id' => $existingSize->id,
                ],
                [
                    'stock' => $request->stock ?? 0,
                    'price_adjustment' => $request->price_adjustment ?? 0,
                    'is_available' => $request->is_available ?? true,
                ]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Size created successfully.',
                'size' => [
                    'id' => $existingSize->id,
                    'name' => $existingSize->name,
                    'value' => $existingSize->value,
                    'category' => 'clothes', // Default category for backward compatibility
                    'additional_info' => $existingSize->additional_info,
                    'allocated_stock' => $colorSize->stock,
                    'price_adjustment' => $colorSize->price_adjustment,
                    'is_available' => $colorSize->is_available,
                    'display_order' => $existingSize->display_order,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create size: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an existing size for a color variant.
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

        // Verify the size and color belong to a product owned by the authenticated vendor's company (branch-based ownership)
        $actingVendorUserId = $this->getActingVendorUserId();

        // Get branches that belong to the vendor's company
        $userBranches = Branch::whereHas('company', function ($query) use ($actingVendorUserId) {
            $query->where('user_id', $actingVendorUserId);
        })->pluck('id')->toArray();

        // Find the product and verify it belongs to one of the vendor's branches
        $product = Product::findOrFail($size->product_id);

        if (!in_array($product->branch_id, $userBranches)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to access this product.',
            ], 403);
        }

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
                'message' => 'Size not found for this color variant.',
            ], 404);
        }

        try {
            // Use database transaction to ensure data consistency
            DB::transaction(function () use ($size, $colorSize, $product, $request) {
                // Check if size name change would conflict with existing sizes
                if ($size->name !== $request->name) {
                    $existingSize = ProductSize::where('product_id', $product->id)
                        ->where('name', $request->name)
                        ->where('id', '!=', $size->id)
                        ->first();

                    if ($existingSize) {
                        throw new \Exception('A size with this name already exists for this product.');
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
            });

            return response()->json([
                'success' => true,
                'message' => 'Size updated successfully.',
                'size' => [
                    'id' => $size->id,
                    'name' => $size->name,
                    'value' => $size->value,
                    'additional_info' => $size->additional_info,
                    'allocated_stock' => $colorSize->stock,
                    'price_adjustment' => $colorSize->price_adjustment,
                    'is_available' => $colorSize->is_available,
                    'display_order' => $size->display_order,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update size: ' . $e->getMessage()
            ], 500);
        }
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

        // Verify the size and color belong to a product owned by the authenticated vendor's company (branch-based ownership)
        $actingVendorUserId = $this->getActingVendorUserId();

        // Get branches that belong to the vendor's company
        $userBranches = Branch::whereHas('company', function ($query) use ($actingVendorUserId) {
            $query->where('user_id', $actingVendorUserId);
        })->pluck('id')->toArray();

        // Find the product and verify it belongs to one of the vendor's branches
        $product = Product::findOrFail($size->product_id);

        if (!in_array($product->branch_id, $userBranches)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to access this product.',
            ], 403);
        }

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
            // If the color-size combination doesn't exist, consider it already deleted
            return response()->json([
                'success' => true,
                'message' => 'Size removed successfully.',
            ]);
        }

        $colorSize->delete();

        // Note: We keep the base size for potential reuse even if no other color variants use it
        // This allows vendors to easily re-add the size to other colors later

        return response()->json([
            'success' => true,
            'message' => 'Size removed from color variant successfully.',
        ]);
    }
}
