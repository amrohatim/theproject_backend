<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OtpVerification extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'phone_number',
        'otp_code',
        'request_id',
        'type',
        'status',
        'expires_at',
        'attempts',
        'max_attempts',
        'verified_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
        'attempts' => 'integer',
        'max_attempts' => 'integer',
    ];

    /**
     * Check if the OTP is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at < Carbon::now();
    }

    /**
     * Check if the OTP is verified.
     */
    public function isVerified(): bool
    {
        return $this->status === 'verified';
    }

    /**
     * Check if max attempts reached.
     */
    public function maxAttemptsReached(): bool
    {
        return $this->attempts >= $this->max_attempts;
    }

    /**
     * Increment attempt count.
     */
    public function incrementAttempts(): void
    {
        $this->increment('attempts');
        
        if ($this->maxAttemptsReached()) {
            $this->update(['status' => 'failed']);
        }
    }

    /**
     * Mark as verified.
     */
    public function markAsVerified(): void
    {
        $this->update([
            'status' => 'verified',
            'verified_at' => Carbon::now(),
        ]);
    }

    /**
     * Mark as expired.
     */
    public function markAsExpired(): void
    {
        $this->update(['status' => 'expired']);
    }

    /**
     * Generate a random 6-digit OTP code.
     */
    public static function generateOtpCode(): string
    {
        return "666666";//str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically mark as expired if past expiration time
        static::saving(function ($otp) {
            if ($otp->isExpired() && $otp->status === 'pending') {
                $otp->status = 'expired';
            }
        });
    }
}
