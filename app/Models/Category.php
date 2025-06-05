<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'image',
        'parent_id',
        'is_active',
        'type',
        'icon',
        'view_count',
        'purchase_count',
        'trending_score',
        'last_trending_calculation',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'view_count' => 'integer',
        'purchase_count' => 'integer',
        'trending_score' => 'integer',
        'last_trending_calculation' => 'datetime',
    ];

    /**
     * Get the parent category.
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get the child categories.
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Get the products for the category.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the services for the category.
     */
    public function services()
    {
        return $this->hasMany(Service::class);
    }

    /**
     * Get the default size category for this category.
     */
    public function defaultSizeCategory()
    {
        return $this->belongsTo(SizeCategory::class, 'default_size_category_id');
    }

    /**
     * Get the image attribute with full URL.
     *
     * @param  string|null  $value
     * @return string|null
     */
    public function getImageAttribute($value)
    {
        if (empty($value)) {
            return null;
        }

        // If the image path already starts with http, return it as is
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        // If the image path doesn't contain 'categories/', add it
        if (!str_contains($value, 'categories/') && !str_contains($value, '/storage/categories/')) {
            $filename = basename($value);
            $value = "categories/{$filename}";
        }

        // Log the image path for debugging
        \Illuminate\Support\Facades\Log::debug("Category image path before processing: {$value}");

        $fullUrl = \App\Helpers\ImageHelper::getFullImageUrl($value);

        // Log the full URL for debugging
        \Illuminate\Support\Facades\Log::debug("Category image full URL after processing: {$fullUrl}");

        return $fullUrl;
    }
}
