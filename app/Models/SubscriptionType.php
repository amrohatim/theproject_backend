<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'period',
        'charge',
        'title',
        'description',
        'alert_message',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'charge' => 'decimal:2',
    ];

    /**
     * Get the formatted charge amount.
     *
     * @return string
     */
    public function getFormattedChargeAttribute()
    {
        return number_format($this->charge, 2) . ' AED';
    }

    /**
     * Get the type label.
     *
     * @return string
     */
    public function getTypeLabelAttribute()
    {
        return ucfirst($this->type);
    }

    /**
     * Get the period label.
     *
     * @return string
     */
    public function getPeriodLabelAttribute()
    {
        return ucfirst($this->period);
    }

    /**
     * Get the vendor subscriptions for this subscription type.
     */
    public function vendorSubscriptions()
    {
        return $this->hasMany(VendorSubscription::class);
    }

    /**
     * Get the merchant subscriptions for this subscription type.
     */
    public function merchantSubscriptions()
    {
        return $this->hasMany(MerchantSubscription::class);
    }

    /**
     * Get the provider subscriptions for this subscription type.
     */
    public function providerSubscriptions()
    {
        return $this->hasMany(ProviderSubscription::class);
    }
}

