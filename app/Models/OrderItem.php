<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'vendor_id',
        'quantity',
        'price',
        'original_price',
        'discount_percentage',
        'discount_amount',
        'total',
        'applied_deal_id',
        'status',
        'specifications',
        'color_id',
        'color_name',
        'color_value',
        'color_image',
        'size_id',
        'size_name',
        'size_value',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'specifications' => 'json',
    ];

    /**
     * Get the order that owns the item.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the product that the item belongs to.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the vendor (company) that the item belongs to.
     */
    public function vendor()
    {
        return $this->belongsTo(Company::class, 'vendor_id');
    }

    /**
     * Get the deal that was applied to this item.
     */
    public function deal()
    {
        return $this->belongsTo(Deal::class, 'applied_deal_id');
    }

    /**
     * Check if this item has a discount applied.
     *
     * @return bool
     */
    public function hasDiscount()
    {
        return $this->discount_percentage > 0;
    }

    /**
     * Get the discount amount for this item.
     *
     * @return float
     */
    public function getDiscountAmount()
    {
        return $this->discount_amount;
    }

    /**
     * Get the original price (before discount) for this item.
     *
     * @return float
     */
    public function getOriginalTotal()
    {
        return $this->original_price * $this->quantity;
    }

    /**
     * Get the options for this order item.
     */
    public function options()
    {
        return $this->hasMany(OrderItemOption::class);
    }

    /**
     * Get the gift options for this order item.
     */
    public function giftOption()
    {
        return $this->hasOne(GiftOption::class);
    }

    /**
     * Calculate the total price adjustment from options.
     *
     * @return float
     */
    public function getOptionsAdjustment()
    {
        return $this->options()->sum('price_adjustment') * $this->quantity;
    }

    /**
     * Get the total price including options.
     *
     * @return float
     */
    public function getTotalWithOptions()
    {
        return $this->total + $this->getOptionsAdjustment();
    }

    /**
     * Get the status history for this order item.
     */
    public function statusHistory()
    {
        return $this->hasMany(OrderItemStatusHistory::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get the selected color for this order item.
     */
    public function color()
    {
        return $this->belongsTo(ProductColor::class, 'color_id');
    }

    /**
     * Get the selected size for this order item.
     */
    public function size()
    {
        return $this->belongsTo(ProductSize::class, 'size_id');
    }

    /**
     * Get the specifications as a formatted array for display.
     *
     * @return array
     */
    public function getFormattedSpecifications()
    {
        if (empty($this->specifications)) {
            return [];
        }

        return $this->specifications;
    }

    /**
     * Check if this item has color information.
     *
     * @return bool
     */
    public function hasColorInfo()
    {
        return !empty($this->color_name);
    }

    /**
     * Check if this item has size information.
     *
     * @return bool
     */
    public function hasSizeInfo()
    {
        return !empty($this->size_name);
    }
}
