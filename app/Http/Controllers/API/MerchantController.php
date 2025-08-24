<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\Product;
use App\Models\Service;
use App\Models\Deal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MerchantController extends Controller
{
    /**
     * Get all active merchants with basic info
     */
    public function index(Request $request)
    {
        try {
            $query = Merchant::with(['user'])
                ->where('status', 'active');
                // Temporarily removed is_verified requirement for testing
                // ->where('is_verified', true);

            // Filter by emirate if provided
            if ($request->filled('emirate')) {
                $query->where('emirate', $request->emirate);
            }

            // Search by business name
            if ($request->filled('search')) {
                $query->where('business_name', 'like', '%' . $request->search . '%');
            }

            // Temporarily removed product requirement for testing
            // Only show merchants with at least one product
            // $query->whereHas('products', function ($q) {
            //     $q->where('is_available', true);
            // });

            // Order by merchant score and rating
            $query->orderBy('merchant_score', 'desc')
                  ->orderBy('average_rating', 'desc');

            $perPage = min($request->get('per_page', 20), 50);
            $merchants = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $merchants->items(),
                'pagination' => [
                    'current_page' => $merchants->currentPage(),
                    'last_page' => $merchants->lastPage(),
                    'per_page' => $merchants->perPage(),
                    'total' => $merchants->total(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching merchants: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch merchants',
            ], 500);
        }
    }

    /**
     * Get merchant details by ID
     */
    public function show($id, Request $request)
    {
        try {
            $merchant = Merchant::with(['user'])
                ->where('id', $id)
                ->where('status', 'active')
                ->first();

            if (!$merchant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Merchant not found or inactive',
                ], 404);
            }

            // Temporarily removed product requirement for testing
            // Check if merchant has at least one product
            // $hasProducts = $merchant->products()->where('is_available', true)->exists();
            // if (!$hasProducts) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Merchant has no available products',
            //     ], 404);
            // }

            // Note: View count tracking moved to dedicated trackView endpoint
            // This prevents duplicate counting from the same user

            // Get merchant data with additional computed fields
            $merchantData = $merchant->toArray();
            $merchantData['logo_url'] = $merchant->logo ? asset('storage/' . $merchant->logo) : null;
            $merchantData['has_store_location'] = !empty($merchant->store_location_lat) && !empty($merchant->store_location_lng);
            
            return response()->json([
                'success' => true,
                'data' => $merchantData,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching merchant details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch merchant details',
            ], 500);
        }
    }

    /**
     * Get merchant products with pagination
     */
    public function getProducts($id, Request $request)
    {
        try {
            $merchant = Merchant::where('id', $id)
                ->where('status', 'active')
                ->first();

            if (!$merchant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Merchant not found or inactive',
                ], 404);
            }

            $query = Product::with(['category', 'colors', 'sizes'])
                ->where('merchant_id', $id)
                ->where('is_available', true);

            // Filter by category if provided
            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            // Search by product name
            if ($request->filled('search')) {
                $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('product_name_arabic', 'like', '%' . $request->search . '%');
                });
            }

            // Order by featured first, then by display order
            $query->orderBy('featured', 'desc')
                  ->orderBy('display_order', 'asc')
                  ->orderBy('created_at', 'desc');

            $perPage = min($request->get('per_page', 20), 50);
            $products = $query->paginate($perPage);

            // Transform products to include image URLs
            $transformedProducts = $products->getCollection()->map(function ($product) {
                $productArray = $product->toArray();
                $productArray['image_url'] = $product->image ? asset('storage/' . $product->image) : null;
                return $productArray;
            });

            return response()->json([
                'success' => true,
                'data' => $transformedProducts,
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching merchant products: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch merchant products',
            ], 500);
        }
    }

    /**
     * Get merchant services with pagination
     */
    public function getServices($id, Request $request)
    {
        try {
            $merchant = Merchant::where('id', $id)
                ->where('status', 'active')
                ->first();

            if (!$merchant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Merchant not found or inactive',
                ], 404);
            }

            // Query services directly by merchant_id (which references user_id)
            $query = Service::with(['category'])
                ->where('merchant_id', $merchant->user_id)
                ->where('is_available', true);

            // Debug logging
            Log::info("Fetching services for merchant ID: {$id}, user_id: {$merchant->user_id}");

            // Filter by category if provided
            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            // Search by service name
            if ($request->filled('search')) {
                $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('service_name_arabic', 'like', '%' . $request->search . '%');
                });
            }

            // Order by featured first, then by created date
            $query->orderBy('featured', 'desc')
                  ->orderBy('created_at', 'desc');

            $perPage = min($request->get('per_page', 20), 50);
            $services = $query->paginate($perPage);

            // Debug logging
            Log::info("Found {$services->total()} services for merchant user_id: {$merchant->user_id}");

            // Transform services to include image URLs
            $transformedServices = $services->getCollection()->map(function ($service) {
                $serviceArray = $service->toArray();
                $serviceArray['image_url'] = $service->image ? asset('storage/' . $service->image) : null;
                return $serviceArray;
            });

            return response()->json([
                'success' => true,
                'data' => $transformedServices,
                'pagination' => [
                    'current_page' => $services->currentPage(),
                    'last_page' => $services->lastPage(),
                    'per_page' => $services->perPage(),
                    'total' => $services->total(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching merchant services: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch merchant services',
            ], 500);
        }
    }

    /**
     * Get merchant deals
     */
    public function getDeals($id, Request $request)
    {
        try {
            $merchant = Merchant::where('id', $id)
                ->where('status', 'active')
                ->first();

            if (!$merchant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Merchant not found or inactive',
                ], 404);
            }

            // Load deals without relationships for now (products relationship requires deal_product table)
            $query = Deal::where('user_id', $merchant->user_id)
                ->where('status', 'active')
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now());

            // Debug logging
            Log::info("Fetching deals for merchant ID: {$id}, user_id: {$merchant->user_id}");

            // Order by discount percentage and created date (removed featured since it doesn't exist)
            $query->orderBy('discount_percentage', 'desc')
                  ->orderBy('created_at', 'desc');

            $perPage = min($request->get('per_page', 20), 50);
            $deals = $query->paginate($perPage);

            // Debug logging
            Log::info("Found {$deals->total()} deals for merchant user_id: {$merchant->user_id}");

            return response()->json([
                'success' => true,
                'data' => $deals->items(),
                'pagination' => [
                    'current_page' => $deals->currentPage(),
                    'last_page' => $deals->lastPage(),
                    'per_page' => $deals->perPage(),
                    'total' => $deals->total(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching merchant deals: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch merchant deals',
            ], 500);
        }
    }

    /**
     * Track a view for a merchant.
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function trackView($id, Request $request)
    {
        try {
            $merchant = Merchant::findOrFail($id);
            $userId = Auth::id();

            // Log the current view count before any changes
            Log::info("Merchant {$id} view tracking - Initial view count: " .
                (is_null($merchant->view_count) ? 'NULL' : $merchant->view_count));

            // Initialize view_count if it's null or zero
            if ($merchant->view_count === null || $merchant->view_count === 0) {
                $merchant->view_count = 1;
                $merchant->save();
                Log::info("Merchant {$id} view tracking - Initialized view count to 1");
            }

            // Use the view tracking service with duplicate prevention
            $viewTrackingService = app(\App\Services\ViewTrackingService::class);
            $tracked = $viewTrackingService->trackView('merchant', $id, $request);

            // Log whether the view was tracked
            Log::info("Merchant {$id} view tracking - View " . ($tracked ? "tracked" : "not tracked (duplicate)"));

            // Refresh the merchant to get the updated view count
            $merchant->refresh();

            // Log the view count after tracking
            Log::info("Merchant {$id} view tracking - View count after tracking: {$merchant->view_count}");
            return response()->json([
                'success' => true,
                'message' => $tracked ? 'Merchant view tracked successfully' : 'View already tracked recently',
                'tracked' => $tracked,
                'current_view_count' => $merchant->view_count,
                'user_id' => $userId,
            ]);
        } catch (\Exception $e) {
            Log::error("Error tracking merchant view: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());

            // Even if there's an error, try to return the current view count
            $currentCount = 1;
            try {
                $currentCount = Merchant::find($id)->view_count ?? 1;
            } catch (\Exception $innerEx) {
                Log::error("Error getting merchant view count: " . $innerEx->getMessage());
            }

            return response()->json([
                'success' => false,
                'message' => 'Error tracking merchant view',
                'tracked' => false,
                'current_view_count' => $currentCount,
            ], 500);
        }
    }
}
