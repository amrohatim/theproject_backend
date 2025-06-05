<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentMethod extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'provider_type',
        'payment_type',
        'name',
        'card_brand',
        'last_four',
        'expiry_month',
        'expiry_year',
        'billing_email',
        'billing_address_line1',
        'billing_address_line2',
        'billing_city',
        'billing_state',
        'billing_postal_code',
        'billing_country',
        'token_id',
        'customer_id',
        'is_default',
        'is_verified',
        'verified_at',
        'meta_data',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_default' => 'boolean',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'meta_data' => 'array',
    ];

    /**
     * Get the user that owns the payment method.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the transactions for the payment method.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    /**
     * Scope a query to only include credit card payment methods.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreditCards($query)
    {
        return $query->where('payment_type', 'credit_card');
    }

    /**
     * Scope a query to only include PayPal payment methods.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePaypal($query)
    {
        return $query->where('payment_type', 'paypal');
    }

    /**
     * Scope a query to only include default payment methods.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope a query to only include verified payment methods.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Get the formatted expiration date.
     *
     * @return string
     */
    public function getExpirationDateAttribute()
    {
        if ($this->expiry_month && $this->expiry_year) {
            return $this->expiry_month . '/' . $this->expiry_year;
        }

        return '';
    }

    /**
     * Check if the payment method is expired.
     *
     * @return bool
     */
    public function getIsExpiredAttribute()
    {
        if (!$this->expiry_month || !$this->expiry_year) {
            return false;
        }

        $expiryDate = \Carbon\Carbon::createFromDate($this->expiry_year, $this->expiry_month, 1)->endOfMonth();
        return $expiryDate->isPast();
    }

    /**
     * Get the formatted card brand.
     *
     * @return string
     */
    public function getFormattedCardBrandAttribute()
    {
        return ucfirst($this->card_brand);
    }

    /**
     * Get the masked card number.
     *
     * @return string
     */
    public function getMaskedNumberAttribute()
    {
        if ($this->last_four) {
            return '•••• •••• •••• ' . $this->last_four;
        }

        return '';
    }
}
