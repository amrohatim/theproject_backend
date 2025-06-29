<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics for admin.
     *
     * @return \Illuminate\Http\Response
     */
    public function adminStats()
    {
        // Ensure user is admin
        if (Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }

        $stats = [
            'total_users' => User::count(),
            'total_vendors' => User::where('role', 'vendor')->count(),
            'total_customers' => User::where('role', 'customer')->count(),
            'total_products' => Product::count(),
            'total_services' => Service::count(),
            'total_categories' => Category::count(),
            'total_companies' => Company::count(),
            'total_branches' => Branch::count(),
            'recent_users' => User::latest()->take(5)->get(),
            'recent_products' => Product::with('branch')->latest()->take(5)->get(),
            'recent_services' => Service::with('branch')->latest()->take(5)->get(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats,
        ]);
    }

    /**
     * Get dashboard statistics for vendor.
     *
     * @return \Illuminate\Http\Response
     */
    public function vendorStats()
    {
        // Ensure user is vendor
        if (!Auth::user()->isVendor()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }

        $userId = Auth::id();

        $stats = [
            'total_branches' => Branch::where('user_id', $userId)->count(),
            'total_products' => Product::whereHas('branch', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->count(),
            'total_services' => Service::whereHas('branch', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->count(),
            'company' => Company::where('user_id', $userId)->first(),
            'recent_products' => Product::whereHas('branch', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->with('branch')->latest()->take(5)->get(),
            'recent_services' => Service::whereHas('branch', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->with('branch')->latest()->take(5)->get(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats,
        ]);
    }

    /**
     * Get all featured items for the homepage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function featuredItems(Request $request)
    {
        $limit = $request->input('limit', 5);

        // Get featured products
        $products = Product::with([
            'branch',
            'category',
            'colors',
            'sizes',
            'colorSizes.color',
            'colorSizes.size'
        ])
            ->where('featured', true)
            ->where('is_available', true)
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();

        // Add branch_name and color-size combinations to each product
        $products->transform(function ($product) {
            $product->branch_name = $product->branch->name;

            // Add color-size combinations data
            $colorSizeCombinations = [];
            foreach ($product->colorSizes as $colorSize) {
                $colorSizeCombinations[] = [
                    'id' => $colorSize->id,
                    'product_id' => $colorSize->product_id,
                    'color_id' => $colorSize->product_color_id,
                    'size_id' => $colorSize->product_size_id,
                    'color_name' => $colorSize->color->name,
                    'color_code' => $colorSize->color->color_code,
                    'size_name' => $colorSize->size->name,
                    'size_value' => $colorSize->size->value,
                    'stock' => $colorSize->stock,
                    'price_adjustment' => $colorSize->price_adjustment,
                    'is_available' => $colorSize->is_available,
                ];
            }

            // Add the color-size combinations to the product
            $product->color_size_combinations = $colorSizeCombinations;

            return $product;
        });

        // Get featured services
        $services = Service::with(['branch', 'category'])
            ->where('featured', true)
            ->where('is_available', true)
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();

        // Add branch_name to each service
        $services->transform(function ($service) {
            $service->branch_name = $service->branch->name;
            return $service;
        });

        // Get featured branches
        $branches = Branch::with(['company'])
            ->where('featured', true)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();

        return response()->json([
            'success' => true,
            'featured' => [
                'products' => $products,
                'services' => $services,
                'branches' => $branches,
            ],
        ]);
    }
}
