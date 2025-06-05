<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchRating extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'customer_id',
        'branch_id',
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
     * Get the branch being rated.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
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
            $rating->updateBranchRatingStats();
        });

        static::updated(function ($rating) {
            $rating->updateBranchRatingStats();
        });

        static::deleted(function ($rating) {
            $rating->updateBranchRatingStats();
        });
    }

    /**
     * Update the branch's rating statistics.
     */
    protected function updateBranchRatingStats()
    {
        $branch = Branch::find($this->branch_id);
        if ($branch) {
            $ratings = BranchRating::where('branch_id', $this->branch_id);
            $averageRating = $ratings->avg('rating') ?: 0;
            $totalRatings = $ratings->count();

            $branch->update([
                'average_rating' => round($averageRating, 2),
                'total_ratings' => $totalRatings,
            ]);
        }
    }
}
