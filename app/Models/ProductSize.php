<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSize extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'size_category_id',
        'standardized_size_id',
        'name',
        'value',
        'additional_info',
        'price_adjustment',
        'stock',
        'display_order',
        'is_default',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price_adjustment' => 'float',
        'stock' => 'integer',
        'display_order' => 'integer',
        'is_default' => 'boolean',
    ];

    /**
     * Get the product that owns the size.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the size category that this size belongs to.
     */
    public function sizeCategory()
    {
        return $this->belongsTo(SizeCategory::class);
    }

    /**
     * Get the standardized size that this size references.
     */
    public function standardizedSize()
    {
        return $this->belongsTo(StandardizedSize::class);
    }

    /**
     * Scope a query to filter by size category.
     */
    public function scopeForCategory($query, $categoryId)
    {
        return $query->where('size_category_id', $categoryId);
    }

    /**
     * Check if this size is valid for the given category.
     */
    public function isValidForCategory($categoryName)
    {
        return $this->sizeCategory && $this->sizeCategory->name === $categoryName;
    }

    /**
     * Get the full display information for this size.
     */
    public function getFullDisplayInfoAttribute()
    {
        $info = [
            'name' => $this->name,
            'value' => $this->value,
            'additional_info' => $this->additional_info,
            'category' => $this->sizeCategory ? $this->sizeCategory->display_name : null,
        ];

        return $info;
    }

    /**
     * Get the color-size combinations for this size.
     */
    public function colorSizes()
    {
        return $this->hasMany(ProductColorSize::class, 'product_size_id');
    }

    /**
     * Get the available colors for this size.
     */
    public function availableColors()
    {
        return $this->belongsToMany(ProductColor::class, 'product_color_sizes', 'product_size_id', 'product_color_id')
                    ->withPivot('stock', 'price_adjustment', 'is_available')
                    ->wherePivot('is_available', true)
                    ->wherePivot('stock', '>', 0);
    }

    /**
     * Get all colors associated with this size (regardless of stock).
     */
    public function colors()
    {
        return $this->belongsToMany(ProductColor::class, 'product_color_sizes', 'product_size_id', 'product_color_id')
                    ->withPivot('stock', 'price_adjustment', 'is_available');
    }
}
