<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use App\Models\Product;
use App\Models\Category;
use App\Models\Branch;
use App\Services\ProductDealService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DealController extends Controller
{
    protected $dealService;

    public function __construct(ProductDealService $dealService)
    {
        $this->dealService = $dealService;
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
                        // Deals that apply to all products
                        $q->where('applies_to', 'all');

                        // OR deals that apply to specific products that include these products
                        $q->orWhere(function($q2) use ($productIds) {
                            $q2->where('applies_to', 'products')
                               ->whereJsonContains('product_ids', $productIds);
                        });

                        // OR deals that apply to categories of these products
                        $categoryIds = Product::whereIn('id', $productIds)
                                            ->pluck('category_id')
                                            ->unique()
                                            ->toArray();

                        if (!empty($categoryIds)) {
                            $q->orWhere(function($q3) use ($categoryIds) {
                                $q3->where('applies_to', 'categories')
                                   ->whereJsonContains('category_ids', $categoryIds);
                            });
                        }
                    });
                }
            }
        }

        // Filter by product_ids if provided
        if ($request->has('product_ids')) {
            $productIds = explode(',', $request->product_ids);

            // Filter deals that apply to these products
            $query->where(function($q) use ($productIds) {
                // Deals that apply to all products
                $q->where('applies_to', 'all');

                // OR deals that apply to specific products that include these products
                $q->orWhere(function($q2) use ($productIds) {
                    foreach ($productIds as $productId) {
                        $q2->orWhereJsonContains('product_ids', $productId);
                    }
                });

                // OR deals that apply to categories of these products
                $categoryIds = Product::whereIn('id', $productIds)
                                    ->pluck('category_id')
                                    ->unique()
                                    ->toArray();

                if (!empty($categoryIds)) {
                    $q->orWhere(function($q3) use ($categoryIds) {
                        foreach ($categoryIds as $categoryId) {
                            $q3->orWhereJsonContains('category_ids', $categoryId);
                        }
                    });
                }
            });
        }

        // Filter by category_id if provided
        if ($request->has('category_id')) {
            $categoryId = $request->category_id;

            // Filter deals that apply to this category
            $query->where(function($q) use ($categoryId) {
                // Deals that apply to all products
                $q->where('applies_to', 'all');

                // OR deals that apply to this specific category
                $q->orWhere(function($q2) use ($categoryId) {
                    $q2->where('applies_to', 'categories')
                       ->whereJsonContains('category_ids', $categoryId);
                });
            });
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
            'applies_to' => 'required|in:all,products,categories',
            'product_ids' => 'required_if:applies_to,products|array',
            'category_ids' => 'required_if:applies_to,categories|array',
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();

        // Handle product_ids based on applies_to value
        if ($data['applies_to'] === 'all') {
            // Get all product IDs for this vendor
            $productIds = Product::join('branches', 'products.branch_id', '=', 'branches.id')
                ->join('companies', 'branches.company_id', '=', 'companies.id')
                ->where('companies.user_id', Auth::id())
                ->pluck('products.id')
                ->toArray();

            $data['product_ids'] = $productIds;
        } elseif ($data['applies_to'] === 'categories' && isset($data['category_ids'])) {
            // Get all product IDs that belong to the selected categories and are owned by this vendor
            $productIds = Product::join('branches', 'products.branch_id', '=', 'branches.id')
                ->join('companies', 'branches.company_id', '=', 'companies.id')
                ->where('companies.user_id', Auth::id())
                ->whereIn('products.category_id', $data['category_ids'])
                ->pluck('products.id')
                ->toArray();

            $data['product_ids'] = $productIds;
        }

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
            'applies_to' => 'required|in:all,products,categories',
            'product_ids' => 'required_if:applies_to,products|array',
            'category_ids' => 'required_if:applies_to,categories|array',
        ]);

        $data = $request->all();

        // Handle product_ids based on applies_to value
        if ($data['applies_to'] === 'all') {
            // Get all product IDs for this vendor
            $productIds = Product::join('branches', 'products.branch_id', '=', 'branches.id')
                ->join('companies', 'branches.company_id', '=', 'companies.id')
                ->where('companies.user_id', Auth::id())
                ->pluck('products.id')
                ->toArray();

            $data['product_ids'] = $productIds;
        } elseif ($data['applies_to'] === 'categories' && isset($data['category_ids'])) {
            // Get all product IDs that belong to the selected categories and are owned by this vendor
            $productIds = Product::join('branches', 'products.branch_id', '=', 'branches.id')
                ->join('companies', 'branches.company_id', '=', 'companies.id')
                ->where('companies.user_id', Auth::id())
                ->whereIn('products.category_id', $data['category_ids'])
                ->pluck('products.id')
                ->toArray();

            $data['product_ids'] = $productIds;
        }

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

        if ($deal->applies_to === 'all') {
            // Get all products from this vendor
            $products = Product::whereHas('branch.company', function ($query) use ($deal) {
                $query->where('user_id', $deal->user_id);
            })
            ->where('is_available', true)
            ->with(['branch', 'category', 'colors', 'sizes'])
            ->get();
        }
        elseif ($deal->applies_to === 'products' && !empty($deal->product_ids)) {
            // Get specific products
            $productIds = is_string($deal->product_ids)
                ? json_decode($deal->product_ids, true)
                : $deal->product_ids;

            $products = Product::whereIn('id', $productIds)
                ->where('is_available', true)
                ->with(['branch', 'category', 'colors', 'sizes'])
                ->get();
        }
        elseif ($deal->applies_to === 'categories' && !empty($deal->category_ids)) {
            // Get products from specific categories
            $categoryIds = is_string($deal->category_ids)
                ? json_decode($deal->category_ids, true)
                : $deal->category_ids;

            $products = Product::whereIn('category_id', $categoryIds)
                ->whereHas('branch.company', function ($query) use ($deal) {
                    $query->where('user_id', $deal->user_id);
                })
                ->where('is_available', true)
                ->with(['branch', 'category', 'colors', 'sizes'])
                ->get();
        }

        // Apply deal discount to each product
        $products->transform(function ($product) use ($deal) {
            // Calculate discounted price
            $dealInfo = $this->dealService->calculateDiscountedPrice($product);

            // Add branch_name to the product
            $product->branch_name = $product->branch->name;

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
}
