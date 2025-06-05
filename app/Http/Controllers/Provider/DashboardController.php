<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProviderProduct;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the provider dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();

        // Get the provider profile first
        $providerProfile = $user->providerProfile;
        
        if (!$providerProfile) {
            // If no provider profile, show empty data
            $totalProducts = 0;
            $recentProducts = collect([]);
        } else {
            // Count products in provider's inventory specifically
            $totalProducts = ProviderProduct::where('provider_id', $providerProfile->id)->count();
            
            // Get recent products from provider's inventory
            $recentProducts = ProviderProduct::where('provider_id', $providerProfile->id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }
        
        $totalOrders = 0;

        // Create demo orders
        $recentOrders = collect([
            (object)[
                'id' => 1,
                'order_number' => 'ORD-12345',
                'total' => 45.99,
                'status' => 'completed',
                'created_at' => now()->subDays(1),
                'customer_name' => 'John Doe'
            ],
            (object)[
                'id' => 2,
                'order_number' => 'ORD-12346',
                'total' => 32.50,
                'status' => 'processing',
                'created_at' => now()->subDays(2),
                'customer_name' => 'Jane Smith'
            ],
            (object)[
                'id' => 3,
                'order_number' => 'ORD-12347',
                'total' => 18.75,
                'status' => 'pending',
                'created_at' => now()->subDays(3),
                'customer_name' => 'Robert Johnson'
            ]
        ]);

        return view('provider.dashboard', compact(
            'totalProducts',
            'totalOrders',
            'recentProducts',
            'recentOrders'
        ));
    }
}
