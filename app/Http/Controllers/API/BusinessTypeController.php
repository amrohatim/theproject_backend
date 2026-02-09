<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BusinessType;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Service;
use App\Models\Category;
use App\Models\Merchant;
use Illuminate\Http\Request;

class BusinessTypeController extends Controller
{
    /**
     * Get all business types.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $businessTypes = BusinessType::orderBy('business_name')->get()->map(function ($businessType) {
                $data = $businessType->toArray();

                // Fix image URL construction with robust path handling
                if ($businessType->image) {
                    $imageUrl = $businessType->image;

                    if (!str_starts_with($imageUrl, 'http')) {
                        // Clean up the path to avoid duplication
                        $cleanPath = ltrim($imageUrl, '/'); // Remove leading slash if present

                        // Check if the path already contains 'storage/'
                        if (str_starts_with($cleanPath, 'storage/')) {
                            // Path already includes storage/, just prepend base URL
                            $imageUrl = url($cleanPath);
                        } else {
                            // Path doesn't include storage/, add it
                            $imageUrl = url('storage/' . $cleanPath);
                        }
                    }

                    $data['image'] = $imageUrl;
                }

                return $data;
            });

            return response()->json([
                'success' => true,
                'business_types' => $businessTypes,
                'message' => 'Business types retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve business types',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get unique business types from branches table.
     * Only returns business types that have at least one active branch.
     *
     * @return \Illuminate\Http\Response
     */
    public function getFromBranches()
    {
        try {
            // Get unique business types from branches table where business_type is not null
            // Also count the number of branches for each business type
            $businessTypesWithCounts = Branch::select('business_type')
                ->selectRaw('COUNT(*) as branch_count')
                ->whereNotNull('business_type')
                ->where('business_type', '!=', '')
                ->where('status', 'active')
                ->groupBy('business_type')
                ->orderBy('business_type')
                ->get();

            // Filter out business types with zero branches and map to the expected format
            $businessTypes = $businessTypesWithCounts
                ->filter(function ($item) {
                    // Only include business types with at least one branch
                    return $item->branch_count > 0;
                })
                ->map(function ($item) {
                    $businessTypeName = $item->business_type;
                    $branchCount = $item->branch_count;

                    $businessTypeData = [
                        'business_name' => $businessTypeName,
                        'name_arabic' => null,
                        'image' => null,
                        'id' => null,
                        'branch_count' => $branchCount,
                    ];

                    // Try to get image and Arabic name from business_types table
                    $dbBusinessType = BusinessType::where('business_name', $businessTypeName)->first();
                    if ($dbBusinessType) {
                        $businessTypeData['id'] = $dbBusinessType->id;
                        $businessTypeData['name_arabic'] = $dbBusinessType->name_arabic;

                        if ($dbBusinessType->image) {
                            // Construct full URL for the image with robust path handling
                            $imageUrl = $dbBusinessType->image;

                            if (!str_starts_with($imageUrl, 'http')) {
                                // Clean up the path to avoid duplication
                                $cleanPath = ltrim($imageUrl, '/'); // Remove leading slash if present

                                // Check if the path already contains 'storage/'
                                if (str_starts_with($cleanPath, 'storage/')) {
                                    // Path already includes storage/, just prepend base URL
                                    $imageUrl = url($cleanPath);
                                } else {
                                    // Path doesn't include storage/, add it
                                    $imageUrl = url('storage/' . $cleanPath);
                                }
                            }

                            $businessTypeData['image'] = $imageUrl;
                            $businessTypeData['original_image_path'] = $dbBusinessType->image; // For debugging
                        }
                    }

                    return $businessTypeData;
                })
                ->values(); // Reset array keys

            return response()->json([
                'success' => true,
                'business_types' => $businessTypes,
                'message' => 'Business types from branches retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve business types from branches',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get branches filtered by business type.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getBranches(Request $request)
    {
        try {
            $businessType = $request->input('business_type');
            $emirate = $request->input('emirate');
            $limit = $request->input('limit', 20);
            $page = $request->input('page', 1);

            $query = Branch::with(['company'])
                ->where('status', 'active');

            // Filter by business type if provided
            if ($businessType && $businessType !== 'all') {
                $query->where('business_type', $businessType);
            }

            // Filter by emirate if provided
            if ($emirate && $emirate !== 'all') {
                $query->where('emirate', $emirate);
            }

            // Order by popularity and rating
            $query->orderBy('popularity_score', 'desc')
                  ->orderBy('rating', 'desc')
                  ->orderBy('created_at', 'desc');

            $branches = $query->paginate($limit, ['*'], 'page', $page);

            return response()->json([
                'success' => true,
                'branches' => $branches->items(),
                'pagination' => [
                    'current_page' => $branches->currentPage(),
                    'last_page' => $branches->lastPage(),
                    'per_page' => $branches->perPage(),
                    'total' => $branches->total(),
                    'has_more' => $branches->hasMorePages(),
                ],
                'message' => 'Branches retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve branches',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get business type scoped search suggestions for products/services.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function suggestions(Request $request)
    {
        $businessType = $request->input('business_type');
        $query = trim($request->input('query', ''));
        $limit = (int) $request->input('limit', 10);
        $limit = max(1, min($limit, 20));
        $type = strtolower($request->input('type', 'both'));

        if ($query === '') {
            return response()->json([
                'success' => true,
                'suggestions' => [],
            ]);
        }

        $branchQuery = Branch::query()->where('status', 'active');
        if (!empty($businessType) && $businessType !== 'all') {
            $normalizedType = mb_strtolower(trim($businessType));
            $branchQuery->whereRaw('LOWER(TRIM(business_type)) = ?', [$normalizedType]);
        }
        $branchIds = $branchQuery->pluck('id');
        if ($branchIds->isEmpty()) {
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

        if ($type === 'product' || $type === 'both') {
            $products = Product::query()
                ->whereIn('branch_id', $branchIds)
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                        ->orWhere('product_name_arabic', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%")
                        ->orWhere('product_description_arabic', 'like', "%{$query}%");
                })
                ->limit($perType)
                ->get(['name', 'product_name_arabic']);

            foreach ($products as $product) {
                $addSuggestion($product->name);
                $addSuggestion($product->product_name_arabic);
            }
        }

        if ($type === 'service' || $type === 'both') {
            $services = Service::query()
                ->whereIn('branch_id', $branchIds)
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                        ->orWhere('service_name_arabic', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%")
                        ->orWhere('service_description_arabic', 'like', "%{$query}%");
                })
                ->limit($perType)
                ->get(['name', 'service_name_arabic']);

            foreach ($services as $service) {
                $addSuggestion($service->name);
                $addSuggestion($service->service_name_arabic);
            }
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

    /**
     * Get products filtered by business type.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getProducts(Request $request)
    {
        try {
            $businessType = $request->input('business_type');
            $emirate = $request->input('emirate');
            $categoryId = $request->input('category_id');
            $sortBy = $request->input('sort_by', 'latest'); // latest, price_low, price_high, rating
            $limit = $request->input('limit', 20);
            $page = $request->input('page', 1);
            $merchantOnly = $request->boolean('merchant_only', false); // New parameter for merchant products

            // Base query - if merchant_only is true, filter for merchant products
            if ($merchantOnly) {
                // Filter for products with merchant_id NOT NULL and branch_id IS NULL
                $query = Product::with(['category'])
                    ->whereNotNull('merchant_id')
                    ->whereNull('branch_id')
                    ->where('is_available', true);

                // Filter by emirate for merchant products (using merchant's emirate)
                if ($emirate && $emirate !== 'all') {
                    $query->whereHas('merchant', function ($q) use ($emirate) {
                        $q->where('emirate', $emirate);
                    });
                }
            } else {
                // Original logic for branch products
                $query = Product::with(['branch', 'branch.company', 'category'])
                    ->whereHas('branch', function ($q) {
                        $q->where('status', 'active');
                    });

                // Filter by business type if provided
                if ($businessType && $businessType !== 'all') {
                    $query->whereHas('branch', function ($q) use ($businessType) {
                        $q->where('business_type', $businessType);
                    });
                }

                // Filter by emirate if provided
                if ($emirate && $emirate !== 'all') {
                    $query->whereHas('branch', function ($q) use ($emirate) {
                        $q->where('emirate', $emirate);
                    });
                }
            }

            // Filter by category if provided (applies to both merchant and branch products)
            if ($categoryId) {
                $query->where('category_id', $categoryId);
            }

            // Apply sorting
            switch ($sortBy) {
                case 'price_low':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_high':
                    $query->orderBy('price', 'desc');
                    break;
                case 'rating':
                    $query->orderBy('rating', 'desc');
                    break;
                case 'latest':
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }

            $products = $query->paginate($limit, ['*'], 'page', $page);

            return response()->json([
                'success' => true,
                'products' => $products->items(),
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                    'has_more' => $products->hasMorePages(),
                ],
                'message' => 'Products retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve products',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get services filtered by business type.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getServices(Request $request)
    {
        try {
            $businessType = $request->input('business_type');
            $emirate = $request->input('emirate');
            $categoryId = $request->input('category_id');
            $sortBy = $request->input('sort_by', 'latest'); // latest, price_low, price_high, rating
            $limit = $request->input('limit', 20);
            $page = $request->input('page', 1);
            $merchantOnly = $request->boolean('merchant_only', false); // New parameter for merchant services

            // Base query - if merchant_only is true, filter for merchant services
            if ($merchantOnly) {
                // Filter for services with merchant_id NOT NULL and branch_id IS NULL
                $query = Service::with(['category'])
                    ->whereNotNull('merchant_id')
                    ->whereNull('branch_id')
                    ->where('is_available', true);

                // Filter by emirate for merchant services
                // merchant_id in services table is actually user_id, so we need to join through merchants table
                if ($emirate && $emirate !== 'all') {
                    $query->whereHas('merchant.merchantRecord', function ($q) use ($emirate) {
                        $q->where('emirate', $emirate);
                    });
                }
            } else {
                // Original logic for branch services
                $query = Service::with(['branch', 'branch.company', 'category'])
                    ->whereHas('branch', function ($q) {
                        $q->where('status', 'active');
                    });

                // Filter by business type if provided
                if ($businessType && $businessType !== 'all') {
                    $query->whereHas('branch', function ($q) use ($businessType) {
                        $q->where('business_type', $businessType);
                    });
                }

                // Filter by emirate if provided
                if ($emirate && $emirate !== 'all') {
                    $query->whereHas('branch', function ($q) use ($emirate) {
                        $q->where('emirate', $emirate);
                    });
                }
            }

            // Filter by category if provided (applies to both merchant and branch services)
            if ($categoryId) {
                $query->where('category_id', $categoryId);
            }

            // Apply sorting
            switch ($sortBy) {
                case 'price_low':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_high':
                    $query->orderBy('price', 'desc');
                    break;
                case 'rating':
                    $query->orderBy('rating', 'desc');
                    break;
                case 'latest':
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }

            $services = $query->paginate($limit, ['*'], 'page', $page);

            return response()->json([
                'success' => true,
                'services' => $services->items(),
                'pagination' => [
                    'current_page' => $services->currentPage(),
                    'last_page' => $services->lastPage(),
                    'per_page' => $services->perPage(),
                    'total' => $services->total(),
                    'has_more' => $services->hasMorePages(),
                ],
                'message' => 'Services retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve services',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get available emirates from branches or merchants.
     * Optionally filter by business type.
     * Use 'source=merchants' to get emirates from merchants instead of branches.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getEmirates(Request $request)
    {
        try {
            $businessType = $request->input('business_type');
            $source = $request->input('source', 'branches'); // 'branches' or 'merchants'

            if ($source === 'merchants') {
                // Get emirates from merchants table
                $query = Merchant::select('emirate')
                    ->whereNotNull('emirate')
                    ->where('emirate', '!=', '')
                    ->where('status', 'active');

                // Filter by business type if provided
                if ($businessType && $businessType !== 'all') {
                    $query->where('business_type', $businessType);
                }

                $emirates = $query->groupBy('emirate')
                    ->orderBy('emirate')
                    ->pluck('emirate');
            } else {
                // Get emirates from branches table (original logic)
                $query = Branch::select('emirate')
                    ->whereNotNull('emirate')
                    ->where('emirate', '!=', '')
                    ->where('status', 'active');

                // Filter by business type if provided
                if ($businessType && $businessType !== 'all') {
                    $query->where('business_type', $businessType);
                }

                $emirates = $query->groupBy('emirate')
                    ->orderBy('emirate')
                    ->pluck('emirate');
            }

            return response()->json([
                'success' => true,
                'emirates' => $emirates,
                'source' => $source,
                'business_type' => $businessType,
                'message' => 'Emirates retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve emirates',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get categories for a specific business type.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getCategories(Request $request)
    {
        try {
            $businessTypeName = $request->input('business_type');
            $type = $request->input('type', 'both'); // 'product', 'service', or 'both'

            if (!$businessTypeName) {
                return response()->json([
                    'success' => false,
                    'message' => 'Business type is required',
                ], 400);
            }

            // Find the business type
            $businessType = BusinessType::where('business_name', $businessTypeName)->first();

            if (!$businessType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Business type not found',
                ], 404);
            }

            $categories = collect();

            // Get product categories if requested
            if (($type === 'product' || $type === 'both') && $businessType->product_categories) {
                $productCategories = Category::whereIn('id', $businessType->product_categories)
                    ->where('type', 'product')
                    ->where('is_active', true)
                    ->whereHas('products', function ($query) {
                        $query->where('is_available', true);
                    })
                    ->withCount(['products' => function ($query) {
                        $query->where('is_available', true);
                    }])
                    ->get()
                    ->map(function ($category) {
                        $data = $category->toArray();

                        // Fix image URL construction
                        if ($category->image) {
                            $imageUrl = $category->image;
                            if (!str_starts_with($imageUrl, 'http')) {
                                $cleanPath = ltrim($imageUrl, '/');
                                if (str_starts_with($cleanPath, 'storage/')) {
                                    $imageUrl = url($cleanPath);
                                } else {
                                    $imageUrl = url('storage/' . $cleanPath);
                                }
                            }
                            $data['image'] = $imageUrl;
                        }

                        return $data;
                    });

                $categories = $categories->merge($productCategories);
            }

            // Get service categories if requested
            if (($type === 'service' || $type === 'both') && $businessType->service_categories) {
                $serviceCategories = Category::whereIn('id', $businessType->service_categories)
                    ->where('type', 'service')
                    ->where('is_active', true)
                    ->whereHas('services', function ($query) {
                        $query->where('is_available', true);
                    })
                    ->withCount(['services' => function ($query) {
                        $query->where('is_available', true);
                    }])
                    ->get()
                    ->map(function ($category) {
                        $data = $category->toArray();

                        // Fix image URL construction
                        if ($category->image) {
                            $imageUrl = $category->image;
                            if (!str_starts_with($imageUrl, 'http')) {
                                $cleanPath = ltrim($imageUrl, '/');
                                if (str_starts_with($cleanPath, 'storage/')) {
                                    $imageUrl = url($cleanPath);
                                } else {
                                    $imageUrl = url('storage/' . $cleanPath);
                                }
                            }
                            $data['image'] = $imageUrl;
                        }

                        return $data;
                    });

                $categories = $categories->merge($serviceCategories);
            }

            // Remove duplicates and sort by name
            $categories = $categories->unique('id')->sortBy('name')->values();

            return response()->json([
                'success' => true,
                'categories' => $categories,
                'business_type' => $businessTypeName,
                'type' => $type,
                'message' => 'Categories retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve categories',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
