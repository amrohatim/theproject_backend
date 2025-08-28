<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;
    
    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        // Ensure view_count is initialized to at least 1 when creating a new branch
        static::creating(function ($branch) {
            if (is_null($branch->view_count) || $branch->view_count < 1) {
                $branch->view_count = 1;
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'company_id',
        'name',
        'address',
        'emirate',
        'lat',
        'lng',
        'status',
        'featured',
        'image',
        'branch_image',
        'use_company_image',
        'description',
        'business_type',
        'rating',
        'phone',
        'email',
        'opening_hours',
        'view_count',
        'order_count',
        'popularity_score',
        'last_score_calculation',
        'average_rating',
        'total_ratings',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
        'rating' => 'float',
        'featured' => 'boolean',
        'use_company_image' => 'boolean',
        'opening_hours' => 'json',
        'view_count' => 'integer',
        'order_count' => 'integer',
        'popularity_score' => 'integer',
        'last_score_calculation' => 'datetime',
    ];

    /**
     * Get the user that owns the branch.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the company that owns the branch.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the products for the branch.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the products associated with this branch through the product_branches pivot table.
     */
    public function multiProducts()
    {
        return $this->belongsToMany(Product::class, 'product_branches')
            ->withPivot('stock', 'is_available', 'price')
            ->withTimestamps();
    }

    /**
     * Get the product-branch pivot records.
     */
    public function productBranches()
    {
        return $this->hasMany(ProductBranch::class);
    }

    /**
     * Get the services for the branch.
     */
    public function services()
    {
        return $this->hasMany(Service::class);
    }

    /**
     * Scope a query to filter featured branches.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  bool  $featured
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByFeatured($query, $featured = true)
    {
        return $query->where('featured', $featured);
    }

    /**
     * Get the branch image with fallback to company image if needed.
     *
     * @return string|null
     */
    public function getBranchImageAttribute()
    {
        // If branch_image is set and we're not using company image, return it
        if (!$this->use_company_image && !empty($this->attributes['branch_image'])) {
            return \App\Helpers\ImageHelper::getFullImageUrl($this->attributes['branch_image']);
        }

        // If we're using company image and company exists, return company logo
        if ($this->use_company_image && $this->company && $this->company->logo) {
            // Make sure we're getting the full URL for the company logo
            return \App\Helpers\ImageHelper::getFullImageUrl($this->company->logo);
        }

        // Fallback to the original image field if it exists
        if (!empty($this->attributes['image'])) {
            return \App\Helpers\ImageHelper::getFullImageUrl($this->attributes['image']);
        }

        // Final fallback - return null if no image is available
        return null;
    }

    /**
     * Get the ratings for this branch.
     */
    public function ratings()
    {
        return $this->hasMany(BranchRating::class);
    }

    /**
     * Get the licenses for this branch.
     */
    public function licenses()
    {
        return $this->hasMany(BranchLicense::class);
    }

    /**
     * Get the current active license for this branch.
     */
    public function currentLicense()
    {
        return $this->hasOne(BranchLicense::class)
                    ->where('status', 'active')
                    ->where('end_date', '>=', now()->toDateString())
                    ->latest('created_at');
    }

    /**
     * Get the latest license for this branch (regardless of status).
     */
    public function latestLicense()
    {
        return $this->hasOne(BranchLicense::class)->latest('created_at');
    }

    /**
     * Check if the branch has an active license.
     *
     * @return bool
     */
    public function hasActiveLicense(): bool
    {
        return $this->currentLicense()->exists();
    }

    /**
     * Get the license status for this branch.
     *
     * @return string|null
     */
    public function getLicenseStatus(): ?string
    {
        $license = $this->latestLicense;

        if (!$license) {
            return null;
        }

        // Check if license is expired by date
        if ($license->end_date < now()->toDateString()) {
            return 'expired';
        }

        return $license->status;
    }

    /**
     * Check if the branch can have products/services created.
     * Only branches with active licenses can have products/services.
     *
     * @return bool
     */
    public function canCreateProductsServices(): bool
    {
        return $this->hasActiveLicense();
    }

    /**
     * Scope to get only branches with active licenses.
     */
    public function scopeWithActiveLicense($query)
    {
        return $query->whereHas('currentLicense');
    }
}
