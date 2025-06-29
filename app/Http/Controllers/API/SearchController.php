<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
{
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

        // Apply category filter - only if array is provided and not empty
        if ($request->has('category_ids') && is_array($request->category_ids) && !empty($request->category_ids)) {
            // Filter out any null or invalid values
            $categoryIds = array_filter($request->category_ids, function($id) {
                return is_numeric($id) && $id > 0;
            });

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

        // Apply category filter - only if array is provided and not empty
        if ($request->has('category_ids') && is_array($request->category_ids) && !empty($request->category_ids)) {
            // Filter out any null or invalid values
            $categoryIds = array_filter($request->category_ids, function($id) {
                return is_numeric($id) && $id > 0;
            });

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

        // Apply emirate filter - only if provided and not empty
        if ($request->filled('emirate')) {
            $emirate = $request->emirate;
            Log::info('Applying emirate filter to services', ['emirate' => $emirate]);
            $query->filterByEmirate($emirate);
            Log::info('Emirate filter applied to services successfully');
        }

        // Apply availability filter
        $query->filterByAvailability(true);

        // Get paginated results with page parameter support
        $perPage = $request->input('per_page', 40);
        $page = $request->input('page', 1);
        $services = $query->latest()->paginate($perPage, ['*'], 'page', $page);

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
        // Define the global size mapping (matching the Flutter fallback sizes)
        $globalSizes = [
            1 => 'XXS',
            2 => 'XS',
            3 => 'S',
            4 => 'M',
            5 => 'L',
            6 => 'XL',
            7 => 'XXL',
            8 => '3XL',
            9 => '4XL',
            10 => '5XL',
        ];

        $sizeNames = [];
        foreach ($sizeIds as $id) {
            if (isset($globalSizes[$id])) {
                $sizeNames[] = $globalSizes[$id];
            }
        }

        Log::info('Size filter mapping', [
            'input_ids' => $sizeIds,
            'mapped_names' => $sizeNames
        ]);

        return $sizeNames;
    }
}
