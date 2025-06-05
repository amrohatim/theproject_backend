<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Order;
use App\Models\Shipment;
use App\Services\ShippingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ShippingController extends Controller
{
    /**
     * The shipping service instance.
     *
     * @var \App\Services\ShippingService
     */
    protected $shippingService;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Services\ShippingService  $shippingService
     * @return void
     */
    public function __construct(ShippingService $shippingService)
    {
        $this->shippingService = $shippingService;
    }

    /**
     * Display the shipping settings page.
     *
     * @return \Illuminate\Http\Response
     */
    public function settings()
    {
        $user = Auth::user();
        $company = $user->company;

        return view('vendor.shipping.settings', compact('company'));
    }

    /**
     * Update the vendor's shipping settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'can_deliver' => 'required|boolean',
        ]);

        $user = Auth::user();
        $company = $user->company;

        $company->can_deliver = $request->can_deliver;
        $company->save();

        return redirect()->route('vendor.shipping.settings')
            ->with('success', 'Shipping settings updated successfully.');
    }

    /**
     * Display the vendor's orders that need shipping.
     *
     * @return \Illuminate\Http\Response
     */
    public function orders()
    {
        $user = Auth::user();
        $company = $user->company;

        // Get orders where this vendor is responsible for shipping
        $orders = Order::whereHas('items', function ($query) use ($company) {
                $query->where('vendor_id', $company->id);
            })
            ->where('shipping_method', 'vendor')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('vendor.shipping.orders', compact('orders'));
    }

    /**
     * Display the shipping details for an order.
     *
     * @param  int  $orderId
     * @return \Illuminate\Http\Response
     */
    public function orderDetails($orderId)
    {
        $user = Auth::user();
        $company = $user->company;

        // Get the order and verify that this vendor is responsible for it
        $order = Order::whereHas('items', function ($query) use ($company) {
                $query->where('vendor_id', $company->id);
            })
            ->where('id', $orderId)
            ->firstOrFail();

        return view('vendor.shipping.order_details', compact('order'));
    }

    /**
     * Update the shipping status for an order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $orderId
     * @return \Illuminate\Http\Response
     */
    public function updateShippingStatus(Request $request, $orderId)
    {
        $request->validate([
            'shipping_status' => 'required|string|in:pending,processing,shipped,delivered',
        ]);

        $user = Auth::user();
        $company = $user->company;

        // Get the order and verify that this vendor is responsible for it
        $order = Order::whereHas('items', function ($query) use ($company) {
                $query->where('vendor_id', $company->id);
            })
            ->where('id', $orderId)
            ->firstOrFail();

        // Update the shipping status
        $result = $this->shippingService->updateShippingStatus($order, $request->shipping_status);

        if ($result) {
            return redirect()->route('vendor.shipping.orders')
                ->with('success', 'Shipping status updated successfully.');
        } else {
            return redirect()->route('vendor.shipping.orders')
                ->with('error', 'Failed to update shipping status.');
        }
    }

    /**
     * Display the vendor's shipments.
     *
     * @return \Illuminate\Http\Response
     */
    public function shipments()
    {
        $user = Auth::user();
        $company = $user->company;

        // Get shipments for orders where this vendor is responsible
        $shipments = Shipment::whereHas('order.items', function ($query) use ($company) {
                $query->where('vendor_id', $company->id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('vendor.shipping.shipments', compact('shipments'));
    }
}
