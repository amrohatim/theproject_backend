<?php

namespace App\Http\Controllers\ProductsManager;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\Deal;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the products manager dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        $productsManager = $user->productsManager;

        if (!$productsManager) {
            return redirect('/')->with('error', 'Products manager profile not found.');
        }

        $company = $productsManager->company;

        // Get all products for the company
        $products = Product::whereHas('branch', function ($query) use ($company) {
            $query->where('company_id', $company->id);
        })->with(['branch', 'category'])->get();

        // Get recent orders for company products (with error handling)
        try {
            $recentOrders = collect(); // Default to empty collection for now
        } catch (\Exception $e) {
            $recentOrders = collect();
        }

        // Get statistics (with error handling)
        try {
            $stats = [
                'total_products' => $products->count(),
                'total_branches' => Branch::where('company_id', $company->id)->count(),
                'available_products' => $products->where('status', 'available')->count(),
                'out_of_stock' => $products->where('stock', 0)->count(),
                'low_stock' => $products->where('stock', '>', 0)->where('stock', '<=', 10)->count(),
                'total_orders' => 0, // Default to 0 for now
                'pending_orders' => 0, // Default to 0 for now
                'completed_orders' => 0, // Default to 0 for now
                'total_revenue' => 0, // Default to 0 for now
            ];
        } catch (\Exception $e) {
            $stats = [
                'total_products' => 0,
                'total_branches' => 0,
                'available_products' => 0,
                'out_of_stock' => 0,
                'low_stock' => 0,
                'total_orders' => 0,
                'pending_orders' => 0,
                'completed_orders' => 0,
                'total_revenue' => 0,
            ];
        }

        // Get monthly order statistics for chart (with error handling)
        try {
            $monthlyOrders = collect(); // Default to empty collection for now
        } catch (\Exception $e) {
            $monthlyOrders = collect();
        }

        // Get active deals for company products (with error handling)
        try {
            $activeDeals = collect(); // Default to empty collection for now
        } catch (\Exception $e) {
            $activeDeals = collect();
        }

        // Get top selling products (with error handling)
        try {
            $topProducts = $products->take(5); // Just take first 5 products for now
        } catch (\Exception $e) {
            $topProducts = collect();
        }

        return view('products-manager.dashboard', compact(
            'user',
            'productsManager',
            'company',
            'products',
            'recentOrders',
            'stats',
            'monthlyOrders',
            'activeDeals',
            'topProducts'
        ));
    }

    /**
     * Get dashboard statistics for AJAX requests.
     */
    public function getStats()
    {
        $user = Auth::user();
        $productsManager = $user->productsManager;

        if (!$productsManager) {
            return response()->json(['error' => 'Products manager profile not found'], 404);
        }

        $company = $productsManager->company;

        $products = Product::whereHas('branch', function ($query) use ($company) {
            $query->where('company_id', $company->id);
        })->get();

        $stats = [
            'total_products' => $products->count(),
            'total_branches' => Branch::where('company_id', $company->id)->count(),
            'available_products' => $products->where('status', 'available')->count(),
            'out_of_stock' => $products->where('stock', 0)->count(),
            'low_stock' => $products->where('stock', '>', 0)->where('stock', '<=', 10)->count(),
            'total_orders' => Order::whereHas('orderItems.product.branch', function ($query) use ($company) {
                $query->where('company_id', $company->id);
            })->count(),
            'pending_orders' => Order::whereHas('orderItems.product.branch', function ($query) use ($company) {
                $query->where('company_id', $company->id);
            })->where('status', 'pending')->count(),
            'completed_orders' => Order::whereHas('orderItems.product.branch', function ($query) use ($company) {
                $query->where('company_id', $company->id);
            })->where('status', 'completed')->count(),
            'total_revenue' => Order::whereHas('orderItems.product.branch', function ($query) use ($company) {
                $query->where('company_id', $company->id);
            })->where('status', 'completed')->sum('total_amount'),
        ];

        return response()->json($stats);
    }

    /**
     * Get recent activity for AJAX requests.
     */
    public function getRecentActivity()
    {
        $user = Auth::user();
        $productsManager = $user->productsManager;

        if (!$productsManager) {
            return response()->json(['error' => 'Products manager profile not found'], 404);
        }

        $company = $productsManager->company;

        $recentOrders = Order::whereHas('orderItems.product.branch', function ($query) use ($company) {
            $query->where('company_id', $company->id);
        })->with(['user', 'orderItems.product'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json($recentOrders);
    }
}
