<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use App\Models\Product;
use App\Models\Service;
use App\Models\Category;
use App\Models\Branch;
use App\Services\ProductDealService;
use App\Services\ServiceDealService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DealController extends Controller
{
    protected $dealService;
    protected $serviceDealService;

    public function __construct(ProductDealService $dealService, ServiceDealService $serviceDealService)
    {
        $this->dealService = $dealService;
        $this->serviceDealService = $serviceDealService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Start with a base query
        $query = Deal::query();

        // If vendor_id is provided, get deals for that vendor
        if ($request->has('vendor_id')) {
            $query->where('user_id', $request->vendor_id);
        }
        // If status=active is provided, get only active deals
        elseif ($request->has('status') && $request->status === 'active') {
            $query = Deal::active();
        }
        // Otherwise, get all deals for the authenticated user (if authenticated)
        elseif (Auth::check()) {
            $query->where('user_id', Auth::id());
        }

        // Filter by branch_id if provided
        if ($request->has('branch_id')) {
            $branchId = $request->branch_id;

            // Get the branch
            $branch = Branch::find($branchId);
            if ($branch) {
                // Get the company (vendor) ID for this branch
                $companyId = $branch->company_id;

                // Get the vendor user ID for this company
                $vendorId = $branch->company->user_id;

                // Filter deals by this vendor
                $query->where('user_id', $vendorId);

                // If product_ids is not provided, get all products from this branch
                if (!$request->has('product_ids')) {
                    // Get all product IDs from this branch
                    $productIds = Product::where('branch_id', $branchId)->pluck('id')->toArray();

                    // Filter deals that apply to these products
                    $query->where(function($q) use ($productIds) {
                        // Deals that apply to specific products that include these products
                        $q->where(function($q2) use ($productIds) {
                            $q2->where('applies_to', 'products')
                               ->whereJsonContains('product_ids', $productIds);
                        });
                    });
                }
            }
        }

        // Filter by product_ids if provided
        if ($request->has('product_ids')) {
            $productIds = explode(',', $request->product_ids);

            // Filter deals that apply to these products
            $query->where(function($q) use ($productIds) {
                // Deals that apply to specific products that include these products
                $q->where(function($q2) use ($productIds) {
                    foreach ($productIds as $productId) {
                        $q2->orWhereJsonContains('product_ids', $productId);
                    }
                });
            });
        }

        // Filter by category_id if provided - get products from that category and filter by product deals
        if ($request->has('category_id')) {
            $categoryId = $request->category_id;

            // Get product IDs from this category
            $categoryProductIds = Product::where('category_id', $categoryId)
                                        ->pluck('id')
                                        ->toArray();

            if (!empty($categoryProductIds)) {
                // Filter deals that apply to products in this category
                $query->where(function($q) use ($categoryProductIds) {
                    $q->where('applies_to', 'products')
                      ->where(function($q2) use ($categoryProductIds) {
                          foreach ($categoryProductIds as $productId) {
                              $q2->orWhereJsonContains('product_ids', $productId);
                          }
                      });
                });
            }
        }

        // Order by creation date (newest first)
        $query->orderBy('created_at', 'desc');

        // Get the deals
        $deals = $query->get();

        return response()->json([
            'success' => true,
            'deals' => $deals
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'promotional_message' => 'nullable|string|max:50',
            'discount_percentage' => 'required|numeric|min:1|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'image' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'applies_to' => 'required|in:products,services,products_and_services',
            'product_ids' => 'required_if:applies_to,products|required_if:applies_to,products_and_services|array',
            'service_ids' => 'required_if:applies_to,services|required_if:applies_to,products_and_services|array',
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();

        // Ensure only the relevant IDs are set based on applies_to value
        if ($data['applies_to'] === 'products') {
            // Clear service_ids for product-only deals
            $data['service_ids'] = null;
        } elseif ($data['applies_to'] === 'services') {
            // Clear product_ids for service-only deals
            $data['product_ids'] = null;
        }
        // For 'products_and_services', keep both arrays as provided

        // Create the deal
        $deal = Deal::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Deal created successfully',
            'deal' => $deal
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $deal = Deal::findOrFail($id);

        return response()->json([
            'success' => true,
            'deal' => $deal
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $deal = Deal::where('user_id', Auth::id())
            ->findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'promotional_message' => 'nullable|string|max:50',
            'discount_percentage' => 'required|numeric|min:1|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'image' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'applies_to' => 'required|in:products,services,products_and_services',
            'product_ids' => 'required_if:applies_to,products|required_if:applies_to,products_and_services|array',
            'service_ids' => 'required_if:applies_to,services|required_if:applies_to,products_and_services|array',
        ]);

        $data = $request->all();

        // Ensure only the relevant IDs are set based on applies_to value
        if ($data['applies_to'] === 'products') {
            // Clear service_ids for product-only deals
            $data['service_ids'] = null;
        } elseif ($data['applies_to'] === 'services') {
            // Clear product_ids for service-only deals
            $data['product_ids'] = null;
        }
        // For 'products_and_services', keep both arrays as provided

        // Update the deal
        $deal->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Deal updated successfully',
            'deal' => $deal
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deal = Deal::where('user_id', Auth::id())
            ->findOrFail($id);

        $deal->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deal deleted successfully'
        ]);
    }

    /**
     * Get products for a specific deal.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getProducts($id)
    {
        // Find the deal
        $deal = Deal::findOrFail($id);

        // Get products based on the deal's applies_to value
        $products = collect();

        if (($deal->applies_to === 'products' || $deal->applies_to === 'products_and_services') && !empty($deal->product_ids)) {
            // Get specific products
            $productIds = is_string($deal->product_ids)
                ? json_decode($deal->product_ids, true)
                : $deal->product_ids;

            $products = Product::whereIn('id', $productIds)
                ->where('is_available', true)
                ->with(['branch', 'category', 'colors', 'sizes'])
                ->get();
        }

        // Apply deal discount to each product
        $products->transform(function ($product) use ($deal) {
            // Calculate discounted price
            $dealInfo = $this->dealService->calculateDiscountedPrice($product);

            // Add branch_name to the product
            $product->branch_name = $product->branch ? $product->branch->name : null;

            // Apply the calculated discount information to the product
            $product->deal = $deal;
            $product->discount_percentage = $dealInfo['discount_percentage'];
            $product->discounted_price = $dealInfo['discounted_price'];
            $product->original_price = $dealInfo['original_price'];
            $product->has_discount = $dealInfo['has_discount'];

            return $product;
        });

        return response()->json([
            'success' => true,
            'deal' => $deal,
            'products' => $products
        ]);
    }

    /**
     * Get services for a specific deal.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getServices($id)
    {
        // Find the deal
        $deal = Deal::findOrFail($id);

        // Get services based on the deal's applies_to value
        $services = collect();

        if (($deal->applies_to === 'services' || $deal->applies_to === 'products_and_services') && !empty($deal->service_ids)) {
            // Get specific services
            $serviceIds = is_string($deal->service_ids)
                ? json_decode($deal->service_ids, true)
                : $deal->service_ids;

            $services = Service::whereIn('id', $serviceIds)
                ->where('is_available', true)
                ->with(['branch', 'category'])
                ->get();
        }

        // Apply deal discount to each service
        $services->transform(function ($service) use ($deal) {
            // Calculate discounted price
            $dealInfo = $this->serviceDealService->calculateDiscountedPrice($service);

            // Add branch_name to the service
            $service->branch_name = $service->branch ? $service->branch->name : null;

            // Apply the calculated discount information to the service
            $service->deal = $deal;
            $service->discount_percentage = $dealInfo['discount_percentage'];
            $service->discounted_price = $dealInfo['discounted_price'];
            $service->original_price = $dealInfo['original_price'];
            $service->has_discount = $dealInfo['has_discount'];

            return $service;
        });

        return response()->json([
            'success' => true,
            'deal' => $deal,
            'services' => $services
        ]);
    }

    /**
     * Get all active deals (public endpoint).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getActiveDeals(Request $request)
    {
        try {
            // Get all active deals using the Deal model's active scope
            $query = Deal::active();

            // Filter by branch_id if provided
            if ($request->has('branch_id')) {
                $branchId = $request->branch_id;
                
                // Get the branch
                $branch = Branch::find($branchId);
                if ($branch) {
                    // Get the vendor user ID for this company
                    $vendorId = $branch->company->user_id;
                    
                    // Filter deals by this vendor
                    $query->where('user_id', $vendorId);
                }
            }

            // Filter by product_ids if provided
            if ($request->has('product_ids')) {
                $productIds = explode(',', $request->product_ids);
                
                // Filter deals that apply to these products
                $query->where(function($q) use ($productIds) {
                    $q->where('applies_to', 'products')
                      ->where(function($q2) use ($productIds) {
                          foreach ($productIds as $productId) {
                              $q2->orWhereJsonContains('product_ids', $productId);
                          }
                      });
                });
            }

            // Filter by category_id if provided
            if ($request->has('category_id')) {
                $categoryId = $request->category_id;
                
                // Get product IDs from this category
                $categoryProductIds = Product::where('category_id', $categoryId)
                                            ->pluck('id')
                                            ->toArray();

                if (!empty($categoryProductIds)) {
                    // Filter deals that apply to products in this category
                    $query->where(function($q) use ($categoryProductIds) {
                        $q->where('applies_to', 'products')
                          ->where(function($q2) use ($categoryProductIds) {
                              foreach ($categoryProductIds as $productId) {
                                  $q2->orWhereJsonContains('product_ids', $productId);
                              }
                          });
                    });
                }
            }

            // Filter by business_type if provided
            if ($request->has('business_type')) {
                $businessType = $request->business_type;

                $productIds = Product::whereHas('branch', function ($q) use ($businessType) {
                    $q->where('business_type', $businessType);
                })->pluck('id')->toArray();

                $serviceIds = Service::whereHas('branch', function ($q) use ($businessType) {
                    $q->where('business_type', $businessType);
                })->pluck('id')->toArray();

                if (empty($productIds) && empty($serviceIds)) {
                    $query->whereRaw('1 = 0');
                } else {
                    $query->where(function ($q) use ($productIds, $serviceIds) {
                        if (!empty($productIds)) {
                            $q->where(function ($q2) use ($productIds) {
                                $q2->whereIn('applies_to', ['products', 'products_and_services'])
                                   ->where(function ($q3) use ($productIds) {
                                       foreach ($productIds as $productId) {
                                           $q3->orWhereJsonContains('product_ids', $productId);
                                       }
                                   });
                            });
                        }

                        if (!empty($serviceIds)) {
                            $q->orWhere(function ($q2) use ($serviceIds) {
                                $q2->whereIn('applies_to', ['services', 'products_and_services'])
                                   ->where(function ($q3) use ($serviceIds) {
                                       foreach ($serviceIds as $serviceId) {
                                           $q3->orWhereJsonContains('service_ids', $serviceId);
                                       }
                                   });
                            });
                        }
                    });
                }
            }

            // Order by creation date (newest first)
            $query->orderBy('created_at', 'desc');

            // Get the deals
            $deals = $query->get();

            return response()->json([
                'success' => true,
                'deals' => $deals,
                'message' => 'Active deals retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching active deals: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch active deals',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get analytics for a specific deal.
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getAnalytics($id, Request $request)
    {
        try {
            $deal = Deal::where('user_id', Auth::id())
                ->findOrFail($id);

            // For now, return basic analytics
            // This can be expanded with actual analytics data
            $analytics = [
                'deal_id' => $deal->id,
                'title' => $deal->title,
                'views' => 0, // Placeholder
                'clicks' => 0, // Placeholder
                'conversions' => 0, // Placeholder
                'revenue' => 0, // Placeholder
            ];

            return response()->json([
                'success' => true,
                'analytics' => $analytics
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching deal analytics: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch deal analytics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get analytics for all deals.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getAllAnalytics(Request $request)
    {
        try {
            $deals = Deal::where('user_id', Auth::id())->get();

            $analytics = $deals->map(function ($deal) {
                return [
                    'deal_id' => $deal->id,
                    'title' => $deal->title,
                    'views' => 0, // Placeholder
                    'clicks' => 0, // Placeholder
                    'conversions' => 0, // Placeholder
                    'revenue' => 0, // Placeholder
                ];
            });

            return response()->json([
                'success' => true,
                'analytics' => $analytics
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching all deals analytics: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch deals analytics',
                'error' => $e->getMessage()
            ], 500);
                }
            }
        }

        // Filter by business_type if provided - match deals tied to products/services in that business type
        if ($request->has('business_type')) {
            $businessType = $request->business_type;

            $productIds = Product::whereHas('branch', function ($q) use ($businessType) {
                $q->where('business_type', $businessType);
            })->pluck('id')->toArray();

            $serviceIds = Service::whereHas('branch', function ($q) use ($businessType) {
                $q->where('business_type', $businessType);
            })->pluck('id')->toArray();

            if (empty($productIds) && empty($serviceIds)) {
                $query->whereRaw('1 = 0');
            } else {
                $query->where(function ($q) use ($productIds, $serviceIds) {
                    if (!empty($productIds)) {
                        $q->where(function ($q2) use ($productIds) {
                            $q2->whereIn('applies_to', ['products', 'products_and_services'])
                               ->where(function ($q3) use ($productIds) {
                                   foreach ($productIds as $productId) {
                                       $q3->orWhereJsonContains('product_ids', $productId);
                                   }
                               });
                        });
                    }

                    if (!empty($serviceIds)) {
                        $q->orWhere(function ($q2) use ($serviceIds) {
                            $q2->whereIn('applies_to', ['services', 'products_and_services'])
                               ->where(function ($q3) use ($serviceIds) {
                                   foreach ($serviceIds as $serviceId) {
                                       $q3->orWhereJsonContains('service_ids', $serviceId);
                                   }
                               });
                        });
                    }
                });
            }
        }
