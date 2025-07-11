<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductSpecification;
use App\Models\ProductColor;
use App\Models\ProductSize;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductSpecificationController extends Controller
{
    /**
     * Show the form for editing product specifications.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::with(['specifications', 'colors', 'sizes', 'branch'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        // Get all branches for the merchant
        $branches = Auth::user()->branches()->where('status', 'active')->get();

        $specifications = $product->specifications;
        $colors = $product->colors;
        $sizes = $product->sizes;

        return view('merchant.products.specifications', compact(
            'product',
            'specifications',
            'colors',
            'sizes',
            'branches'
        ));
    }

    /**
     * Update product specifications.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateSpecifications(Request $request, $id)
    {
        $product = Product::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'specifications' => 'nullable|array',
            'specifications.*.key' => 'nullable|string|max:255',
            'specifications.*.value' => 'nullable|string',
            'specifications.*.display_order' => 'nullable|integer',
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

        return redirect()->route('merchant.products.specifications.edit', $product->id)
            ->with('success', 'Product specifications updated successfully.');
    }

    /**
     * Update product colors.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateColors(Request $request, $id)
    {
        $product = Product::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'colors' => 'required|array',
            'colors.*.name' => 'required|string|max:255',
            'colors.*.color_code' => 'nullable|string|max:10',
            'colors.*.price_adjustment' => 'nullable|numeric',
            'colors.*.stock' => 'nullable|integer|min:0',
            'colors.*.display_order' => 'nullable|integer',
            'colors.*.is_default' => 'nullable|boolean',
            'color_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Delete existing colors
        $product->colors()->delete();

        $defaultColorImage = null;
        $hasDefaultColor = false;

        foreach ($request->colors as $index => $colorData) {
            $image = $colorData['image'] ?? null; // Get existing image path if any
            $isDefault = isset($colorData['is_default']) ? true : false;

            // Check if a new image was uploaded
            if ($request->hasFile("color_images.$index")) {
                $file = $request->file("color_images.$index");
                $path = $file->store('product-colors', 'public');
                $image = $path;

                // If this is the default color, save its image to use as the product's main image
                if ($isDefault && $image) {
                    $defaultColorImage = $image;
                }
            } else if ($isDefault && $image) {
                // If this is the default color with an existing image
                $defaultColorImage = $image;
            }

            if ($isDefault) {
                $hasDefaultColor = true;
            }

            // Create the color
            $product->colors()->create([
                'name' => $colorData['name'],
                'color_code' => $colorData['color_code'] ?? null,
                'image' => $image,
                'price_adjustment' => $colorData['price_adjustment'] ?? 0,
                'stock' => $colorData['stock'] ?? 0,
                'display_order' => $colorData['display_order'] ?? $index,
                'is_default' => $isDefault,
            ]);
        }

        // If we have a default color image, use it as the product's main image
        if ($defaultColorImage) {
            $product->update(['image' => $defaultColorImage]);
        } else if (!$hasDefaultColor) {
            // If no color is marked as default, make the first one default
            $firstColor = $product->colors()->first();
            if ($firstColor) {
                $firstColor->update(['is_default' => true]);

                // If the first color has an image, use it as the product's main image
                if ($firstColor->image) {
                    $product->update(['image' => $firstColor->image]);
                }
            }
        }

        return redirect()->route('merchant.products.specifications.edit', $product->id)
            ->with('success', 'Product colors updated successfully.');
    }

    /**
     * Update product sizes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateSizes(Request $request, $id)
    {
        $product = Product::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'sizes' => 'required|array',
            'sizes.*.name' => 'required|string|max:255',
            'sizes.*.value' => 'nullable|string',
            'sizes.*.additional_info' => 'nullable|string',
            'sizes.*.price_adjustment' => 'nullable|numeric',
            'sizes.*.stock' => 'nullable|integer|min:0',
            'sizes.*.display_order' => 'nullable|integer',
            'sizes.*.is_default' => 'nullable|boolean',
        ]);

        // Instead of deleting all existing sizes, we'll use firstOrCreate to preserve existing ones
        // and only add new sizes that don't already exist
        foreach ($request->sizes as $index => $sizeData) {
            // Use firstOrCreate to preserve existing sizes and only add new ones
            ProductSize::firstOrCreate([
                'product_id' => $product->id,
                'name' => $sizeData['name'],
            ], [
                'value' => $sizeData['value'] ?? null,
                'additional_info' => $sizeData['additional_info'] ?? null,
                'price_adjustment' => $sizeData['price_adjustment'] ?? 0,
                'stock' => $sizeData['stock'] ?? 0,
                'display_order' => $sizeData['display_order'] ?? $index,
                'is_default' => isset($sizeData['is_default']) ? true : false,
            ]);
        }

        return redirect()->route('merchant.products.specifications.edit', $product->id)
            ->with('success', 'Product sizes updated successfully. Existing sizes have been preserved.');
    }
}
