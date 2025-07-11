<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductSize;
use App\Models\ProductColorSize;
use App\Models\ProductSpecification;
use App\Models\Category;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Helpers\ImageHelper;

class ProductController extends Controller
{
    /**
     * Display a listing of the merchant's products.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Product::where('user_id', $user->id)->with('category');

        // Apply search if provided
        if ($request->filled('search')) {
            $searchTerm = $request->get('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('sku', 'like', "%{$searchTerm}%")
                  ->orWhereHas('category', function ($categoryQuery) use ($searchTerm) {
                      $categoryQuery->where('name', 'like', "%{$searchTerm}%");
                  });
            });
        }

        // Apply filters
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->get('category_id'));
        }

        if ($request->filled('status')) {
            $status = $request->get('status');
            if ($status === 'active') {
                $query->where('is_available', true);
            } elseif ($status === 'inactive') {
                $query->where('is_available', false);
            }
        }

        if ($request->filled('featured')) {
            $query->where('featured', $request->boolean('featured'));
        }

        if ($request->filled('stock_status')) {
            $stockStatus = $request->get('stock_status');
            switch ($stockStatus) {
                case 'in_stock':
                    $query->where('stock', '>', 10);
                    break;
                case 'low_stock':
                    $query->whereBetween('stock', [1, 10]);
                    break;
                case 'out_of_stock':
                    $query->where('stock', 0);
                    break;
            }
        }

        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->get('price_min'));
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->get('price_max'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        $allowedSortFields = ['created_at', 'updated_at', 'name', 'price', 'stock'];
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(15)->appends($request->query());

        // If this is an AJAX request, return JSON response
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('merchant.products.partials.products-table', compact('products'))->render(),
                'pagination' => view('merchant.products.partials.pagination', compact('products'))->render(),
                'total' => $products->total(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
            ]);
        }

        return view('merchant.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        // Get categories with parent-child relationships for enhanced form
        $parentCategories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->with(['children' => function($query) {
                $query->where('is_active', true)->orderBy('name');
            }])
            ->orderBy('name')
            ->get();

        // Get user's branches
        $user = Auth::user();
        $branches = $user->branches()->where('status', 'active')->get();

        // If no branches exist, we'll create one in the store method
        if ($branches->isEmpty()) {
            $merchant = $user->merchantRecord;
            $branchName = $merchant ? $merchant->business_name : $user->name . "'s Store";
            $branches = collect([
                (object) [
                    'id' => 'auto',
                    'name' => $branchName . ' (Auto-created)',
                ]
            ]);
        }

        return view('merchant.products.create', compact('parentCategories', 'branches'));
    }

    /**
     * Store a newly created product in storage.
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
            'specifications.*.value' => 'nullable|string|max:255',
            'specifications.*.display_order' => 'nullable|integer',
        ]);

        // Prepare data for product creation
        $data = $request->except(['specifications', 'colors', 'sizes', 'branches', 'color_images']);
        $data['is_available'] = $request->has('is_available') ? true : false;
        $data['user_id'] = Auth::id();

        // Handle branch assignment
        if ($request->branch_id === 'auto') {
            // Get or create a branch for the merchant
            $user = Auth::user();
            $userBranch = $user->branches()->first();

            // If no branch exists, create a default one for the merchant
            if (!$userBranch) {
                $merchant = $user->merchantRecord;
                $branchName = $merchant ? $merchant->business_name : $user->name . "'s Store";

                $userBranch = Branch::create([
                    'user_id' => $user->id,
                    'name' => $branchName,
                    'address' => $merchant->store_location_address ?? 'Default Address',
                    'emirate' => $merchant->emirate ?? 'Dubai',
                    'lat' => $merchant->store_location_lat ?? 25.2048,
                    'lng' => $merchant->store_location_lng ?? 55.2708,
                    'status' => 'active',
                    'phone' => $user->phone,
                    'email' => $user->email,
                ]);
            }
            $data['branch_id'] = $userBranch->id;
        }

        // Create the product
        $product = Product::create($data);

        // Validate that only one color is marked as default
        $defaultCount = 0;
        foreach ($request->colors as $colorData) {
            if (isset($colorData['is_default']) && $colorData['is_default']) {
                $defaultCount++;
            }
        }

        if ($defaultCount > 1) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['colors' => 'Only one color can be marked as default.']);
        }

        // Add colors with their images (required)
        $defaultColorImage = null;
        $hasDefaultColor = false;

        foreach ($request->colors as $index => $colorData) {
            $isDefault = isset($colorData['is_default']) ? true : false;

            // If this is marked as default or no default has been set yet
            if ($isDefault) {
                $hasDefaultColor = true;
            }

            // Handle color image upload
            $colorImagePath = null;
            if ($request->hasFile("color_images.{$index}")) {
                try {
                    $image = $request->file("color_images.{$index}");

                    // Validate image
                    if (!$image->isValid()) {
                        return redirect()->back()
                            ->withInput()
                            ->withErrors(["color_images.{$index}" => 'The uploaded color image is corrupted or invalid.']);
                    }

                    // Generate unique filename
                    $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();

                    // Store the image
                    $colorImagePath = $image->storeAs('products/colors', $imageName, 'public');

                    if (!$colorImagePath) {
                        return redirect()->back()
                            ->withInput()
                            ->withErrors(["color_images.{$index}" => 'Failed to upload color image. Please try again.']);
                    }

                    // Sync to public storage
                    ImageHelper::syncUploadedImage($colorImagePath);

                    // Set as default product image if this is the default color
                    if ($isDefault) {
                        $defaultColorImage = $colorImagePath;
                    }

                } catch (\Exception $e) {
                    \Log::error('Color image upload failed: ' . $e->getMessage());
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(["color_images.{$index}" => 'Failed to upload color image. Please try again.']);
                }
            }

            // Create the color
            $color = ProductColor::create([
                'product_id' => $product->id,
                'name' => $colorData['name'],
                'color_code' => $colorData['color_code'] ?? null,
                'image' => $colorImagePath,
                'price_adjustment' => $colorData['price_adjustment'] ?? 0,
                'stock' => $colorData['stock'] ?? 0,
                'display_order' => $colorData['display_order'] ?? $index,
                'is_default' => $isDefault,
            ]);
        }

        // Set the default color image as the main product image
        if ($defaultColorImage) {
            $product->update(['image' => $defaultColorImage]);
        } elseif (!$hasDefaultColor && $request->colors) {
            // If no default was explicitly set, use the first color's image
            $firstColor = ProductColor::where('product_id', $product->id)->first();
            if ($firstColor && $firstColor->image) {
                $product->update(['image' => $firstColor->image]);
                $firstColor->update(['is_default' => true]);
            }
        }

        // Add specifications if provided
        if ($request->has('specifications') && is_array($request->specifications)) {
            foreach ($request->specifications as $index => $specData) {
                if (!empty($specData['key']) && !empty($specData['value'])) {
                    ProductSpecification::create([
                        'product_id' => $product->id,
                        'key' => $specData['key'],
                        'value' => $specData['value'],
                        'display_order' => $specData['display_order'] ?? $index,
                    ]);
                }
            }
        }

        // Process color-size allocations if provided
        if ($request->has('color_size_allocations') && is_array($request->color_size_allocations)) {
            $this->processColorSizeAllocations($request->color_size_allocations, $product);
        }

        return redirect()->route('merchant.products.index')
            ->with('success', 'Product created successfully with colors and specifications.');
    }

    /**
     * Display the specified product.
     */
    public function show($id)
    {
        $product = Product::where('user_id', Auth::id())
            ->with([
                'category',
                'specifications' => function($query) {
                    $query->orderBy('display_order');
                },
                'colors' => function($query) {
                    $query->orderBy('display_order');
                },
                'sizes' => function($query) {
                    $query->orderBy('display_order');
                },
                'colorSizes.color',
                'colorSizes.size'
            ])
            ->findOrFail($id);

        return view('merchant.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit($id)
    {
        $product = Product::where('user_id', Auth::id())
            ->with([
                'colors' => function($query) {
                    $query->orderBy('display_order');
                },
                'colors.sizes' => function($query) {
                    $query->orderBy('display_order');
                },
                'sizes' => function($query) {
                    $query->orderBy('display_order');
                },
                'specifications' => function($query) {
                    $query->orderBy('display_order');
                },
                'colorSizes.color',
                'colorSizes.size'
            ])
            ->findOrFail($id);

        // Get categories with parent-child relationships for enhanced form
        $parentCategories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->with(['children' => function($query) {
                $query->where('is_active', true)->orderBy('name');
            }])
            ->orderBy('name')
            ->get();

        // Get user's branches
        $user = Auth::user();
        $branches = $user->branches()->where('status', 'active')->get();

        // Process color-size data for JavaScript
        foreach ($product->colors as $color) {
            // Get size allocations for this color from colorSizes relationship
            $colorSizeAllocations = $product->colorSizes->where('product_color_id', $color->id);

            // Create sizes array with allocation data
            $sizesWithAllocations = [];
            foreach ($colorSizeAllocations as $allocation) {
                if ($allocation->size) {
                    $sizesWithAllocations[] = [
                        'id' => $allocation->size->id,
                        'name' => $allocation->size->name,
                        'value' => $allocation->size->value,
                        'stock' => $allocation->stock,
                        'price_adjustment' => $allocation->price_adjustment,
                        'is_available' => $allocation->is_available,
                    ];
                }
            }

            // Add sizes data to color object for JavaScript
            $color->sizes_with_allocations = $sizesWithAllocations;
        }

        return view('merchant.products.edit-vue', compact('product', 'parentCategories', 'branches'));
    }

    /**
     * Get product data for Vue.js component (API endpoint)
     */
    public function getEditData($id)
    {
        $product = Product::where('user_id', Auth::id())
            ->with([
                'colors' => function($query) {
                    $query->orderBy('display_order');
                },
                'colors.sizes' => function($query) {
                    $query->orderBy('display_order');
                },
                'sizes' => function($query) {
                    $query->orderBy('display_order');
                },
                'specifications' => function($query) {
                    $query->orderBy('display_order');
                },
                'colorSizes.color',
                'colorSizes.size'
            ])
            ->findOrFail($id);

        // Get categories with parent-child relationships
        $parentCategories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->with(['children' => function($query) {
                $query->where('is_active', true)->orderBy('name');
            }])
            ->orderBy('name')
            ->get();

        // Get user's branches
        $user = Auth::user();
        $branches = $user->branches()->where('status', 'active')->get();

        // Process color-size data for Vue.js
        foreach ($product->colors as $color) {
            // Get size allocations for this color from colorSizes relationship
            $colorSizeAllocations = $product->colorSizes->where('product_color_id', $color->id);

            // Create sizes array with allocation data
            $sizesWithAllocations = [];
            foreach ($colorSizeAllocations as $allocation) {
                if ($allocation->size) {
                    $sizesWithAllocations[] = [
                        'id' => $allocation->size->id,
                        'name' => $allocation->size->name,
                        'value' => $allocation->size->value,
                        'stock' => $allocation->stock,
                        'price_adjustment' => $allocation->price_adjustment,
                        'is_available' => $allocation->is_available,
                    ];
                }
            }

            // Add sizes data to color object for Vue.js
            $color->sizes_with_allocations = $sizesWithAllocations;
        }

        return response()->json([
            'product' => $product,
            'parentCategories' => $parentCategories,
            'branches' => $branches
        ]);
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, $id)
    {
        $product = Product::where('user_id', Auth::id())->findOrFail($id);

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
            // Color-sizes validation (from edit form)
            'color_sizes' => 'nullable|array',
            'color_sizes.*.*' => 'nullable|array',
            'color_sizes.*.*.size_name' => 'nullable|string|max:255',
            'color_sizes.*.*.size_value' => 'nullable|string',
            'color_sizes.*.*.size_id' => 'nullable|integer',
            'color_sizes.*.*.stock' => 'nullable|integer|min:0',
            'color_sizes.*.*.price_adjustment' => 'nullable|numeric',
            'color_sizes.*.*.is_available' => 'nullable|boolean',
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
            'specifications.*.value' => 'nullable|string|max:255',
            'specifications.*.display_order' => 'nullable|integer',
        ]);

        // Prepare data for product update
        $data = $request->except(['specifications', 'colors', 'sizes', 'branches', 'color_images', 'color_sizes', 'color_size_allocations']);
        $data['is_available'] = $request->has('is_available') ? true : false;

        // Update basic product information
        $product->update($data);

        // Clear existing related data (except sizes - they will be managed intelligently)
        $product->specifications()->delete();
        $product->colors()->delete();
        // Note: We don't delete sizes here anymore - they will be preserved and managed in processColorSizeAllocations
        ProductColorSize::where('product_id', $product->id)->delete();

        // Validate that only one color is marked as default
        $defaultCount = 0;
        foreach ($request->colors as $colorData) {
            if (isset($colorData['is_default']) && $colorData['is_default']) {
                $defaultCount++;
            }
        }

        if ($defaultCount > 1) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['colors' => 'Only one color can be marked as default.']);
        }

        // Process colors with their images (same logic as store method)
        $defaultColorImage = null;
        $hasDefaultColor = false;

        foreach ($request->colors as $index => $colorData) {
            $isDefault = isset($colorData['is_default']) ? true : false;

            // If this is marked as default or no default has been set yet
            if ($isDefault) {
                $hasDefaultColor = true;
            }

            // Handle color image upload
            $colorImagePath = null;
            if ($request->hasFile("color_images.{$index}")) {
                try {
                    $image = $request->file("color_images.{$index}");

                    // Validate image
                    if (!$image->isValid()) {
                        return redirect()->back()
                            ->withInput()
                            ->withErrors(["color_images.{$index}" => 'The uploaded color image is corrupted or invalid.']);
                    }

                    // Generate unique filename
                    $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();

                    // Store the image
                    $colorImagePath = $image->storeAs('products/colors', $imageName, 'public');

                    if (!$colorImagePath) {
                        return redirect()->back()
                            ->withInput()
                            ->withErrors(["color_images.{$index}" => 'Failed to upload color image. Please try again.']);
                    }

                    // Sync to public storage
                    ImageHelper::syncUploadedImage($colorImagePath);

                    // Set as default product image if this is the default color
                    if ($isDefault) {
                        $defaultColorImage = $colorImagePath;
                    }

                } catch (\Exception $e) {
                    \Log::error('Color image upload failed: ' . $e->getMessage());
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(["color_images.{$index}" => 'Failed to upload color image. Please try again.']);
                }
            } else {
                // Use existing image from hidden input if available
                if (isset($colorData['image']) && !empty($colorData['image'])) {
                    $colorImagePath = $colorData['image'];
                    if ($isDefault) {
                        $defaultColorImage = $colorData['image'];
                    }
                }
            }

            // Create the color
            $color = ProductColor::create([
                'product_id' => $product->id,
                'name' => $colorData['name'],
                'color_code' => $colorData['color_code'] ?? null,
                'image' => $colorImagePath,
                'price_adjustment' => $colorData['price_adjustment'] ?? 0,
                'stock' => $colorData['stock'] ?? 0,
                'display_order' => $colorData['display_order'] ?? $index,
                'is_default' => $isDefault,
            ]);
        }

        // Set the default color image as the main product image
        if ($defaultColorImage) {
            $product->update(['image' => $defaultColorImage]);
        } elseif (!$hasDefaultColor && $request->colors) {
            // If no default was explicitly set, use the first color's image
            $firstColor = ProductColor::where('product_id', $product->id)->first();
            if ($firstColor && $firstColor->image) {
                $product->update(['image' => $firstColor->image]);
                $firstColor->update(['is_default' => true]);
            }
        }

        // Add specifications if provided
        if ($request->has('specifications') && is_array($request->specifications)) {
            foreach ($request->specifications as $index => $specData) {
                if (!empty($specData['key']) && !empty($specData['value'])) {
                    ProductSpecification::create([
                        'product_id' => $product->id,
                        'key' => $specData['key'],
                        'value' => $specData['value'],
                        'display_order' => $specData['display_order'] ?? $index,
                    ]);
                }
            }
        }

        // Process color-size allocations if provided (legacy format)
        if ($request->has('color_size_allocations') && is_array($request->color_size_allocations)) {
            $this->processColorSizeAllocations($request->color_size_allocations, $product);
        }

        // Process color_sizes data from the edit form
        if ($request->has('color_sizes') && is_array($request->color_sizes)) {
            $this->processColorSizesData($request->color_sizes, $product);
        }

        return redirect()->route('merchant.products.index')
            ->with('success', 'Product updated successfully with colors and specifications.');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy($id)
    {
        $product = Product::where('user_id', Auth::id())->findOrFail($id);

        // Image cleanup is handled automatically by the Product model's booted method
        $product->delete();

        return redirect()->route('merchant.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    /**
     * Get search suggestions for products.
     */
    public function searchSuggestions(Request $request)
    {
        $user = Auth::user();
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([
                'success' => true,
                'suggestions' => []
            ]);
        }

        $products = Product::where('user_id', $user->id)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%");
            })
            ->select('id', 'name', 'sku', 'image', 'price')
            ->limit(10)
            ->get();

        $categories = \App\Models\Category::where('name', 'like', "%{$query}%")
            ->where('is_active', true)
            ->select('id', 'name')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'suggestions' => [
                'products' => $products->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'sku' => $product->sku,
                        'price' => $product->price,
                        'image' => $product->image,
                        'type' => 'product'
                    ];
                }),
                'categories' => $categories->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'type' => 'category'
                    ];
                })
            ]
        ]);
    }

    /**
     * Get filter options for products.
     */
    public function getFilterOptions(Request $request)
    {
        $user = Auth::user();

        $categories = \App\Models\Category::whereHas('products', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->where('is_active', true)
        ->select('id', 'name')
        ->orderBy('name')
        ->get();

        $priceRange = Product::where('user_id', $user->id)
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        return response()->json([
            'success' => true,
            'options' => [
                'categories' => $categories,
                'price_range' => [
                    'min' => $priceRange->min_price ?? 0,
                    'max' => $priceRange->max_price ?? 1000
                ],
                'stock_statuses' => [
                    ['value' => 'in_stock', 'label' => 'In Stock (>10)'],
                    ['value' => 'low_stock', 'label' => 'Low Stock (1-10)'],
                    ['value' => 'out_of_stock', 'label' => 'Out of Stock (0)']
                ],
                'statuses' => [
                    ['value' => 'active', 'label' => 'Active'],
                    ['value' => 'inactive', 'label' => 'Inactive']
                ]
            ]
        ]);
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

                // Create or find the size
                $size = ProductSize::firstOrCreate([
                    'product_id' => $product->id,
                    'name' => $sizeData['size_name'],
                ], [
                    'value' => $sizeData['size_value'] ?? null,
                    'price_adjustment' => $sizeData['price_adjustment'] ?? 0,
                    'stock' => 0, // Individual size stock is managed through color-size combinations
                    'display_order' => $sizeData['display_order'] ?? $sizeIndex,
                    'is_default' => $sizeData['is_default'] ?? false,
                ]);

                // Create the color-size combination
                if ($sizeData['stock'] > 0) {
                    ProductColorSize::create([
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
     * Process color_sizes data from the edit form.
     *
     * @param array $colorSizesData
     * @param Product $product
     * @return void
     */
    private function processColorSizesData(array $colorSizesData, Product $product)
    {
        // Get all colors for this product in order
        $colors = $product->colors()->orderBy('display_order')->get();

        foreach ($colorSizesData as $colorIndex => $sizeAllocations) {
            // Get the color for this index
            $color = $colors->get($colorIndex);
            if (!$color) {
                continue; // Skip if color doesn't exist
            }

            foreach ($sizeAllocations as $sizeIndex => $sizeData) {
                // Skip if no size name provided
                if (empty($sizeData['size_name'])) {
                    continue;
                }

                // Create or find the size
                $size = ProductSize::firstOrCreate([
                    'product_id' => $product->id,
                    'name' => $sizeData['size_name'],
                ], [
                    'value' => $sizeData['size_value'] ?? null,
                    'price_adjustment' => $sizeData['price_adjustment'] ?? 0,
                    'stock' => 0, // Individual size stock is managed through color-size combinations
                    'display_order' => $sizeData['display_order'] ?? $sizeIndex,
                    'is_default' => isset($sizeData['is_default']) ? true : false,
                ]);

                // Create the color-size combination if stock is provided and > 0
                $stock = isset($sizeData['stock']) ? (int)$sizeData['stock'] : 0;
                if ($stock > 0) {
                    ProductColorSize::create([
                        'product_id' => $product->id,
                        'product_color_id' => $color->id,
                        'product_size_id' => $size->id,
                        'stock' => $stock,
                        'price_adjustment' => $sizeData['price_adjustment'] ?? 0,
                        'is_available' => isset($sizeData['is_available']) ? true : true,
                    ]);
                }
            }
        }
    }
}
