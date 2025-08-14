<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deal extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'title_arabic',
        'description',
        'description_arabic',
        'promotional_message',
        'promotional_message_arabic',
        'discount_percentage',
        'start_date',
        'end_date',
        'image',
        'status',
        'applies_to',
        'product_ids',
        'service_ids',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'discount_percentage' => 'float',
        'start_date' => 'date',
        'end_date' => 'date',
        'product_ids' => 'array',
        'service_ids' => 'array',
    ];

    /**
     * Get the services associated with this deal.
     */
    public function services()
    {
        if (!$this->service_ids) {
            return collect();
        }

        return Service::whereIn('id', $this->service_ids)->get();
    }

    /**
     * Get the image attribute with full URL.
     *
     * @param  string|null  $value
     * @return string|null
     */
    public function getImageAttribute($value)
    {
        // If the value doesn't contain 'deals/' prefix, add it
        if ($value && !str_contains($value, 'deals/') && !str_contains($value, '/deals/')) {
            $value = 'deals/' . basename($value);
        }

        return \App\Helpers\ImageHelper::getFullImageUrl($value);
    }

    /**
     * Get the raw original image path without accessor transformation.
     *
     * @return string|null
     */
    public function getRawOriginalImage()
    {
        return $this->attributes['image'] ?? null;
    }

    /**
     * Get the user that owns the deal.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the products associated with this deal.
     */
    public function products()
    {
        if (!$this->product_ids) {
            return collect();
        }

        return \App\Models\Product::whereIn('id', $this->product_ids)->get();
    }



    /**
     * Scope a query to only include active deals.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
    }
}
