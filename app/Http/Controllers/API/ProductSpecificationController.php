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

            // Get all unique colors from product_colors table
            $colors = ProductColor::select('id', 'name', 'color_code', 'image')
                ->distinct()
                ->orderBy('name')
                ->get();

            Log::info("Fetched {$colors->count()} unique colors from database");

            // Log first few colors for debugging
            foreach ($colors->take(3) as $color) {
                Log::info("Color: {$color->name} - {$color->color_code}");
            }

            $formattedColors = $colors->map(function($color) {
                // Fix image URLs if needed
                if (!empty($color->image)) {
                    $host = request()->getHost();
                    $port = request()->getPort();
                    $scheme = request()->getScheme();

                    $portString = '';
                    if (($scheme === 'http' && $port != 80) || ($scheme === 'https' && $port != 443)) {
                        $portString = ":{$port}";
                    }

                    $path = parse_url($color->image, PHP_URL_PATH);
                    if (empty($path)) {
                        $path = $color->image;
                    }

                    $color->image = "{$scheme}://{$host}{$portString}{$path}";
                }

                return [
                    'id' => $color->id,
                    'name' => $color->name,
                    'color_code' => $color->color_code,
                    'hex_code' => $color->color_code, // For compatibility
                    'image' => $color->image,
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

        // Delete existing sizes
        $product->sizes()->delete();

        // Add new sizes
        foreach ($request->sizes as $index => $size) {
            $product->sizes()->create([
                'name' => $size['name'],
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
            'message' => 'Product sizes updated successfully',
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
                ['id' => 1, 'name' => 'XXS', 'value' => 'XXS', 'category' => 'clothes'],
                ['id' => 2, 'name' => 'XS', 'value' => 'XS', 'category' => 'clothes'],
                ['id' => 3, 'name' => 'S', 'value' => 'S', 'category' => 'clothes'],
                ['id' => 4, 'name' => 'M', 'value' => 'M', 'category' => 'clothes'],
                ['id' => 5, 'name' => 'L', 'value' => 'L', 'category' => 'clothes'],
                ['id' => 6, 'name' => 'XL', 'value' => 'XL', 'category' => 'clothes'],
                ['id' => 7, 'name' => 'XXL', 'value' => 'XXL', 'category' => 'clothes'],
                ['id' => 8, 'name' => '3XL', 'value' => '3XL', 'category' => 'clothes'],
                ['id' => 9, 'name' => '4XL', 'value' => '4XL', 'category' => 'clothes'],
                ['id' => 10, 'name' => '5XL', 'value' => '5XL', 'category' => 'clothes'],
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
}
