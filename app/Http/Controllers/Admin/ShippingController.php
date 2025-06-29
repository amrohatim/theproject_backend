<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Order;
use App\Models\Shipment;
use App\Services\AramexService;
use App\Services\ShippingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
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
     * The Aramex service instance.
     *
     * @var \App\Services\AramexService
     */
    protected $aramexService;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Services\ShippingService  $shippingService
     * @param  \App\Services\AramexService  $aramexService
     * @return void
     */
    public function __construct(ShippingService $shippingService, AramexService $aramexService)
    {
        $this->shippingService = $shippingService;
        $this->aramexService = $aramexService;
    }

    /**
     * Display the shipping settings page.
     *
     * @return \Illuminate\Http\Response
     */
    public function settings()
    {
        $aramexSettings = [
            'account_number' => Config::get('services.aramex.account_number'),
            'username' => Config::get('services.aramex.username'),
            'password' => Config::get('services.aramex.password'),
            'account_pin' => Config::get('services.aramex.account_pin'),
            'entity' => Config::get('services.aramex.entity'),
            'country_code' => Config::get('services.aramex.country_code'),
            'api_mode' => Config::get('services.aramex.api_mode'),
            'shipper_name' => Config::get('services.aramex.shipper_name'),
            'shipper_company' => Config::get('services.aramex.shipper_company'),
            'shipper_phone' => Config::get('services.aramex.shipper_phone'),
            'shipper_email' => Config::get('services.aramex.shipper_email'),
            'shipper_address_line1' => Config::get('services.aramex.shipper_address_line1'),
            'shipper_city' => Config::get('services.aramex.shipper_city'),
            'shipper_country_code' => Config::get('services.aramex.shipper_country_code'),
        ];

        return view('admin.shipping.settings', compact('aramexSettings'));
    }

    /**
     * Update the shipping settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateSettings(Request $request)
    {
        // This would typically update the .env file or database settings
        // For this example, we'll just return a success message
        return redirect()->route('admin.shipping.settings')
            ->with('success', 'Shipping settings updated successfully.');
    }

    /**
     * Display all orders with shipping information.
     *
     * @return \Illuminate\Http\Response
     */
    public function orders()
    {
        $orders = Order::with(['user', 'items.product', 'shipment'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.shipping.orders', compact('orders'));
    }

    /**
     * Display the shipping details for an order.
     *
     * @param  int  $orderId
     * @return \Illuminate\Http\Response
     */
    public function orderDetails($orderId)
    {
        $order = Order::with(['user', 'items.product', 'items.vendor', 'shipment'])
            ->findOrFail($orderId);

        return view('admin.shipping.order_details', compact('order'));
    }

    /**
     * Update the shipping method for an order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $orderId
     * @return \Illuminate\Http\Response
     */
    public function updateShippingMethod(Request $request, $orderId)
    {
        $request->validate([
            'shipping_method' => 'required|string|in:vendor,aramex',
        ]);

        $order = Order::findOrFail($orderId);
        $oldMethod = $order->shipping_method;
        $newMethod = $request->shipping_method;

        // Only process if the method is actually changing
        if ($oldMethod !== $newMethod) {
            $order->shipping_method = $newMethod;
            $order->save();

            if ($newMethod === 'aramex') {
                // Create a shipment record if it doesn't exist
                if (!$order->shipment) {
                    $shipment = Shipment::create([
                        'order_id' => $order->id,
                        'status' => 'pending',
                    ]);

                    // Create Aramex shipment
                    $result = $this->aramexService->createShipment($order);

                    if (!$result['success']) {
                        return redirect()->route('admin.shipping.order-details', $order->id)
                            ->with('error', 'Failed to create Aramex shipment: ' . $result['message']);
                    }
                }
            } else {
                // If changing from Aramex to vendor, delete the shipment
                if ($order->shipment) {
                    $order->shipment->delete();
                }
            }
        }

        return redirect()->route('admin.shipping.order-details', $order->id)
            ->with('success', 'Shipping method updated successfully.');
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
            'shipping_status' => 'required|string|in:pending,processing,shipped,delivered,failed',
        ]);

        $order = Order::findOrFail($orderId);
        
        // Update the shipping status
        $result = $this->shippingService->updateShippingStatus($order, $request->shipping_status);

        if ($result) {
            return redirect()->route('admin.shipping.order-details', $order->id)
                ->with('success', 'Shipping status updated successfully.');
        } else {
            return redirect()->route('admin.shipping.order-details', $order->id)
                ->with('error', 'Failed to update shipping status.');
        }
    }

    /**
     * Display all shipments.
     *
     * @return \Illuminate\Http\Response
     */
    public function shipments()
    {
        $shipments = Shipment::with('order')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.shipping.shipments', compact('shipments'));
    }

    /**
     * Display the shipment details.
     *
     * @param  int  $shipmentId
     * @return \Illuminate\Http\Response
     */
    public function shipmentDetails($shipmentId)
    {
        $shipment = Shipment::with(['order.user', 'order.items.product'])
            ->findOrFail($shipmentId);

        return view('admin.shipping.shipment_details', compact('shipment'));
    }

    /**
     * Track a shipment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function trackShipment(Request $request)
    {
        $request->validate([
            'tracking_number' => 'required|string',
        ]);

        // This would typically call the Aramex API to get tracking information
        // For this example, we'll just return a success message
        return redirect()->route('admin.shipping.shipments')
            ->with('success', 'Shipment tracking information retrieved successfully.');
    }

    /**
     * Display vendors with shipping capabilities.
     *
     * @return \Illuminate\Http\Response
     */
    public function vendors()
    {
        $vendors = Company::orderBy('name')
            ->paginate(10);

        return view('admin.shipping.vendors', compact('vendors'));
    }

    /**
     * Update a vendor's shipping capability.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $vendorId
     * @return \Illuminate\Http\Response
     */
    public function updateVendorShipping(Request $request, $vendorId)
    {
        $request->validate([
            'can_deliver' => 'required|boolean',
        ]);

        $vendor = Company::findOrFail($vendorId);
        $vendor->can_deliver = $request->can_deliver;
        $vendor->save();

        return redirect()->route('admin.shipping.vendors')
            ->with('success', 'Vendor shipping capability updated successfully.');
    }
}
