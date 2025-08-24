<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class License extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'license_type',
        'license_file_path',
        'license_file_name',
        'start_date',
        'end_date',
        'duration_days',
        'status',
        'renewal_date',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'renewal_date' => 'date',
        'duration_days' => 'integer',
    ];

    /**
     * Get the user that owns the license.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the license is expired.
     */
    public function isExpired(): bool
    {
        return $this->end_date < Carbon::now()->toDateString();
    }

    /**
     * Check if the license is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && !$this->isExpired();
    }

    /**
     * Get days until expiration.
     */
    public function daysUntilExpiration(): int
    {
        return Carbon::now()->diffInDays($this->end_date, false);
    }

    /**
     * Get the full file URL.
     */
    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . $this->license_file_path);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically update status when license expires
        static::saving(function ($license) {
            if ($license->isExpired() && $license->status === 'active') {
                $license->status = 'expired';
            }
        });
    }
}
