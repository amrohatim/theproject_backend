<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\OrderItemStatusHistory;
use App\Models\VendorOrderStatus;
use App\Models\VendorOrderStatusHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderStatusService
{
    /**
     * Update the status of an order.
     *
     * @param Order $order
     * @param string $status
     * @param string|null $notes
     * @param int|null $userId
     * @return bool
     */
    public function updateOrderStatus(Order $order, string $status, ?string $notes = null, ?int $userId = null)
    {
        try {
            $previousStatus = $order->status;

            // Only update if status is different
            if ($previousStatus !== $status) {
                $order->status = $status;
                $order->save();

                // Record status change in history
                $this->recordOrderStatusHistory($order, $status, $previousStatus, $notes, $userId);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error updating order status: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update the status of an order item.
     *
     * @param OrderItem $orderItem
     * @param string $status
     * @param string|null $notes
     * @param int|null $userId
     * @return bool
     */
    public function updateOrderItemStatus(OrderItem $orderItem, string $status, ?string $notes = null, ?int $userId = null)
    {
        try {
            $previousStatus = $orderItem->status;

            // Only update if status is different
            if ($previousStatus !== $status) {
                $orderItem->status = $status;
                $orderItem->save();

                // Record status change in history
                $this->recordOrderItemStatusHistory($orderItem, $status, $previousStatus, $notes, $userId);

                // Get the order for this item
                $order = $orderItem->order;

                // Log the status change
                Log::info("Order item #{$orderItem->id} status changed from {$previousStatus} to {$status}");

                // Update parent order status based on items
                // We don't call this here because we'll do it after all items are updated in bulk operations
                // $order->updateStatusFromItems();

                // Instead, we'll log that we're skipping the automatic update
                Log::info("Skipping automatic order status update for order #{$order->id} - will be handled after all items are updated");
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error updating order item status: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update the status of all items for a specific vendor in an order.
     *
     * @param Order $order
     * @param int $vendorId
     * @param string $status
     * @param string|null $notes
     * @param int|null $userId
     * @return bool
     */
    public function updateVendorItemsStatus(Order $order, int $vendorId, string $status, ?string $notes = null, ?int $userId = null)
    {
        try {
            $items = $order->getVendorItems($vendorId);
            $itemCount = $items->count();

            Log::info("Updating {$itemCount} items for vendor #{$vendorId} in order #{$order->id} to status: {$status}");

            foreach ($items as $item) {
                $this->updateOrderItemStatus($item, $status, $notes, $userId);
            }

            // After updating all items, explicitly update the order status
            $order->refresh(); // Refresh to get the latest item statuses
            $oldStatus = $order->status;
            $order->updateStatusFromItems();
            $newStatus = $order->status;

            Log::info("After updating all vendor items: Order #{$order->id} status changed from {$oldStatus} to {$newStatus}");

            return true;
        } catch (\Exception $e) {
            Log::error('Error updating vendor items status: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Record order status change in history.
     *
     * @param Order $order
     * @param string $status
     * @param string|null $previousStatus
     * @param string|null $notes
     * @param int|null $userId
     * @return void
     */
    private function recordOrderStatusHistory(Order $order, string $status, ?string $previousStatus, ?string $notes, ?int $userId)
    {
        OrderStatusHistory::create([
            'order_id' => $order->id,
            'status' => $status,
            'previous_status' => $previousStatus,
            'notes' => $notes,
            'updated_by' => $userId ?? Auth::id(),
        ]);
    }

    /**
     * Record order item status change in history.
     *
     * @param OrderItem $orderItem
     * @param string $status
     * @param string|null $previousStatus
     * @param string|null $notes
     * @param int|null $userId
     * @return void
     */
    private function recordOrderItemStatusHistory(OrderItem $orderItem, string $status, ?string $previousStatus, ?string $notes, ?int $userId)
    {
        OrderItemStatusHistory::create([
            'order_item_id' => $orderItem->id,
            'status' => $status,
            'previous_status' => $previousStatus,
            'notes' => $notes,
            'updated_by' => $userId ?? Auth::id(),
        ]);
    }

    /**
     * Update the status for a specific vendor in an order.
     *
     * @param Order $order
     * @param int $vendorId
     * @param string $status
     * @param string|null $notes
     * @param int|null $userId
     * @return bool
     */
    public function updateVendorOrderStatus(Order $order, int $vendorId, string $status, ?string $notes = null, ?int $userId = null)
    {
        try {
            Log::info("Updating vendor order status: Order #{$order->id}, Vendor #{$vendorId}, Status: {$status}");

            // Get or create vendor status record
            $vendorStatus = VendorOrderStatus::firstOrNew([
                'order_id' => $order->id,
                'vendor_id' => $vendorId,
            ]);

            $previousStatus = $vendorStatus->status;
            Log::info("Previous status: " . ($previousStatus ?? 'none') . ", New status: {$status}");

            // Only update if status is different or new record
            if (!$vendorStatus->exists || $previousStatus !== $status) {
                $vendorStatus->status = $status;
                $vendorStatus->notes = $notes;
                $vendorStatus->updated_by = $userId ?? Auth::id();
                $vendorStatus->save();

                Log::info("Vendor status updated successfully: " . ($vendorStatus->exists ? 'Updated existing record' : 'Created new record'));

                // Record status change in history
                $history = $this->recordVendorOrderStatusHistory($order, $vendorId, $status, $previousStatus, $notes, $userId);
                Log::info("Vendor status history recorded: ID #{$history->id}");

                // First, update the items for this vendor to match the vendor status
                // This will trigger individual updateStatusFromItems() calls for each item
                $this->updateVendorItemsStatus($order, $vendorId, $status, $notes, $userId);
                Log::info("Vendor items status updated to match vendor status");

                // Refresh the order to get the latest item statuses
                $order->refresh();

                // Now update parent order status based on all items
                // This is more reliable than using vendor statuses because it looks at the actual item statuses
                $oldOrderStatus = $order->status;
                $order->updateStatusFromItems();
                $newOrderStatus = $order->status;

                Log::info("Order status updated from items: {$oldOrderStatus} -> {$newOrderStatus}");

                // If the status didn't change or we need a more nuanced status based on vendor statuses,
                // also try updating from vendor statuses
                if ($oldOrderStatus === $newOrderStatus) {
                    Log::info("Order status didn't change from items, trying vendor statuses");
                    $order->updateStatusFromVendorStatuses();
                    $finalStatus = $order->status;

                    if ($newOrderStatus !== $finalStatus) {
                        Log::info("Order status updated from vendor statuses: {$newOrderStatus} -> {$finalStatus}");
                    }
                }
            } else {
                Log::info("No status change needed - status already {$status}");
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error updating vendor order status: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return false;
        }
    }

    /**
     * Record vendor order status change in history.
     *
     * @param Order $order
     * @param int $vendorId
     * @param string $status
     * @param string|null $previousStatus
     * @param string|null $notes
     * @param int|null $userId
     * @return \App\Models\VendorOrderStatusHistory
     */
    private function recordVendorOrderStatusHistory(Order $order, int $vendorId, string $status, ?string $previousStatus, ?string $notes, ?int $userId)
    {
        try {
            $history = VendorOrderStatusHistory::create([
                'order_id' => $order->id,
                'vendor_id' => $vendorId,
                'status' => $status,
                'previous_status' => $previousStatus,
                'notes' => $notes,
                'updated_by' => $userId ?? Auth::id(),
            ]);

            Log::info('Recorded vendor order status history: Order #' . $order->id .
                      ', Vendor #' . $vendorId .
                      ', Status: ' . $status .
                      ', Previous: ' . ($previousStatus ?? 'none'));

            return $history;
        } catch (\Exception $e) {
            Log::error('Failed to record vendor order status history: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Initialize vendor statuses for a new order.
     *
     * @param Order $order
     * @return bool
     */
    public function initializeVendorStatuses(Order $order)
    {
        try {
            // Get all unique vendors from order items
            $vendors = $order->getVendors();

            foreach ($vendors as $vendor) {
                // Create initial vendor status record
                VendorOrderStatus::create([
                    'order_id' => $order->id,
                    'vendor_id' => $vendor->id,
                    'status' => 'pending',
                    'notes' => 'Initial status',
                    'updated_by' => Auth::id(),
                ]);

                // Record in history
                $this->recordVendorOrderStatusHistory(
                    $order,
                    $vendor->id,
                    'pending',
                    null,
                    'Initial status',
                    Auth::id()
                );
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error initializing vendor statuses: ' . $e->getMessage());
            return false;
        }
    }
}
