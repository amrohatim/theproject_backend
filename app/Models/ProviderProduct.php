<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class ProviderProduct extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'provider_id',
        'product_id',
        'product_name',
        'description',
        'price',
        'original_price',
        'stock',
        'sku',
        'category_id',
        'is_active',
        'image',
        'branch_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'float',
        'original_price' => 'float',
        'stock' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the raw image attribute without any processing.
     * This bypasses Laravel's accessor magic to return the raw stored value.
     *
     * @return string|null
     */
    public function getRawImageAttribute()
    {
        return $this->attributes['image'] ?? null;
    }

    /**
     * Get active provider products.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        // First check if the status column exists in the provider_products table
        if (Schema::hasColumn('provider_products', 'status')) {
            // If it exists, use it
            return $query->where('status', 'active');
        } else if (Schema::hasColumn('provider_products', 'is_active')) {
            // If is_active exists, use it
            return $query->where('is_active', true);
        } else {
            // If product_id is not null, check the product's is_available column
            return $query->where(function($q) {
                $q->whereNull('product_id')
                  ->orWhereHas('product', function($q) {
                      $q->where('is_available', true);
                  });
            });
        }
    }

    /**
     * Get the provider that owns the product.
     */
    public function provider()
    {
        return $this->belongsTo(Provider::class, 'provider_id');
    }

    /**
     * Get the product that belongs to the provider.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Get the category that the product belongs to.
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Get the branch that the product belongs to.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    /**
     * Get the reviews for the provider product.
     */
    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }
}
