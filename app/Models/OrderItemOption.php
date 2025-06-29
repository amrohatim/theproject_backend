<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItemOption extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_item_id',
        'option_type_id',
        'option_value_id',
        'option_name',
        'option_value',
        'price_adjustment',
        'custom_value',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price_adjustment' => 'decimal:2',
    ];

    /**
     * Get the order item that owns this option.
     */
    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    /**
     * Get the option type for this order item option.
     */
    public function optionType()
    {
        return $this->belongsTo(ProductOptionType::class, 'option_type_id');
    }

    /**
     * Get the option value for this order item option.
     */
    public function optionValue()
    {
        return $this->belongsTo(ProductOptionValue::class, 'option_value_id');
    }
}
