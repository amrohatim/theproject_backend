<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorLocation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'address',
        'emirate',
        'city',
        'latitude',
        'longitude',
        'is_primary',
        'can_deliver_to_vendors',
        'delivery_fees',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_primary' => 'boolean',
        'can_deliver_to_vendors' => 'boolean',
        'delivery_fees' => 'array',
    ];

    /**
     * Get the user that owns the location.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get delivery fee for a specific emirate.
     */
    public function getDeliveryFeeForEmirate(string $emirate): ?float
    {
        if (!$this->delivery_fees || !is_array($this->delivery_fees)) {
            return null;
        }

        return $this->delivery_fees[$emirate] ?? null;
    }

    /**
     * Set as primary location (and unset others).
     */
    public function setAsPrimary(): void
    {
        // First, unset all other primary locations for this user
        static::where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->update(['is_primary' => false]);

        // Then set this one as primary
        $this->update(['is_primary' => true]);
    }

    /**
     * Check if location is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Scope for active locations.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for primary locations.
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Scope for locations that can deliver to vendors.
     */
    public function scopeCanDeliverToVendors($query)
    {
        return $query->where('can_deliver_to_vendors', true);
    }
}
