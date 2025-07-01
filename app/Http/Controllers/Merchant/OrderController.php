<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of the merchant's orders.
     */
    public function index()
    {
        // TODO: Implement order listing when order system is ready
        $orders = collect([]); // Empty collection for now
        
        return view('merchant.orders.index', compact('orders'));
    }

    /**
     * Display the specified order.
     */
    public function show($id)
    {
        // TODO: Implement order details view
        return view('merchant.orders.show');
    }

    /**
     * Update the status of the specified order.
     */
    public function updateStatus(Request $request, $id)
    {
        // TODO: Implement order status update
        return redirect()->route('merchant.orders.index')
            ->with('success', 'Order status updated successfully.');
    }

    /**
     * Cancel the specified order.
     */
    public function cancel($id)
    {
        // TODO: Implement order cancellation
        return redirect()->route('merchant.orders.index')
            ->with('success', 'Order cancelled successfully.');
    }
}
