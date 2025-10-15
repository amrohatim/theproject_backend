<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ProviderSubscription extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'subscription_type_id',
        'provider_id',
        'status',
        'start_at',
        'end_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_at' => 'date',
        'end_at' => 'date',
    ];

    /**
     * Get the subscription type that owns the provider subscription.
     */
    public function subscriptionType()
    {
        return $this->belongsTo(SubscriptionType::class);
    }

    /**
     * Get the provider that owns the subscription.
     */
    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    /**
     * Get the formatted start date (DD-MM-YYYY).
     *
     * @return string
     */
    public function getFormattedStartDateAttribute()
    {
        return $this->start_at ? $this->start_at->format('d-m-Y') : '';
    }

    /**
     * Get the formatted end date (DD-MM-YYYY).
     *
     * @return string
     */
    public function getFormattedEndDateAttribute()
    {
        return $this->end_at ? $this->end_at->format('d-m-Y') : '';
    }

    /**
     * Get the days remaining until expiration.
     *
     * @return int
     */
    public function getDaysRemainingAttribute()
    {
        if (!$this->end_at) {
            return 0;
        }
        
        $now = Carbon::now()->startOfDay();
        $endDate = Carbon::parse($this->end_at)->startOfDay();
        
        return max(0, $now->diffInDays($endDate, false));
    }

    /**
     * Check if the subscription is expired.
     *
     * @return bool
     */
    public function isExpired()
    {
        return $this->end_at && Carbon::now()->startOfDay()->gt(Carbon::parse($this->end_at)->startOfDay());
    }

    /**
     * Check if the subscription is expiring soon (within 7 days).
     *
     * @return bool
     */
    public function isExpiringSoon()
    {
        if (!$this->end_at) {
            return false;
        }
        
        $daysRemaining = $this->days_remaining;
        return $daysRemaining > 0 && $daysRemaining <= 7;
    }

    /**
     * Scope a query to only include active subscriptions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                     ->where('end_at', '>=', Carbon::now()->startOfDay());
    }

    /**
     * Scope a query to only include inactive subscriptions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope a query to only include cancelled subscriptions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope a query to only include expired subscriptions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExpired($query)
    {
        return $query->where('end_at', '<', Carbon::now()->startOfDay());
    }

    /**
     * Get the status badge color.
     *
     * @return string
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'active' => 'green',
            'inactive' => 'yellow',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get the status label.
     *
     * @return string
     */
    public function getStatusLabelAttribute()
    {
        return ucfirst($this->status);
    }
}

