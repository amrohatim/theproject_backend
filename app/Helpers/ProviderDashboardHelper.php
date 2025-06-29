<?php

namespace App\Helpers;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class ProviderDashboardHelper
{
    /**
     * Get dashboard data for the provider.
     *
     * @param string|null $message
     * @return \Illuminate\View\View
     */
    public static function getDashboardData($message = null)
    {
        $user = Auth::user();

        // Get total products
        $totalProducts = Product::where('user_id', $user->id)->count();
        $totalOrders = 0;

        // Create provider record if it doesn't exist
        $provider = $user->providerRecord;
        if (!$provider) {
            $provider = \App\Models\Provider::create([
                'user_id' => $user->id,
                'business_name' => "{$user->name}'s Business",
                'company_name' => "{$user->name}'s Company",
                'status' => 'active',
                'is_verified' => false
            ]);
        }

        // Get recent products
        $recentProducts = Product::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

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

        $data = [
            'totalProducts' => $totalProducts,
            'totalOrders' => $totalOrders,
            'recentProducts' => $recentProducts,
            'recentOrders' => $recentOrders
        ];

        if ($message) {
            $data['message'] = $message;
        }

        return view('provider.dashboard', $data);
    }
}
