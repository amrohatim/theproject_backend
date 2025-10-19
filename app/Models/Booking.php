<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\UserLocation;

class Booking extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'service_id',
        'branch_id',
        'booking_number',
        'booking_date',
        'booking_time',
        'duration',
        'price',
        'status',
        'payment_status',
        'payment_method',
        'notes',
        'is_home_service',
        'service_location',
        'customer_location',
        'address',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'booking_date' => 'date',
        'price' => 'decimal:2',
        'is_home_service' => 'boolean',
    ];

    /**
     * Get the user that owns the booking.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the service that the booking belongs to.
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the branch that the booking belongs to.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the saved customer location associated with the booking.
     */
    public function customerLocation()
    {
        return $this->belongsTo(UserLocation::class, 'customer_location');
    }
}
