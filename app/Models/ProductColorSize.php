<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductColorSize extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'product_color_id',
        'product_size_id',
        'stock',
        'price_adjustment',
        'is_available',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'stock' => 'integer',
        'price_adjustment' => 'float',
        'is_available' => 'boolean',
    ];

    /**
     * Get the product that owns this color-size combination.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the color for this combination.
     */
    public function color()
    {
        return $this->belongsTo(ProductColor::class, 'product_color_id');
    }

    /**
     * Get the size for this combination.
     */
    public function size()
    {
        return $this->belongsTo(ProductSize::class, 'product_size_id');
    }

    /**
     * Scope to get available combinations.
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)->where('stock', '>', 0);
    }

    /**
     * Scope to get combinations for a specific color.
     */
    public function scopeForColor($query, $colorId)
    {
        return $query->where('product_color_id', $colorId);
    }

    /**
     * Scope to get combinations for a specific size.
     */
    public function scopeForSize($query, $sizeId)
    {
        return $query->where('product_size_id', $sizeId);
    }

    /**
     * Get the total price for this color-size combination.
     */
    public function getTotalPriceAttribute()
    {
        $basePrice = $this->product->price;
        $colorAdjustment = $this->color->price_adjustment ?? 0;
        $sizeAdjustment = $this->size->price_adjustment ?? 0;
        $combinationAdjustment = $this->price_adjustment ?? 0;

        return $basePrice + $colorAdjustment + $sizeAdjustment + $combinationAdjustment;
    }

    /**
     * Check if this combination is in stock.
     */
    public function isInStock()
    {
        return $this->is_available && $this->stock > 0;
    }

    /**
     * Reduce stock for this combination.
     */
    public function reduceStock($quantity)
    {
        if ($this->stock >= $quantity) {
            $this->decrement('stock', $quantity);
            return true;
        }
        return false;
    }

    /**
     * Increase stock for this combination.
     */
    public function increaseStock($quantity)
    {
        $this->increment('stock', $quantity);
    }
}
