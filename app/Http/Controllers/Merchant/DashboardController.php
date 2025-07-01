<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Service;
use App\Models\Order;
use App\Models\User;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the merchant dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();

        // Get the merchant record
        $merchant = $user->merchantRecord;

        if (!$merchant) {
            return redirect('/')->with('error', 'Merchant profile not found. Please contact support.');
        }

        // Get merchant statistics
        $totalProducts = Product::where('user_id', $user->id)->count();

        // Services are linked through branches, so we need to get services from user's branches
        $userBranches = \App\Models\Branch::where('user_id', $user->id)->pluck('id');
        $totalServices = Service::whereIn('branch_id', $userBranches)->count();

        // Get recent products (limit to 5)
        $recentProducts = Product::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get recent services (limit to 5)
        $recentServices = Service::whereIn('branch_id', $userBranches)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // For now, we don't have orders and customers models specific to merchants, so we'll set them to 0
        // These can be updated when the order system is implemented
        $totalOrders = 0;
        $totalCustomers = 0;
        $totalReports = 0;

        // Calculate some basic metrics
        $averageRating = $merchant->average_rating ?? 0;
        $totalRatings = $merchant->total_ratings ?? 0;
        $viewCount = $merchant->view_count ?? 0;
        $orderCount = $merchant->order_count ?? 0;

        return view('merchant.dashboard', compact(
            'merchant',
            'totalProducts',
            'totalServices',
            'totalOrders',
            'totalCustomers',
            'totalReports',
            'recentProducts',
            'recentServices',
            'averageRating',
            'totalRatings',
            'viewCount',
            'orderCount'
        ));
    }

    /**
     * Get dashboard statistics for API.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStats()
    {
        $user = Auth::user();
        $merchant = $user->merchantRecord;

        if (!$merchant) {
            return response()->json(['error' => 'Merchant profile not found'], 404);
        }

        // Get user's branches for service counting
        $userBranches = \App\Models\Branch::where('user_id', $user->id)->pluck('id');

        $stats = [
            'products' => Product::where('user_id', $user->id)->count(),
            'services' => Service::whereIn('branch_id', $userBranches)->count(),
            'orders' => 0, // To be implemented
            'customers' => 0, // To be implemented
            'reports' => 0, // To be implemented
            'average_rating' => $merchant->average_rating ?? 0,
            'total_ratings' => $merchant->total_ratings ?? 0,
            'view_count' => $merchant->view_count ?? 0,
            'order_count' => $merchant->order_count ?? 0,
            'merchant_score' => $merchant->merchant_score ?? 0,
        ];

        return response()->json($stats);
    }
}
