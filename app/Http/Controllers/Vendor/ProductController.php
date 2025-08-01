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

        return view('vendor.products.create-vue', compact('parentCategories', 'branches'));
    }

    /**
     * Get data for Vue.js product creation interface
     */
    public function getCreateData()
    {
        try {
            // Get product categories with their children
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
                return response()->json([
                    'success' => false,
                    'message' => 'You need to create a branch before adding products.',
                    'redirect' => route('vendor.branches.create')
                ]);
            }

            // Check if there are any categories
            if ($parentCategories->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No product categories found. Please contact the administrator.',
                    'redirect' => route('vendor.products.index')
                ]);
            }

            return response()->json([
                'success' => true,
                'categories' => $parentCategories,
                'branches' => $branches
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store session data for multi-tab form
     */
    public function storeSessionData(Request $request)
    {
        try {
            $tabData = $request->input('tabData', []);
            $currentTab = $request->input('currentTab', 'basic');

            // Store the data in session
            session(['vendor_product_create_data' => $tabData]);
            session(['vendor_product_create_current_tab' => $currentTab]);

            return response()->json([
                'success' => true,
                'message' => 'Data saved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get session data for multi-tab form
     */
    public function getSessionData()
    {
        try {
            $tabData = session('vendor_product_create_data', []);
            $currentTab = session('vendor_product_create_current_tab', 'basic');

            return response()->json([
                'success' => true,
                'tabData' => $tabData,
                'currentTab' => $currentTab
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load session data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear session data for multi-tab form
     */
    public function clearSessionData()
    {
        try {
            session()->forget(['vendor_product_create_data', 'vendor_product_create_current_tab']);

            return response()->json([
                'success' => true,
                'message' => 'Session data cleared'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear session data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
            'name' => 'required|string|max:255',
            'product_name_arabic' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'branch_id' => 'required|exists:branches,id',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string|required_with:product_description_arabic',
            'product_description_arabic' => 'nullable|string|required_with:description',
            // Colors validation - now required
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

        // Custom validation: Ensure each color has an image
        $colorImageErrors = [];
        if ($request->has('colors') && is_array($request->colors)) {
            foreach ($request->colors as $index => $colorData) {
                if (!$request->hasFile("color_images.$index")) {
                    $colorImageErrors["color_images.$index"] = "Image is required for color: " . ($colorData['name'] ?? "Color " . ($index + 1));
                }
            }
        }

        if (!empty($colorImageErrors)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $colorImageErrors
                ], 422);
            }
            return redirect()->back()
                ->withInput()
                ->withErrors($colorImageErrors);
        }

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
        $data['user_id'] = Auth::id(); // Assign the authenticated vendor's user ID

        // Set merchant tracking fields (vendor dashboard = not merchant)
        $data['is_merchant'] = false;
        $data['merchant_name'] = null;

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
                        // Determine size category ID from size data
                        $sizeCategoryId = $this->getSizeCategoryId($sizeData);

                        $size = $product->sizes()->create([
                            'name' => $sizeData['name'],
                            'value' => $sizeData['value'] ?? null,
                            'additional_info' => $sizeData['additional_info'] ?? null,
                            'price_adjustment' => $sizeData['price_adjustment'] ?? 0,
                            'stock' => $sizeData['stock'] ?? 0,
                            'display_order' => $sizeData['display_order'] ?? $sizeIndex,
                            'is_default' => isset($sizeData['is_default']) ? true : false,
                            'size_category_id' => $sizeCategoryId,
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

        // Clear session data after successful creation
        session()->forget(['vendor_product_create_data', 'vendor_product_create_current_tab']);

        // Check if this is an AJAX request (from Vue.js)
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Product created successfully.',
                'product' => $product,
                'redirect' => route('vendor.products.index')
            ]);
        }

        return redirect()->route('vendor.products.index')
            ->with('success', 'Product created successfully.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors for AJAX requests
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            // Handle general errors
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create product: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create product: ' . $e->getMessage());
        }
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

        return view('vendor.products.edit-vue', compact('product'));
    }

    /**
     * Get product data for Vue.js edit interface.
     */
    public function getEditData(Product $product)
    {
        // Check if the product belongs to the vendor's company
        $userBranches = Branch::whereHas('company', function ($query) {
            $query->where('user_id', Auth::id());
        })->pluck('id')->toArray();

        if (!in_array($product->branch_id, $userBranches)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to edit this product.'
            ], 403);
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

        // Load product with all related data including colors with their sizes
        $product->load([
            'specifications' => function($query) {
                $query->orderBy('display_order');
            },
            'colors' => function($query) {
                $query->orderBy('display_order');
            },
            'colors.sizes' => function($query) {
                $query->orderBy('display_order');
            }
        ]);

        // Process colors to include image URLs and size data
        $colors = $product->colors->map(function($color) {
            return [
                'id' => $color->id,
                'name' => $color->name,
                'color_code' => $color->color_code,
                'price_adjustment' => $color->price_adjustment,
                'stock' => $color->stock,
                'display_order' => $color->display_order,
                'is_default' => $color->is_default,
                'image' => $color->image, // Use the processed image URL from the accessor
                'sizes' => $color->sizes->map(function($size) {
                    return [
                        'id' => $size->id,
                        'name' => $size->name,
                        'value' => $size->value,
                        'category' => $size->category,
                        'additional_info' => $size->additional_info,
                        'stock' => $size->pivot->stock ?? 0, // Get stock from pivot table
                        'price_adjustment' => $size->price_adjustment,
                        'display_order' => $size->display_order,
                        'is_default' => $size->is_default,
                        'is_available' => $size->is_available
                    ];
                })->toArray()
            ];
        });

        // Process specifications
        $specifications = $product->specifications->map(function($spec) {
            return [
                'id' => $spec->id,
                'key' => $spec->key,
                'value' => $spec->value,
                'display_order' => $spec->display_order
            ];
        });

        return response()->json([
            'success' => true,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'product_name_arabic' => $product->product_name_arabic,
                'category_id' => $product->category_id,
                'branch_id' => $product->branch_id,
                'price' => $product->price,
                'original_price' => $product->original_price,
                'stock' => $product->stock,
                'description' => $product->description,
                'product_description_arabic' => $product->product_description_arabic,
                'is_available' => $product->is_available,
                'display_order' => $product->display_order,
                'colors' => $colors,
                'specifications' => $specifications
            ],
            'parentCategories' => $parentCategories,
            'branches' => $branches
        ]);
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

        // Enhanced validation - colors are now optional for updates
        $request->validate([
            'name' => 'required|string|max:255',
            'product_name_arabic' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'branch_id' => 'required|exists:branches,id',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string|required_with:product_description_arabic',
            'product_description_arabic' => 'nullable|string|required_with:description',
            // Colors validation - now optional for updates
            'colors' => 'nullable|array',
            'colors.*.name' => 'required_with:colors|string|max:255',
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

        // Clear existing related data (except colors - they will be managed intelligently)
        $product->specifications()->delete();
        $product->productBranches()->delete();

        // Check if colors data has actually changed to avoid unnecessary deletion/recreation
        $existingColors = $product->colors()->orderBy('display_order')->get();
        $colorsChanged = $this->haveColorsChanged($existingColors, $request->colors ?? [], $request);

        // Log color change detection for debugging
        \Log::info('Product color update check', [
            'product_id' => $product->id,
            'colors_changed' => $colorsChanged,
            'existing_colors_count' => $existingColors->count(),
            'request_colors_count' => count($request->colors ?? [])
        ]);

        // Only process colors if they have actually changed
        if ($colorsChanged && $request->has('colors') && is_array($request->colors) && count($request->colors) > 0) {
            Log::info('Starting intelligent color update process', [
                'product_id' => $product->id,
                'colors_count' => count($request->colors),
                'existing_colors_count' => $existingColors->count()
            ]);

            // Process colors intelligently - update existing, create new, delete removed
            $this->processColorsIntelligently($request, $product);
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
                    // Use updateOrCreate to prevent data corruption
                    \App\Models\ProductColorSize::updateOrCreate(
                        [
                            'product_id' => $product->id,
                            'product_color_id' => $color->id,
                            'product_size_id' => $size->id,
                        ],
                        [
                            'stock' => $sizeData['stock'],
                            'price_adjustment' => $sizeData['price_adjustment'] ?? 0,
                            'is_available' => true,
                            'updated_at' => now(),
                        ]
                    );
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
     * Process colors intelligently - update existing, create new, delete removed.
     *
     * @param Request $request
     * @param Product $product
     * @return void
     */
    private function processColorsIntelligently($request, Product $product)
    {
        // Get existing colors indexed by ID
        $existingColors = $product->colors()->get()->keyBy('id');
        $submittedColorIds = [];
        $defaultColorImage = null;

        foreach ($request->colors as $index => $colorData) {
            $colorId = isset($colorData['id']) ? (int)$colorData['id'] : null;
            $isDefault = isset($colorData['is_default']) && $colorData['is_default'];

            // Handle color image upload or preserve existing
            $colorImagePath = null;
            if ($request->hasFile("color_images.{$index}")) {
                // New image uploaded
                try {
                    $image = $request->file("color_images.{$index}");

                    // Validate image
                    if (!$image->isValid()) {
                        throw new \Exception('The uploaded color image is corrupted or invalid.');
                    }

                    // Store the image using the same pattern as the store method
                    $path = $image->store('product-colors', 'public');
                    $colorImagePath = '/storage/' . $path;

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

                    Log::info('New color image uploaded during update', [
                        'color_name' => $colorData['name'],
                        'image_path' => $colorImagePath,
                        'index' => $index
                    ]);

                } catch (\Exception $e) {
                    Log::error('Color image upload failed: ' . $e->getMessage());
                    // Continue with existing image if upload fails
                    if ($colorId && isset($existingColors[$colorId])) {
                        $colorImagePath = $existingColors[$colorId]->image;
                    }
                }
            } else {
                // Use existing image from form data or preserve current image
                if (isset($colorData['image']) && !empty($colorData['image'])) {
                    $colorImagePath = $colorData['image'];
                } elseif ($colorId && isset($existingColors[$colorId])) {
                    $colorImagePath = $existingColors[$colorId]->image;
                }
            }

            // If this is the default color, save its image to use as the product's main image
            if ($isDefault) {
                $defaultColorImage = $colorImagePath;
            }

            if ($colorId && isset($existingColors[$colorId])) {
                // Update existing color
                $existingColors[$colorId]->update([
                    'name' => $colorData['name'],
                    'color_code' => $colorData['color_code'] ?? null,
                    'image' => $colorImagePath,
                    'price_adjustment' => $colorData['price_adjustment'] ?? 0,
                    'stock' => $colorData['stock'] ?? 0,
                    'display_order' => $colorData['display_order'] ?? $index,
                    'is_default' => $isDefault,
                ]);
                $submittedColorIds[] = $colorId;

                Log::info('Updated existing color', [
                    'color_id' => $colorId,
                    'color_name' => $colorData['name'],
                    'image_path' => $colorImagePath
                ]);
            } else {
                // Create new color
                $newColor = \App\Models\ProductColor::create([
                    'product_id' => $product->id,
                    'name' => $colorData['name'],
                    'color_code' => $colorData['color_code'] ?? null,
                    'image' => $colorImagePath,
                    'price_adjustment' => $colorData['price_adjustment'] ?? 0,
                    'stock' => $colorData['stock'] ?? 0,
                    'display_order' => $colorData['display_order'] ?? $index,
                    'is_default' => $isDefault,
                ]);
                $submittedColorIds[] = $newColor->id;

                Log::info('Created new color', [
                    'color_id' => $newColor->id,
                    'color_name' => $colorData['name'],
                    'image_path' => $colorImagePath
                ]);
            }
        }

        // Delete colors that were not submitted (removed from the form)
        $colorsToDelete = $existingColors->whereNotIn('id', $submittedColorIds);
        foreach ($colorsToDelete as $colorToDelete) {
            // Clean up image file if it exists
            if ($colorToDelete->image) {
                $imagePath = str_replace('/storage/', '', $colorToDelete->image);
                \Storage::disk('public')->delete($imagePath);
            }
            $colorToDelete->delete();
            Log::info('Deleted removed color', ['color_id' => $colorToDelete->id, 'color_name' => $colorToDelete->name]);
        }

        // Set the product's main image to the default color image
        if ($defaultColorImage) {
            $product->updateMainImageFromColorImage($defaultColorImage);
            $product->refresh();
        }
    }

    /**
     * Check if colors data has changed compared to existing colors.
     */
    private function haveColorsChanged($existingColors, array $requestColors, $request = null): bool
    {
        // If the number of colors is different, they've changed
        if ($existingColors->count() !== count($requestColors)) {
            \Log::info('Colors changed: count mismatch', [
                'existing_count' => $existingColors->count(),
                'request_count' => count($requestColors)
            ]);
            return true;
        }

        // Check each color for changes
        foreach ($requestColors as $index => $requestColor) {
            $existingColor = $existingColors->get($index);

            if (!$existingColor) {
                \Log::info('Colors changed: new color added at index', ['index' => $index]);
                return true; // New color added
            }

            // Check if new image file was uploaded for this color
            if ($request && $request->hasFile("color_images.$index")) {
                \Log::info('Colors changed: new image file uploaded', ['index' => $index]);
                return true;
            }

            // Normalize is_default values for comparison (handle both boolean and integer formats)
            $existingIsDefault = (bool) $existingColor->is_default;
            $requestIsDefault = isset($requestColor['is_default']) ?
                (is_bool($requestColor['is_default']) ? $requestColor['is_default'] : (bool) $requestColor['is_default']) :
                false;

            // Normalize numeric values for proper comparison (cast to same types)
            $existingPriceAdjustment = (float) $existingColor->price_adjustment;
            $requestPriceAdjustment = (float) ($requestColor['price_adjustment'] ?? 0);

            $existingStock = (int) $existingColor->stock;
            $requestStock = (int) ($requestColor['stock'] ?? 0);

            // Normalize string values
            $existingName = trim($existingColor->name ?? '');
            $requestName = trim($requestColor['name'] ?? '');

            $existingColorCode = trim($existingColor->color_code ?? '');
            $requestColorCode = trim($requestColor['color_code'] ?? '');

            // Check if basic color properties have changed with proper type comparisons
            if ($existingName !== $requestName ||
                $existingColorCode !== $requestColorCode ||
                $existingPriceAdjustment !== $requestPriceAdjustment ||
                $existingStock !== $requestStock ||
                $existingIsDefault !== $requestIsDefault) {

                \Log::info('Colors changed: property mismatch', [
                    'index' => $index,
                    'existing_name' => $existingName,
                    'request_name' => $requestName,
                    'existing_color_code' => $existingColorCode,
                    'request_color_code' => $requestColorCode,
                    'existing_price_adjustment' => $existingPriceAdjustment,
                    'request_price_adjustment' => $requestPriceAdjustment,
                    'existing_stock' => $existingStock,
                    'request_stock' => $requestStock,
                    'existing_is_default' => $existingIsDefault,
                    'request_is_default' => $requestIsDefault,
                ]);
                return true;
            }

            // Compare image paths (handle both relative and full URL formats)
            if (isset($requestColor['image'])) {
                $existingImagePath = $existingColor->getRawImagePath();
                $requestImagePath = $requestColor['image'];

                // Normalize paths for comparison (remove domain if present and handle null values)
                $normalizedExisting = $existingImagePath ? str_replace(url(''), '', $existingImagePath) : '';
                $normalizedRequest = $requestImagePath ? str_replace(url(''), '', $requestImagePath) : '';

                if ($normalizedExisting !== $normalizedRequest) {
                    \Log::info('Colors changed: image path mismatch', [
                        'index' => $index,
                        'existing_image' => $normalizedExisting,
                        'request_image' => $normalizedRequest
                    ]);
                    return true;
                }
            }
        }

        \Log::info('Colors unchanged: no differences detected');
        return false; // No changes detected
    }

    /**
     * API endpoint to get the latest product ID for testing purposes.
     */
    public function getLatestProductId()
    {
        $latestProduct = Product::whereHas('branch', function ($query) {
                $query->whereHas('company', function ($query) {
                    $query->where('user_id', Auth::id());
                });
            })
            ->orderBy('created_at', 'desc')
            ->first();

        return response()->json([
            'product_id' => $latestProduct ? $latestProduct->id : null
        ]);
    }

    /**
     * API endpoint to verify product sizes for testing purposes.
     */
    public function verifySizes($id)
    {
        $product = Product::whereHas('branch', function ($query) {
                $query->whereHas('company', function ($query) {
                    $query->where('user_id', Auth::id());
                });
            })
            ->with(['sizes.sizeCategory', 'colors.sizes.sizeCategory'])
            ->findOrFail($id);

        $sizesData = [];

        // Get direct product sizes
        foreach ($product->sizes as $size) {
            $sizesData[] = [
                'id' => $size->id,
                'name' => $size->name,
                'size_category_id' => $size->size_category_id,
                'category_name' => $size->sizeCategory ? $size->sizeCategory->name : null,
                'source' => 'direct'
            ];
        }

        // Get sizes from colors
        foreach ($product->colors as $color) {
            foreach ($color->sizes as $size) {
                $sizesData[] = [
                    'id' => $size->id,
                    'name' => $size->name,
                    'size_category_id' => $size->size_category_id,
                    'category_name' => $size->sizeCategory ? $size->sizeCategory->name : null,
                    'source' => 'color',
                    'color_name' => $color->name
                ];
            }
        }

        return response()->json([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'sizes' => $sizesData
        ]);
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
