<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\Helpers\ProviderDashboardHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the provider's orders.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Ensure the provider record exists
        $user = Auth::user();
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

        // If no view exists yet, return to dashboard with a message
        if (!view()->exists('provider.orders.index')) {
            return ProviderDashboardHelper::getDashboardData('Order management is under development');
        }

        // Build the query for orders that contain products from this provider
        $query = Order::whereHas('items.product', function($query) {
                $query->where('user_id', Auth::id());
            })
            ->with(['items.product', 'user'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%");
            })
            ->orWhereHas('user', function ($userQuery) use ($search) {
                $userQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_range')) {
            $dates = explode(' - ', $request->date_range);
            if (count($dates) == 2) {
                $startDate = \Carbon\Carbon::createFromFormat('m/d/Y', $dates[0])->startOfDay();
                $endDate = \Carbon\Carbon::createFromFormat('m/d/Y', $dates[1])->endOfDay();
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
        }

        // Paginate the results
        $orders = $query->paginate(10);

        return view('provider.orders.index', compact('orders'));
    }

    /**
     * Display the specified order.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $user = Auth::user();

        // If no view exists yet, return to dashboard with a message
        if (!view()->exists('provider.orders.show')) {
            return ProviderDashboardHelper::getDashboardData('Order details view is under development');
        }

        // Find the order that contains products from this provider
        $order = Order::whereHas('items.product', function($query) {
                $query->where('user_id', Auth::id());
            })
            ->with(['items.product', 'user', 'shipment'])
            ->findOrFail($id);

        // Get only the items that belong to this provider
        $providerItems = $order->items()
            ->whereHas('product', function($query) {
                $query->where('user_id', Auth::id());
            })
            ->with(['product', 'product.category'])
            ->get();

        // Calculate provider subtotal
        $providerSubtotal = $providerItems->sum(function($item) {
            return $item->price * $item->quantity;
        });

        return view('provider.orders.show', compact('order', 'providerItems', 'providerSubtotal'));
    }

    /**
     * Update the order status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, $id)
    {
        $order = Order::whereHas('items.product', function($query) {
                $query->where('user_id', Auth::id());
            })
            ->findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
        ]);

        $order->status = $request->status;
        $order->save();

        return redirect()->route('provider.orders.show', $order->id)
            ->with('success', 'Order status updated successfully');
    }
}
