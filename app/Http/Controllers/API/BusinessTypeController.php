<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BusinessType;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Service;
use App\Models\Category;
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
     *
     * @return \Illuminate\Http\Response
     */
    public function getFromBranches()
    {
        try {
            // Get unique business types from branches table where business_type is not null
            $uniqueBusinessTypes = Branch::select('business_type')
                ->whereNotNull('business_type')
                ->where('business_type', '!=', '')
                ->where('status', 'active')
                ->groupBy('business_type')
                ->orderBy('business_type')
                ->pluck('business_type');

            $businessTypes = $uniqueBusinessTypes->map(function ($businessTypeName) {
                $businessTypeData = [
                    'business_name' => $businessTypeName,
                    'name_arabic' => null,
                    'image' => null,
                    'id' => null,
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
            });

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

            // Filter by category if provided
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

            // Filter by category if provided
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
     * Get available emirates from branches.
     *
     * @return \Illuminate\Http\Response
     */
    public function getEmirates()
    {
        try {
            $emirates = Branch::select('emirate')
                ->whereNotNull('emirate')
                ->where('emirate', '!=', '')
                ->where('status', 'active')
                ->groupBy('emirate')
                ->orderBy('emirate')
                ->pluck('emirate');

            return response()->json([
                'success' => true,
                'emirates' => $emirates,
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
