<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductSpecification;
use App\Models\ProductColor;
use App\Models\ProductSize;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProductSpecificationController extends Controller
{
    /**
     * Get specifications for a product.
     *
     * @param  int  $productId
     * @return \Illuminate\Http\Response
     */
    public function getSpecifications($productId)
    {
        $product = Product::with(['specifications' => function ($query) {
            $query->orderBy('display_order');
        }])->findOrFail($productId);

        return response()->json([
            'success' => true,
            'specifications' => $product->specifications,
        ]);
    }

    /**
     * Add or update specifications for a product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $productId
     * @return \Illuminate\Http\Response
     */
    public function updateSpecifications(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);

        // Validate user has permission to update this product
        $this->authorizeProductAccess($product);

        $request->validate([
            'specifications' => 'nullable|array',
            'specifications.*.key' => 'nullable|string|max:255',
            'specifications.*.value' => 'nullable|string',
            'specifications.*.display_order' => 'integer',
        ]);

        // Delete existing specifications
        $product->specifications()->delete();

        // Add new specifications (filter out empty ones)
        if ($request->has('specifications') && is_array($request->specifications)) {
            foreach ($request->specifications as $index => $spec) {
                // Only create specification if both key and value are provided and not empty
                if (!empty($spec['key']) && !empty($spec['value'])) {
                    $product->specifications()->create([
                        'key' => trim($spec['key']),
                        'value' => trim($spec['value']),
                        'display_order' => $spec['display_order'] ?? $index,
                    ]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Product specifications updated successfully',
            'specifications' => $product->specifications()->orderBy('display_order')->get(),
        ]);
    }

    /**
     * Get colors for a product.
     *
     * @param  int  $productId
     * @return \Illuminate\Http\Response
     */
    public function getColors($productId)
    {
        $product = Product::with(['colors' => function ($query) {
            $query->orderBy('display_order');
        }])->findOrFail($productId);

        // Get colors and fix image URLs
        $colors = $product->colors->map(function($color) {
            // If the color has an image, make sure it uses the correct host
            if (!empty($color->image)) {
                // Get the current request host
                $host = request()->getHost();
                $port = request()->getPort();
                $scheme = request()->getScheme();

                // If the port is not the default port for the scheme, include it in the URL
                $portString = '';
                if (($scheme === 'http' && $port != 80) || ($scheme === 'https' && $port != 443)) {
                    $portString = ":{$port}";
                }

                // Extract the path from the current image URL
                $path = parse_url($color->image, PHP_URL_PATH);

                // If no path was extracted, use the original image
                if (empty($path)) {
                    $path = $color->image;
                }

                // Create a new URL with the current host
                $color->image = "{$scheme}://{$host}{$portString}{$path}";
            }

            return $color;
        });

        return response()->json([
            'success' => true,
            'colors' => $colors,
        ]);
    }

    /**
     * Add or update colors for a product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $productId
     * @return \Illuminate\Http\Response
     */
    public function updateColors(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);

        // Validate user has permission to update this product
        $this->authorizeProductAccess($product);

        $request->validate([
            'colors' => 'required|array',
            'colors.*.name' => 'required|string|max:255',
            'colors.*.color_code' => 'nullable|string|max:10',
            'colors.*.image' => 'nullable|string',
            'colors.*.price_adjustment' => 'nullable|numeric',
            'colors.*.stock' => 'nullable|integer|min:0',
            'colors.*.display_order' => 'integer',
            'colors.*.is_default' => 'boolean',
        ]);

        // Delete existing colors
        $product->colors()->delete();

        // Add new colors
        $defaultColorImage = null;
        $hasDefaultColor = false;

        foreach ($request->colors as $index => $color) {
            $isDefault = $color['is_default'] ?? false;
            $image = $color['image'] ?? null;

            // If this is the default color with an image, save it to use as the product's main image
            if ($isDefault && $image) {
                $defaultColorImage = $image;
            }

            if ($isDefault) {
                $hasDefaultColor = true;
            }

            $product->colors()->create([
                'name' => $color['name'],
                'color_code' => $color['color_code'] ?? null,
                'image' => $image,
                'price_adjustment' => $color['price_adjustment'] ?? 0,
                'stock' => $color['stock'] ?? 0,
                'display_order' => $color['display_order'] ?? $index,
                'is_default' => $isDefault,
            ]);
        }

        // If we have a default color image, use it as the product's main image
        if ($defaultColorImage) {
            $product->updateMainImageFromColorImage($defaultColorImage);
        } else if (!$hasDefaultColor) {
            // If no color is marked as default, make the first one default
            $firstColor = $product->colors()->first();
            if ($firstColor) {
                $firstColor->update(['is_default' => true]);

                // If the first color has an image, use it as the product's main image
                if ($firstColor->image) {
                    $product->updateMainImageFromColorImage($firstColor->image);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Product colors updated successfully',
            'colors' => $product->colors()->orderBy('display_order')->get(),
        ]);
    }

    /**
     * Get sizes for a product.
     *
     * @param  int  $productId
     * @return \Illuminate\Http\Response
     */
    public function getSizes($productId)
    {
        $product = Product::with(['sizes' => function ($query) {
            $query->orderBy('display_order');
        }])->findOrFail($productId);

        return response()->json([
            'success' => true,
            'sizes' => $product->sizes,
        ]);
    }

    /**
     * Get all unique colors used in products (for filter).
     *
     * @return \Illuminate\Http\Response
     */
    public function getAllProductColors()
    {
        try {
            Log::info('API: getAllProductColors called');

            // Get total count of colors in database
            $totalColors = ProductColor::count();
            Log::info("Total colors in database: {$totalColors}");

            // Get all unique colors from product_colors table (include Arabic name for localization)
            $colors = ProductColor::select('id', 'name', 'name_arabic', 'color_code')
                ->distinct()
                ->orderBy('name')
                ->get();

            Log::info("Fetched {$colors->count()} unique colors from database");

            // Log first few colors for debugging
            foreach ($colors->take(3) as $color) {
                Log::info("Color: {$color->name} - {$color->color_code}");
            }

            $formattedColors = $colors->map(function($color) {
                return [
                    'id' => $color->id,
                    'name' => $color->name,
                    'name_arabic' => $color->name_arabic,
                    'color_code' => $color->color_code,
                    'hex_code' => $color->color_code, // For compatibility
                ];
            });

            Log::info("Returning {$formattedColors->count()} formatted colors");

            return response()->json([
                'success' => true,
                'colors' => $formattedColors,
                'count' => $formattedColors->count(),
                'total_in_db' => $totalColors,
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getAllProductColors: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch product colors',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Add or update sizes for a product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $productId
     * @return \Illuminate\Http\Response
     */
    public function updateSizes(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);

        // Validate user has permission to update this product
        $this->authorizeProductAccess($product);

        $request->validate([
            'sizes' => 'required|array',
            'sizes.*.name' => 'required|string|max:255',
            'sizes.*.value' => 'nullable|string',
            'sizes.*.additional_info' => 'nullable|string',
            'sizes.*.size_category_id' => 'nullable|exists:size_categories,id',
            'sizes.*.standardized_size_id' => 'nullable|exists:standardized_sizes,id',
            'sizes.*.price_adjustment' => 'nullable|numeric',
            'sizes.*.stock' => 'nullable|integer|min:0',
            'sizes.*.display_order' => 'integer',
            'sizes.*.is_default' => 'boolean',
        ]);

        // Instead of deleting all existing sizes, we'll use firstOrCreate to preserve existing ones
        // and only add new sizes that don't already exist
        foreach ($request->sizes as $index => $size) {
            // Use firstOrCreate to preserve existing sizes and only add new ones
            ProductSize::firstOrCreate([
                'product_id' => $product->id,
                'name' => $size['name'],
            ], [
                'value' => $size['value'] ?? null,
                'additional_info' => $size['additional_info'] ?? null,
                'size_category_id' => $size['size_category_id'] ?? null,
                'standardized_size_id' => $size['standardized_size_id'] ?? null,
                'price_adjustment' => $size['price_adjustment'] ?? 0,
                'stock' => $size['stock'] ?? 0,
                'display_order' => $size['display_order'] ?? $index,
                'is_default' => $size['is_default'] ?? false,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product sizes updated successfully. Existing sizes have been preserved.',
            'sizes' => $product->sizes()->orderBy('display_order')->get(),
        ]);
    }

    /**
     * Authorize product access for the current user.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    private function authorizeProductAccess($product)
    {
        $user = Auth::user();

        // Admin can access any product
        if ($user->isAdmin()) {
            return;
        }

        // Check if the product belongs to the user's company
        $userBranches = $user->branches()->pluck('id')->toArray();

        if (!in_array($product->branch_id, $userBranches)) {
            abort(403, 'You do not have permission to update this product.');
        }
    }

    /**
     * Get standardized colors for filter dropdown (global color list).
     *
     * @return \Illuminate\Http\Response
     */
    public function getStandardizedColors()
    {
        try {
            Log::info('API: getStandardizedColors called');

            // Return the standardized color list that matches the filter system
            $standardizedColors = [
                ['id' => 1, 'name' => 'Red', 'hex_code' => '#FF0000'],
                ['id' => 2, 'name' => 'Blue', 'hex_code' => '#0000FF'],
                ['id' => 3, 'name' => 'Green', 'hex_code' => '#008000'],
                ['id' => 4, 'name' => 'Yellow', 'hex_code' => '#FFFF00'],
                ['id' => 5, 'name' => 'Black', 'hex_code' => '#000000'],
                ['id' => 6, 'name' => 'White', 'hex_code' => '#FFFFFF'],
                ['id' => 7, 'name' => 'Gray', 'hex_code' => '#808080'],
                ['id' => 8, 'name' => 'Pink', 'hex_code' => '#FFC0CB'],
                ['id' => 9, 'name' => 'Purple', 'hex_code' => '#800080'],
                ['id' => 10, 'name' => 'Orange', 'hex_code' => '#FFA500'],
                ['id' => 11, 'name' => 'Brown', 'hex_code' => '#A52A2A'],
                ['id' => 12, 'name' => 'Navy', 'hex_code' => '#000080'],
            ];

            Log::info("Returning {count} standardized colors", ['count' => count($standardizedColors)]);

            return response()->json([
                'success' => true,
                'colors' => $standardizedColors,
                'count' => count($standardizedColors),
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getStandardizedColors: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch standardized colors',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get standardized sizes for filter dropdown (global size list).
     *
     * @return \Illuminate\Http\Response
     */
    public function getStandardizedSizes()
    {
        try {
            Log::info('API: getStandardizedSizes called');

            // Return the standardized size list that matches the filter system
            $standardizedSizes = [
                // Clothing sizes
                ['id' => 1, 'name' => 'XXS', 'value' => 'XXS', 'category' => 'clothes'],
                ['id' => 2, 'name' => 'XS', 'value' => 'XS', 'category' => 'clothes'],
                ['id' => 3, 'name' => 'S', 'value' => 'S', 'category' => 'clothes'],
                ['id' => 4, 'name' => 'M', 'value' => 'M', 'category' => 'clothes'],
                ['id' => 5, 'name' => 'L', 'value' => 'L', 'category' => 'clothes'],
                ['id' => 6, 'name' => 'XL', 'value' => 'XL', 'category' => 'clothes', 'category_name_arabic' => 'الملابس'],
                ['id' => 7, 'name' => 'XXL', 'value' => 'XXL', 'category' => 'clothes', 'category_name_arabic' => 'الملابس'],
                ['id' => 8, 'name' => '3XL', 'value' => '3XL', 'category' => 'clothes', 'category_name_arabic' => 'الملابس'],
                ['id' => 9, 'name' => '4XL', 'value' => '4XL', 'category' => 'clothes', 'category_name_arabic' => 'الملابس'],
                ['id' => 10, 'name' => '5XL', 'value' => '5XL', 'category' => 'clothes', 'category_name_arabic' => 'الملابس'],

                // Shoe sizes (US sizing)
                ['id' => 11, 'name' => '5', 'value' => 'US 5', 'category' => 'shoes', 'category_name_arabic' => 'الأحذية'],
                ['id' => 12, 'name' => '5.5', 'value' => 'US 5.5', 'category' => 'shoes', 'category_name_arabic' => 'الأحذية'],
                ['id' => 13, 'name' => '6', 'value' => 'US 6', 'category' => 'shoes', 'category_name_arabic' => 'الأحذية'],
                ['id' => 14, 'name' => '6.5', 'value' => 'US 6.5', 'category' => 'shoes', 'category_name_arabic' => 'الأحذية'],
                ['id' => 15, 'name' => '7', 'value' => 'US 7', 'category' => 'shoes', 'category_name_arabic' => 'الأحذية'],
                ['id' => 16, 'name' => '7.5', 'value' => 'US 7.5', 'category' => 'shoes', 'category_name_arabic' => 'الأحذية'],
                ['id' => 17, 'name' => '8', 'value' => 'US 8', 'category' => 'shoes', 'category_name_arabic' => 'الأحذية'],
                ['id' => 18, 'name' => '8.5', 'value' => 'US 8.5', 'category' => 'shoes', 'category_name_arabic' => 'الأحذية'],
                ['id' => 19, 'name' => '9', 'value' => 'US 9', 'category' => 'shoes', 'category_name_arabic' => 'الأحذية'],
                ['id' => 20, 'name' => '9.5', 'value' => 'US 9.5', 'category' => 'shoes', 'category_name_arabic' => 'الأحذية'],
                ['id' => 21, 'name' => '10', 'value' => 'US 10', 'category' => 'shoes', 'category_name_arabic' => 'الأحذية'],
                ['id' => 22, 'name' => '10.5', 'value' => 'US 10.5', 'category' => 'shoes', 'category_name_arabic' => 'الأحذية'],
                ['id' => 23, 'name' => '11', 'value' => 'US 11', 'category' => 'shoes', 'category_name_arabic' => 'الأحذية'],
                ['id' => 24, 'name' => '11.5', 'value' => 'US 11.5', 'category' => 'shoes', 'category_name_arabic' => 'الأحذية'],
                ['id' => 25, 'name' => '12', 'value' => 'US 12', 'category' => 'shoes', 'category_name_arabic' => 'الأحذية'],
                ['id' => 26, 'name' => '13', 'value' => 'US 13', 'category' => 'shoes', 'category_name_arabic' => 'الأحذية'],
                ['id' => 27, 'name' => '14', 'value' => 'US 14', 'category' => 'shoes', 'category_name_arabic' => 'الأحذية'],

                // Hat sizes (circumference in cm and inches)
                ['id' => 28, 'name' => 'XS', 'value' => '53-54 cm (20.9-21.3")', 'category' => 'hats', 'category_name_arabic' => 'القبعات'],
                ['id' => 29, 'name' => 'S', 'value' => '55-56 cm (21.7-22.0")', 'category' => 'hats', 'category_name_arabic' => 'القبعات'],
                ['id' => 30, 'name' => 'M', 'value' => '57-58 cm (22.4-22.8")', 'category' => 'hats', 'category_name_arabic' => 'القبعات'],
                ['id' => 31, 'name' => 'L', 'value' => '59-60 cm (23.2-23.6")', 'category' => 'hats', 'category_name_arabic' => 'القبعات'],
                ['id' => 32, 'name' => 'XL', 'value' => '61-62 cm (24.0-24.4")', 'category' => 'hats', 'category_name_arabic' => 'القبعات'],
                ['id' => 33, 'name' => 'XXL', 'value' => '63-64 cm (24.8-25.2")', 'category' => 'hats', 'category_name_arabic' => 'القبعات'],
            ];

            Log::info("Returning {count} standardized sizes", ['count' => count($standardizedSizes)]);

            return response()->json([
                'success' => true,
                'sizes' => $standardizedSizes,
                'count' => count($standardizedSizes),
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getStandardizedSizes: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch standardized sizes',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get available sizes from actual products (only sizes that exist in product_sizes table)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAvailableSizes()
    {
        try {
            // Get all unique sizes from product_sizes table and map them to standardized_sizes
            $availableSizes = DB::table('product_sizes as ps')
                ->join('products as p', 'ps.product_id', '=', 'p.id')
                ->leftJoin('standardized_sizes as ss', 'ps.name', '=', 'ss.name')
                ->leftJoin('size_categories as sc', 'ss.size_category_id', '=', 'sc.id')
                ->select(
                    'ss.id as standardized_id',
                    'ps.name',
                    'ps.value',
                    'sc.name_arabic as category_name_arabic',
                    DB::raw('COALESCE(sc.name, CASE
                        WHEN ps.name IN ("XXS", "XS", "S", "M", "L", "XL", "XXL", "3XL", "4XL", "5XL") THEN "clothes"
                        WHEN ps.name REGEXP "^[0-9]+$" AND CAST(ps.name AS UNSIGNED) BETWEEN 16 AND 48 THEN "shoes"
                        WHEN ps.name REGEXP "^[0-9]+$" AND CAST(ps.name AS UNSIGNED) BETWEEN 50 AND 64 THEN "hats"
                        ELSE "clothes"
                    END) as category'),
                    DB::raw('COUNT(DISTINCT ps.product_id) as product_count'),
                    DB::raw('SUM(ps.stock) as total_stock')
                )
                ->where('p.is_available', true)
                ->where('ps.stock', '>', 0)
                ->groupBy('ss.id', 'ps.name', 'ps.value', 'sc.name')
                ->orderBy('category')
                ->orderBy('ps.name')
                ->get();

            // Transform to match the expected format, using standardized_sizes IDs where available
            $formattedSizes = [];
            $fallbackIdCounter = 1000; // Start high to avoid conflicts

            foreach ($availableSizes as $size) {
                // Use standardized_size ID if available, otherwise generate a fallback ID
                $sizeId = $size->standardized_id ?: $fallbackIdCounter++;

                $formattedSizes[] = [
                    'id' => $sizeId,
                    'name' => $size->name,
                    'value' => $size->value ?: $size->name,
                    'category' => $size->category,
                    'category_name_arabic' => $size->category_name_arabic, // expose size_categories.name_arabic
                    'product_count' => $size->product_count,
                    'total_stock' => $size->total_stock,
                    'has_standardized_id' => $size->standardized_id !== null
                ];
            }

            Log::info('Available sizes fetched', [
                'total_sizes' => count($formattedSizes),
                'categories' => array_unique(array_column($formattedSizes, 'category'))
            ]);

            return response()->json([
                'success' => true,
                'sizes' => $formattedSizes,
                'count' => count($formattedSizes),
                'categories' => array_values(array_unique(array_column($formattedSizes, 'category')))
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching available sizes', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error fetching available sizes',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
