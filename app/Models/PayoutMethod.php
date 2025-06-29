<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayoutMethod extends Model
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
        'payout_type',
        'name',
        'bank_name',
        'account_number',
        'last_four',
        'routing_number',
        'account_type',
        'account_holder_name',
        'account_holder_type',
        'currency',
        'country',
        'payout_email',
        'token_id',
        'external_account_id',
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
     * Get the user that owns the payout method.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the transactions for the payout method.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    /**
     * Get the payout preference that uses this payout method as default.
     */
    public function payoutPreference(): HasOne
    {
        return $this->hasOne(PayoutPreference::class, 'default_payout_method_id');
    }

    /**
     * Scope a query to only include bank account payout methods.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBankAccounts($query)
    {
        return $query->where('payout_type', 'bank_account');
    }

    /**
     * Scope a query to only include PayPal payout methods.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePaypal($query)
    {
        return $query->where('payout_type', 'paypal');
    }

    /**
     * Scope a query to only include default payout methods.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope a query to only include verified payout methods.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Get the masked account number.
     *
     * @return string
     */
    public function getMaskedAccountNumberAttribute()
    {
        if ($this->last_four) {
            return '••••••' . $this->last_four;
        }

        return '';
    }

    /**
     * Get the formatted account type.
     *
     * @return string
     */
    public function getFormattedAccountTypeAttribute()
    {
        return ucfirst($this->account_type);
    }
}
