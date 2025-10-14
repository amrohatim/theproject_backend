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
use App\Models\SizeCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Helpers\ImageHelper;
use App\Services\WebPImageService;

class ProductController extends Controller
{
    /**
     * Display a listing of the merchant's products.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Start with optimized base query using indexed columns
        $query = Product::select('products.*')
            ->where('products.merchant_id', $user->merchantRecord->id)
            ->with(['category:id,name']); // Only load necessary category fields

        // Apply search if provided - optimized for indexed columns
        if ($request->filled('search')) {
            $searchTerm = $request->get('search');
            $query->where(function ($q) use ($searchTerm) {
                // Search by name first (indexed)
                $q->where('products.name', 'like', "%{$searchTerm}%");

                // Search by SKU (indexed) - exact match first for better performance
                if (Schema::hasColumn('products', 'sku')) {
                    $q->orWhere('products.sku', '=', $searchTerm)
                      ->orWhere('products.sku', 'like', "%{$searchTerm}%");
                }

                // Search by description (less indexed, so lower priority)
                $q->orWhere('products.description', 'like', "%{$searchTerm}%");

                // Search by category name using join for better performance
                $q->orWhereExists(function ($categoryQuery) use ($searchTerm) {
                    $categoryQuery->select(DB::raw(1))
                        ->from('categories')
                        ->whereColumn('categories.id', 'products.category_id')
                        ->where('categories.name', 'like', "%{$searchTerm}%");
                });
            });
        }

        // Apply category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->get('category'));
        }

        // Apply status filter
        if ($request->filled('status')) {
            $status = $request->get('status');
            if ($status === 'active') {
                $query->where('is_available', true);
            } elseif ($status === 'inactive') {
                $query->where('is_available', false);
            }
            // 'all' doesn't add any filter
        }

        // Apply price range filter
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->get('price_min'));
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->get('price_max'));
        }

        // Apply stock level filter
        if ($request->filled('stock_level')) {
            $stockLevel = $request->get('stock_level');
            switch ($stockLevel) {
                case 'in_stock':
                    $query->where('stock', '>', 0);
                    break;
                case 'low_stock':
                    $query->where('stock', '>', 0)->where('stock', '<', 10);
                    break;
                case 'out_of_stock':
                    $query->where('stock', '<=', 0);
                    break;
            }
        }

        // Apply date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        // Apply sorting - optimized for indexed columns
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        // Validate sort direction
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }

        switch ($sortBy) {
            case 'name':
                // Use indexed name column
                $query->orderBy('products.name', $sortDirection);
                break;
            case 'category':
                // Use optimized join with indexed columns
                $query->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                      ->orderBy('categories.name', $sortDirection)
                      ->select('products.*');
                break;
            case 'price':
                // Use indexed price column
                $query->orderBy('products.price', $sortDirection);
                break;
            case 'stock':
                // Use indexed stock column
                $query->orderBy('products.stock', $sortDirection);
                break;
            case 'status':
                // Use indexed is_available column
                $query->orderBy('products.is_available', $sortDirection);
                break;
            case 'created_at':
            default:
                // Use indexed created_at column (default sort)
                $query->orderBy('products.created_at', $sortDirection);
                break;
        }

        // Add secondary sort by ID for consistent pagination
        $query->orderBy('products.id', 'desc');

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

        // Use optimized pagination with cursor-based pagination for large datasets
        $perPage = min($request->get('per_page', 15), 50); // Limit max items per page
        $products = $query->paginate($perPage)->appends($request->query());



        // Get filter options for the view - optimized query using indexes
        $categories = Category::select('id', 'name')
            ->where('is_active', true)
            ->whereNotNull('parent_id') // Only subcategories can be selected
            ->orderBy('name')
            ->get();

        // If this is an AJAX request, return JSON response
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('merchant.products.partials.products-table', compact('products'))->render(),
                'pagination' => view('merchant.products.partials.pagination', compact('products'))->render(),
                'total' => $products->total(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'filters' => [
                    'search' => $request->get('search', ''),
                    'category' => $request->get('category', ''),
                    'status' => $request->get('status', ''),
                    'price_min' => $request->get('price_min', ''),
                    'price_max' => $request->get('price_max', ''),
                    'stock_level' => $request->get('stock_level', ''),
                    'date_from' => $request->get('date_from', ''),
                    'date_to' => $request->get('date_to', ''),
                    'sort' => $request->get('sort', 'created_at'),
                    'direction' => $request->get('direction', 'desc'),
                ]
            ]);
        }

        return view('merchant.products.index', compact('products', 'categories'));
    }

    /**
     * Handle bulk actions on products
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'exists:products,id'
        ]);

        $user = Auth::user();
        $action = $request->get('action');
        $productIds = $request->get('product_ids');

        // Ensure user can only perform actions on their own products
        $products = Product::where('user_id', $user->id)
            ->whereIn('id', $productIds)
            ->get();

        if ($products->count() !== count($productIds)) {
            return response()->json([
                'success' => false,
                'message' => __('merchant.unauthorized_products_selected')
            ], 403);
        }

        try {
            switch ($action) {
                case 'activate':
                    Product::whereIn('id', $productIds)
                        ->where('user_id', $user->id)
                        ->update(['is_available' => true]);
                    $message = __('merchant.products_activated_successfully');
                    break;

                case 'deactivate':
                    Product::whereIn('id', $productIds)
                        ->where('user_id', $user->id)
                        ->update(['is_available' => false]);
                    $message = __('merchant.products_deactivated_successfully');
                    break;

                case 'delete':
                    Product::whereIn('id', $productIds)
                        ->where('user_id', $user->id)
                        ->delete();
                    $message = __('merchant.products_deleted_successfully');
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'affected_count' => $products->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('merchant.bulk_action_error')
            ], 500);
        }
    }



    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        return view('merchant.products.create-vue');
    }

    /**
     * Get initial data for Vue.js product creation component (API endpoint)
     */
    public function getCreateData()
    {
        // Get categories with parent-child relationships for enhanced form
        $parentCategories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->with(['children' => function($query) {
                $query->where('is_active', true)->orderBy('name');
            }])
            ->orderBy('name')
            ->get();

        // Add hierarchy information to categories
        $parentCategories->each(function ($parent) {
            $parent->is_selectable = false; // Parent categories are not selectable
            $parent->children->each(function ($child) {
                $child->is_selectable = $child->canBeSelectedForProducts();
            });
        });

        // Get user's branches
        $user = Auth::user();
        $branches = $user->branches()->where('status', 'active')->get();

        return response()->json([
            'categories' => $parentCategories,
            'branches' => $branches
        ]);
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        error_log('=== PRODUCT STORE METHOD CALLED ===');
        \Log::info('Product creation request received', [
            'user_id' => auth()->id(),
            'request_data' => $request->all()
        ]);

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'product_name_arabic' => 'required|string|max:255',
                'category_id' => ['required', 'exists:categories,id', new \App\Rules\LeafCategoryRule()],
                'branch_id' => 'nullable|string', // Allow 'auto' or existing branch ID
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
                'color_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
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

            \Log::info('Validation passed, starting product creation');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Unexpected error during validation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }

