<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Shipment;
use App\Services\ShippingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ShipmentController extends Controller
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
     * Get shipment details for an order.
     *
     * @param  int  $orderId
     * @return \Illuminate\Http\Response
     */
    public function getShipmentDetails($orderId)
    {
        try {
            $order = Order::with('shipment')->findOrFail($orderId);
            
            // Check if the user is authorized to view this order
            if (Auth::id() !== $order->user_id && !Auth::user()->isAdmin() && !Auth::user()->isVendor()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 403);
            }
            
            // Get shipment details
            $shipment = $order->shipment;
            
            if (!$shipment) {
                return response()->json([
                    'success' => false,
                    'message' => 'No shipment found for this order',
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'shipment' => [
                    'id' => $shipment->id,
                    'order_id' => $shipment->order_id,
                    'awb_number' => $shipment->awb_number,
                    'status' => $shipment->status,
                    'shipped_at' => $shipment->shipped_at,
                    'delivered_at' => $shipment->delivered_at,
                    'tracking_history' => $shipment->tracking_history,
                ],
                'order' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'shipping_method' => $order->shipping_method,
                    'shipping_status' => $order->shipping_status,
                    'tracking_number' => $order->tracking_number,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting shipment details: ' . $e->getMessage(), [
                'order_id' => $orderId,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get shipment details: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Track a shipment by tracking number.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function trackShipment(Request $request)
    {
        try {
            $request->validate([
                'tracking_number' => 'required|string',
            ]);
            
            $trackingNumber = $request->tracking_number;
            
            // Find the order by tracking number
            $order = Order::where('tracking_number', $trackingNumber)->first();
            
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'No order found with this tracking number',
                ], 404);
            }
            
            // Find the shipment
            $shipment = Shipment::where('order_id', $order->id)
                ->orWhere('awb_number', $trackingNumber)
                ->first();
            
            if (!$shipment) {
                return response()->json([
                    'success' => false,
                    'message' => 'No shipment found with this tracking number',
                ], 404);
            }
            
            // In a real application, you would call the Aramex API to get the latest tracking info
            // For now, we'll just return the stored tracking history
            
            return response()->json([
                'success' => true,
                'tracking_number' => $trackingNumber,
                'status' => $shipment->status,
                'shipped_at' => $shipment->shipped_at,
                'delivered_at' => $shipment->delivered_at,
                'tracking_history' => $shipment->tracking_history ?? [
                    [
                        'timestamp' => now()->subDays(2)->toDateTimeString(),
                        'status' => 'Shipment created',
                        'location' => 'Origin Facility',
                    ],
                    [
                        'timestamp' => now()->subDay()->toDateTimeString(),
                        'status' => 'In transit',
                        'location' => 'Distribution Center',
                    ],
                ],
                'order' => [
                    'order_number' => $order->order_number,
                    'shipping_method' => $order->shipping_method,
                    'shipping_status' => $order->shipping_status,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error tracking shipment: ' . $e->getMessage(), [
                'tracking_number' => $request->tracking_number ?? 'not provided',
                'error' => $e->getMessage(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to track shipment: ' . $e->getMessage(),
            ], 500);
        }
    }
}
