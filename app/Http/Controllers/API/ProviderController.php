<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Provider;

use App\Models\Product;
use App\Models\ProviderProduct;
use App\Models\ProviderLocation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProviderController extends Controller
{
    /**
     * Transform provider products to match the expected format for the API response.
     *
     * @param \Illuminate\Pagination\LengthAwarePaginator $providerProducts
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    private function transformProviderProducts($providerProducts)
    {
        // Transform the provider products to match the expected format
        $transformedProducts = $providerProducts->map(function ($providerProduct) {
            // Get the related product
            $product = $providerProduct->product;

            if (!$product) {
                // If there's no related product, create a product-like object from provider_product data
                Log::info("Creating product-like object for provider product ID {$providerProduct->id} with no related product");

                // Ensure we have valid values for required fields
                $categoryId = $providerProduct->category_id ?? 1; // Default to category ID 1 if null
                $productName = $providerProduct->product_name ?? 'Unknown Product'; // Default name if null
                $price = $providerProduct->price ?? 0; // Default price if null
                $stock = $providerProduct->stock ?? 0; // Default stock if null

                // Set is_active to true if it's null
                $isActive = $providerProduct->is_active ?? true;

                // Get branch information if available
                $branchId = $providerProduct->branch_id ?? 0;
                $branchName = null;
                if ($branchId && $providerProduct->branch) {
                    $branchName = $providerProduct->branch->name;
                }

                // Get category information if available
                $categoryName = null;
                if ($categoryId && $providerProduct->category) {
                    $categoryName = $providerProduct->category->name;
                }

                return [
                    'id' => $providerProduct->id,
                    'branch_id' => $branchId,
                    'category_id' => $categoryId,
                    'name' => $productName,
                    'price' => $price,
                    'original_price' => $providerProduct->original_price,
                    'stock' => $stock,
                    'description' => $providerProduct->description,
                    'image' => $providerProduct->image,
                    'is_available' => $isActive,
                    // Add other fields that the Flutter app expects
                    'rating' => null,
                    'featured' => false,
                    'has_discount' => false,
                    'branch_name' => $branchName,
                    'category_name' => $categoryName,
                ];
            }

            // Merge product data with provider product data
            $productData = $product->toArray();
            Log::info("Transforming provider product ID {$providerProduct->id} with related product ID {$product->id}");

            // Override product fields with provider product fields if they exist
            if ($providerProduct->product_name) {
                $productData['name'] = $providerProduct->product_name;
            }
            if ($providerProduct->price !== null) {
                $productData['price'] = $providerProduct->price;
            }
            if ($providerProduct->original_price !== null) {
                $productData['original_price'] = $providerProduct->original_price;
            }
            if ($providerProduct->stock !== null) {
                $productData['stock'] = $providerProduct->stock;
            }
            if ($providerProduct->description) {
                $productData['description'] = $providerProduct->description;
            }
            if ($providerProduct->image) {
                $productData['image'] = $providerProduct->image;
            }

            // Ensure is_available is set based on provider product's is_active
            $productData['is_available'] = $providerProduct->is_active ?? true;

            return $productData;
        });

        // Create a new paginator with the transformed products
        return new \Illuminate\Pagination\LengthAwarePaginator(
            $transformedProducts,
            $providerProducts->total(),
            $providerProducts->perPage(),
            $providerProducts->currentPage(),
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );
    }
    /**
     * Display a listing of providers.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            // Start with a query that includes the user relationship to get profile_image
            $query = Provider::with('user:id,name,profile_image');

            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('search')) {
                $query->where('business_name', 'like', "%{$request->search}%");
            }

            if ($request->has('is_verified')) {
                $query->where('is_verified', $request->boolean('is_verified'));
            }

            // Get providers with pagination
            $providers = $query->paginate(15);

            // Log for debugging
            Log::info("Fetched " . $providers->count() . " providers with user data");

            return response()->json([
                'success' => true,
                'providers' => $providers,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching providers: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch providers: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified provider.
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        try {
            // Include the user relationship to get profile_image
            $provider = Provider::with('user:id,name,profile_image')->findOrFail($id);

            // Track the view for analytics using the new service
            try {
                $viewTrackingService = app(\App\Services\ViewTrackingService::class);
                $viewTrackingService->trackView('provider', $id, $request);
            } catch (\Exception $e) {
                // Silently fail if tracking fails
                Log::warning("Failed to track provider view: " . $e->getMessage());
            }

            // Log for debugging
            Log::info("Fetched provider ID {$id} with user data");

            return response()->json([
                'success' => true,
                'provider' => $provider,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching provider details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch provider details: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all products from all providers.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getAllProducts(Request $request)
    {
        try {
            // First, check if there are any provider products at all
            $totalInTable = ProviderProduct::count();
            Log::info("Total provider products in table (before any filters): {$totalInTable}");

            // Check how many are active vs inactive
            $activeInTable = ProviderProduct::where('is_active', true)->count();
            $inactiveInTable = ProviderProduct::where('is_active', false)->count();
            $nullActiveInTable = ProviderProduct::whereNull('is_active')->count();

            Log::info("Provider products with is_active=true: {$activeInTable}");
            Log::info("Provider products with is_active=false: {$inactiveInTable}");
            Log::info("Provider products with is_active=NULL: {$nullActiveInTable}");

            // If there are no active products, but there are inactive ones, let's temporarily remove the is_active filter
            $skipActiveFilter = ($activeInTable == 0 && ($inactiveInTable > 0 || $nullActiveInTable > 0));

            if ($skipActiveFilter) {
                Log::warning("No active provider products found, temporarily skipping is_active filter for debugging");
            }

            // Get all provider products from the provider_products table
            $query = ProviderProduct::query()
                ->with(['product.category', 'product.colors', 'product.sizes', 'category', 'branch']);

            // Apply filters if needed
            if ($request->filled('search')) {
                $query->where('product_name', 'like', "%{$request->search}%");
            }

            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            // Temporarily disable the is_active filter to show all provider products
            // if (!$skipActiveFilter) {
            //     $query->where('is_active', true);
            // }

            // Debug: Count total provider products before pagination
            $totalCount = $query->count();
            Log::info("Total provider products count after filters but before pagination: {$totalCount}");

            // Get provider products with pagination
            $providerProducts = $query->paginate(20);

            Log::info("Provider products after pagination: {$providerProducts->count()} items");

            // Debug: Log each provider product
            foreach ($providerProducts as $index => $providerProduct) {
                Log::info("Provider product #{$index}: ID={$providerProduct->id}, Name={$providerProduct->product_name}, Active=" .
                    ($providerProduct->is_active === null ? 'NULL' : ($providerProduct->is_active ? 'true' : 'false')));

                // Check if product relation exists
                if ($providerProduct->product) {
                    Log::info("  - Related product exists: ID={$providerProduct->product->id}, Name={$providerProduct->product->name}");
                } else {
                    Log::warning("  - No related product found for provider_product ID {$providerProduct->id}");
                }
            }

            // Transform the provider products to match the expected format
            $paginatedProducts = $this->transformProviderProducts($providerProducts);

            Log::info("Transformed products count: {$paginatedProducts->count()}");

            return response()->json([
                'success' => true,
                'products' => $paginatedProducts,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching provider products: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch provider products: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get products for a specific provider.
     *
     * @param  int  $providerId
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getProviderProducts($providerId, Request $request)
    {
        try {
            $provider = Provider::findOrFail($providerId);

            // Get provider products for this provider
            $query = ProviderProduct::query()
                ->where('provider_id', $providerId)
                // Temporarily disable the is_active filter to show all provider products
                // ->where('is_active', true)
                ->with(['product.category', 'product.colors', 'product.sizes', 'category', 'branch']);

            // Apply filters if needed
            if ($request->filled('search')) {
                $query->where('product_name', 'like', "%{$request->search}%");
            }

            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            // Get provider products with pagination
            $providerProducts = $query->paginate(20);

            // Transform the provider products to match the expected format
            $paginatedProducts = $this->transformProviderProducts($providerProducts);

            return response()->json([
                'success' => true,
                'provider' => $provider,
                'products' => $paginatedProducts,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching provider products: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch provider products: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get products by category for all providers.
     *
     * @param  int  $categoryId
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getProductsByCategory($categoryId, Request $request)
    {
        try {
            // Check if the category is a parent category (has no parent_id)
            $category = \App\Models\Category::find($categoryId);
            $isParentCategory = $category && $category->parent_id === null;

            // For parent categories, automatically include subcategories
            $includeSubcategories = $request->boolean('include_subcategories', $isParentCategory);

            Log::info("Getting products for category ID: {$categoryId}, is parent: " . ($isParentCategory ? 'true' : 'false') . ", include subcategories: " . ($includeSubcategories ? 'true' : 'false'));

            // Get the category and its subcategories if needed
            $categoryIds = [$categoryId];

            if ($includeSubcategories && $category) {
                // Get all subcategory IDs
                $subcategoryIds = $category->children()->pluck('id')->toArray();

                Log::info("Found " . count($subcategoryIds) . " subcategories for category {$categoryId}: " . implode(', ', $subcategoryIds));

                // Merge with the parent category ID
                $categoryIds = array_merge($categoryIds, $subcategoryIds);

                Log::info("Final category IDs to query: " . implode(', ', $categoryIds));
            } else if (!$category) {
                Log::warning("Category {$categoryId} not found");
            }

            // Get provider products for this category and subcategories if requested
            $query = ProviderProduct::query()
                ->whereIn('category_id', $categoryIds)
                // Temporarily disable the is_active filter to show all provider products
                // ->where('is_active', true)
                ->with(['product.category', 'product.colors', 'product.sizes', 'category', 'branch']);

            // Apply filters if needed
            if ($request->filled('search')) {
                $query->where('product_name', 'like', "%{$request->search}%");
            }

            // Log the SQL query for debugging
            Log::info("Provider products query SQL: " . $query->toSql());
            Log::info("Provider products query bindings: " . json_encode($query->getBindings()));

            // Get provider products with pagination
            $providerProducts = $query->paginate(20);

            Log::info("Found " . $providerProducts->total() . " provider products for the selected categories");

            // Transform the provider products to match the expected format
            $paginatedProducts = $this->transformProviderProducts($providerProducts);

            return response()->json([
                'success' => true,
                'category_id' => $categoryId,
                'include_subcategories' => $includeSubcategories,
                'category_ids' => $categoryIds,
                'products' => $paginatedProducts,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching category products: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch category products: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get analytics data for providers.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAnalytics()
    {
        try {
            // Get analytics data
            $totalProviders = Provider::count();
            $activeProviders = Provider::where('status', 'active')->count();
            $totalProducts = ProviderProduct::where('is_active', true)->count();

            // Get top providers by product count
            $topProviders = Provider::select('providers.*', DB::raw('COUNT(provider_products.id) as product_count'))
                ->leftJoin('provider_products', 'providers.id', '=', 'provider_products.provider_id')
                ->where('provider_products.is_active', true)
                ->groupBy('providers.id')
                ->orderBy('product_count', 'desc')
                ->limit(5)
                ->get();

            return response()->json([
                'success' => true,
                'analytics' => [
                    'total_providers' => $totalProviders,
                    'active_providers' => $activeProviders,
                    'total_products' => $totalProducts,
                    'top_providers' => $topProviders,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching provider analytics: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch provider analytics: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get locations for a specific provider.
     *
     * @param  int  $providerId
     * @return \Illuminate\Http\Response
     */
    public function getProviderLocations($providerId)
    {
        try {
            // Find the provider
            $provider = Provider::findOrFail($providerId);

            // Get locations for this provider directly
            $locations = ProviderLocation::where('provider_id', $providerId)->get();

            Log::info("Fetched {$locations->count()} locations for provider ID: {$providerId}");

            return response()->json([
                'success' => true,
                'locations' => $locations,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching provider locations: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch provider locations: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get products for a specific provider.
     * This method name matches the route definition.
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getProducts($id, Request $request)
    {
        return $this->getProviderProducts($id, $request);
    }

    /**
     * Get categories that have provider products.
     * Returns parent categories with their filtered children that have products.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCategoriesWithProducts()
    {
        try {
            Log::info('Getting categories with provider products...');

            // First, get all subcategories that have provider products
            $subcategoriesWithProducts = DB::select("
                SELECT DISTINCT
                    child_cats.id,
                    child_cats.name,
                    child_cats.description,
                    child_cats.image,
                    child_cats.parent_id,
                    child_cats.is_active,
                    child_cats.type,
                    child_cats.icon,
                    child_cats.view_count,
                    child_cats.purchase_count,
                    child_cats.trending_score,
                    parent_cats.name as parent_name,
                    COUNT(DISTINCT pp.id) as product_count
                FROM categories child_cats
                INNER JOIN categories parent_cats ON child_cats.parent_id = parent_cats.id
                LEFT JOIN provider_products pp ON (
                    pp.category_id = child_cats.id
                    OR (pp.product_id IS NOT NULL AND EXISTS (
                        SELECT 1 FROM products p WHERE p.id = pp.product_id AND p.category_id = child_cats.id
                    ))
                )
                WHERE child_cats.is_active = 1
                AND child_cats.parent_id IS NOT NULL
                AND pp.id IS NOT NULL
                GROUP BY child_cats.id, child_cats.name, child_cats.description, child_cats.image,
                         child_cats.parent_id, child_cats.is_active, child_cats.type, child_cats.icon,
                         child_cats.view_count, child_cats.purchase_count, child_cats.trending_score, parent_cats.name
                HAVING product_count > 0
                ORDER BY parent_cats.name, child_cats.name
            ");

            // Group subcategories by parent_id
            $subcategoriesByParent = collect($subcategoriesWithProducts)->groupBy('parent_id');

            // Get unique parent IDs that have children with products
            $parentIds = $subcategoriesByParent->keys();

            // Get parent categories
            $parentCategories = \App\Models\Category::whereIn('id', $parentIds)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();

            // Build hierarchical structure
            $categoriesWithProducts = $parentCategories->map(function ($parent) use ($subcategoriesByParent) {
                $children = $subcategoriesByParent->get($parent->id, collect())->map(function ($child) {
                    return [
                        'id' => $child->id,
                        'name' => $child->name,
                        'description' => $child->description,
                        'image' => $child->image,
                        'parent_id' => $child->parent_id,
                        'is_active' => (bool) $child->is_active, // Ensure boolean type
                        'type' => $child->type,
                        'icon' => $child->icon,
                        'view_count' => $child->view_count ?? 0,
                        'purchase_count' => $child->purchase_count ?? 0,
                        'trending_score' => $child->trending_score ?? 0,
                        'product_count' => $child->product_count,
                        'parent_name' => $child->parent_name, // Include parent name for backward compatibility
                    ];
                })->toArray();

                return [
                    'id' => $parent->id,
                    'name' => $parent->name,
                    'description' => $parent->description,
                    'image' => $parent->image,
                    'parent_id' => $parent->parent_id,
                    'is_active' => $parent->is_active,
                    'type' => $parent->type,
                    'icon' => $parent->icon,
                    'view_count' => $parent->view_count ?? 0,
                    'purchase_count' => $parent->purchase_count ?? 0,
                    'trending_score' => $parent->trending_score ?? 0,
                    'children' => $children,
                ];
            });

            Log::info("Found {$categoriesWithProducts->count()} parent categories with filtered children");

            return response()->json([
                'success' => true,
                'categories' => $categoriesWithProducts,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching categories with provider products: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch categories with products: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Track a view for a provider.
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function trackView($id, Request $request)
    {
        $provider = Provider::findOrFail($id);

        // Use the new view tracking service with duplicate prevention
        $viewTrackingService = app(\App\Services\ViewTrackingService::class);
        $tracked = $viewTrackingService->trackView('provider', $id, $request);

        return response()->json([
            'success' => true,
            'message' => $tracked ? 'Provider view tracked successfully' : 'View already tracked recently',
            'tracked' => $tracked,
        ]);
    }

    /**
     * Track an order for a provider.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function trackOrder($id)
    {
        $provider = Provider::findOrFail($id);

        // Use the trending service to track the order
        app(\App\Services\TrendingService::class)->incrementProviderOrder($id);

        return response()->json([
            'success' => true,
            'message' => 'Provider order tracked successfully',
        ]);
    }
}
