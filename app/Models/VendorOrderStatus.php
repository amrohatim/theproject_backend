<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorOrderStatus extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'vendor_id',
        'status',
        'notes',
        'updated_by',
    ];

    /**
     * Get the order that owns the vendor status.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the vendor (company) that owns the status.
     */
    public function vendor()
    {
        return $this->belongsTo(Company::class, 'vendor_id');
    }

    /**
     * Get the user who updated the status.
     */
    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
