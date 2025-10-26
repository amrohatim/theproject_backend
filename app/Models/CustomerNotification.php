<?php

namespace App\Models;

use App\Models\Booking;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerNotification extends Model
{
    use HasFactory;

    const TYPE_ORDER = 'order';
    const TYPE_BOOKING = 'booking';
    const TYPE_ADMIN = 'admin';
    const TYPE_MARKETING = 'marketing';

    const TYPES = [
        self::TYPE_ORDER,
        self::TYPE_BOOKING,
        self::TYPE_ADMIN,
        self::TYPE_MARKETING,
    ];

    protected $fillable = [
        'notification_type',
        'sender_name',
        'message',
        'status',
        'is_opened',
        'order_item_id',
        'booking_id',
        'customer_id',
    ];

    protected $casts = [
        'is_opened' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }
}
