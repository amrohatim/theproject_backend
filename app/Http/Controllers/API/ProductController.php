<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Deal;
use App\Services\ProductDealService;
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

    /**
     * Create a new controller instance.
     *
     * @param  \App\Services\ProductDealService  $dealService
     * @return void
     */
    public function __construct(ProductDealService $dealService)
    {
        $this->dealService = $dealService;
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
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully',
        ]);
    }

    /**
     * Get featured products for the homepage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function featured(Request $request)
    {
        $limit = $request->input('limit', 10);

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
}
