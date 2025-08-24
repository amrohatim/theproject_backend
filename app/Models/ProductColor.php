<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class ProductColor extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'name',
        'color_code',
        'image',
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
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        // When a color is updated
        static::updated(function ($color) {
            // If this color is set as default, update the product's main image
            if ($color->is_default && $color->image) {
                Log::debug("ProductColor {$color->id} set as default for product {$color->product_id}. Updating product image.");
                $product = $color->product;
                if ($product) {
                    // Get the raw image path without URL processing
                    $rawImagePath = $color->getRawImagePath();
                    $product->updateMainImageFromColorImage($rawImagePath);
                }
            }
        });

        // When a new color is created
        static::created(function ($color) {
            // If this color is set as default, update the product's main image
            if ($color->is_default && $color->image) {
                Log::debug("New ProductColor {$color->id} created as default for product {$color->product_id}. Updating product image.");
                $product = $color->product;
                if ($product) {
                    // Get the raw image path without URL processing
                    $rawImagePath = $color->getRawImagePath();
                    $product->updateMainImageFromColorImage($rawImagePath);
                }
            }
        });
    }

    /**
     * Get the image attribute with full URL.
     *
     * @param  string|null  $value
     * @return string|null
     */
    public function getImageAttribute($value)
    {
        return \App\Helpers\ImageHelper::getFullImageUrl($value);
    }

    /**
     * Get the raw image path without URL processing.
     *
     * @return string|null
     */
    public function getRawImagePath()
    {
        return $this->attributes['image'] ?? null;
    }

    /**
     * Get the product that owns the color.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the color-size combinations for this color.
     */
    public function colorSizes()
    {
        return $this->hasMany(ProductColorSize::class, 'product_color_id');
    }

    /**
     * Get the available sizes for this color.
     */
    public function availableSizes()
    {
        return $this->belongsToMany(ProductSize::class, 'product_color_sizes', 'product_color_id', 'product_size_id')
                    ->withPivot('stock', 'price_adjustment', 'is_available')
                    ->wherePivot('is_available', true)
                    ->wherePivot('stock', '>', 0);
    }

    /**
     * Get all sizes associated with this color (regardless of stock).
     */
    public function sizes()
    {
        return $this->belongsToMany(ProductSize::class, 'product_color_sizes', 'product_color_id', 'product_size_id')
                    ->withPivot('stock', 'price_adjustment', 'is_available');
    }

    /**
     * Get the total allocated stock for this color across all sizes.
     */
    public function getAllocatedStock()
    {
        return $this->colorSizes()->sum('stock');
    }

    /**
     * Get the remaining stock available for allocation.
     */
    public function getRemainingStock()
    {
        return max(0, $this->stock - $this->getAllocatedStock());
    }

    /**
     * Check if this color can accommodate additional stock allocation.
     */
    public function canAllocateStock($amount)
    {
        return $this->getRemainingStock() >= $amount;
    }
}
