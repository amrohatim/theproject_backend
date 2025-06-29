<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Merchant extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'business_name',
        'business_type',
        'registration_number',
        'description',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'emirate',
        'website',
        'logo',
        'status',
        'is_verified',
        'average_rating',
        'total_ratings',
        'view_count',
        'order_count',
        'merchant_score',
        'last_score_calculation',
        'uae_id_front',
        'uae_id_back',
        'store_location_lat',
        'store_location_lng',
        'store_location_address',
        'delivery_capability',
        'delivery_fees',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_verified' => 'boolean',
        'average_rating' => 'decimal:2',
        'total_ratings' => 'integer',
        'view_count' => 'integer',
        'order_count' => 'integer',
        'merchant_score' => 'integer',
        'last_score_calculation' => 'datetime',
        'store_location_lat' => 'decimal:8',
        'store_location_lng' => 'decimal:8',
        'delivery_capability' => 'boolean',
        'delivery_fees' => 'array',
    ];

    /**
     * Get the user that owns the merchant.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the logo attribute with full URL.
     *
     * @param  string|null  $value
     * @return string|null
     */
    public function getLogoAttribute($value)
    {
        return \App\Helpers\ImageHelper::getFullImageUrl($value);
    }

    /**
     * Get the UAE ID front image with full URL.
     *
     * @param  string|null  $value
     * @return string|null
     */
    public function getUaeIdFrontAttribute($value)
    {
        return \App\Helpers\ImageHelper::getFullImageUrl($value);
    }

    /**
     * Get the UAE ID back image with full URL.
     *
     * @param  string|null  $value
     * @return string|null
     */
    public function getUaeIdBackAttribute($value)
    {
        return \App\Helpers\ImageHelper::getFullImageUrl($value);
    }

    /**
     * Check if the merchant is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if the merchant is verified.
     */
    public function isVerified(): bool
    {
        return $this->is_verified;
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
     * Check if merchant can deliver to a specific emirate.
     */
    public function canDeliverToEmirate(string $emirate): bool
    {
        if (!$this->delivery_capability) {
            return false;
        }

        return isset($this->delivery_fees[$emirate]);
    }

    /**
     * Scope for active merchants.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for verified merchants.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope for merchants with delivery capability.
     */
    public function scopeCanDeliver($query)
    {
        return $query->where('delivery_capability', true);
    }

    /**
     * Get the full store location as a formatted string.
     */
    public function getFullStoreLocationAttribute(): ?string
    {
        if (!$this->store_location_address) {
            return null;
        }

        $location = $this->store_location_address;
        
        if ($this->city) {
            $location .= ', ' . $this->city;
        }
        
        if ($this->emirate) {
            $location .= ', ' . $this->emirate;
        }

        return $location;
    }

    /**
     * Check if merchant has store location coordinates.
     */
    public function hasStoreCoordinates(): bool
    {
        return !is_null($this->store_location_lat) && !is_null($this->store_location_lng);
    }

    /**
     * Get store coordinates as array.
     */
    public function getStoreCoordinates(): ?array
    {
        if (!$this->hasStoreCoordinates()) {
            return null;
        }

        return [
            'lat' => (float) $this->store_location_lat,
            'lng' => (float) $this->store_location_lng,
        ];
    }
}
