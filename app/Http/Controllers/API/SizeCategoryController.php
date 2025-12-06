<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SizeCategory;
use App\Models\StandardizedSize;
use App\Models\Category;
use Illuminate\Http\Request;

class SizeCategoryController extends Controller
{
    /**
     * Get all active size categories.
     */
    public function index(Request $request)
    {
        try {
            $includeInactive = $request->boolean('include_inactive', false);

            $sizesScope = function ($query) use ($includeInactive) {
                if (!$includeInactive) {
                    $query->where('is_active', true);
                }
                $query->orderBy('display_order');
            };

            $sizeCategories = SizeCategory::query()
                ->when(!$includeInactive, fn ($q) => $q->where('is_active', true))
                ->orderBy('display_order')
                ->orderBy('name')
                ->with(['standardizedSizes' => $sizesScope])
                ->get();

            return response()->json([
                'success' => true,
                'data' => $sizeCategories,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch size categories',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get standardized sizes for a specific size category.
     */
    public function getSizes($categoryName)
    {
        try {
            $sizeCategory = SizeCategory::where('name', $categoryName)
                ->where('is_active', true)
                ->first();

            if (!$sizeCategory) {
                return response()->json([
                    'success' => false,
                    'message' => 'Size category not found',
                ], 404);
            }

            $sizes = $sizeCategory->getAvailableSizes();

            return response()->json([
                'success' => true,
                'data' => [
                    'category' => $sizeCategory,
                    'sizes' => $sizes,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch sizes',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get the default size category for a product category.
     */
    public function getCategoryDefaultSizes($categoryId)
    {
        try {
            $category = Category::with('defaultSizeCategory.standardizedSizes')
                ->find($categoryId);

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found',
                ], 404);
            }

            if (!$category->defaultSizeCategory) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'category' => $category,
                        'size_category' => null,
                        'sizes' => [],
                        'message' => 'No default size category configured for this category',
                    ],
                ]);
            }

            $sizes = $category->defaultSizeCategory->getAvailableSizes();

            return response()->json([
                'success' => true,
                'data' => [
                    'category' => $category,
                    'size_category' => $category->defaultSizeCategory,
                    'sizes' => $sizes,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch category sizes',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Validate if a size is valid for a given category.
     */
    public function validateSize(Request $request)
    {
        $request->validate([
            'size_category_name' => 'required|string',
            'size_name' => 'required|string',
        ]);

        try {
            $sizeCategory = SizeCategory::where('name', $request->size_category_name)
                ->where('is_active', true)
                ->first();

            if (!$sizeCategory) {
                return response()->json([
                    'success' => false,
                    'message' => 'Size category not found',
                    'valid' => false,
                ], 404);
            }

            $standardizedSize = StandardizedSize::where('size_category_id', $sizeCategory->id)
                ->where('name', $request->size_name)
                ->where('is_active', true)
                ->first();

            $isValid = $standardizedSize !== null;

            return response()->json([
                'success' => true,
                'valid' => $isValid,
                'data' => [
                    'size_category' => $sizeCategory,
                    'standardized_size' => $standardizedSize,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to validate size',
                'error' => $e->getMessage(),
                'valid' => false,
            ], 500);
        }
    }

    /**
     * Get size data for frontend JavaScript (matches the existing enhanced-size-selection.js format).
     */
    public function getSizeData()
    {
        try {
            $sizeData = [];

            $sizeCategories = SizeCategory::active()
                ->with(['standardizedSizes' => function ($query) {
                    $query->where('is_active', true)->orderBy('display_order');
                }])
                ->get();

            foreach ($sizeCategories as $category) {
                $categoryData = [];
                
                foreach ($category->standardizedSizes as $size) {
                    if ($category->name === 'clothes') {
                        $categoryData[$size->name] = $size->value;
                    } else {
                        $categoryData[$size->name] = $size->additional_info;
                    }
                }
                
                $sizeData[$category->name] = $categoryData;
            }

            return response()->json([
                'success' => true,
                'data' => $sizeData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch size data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
