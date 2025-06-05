<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorRating extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'customer_id',
        'vendor_id',
        'rating',
        'review_text',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rating' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the customer who made the rating.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Get the vendor being rated.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    /**
     * Scope to filter by rating value.
     */
    public function scopeByRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    /**
     * Scope to get recent ratings.
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($rating) {
            $rating->updateVendorRatingStats();
        });

        static::updated(function ($rating) {
            $rating->updateVendorRatingStats();
        });

        static::deleted(function ($rating) {
            $rating->updateVendorRatingStats();
        });
    }

    /**
     * Update the vendor's rating statistics.
     */
    protected function updateVendorRatingStats()
    {
        $vendor = User::find($this->vendor_id);
        if ($vendor) {
            $ratings = VendorRating::where('vendor_id', $this->vendor_id);
            $averageRating = $ratings->avg('rating') ?: 0;
            $totalRatings = $ratings->count();

            $vendor->update([
                'average_rating' => round($averageRating, 2),
                'total_ratings' => $totalRatings,
            ]);

            // Also update the company's rating data if it exists
            $company = \App\Models\Company::where('user_id', $this->vendor_id)->first();
            if ($company) {
                $company->update([
                    'average_rating' => round($averageRating, 2),
                    'rating_count' => $totalRatings,
                ]);
            }
        }
    }
}
