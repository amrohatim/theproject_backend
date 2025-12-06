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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductColorSizeController extends Controller
{
    /**
     * Get sizes available for a specific color.
     */
    public function getSizesForColor(Request $request): JsonResponse
    {
        try {
            // Log the incoming request for debugging
            Log::info('getSizesForColor request received', [
                'color_id' => $request->color_id,
                'product_id' => $request->product_id,
                'only_allocated' => $request->only_allocated,
                'user_id' => Auth::id()
            ]);

            $request->validate([
                'color_id' => 'required|integer',
                'product_id' => 'required|integer',
                'only_allocated' => 'nullable|boolean', // New parameter to control behavior
            ]);

            // Verify the product belongs to the authenticated merchant
            $product = Product::where('user_id', Auth::id())->find($request->product_id);
            if (!$product) {
                Log::warning('Product not found or not owned by user', [
                    'product_id' => $request->product_id,
                    'user_id' => Auth::id()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found or access denied.',
                ], 422);
            }

            $color = ProductColor::where('product_id', $product->id)->find($request->color_id);
            if (!$color) {
                Log::warning('Color not found for product', [
                    'color_id' => $request->color_id,
                    'product_id' => $request->product_id
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Color not found for this product.',
                ], 422);
            }

        // Add debugging for the issue
        Log::info('getSizesForColor called', [
            'color_id' => $request->color_id,
            'product_id' => $request->product_id,
            'only_allocated' => $request->only_allocated,
            'color_name' => $color->name
        ]);

        // Get existing color-size combinations for this color
        $existingCombinations = ProductColorSize::where('product_color_id', $request->color_id)
            ->get()
            ->keyBy('product_size_id');

        Log::info('Existing combinations found', [
            'color_id' => $request->color_id,
            'combinations_count' => $existingCombinations->count(),
            'combination_ids' => $existingCombinations->keys()->toArray()
        ]);

        // If only_allocated is true, only return sizes that have actual allocations
        if ($request->only_allocated) {
            // Get only sizes that have combinations with this color
            $sizeIds = $existingCombinations->keys();

            // If no combinations found but we're looking for allocated sizes,
            // this might be a timing issue with a newly created color
            if ($sizeIds->isEmpty()) {
                Log::warning('No allocated sizes found for color, checking if color was just created', [
                    'color_id' => $request->color_id,
                    'color_created_at' => $color->created_at,
                    'seconds_since_creation' => $color->created_at->diffInSeconds(now())
                ]);

                // If the color was created very recently (within last 5 seconds),
                // wait a moment and try again to handle potential timing issues
                if ($color->created_at->diffInSeconds(now()) < 5) {
                    usleep(100000); // Wait 100ms
                    $existingCombinations = ProductColorSize::where('product_color_id', $request->color_id)
                        ->get()
                        ->keyBy('product_size_id');
                    $sizeIds = $existingCombinations->keys();

                    Log::info('Retry after delay - combinations found', [
                        'color_id' => $request->color_id,
                        'combinations_count' => $existingCombinations->count(),
                        'combination_ids' => $sizeIds->toArray()
                    ]);
                }
            }

            $allSizes = ProductSize::where('product_id', $request->product_id)
                ->whereIn('id', $sizeIds)
                ->with('sizeCategory')
                ->orderBy('display_order')
                ->get();

            Log::info('Only allocated sizes requested', [
                'color_id' => $request->color_id,
                'size_ids' => $sizeIds->toArray(),
                'sizes_found' => $allSizes->count()
            ]);
        } else {
            // Get all sizes for the product (original behavior) with size category relationship
            $allSizes = ProductSize::where('product_id', $request->product_id)
                ->with('sizeCategory')
                ->orderBy('display_order')
                ->get();

            Log::info('All sizes requested', [
                'color_id' => $request->color_id,
                'sizes_found' => $allSizes->count()
            ]);
        }

        $sizesData = $allSizes->map(function ($size) use ($existingCombinations) {
            $combination = $existingCombinations->get($size->id);

            // Get the actual category name from the size's relationship, fallback to 'clothes' for backward compatibility
            $categoryName = 'clothes'; // Default fallback
            if ($size->sizeCategory) {
                $categoryName = $size->sizeCategory->name;
            }

            return [
                'id' => $size->id,
                'name' => $size->name,
                'value' => $size->value,
                'category' => $categoryName,
                'additional_info' => $size->additional_info,
                'display_order' => $size->display_order,
                'allocated_stock' => $combination ? $combination->stock : 0,
                'is_available' => $combination ? $combination->is_available : true,
                'price_adjustment' => $combination ? $combination->price_adjustment : 0,
            ];
        });

        Log::info('getSizesForColor response', [
            'color_id' => $request->color_id,
            'sizes_count' => $sizesData->count(),
            'sizes_data' => $sizesData->toArray()
        ]);

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

        } catch (\Exception $e) {
            Log::error('Error in getSizesForColor', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'color_id' => $request->color_id ?? null,
                'product_id' => $request->product_id ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching sizes. Please try again.',
            ], 500);
        }
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
     * Uses upsert logic to preserve existing records while handling updates and new additions.
     * ENHANCED: Added comprehensive validation and transaction safety.
     */
    public function saveColorSizeCombinations(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'color_id' => 'required|exists:product_colors,id',
            'size_allocations' => 'required|array|min:1',
            'size_allocations.*.size_id' => 'required|exists:product_sizes,id',
            'size_allocations.*.stock' => 'required|integer|min:0|max:999999',
            'size_allocations.*.price_adjustment' => 'nullable|numeric|min:-99999.99|max:99999.99',
            'size_allocations.*.is_available' => 'nullable|boolean',
        ]);

        // Verify the product belongs to the authenticated merchant
        $product = Product::where('user_id', Auth::id())->findOrFail($request->product_id);

        $color = ProductColor::where('product_id', $product->id)->findOrFail($request->color_id);

        // ENHANCED VALIDATION: Check that all sizes belong to the same product
        $sizeIds = collect($request->size_allocations)->pluck('size_id');
        $invalidSizes = ProductSize::whereIn('id', $sizeIds)
            ->where('product_id', '!=', $product->id)
            ->count();
        
        if ($invalidSizes > 0) {
            return response()->json([
                'success' => false,
                'message' => 'One or more sizes do not belong to this product.',
            ], 422);
        }

        // Validate total allocation with enhanced stock calculation
        $totalAllocated = collect($request->size_allocations)->sum('stock');
        
        // Get current allocated stock excluding the sizes being updated
        $currentAllocated = ProductColorSize::where('product_color_id', $color->id)
            ->whereNotIn('product_size_id', $sizeIds)
            ->sum('stock');
        
        $finalTotalAllocation = $currentAllocated + $totalAllocated;
        
        if ($finalTotalAllocation > $color->stock) {
            return response()->json([
                'success' => false,
                'message' => "Total stock allocation ({$finalTotalAllocation}) exceeds color stock limit ({$color->stock}).",
                'details' => [
                    'color_stock' => $color->stock,
                    'current_allocated' => $currentAllocated,
                    'new_allocation' => $totalAllocated,
                    'total_would_be' => $finalTotalAllocation,
                ],
            ], 422);
        }

        try {
            Log::info('Starting color-size combination transaction', [
                'color_id' => $color->id,
                'product_id' => $product->id,
                'allocations_count' => count($request->size_allocations)
            ]);

            // Use database transaction with enhanced error handling
            $updatedRecords = DB::transaction(function () use ($request, $product, $color) {
                $records = [];
                
                // Update or create combinations for provided sizes
                foreach ($request->size_allocations as $allocation) {
                    // CRITICAL FIX: Always use updateOrCreate instead of delete
                    // This preserves data integrity and prevents corruption during edit operations
                    $record = ProductColorSize::updateOrCreate(
                        [
                            'product_id' => $request->product_id,
                            'product_color_id' => $request->color_id,
                            'product_size_id' => $allocation['size_id'],
                        ],
                        [
                            'stock' => $allocation['stock'], // Allow 0 stock - don't delete record
                            'price_adjustment' => $allocation['price_adjustment'] ?? 0,
                            'is_available' => $allocation['is_available'] ?? ($allocation['stock'] > 0), // Auto-set availability based on stock
                        ]
                    );
                    
                    $records[] = $record;
                }

                // Note: We intentionally do NOT delete combinations for sizes not in the request
                // This preserves existing size-color combinations that weren't part of this update
                
                return $records;
            });

            // Refresh the color model to get updated stock information
            $color->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Color-size combinations saved successfully.',
                'updated_records' => count($updatedRecords),
                'remaining_stock' => $color->getRemainingStock(),
            ]);
            
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error saving color-size combinations', [
                'product_id' => $request->product_id,
                'color_id' => $request->color_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to save color-size combinations. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Save a single color variant for a product.
     * This is used when we need to save a color before adding sizes to it.
     */
    public function saveColor(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'name' => 'required|string|max:255',
            'color_code' => 'nullable|string|max:10',
            'price_adjustment' => 'nullable|numeric',
            'stock' => 'required|integer|min:0',
            'display_order' => 'nullable|integer',
            'is_default' => 'nullable|boolean',
        ]);

        // Verify the product belongs to the authenticated merchant
        $product = Product::where('user_id', Auth::id())->findOrFail($request->product_id);

        try {
            DB::beginTransaction();

            // Create the color
            $color = ProductColor::create([
                'product_id' => $product->id,
                'name' => $request->name,
                'color_code' => $request->color_code ?? '#000000',
                'price_adjustment' => $request->price_adjustment ?? 0,
                'stock' => $request->stock,
                'display_order' => $request->display_order ?? 0,
                'is_default' => $request->is_default ?? false,
                'image' => null, // Image will be handled separately if needed
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Color saved successfully',
                'color' => $color
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to save color: ' . $e->getMessage()
            ], 500);
        }
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
            'category' => 'nullable|string|exists:size_categories,name',
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

        Log::info('createSize called', [
            'product_id' => $request->product_id,
            'color_id' => $request->color_id,
            'size_name' => $request->name,
            'color_name' => $color->name
        ]);

        try {
            // Use database transaction to ensure data consistency
            $result = DB::transaction(function () use ($product, $color, $request) {
                // Determine the size category ID
                $sizeCategoryId = null;
                if ($request->category) {
                    $sizeCategory = \App\Models\SizeCategory::where('name', $request->category)->first();
                    if ($sizeCategory) {
                        $sizeCategoryId = $sizeCategory->id;
                    }
                } else {
                    $sizeCategoryId = \App\Models\SizeCategory::where('name', 'clothes')->value('id');
                }

                // If size doesn't exist, create it
                $existingSize = ProductSize::where('product_id', $product->id)
                    ->where('name', $request->name)
                    ->first();

                if (!$existingSize) {
                    $maxOrder = ProductSize::where('product_id', $product->id)->max('display_order') ?? 0;

                    $existingSize = ProductSize::create([
                        'product_id' => $product->id,
                        'size_category_id' => $sizeCategoryId,
                        'name' => $request->name,
                        'value' => $request->value,
                        'additional_info' => $request->additional_info,
                        'price_adjustment' => 0, // Base size has no price adjustment
                        'stock' => 0, // Base size has no stock
                        'display_order' => $maxOrder + 1,
                        'is_default' => false,
                    ]);

                    Log::info('New ProductSize created', [
                        'size_id' => $existingSize->id,
                        'size_name' => $existingSize->name,
                        'product_id' => $product->id
                    ]);
                }

                // Check if color-size combination already exists
                $existingCombination = ProductColorSize::where('product_color_id', $color->id)
                    ->where('product_size_id', $existingSize->id)
                    ->first();

                if ($existingCombination) {
                    throw new \Exception('This size already exists for this color variant.');
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

                Log::info('ProductColorSize created successfully', [
                    'color_size_id' => $colorSize->id,
                    'color_id' => $color->id,
                    'size_id' => $existingSize->id,
                    'stock' => $colorSize->stock,
                    'price_adjustment' => $colorSize->price_adjustment,
                    'is_available' => $colorSize->is_available
                ]);

                // Return both the size and color-size combination
                return [
                    'size' => $existingSize,
                    'colorSize' => $colorSize
                ];
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Size created successfully.',
            'size' => [
                'id' => $result['size']->id,
                'name' => $result['size']->name,
                'value' => $result['size']->value,
                'category' => 'clothes', // Default category for backward compatibility
                'additional_info' => $result['size']->additional_info,
                'allocated_stock' => $result['colorSize']->stock, // Use allocated_stock to match frontend expectations
                'price_adjustment' => $result['colorSize']->price_adjustment,
                'is_available' => $result['colorSize']->is_available,
                'display_order' => $result['size']->display_order,
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

        // Find the color-size combination, or create it if it doesn't exist
        $colorSize = ProductColorSize::where('product_color_id', $color->id)
            ->where('product_size_id', $size->id)
            ->first();

        if (!$colorSize) {
            // Create the color-size combination if it doesn't exist
            // This handles cases where sizes were created before the enhanced system
            $colorSize = ProductColorSize::create([
                'product_id' => $product->id,
                'product_color_id' => $color->id,
                'product_size_id' => $size->id,
                'stock' => 0,
                'price_adjustment' => 0,
                'is_available' => true,
            ]);
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

                // Update the color-size combination - only update fields that are provided
                $updateData = [];

                // Only update stock if it's explicitly provided and not null
                if ($request->has('stock') && $request->stock !== null) {
                    $updateData['stock'] = $request->stock;
                }

                // Only update price_adjustment if it's explicitly provided
                if ($request->has('price_adjustment')) {
                    $updateData['price_adjustment'] = $request->price_adjustment ?? 0;
                }

                // Only update is_available if it's explicitly provided
                if ($request->has('is_available')) {
                    $updateData['is_available'] = $request->is_available ?? true;
                }

                // Only perform update if there are fields to update
                if (!empty($updateData)) {
                    $colorSize->update($updateData);
                }
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Size updated successfully.',
            'size' => [
                'id' => $size->id,
                'name' => $size->name,
                'value' => $size->value,
                'category' => 'clothes', // Default category for backward compatibility
                'additional_info' => $size->additional_info,
                'allocated_stock' => $colorSize->stock, // Use allocated_stock to match frontend expectations
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
            // If the color-size combination doesn't exist, consider it already deleted
            // This handles cases where sizes were created before the enhanced system
            return response()->json([
                'success' => true,
                'message' => 'Size removed successfully.',
            ]);
        }

        $colorSize->delete();

        // Note: We keep the base size for potential reuse even if no other color variants use it
        // This allows merchants to easily re-add the size to other colors later

        return response()->json([
            'success' => true,
            'message' => 'Size removed from color variant successfully.',
        ]);
    }
}
