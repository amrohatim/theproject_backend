<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductSpecification;
use App\Models\ProductColor;
use App\Models\ProductSize;
use App\Models\ProductBranch;
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
        $product = Product::with(['specifications', 'colors', 'sizes', 'branches', 'branch.company'])
            ->findOrFail($id);

        // Check if user has permission to edit this product
        $this->authorizeProductAccess($product);

        // Get all branches for the company
        $branches = Branch::where('company_id', $product->branch->company_id)
            ->orderBy('name')
            ->get();

        $specifications = $product->specifications;
        $colors = $product->colors;
        $sizes = $product->sizes;
        $productBranches = $product->branches;

        return view('vendor.products.specifications', compact(
            'product',
            'specifications',
            'colors',
            'sizes',
            'branches',
            'productBranches'
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
        $product = Product::findOrFail($id);

        // Check if user has permission to edit this product
        $this->authorizeProductAccess($product);

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

        return redirect()->route('vendor.products.specifications.edit', $product->id)
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
        $product = Product::findOrFail($id);

        // Check if user has permission to edit this product
        $this->authorizeProductAccess($product);

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

        // Add new colors
        $defaultColorImage = null;
        $hasDefaultColor = false;

        foreach ($request->colors as $index => $colorData) {
            $image = $colorData['image']; // Get existing image path if any
            $isDefault = isset($colorData['is_default']) ? true : false;

            // Check if a new image was uploaded
            if ($request->hasFile("color_images.$index")) {
                $file = $request->file("color_images.$index");
                $path = $file->store('product-colors', 'public');
                $image = Storage::url($path);

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

        return redirect()->route('vendor.products.specifications.edit', $product->id)
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
        $product = Product::findOrFail($id);

        // Check if user has permission to edit this product
        $this->authorizeProductAccess($product);

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

        // Delete existing sizes
        $product->sizes()->delete();

        // Add new sizes
        foreach ($request->sizes as $index => $sizeData) {
            $product->sizes()->create([
                'name' => $sizeData['name'],
                'value' => $sizeData['value'] ?? null,
                'additional_info' => $sizeData['additional_info'] ?? null,
                'price_adjustment' => $sizeData['price_adjustment'] ?? 0,
                'stock' => $sizeData['stock'] ?? 0,
                'display_order' => $sizeData['display_order'] ?? $index,
                'is_default' => isset($sizeData['is_default']) ? true : false,
            ]);
        }

        return redirect()->route('vendor.products.specifications.edit', $product->id)
            ->with('success', 'Product sizes updated successfully.');
    }

    /**
     * Update product branches.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateBranches(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        // Check if user has permission to edit this product
        $this->authorizeProductAccess($product);

        $request->validate([
            'is_multi_branch' => 'required|boolean',
            'branches' => 'required_if:is_multi_branch,1|array',
            'branches.*.branch_id' => 'required_if:is_multi_branch,1|exists:branches,id',
            'branches.*.stock' => 'nullable|integer|min:0',
            'branches.*.price' => 'nullable|numeric|min:0',
            'branches.*.is_available' => 'nullable|boolean',
        ]);

        // Update multi-branch flag
        $product->update([
            'is_multi_branch' => $request->is_multi_branch,
        ]);

        // If multi-branch is enabled, update branch associations
        if ($request->is_multi_branch) {
            // Delete existing branch associations
            $product->productBranches()->delete();

            // Add new branch associations
            foreach ($request->branches as $branchData) {
                $product->productBranches()->create([
                    'branch_id' => $branchData['branch_id'],
                    'stock' => $branchData['stock'] ?? 0,
                    'price' => $branchData['price'] ?? $product->price,
                    'is_available' => isset($branchData['is_available']) ? true : false,
                ]);
            }
        }

        return redirect()->route('vendor.products.specifications.edit', $product->id)
            ->with('success', 'Product branch settings updated successfully.');
    }

    /**
     * Check if the authenticated user has permission to access the product.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    protected function authorizeProductAccess($product)
    {
        $userCompanies = Auth::user()->companies()->pluck('id')->toArray();
        $productCompanyId = $product->branch->company_id;

        if (!in_array($productCompanyId, $userCompanies)) {
            abort(403, 'You do not have permission to access this product.');
        }
    }
}
