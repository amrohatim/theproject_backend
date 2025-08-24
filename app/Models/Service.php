<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Service extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'branch_id',
        'merchant_id',
        'merchant_name',
        'category_id',
        'name',
        'service_name_arabic',
        'price',
        'duration',
        'featured',
        'description',
        'service_description_arabic',
        'image',
        'rating',
        'is_available',
        'home_service',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'float',
        'duration' => 'integer',
        'featured' => 'boolean',
        'rating' => 'float',
        'is_available' => 'boolean',
        'home_service' => 'boolean',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        // Handle image deletion when a service is being deleted using WebPImageService
        static::deleting(function ($service) {
            try {
                Log::info("Starting image cleanup for service ID: {$service->id}");

                // Delete service image using WebPImageService
                $rawImagePath = $service->getRawImagePath();
                if ($rawImagePath) {
                    try {
                        $webpService = new \App\Services\WebPImageService();
                        $webpService->deleteImage($rawImagePath);
                        Log::info("Deleted service image using WebPImageService", [
                            'service_id' => $service->id,
                            'image_path' => $rawImagePath
                        ]);
                    } catch (\Exception $e) {
                        Log::warning("Failed to delete service image: " . $e->getMessage(), [
                            'service_id' => $service->id,
                            'image_path' => $rawImagePath
                        ]);
                    }
                }

                Log::info("Completed image cleanup for service ID: {$service->id}");

            } catch (\Exception $e) {
                Log::error("Error during image cleanup for service ID: {$service->id}. Error: " . $e->getMessage());
                // Don't throw the exception to prevent deletion failure
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
        return parent::getRawOriginal('image');
    }

    /**
     * Get the branch that owns the service.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the merchant that owns the service.
     */
    public function merchant()
    {
        return $this->belongsTo(User::class, 'merchant_id');
    }

    /**
     * Get the category that owns the service.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the reviews for the service.
     */
    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    /**
     * Scope a query to filter services by price range.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  float  $min
     * @param  float  $max
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByPrice($query, $min, $max)
    {
        return $query->whereBetween('price', [$min, $max]);
    }

    /**
     * Scope a query to filter services by duration range.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $min
     * @param  int  $max
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByDuration($query, $min, $max)
    {
        return $query->whereBetween('duration', [$min, $max]);
    }

    /**
     * Scope a query to filter services by minimum rating.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  float  $rating
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByRating($query, $rating)
    {
        return $query->where('rating', '>=', $rating);
    }

    /**
     * Scope a query to filter services by availability.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  bool  $available
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByAvailability($query, $available = true)
    {
        return $query->where('is_available', $available);
    }

    /**
     * Scope a query to filter services by category.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $categoryId
     * @param  bool  $includeSubcategories Whether to include services from subcategories
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByCategory($query, $categoryId, $includeSubcategories = false)
    {
        if (!$includeSubcategories) {
            return $query->where('category_id', $categoryId);
        }

        // Get the category and its subcategories
        $category = Category::find($categoryId);
        if (!$category) {
            return $query->where('category_id', $categoryId);
        }

        // Get all subcategory IDs
        $subcategoryIds = $category->children()->pluck('id')->toArray();

        // Include the parent category ID and all subcategory IDs
        $allCategoryIds = array_merge([$categoryId], $subcategoryIds);

        return $query->whereIn('category_id', $allCategoryIds);
    }

    /**
     * Scope a query to filter services by branch.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $branchId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Scope a query to filter services by emirate.
     * Includes services from branches with matching emirate OR services with null branch_id
     * where the merchant has the matching emirate.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $emirate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByEmirate($query, $emirate)
    {
        // Map emirate codes to emirate names
        $emirateMapping = [
            'DXB' => 'Dubai',
            'AUH' => 'Abu Dhabi',
            'SHJ' => 'Sharjah',
            'AJM' => 'Ajman',
            'RAK' => 'Ras Al Khaimah',
            'FUJ' => 'Fujairah',
            'UAQ' => 'Umm Al Quwain',
        ];

        // Check if the provided emirate is a code or name
        $emirateName = $emirateMapping[$emirate] ?? $emirate;

        return $query->where(function ($mainQuery) use ($emirateName, $emirate) {
            // Case 1: Services with branch_id that have matching emirate in their branch
            $mainQuery->whereHas('branch', function ($branchQuery) use ($emirateName, $emirate) {
                $branchQuery->where(function ($q) use ($emirateName, $emirate) {
                    $q->where('emirate', $emirateName)
                      ->orWhere('emirate', $emirate);
                });
            })
            // Case 2: Services with branch_id = NULL where merchant has matching emirate
            ->orWhere(function ($nullBranchQuery) use ($emirateName, $emirate) {
                $nullBranchQuery->whereNull('branch_id')
                    ->whereHas('merchant.merchant', function ($merchantQuery) use ($emirateName, $emirate) {
                        $merchantQuery->where(function ($q) use ($emirateName, $emirate) {
                            $q->where('emirate', $emirateName)
                              ->orWhere('emirate', $emirate);
                        });
                    });
            });
        });
    }

    /**
     * Scope a query to search services by name or description.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * Scope a query to sort services.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $sortBy
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortBy($query, $sortBy)
    {
        switch ($sortBy) {
            case 'price_low':
                return $query->orderBy('price', 'asc');
            case 'price_high':
                return $query->orderBy('price', 'desc');
            case 'rating':
                return $query->orderBy('rating', 'desc');
            case 'duration_low':
                return $query->orderBy('duration', 'asc');
            case 'duration_high':
                return $query->orderBy('duration', 'desc');
            case 'newest':
                return $query->orderBy('created_at', 'desc');
            case 'popularity':
            default:
                return $query->orderBy('rating', 'desc');
        }
    }

    /**
     * Scope a query to filter featured services.
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
     * Scope a query to filter home services.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  bool  $homeService
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByHomeService($query, $homeService = true)
    {
        return $query->where('home_service', $homeService);
    }

    /**
     * Get the specifications for the service.
     */
    public function specifications()
    {
        return $this->hasMany(ServiceSpecification::class)
            ->orderBy('display_order');
    }
}
