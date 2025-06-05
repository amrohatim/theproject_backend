<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Shipment;
use App\Jobs\CreateAramexShipmentJob;
use Illuminate\Support\Facades\Log;

class ShippingService
{
    /**
     * Assign the appropriate delivery method for an order.
     *
     * @param Order $order
     * @return void
     */
    public function assignDelivery(Order $order)
    {
        try {
            // Check if all vendors in the order can deliver
            $canAllDeliver = $order->canAllVendorsDeliver();

            if ($canAllDeliver) {
                // All vendors can deliver: mark as vendor shipment
                $order->shipping_method = 'vendor';
                $order->shipping_status = 'pending';
                $order->save();
                
                // Notify vendors to fulfill their items
                $this->notifyVendors($order);
            } else {
                // At least one vendor cannot deliver: use Aramex for the whole order
                $order->shipping_method = 'aramex';
                $order->shipping_status = 'pending';
                $order->save();
                
                // Create a shipment record
                $shipment = Shipment::create([
                    'order_id' => $order->id,
                    'status' => 'pending',
                ]);
                
                // Dispatch an async job to create an Aramex shipment
                CreateAramexShipmentJob::dispatch($order);
            }
            
            return true;
        } catch (\Exception $e) {
            Log::error('Error assigning delivery method: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return false;
        }
    }
    
    /**
     * Notify vendors about new orders they need to fulfill.
     *
     * @param Order $order
     * @return void
     */
    protected function notifyVendors(Order $order)
    {
        // Get all vendors for this order
        $vendors = $order->getVendors();
        
        foreach ($vendors as $vendor) {
            // In a real application, you would send notifications to vendors
            // This could be via email, SMS, push notification, etc.
            
            // For now, we'll just log it
            Log::info('Notifying vendor about new order', [
                'order_id' => $order->id,
                'vendor_id' => $vendor->id,
                'vendor_name' => $vendor->name
            ]);
            
            // Example of how you might send an email notification:
            // Mail::to($vendor->email)->send(new NewOrderNotification($order, $vendor));
        }
    }
    
    /**
     * Update the shipping status of an order.
     *
     * @param Order $order
     * @param string $status
     * @return bool
     */
    public function updateShippingStatus(Order $order, string $status)
    {
        try {
            $order->shipping_status = $status;
            $order->save();
            
            // If this is an Aramex shipment, update the shipment record too
            if ($order->shipping_method === 'aramex' && $order->shipment) {
                $order->shipment->status = $status;
                
                if ($status === 'shipped' && !$order->shipment->shipped_at) {
                    $order->shipment->shipped_at = now();
                }
                
                if ($status === 'delivered' && !$order->shipment->delivered_at) {
                    $order->shipment->delivered_at = now();
                }
                
                $order->shipment->save();
            }
            
            return true;
        } catch (\Exception $e) {
            Log::error('Error updating shipping status: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'status' => $status,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
}
