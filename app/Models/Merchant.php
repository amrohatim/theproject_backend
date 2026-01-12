<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'license_file',
        'license_start_date',
        'license_expiry_date',
        'license_status',
        'license_verified',
        'license_rejection_reason',
        'license_uploaded_at',
        'license_approved_at',
        'license_approved_by',
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
        'license_start_date' => 'date',
        'license_expiry_date' => 'date',
        'license_verified' => 'boolean',
        'license_uploaded_at' => 'datetime',
        'license_approved_at' => 'datetime',
    ];

    /**
     * Get the user that owns the merchant.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the products for the merchant.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'merchant_id');
    }

    /**
     * Get the services for the merchant.
     */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'merchant_id', 'user_id');
    }

    /**
     * Get the deals for the merchant.
     */
    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class, 'user_id', 'user_id');
    }

    /**
     * Get the reviews for the merchant.
     */
    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    /**
     * Get the subscriptions for the merchant.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(MerchantSubscription::class);
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

    /**
     * Check if merchant has a valid license.
     */
    public function hasValidLicense(): bool
    {
        return $this->license_verified &&
               $this->license_status === 'verified' &&
               $this->license_expiry_date &&
               $this->license_expiry_date->isFuture();
    }

    /**
     * Check if merchant's license is expired.
     */
    public function isLicenseExpired(): bool
    {
        return $this->license_expiry_date && $this->license_expiry_date->isPast();
    }

    /**
     * Get days until license expiration.
     */
    public function daysUntilLicenseExpiration(): ?int
    {
        if (!$this->license_expiry_date) {
            return null;
        }

        return now()->diffInDays($this->license_expiry_date, false);
    }

    /**
     * Get license status with color coding for UI.
     */
    public function getLicenseStatusWithColor(): array
    {
        $status = $this->license_status;
        $color = match($status) {
            'verified' => 'var(--discord-green)',
            'checking' => 'var(--discord-yellow)',
            'expired' => 'var(--discord-red)',
            'rejected' => 'var(--discord-red)',
            default => 'var(--discord-light)'
        };

        $icon = match($status) {
            'verified' => 'fas fa-check-circle',
            'checking' => 'fas fa-clock',
            'expired' => 'fas fa-times-circle',
            'rejected' => 'fas fa-times-circle',
            default => 'fas fa-question-circle'
        };

        $text = match($status) {
            'verified' => 'Verified',
            'checking' => 'Checking',
            'expired' => 'Expired',
            'rejected' => 'Rejected',
            default => 'Unknown'
        };

        return [
            'status' => $status,
            'color' => $color,
            'icon' => $icon,
            'text' => $text
        ];
    }

    /**
     * Get the license file URL.
     */
    public function getLicenseFileUrlAttribute(): ?string
    {
        if (!$this->license_file) {
            return null;
        }

        return route('merchant.license.view');
    }

    /**
     * Check if merchant can add products/services.
     */
    public function canAddProducts(): bool
    {
        return $this->hasValidLicense();
    }

    /**
     * Get the admin who approved the license.
     */
    public function licenseApprovedBy()
    {
        return $this->belongsTo(User::class, 'license_approved_by');
    }

    /**
     * Scope for merchants with valid licenses.
     */
    public function scopeWithValidLicense($query)
    {
        return $query->where('license_verified', true)
                    ->where('license_status', 'verified')
                    ->where('license_expiry_date', '>', now());
    }

    /**
     * Scope for merchants with expired licenses.
     */
    public function scopeWithExpiredLicense($query)
    {
        return $query->where('license_verified', true)
                    ->where('license_status', 'verified')
                    ->where('license_expiry_date', '<', now());
    }

    /**
     * Scope for merchants with licenses pending review.
     */
    public function scopeWithPendingLicense($query)
    {
        return $query->where('license_status', 'checking');
    }

    /**
     * Get license validation errors.
     */
    public function getLicenseValidationErrors(): array
    {
        $service = app(\App\Services\LicenseManagementService::class);
        return $service->getLicenseValidationErrors($this);
    }

    /**
     * Get license action message for UI.
     */
    public function getLicenseActionMessage(): string
    {
        $service = app(\App\Services\LicenseManagementService::class);
        return $service->getLicenseActionMessage($this);
    }

    /**
     * Check if license needs renewal (within 30 days of expiry).
     */
    public function needsLicenseRenewal(): bool
    {
        if (!$this->license_expiry_date) {
            return false;
        }

        $daysUntilExpiry = $this->daysUntilLicenseExpiration();
        return $daysUntilExpiry !== null && $daysUntilExpiry <= 30 && $daysUntilExpiry >= 0;
    }

    /**
     * Get license renewal urgency level.
     */
    public function getLicenseRenewalUrgency(): string
    {
        if (!$this->license_expiry_date) {
            return 'none';
        }

        $daysUntilExpiry = $this->daysUntilLicenseExpiration();

        if ($daysUntilExpiry === null) {
            return 'none';
        }

        if ($daysUntilExpiry < 0) {
            return 'expired';
        } elseif ($daysUntilExpiry <= 7) {
            return 'critical';
        } elseif ($daysUntilExpiry <= 30) {
            return 'warning';
        }

        return 'normal';
    }
}
