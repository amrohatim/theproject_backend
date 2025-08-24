<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'branch_id',
        'order_number',
        'total',
        'discount',
        'status',
        'payment_status',
        'payment_method',
        'shipping_address',
        'billing_address',
        'notes',
        'shipping_method',
        'shipping_status',
        'shipping_cost',
        'tracking_number',
        'estimated_delivery',
        'customer_name',
        'customer_phone',
        'shipping_city',
        'shipping_country',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'total' => 'decimal:2',
        'discount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'shipping_address' => 'array',
        'billing_address' => 'array',
        'estimated_delivery' => 'datetime',
    ];

    /**
     * Get the user that owns the order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the branch that the order belongs to.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the order items for the order.
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the items count for the order.
     */
    public function getItemsCountAttribute()
    {
        return $this->items()->count();
    }

    /**
     * Get the formatted estimated delivery date.
     */
    public function getFormattedEstimatedDeliveryAttribute()
    {
        if (!$this->estimated_delivery) {
            return 'Not available';
        }

        return $this->estimated_delivery->format('F j, Y');
    }

    /**
     * Get the shipment for the order.
     */
    public function shipment()
    {
        return $this->hasOne(Shipment::class);
    }

    /**
     * Check if all vendors in this order can deliver.
     *
     * @return bool
     */
    public function canAllVendorsDeliver()
    {
        // Get all unique vendors from order items
        $vendorIds = $this->items()
            ->with(['product.branch.company'])
            ->get()
            ->pluck('product.branch.company.id')
            ->unique()
            ->toArray();

        // Check if all vendors can deliver
        $canAllDeliver = Company::whereIn('id', $vendorIds)
            ->where('can_deliver', false)
            ->count() === 0;

        return $canAllDeliver;
    }

    /**
     * Get all unique vendors for this order.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getVendors()
    {
        return $this->items()
            ->with(['product.branch.company'])
            ->get()
            ->pluck('product.branch.company')
            ->unique('id');
    }

    /**
     * Get the total discount amount for the order.
     *
     * @return float
     */
    public function getTotalDiscountAttribute()
    {
        return $this->discount ?? $this->items()->sum('discount_amount');
    }

    /**
     * Get the original total (before discounts) for the order.
     *
     * @return float
     */
    public function getOriginalTotalAttribute()
    {
        return $this->items()->sum(DB::raw('original_price * quantity'));
    }

    /**
     * Check if this order has any discounts applied.
     *
     * @return bool
     */
    public function hasDiscounts()
    {
        return $this->discount > 0 || $this->items()->where('discount_percentage', '>', 0)->exists();
    }

    /**
     * Get all deals applied to this order.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAppliedDeals()
    {
        $dealIds = $this->items()
            ->whereNotNull('applied_deal_id')
            ->pluck('applied_deal_id')
            ->unique()
            ->toArray();

        return Deal::whereIn('id', $dealIds)->get();
    }

    /**
     * Get the status history for this order.
     */
    public function statusHistory()
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('created_at', 'desc');
    }

    /**
     * Update the order status based on the status of its items.
     * This method should be called after updating an order item's status.
     *
     * @return void
     */
    public function updateStatusFromItems()
    {
        $items = $this->items()->get();
        $itemStatuses = $items->pluck('status')->unique()->toArray();

        // If all items have the same status, set the order status to match
        if (count($itemStatuses) === 1) {
            $this->status = $itemStatuses[0];
            $this->save();
            return;
        }

        // If any item is cancelled, but not all, keep current status
        if (in_array('cancelled', $itemStatuses) && count($itemStatuses) > 1) {
            // Don't change the status
            return;
        }

        // If some items are shipped but not all, set to partially_shipped
        if (in_array('shipped', $itemStatuses) && count($itemStatuses) > 1) {
            $this->status = 'partially_shipped';
            $this->save();
            return;
        }

        // If some items are delivered but not all, keep as partially_shipped
        if (in_array('delivered', $itemStatuses) && count($itemStatuses) > 1) {
            $this->status = 'partially_shipped';
            $this->save();
            return;
        }

        // If all items are delivered, set to delivered
        if (count(array_diff($itemStatuses, ['delivered'])) === 0) {
            $this->status = 'delivered';
            $this->save();
            return;
        }

        // If some items are processing but not all, set to processing
        if (in_array('processing', $itemStatuses)) {
            $this->status = 'processing';
            $this->save();
            return;
        }
    }

    /**
     * Get items for a specific vendor.
     *
     * @param int $vendorId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getVendorItems($vendorId)
    {
        return $this->items()->where('vendor_id', $vendorId)->get();
    }

    /**
     * Check if all items for a specific vendor have the same status.
     *
     * @param int $vendorId
     * @return bool
     */
    public function vendorItemsHaveSameStatus($vendorId)
    {
        $items = $this->getVendorItems($vendorId);
        $statuses = $items->pluck('status')->unique();

        return $statuses->count() === 1;
    }

    /**
     * Get the common status for a vendor's items, or null if mixed.
     *
     * @param int $vendorId
     * @return string|null
     */
    public function getVendorItemsStatus($vendorId)
    {
        $items = $this->getVendorItems($vendorId);
        $statuses = $items->pluck('status')->unique();

        return $statuses->count() === 1 ? $statuses->first() : null;
    }

    /**
     * Get all vendor-specific statuses for this order.
     */
    public function vendorStatuses()
    {
        return $this->hasMany(VendorOrderStatus::class);
    }

    /**
     * Get the status for a specific vendor.
     *
     * @param int $vendorId
     * @return string|null
     */
    public function getVendorStatus($vendorId)
    {
        $vendorStatus = $this->vendorStatuses()->where('vendor_id', $vendorId)->first();
        return $vendorStatus?->status;
    }

    /**
     * Get the vendor status history for this order.
     */
    public function vendorStatusHistory()
    {
        return $this->hasMany(VendorOrderStatusHistory::class)->orderBy('created_at', 'desc');
    }

    /**
     * Alias for vendorStatusHistory to match API response structure.
     */
    public function getVendorStatusHistoryAttribute()
    {
        return $this->vendorStatusHistory()->get();
    }

    /**
     * Update the overall order status based on vendor statuses.
     * This method should be called after updating a vendor's status.
     *
     * @return void
     */
    public function updateStatusFromVendorStatuses()
    {
        $vendorStatuses = $this->vendorStatuses()->get();

        // If no vendor statuses exist yet, don't change anything
        if ($vendorStatuses->isEmpty()) {
            \Log::info("No vendor statuses exist for order #{$this->id}. Keeping current status: {$this->status}");
            return;
        }

        $statusValues = $vendorStatuses->pluck('status')->unique()->toArray();
        $oldStatus = $this->status;

        // Log the current status values for debugging
        \Log::info("Updating order #{$this->id} status from vendor statuses: " . implode(', ', $statusValues));
        \Log::info("Current order status: {$oldStatus}");

        // If all vendors have the same status, set the order status to match
        if (count($statusValues) === 1) {
            $this->status = $statusValues[0];
            $this->save();
            \Log::info("All vendors have same status: {$statusValues[0]}. Setting order status to match.");
            return;
        }

        // If any vendor has cancelled status, but not all, keep current status
        if (in_array('cancelled', $statusValues) && count($statusValues) > 1) {
            // Don't change the status
            \Log::info("Some vendors have cancelled status, but not all. Keeping current status: {$this->status}");
            return;
        }

        // If some vendors have delivered and others have shipped, set to partially_delivered
        if (in_array('delivered', $statusValues) && in_array('shipped', $statusValues)) {
            $this->status = 'partially_delivered';
            $this->save();
            \Log::info('Some vendors delivered, some shipped. Setting status to partially_delivered.');
            return;
        }

        // If some vendors have delivered and others are in earlier stages, set to partially_delivered
        if (in_array('delivered', $statusValues) &&
            (in_array('processing', $statusValues) || in_array('confirmed', $statusValues) || in_array('pending', $statusValues))) {
            $this->status = 'partially_delivered';
            $this->save();
            \Log::info('Some vendors delivered, others in earlier stages. Setting status to partially_delivered.');
            return;
        }

        // If some vendors have shipped and others are in earlier stages, set to partially_shipped
        if (in_array('shipped', $statusValues) &&
            (in_array('processing', $statusValues) || in_array('confirmed', $statusValues) || in_array('pending', $statusValues))) {
            $this->status = 'partially_shipped';
            $this->save();
            \Log::info('Some vendors shipped, others in earlier stages. Setting status to partially_shipped.');
            return;
        }

        // If all vendors have delivered, set to delivered
        if (count(array_diff($statusValues, ['delivered'])) === 0) {
            $this->status = 'delivered';
            $this->save();
            \Log::info('All vendors have delivered. Setting status to delivered.');
            return;
        }

        // If all vendors have shipped, set to shipped
        if (count(array_diff($statusValues, ['shipped'])) === 0) {
            $this->status = 'shipped';
            $this->save();
            \Log::info('All vendors have shipped. Setting status to shipped.');
            return;
        }

        // If some vendors are processing but not all, set to processing
        if (in_array('processing', $statusValues)) {
            $this->status = 'processing';
            $this->save();
            \Log::info('Some vendors are processing. Setting status to processing.');
            return;
        }

        // If some vendors are confirmed but not all, set to confirmed
        if (in_array('confirmed', $statusValues)) {
            $this->status = 'confirmed';
            $this->save();
            \Log::info('Some vendors are confirmed. Setting status to confirmed.');
            return;
        }

        // Default fallback - if we get here, keep the current status
        \Log::info("No specific status rule matched. Keeping current status: {$this->status}");

        // Log the final status change
        if ($oldStatus !== $this->status) {
            \Log::info("Order #{$this->id} status changed from {$oldStatus} to {$this->status}");
        } else {
            \Log::info("Order #{$this->id} status remained as {$this->status}");
        }
    }
}
