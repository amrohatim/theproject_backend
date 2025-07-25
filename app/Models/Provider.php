<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Provider extends Model
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
        'website',
        'logo',
        'delivery_capability',
        'delivery_fees',
        'stock_locations',
        'status',
        'is_verified',
        'average_rating',
        'total_ratings',
        'company_name',
        'contact_email',
        'contact_phone',
        'zip_code',
        'view_count',
        'order_count',
        'provider_score',
        'last_score_calculation',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_verified' => 'boolean',
        'delivery_capability' => 'boolean',
        'delivery_fees' => 'array',
        'stock_locations' => 'array',
    ];

    /**
     * Get the user that owns the provider.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the provider's products.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the provider's product inventory (provider_products).
     */
    public function providerProducts()
    {
        return $this->hasMany(ProviderProduct::class, 'provider_id');
    }

    /**
     * Get the provider's locations.
     */
    public function locations()
    {
        return $this->hasMany(ProviderLocation::class, 'provider_id');
    }

    /**
     * Get the ratings for this provider.
     */
    public function ratings()
    {
        return $this->hasMany(ProviderRating::class, 'provider_id');
    }

    /**
     * Get delivery fee for a specific emirate.
     *
     * @param string $emirate
     * @return float|null
     */
    public function getDeliveryFeeForEmirate(string $emirate): ?float
    {
        if (!$this->delivery_capability || !$this->delivery_fees) {
            return null;
        }

        // Handle different emirate name formats
        $emirateKey = strtolower(str_replace(' ', '_', $emirate));

        return $this->delivery_fees[$emirateKey] ?? null;
    }

    /**
     * Check if provider offers delivery services.
     *
     * @return bool
     */
    public function offersDelivery(): bool
    {
        return (bool) $this->delivery_capability;
    }
}
