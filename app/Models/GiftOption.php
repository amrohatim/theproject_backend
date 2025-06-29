<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GiftOption extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_item_id',
        'is_gift',
        'gift_wrap',
        'gift_wrap_price',
        'gift_wrap_type',
        'gift_message',
        'gift_from',
        'gift_to',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_gift' => 'boolean',
        'gift_wrap' => 'boolean',
        'gift_wrap_price' => 'decimal:2',
    ];

    /**
     * Get the order item that owns this gift option.
     */
    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }
}
