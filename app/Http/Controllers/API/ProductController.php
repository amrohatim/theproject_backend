<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Deal;
use App\Models\Category;
use App\Services\ProductDealService;
use App\Services\TrendingService;
use App\Services\ViewTrackingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * The product deal service instance.
     *
     * @var \App\Services\ProductDealService
     */
    protected $dealService;
    protected $trendingService;
    protected $viewTrackingService;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Services\ProductDealService  $dealService
     * @param  \App\Services\TrendingService  $trendingService
     * @param  \App\Services\ViewTrackingService  $viewTrackingService
     * @return void
     */
    public function __construct(
        ProductDealService $dealService,
        TrendingService $trendingService,
        ViewTrackingService $viewTrackingService
    )
    {
        $this->dealService = $dealService;
        $this->trendingService = $trendingService;
        $this->viewTrackingService = $viewTrackingService;
    }

    /**
     * Get trending products ordered by trending_score.
     */
    public function trendingProducts(Request $request)
    {
        $limit = (int) $request->input('limit', 20);

        // Primary: products with a positive trending score
        $products = Product::with(['branch', 'category'])
            ->where('is_available', true)
            ->where('trending_score', '>', 0)
            ->orderByDesc('trending_score')
            ->take($limit)
            ->get();

        // Fallback: if no scores, use order_count/view_count/rating
        if ($products->isEmpty()) {
            $products = Product::with(['branch', 'category'])
                ->where('is_available', true)
                ->orderByDesc('order_count')
                ->orderByDesc('view_count')
                ->orderByDesc('rating')
                ->take($limit)
                ->get();
        }

        // Add convenience fields
        $products->transform(function ($product) {
            $product->branch_name = $product->branch ? $product->branch->name : null;
            $product->default_color_image = $product->getDefaultColorImage();
            return $product;
        });

        return response()->json([
            'success' => true,
            'products' => $products,
        ]);
    }
    /**
     * Display a listing of the products with advanced filtering.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Product::with([
            'branch',
            'category',
            'colors',
            'sizes',
            'colorSizes.color',
            'colorSizes.size'
        ]);

        // Apply filters
        if ($request->has('branch_id')) {
            $query->filterByBranch($request->branch_id);
        }

        if ($request->has('category_id')) {
            $includeSubcategories = $request->boolean('include_subcategories', false);
            $query->filterByCategory($request->category_id, $includeSubcategories);
        }

        if ($request->has('min_price') && $request->has('max_price')) {
            $query->filterByPrice($request->min_price, $request->max_price);
        }

        if ($request->has('min_rating')) {
            $query->filterByRating($request->min_rating);
        }

        if ($request->has('only_available')) {
            $query->filterByAvailability($request->boolean('only_available'));
        }

        if ($request->has('only_in_stock')) {
            $query->filterByStock($request->boolean('only_in_stock'));
        }

        if ($request->has('has_discount')) {
            $query->filterByDiscount($request->boolean('has_discount'));
        }

        if ($request->has('featured')) {
            $query->filterByFeatured($request->boolean('featured'));
        }

        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Filter by deals only if requested
        if ($request->boolean('deals_only', false)) {
            $query->filterByActiveDeals();
        }

        // Apply emirate filter - only if provided and not empty
        if ($request->filled('emirate')) {
            $query->filterByEmirate($request->emirate);
        }

        // Apply sorting
        $sortBy = $request->input('sort_by', 'popularity');
        $query->sortBy($sortBy);

        // Paginate results
        $perPage = $request->input('per_page', 10);
        $products = $query->paginate($perPage);

        // Log the result count for deals filtering
        if ($request->boolean('deals_only', false)) {
            Log::info('ProductController: Products with deals found', [
                'total_count' => $products->total(),
                'current_page_count' => $products->count(),
                'category_id' => $request->get('category_id')
            ]);
        }

        // Add branch_name and deal information to each product
        $products->getCollection()->transform(function ($product) {
            // Add branch name (with null check)
            $product->branch_name = $product->branch ? $product->branch->name : null;

            // Log the original price from the database before any modifications
            \Illuminate\Support\Facades\Log::debug("Product {$product->id} ({$product->name}) - Database original_price: {$product->original_price}, price: {$product->price}");

            // Get deal information for the product
            $dealInfo = $this->dealService->calculateDiscountedPrice($product);

            // Add deal information to the product
            $product->has_discount = $dealInfo['has_discount'];
            $product->original_price = $dealInfo['original_price'];
            $product->discounted_price = $dealInfo['discounted_price'];
            $product->discount_percentage = $dealInfo['discount_percentage'];
            $product->discount_amount = $dealInfo['discount_amount'];

            // Log the final price values after applying deal info
            \Illuminate\Support\Facades\Log::debug("Product {$product->id} ({$product->name}) - Final values: has_discount: {$product->has_discount}, original_price: {$product->original_price}, price: {$product->price}, discounted_price: {$product->discounted_price}");

            // Add deal details if available
            if ($dealInfo['deal']) {
                $product->deal = $dealInfo['deal'];
            }

            // Add default color image if available
            $product->default_color_image = $product->getDefaultColorImage();

            // Log image paths for debugging
            \Illuminate\Support\Facades\Log::debug("Product {$product->id} main image: {$product->image}");
            \Illuminate\Support\Facades\Log::debug("Product {$product->id} default color image: {$product->default_color_image}");

            // Add color-size combinations data
            $colorSizeCombinations = [];
            foreach ($product->colorSizes as $colorSize) {
                $colorSizeCombinations[] = [
                    'id' => $colorSize->id,
                    'product_id' => $colorSize->product_id,
                    'color_id' => $colorSize->product_color_id,
                    'size_id' => $colorSize->product_size_id,
                    'color_name' => $colorSize->color ? $colorSize->color->name : null,
                    'color_code' => $colorSize->color ? $colorSize->color->color_code : null,
                    'size_name' => $colorSize->size ? $colorSize->size->name : null,
                    'size_value' => $colorSize->size ? $colorSize->size->value : null,
                    'stock' => $colorSize->stock,
                    'price_adjustment' => $colorSize->price_adjustment,
                    'is_available' => $colorSize->is_available,
                ];
            }

            // Add the color-size combinations to the product
            $product->color_size_combinations = $colorSizeCombinations;

            return $product;
        });

        return response()->json([
            'success' => true,
            'products' => $products,
        ]);
    }

    /**
     * Get branch products grouped by category with optional category filtering.
     */
    public function branchProductsByCategory(Request $request, $branchId)
    {
        $perPage = (int) $request->input('per_page', 10);
        $categoryId = $request->input('category_id');
        $includeProducts = $request->boolean('include_products', true);

        $categoryIds = Product::query()
            ->where('branch_id', $branchId)
            ->whereNotNull('category_id')
            ->distinct()
            ->pluck('category_id');

        $categoryRows = Category::query()
            ->select(['id', 'parent_id'])
            ->whereIn('id', $categoryIds)
            ->get();

        $categoryMap = $categoryRows->pluck('parent_id', 'id')->toArray();
        $pendingParentIds = array_values(array_filter($categoryMap));

        while (!empty($pendingParentIds)) {
            $pendingParentIds = array_values(array_diff(
                $pendingParentIds,
                array_keys($categoryMap)
            ));

            if (empty($pendingParentIds)) {
                break;
            }

            $parentRows = Category::query()
                ->select(['id', 'parent_id'])
                ->whereIn('id', $pendingParentIds)
                ->get();

            foreach ($parentRows as $row) {
                $categoryMap[$row->id] = $row->parent_id;
            }

            $pendingParentIds = $parentRows->pluck('parent_id')->filter()->values()->toArray();
        }

        $parentIds = collect($categoryIds)->map(function ($categoryId) use ($categoryMap) {
            $current = $categoryId;
            while (isset($categoryMap[$current]) && $categoryMap[$current]) {
                $current = $categoryMap[$current];
            }
            return $current;
        })->unique()->values();

        $categories = Category::query()
            ->whereIn('id', $parentIds)
            ->where('type', 'product')
            ->orderBy('name')
            ->get();

        $selectedCategoryId = $categoryId ?: ($categories->first()->id ?? null);

        if (!$includeProducts || $selectedCategoryId === null) {
            return response()->json([
                'success' => true,
                'categories' => $categories,
                'selected_category_id' => $selectedCategoryId,
                'products' => [
                    'data' => [],
                ],
            ]);
        }

        $query = Product::with([
            'branch',
            'category',
            'colors',
            'sizes',
            'colorSizes.color',
            'colorSizes.size'
        ])
            ->where('branch_id', $branchId);

        $includeSubcategories = $request->boolean('include_subcategories', true);
        $query->filterByCategory($selectedCategoryId, $includeSubcategories);

        if ($request->has('only_available')) {
            $query->filterByAvailability($request->boolean('only_available'));
        }

        $products = $query->paginate($perPage);

        $products->getCollection()->transform(function ($product) {
            $product->branch_name = $product->branch ? $product->branch->name : null;

            $dealInfo = $this->dealService->calculateDiscountedPrice($product);
            $product->has_discount = $dealInfo['has_discount'];
            $product->original_price = $dealInfo['original_price'];
            $product->discounted_price = $dealInfo['discounted_price'];
            $product->discount_percentage = $dealInfo['discount_percentage'];
            $product->discount_amount = $dealInfo['discount_amount'];

            if ($dealInfo['deal']) {
                $product->deal = $dealInfo['deal'];
            }

            $product->default_color_image = $product->getDefaultColorImage();

            $colorSizeCombinations = [];
            foreach ($product->colorSizes as $colorSize) {
                $colorSizeCombinations[] = [
                    'id' => $colorSize->id,
                    'product_id' => $colorSize->product_id,
                    'color_id' => $colorSize->product_color_id,
                    'size_id' => $colorSize->product_size_id,
                    'color_name' => $colorSize->color ? $colorSize->color->name : null,
                    'color_code' => $colorSize->color ? $colorSize->color->color_code : null,
                    'size_name' => $colorSize->size ? $colorSize->size->name : null,
                    'size_value' => $colorSize->size ? $colorSize->size->value : null,
                    'stock' => $colorSize->stock,
                    'price_adjustment' => $colorSize->price_adjustment,
                    'is_available' => $colorSize->is_available,
                ];
            }

            $product->color_size_combinations = $colorSizeCombinations;

            return $product;
        });

        return response()->json([
            'success' => true,
            'categories' => $categories,
            'selected_category_id' => $selectedCategoryId,
            'products' => $products,
        ]);
    }

    /**
     * Display the specified product.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::with([
            'branch',
            'category',
            'reviews.user',
            'colors.sizes',
            'sizes',
            'specifications',
            'colorSizes.color',
            'colorSizes.size'
        ])->findOrFail($id);

        // Track unique product view with duplicate prevention
        try {
            $this->viewTrackingService->trackView('product', $product->id, request());
        } catch (\Exception $e) {
            Log::warning('Failed to track product view', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
            ]);
        }

        // Add branch_name to the product (with null check)
        $product->branch_name = $product->branch ? $product->branch->name : null;

        // Log the original price from the database before any modifications
        \Illuminate\Support\Facades\Log::debug("Product {$product->id} ({$product->name}) - Database original_price: {$product->original_price}, price: {$product->price}");

        // Get deal information for the product
        $dealInfo = $this->dealService->calculateDiscountedPrice($product);

        // Log the deal information
        \Illuminate\Support\Facades\Log::debug("Product {$product->id} ({$product->name}) - Deal info: " . json_encode($dealInfo));

        // Add deal information to the product
        $product->has_discount = $dealInfo['has_discount'];
        $product->original_price = $dealInfo['original_price'];
        $product->discounted_price = $dealInfo['discounted_price'];
        $product->discount_percentage = $dealInfo['discount_percentage'];
        $product->discount_amount = $dealInfo['discount_amount'];

        // Log the final price values after applying deal info
        \Illuminate\Support\Facades\Log::debug("Product {$product->id} ({$product->name}) - Final values: has_discount: {$product->has_discount}, original_price: {$product->original_price}, price: {$product->price}, discounted_price: {$product->discounted_price}");

        // Add deal details if available
        if ($dealInfo['deal']) {
            $product->deal = $dealInfo['deal'];
        }

        // Add default color image if available
        $product->default_color_image = $product->getDefaultColorImage();

        // Log image paths for debugging
        \Illuminate\Support\Facades\Log::debug("Product {$product->id} detail - main image: {$product->image}");
        \Illuminate\Support\Facades\Log::debug("Product {$product->id} detail - default color image: {$product->default_color_image}");

        // Log color information for debugging
        if ($product->colors && $product->colors->count() > 0) {
            foreach ($product->colors as $color) {
                \Illuminate\Support\Facades\Log::debug("Product {$product->id} color {$color->id} - name: {$color->name}, image: {$color->image}");
            }
        }

        // Add color-size combinations data
        $colorSizeCombinations = [];
        foreach ($product->colorSizes as $colorSize) {
            $colorSizeCombinations[] = [
                'id' => $colorSize->id,
                'product_id' => $colorSize->product_id,
                'color_id' => $colorSize->product_color_id,
                'size_id' => $colorSize->product_size_id,
                'color_name' => $colorSize->color->name,
                'color_code' => $colorSize->color->color_code,
                'size_name' => $colorSize->size->name,
                'size_value' => $colorSize->size->value,
                'stock' => $colorSize->stock,
                'price_adjustment' => $colorSize->price_adjustment,
                'is_available' => $colorSize->is_available,
            ];
        }

        // Add the color-size combinations to the product
        $product->color_size_combinations = $colorSizeCombinations;

        return response()->json([
            'success' => true,
            'product' => $product,
        ]);
    }

    /**
     * Store a newly created product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'is_available' => 'boolean',
            'featured' => 'boolean',
            'specifications' => 'nullable|array',
            'specifications.*.key' => 'nullable|string|max:255',
            'specifications.*.value' => 'nullable|string',
            'specifications.*.display_order' => 'nullable|integer',
        ]);

        // Create the product
        $product = Product::create($request->except('specifications'));

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

        // Load the specifications relationship
        $product->load('specifications');

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'product' => $product,
        ], 201);
    }

    /**
     * Update the specified product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'branch_id' => 'exists:branches,id',
            'category_id' => 'exists:categories,id',
            'name' => 'string|max:255',
            'price' => 'numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'stock' => 'integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'is_available' => 'boolean',
            'featured' => 'boolean',
            'specifications' => 'nullable|array',
            'specifications.*.key' => 'nullable|string|max:255',
            'specifications.*.value' => 'nullable|string',
            'specifications.*.display_order' => 'nullable|integer',
        ]);

        // Update the product
        $product->update($request->except('specifications'));

        // Update specifications if provided
        if ($request->has('specifications')) {
            // Delete existing specifications
            $product->specifications()->delete();

            // Add new specifications (filter out empty ones)
            if (is_array($request->specifications)) {
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
        }

        // Load the specifications relationship
        $product->load('specifications');

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'product' => $product,
        ]);
    }

    /**
     * Remove the specified product from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);

            // The Product model's deleting event will handle cascading deletion
            // of colors, sizes, color-size combinations, specifications, and images
            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product and all related data deleted successfully',
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting product via API: ' . $e->getMessage(), [
                'product_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete product: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get featured products for the homepage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function featured(Request $request)
    {
        // Allow caller to specify limit (per_page) otherwise return a larger default
        $limit = $request->input('limit', $request->input('per_page', 100));

        $products = Product::with([
            'branch',
            'category',
            'colors',
            'sizes',
            'colorSizes.color',
            'colorSizes.size'
        ])
            ->where('featured', true)
            ->where('is_available', true)
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();

        // Add branch_name and deal information to each product
        $products->transform(function ($product) {
            // Add branch name (with null check)
            $product->branch_name = $product->branch ? $product->branch->name : null;

            // Get deal information for the product
            $dealInfo = $this->dealService->calculateDiscountedPrice($product);

            // Add deal information to the product
            $product->has_discount = $dealInfo['has_discount'];
            $product->original_price = $dealInfo['original_price'];
            $product->discounted_price = $dealInfo['discounted_price'];
            $product->discount_percentage = $dealInfo['discount_percentage'];
            $product->discount_amount = $dealInfo['discount_amount'];

            // Add deal details if available
            if ($dealInfo['deal']) {
                $product->deal = $dealInfo['deal'];
            }

            // Add default color image if available
            $product->default_color_image = $product->getDefaultColorImage();

            // Add color-size combinations data
            $colorSizeCombinations = [];
            foreach ($product->colorSizes as $colorSize) {
                $colorSizeCombinations[] = [
                    'id' => $colorSize->id,
                    'product_id' => $colorSize->product_id,
                    'color_id' => $colorSize->product_color_id,
                    'size_id' => $colorSize->product_size_id,
                    'color_name' => $colorSize->color->name,
                    'color_code' => $colorSize->color->color_code,
                    'size_name' => $colorSize->size->name,
                    'size_value' => $colorSize->size->value,
                    'stock' => $colorSize->stock,
                    'price_adjustment' => $colorSize->price_adjustment,
                    'is_available' => $colorSize->is_available,
                ];
            }

            // Add the color-size combinations to the product
            $product->color_size_combinations = $colorSizeCombinations;

            return $product;
        });

        return response()->json([
            'success' => true,
            'products' => $products,
        ]);
    }

    /**
     * Update the featured status of a product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateFeatured(Request $request, $id)
    {
        // Only admin can update featured status
        if (Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only admins can update featured status.',
            ], 403);
        }

        $product = Product::findOrFail($id);

        $request->validate([
            'featured' => 'required|boolean',
        ]);

        $product->featured = $request->featured;
        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Product featured status updated successfully',
            'product' => $product,
        ]);
    }

    /**
     * Get products with active deals.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deals(Request $request)
    {
        $limit = $request->input('limit', 10);

        // Get products with active deals
        $products = $this->dealService->getProductsWithActiveDeals($limit);

        // Load relationships
        $products->load(['branch', 'category']);

        // Add branch_name to each product
        $products->transform(function ($product) {
            $product->branch_name = $product->branch ? $product->branch->name : null;
            return $product;
        });

        return response()->json([
            'success' => true,
            'products' => $products,
        ]);
    }

    /**
     * Check stock availability for a product variant.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function checkStockAvailability(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);
            $quantity = $request->input('quantity', 1);
            $colorId = $request->input('color_id');
            $sizeId = $request->input('size_id');

            $available = false;
            $currentStock = 0;

            if ($colorId && $sizeId) {
                // Check specific color-size combination stock
                $colorSize = $product->colorSizes()
                    ->where('product_color_id', $colorId)
                    ->where('product_size_id', $sizeId)
                    ->first();
                
                if ($colorSize) {
                    $currentStock = $colorSize->stock;
                    $available = $colorSize->stock >= $quantity && $colorSize->is_available;
                }
            } elseif ($colorId) {
                // Check color variation stock
                $color = $product->colors()->where('id', $colorId)->first();
                if ($color) {
                    $currentStock = $color->stock;
                    $available = $color->stock >= $quantity && $color->is_available;
                }
            } else {
                // Check general product stock
                $currentStock = $product->stock;
                $available = $product->stock >= $quantity && $product->is_available;
            }

            return response()->json([
                'success' => true,
                'available' => $available,
                'quantity' => $currentStock,
                'requested_quantity' => $quantity,
            ]);

        } catch (\Exception $e) {
            Log::error('Error checking stock availability: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error checking stock availability',
                'available' => false,
                'quantity' => 0,
            ], 500);
        }
    }

    /**
     * Get available stock for a product variant.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getAvailableStock(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);
            $colorId = $request->input('color_id');
            $sizeId = $request->input('size_id');

            $stock = 0;

            if ($colorId && $sizeId) {
                // Get specific color-size combination stock
                $colorSize = $product->colorSizes()
                    ->where('product_color_id', $colorId)
                    ->where('product_size_id', $sizeId)
                    ->first();
                
                if ($colorSize && $colorSize->is_available) {
                    $stock = $colorSize->stock;
                }
            } elseif ($colorId) {
                // Get color variation stock
                $color = $product->colors()->where('id', $colorId)->first();
                if ($color && $color->is_available) {
                    $stock = $color->stock;
                }
            } else {
                // Get general product stock
                if ($product->is_available) {
                    $stock = $product->stock;
                }
            }

            return response()->json([
                'success' => true,
                'stock' => $stock,
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting available stock: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error getting available stock',
                'stock' => 0,
            ], 500);
        }
    }

    /**
     * Get comprehensive stock information for a product.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getStockInfo($id)
    {
        try {
            $product = Product::with([
                'colors',
                'colorSizes.color',
                'colorSizes.size'
            ])->findOrFail($id);

            // General product stock
            $stockInfo = [
                'success' => true,
                'general_stock' => $product->stock,
                'color_variations' => [],
                'color_size_combinations' => [],
            ];

            // Color variations stock
            foreach ($product->colors as $color) {
                $stockInfo['color_variations'][] = [
                    'id' => $color->id,
                    'name' => $color->name,
                    'color_code' => $color->color_code,
                    'stock' => $color->stock,
                    'is_available' => $color->is_available,
                ];
            }

            // Color-size combinations stock
            foreach ($product->colorSizes as $colorSize) {
                $stockInfo['color_size_combinations'][] = [
                    'id' => $colorSize->id,
                    'color_id' => $colorSize->product_color_id,
                    'size_id' => $colorSize->product_size_id,
                    'color_name' => $colorSize->color ? $colorSize->color->name : null,
                    'size_name' => $colorSize->size ? $colorSize->size->name : null,
                    'stock' => $colorSize->stock,
                    'is_available' => $colorSize->is_available,
                ];
            }

            return response()->json($stockInfo);

        } catch (\Exception $e) {
            Log::error('Error getting stock info: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error getting stock information',
            ], 500);
        }
    }

    /**
     * Get featured products.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getFeatured(Request $request)
    {
        return $this->featured($request);
    }

    /**
     * Get product options (colors, sizes, etc.).
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getOptions($id)
    {
        try {
            $product = Product::with([
                'colors',
                'sizes',
                'colorSizes.color',
                'colorSizes.size'
            ])->findOrFail($id);

            $options = [
                'success' => true,
                'colors' => $product->colors->map(function ($color) {
                    return [
                        'id' => $color->id,
                        'name' => $color->name,
                        'color_code' => $color->color_code,
                        'image' => $color->image,
                        'stock' => $color->stock,
                        'is_available' => $color->is_available,
                    ];
                }),
                'sizes' => $product->sizes->map(function ($size) {
                    return [
                        'id' => $size->id,
                        'name' => $size->name,
                        'value' => $size->value,
                        'stock' => $size->stock,
                        'is_available' => $size->is_available,
                    ];
                }),
                'color_size_combinations' => $product->colorSizes->map(function ($colorSize) {
                    return [
                        'id' => $colorSize->id,
                        'color_id' => $colorSize->product_color_id,
                        'size_id' => $colorSize->product_size_id,
                        'color_name' => $colorSize->color ? $colorSize->color->name : null,
                        'size_name' => $colorSize->size ? $colorSize->size->name : null,
                        'stock' => $colorSize->stock,
                        'price_adjustment' => $colorSize->price_adjustment,
                        'is_available' => $colorSize->is_available,
                    ];
                }),
            ];

            return response()->json($options);

        } catch (\Exception $e) {
            Log::error('Error getting product options: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error getting product options',
            ], 500);
        }
    }

    /**
     * Validate product options selection.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function validateOptions(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);
            $selectedOptions = $request->input('selected_options', []);

            $validation = [
                'success' => true,
                'valid' => true,
                'errors' => [],
                'total_price' => $product->price,
            ];

            foreach ($selectedOptions as $option) {
                $colorId = $option['color_id'] ?? null;
                $sizeId = $option['size_id'] ?? null;
                $quantity = $option['quantity'] ?? 1;

                if ($colorId && $sizeId) {
                    // Validate color-size combination
                    $colorSize = $product->colorSizes()
                        ->where('product_color_id', $colorId)
                        ->where('product_size_id', $sizeId)
                        ->first();

                    if (!$colorSize) {
                        $validation['valid'] = false;
                        $validation['errors'][] = 'Invalid color-size combination';
                    } elseif (!$colorSize->is_available || $colorSize->stock < $quantity) {
                        $validation['valid'] = false;
                        $validation['errors'][] = 'Selected combination is not available or insufficient stock';
                    } else {
                        $validation['total_price'] += $colorSize->price_adjustment;
                    }
                }
            }

            return response()->json($validation);

        } catch (\Exception $e) {
            Log::error('Error validating product options: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error validating product options',
            ], 500);
        }
    }

    /**
     * Serve product images directly.
     *
     * @param  string  $filename
     * @return \Illuminate\Http\Response
     */
    public function getImage($filename)
    {
        $path = storage_path('app/public/images/products/' . $filename);
        
        if (!file_exists($path)) {
            // Return a placeholder image or 404
            return response()->json(['error' => 'Image not found'], 404);
        }

        return response()->file($path);
    }

    /**
     * Serve provider product images directly.
     *
     * @param  string  $filename
     * @return \Illuminate\Http\Response
     */
    public function getProviderProductImage($filename)
    {
        $path = storage_path('app/public/images/provider_products/' . $filename);
        
        if (!file_exists($path)) {
            // Return a placeholder image or 404
            return response()->json(['error' => 'Image not found'], 404);
        }

        return response()->file($path);
    }
}
