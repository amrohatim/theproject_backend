<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SizeCategory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'name_arabic',
        'display_name',
        'display_name_arabic',
        'description',
        'is_active',
        'display_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    /**
     * Get the standardized sizes for this category.
     */
    public function standardizedSizes()
    {
        return $this->hasMany(StandardizedSize::class)->orderBy('display_order');
    }

    /**
     * Get the product sizes using this category.
     */
    public function productSizes()
    {
        return $this->hasMany(ProductSize::class);
    }

    /**
     * Get the categories that use this as their default size category.
     */
    public function categories()
    {
        return $this->hasMany(Category::class, 'default_size_category_id');
    }

    /**
     * Scope a query to only include active size categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get size category by name.
     */
    public static function findByName($name)
    {
        return static::where('name', $name)->first();
    }

    /**
     * Get all available sizes for this category.
     */
    public function getAvailableSizes()
    {
        return $this->standardizedSizes()
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get();
    }
}
