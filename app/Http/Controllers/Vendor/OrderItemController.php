<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Order;
use App\Services\OrderStatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderItemController extends Controller
{
    protected $orderStatusService;

    public function __construct(OrderStatusService $orderStatusService)
    {
        $this->orderStatusService = $orderStatusService;
    }

    /**
     * Show the form for editing the specified order item.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $orderItem = OrderItem::with(['order', 'product'])->findOrFail($id);
        
        // Get the vendor's company ID
        $companyId = Auth::user()->company->id ?? null;
        
        if (!$companyId) {
            return redirect()->route('vendor.orders.index')
                ->with('error', 'You do not have a company associated with your account.');
        }
        
        // Check if the item belongs to this vendor
        if ($orderItem->vendor_id !== $companyId) {
            return redirect()->route('vendor.orders.show', $orderItem->order_id)
                ->with('error', 'You do not have permission to edit this order item.');
        }
        
        return view('vendor.order_items.edit', compact('orderItem'));
    }

    /**
     * Update the specified order item status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, $id)
    {
        $orderItem = OrderItem::findOrFail($id);
        
        // Get the vendor's company ID
        $companyId = Auth::user()->company->id ?? null;
        
        if (!$companyId) {
            return redirect()->route('vendor.orders.index')
                ->with('error', 'You do not have a company associated with your account.');
        }
        
        // Check if the item belongs to this vendor
        if ($orderItem->vendor_id !== $companyId) {
            return redirect()->route('vendor.orders.show', $orderItem->order_id)
                ->with('error', 'You do not have permission to update this order item.');
        }
        
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'notes' => 'nullable|string',
        ]);
        
        // Update the order item status using the service
        $success = $this->orderStatusService->updateOrderItemStatus(
            $orderItem, 
            $request->status, 
            $request->notes, 
            Auth::id()
        );
        
        if ($success) {
            return redirect()->route('vendor.orders.show', $orderItem->order_id)
                ->with('success', 'Order item status updated successfully.');
        } else {
            return redirect()->route('vendor.orders.show', $orderItem->order_id)
                ->with('error', 'Failed to update order item status.');
        }
    }
    
    /**
     * Update the status of all items for a vendor in an order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $orderId
     * @return \Illuminate\Http\Response
     */
    public function updateVendorItemsStatus(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        
        // Get the vendor's company ID
        $companyId = Auth::user()->company->id ?? null;
        
        if (!$companyId) {
            return redirect()->route('vendor.orders.index')
                ->with('error', 'You do not have a company associated with your account.');
        }
        
        // Check if the vendor has any items in this order
        $vendorItemsCount = $order->items()->where('vendor_id', $companyId)->count();
        
        if ($vendorItemsCount === 0) {
            return redirect()->route('vendor.orders.index')
                ->with('error', 'You do not have any products in this order.');
        }
        
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'notes' => 'nullable|string',
        ]);
        
        // Update all vendor items status using the service
        $success = $this->orderStatusService->updateVendorItemsStatus(
            $order,
            $companyId,
            $request->status,
            $request->notes,
            Auth::id()
        );
        
        if ($success) {
            return redirect()->route('vendor.orders.show', $order->id)
                ->with('success', 'All your items in this order have been updated successfully.');
        } else {
            return redirect()->route('vendor.orders.show', $order->id)
                ->with('error', 'Failed to update order items status.');
        }
    }
}
