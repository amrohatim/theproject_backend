<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'awb_number',
        'status',
        'shipment_details',
        'tracking_history',
        'shipped_at',
        'delivered_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'shipment_details' => 'array',
        'tracking_history' => 'array',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    /**
     * Get the order that owns the shipment.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Update the shipment status based on tracking information.
     *
     * @param string $status
     * @return void
     */
    public function updateStatus(string $status)
    {
        $this->status = $status;
        
        if ($status === 'shipped' && !$this->shipped_at) {
            $this->shipped_at = now();
        }
        
        if ($status === 'delivered' && !$this->delivered_at) {
            $this->delivered_at = now();
        }
        
        $this->save();
        
        // Also update the order's shipping status
        if ($this->order) {
            $this->order->shipping_status = $status;
            $this->order->save();
        }
    }
}
