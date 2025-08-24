<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProviderRating extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vendor_id',
        'provider_id',
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
     * Get the vendor who made the rating.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    /**
     * Get the provider being rated.
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class, 'provider_id');
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
            $rating->updateProviderRatingStats();
        });

        static::updated(function ($rating) {
            $rating->updateProviderRatingStats();
        });

        static::deleted(function ($rating) {
            $rating->updateProviderRatingStats();
        });
    }

    /**
     * Update the provider's rating statistics.
     */
    protected function updateProviderRatingStats()
    {
        $provider = Provider::find($this->provider_id);
        if ($provider) {
            $ratings = ProviderRating::where('provider_id', $this->provider_id);
            $averageRating = $ratings->avg('rating') ?: 0;
            $totalRatings = $ratings->count();

            $provider->update([
                'average_rating' => round($averageRating, 2),
                'total_ratings' => $totalRatings,
            ]);
        }
    }
}