        // Use database transaction to ensure data consistency
        try {
            return \DB::transaction(function () use ($request) {
            \Log::info('Starting database transaction for product creation');

            // Prepare data for product creation
            $data = $request->except(['specifications', 'colors', 'sizes', 'branches', 'color_images']);

            // Handle is_available checkbox properly - convert boolean to integer (1 or 0)
            $isAvailable = $request->input('is_available');
            $data['is_available'] = ($isAvailable === 'true' || $isAvailable === true || $isAvailable === '1' || $isAvailable === 1) ? 1 : 0;

            $data['user_id'] = Auth::id(); // Keep user_id for tracking who created the product

            // Set merchant tracking fields
            $data['is_merchant'] = true;
            $data['merchant_name'] = Auth::user()->name;

            // Set merchant_id for direct merchant ownership
            $data['merchant_id'] = Auth::user()->merchantRecord->id;
            // Remove branch_id as we're using direct merchant ownership
            unset($data['branch_id']);

            // Create the product
            $product = Product::create($data);
            \Log::info('Product created successfully', ['product_id' => $product->id]);

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
            if (isset($colorData['image']) && $colorData['image'] instanceof \Illuminate\Http\UploadedFile) {
                try {
                    $image = $colorData['image'];

                    // Validate image
                    if (!$image->isValid()) {
                        return redirect()->back()
                            ->withInput()
                            ->withErrors(["colors.{$index}.image" => 'The uploaded color image is corrupted or invalid.']);
                    }

                    // Convert to WebP and store
                    $webpService = new WebPImageService();
                    $colorImagePath = $webpService->convertAndStoreWithUrl($image, 'products/colors');

                    if (!$colorImagePath) {
                        // Fallback to original upload method if WebP conversion fails
                        $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                        $fallbackPath = $image->storeAs('products/colors', $imageName, 'public');
                        $colorImagePath = $fallbackPath ? '/storage/' . $fallbackPath : null;

                        \Log::warning('WebP conversion failed for product color, using fallback method', [
                            'color_index' => $index,
                            'original_name' => $image->getClientOriginalName(),
                            'fallback_path' => $colorImagePath
                        ]);
                    }

                    if (!$colorImagePath) {
                        return redirect()->back()
                            ->withInput()
                            ->withErrors(["colors.{$index}.image" => 'Failed to upload color image. Please try again.']);
                    }

                    \Log::info('Product color image uploaded successfully', [
                        'color_index' => $index,
                        'image_path' => $colorImagePath,
                        'is_default' => $isDefault,
                        'original_name' => $image->getClientOriginalName()
                    ]);

                    // Set as default product image if this is the default color
                    if ($isDefault) {
                        $defaultColorImage = $colorImagePath;
                    }

                } catch (\Exception $e) {
                    \Log::error('Color image upload failed: ' . $e->getMessage());
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(["colors.{$index}.image" => 'Failed to upload color image. Please try again.']);
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

            // Process sizes for this color if provided
            \Log::info('Checking for sizes in color data', [
                'has_sizes' => isset($colorData['sizes']),
                'is_array' => isset($colorData['sizes']) ? is_array($colorData['sizes']) : false,
                'is_empty' => isset($colorData['sizes']) ? empty($colorData['sizes']) : true,
                'sizes_data' => $colorData['sizes'] ?? null
            ]);

            if (isset($colorData['sizes']) && is_array($colorData['sizes']) && !empty($colorData['sizes'])) {
                \Log::info('Processing sizes for color', ['color_id' => $color->id, 'sizes_count' => count($colorData['sizes'])]);
                $this->processColorSizes($colorData['sizes'], $product, $color);
            } else {
                \Log::info('No sizes to process for color', ['color_id' => $color->id]);
            }
        }

        // The ProductColor model event handlers will automatically set the product's main image
        // when a default color is created. If no default was set, make the first color default.
        if (!$hasDefaultColor && $request->colors) {
            $firstColor = ProductColor::where('product_id', $product->id)->first();
            if ($firstColor) {
                \Log::info('No default color set, making first color default', [
                    'product_id' => $product->id,
                    'color_id' => $firstColor->id,
                    'color_name' => $firstColor->name
                ]);
                // This will trigger the ProductColor event handler to update the product image
                $firstColor->update(['is_default' => true]);
            }
        }

        // Refresh the product to get the updated image from the event handlers
        $product->refresh();
        \Log::info('Product image after color processing', [
            'product_id' => $product->id,
            'image_path' => $product->getRawOriginal('image')
        ]);

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

            \Log::info('Product creation completed successfully', ['product_id' => $product->id]);

                return redirect()->route('merchant.products.index')
                    ->with('success', 'Product created successfully with colors and specifications.');
            });
        } catch (\Exception $e) {
            \Log::error('Product creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create product: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified product.
     */
    public function show($id)
    {
        $product = Product::where('merchant_id', Auth::user()->merchantRecord->id)
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
        $product = Product::where('merchant_id', Auth::user()->merchantRecord->id)
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
        $product = Product::where('merchant_id', Auth::user()->merchantRecord->id)
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

        // Add hierarchy information to categories
        $parentCategories->each(function ($parent) {
            $parent->is_selectable = false; // Parent categories are not selectable
            $parent->children->each(function ($child) {
                $child->is_selectable = $child->canBeSelectedForProducts();
            });
        });

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


        $product = Product::where('merchant_id', Auth::user()->merchantRecord->id)->findOrFail($id);

        // Enhanced validation to match create method
        $request->validate([
            'name' => 'required|string|max:255',
            'product_name_arabic' => 'required|string|max:255',
            'category_id' => ['required', 'exists:categories,id', new \App\Rules\LeafCategoryRule()],
            'branch_id' => 'nullable|exists:branches,id',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string|required_with:product_description_arabic',
            'product_description_arabic' => 'nullable|string|required_with:description',
            // Colors validation - now required for updates too
            'colors' => 'required|array|min:1',
            'colors.*.name' => 'required|string|max:255',
            'colors.*.color_code' => 'nullable|string|max:10',
            'colors.*.price_adjustment' => 'nullable|numeric',
            'colors.*.stock' => 'nullable|integer|min:0',
            'colors.*.display_order' => 'nullable|integer',
            'colors.*.is_default' => 'nullable|boolean',
            'color_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
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

        // Handle is_available checkbox properly - convert boolean to integer (1 or 0)
        $isAvailable = $request->input('is_available');
        $data['is_available'] = ($isAvailable === 'true' || $isAvailable === true || $isAvailable === '1' || $isAvailable === 1) ? 1 : 0;

        // Update basic product information
        $product->update($data);
        // Clear existing related data (except colors - they will be managed intelligently)
        // Note: We don't delete colors here anymore - they will be preserved and managed intelligently
        // Note: We don't delete sizes here anymore - they will be preserved and managed in processColorSizeAllocations
        // Note: We don't delete ProductColorSize records here anymore - they will be managed safely with updateOrCreate

        // Process specifications in a transaction to ensure data consistency
        DB::transaction(function () use ($product, $request) {
            $product->specifications()->delete();

            // Add specifications if provided
            if ($request->has('specifications') && is_array($request->specifications)) {
                foreach ($request->specifications as $index => $specData) {
                    // Check if specData is an array and has the required keys
                    if (is_array($specData) && !empty($specData['key']) && !empty($specData['value'])) {
                        ProductSpecification::create([
                            'product_id' => $product->id,
                            'key' => trim($specData['key']),
                            'value' => trim($specData['value']),
                            'display_order' => $specData['display_order'] ?? $index,
                        ]);
                    }
                }
            }
        });

        // Validate and fix default color selection
        $defaultCount = 0;
        $defaultIndex = -1;
        foreach ($request->colors as $index => $colorData) {
            if (isset($colorData['is_default']) && $colorData['is_default']) {
                $defaultCount++;
                if ($defaultIndex === -1) {
                    $defaultIndex = $index; // Remember the first default
                }
            }
        }

        // Handle multiple defaults - keep only the first one
        if ($defaultCount > 1) {
            $colors = $request->colors;
            foreach ($colors as $index => &$colorData) {
                $colorData['is_default'] = ($index === $defaultIndex);
            }
            $request->merge(['colors' => $colors]);
        }

        // Handle no defaults - set first color as default
        if ($defaultCount === 0 && count($request->colors) > 0) {
            $colors = $request->colors;
            $colors[0]['is_default'] = true;
            $request->merge(['colors' => $colors]);
        }

        // Process colors intelligently - update existing, create new, delete removed
        $this->processColorsIntelligently($request, $product);

        // Get the default color image for the product
        $defaultColor = $product->colors()->where('is_default', true)->first();
        $defaultColorImage = $defaultColor ? $defaultColor->image : null;

        // Set the default color image as the main product image
        if ($defaultColorImage) {
            $product->update(['image' => $defaultColorImage]);
        } else {
            // If no default color has an image, use the first color's image
            $firstColor = $product->colors()->orderBy('display_order')->first();
            if ($firstColor && $firstColor->image) {
                $product->update(['image' => $firstColor->image]);
                // Set first color as default if no default is set
                if (!$product->colors()->where('is_default', true)->exists()) {
                    $firstColor->update(['is_default' => true]);
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
        $product = Product::where('merchant_id', Auth::user()->merchantRecord->id)->findOrFail($id);

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

                // Determine size category ID from size data
                $sizeCategoryId = $this->getSizeCategoryId($sizeData);

                // Create or find the size
                $size = ProductSize::firstOrCreate([
                    'product_id' => $product->id,
                    'name' => $sizeData['size_name'],
                ], [
                    'value' => $sizeData['size_value'] ?? null,
                    'size_category_id' => $sizeCategoryId,
                    'price_adjustment' => $sizeData['price_adjustment'] ?? 0,
                    'stock' => 0, // Individual size stock is managed through color-size combinations
                    'display_order' => $sizeData['display_order'] ?? $sizeIndex,
                    'is_default' => $sizeData['is_default'] ?? false,
                ]);

                // Create or update the color-size combination safely
                if ($sizeData['stock'] > 0) {
                    ProductColorSize::updateOrCreate(
                        [
                            'product_id' => $product->id,
                            'product_color_id' => $color->id,
                            'product_size_id' => $size->id,
                        ],
                        [
                            'stock' => $sizeData['stock'],
                            'price_adjustment' => $sizeData['price_adjustment'] ?? 0,
                            'is_available' => true,
                        ]
                    );
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

                // Determine size category ID from size data
                $sizeCategoryId = $this->getSizeCategoryId($sizeData);

                // Create or find the size
                $size = ProductSize::firstOrCreate([
                    'product_id' => $product->id,
                    'name' => $sizeData['size_name'],
                ], [
                    'value' => $sizeData['size_value'] ?? null,
                    'size_category_id' => $sizeCategoryId,
                    'price_adjustment' => $sizeData['price_adjustment'] ?? 0,
                    'stock' => 0, // Individual size stock is managed through color-size combinations
                    'display_order' => $sizeData['display_order'] ?? $sizeIndex,
                    'is_default' => isset($sizeData['is_default']) ? true : false,
                ]);

                // Create or update the color-size combination safely if stock is provided and > 0
                $stock = isset($sizeData['stock']) ? (int)$sizeData['stock'] : 0;
                if ($stock > 0) {
                    ProductColorSize::updateOrCreate(
                        [
                            'product_id' => $product->id,
                            'product_color_id' => $color->id,
                            'product_size_id' => $size->id,
                        ],
                        [
                            'stock' => $stock,
                            'price_adjustment' => $sizeData['price_adjustment'] ?? 0,
                            'is_available' => isset($sizeData['is_available']) ? true : true,
                        ]
                    );
                }
            }
        }
    }

    /**
     * Process colors intelligently - update existing, create new, delete removed.
     *
     * @param Request $request
     * @param Product $product
     * @return void
     */
    private function processColorsIntelligently(Request $request, Product $product)
    {
        // Get existing colors indexed by ID
        $existingColors = $product->colors()->get()->keyBy('id');
        $submittedColorIds = [];

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

                    // Delete old image if exists and new conversion is successful
                    if ($colorId && isset($existingColors[$colorId]) && $existingColors[$colorId]->image) {
                        $webpService = new WebPImageService();
                        $webpService->deleteImage($existingColors[$colorId]->image);
                    }

                    // Convert to WebP and store
                    $webpService = new WebPImageService();
                    $colorImagePath = $webpService->convertAndStoreWithUrl($image, 'products/colors');

                    if (!$colorImagePath) {
                        // Fallback to original upload method if WebP conversion fails
                        $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                        $fallbackPath = $image->storeAs('products/colors', $imageName, 'public');
                        $colorImagePath = $fallbackPath ? '/storage/' . $fallbackPath : null;

                        \Log::warning('WebP conversion failed for product color update, using fallback method', [
                            'color_index' => $index,
                            'original_name' => $image->getClientOriginalName(),
                            'fallback_path' => $colorImagePath
                        ]);
                    }

                    if (!$colorImagePath) {
                        throw new \Exception('Failed to upload color image.');
                    }

                    \Log::info('Product color image updated successfully', [
                        'color_index' => $index,
                        'image_path' => $colorImagePath,
                        'original_name' => $image->getClientOriginalName()
                    ]);

                } catch (\Exception $e) {
                    \Log::error('Color image upload failed: ' . $e->getMessage());
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
            } else {
                // Create new color
                $newColor = ProductColor::create([
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
            }
        }

        // Delete colors that were not submitted (removed from the form)
        $colorsToDelete = $existingColors->whereNotIn('id', $submittedColorIds);
        foreach ($colorsToDelete as $colorToDelete) {
            // Clean up image file if it exists
            if ($colorToDelete->image) {
                Storage::disk('public')->delete($colorToDelete->image);
            }
            $colorToDelete->delete();
        }
    }

    /**
     * Process sizes data for a specific color during product creation.
     *
     * @param array $sizesData
     * @param Product $product
     * @param ProductColor $color
     * @return void
     */
    private function processColorSizes(array $sizesData, Product $product, ProductColor $color)
    {
        \Log::info('processColorSizes called', [
            'sizesData' => $sizesData,
            'product_id' => $product->id,
            'color_id' => $color->id
        ]);

        foreach ($sizesData as $sizeData) {
            \Log::info('Processing size data', ['sizeData' => $sizeData]);

            // Skip if essential data is missing
            if (empty($sizeData['name'])) {
                \Log::warning('Skipping size - missing name', ['sizeData' => $sizeData]);
                continue;
            }

            try {
                // Determine size category (default to 'clothes' if not provided)
                $sizeCategory = $sizeData['category'] ?? 'clothes';
                \Log::info('Size category determined', ['category' => $sizeCategory]);

                // Find or create the size category
                $sizeCategoryModel = SizeCategory::firstOrCreate([
                    'name' => $sizeCategory
                ], [
                    'display_name' => ucfirst($sizeCategory),
                    'description' => "Auto-created category for {$sizeCategory}",
                    'display_order' => 0,
                    'is_active' => true
                ]);
                \Log::info('Size category found/created', ['category_id' => $sizeCategoryModel->id]);

                // Create the product size
                $productSize = ProductSize::create([
                    'product_id' => $product->id,
                    'size_category_id' => $sizeCategoryModel->id,
                    'name' => $sizeData['name'],
                    'value' => $sizeData['value'] ?? $sizeData['name'],
                    'additional_info' => $sizeData['additional_info'] ?? null,
                    'price_adjustment' => $sizeData['price_adjustment'] ?? 0,
                    'stock' => $sizeData['stock'] ?? 0,
                    'display_order' => $sizeData['display_order'] ?? 0,
                    'is_default' => isset($sizeData['is_default']) ? (bool)$sizeData['is_default'] : false,
                ]);
                \Log::info('Product size created', ['size_id' => $productSize->id]);

                // Create the color-size relationship
                $colorSize = ProductColorSize::create([
                    'product_id' => $product->id,
                    'product_color_id' => $color->id,
                    'product_size_id' => $productSize->id,
                    'stock' => $sizeData['stock'] ?? 0,
                    'price_adjustment' => $sizeData['price_adjustment'] ?? 0,
                    'is_available' => true,
                ]);
                \Log::info('Color-size relationship created', ['color_size_id' => $colorSize->id]);

            } catch (\Exception $e) {
                \Log::error('Error processing size', [
                    'sizeData' => $sizeData,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e; // Re-throw to stop the transaction
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
        $sizeCategory = SizeCategory::where('name', $categoryName)->first();
        return $sizeCategory ? $sizeCategory->id : null;
    }

    /**
     * API endpoint to get the latest product ID for testing purposes.
     */
    public function getLatestProductId()
    {
        $latestProduct = Product::where('merchant_id', Auth::user()->merchantRecord->id)
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
        $product = Product::where('merchant_id', Auth::user()->merchantRecord->id)
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
}
