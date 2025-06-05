<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StandardizedSize extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'size_category_id',
        'name',
        'value',
        'additional_info',
        'display_order',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'display_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the size category that owns this standardized size.
     */
    public function sizeCategory()
    {
        return $this->belongsTo(SizeCategory::class);
    }

    /**
     * Get the product sizes that reference this standardized size.
     */
    public function productSizes()
    {
        return $this->hasMany(ProductSize::class);
    }

    /**
     * Scope a query to only include active sizes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by size category.
     */
    public function scopeForCategory($query, $categoryId)
    {
        return $query->where('size_category_id', $categoryId);
    }

    /**
     * Get the full display name for this size.
     */
    public function getFullDisplayNameAttribute()
    {
        $display = $this->name;
        
        if ($this->value && $this->value !== $this->name) {
            $display .= " ({$this->value})";
        }
        
        if ($this->additional_info) {
            $display .= " - {$this->additional_info}";
        }
        
        return $display;
    }

    /**
     * Check if this size is valid for the given category.
     */
    public function isValidForCategory($categoryName)
    {
        return $this->sizeCategory && $this->sizeCategory->name === $categoryName;
    }
}
