<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Category::query();

        // Filter by type if provided
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by parent_id if provided
        if ($request->has('parent_id')) {
            $query->where('parent_id', $request->parent_id);
        }

        // Filter by is_active if provided
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Get only parent categories
        if ($request->has('parents_only') && $request->boolean('parents_only')) {
            $query->whereNull('parent_id');
        }

        // Include children if requested
        if ($request->has('with_children') && $request->boolean('with_children')) {
            $query->with('children');
        }

        $categories = $query->get();

        // Add total counts for each category (including subcategories)
        foreach ($categories as $category) {
            // Calculate both product and service counts for all categories
            // This allows parent categories to show content from mixed-type children
            $totalProductCount = $this->getTotalProductCount($category);
            $totalServiceCount = $this->getTotalServiceCount($category);

            $category->total_product_count = $totalProductCount;
            $category->total_service_count = $totalServiceCount;

            // Add counts for children if they exist
            if ($category->children) {
                foreach ($category->children as $child) {
                    // Calculate both counts for children as well
                    $childProductCount = $this->getTotalProductCount($child);
                    $childServiceCount = $this->getTotalServiceCount($child);

                    $child->total_product_count = $childProductCount;
                    $child->total_service_count = $childServiceCount;
                }
            }
        }

        // Apply filtering logic to remove empty categories
        $filteredCategories = $this->filterEmptyCategories($categories, $request);

        return response()->json([
            'success' => true,
            'categories' => $filteredCategories->values(), // Reset array keys to ensure proper JSON array
        ]);
    }

    /**
     * Store a newly created category in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Only admin can create categories
        if (!Auth::user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean',
        ]);

        $category = Category::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully',
            'category' => $category,
        ], 201);
    }

    /**
     * Display the specified category.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $category = Category::with(['parent', 'children'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'category' => $category,
        ]);
    }

    /**
     * Update the specified category in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Only admin can update categories
        if (!Auth::user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }

        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean',
        ]);

        // Prevent category from being its own parent
        if ($request->has('parent_id') && $request->parent_id == $id) {
            return response()->json([
                'success' => false,
                'message' => 'A category cannot be its own parent',
            ], 400);
        }

        $category->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully',
            'category' => $category,
        ]);
    }

    /**
     * Remove the specified category from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Only admin can delete categories
        if (!Auth::user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }

        $category = Category::findOrFail($id);

        // Check if category has children
        if ($category->children()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category with subcategories',
            ], 400);
        }

        // Check if category has products or services
        if ($category->products()->count() > 0 || $category->services()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category with associated products or services',
            ], 400);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully',
        ]);
    }
    /**
     * Get trending categories.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function trending(Request $request)
    {
        $limit = $request->input('limit', 10);
        $type = $request->input('type');

        // Build base query
        $baseQuery = Category::where('is_active', true);

        // Filter by type if provided
        if ($type) {
            $baseQuery->where('type', $type);
        }

        // First try to get categories with trending scores > 0
        $categories = (clone $baseQuery)
            ->where('trending_score', '>', 0)
            ->withCount(['products', 'services'])
            ->orderBy('trending_score', 'desc')
            ->take($limit)
            ->get();

        // If no trending categories found, fall back to all active categories
        // ordered by view_count or just by name
        if ($categories->isEmpty()) {
            Log::info('No trending categories found, falling back to all active categories');

            $categories = (clone $baseQuery)
                ->withCount(['products', 'services'])
                ->orderBy('view_count', 'desc')
                ->orderBy('name', 'asc')
                ->take($limit)
                ->get();
        }

        // Add total counts for each category (including subcategories)
        foreach ($categories as $category) {
            // Calculate both product and service counts for all categories
            $totalProductCount = $this->getTotalProductCount($category);
            $totalServiceCount = $this->getTotalServiceCount($category);

            $category->total_product_count = $totalProductCount;
            $category->total_service_count = $totalServiceCount;
        }

        // Filter out empty categories for trending as well
        $filteredCategories = $categories->filter(function ($category) {
            // For trending categories, only show categories with content
            $hasContent = ($category->total_product_count ?? 0) > 0 ||
                         ($category->total_service_count ?? 0) > 0;
            return $hasContent;
        });

        Log::info("Returning {$filteredCategories->count()} trending categories (after filtering)");

        return response()->json([
            'success' => true,
            'categories' => $filteredCategories->values(), // Reset array keys
            'message' => $filteredCategories->isEmpty() ? 'No categories found' : 'Categories retrieved successfully',
        ]);
    }

    /**
     * Get categories that have active deals.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function categoriesWithDeals(Request $request)
    {
        $limit = $request->input('limit', 10);
        $type = $request->input('type');

        // Get all active deals
        $today = now()->format('Y-m-d');
        $activeDeals = \App\Models\Deal::where('status', 'active')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->get();

        if ($activeDeals->isEmpty()) {
            return response()->json([
                'success' => true,
                'categories' => [],
                'message' => 'No active deals found',
            ]);
        }

        // Collect all category IDs that have active deals
        $categoryIdsWithDeals = collect();

        foreach ($activeDeals as $deal) {
            if ($deal->applies_to === 'products' && !empty($deal->product_ids)) {
                // For product-specific deals, get the categories of those products
                $dealProductIds = is_string($deal->product_ids)
                    ? json_decode($deal->product_ids, true)
                    : $deal->product_ids;

                if (is_array($dealProductIds)) {
                    $productCategories = \App\Models\Product::whereIn('id', $dealProductIds)
                        ->pluck('category_id');
                    $categoryIdsWithDeals = $categoryIdsWithDeals->merge($productCategories);
                }
            }
        }

        // Remove duplicates and filter out null values
        $categoryIdsWithDeals = $categoryIdsWithDeals->unique()->filter();

        if ($categoryIdsWithDeals->isEmpty()) {
            return response()->json([
                'success' => true,
                'categories' => [],
                'message' => 'No categories with active deals found',
            ]);
        }

        // Build query for categories with deals
        $query = Category::whereIn('id', $categoryIdsWithDeals->toArray())
            ->where('is_active', true);

        // Filter by type if provided
        if ($type) {
            $query->where('type', $type);
        }

        // Get categories with counts
        $categories = $query->withCount(['products', 'services'])
            ->orderBy('trending_score', 'desc')
            ->orderBy('view_count', 'desc')
            ->take($limit)
            ->get();

        // Add total counts for each category (including subcategories)
        foreach ($categories as $category) {
            // Calculate both product and service counts for all categories
            $totalProductCount = $this->getTotalProductCount($category);
            $totalServiceCount = $this->getTotalServiceCount($category);

            $category->total_product_count = $totalProductCount;
            $category->total_service_count = $totalServiceCount;
        }

        // Filter out categories that don't actually have content with deals
        $filteredCategories = $categories->filter(function ($category) use ($activeDeals) {
            // For product categories, check if they have products with deals
            if ($category->type === 'product' || $category->type === null) {
                $hasProductsWithDeals = $this->categoryHasProductsWithDeals($category, $activeDeals);
                return $hasProductsWithDeals;
            }

            // For service categories, check if they have services with deals
            if ($category->type === 'service') {
                $hasServicesWithDeals = $this->categoryHasServicesWithDeals($category, $activeDeals);
                return $hasServicesWithDeals;
            }

            return false;
        });

        Log::info("Returning {$filteredCategories->count()} categories with deals (after filtering)");

        return response()->json([
            'success' => true,
            'categories' => $filteredCategories->values(),
            'message' => $filteredCategories->isEmpty() ? 'No categories with deals found' : 'Categories with deals retrieved successfully',
        ]);
    }

    /**
     * Get categories that have active deals.
     * This method name matches the route definition.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getCategoriesWithDeals(Request $request)
    {
        return $this->categoriesWithDeals($request);
    }

    /**
     * Check if a category has products with active deals.
     *
     * @param  \App\Models\Category  $category
     * @param  \Illuminate\Support\Collection  $activeDeals
     * @return bool
     */
    private function categoryHasProductsWithDeals($category, $activeDeals)
    {
        // Get all products in this category (including subcategories)
        $productIds = $this->getAllProductIdsInCategory($category);

        if ($productIds->isEmpty()) {
            return false;
        }

        // Check if any active deals apply to these products
        foreach ($activeDeals as $deal) {
            if ($deal->applies_to === 'products') {
                $dealProductIds = is_string($deal->product_ids)
                    ? json_decode($deal->product_ids, true)
                    : $deal->product_ids;

                if (is_array($dealProductIds)) {
                    $hasMatchingProducts = $productIds->intersect($dealProductIds)->isNotEmpty();
                    if ($hasMatchingProducts) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Check if a category has services with active deals.
     *
     * @param  \App\Models\Category  $category
     * @param  \Illuminate\Support\Collection  $activeDeals
     * @return bool
     */
    private function categoryHasServicesWithDeals($category, $activeDeals)
    {
        // For now, we'll return false since services don't typically have deals
        // This can be expanded later if service deals are implemented
        return false;
    }

    /**
     * Get all product IDs in a category including subcategories.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Support\Collection
     */
    private function getAllProductIdsInCategory($category)
    {
        $productIds = collect();

        // Get direct products
        $directProducts = $category->products()->pluck('id');
        $productIds = $productIds->merge($directProducts);

        // Get products from subcategories recursively
        $subcategories = Category::where('parent_id', $category->id)->get();
        foreach ($subcategories as $subcategory) {
            $subcategoryProducts = $this->getAllProductIdsInCategory($subcategory);
            $productIds = $productIds->merge($subcategoryProducts);
        }

        return $productIds;
    }

    /**
     * Track a view for a category.
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function trackView($id, Request $request)
    {
        $category = Category::findOrFail($id);

        // Use the new view tracking service with duplicate prevention
        $viewTrackingService = app(\App\Services\ViewTrackingService::class);
        $tracked = $viewTrackingService->trackView('category', $id, $request);

        return response()->json([
            'success' => true,
            'message' => $tracked ? 'Category view tracked successfully' : 'View already tracked recently',
            'tracked' => $tracked,
        ]);
    }

    /**
     * Track a purchase for a category.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function trackPurchase($id)
    {
        $category = Category::findOrFail($id);

        // Use the trending service to track the purchase
        app(\App\Services\TrendingService::class)->incrementCategoryPurchase($id);

        return response()->json([
            'success' => true,
            'message' => 'Category purchase tracked successfully',
        ]);
    }

    /**
     * Get total product count for a category including all subcategories.
     *
     * @param  \App\Models\Category  $category
     * @return int
     */
    private function getTotalProductCount($category)
    {
        // Start with direct products count
        $totalCount = $category->products()->count();

        // Add products from all subcategories recursively
        $subcategories = Category::where('parent_id', $category->id)->get();
        foreach ($subcategories as $subcategory) {
            $totalCount += $this->getTotalProductCount($subcategory);
        }

        return $totalCount;
    }

    /**
     * Get total service count for a category including all subcategories.
     *
     * @param  \App\Models\Category  $category
     * @return int
     */
    private function getTotalServiceCount($category)
    {
        // Start with direct services count
        $totalCount = $category->services()->count();

        // Add services from all subcategories recursively
        $subcategories = Category::where('parent_id', $category->id)->get();
        foreach ($subcategories as $subcategory) {
            $totalCount += $this->getTotalServiceCount($subcategory);
        }

        return $totalCount;
    }

    /**
     * Filter out empty categories based on the filtering rules.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $categories
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function filterEmptyCategories($categories, $request)
    {
        return $categories->filter(function ($category) use ($request) {
            // For parent categories (categories with no parent_id)
            if ($category->parent_id === null) {
                // Check if parent category has children
                $hasChildren = $category->children && $category->children->count() > 0;

                if (!$hasChildren) {
                    // Parent category with no children - don't display
                    return false;
                }

                // Filter children to only include those with content
                if ($category->children) {
                    $filteredChildren = $category->children->filter(function ($child) {
                        // Child category must have products OR services
                        $hasContent = ($child->total_product_count ?? 0) > 0 ||
                                     ($child->total_service_count ?? 0) > 0;
                        return $hasContent;
                    });

                    // Update the children collection with filtered results (reset array keys)
                    $category->setRelation('children', $filteredChildren->values());

                    // If no children have content, don't display the parent
                    return $filteredChildren->count() > 0;
                }

                return false;
            } else {
                // For child categories, check if they have products OR services
                $hasContent = ($category->total_product_count ?? 0) > 0 ||
                             ($category->total_service_count ?? 0) > 0;
                return $hasContent;
            }
        });
    }
}