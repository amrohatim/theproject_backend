<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Service;
use App\Models\Company;
use App\Models\Merchant;
use App\Services\ProductDealService;
use App\Services\ServiceDealService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    protected ProductDealService $productDealService;
    protected ServiceDealService $serviceDealService;

    public function __construct(
        ProductDealService $productDealService,
        ServiceDealService $serviceDealService
    ) {
        $this->productDealService = $productDealService;
        $this->serviceDealService = $serviceDealService;
    }
    /**
     * Apply filters to products or services based on the filter type
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function filter(Request $request)
    {
        try {
            Log::info('Filter request received', $request->all());

            // Validate the request data
            $validationResult = $this->validateFilterRequest($request);
            if ($validationResult !== true) {
                return $validationResult;
            }

            // Get the filter type (product or service)
            $type = $request->input('type', 'product');

            if ($type === 'service') {
                return $this->filterServices($request);
            } else {
                return $this->filterProducts($request);
            }
        } catch (\Exception $e) {
            Log::error('Error in filter method: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Failed to apply filters: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Validate filter request parameters
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|bool
     */
    private function validateFilterRequest(Request $request)
    {
        try {
            // Validate type
            $type = $request->input('type', 'product');
            if (!in_array($type, ['product', 'service'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid filter type. Must be "product" or "service".',
                    'data' => []
                ], 400);
            }

            // Validate price range
            if ($request->has('min_price') || $request->has('max_price')) {
                $minPrice = $request->input('min_price');
                $maxPrice = $request->input('max_price');

                if ($minPrice !== null && (!is_numeric($minPrice) || $minPrice < 0)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid minimum price. Must be a positive number.',
                        'data' => []
                    ], 400);
                }

                if ($maxPrice !== null && (!is_numeric($maxPrice) || $maxPrice < 0)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid maximum price. Must be a positive number.',
                        'data' => []
                    ], 400);
                }

                if ($minPrice !== null && $maxPrice !== null && $maxPrice < $minPrice) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Maximum price must be greater than or equal to minimum price.',
                        'data' => []
                    ], 400);
                }
            }

            // Validate rating
            if ($request->has('min_rating')) {
                $rating = $request->input('min_rating');
                if ($rating !== null && (!is_numeric($rating) || $rating < 0 || $rating > 5)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid rating. Must be between 0 and 5.',
                        'data' => []
                    ], 400);
                }
            }

            // Validate array parameters
            $arrayParams = ['category_ids', 'color_ids', 'size_ids'];
            foreach ($arrayParams as $param) {
                if ($request->has($param)) {
                    $value = $request->input($param);
                    if ($value !== null && !is_array($value)) {
                        return response()->json([
                            'success' => false,
                            'message' => "Invalid {$param}. Must be an array.",
                            'data' => []
                        ], 400);
                    }
                }
            }

            // Validate date parameters
            if ($request->has('from_date')) {
                $fromDate = $request->input('from_date');
                if ($fromDate !== null) {
                    try {
                        new \DateTime($fromDate);
                    } catch (\Exception $e) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Invalid from_date format. Must be a valid ISO8601 date string.',
                            'data' => []
                        ], 400);
                    }
                }
            }

            if ($request->has('to_date')) {
                $toDate = $request->input('to_date');
                if ($toDate !== null) {
                    try {
                        new \DateTime($toDate);
                    } catch (\Exception $e) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Invalid to_date format. Must be a valid ISO8601 date string.',
                            'data' => []
                        ], 400);
                    }
                }
            }

            // Validate date range logic
            if ($request->has('from_date') && $request->has('to_date')) {
                $fromDate = $request->input('from_date');
                $toDate = $request->input('to_date');

                if ($fromDate !== null && $toDate !== null) {
                    try {
                        $fromDateTime = new \DateTime($fromDate);
                        $toDateTime = new \DateTime($toDate);

                        if ($toDateTime < $fromDateTime) {
                            return response()->json([
                                'success' => false,
                                'message' => 'to_date must be greater than or equal to from_date.',
                                'data' => []
                            ], 400);
                        }
                    } catch (\Exception $e) {
                        // Already handled above
                    }
                }
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error validating filter request: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . $e->getMessage(),
                'data' => []
            ], 400);
        }
    }

    /**
     * Filter products based on the provided criteria
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    private function filterProducts(Request $request)
    {
        Log::info('FilterProducts called with request data', $request->all());

        $query = Product::with([
            'branch',
            'category',
            'colors',
            'sizes',
            'colorSizes.color',
            'colorSizes.size'
        ]);

        // Apply price filter - only if both min and max are provided and valid
        if ($request->filled('min_price') && $request->filled('max_price')) {
            $minPrice = (float) $request->min_price;
            $maxPrice = (float) $request->max_price;

            Log::info('Applying price filter', ['min' => $minPrice, 'max' => $maxPrice]);

            // Validate price range
            if ($minPrice >= 0 && $maxPrice >= 0 && $maxPrice >= $minPrice) {
                $query->filterByPrice($minPrice, $maxPrice);
                Log::info('Price filter applied successfully');
            } else {
                Log::warning('Invalid price range', ['min' => $minPrice, 'max' => $maxPrice]);
            }
        }

        // Apply rating filter - only if provided and valid
        if ($request->filled('min_rating')) {
            $minRating = (float) $request->min_rating;
            if ($minRating >= 0 && $minRating <= 5) {
                $query->filterByRating($minRating);
            }
        }

        // Apply hierarchical category filter
        if ($request->has('category_ids') && is_array($request->category_ids) && !empty($request->category_ids)) {
            $categoryIds = $this->processHierarchicalCategoryFilter($request->category_ids);

            if (!empty($categoryIds)) {
                $query->whereIn('category_id', $categoryIds);
            }
        }

        // Apply color filter - filter by color names instead of product-specific IDs
        if ($request->has('color_ids') && is_array($request->color_ids) && !empty($request->color_ids)) {
            Log::info('Processing color filter', ['raw_color_ids' => $request->color_ids]);

            // Filter out any null or invalid values
            $colorIds = array_filter($request->color_ids, function($id) {
                return is_numeric($id) && $id > 0;
            });

            Log::info('Filtered color IDs', ['valid_color_ids' => $colorIds]);

            if (!empty($colorIds)) {
                // Get color names from the global color list (using fallback color mapping)
                $colorNames = $this->getColorNamesByIds($colorIds);

                if (!empty($colorNames)) {
                    Log::info('Applying color filter with names', ['color_names' => $colorNames]);
                    $query->whereHas('colors', function ($q) use ($colorNames) {
                        $q->whereIn('name', $colorNames);
                    });
                    Log::info('Color filter applied successfully');
                } else {
                    Log::warning('No valid color names found for IDs', ['color_ids' => $colorIds]);
                }
            }
        }

        // Apply size filter - filter by size names instead of product-specific IDs
        if ($request->has('size_ids') && is_array($request->size_ids) && !empty($request->size_ids)) {
            Log::info('Processing size filter', ['raw_size_ids' => $request->size_ids]);

            // Filter out any null or invalid values
            $sizeIds = array_filter($request->size_ids, function($id) {
                return is_numeric($id) && $id > 0;
            });

            Log::info('Filtered size IDs', ['valid_size_ids' => $sizeIds]);

            if (!empty($sizeIds)) {
                // Get size names from the global size list (using fallback size mapping)
                $sizeNames = $this->getSizeNamesByIds($sizeIds);

                if (!empty($sizeNames)) {
                    Log::info('Applying size filter with names', ['size_names' => $sizeNames]);
                    $query->whereHas('sizes', function ($q) use ($sizeNames) {
                        $q->whereIn('name', $sizeNames);
                    });
                    Log::info('Size filter applied successfully');
                } else {
                    Log::warning('No valid size names found for IDs', ['size_ids' => $sizeIds]);
                }
            }
        }

        // Apply featured filter - only if explicitly set to true
        if ($request->has('featured') && $request->boolean('featured') === true) {
            $query->filterByFeatured(true);
        }

        // Apply deals filter - only if explicitly set to true
        if ($request->has('deals') && $request->boolean('deals') === true) {
            $query->filterByActiveDeals();
        }

        // Apply emirate filter - only if provided and not empty
        if ($request->filled('emirate')) {
            $emirate = $request->emirate;
            Log::info('Applying emirate filter', ['emirate' => $emirate]);
            $query->filterByEmirate($emirate);
            Log::info('Emirate filter applied successfully');
        }

        // Apply date filter - only if provided
        if ($request->filled('from_date') || $request->filled('to_date')) {
            $fromDate = $request->from_date;
            $toDate = $request->to_date;

            Log::info('Applying date filter', ['from_date' => $fromDate, 'to_date' => $toDate]);

            if ($fromDate) {
                try {
                    $fromDateTime = new \DateTime($fromDate);
                    $query->whereDate('created_at', '>=', $fromDateTime->format('Y-m-d'));
                    Log::info('From date filter applied', ['from_date' => $fromDateTime->format('Y-m-d')]);
                } catch (\Exception $e) {
                    Log::warning('Invalid from_date format', ['from_date' => $fromDate, 'error' => $e->getMessage()]);
                }
            }

            if ($toDate) {
                try {
                    $toDateTime = new \DateTime($toDate);
                    $query->whereDate('created_at', '<=', $toDateTime->format('Y-m-d'));
                    Log::info('To date filter applied', ['to_date' => $toDateTime->format('Y-m-d')]);
                } catch (\Exception $e) {
                    Log::warning('Invalid to_date format', ['to_date' => $toDate, 'error' => $e->getMessage()]);
                }
            }

            Log::info('Date filter applied successfully');
        }

        // Apply availability filter
        $query->filterByAvailability(true);

        // Apply stock filter
        $query->filterByStock(true);

        // Log the final SQL query for debugging
        Log::info('Final query SQL', ['sql' => $query->toSql(), 'bindings' => $query->getBindings()]);

        // Get paginated results with page parameter support
        $perPage = $request->input('per_page', 40);
        $page = $request->input('page', 1);
        $products = $query->latest()->paginate($perPage, ['*'], 'page', $page);

        $products->getCollection()->transform(function ($product) {
            $dealInfo = $this->productDealService->calculateDiscountedPrice($product);
            $product->has_discount = $dealInfo['has_discount'];
            $product->original_price = $dealInfo['original_price'];
            $product->discounted_price = $dealInfo['discounted_price'];
            $product->discount_percentage = $dealInfo['discount_percentage'];
            $product->discount_amount = $dealInfo['discount_amount'];
            if ($dealInfo['deal']) {
                $product->deal = $dealInfo['deal'];
            }
            return $product;
        });

        Log::info('Products filtered successfully', [
            'total' => $products->total(),
            'per_page' => $products->perPage(),
            'current_page' => $products->currentPage(),
            'returned_product_ids' => $products->pluck('id')->toArray()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Products filtered successfully',
            'data' => $products->items(),
            'pagination' => [
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'has_more_pages' => $products->hasMorePages()
            ]
        ]);
    }

    /**
     * Filter services based on the provided criteria
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    private function filterServices(Request $request)
    {
        $query = Service::with(['branch', 'category']);

        // Apply price filter - only if both min and max are provided and valid
        if ($request->filled('min_price') && $request->filled('max_price')) {
            $minPrice = (float) $request->min_price;
            $maxPrice = (float) $request->max_price;

            // Validate price range
            if ($minPrice >= 0 && $maxPrice >= 0 && $maxPrice >= $minPrice) {
                $query->filterByPrice($minPrice, $maxPrice);
            }
        }

        // Apply duration filter (minutes) - only if both min and max are provided and valid
        if ($request->filled('min_minutes') && $request->filled('max_minutes')) {
            $minMinutes = (int) $request->min_minutes;
            $maxMinutes = (int) $request->max_minutes;

            // Validate duration range
            if ($minMinutes >= 0 && $maxMinutes >= 0 && $maxMinutes >= $minMinutes) {
                $query->filterByDuration($minMinutes, $maxMinutes);
            }
        }

        // Apply rating filter - only if provided and valid
        if ($request->filled('min_rating')) {
            $minRating = (float) $request->min_rating;
            if ($minRating >= 0 && $minRating <= 5) {
                $query->filterByRating($minRating);
            }
        }

        // Apply hierarchical category filter
        if ($request->has('category_ids') && is_array($request->category_ids) && !empty($request->category_ids)) {
            $categoryIds = $this->processHierarchicalCategoryFilter($request->category_ids);

            if (!empty($categoryIds)) {
                $query->whereIn('category_id', $categoryIds);
            }
        }

        // Apply featured filter - only if explicitly set to true
        if ($request->has('featured') && $request->boolean('featured') === true) {
            $query->filterByFeatured(true);
        }

        // Apply home service filter - only if explicitly set to true
        if ($request->has('home_service') && $request->boolean('home_service') === true) {
            $query->filterByHomeService(true);
        }

        // Apply deals filter - only if explicitly set to true
        if ($request->has('deals') && $request->boolean('deals') === true) {
            $query->filterByActiveDeals();
        }

        // Apply emirate filter - only if provided and not empty
        if ($request->filled('emirate')) {
            $emirate = $request->emirate;
            Log::info('Applying emirate filter to services', ['emirate' => $emirate]);
            $query->filterByEmirate($emirate);
            Log::info('Emirate filter applied to services successfully');
        }

        // Apply date filter - only if provided
        if ($request->filled('from_date') || $request->filled('to_date')) {
            $fromDate = $request->from_date;
            $toDate = $request->to_date;

            Log::info('Applying date filter to services', ['from_date' => $fromDate, 'to_date' => $toDate]);

            if ($fromDate) {
                try {
                    $fromDateTime = new \DateTime($fromDate);
                    $query->whereDate('created_at', '>=', $fromDateTime->format('Y-m-d'));
                    Log::info('From date filter applied to services', ['from_date' => $fromDateTime->format('Y-m-d')]);
                } catch (\Exception $e) {
                    Log::warning('Invalid from_date format for services', ['from_date' => $fromDate, 'error' => $e->getMessage()]);
                }
            }

            if ($toDate) {
                try {
                    $toDateTime = new \DateTime($toDate);
                    $query->whereDate('created_at', '<=', $toDateTime->format('Y-m-d'));
                    Log::info('To date filter applied to services', ['to_date' => $toDateTime->format('Y-m-d')]);
                } catch (\Exception $e) {
                    Log::warning('Invalid to_date format for services', ['to_date' => $toDate, 'error' => $e->getMessage()]);
                }
            }

            Log::info('Date filter applied to services successfully');
        }

        // Apply availability filter
        $query->filterByAvailability(true);

        // Get paginated results with page parameter support
        $perPage = $request->input('per_page', 40);
        $page = $request->input('page', 1);
        $services = $query->latest()->paginate($perPage, ['*'], 'page', $page);

        $services->getCollection()->transform(function ($service) {
            $dealInfo = $this->serviceDealService->calculateDiscountedPrice($service);
            $service->has_discount = $dealInfo['has_discount'];
            $service->discounted_price = $dealInfo['discounted_price'];
            $service->discount_percentage = $dealInfo['discount_percentage'];
            $service->discount_amount = $dealInfo['discount_amount'];
            $service->deal = $dealInfo['deal'];
            return $service;
        });

        Log::info('Services filtered successfully', [
            'total' => $services->total(),
            'per_page' => $services->perPage(),
            'current_page' => $services->currentPage()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Services filtered successfully',
            'data' => $services->items(),
            'pagination' => [
                'total' => $services->total(),
                'per_page' => $services->perPage(),
                'current_page' => $services->currentPage(),
                'last_page' => $services->lastPage(),
                'has_more_pages' => $services->hasMorePages()
            ]
        ]);
    }

    /**
     * Get color names by their IDs from the database or fallback mapping
     *
     * @param array $colorIds
     * @return array
     */
    private function getColorNamesByIds(array $colorIds)
    {
        $colorNames = [];

        try {
            // First, try to get color names from the database (for real color variations)
            $databaseColors = \App\Models\ProductColor::whereIn('id', $colorIds)
                ->select('id', 'name')
                ->get()
                ->keyBy('id');

            Log::info('Database color lookup', [
                'requested_ids' => $colorIds,
                'found_colors' => $databaseColors->count()
            ]);

            // Collect color names from database results
            foreach ($colorIds as $id) {
                if ($databaseColors->has($id)) {
                    $colorNames[] = $databaseColors[$id]->name;
                    Log::info("Found database color: ID {$id} -> {$databaseColors[$id]->name}");
                }
            }

            // If we found some colors from database, use those
            if (!empty($colorNames)) {
                Log::info('Using database color names', [
                    'input_ids' => $colorIds,
                    'mapped_names' => $colorNames
                ]);
                return $colorNames;
            }

        } catch (\Exception $e) {
            Log::warning('Database color lookup failed, using fallback', [
                'error' => $e->getMessage()
            ]);
        }

        // Fallback to global color mapping (for basic colors when database lookup fails)
        $globalColors = [
            1 => 'Red',
            2 => 'Blue',
            3 => 'Green',
            4 => 'Yellow',
            5 => 'Black',
            6 => 'White',
            7 => 'Gray',
            8 => 'Pink',
            9 => 'Purple',
            10 => 'Orange',
            11 => 'Brown',
            12 => 'Navy',
        ];

        $colorNames = [];
        foreach ($colorIds as $id) {
            if (isset($globalColors[$id])) {
                $colorNames[] = $globalColors[$id];
            }
        }

        Log::info('Using fallback color mapping', [
            'input_ids' => $colorIds,
            'mapped_names' => $colorNames
        ]);

        return $colorNames;
    }

    /**
     * Get size names by their IDs from the global size mapping
     *
     * @param array $sizeIds
     * @return array
     */
    private function getSizeNamesByIds(array $sizeIds)
    {
        // Query the standardized_sizes table to get the actual size names
        try {
            $standardizedSizes = DB::table('standardized_sizes')
                ->whereIn('id', $sizeIds)
                ->pluck('name', 'id')
                ->toArray();

            $sizeNames = array_values($standardizedSizes);

            Log::info('Size filter mapping from database', [
                'input_ids' => $sizeIds,
                'found_sizes' => $standardizedSizes,
                'mapped_names' => $sizeNames
            ]);

            return $sizeNames;
        } catch (\Exception $e) {
            Log::error('Error querying standardized sizes', [
                'error' => $e->getMessage(),
                'size_ids' => $sizeIds
            ]);

            // Fallback to hardcoded mapping for basic clothing sizes
            $fallbackSizes = [
                1 => 'XXS', 2 => 'XS', 3 => 'S', 4 => 'M', 5 => 'L',
                6 => 'XL', 7 => 'XXL', 8 => '3XL', 9 => '4XL', 10 => '5XL',
            ];

            $sizeNames = [];
            foreach ($sizeIds as $id) {
                if (isset($fallbackSizes[$id])) {
                    $sizeNames[] = $fallbackSizes[$id];
                }
            }

            return $sizeNames;
        }
    }

    /**
     * Process hierarchical category filter logic
     *
     * Core Logic:
     * - If only parent categories are selected, include all their children
     * - If parent + specific subcategories are selected, only include the specific subcategories
     * - If only subcategories are selected, include only those subcategories
     *
     * @param array $selectedCategoryIds
     * @return array
     */
    private function processHierarchicalCategoryFilter(array $selectedCategoryIds)
    {
        // Filter out any null or invalid values
        $categoryIds = array_filter($selectedCategoryIds, function($id) {
            return is_numeric($id) && $id > 0;
        });

        if (empty($categoryIds)) {
            return [];
        }

        Log::info('Processing hierarchical category filter', ['input_ids' => $categoryIds]);

        // Get all categories with their parent-child relationships
        $allCategories = \App\Models\Category::whereIn('id', $categoryIds)
            ->orWhereIn('parent_id', $categoryIds)
            ->get();

        $parentCategories = [];
        $subcategories = [];
        $finalCategoryIds = [];

        // Separate parent categories from subcategories
        foreach ($categoryIds as $categoryId) {
            $category = $allCategories->firstWhere('id', $categoryId);
            if ($category) {
                if ($category->parent_id === null) {
                    // This is a parent category
                    $parentCategories[] = $categoryId;
                } else {
                    // This is a subcategory
                    $subcategories[] = $categoryId;
                }
            }
        }

        Log::info('Categorized selections', [
            'parent_categories' => $parentCategories,
            'subcategories' => $subcategories
        ]);

        // Apply hierarchical logic
        foreach ($parentCategories as $parentId) {
            // Check if any subcategories of this parent are explicitly selected
            $childrenOfThisParent = $allCategories->where('parent_id', $parentId)->pluck('id')->toArray();

            $hasExplicitSubcategories = !empty(array_intersect($childrenOfThisParent, $subcategories));

            if ($hasExplicitSubcategories) {
                // Parent has explicit subcategories selected - don't include parent automatically
                Log::info("Parent category {$parentId} has explicit subcategories selected, skipping auto-inclusion");
            } else {
                // Parent category selected without specific subcategories - include all children
                $finalCategoryIds[] = $parentId;
                $finalCategoryIds = array_merge($finalCategoryIds, $childrenOfThisParent);
                Log::info("Parent category {$parentId} selected without subcategories, including all children", [
                    'children' => $childrenOfThisParent
                ]);
            }
        }

        // Add explicitly selected subcategories
        $finalCategoryIds = array_merge($finalCategoryIds, $subcategories);

        // Remove duplicates and return
        $finalCategoryIds = array_unique($finalCategoryIds);

        Log::info('Final category IDs for filtering', ['final_ids' => $finalCategoryIds]);

        return $finalCategoryIds;
    }

    /**
     * Get search suggestions for autocomplete.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function suggestions(Request $request)
    {
        $query = trim($request->input('query', ''));
        $limit = (int) $request->input('limit', 8);
        $limit = max(1, min($limit, 20));

        if ($query === '') {
            return response()->json([
                'success' => true,
                'suggestions' => [],
            ]);
        }

        $suggestions = collect();
        $perType = min($limit, 10);

        $addSuggestion = function ($value) use (&$suggestions) {
            $trimmed = trim((string) $value);
            if ($trimmed !== '') {
                $suggestions->push($trimmed);
            }
        };

        $products = Product::query()
            ->where('name', 'like', "%{$query}%")
            ->orWhere('product_name_arabic', 'like', "%{$query}%")
            ->limit($perType)
            ->get(['name', 'product_name_arabic']);

        foreach ($products as $product) {
            $addSuggestion($product->name);
            $addSuggestion($product->product_name_arabic);
        }

        $services = Service::query()
            ->where('name', 'like', "%{$query}%")
            ->orWhere('service_name_arabic', 'like', "%{$query}%")
            ->limit($perType)
            ->get(['name', 'service_name_arabic']);

        foreach ($services as $service) {
            $addSuggestion($service->name);
            $addSuggestion($service->service_name_arabic);
        }

        $companies = Company::query()
            ->where('name', 'like', "%{$query}%")
            ->limit($perType)
            ->get(['name']);

        foreach ($companies as $company) {
            $addSuggestion($company->name);
        }

        $merchants = Merchant::query()
            ->where('business_name', 'like', "%{$query}%")
            ->limit($perType)
            ->get(['business_name']);

        foreach ($merchants as $merchant) {
            $addSuggestion($merchant->business_name);
        }

        $unique = $suggestions
            ->unique(function ($value) {
                return mb_strtolower($value);
            })
            ->values()
            ->take($limit)
            ->values();

        return response()->json([
            'success' => true,
            'suggestions' => $unique,
        ]);
    }
}
