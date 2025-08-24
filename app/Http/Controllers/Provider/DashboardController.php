<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\ProviderProduct;
use Illuminate\Support\Facades\Auth;

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

        // Get the provider record first
        $provider = $user->providerRecord;

        if (!$provider) {
            // If no provider record, show empty data
            $totalProducts = 0;
            $recentProducts = collect([]);
        } else {
            // Count products in provider's inventory specifically
            $totalProducts = ProviderProduct::where('provider_id', $provider->id)->count();

            // Get recent products from provider's inventory
            $recentProducts = ProviderProduct::where('provider_id', $provider->id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }
        
        // TODO: Implement real order tracking for providers
        $totalOrders = 0;
        $recentOrders = collect([]);

        return view('provider.dashboard', compact(
            'totalProducts',
            'totalOrders',
            'recentProducts',
            'recentOrders'
        ));
    }
}
