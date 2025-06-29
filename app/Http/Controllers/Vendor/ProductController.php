<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::query()
            ->with(['branch', 'category'])
            ->whereHas('branch', function ($query) {
                $query->whereHas('company', function ($query) {
                    $query->where('user_id', Auth::id());
                });
            });

        // Apply filters
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('category')) {
            $query->filterByCategory($request->category);
        }

        if ($request->filled('branch')) {
            $query->filterByBranch($request->branch);
        }

        $products = $query->latest()->paginate(10);

        // Get categories for filter dropdown
        $categories = Category::where('type', 'product')
            ->whereNull('parent_id')
            ->with(['children' => function($query) {
                $query->orderBy('name');
            }])
            ->orderBy('name')
            ->get();

        // Get branches that belong to the vendor's company for filter dropdown
        $branches = Branch::whereHas('company', function ($query) {
            $query->where('user_id', Auth::id());
        })->orderBy('name')->get();

        return view('vendor.products.index', compact('products', 'categories', 'branches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get product categories with their children - force a fresh query to get the latest data
        $parentCategories = Category::where('type', 'product')
            ->whereNull('parent_id')
            ->with(['children' => function($query) {
                $query->orderBy('name');
            }])
            ->orderBy('name')
            ->get();

        // Get branches that belong to the vendor's company
        $branches = Branch::whereHas('company', function ($query) {
            $query->where('user_id', Auth::id());
        })->orderBy('name')->get();

        // Check if the vendor has any branches
        if ($branches->isEmpty()) {
            return redirect()->route('vendor.branches.create')
                ->with('warning', 'You need to create a branch before adding products. Please create a branch first.');
        }

        // Check if there are any categories
        if ($parentCategories->isEmpty()) {
            return redirect()->route('vendor.products.index')
                ->with('warning', 'No product categories found. Please contact the administrator.');
        }

        return view('vendor.products.create', compact('parentCategories', 'branches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'branch_id' => 'required|exists:branches,id',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            // Colors validation - now required
            'colors' => 'required|array|min:1',
            'colors.*.name' => 'required|string|max:255',
            'colors.*.color_code' => 'nullable|string|max:10',
            'colors.*.price_adjustment' => 'nullable|numeric',
            'colors.*.stock' => 'nullable|integer|min:0',
            'colors.*.display_order' => 'nullable|integer',
            'colors.*.is_default' => 'nullable|boolean',
            'color_images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            // Color-specific sizes validation
            'colors.*.sizes' => 'nullable|array',
            'colors.*.sizes.*.category' => 'required_with:colors.*.sizes|string|in:clothes,shoes,hats',
            'colors.*.sizes.*.name' => 'required_with:colors.*.sizes|string|max:255',
            'colors.*.sizes.*.value' => 'nullable|string',
            'colors.*.sizes.*.additional_info' => 'nullable|string',
            'colors.*.sizes.*.stock' => 'nullable|integer|min:0',
            'colors.*.sizes.*.price_adjustment' => 'nullable|numeric',
            'colors.*.sizes.*.display_order' => 'nullable|integer',
            'colors.*.sizes.*.is_default' => 'nullable|boolean',
            // Color-size allocations validation (alternative data structure)
            'color_size_allocations' => 'nullable|array',
            'color_size_allocations.*' => 'nullable|array',
            'color_size_allocations.*.*' => 'nullable|array',
            'color_size_allocations.*.*.category' => 'nullable|string|in:clothes,shoes,hats',
            'color_size_allocations.*.*.size_name' => 'nullable|string|max:255',
            'color_size_allocations.*.*.size_value' => 'nullable|string|max:255',
            'color_size_allocations.*.*.stock' => 'nullable|integer|min:0',
            'color_size_allocations.*.*.price_adjustment' => 'nullable|numeric',
            'color_size_allocations.*.*.additional_info' => 'nullable|string',
            'color_size_allocations.*.*.display_order' => 'nullable|integer',
            'color_size_allocations.*.*.is_default' => 'nullable|boolean',
            // Specifications validation - made optional
            'specifications' => 'nullable|array',
            'specifications.*.key' => 'nullable|string|max:255',
            'specifications.*.value' => 'nullable|string',
            'specifications.*.display_order' => 'nullable|integer',
            // Multi-branch validation
            'is_multi_branch' => 'nullable|boolean',
            'branches' => 'nullable|array',
            'branches.*.branch_id' => 'required_with:branches|exists:branches,id',
            'branches.*.stock' => 'nullable|integer|min:0',
            'branches.*.price' => 'nullable|numeric|min:0',
            'branches.*.is_available' => 'nullable|boolean',
        ]);

        // Verify that the branch belongs to the vendor's company
        $branch = Branch::findOrFail($request->branch_id);

        // Check if the branch's company belongs to the authenticated user
        $companyBelongsToUser = $branch->company && $branch->company->user_id === Auth::id();

        if (!$companyBelongsToUser) {
            return redirect()->back()->with('error', 'You do not have permission to add products to this branch.');
        }

        $data = $request->except(['specifications', 'colors', 'sizes', 'branches', 'color_images']);
        $data['is_available'] = $request->has('is_available') ? true : false;
        $data['is_multi_branch'] = $request->has('is_multi_branch') ? true : false;

        // We'll set the image later from the default color image

        // Create the product
        $product = Product::create($data);

        // Add colors with their images (required)
        $defaultColorImage = null;
        $hasDefaultColor = false;

        foreach ($request->colors as $index => $colorData) {
            $isDefault = isset($colorData['is_default']) ? true : false;

            // If this is marked as default or no default has been set yet
            if ($isDefault) {
                $hasDefaultColor = true;
            }

            // Process the color image (required)
            if ($request->hasFile("color_images.$index")) {
                $file = $request->file("color_images.$index");
                $path = $file->store('product-colors', 'public');

                // Ensure consistent path format: /storage/product-colors/filename.jpg
                $image = '/storage/' . $path;

                // Log the image path for debugging
                \Illuminate\Support\Facades\Log::debug("Stored color image at path: {$path}, URL: {$image}");

                // If this is the default color, save its image to use as the product's main image
                if ($isDefault) {
                    $defaultColorImage = $image;
                }

                // Ensure the file is accessible by copying it if needed
                $sourceFile = storage_path('app/public/' . $path);
                $publicFile = public_path('storage/' . $path);

                // Make sure the directory exists
                if (!file_exists(dirname($publicFile))) {
                    mkdir(dirname($publicFile), 0755, true);
                }

                // If the file doesn't exist in the public directory, copy it there
                if (!file_exists($publicFile) && file_exists($sourceFile)) {
                    copy($sourceFile, $publicFile);
                    \Illuminate\Support\Facades\Log::debug("Copied image from {$sourceFile} to {$publicFile}");
                }
            } else {
                // Return with error if image is missing
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Each color must have an associated image.');
            }

            $color = $product->colors()->create([
                'name' => $colorData['name'],
                'color_code' => $colorData['color_code'] ?? null,
                'image' => $image,
                'price_adjustment' => $colorData['price_adjustment'] ?? 0,
                'stock' => $colorData['stock'] ?? 0,
                'display_order' => $colorData['display_order'] ?? $index,
                'is_default' => $isDefault,
            ]);

            // Process sizes for this color
            if (isset($colorData['sizes']) && is_array($colorData['sizes'])) {
                foreach ($colorData['sizes'] as $sizeIndex => $sizeData) {
                    if (!empty($sizeData['name'])) {
                        $size = $product->sizes()->create([
                            'name' => $sizeData['name'],
                            'value' => $sizeData['value'] ?? null,
                            'additional_info' => $sizeData['additional_info'] ?? null,
                            'price_adjustment' => $sizeData['price_adjustment'] ?? 0,
                            'stock' => $sizeData['stock'] ?? 0,
                            'display_order' => $sizeData['display_order'] ?? $sizeIndex,
                            'is_default' => isset($sizeData['is_default']) ? true : false,
                        ]);

                        // Create the color-size combination
                        if ($sizeData['stock'] > 0) {
                            \App\Models\ProductColorSize::create([
                                'product_id' => $product->id,
                                'product_color_id' => $color->id,
                                'product_size_id' => $size->id,
                                'stock' => $sizeData['stock'],
                                'price_adjustment' => $sizeData['price_adjustment'] ?? 0,
                                'is_available' => true,
                            ]);
                        }
                    }
                }
            }
        }

        // Process color-size allocations if provided (alternative data structure)
        if ($request->has('color_size_allocations') && is_array($request->color_size_allocations)) {
            $this->processColorSizeAllocations($request->color_size_allocations, $product);
        }

        // If no color is marked as default, make the first one default
        if (!$hasDefaultColor) {
            $firstColor = $product->colors()->first();
            if ($firstColor) {
                $firstColor->update(['is_default' => true]);

                // Use the first color's image as the default
                $defaultColorImage = $firstColor->getRawOriginal('image');
            }
        }

        // Set the product's main image to the default color image
        if ($defaultColorImage) {
            \Illuminate\Support\Facades\Log::debug("Setting main product image to: {$defaultColorImage}");
            $product->updateMainImageFromColorImage($defaultColorImage);

            // Double-check that the image was set correctly
            $product->refresh();
            \Illuminate\Support\Facades\Log::debug("Product image after update: {$product->getRawOriginal('image')}");
        } else {
            // This shouldn't happen with our validation, but just in case
            return redirect()->back()
                ->withInput()
                ->with('error', 'Please select a default color with an image.');
        }



        // Add specifications if provided (filter out empty ones)
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

        // Add branch associations if multi-branch is enabled
        if ($request->has('is_multi_branch') && $request->is_multi_branch && $request->has('branches')) {
            foreach ($request->branches as $branchData) {
                $product->productBranches()->create([
                    'branch_id' => $branchData['branch_id'],
                    'stock' => $branchData['stock'] ?? 0,
                    'price' => $branchData['price'] ?? $product->price,
                    'is_available' => isset($branchData['is_available']) ? true : false,
                ]);
            }
        }

        return redirect()->route('vendor.products.index')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        // Check if the product belongs to the vendor's company
        $userBranches = Branch::whereHas('company', function ($query) {
            $query->where('user_id', Auth::id());
        })->pluck('id')->toArray();

        if (!in_array($product->branch_id, $userBranches)) {
            return redirect()->route('vendor.products.index')
                ->with('error', 'You do not have permission to edit this product.');
        }

        // Get product categories with their children - same structure as create
        $parentCategories = Category::where('type', 'product')
            ->whereNull('parent_id')
            ->with(['children' => function($query) {
                $query->orderBy('name');
            }])
            ->orderBy('name')
            ->get();

        // Get branches that belong to the vendor's company
        $branches = Branch::whereHas('company', function ($query) {
            $query->where('user_id', Auth::id());
        })->orderBy('name')->get();

        // Load product with all related data
        $product->load(['specifications', 'colors', 'sizes', 'branches']);

        return view('vendor.products.edit', compact('product', 'parentCategories', 'branches'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        // Check if the product belongs to the vendor's company
        $userBranches = Branch::whereHas('company', function ($query) {
            $query->where('user_id', Auth::id());
        })->pluck('id')->toArray();

        if (!in_array($product->branch_id, $userBranches)) {
            return redirect()->route('vendor.products.index')
                ->with('error', 'You do not have permission to update this product.');
        }

        // Enhanced validation to match create method
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'branch_id' => 'required|exists:branches,id',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            // Colors validation - now required for updates too
            'colors' => 'required|array|min:1',
            'colors.*.name' => 'required|string|max:255',
            'colors.*.color_code' => 'nullable|string|max:10',
            'colors.*.price_adjustment' => 'nullable|numeric',
            'colors.*.stock' => 'nullable|integer|min:0',
            'colors.*.display_order' => 'nullable|integer',
            'colors.*.is_default' => 'nullable|boolean',
            'color_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            // Color-specific sizes validation
            'colors.*.sizes' => 'nullable|array',
            'colors.*.sizes.*.category' => 'required_with:colors.*.sizes|string|in:clothes,shoes,hats',
            'colors.*.sizes.*.name' => 'required_with:colors.*.sizes|string|max:255',
            'colors.*.sizes.*.value' => 'nullable|string',
            'colors.*.sizes.*.additional_info' => 'nullable|string',
            'colors.*.sizes.*.stock' => 'nullable|integer|min:0',
            'colors.*.sizes.*.price_adjustment' => 'nullable|numeric',
            'colors.*.sizes.*.display_order' => 'nullable|integer',
            'colors.*.sizes.*.is_default' => 'nullable|boolean',
            // Color-size allocations validation (alternative data structure)
            'color_size_allocations' => 'nullable|array',
            'color_size_allocations.*' => 'nullable|array',
            'color_size_allocations.*.*' => 'nullable|array',
            'color_size_allocations.*.*.category' => 'nullable|string|in:clothes,shoes,hats',
            'color_size_allocations.*.*.size_name' => 'nullable|string|max:255',
            'color_size_allocations.*.*.size_value' => 'nullable|string|max:255',
            'color_size_allocations.*.*.stock' => 'nullable|integer|min:0',
            'color_size_allocations.*.*.price_adjustment' => 'nullable|numeric',
            'color_size_allocations.*.*.additional_info' => 'nullable|string',
            'color_size_allocations.*.*.display_order' => 'nullable|integer',
            'color_size_allocations.*.*.is_default' => 'nullable|boolean',
            // Specifications validation - made optional
            'specifications' => 'nullable|array',
            'specifications.*.key' => 'nullable|string|max:255',
            'specifications.*.value' => 'nullable|string',
            'specifications.*.display_order' => 'nullable|integer',
            // Multi-branch validation
            'is_multi_branch' => 'nullable|boolean',
            'branches' => 'nullable|array',
            'branches.*.branch_id' => 'required_with:branches|exists:branches,id',
            'branches.*.stock' => 'nullable|integer|min:0',
            'branches.*.price' => 'nullable|numeric|min:0',
            'branches.*.is_available' => 'nullable|boolean',
        ]);

        // Verify that the branch belongs to the vendor's company
        $branch = Branch::findOrFail($request->branch_id);

        // Check if the branch's company belongs to the authenticated user
        $companyBelongsToUser = $branch->company && $branch->company->user_id === Auth::id();

        if (!$companyBelongsToUser) {
            return redirect()->back()->with('error', 'You do not have permission to move products to this branch.');
        }

        $data = $request->except(['specifications', 'colors', 'sizes', 'branches', 'color_images']);
        $data['is_available'] = $request->has('is_available') ? true : false;
        $data['is_multi_branch'] = $request->has('is_multi_branch') ? true : false;

        // Update basic product information
        $product->update($data);

        // Clear existing related data
        $product->specifications()->delete();
        $product->colors()->delete();
        $product->sizes()->delete();
        \App\Models\ProductColorSize::where('product_id', $product->id)->delete();
        $product->productBranches()->delete();

        // Process colors with their images (same logic as store method)
        $defaultColorImage = null;
        $hasDefaultColor = false;

        foreach ($request->colors as $index => $colorData) {
            $isDefault = isset($colorData['is_default']) ? true : false;

            // If this is marked as default or no default has been set yet
            if ($isDefault) {
                $hasDefaultColor = true;
            }

            // Handle image - either new upload or keep existing
            $image = null;
            if ($request->hasFile("color_images.$index")) {
                // New image uploaded
                $file = $request->file("color_images.$index");
                $path = $file->store('product-colors', 'public');
                $image = '/storage/' . $path;

                // Ensure the file is accessible by copying it if needed
                $sourceFile = storage_path('app/public/' . $path);
                $publicFile = public_path('storage/' . $path);

                // Make sure the directory exists
                if (!file_exists(dirname($publicFile))) {
                    mkdir(dirname($publicFile), 0755, true);
                }

                // If the file doesn't exist in the public directory, copy it there
                if (!file_exists($publicFile) && file_exists($sourceFile)) {
                    copy($sourceFile, $publicFile);
                }
            } else {
                // Check if there's an existing image for this color (by name)
                $existingColor = $product->colors()->where('name', $colorData['name'])->first();
                if ($existingColor && $existingColor->image) {
                    $image = $existingColor->image;
                } else {
                    // Return with error if no image is provided for new colors
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Each color must have an associated image.');
                }
            }

            // If this is the default color, save its image to use as the product's main image
            if ($isDefault) {
                $defaultColorImage = $image;
            }

            $color = $product->colors()->create([
                'name' => $colorData['name'],
                'color_code' => $colorData['color_code'] ?? null,
                'image' => $image,
                'price_adjustment' => $colorData['price_adjustment'] ?? 0,
                'stock' => $colorData['stock'] ?? 0,
                'display_order' => $colorData['display_order'] ?? $index,
                'is_default' => $isDefault,
            ]);

            // Process sizes for this color (same logic as store method)
            if (isset($colorData['sizes']) && is_array($colorData['sizes'])) {
                foreach ($colorData['sizes'] as $sizeIndex => $sizeData) {
                    if (!empty($sizeData['name'])) {
                        $size = $product->sizes()->create([
                            'name' => $sizeData['name'],
                            'value' => $sizeData['value'] ?? null,
                            'additional_info' => $sizeData['additional_info'] ?? null,
                            'price_adjustment' => $sizeData['price_adjustment'] ?? 0,
                            'stock' => $sizeData['stock'] ?? 0,
                            'display_order' => $sizeData['display_order'] ?? $sizeIndex,
                            'is_default' => isset($sizeData['is_default']) ? true : false,
                        ]);

                        // Create the color-size combination
                        if ($sizeData['stock'] > 0) {
                            \App\Models\ProductColorSize::create([
                                'product_id' => $product->id,
                                'product_color_id' => $color->id,
                                'product_size_id' => $size->id,
                                'stock' => $sizeData['stock'],
                                'price_adjustment' => $sizeData['price_adjustment'] ?? 0,
                                'is_available' => true,
                            ]);
                        }
                    }
                }
            }
        }

        // Process color-size allocations if provided (alternative data structure)
        if ($request->has('color_size_allocations') && is_array($request->color_size_allocations)) {
            $this->processColorSizeAllocations($request->color_size_allocations, $product);
        }

        // If no color is marked as default, make the first one default
        if (!$hasDefaultColor) {
            $firstColor = $product->colors()->first();
            if ($firstColor) {
                $firstColor->update(['is_default' => true]);
                $defaultColorImage = $firstColor->getRawOriginal('image');
            }
        }

        // Set the product's main image to the default color image
        if ($defaultColorImage) {
            $product->updateMainImageFromColorImage($defaultColorImage);
            $product->refresh();
        }

        // Add specifications if provided (filter out empty ones)
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

        // Add branch associations if multi-branch is enabled
        if ($request->has('is_multi_branch') && $request->is_multi_branch && $request->has('branches')) {
            foreach ($request->branches as $branchData) {
                $product->productBranches()->create([
                    'branch_id' => $branchData['branch_id'],
                    'stock' => $branchData['stock'] ?? 0,
                    'price' => $branchData['price'] ?? $product->price,
                    'is_available' => isset($branchData['is_available']) ? true : false,
                ]);
            }
        }

        return redirect()->route('vendor.products.index')
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try {
            // Check if the product belongs to the vendor's company
            $userBranches = Branch::whereHas('company', function ($query) {
                $query->where('user_id', Auth::id());
            })->pluck('id')->toArray();

            if (!in_array($product->branch_id, $userBranches)) {
                return redirect()->route('vendor.products.index')
                    ->with('error', 'You do not have permission to delete this product.');
            }

            // Delete legacy image if exists (old format)
            if ($product->image && Storage::exists('public/' . str_replace('/storage/', '', $product->image))) {
                Storage::delete('public/' . str_replace('/storage/', '', $product->image));
            }

            // The Product model's deleting event will handle cascading deletion
            // of colors, sizes, color-size combinations, specifications, and images
            $product->delete();

            return redirect()->route('vendor.products.index')
                ->with('success', 'Product and all related data deleted successfully.');

        } catch (\Exception $e) {
            Log::error('Error deleting vendor product: ' . $e->getMessage(), [
                'product_id' => $product->id,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('vendor.products.index')
                ->with('error', 'Failed to delete product: ' . $e->getMessage());
        }
    }

    /**
     * Process color-size allocations from the dynamic form.
     *
     * @param array $colorSizeAllocations
     * @param Product $product
     * @return void
     */
    private function processColorSizeAllocations(array $colorSizeAllocations, Product $product)
    {
        // Get all colors for this product in order
        $colors = $product->colors()->orderBy('display_order')->get();

        foreach ($colorSizeAllocations as $colorIndex => $sizeAllocations) {
            // Get the color for this index
            $color = $colors->get($colorIndex);
            if (!$color) {
                continue; // Skip if color doesn't exist
            }

            foreach ($sizeAllocations as $sizeIndex => $sizeData) {
                // Skip if no size name or stock is 0
                if (empty($sizeData['size_name']) || (isset($sizeData['stock']) && $sizeData['stock'] <= 0)) {
                    continue;
                }

                // Determine size category ID from category name
                $sizeCategoryId = $this->getSizeCategoryId($sizeData);

                // Create the product size
                $size = $product->sizes()->create([
                    'name' => $sizeData['size_name'],
                    'value' => $sizeData['size_value'] ?? $sizeData['size_name'],
                    'additional_info' => $sizeData['additional_info'] ?? null,
                    'price_adjustment' => $sizeData['price_adjustment'] ?? 0,
                    'stock' => $sizeData['stock'] ?? 0,
                    'display_order' => $sizeData['display_order'] ?? $sizeIndex,
                    'is_default' => isset($sizeData['is_default']) ? (bool)$sizeData['is_default'] : false,
                    'size_category_id' => $sizeCategoryId,
                ]);

                // Create the color-size combination if stock > 0
                if (isset($sizeData['stock']) && $sizeData['stock'] > 0) {
                    \App\Models\ProductColorSize::create([
                        'product_id' => $product->id,
                        'product_color_id' => $color->id,
                        'product_size_id' => $size->id,
                        'stock' => $sizeData['stock'],
                        'price_adjustment' => $sizeData['price_adjustment'] ?? 0,
                        'is_available' => true,
                    ]);
                }
            }
        }
    }

    /**
     * Get size category ID from size data.
     *
     * @param array $sizeData
     * @return int|null
     */
    private function getSizeCategoryId(array $sizeData): ?int
    {
        // First, check if we have explicit category information from the form
        if (!empty($sizeData['category'])) {
            return $this->getSizeCategoryIdByName($sizeData['category']);
        }

        // Fallback: try to determine category from size name patterns
        if (empty($sizeData['size_name'])) {
            return null;
        }

        $sizeName = strtolower($sizeData['size_name']);

        // Check for clothing sizes (S, M, L, XL, etc.)
        if (preg_match('/^(xxs|xs|s|m|l|xl|xxl|xxxl)$/i', $sizeName)) {
            return $this->getSizeCategoryIdByName('clothes');
        }

        // Check for shoe sizes (numbers)
        if (preg_match('/^\d+(\.\d+)?$/', $sizeName) || preg_match('/^(eu\s*)?\d+$/i', $sizeName)) {
            return $this->getSizeCategoryIdByName('shoes');
        }

        // Check for hat sizes
        if (preg_match('/^(one\s*size|os|free\s*size|adjustable)$/i', $sizeName)) {
            return $this->getSizeCategoryIdByName('hats');
        }

        // Default to clothes if we can't determine
        return $this->getSizeCategoryIdByName('clothes');
    }

    /**
     * Get size category ID by name.
     *
     * @param string $categoryName
     * @return int|null
     */
    private function getSizeCategoryIdByName(string $categoryName): ?int
    {
        $sizeCategory = \App\Models\SizeCategory::where('name', $categoryName)->first();
        return $sizeCategory ? $sizeCategory->id : null;
    }

    /**
     * Get search suggestions for products.
     */
    public function searchSuggestions(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $suggestions = Product::query()
            ->with(['branch', 'category'])
            ->whereHas('branch', function ($q) {
                $q->whereHas('company', function ($q) {
                    $q->where('user_id', Auth::id());
                });
            })
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhereHas('category', function ($q) use ($query) {
                      $q->where('name', 'like', "%{$query}%");
                  });
            })
            ->limit(10)
            ->get()
            ->map(function ($product) use ($query) {
                return [
                    'id' => $product->id,
                    'text' => $product->name,
                    'type' => 'product',
                    'icon' => 'fas fa-box',
                    'subtitle' => $product->category->name ?? 'No Category',
                    'highlight' => $this->highlightMatch($product->name, $query),
                ];
            });

        return response()->json($suggestions);
    }

    /**
     * Highlight matching text in search results.
     */
    private function highlightMatch($text, $query)
    {
        return preg_replace('/(' . preg_quote($query, '/') . ')/i', '<mark>$1</mark>', $text);
    }
}
