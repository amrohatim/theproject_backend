<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

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
        'product_name',
        'description',
        'price',
        'original_price',
        'stock',
        'min_order',
        'sku',
        'category_id',
        'is_active',
        'image',
        'rating',
        'total_ratings',
        'product_name_arabic',
        'product_description_arabic',
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
        'min_order' => 'integer',
        'is_active' => 'boolean',
        'rating' => 'float',
        'total_ratings' => 'integer',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        // Handle image deletion when a provider product is being deleted
        static::deleting(function ($providerProduct) {
            try {
                Log::info("Starting image cleanup for provider product ID: {$providerProduct->id}");

                // Delete provider product image using WebPImageService
                $rawImagePath = $providerProduct->getRawImagePath();
                if ($rawImagePath) {
                    try {
                        $webpService = new \App\Services\WebPImageService();
                        $webpService->deleteImage($rawImagePath);
                        Log::info("Deleted provider product image using WebPImageService", [
                            'provider_product_id' => $providerProduct->id,
                            'image_path' => $rawImagePath
                        ]);
                    } catch (\Exception $e) {
                        Log::warning("Failed to delete provider product image: " . $e->getMessage(), [
                            'provider_product_id' => $providerProduct->id,
                            'image_path' => $rawImagePath
                        ]);
                    }
                }

                Log::info("Completed image cleanup for provider product ID: {$providerProduct->id}");

            } catch (\Exception $e) {
                Log::error("Error during image cleanup for provider product ID: {$providerProduct->id}. Error: " . $e->getMessage());
                // Don't throw the exception to prevent deletion failure
            }
        });
    }

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
     * Get the raw image path for deletion purposes.
     * This method is consistent with other models in the system.
     *
     * @return string|null
     */
    public function getRawImagePath()
    {
        return $this->getRawImageAttribute();
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
     * Get the category that the product belongs to.
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Get the reviews for the provider product.
     */
    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }
}
