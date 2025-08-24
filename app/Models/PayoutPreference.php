<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayoutPreference extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'payout_frequency',
        'minimum_payout_amount',
        'currency',
        'default_payout_method_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'minimum_payout_amount' => 'decimal:2',
    ];

    /**
     * Get the user that owns the payout preference.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the default payout method.
     */
    public function defaultPayoutMethod(): BelongsTo
    {
        return $this->belongsTo(PayoutMethod::class, 'default_payout_method_id');
    }
}
