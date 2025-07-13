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
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        $user = Auth::user();

        // Check if user needs to upload license (additional safety check)
        if ($user->registration_step === 'phone_verified') {
            return redirect()->route('merchant.license.upload')
                ->with('info', 'Please upload your business license to complete registration and access your dashboard.');
        }

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

    /**
     * Global search across products and services.
     */
    public function globalSearch(Request $request)
    {
        $user = Auth::user();
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([
                'success' => true,
                'results' => []
            ]);
        }

        // Search products
        $products = Product::where('user_id', $user->id)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%");
            })
            ->with('category')
            ->select('id', 'name', 'sku', 'image', 'price', 'category_id', 'is_available')
            ->limit(10)
            ->get();

        // Search services
        $userBranches = \App\Models\Branch::where('user_id', $user->id)->pluck('id');
        $services = Service::whereIn('branch_id', $userBranches)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->with('category')
            ->select('id', 'name', 'image', 'price', 'duration', 'category_id', 'is_available')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'results' => [
                'products' => $products->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'sku' => $product->sku,
                        'price' => $product->price,
                        'image' => $product->image,
                        'category' => $product->category->name ?? 'Uncategorized',
                        'status' => $product->is_available ? 'Active' : 'Inactive',
                        'type' => 'product',
                        'url' => route('merchant.products.show', $product->id)
                    ];
                }),
                'services' => $services->map(function ($service) {
                    return [
                        'id' => $service->id,
                        'name' => $service->name,
                        'price' => $service->price,
                        'duration' => $service->duration,
                        'image' => $service->image,
                        'category' => $service->category->name ?? 'Uncategorized',
                        'status' => $service->is_available ? 'Active' : 'Inactive',
                        'type' => 'service',
                        'url' => route('merchant.services.show', $service->id)
                    ];
                })
            ]
        ]);
    }

    /**
     * Get search suggestions for global search.
     */
    public function searchSuggestions(Request $request)
    {
        $user = Auth::user();
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([
                'success' => true,
                'suggestions' => []
            ]);
        }

        // Get recent searches from session
        $recentSearches = session('recent_searches', []);
        $matchingRecent = array_filter($recentSearches, function($search) use ($query) {
            return stripos($search, $query) !== false;
        });

        // Get product suggestions
        $products = Product::where('user_id', $user->id)
            ->where('name', 'like', "%{$query}%")
            ->select('name')
            ->distinct()
            ->limit(5)
            ->pluck('name');

        // Get service suggestions
        $userBranches = \App\Models\Branch::where('user_id', $user->id)->pluck('id');
        $services = Service::whereIn('branch_id', $userBranches)
            ->where('name', 'like', "%{$query}%")
            ->select('name')
            ->distinct()
            ->limit(5)
            ->pluck('name');

        // Get category suggestions
        $categories = \App\Models\Category::where('name', 'like', "%{$query}%")
            ->where('is_active', true)
            ->select('name')
            ->distinct()
            ->limit(3)
            ->pluck('name');

        $suggestions = collect($matchingRecent)
            ->merge($products)
            ->merge($services)
            ->merge($categories)
            ->unique()
            ->take(10)
            ->values();

        return response()->json([
            'success' => true,
            'suggestions' => $suggestions
        ]);
    }

    /**
     * Save search query to recent searches.
     */
    public function saveSearch(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) >= 2) {
            $recentSearches = session('recent_searches', []);

            // Remove if already exists
            $recentSearches = array_filter($recentSearches, function($search) use ($query) {
                return $search !== $query;
            });

            // Add to beginning
            array_unshift($recentSearches, $query);

            // Keep only last 10 searches
            $recentSearches = array_slice($recentSearches, 0, 10);

            session(['recent_searches' => $recentSearches]);
        }

        return response()->json(['success' => true]);
    }
}
