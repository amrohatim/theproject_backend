<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'booking_date' => 'date',
        'price' => 'decimal:2',
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
}
