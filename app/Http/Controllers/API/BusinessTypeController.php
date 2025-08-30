<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BusinessType;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Service;
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

                // Fix image URL construction
                if ($businessType->image) {
                    $imageUrl = $businessType->image;

                    if (!str_starts_with($imageUrl, 'http')) {
                        // Check if the path already starts with 'storage/'
                        if (str_starts_with($imageUrl, 'storage/')) {
                            // Path already includes storage/, just prepend base URL
                            $imageUrl = url($imageUrl);
                        } else {
                            // Path doesn't include storage/, add it
                            $imageUrl = url('storage/' . $imageUrl);
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
            $businessTypes = Branch::select('business_type')
                ->whereNotNull('business_type')
                ->where('business_type', '!=', '')
                ->where('status', 'active')
                ->groupBy('business_type')
                ->orderBy('business_type')
                ->get()
                ->map(function ($branch) {
                    $businessTypeData = [
                        'business_name' => $branch->business_type,
                        'image' => null, // Will be populated from business_types table if exists
                    ];

                    // Try to get image from business_types table
                    $dbBusinessType = BusinessType::where('business_name', $branch->business_type)->first();
                    if ($dbBusinessType && $dbBusinessType->image) {
                        // Construct full URL for the image
                        $imageUrl = $dbBusinessType->image;

                        if (!str_starts_with($imageUrl, 'http')) {
                            // Check if the path already starts with 'storage/'
                            if (str_starts_with($imageUrl, 'storage/')) {
                                // Path already includes storage/, just prepend base URL
                                $imageUrl = url($imageUrl);
                            } else {
                                // Path doesn't include storage/, add it
                                $imageUrl = url('storage/' . $imageUrl);
                            }
                        }

                        $businessTypeData['image'] = $imageUrl;
                        $businessTypeData['id'] = $dbBusinessType->id;
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
}
