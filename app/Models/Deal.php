<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deal extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'branch_id',
        'business_type_id',
        'title',
        'title_arabic',
        'description',
        'description_arabic',
        'promotional_message',
        'promotional_message_arabic',
        'discount_percentage',
        'start_date',
        'end_date',
        'image',
        'status',
        'applies_to',
        'product_ids',
        'service_ids',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'discount_percentage' => 'float',
        'start_date' => 'date',
        'end_date' => 'date',
        'product_ids' => 'array',
        'service_ids' => 'array',
    ];

    /**
     * Get the services associated with this deal.
     */
    public function services()
    {
        if (!$this->service_ids) {
            return collect();
        }

        return Service::whereIn('id', $this->service_ids)->get();
    }

    /**
     * Get the image attribute with full URL.
     *
     * @param  string|null  $value
     * @return string|null
     */
    public function getImageAttribute($value)
    {
        // If the value doesn't contain 'deals/' prefix, add it
        if ($value && !str_contains($value, 'deals/') && !str_contains($value, '/deals/')) {
            $value = 'deals/' . basename($value);
        }

        return \App\Helpers\ImageHelper::getFullImageUrl($value);
    }

    /**
     * Get the raw original image path without accessor transformation.
     *
     * @return string|null
     */
    public function getRawOriginalImage()
    {
        return $this->attributes['image'] ?? null;
    }

    /**
     * Get the user that owns the deal.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the products associated with this deal.
     */
    public function products()
    {
        if (!$this->product_ids) {
            return collect();
        }

        return \App\Models\Product::whereIn('id', $this->product_ids)->get();
    }



    /**
     * Scope a query to only include active deals.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
    }

    /**
     * Scope a query to include active and pending deals.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActiveOrPending($query)
    {
        return $query->where('status', 'active')
                    ->where(function($q) {
                        $q->where('end_date', '>=', now())
                          ->orWhere('start_date', '>', now());
                    });
    }

    /**
     * Get product IDs that have active deals (excluding a specific deal ID).
     *
     * @param int|null $excludeDealId Deal ID to exclude from the check
     * @param int|null $userId User ID to filter deals by
     * @return array
     */
    public static function getProductIdsWithActiveDeals($excludeDealId = null, $userId = null)
    {
        // Get all user IDs in the same company as the given user
        $companyUserIds = [];
        if ($userId) {
            $companyUserIds = self::getCompanyUserIds($userId);
        }

        // First check for "all" deals that affect all products
        $allDealsQuery = self::activeOrPending()
            ->where('applies_to', 'all');

        if ($excludeDealId) {
            $allDealsQuery->where('id', '!=', $excludeDealId);
        }

        if (!empty($companyUserIds)) {
            $allDealsQuery->whereIn('user_id', $companyUserIds);
        }

        $hasAllDeal = $allDealsQuery->exists();

        // If there's an "all" deal, get all product IDs for this company
        if ($hasAllDeal && $userId) {
            $user = \App\Models\User::find($userId);
            $allProductIds = [];

            if ($user) {
                // Check if user is a vendor (has direct company relationship)
                if ($user->company) {
                    $allProductIds = \App\Models\Product::join('branches', 'products.branch_id', '=', 'branches.id')
                        ->join('companies', 'branches.company_id', '=', 'companies.id')
                        ->where('companies.user_id', $userId)
                        ->pluck('products.id')
                        ->toArray();
                }
                // Check if user is a Products Manager
                elseif ($user->productsManager) {
                    $allProductIds = \App\Models\Product::join('branches', 'products.branch_id', '=', 'branches.id')
                        ->where('branches.company_id', $user->productsManager->company_id)
                        ->pluck('products.id')
                        ->toArray();
                }
                // Check if user is a Service Provider
                elseif ($user->serviceProvider) {
                    $allProductIds = \App\Models\Product::join('branches', 'products.branch_id', '=', 'branches.id')
                        ->where('branches.company_id', $user->serviceProvider->company_id)
                        ->pluck('products.id')
                        ->toArray();
                }
            }

            return $allProductIds;
        }

        // Check for specific product deals
        $query = self::activeOrPending()
            ->whereNotNull('product_ids')
            ->where(function($q) {
                $q->where('applies_to', 'products')
                  ->orWhere('applies_to', 'products_and_services');
            });

        if ($excludeDealId) {
            $query->where('id', '!=', $excludeDealId);
        }

        if (!empty($companyUserIds)) {
            $query->whereIn('user_id', $companyUserIds);
        }

        $deals = $query->get();
        $productIds = [];

        foreach ($deals as $deal) {
            if ($deal->product_ids && is_array($deal->product_ids)) {
                $productIds = array_merge($productIds, $deal->product_ids);
            }
        }

        return array_unique($productIds);
    }

    /**
     * Get all user IDs in the same company as the given user.
     *
     * @param int $userId
     * @return array
     */
    public static function getCompanyUserIds($userId)
    {
        $user = \App\Models\User::find($userId);
        if (!$user) {
            return [$userId];
        }

        $companyId = null;

        // Determine the company ID based on user type
        if ($user->company) {
            // Vendor user
            $companyId = $user->company->id;
        } elseif ($user->productsManager) {
            // Products Manager user
            $companyId = $user->productsManager->company_id;
        } elseif ($user->serviceProvider) {
            // Service Provider user
            $companyId = $user->serviceProvider->company_id;
        }

        if (!$companyId) {
            return [$userId];
        }

        // Get all user IDs in the same company
        $companyUserIds = collect();

        // Vendor users (direct company relationship)
        $vendorUsers = \App\Models\User::whereHas('company', function($query) use ($companyId) {
            $query->where('id', $companyId);
        })->pluck('id');
        $companyUserIds = $companyUserIds->merge($vendorUsers);

        // Products Manager users in the same company
        $pmUsers = \App\Models\User::whereHas('productsManager', function($query) use ($companyId) {
            $query->where('company_id', $companyId);
        })->pluck('id');
        $companyUserIds = $companyUserIds->merge($pmUsers);

        // Service Provider users in the same company
        $spUsers = \App\Models\User::whereHas('serviceProvider', function($query) use ($companyId) {
            $query->where('company_id', $companyId);
        })->pluck('id');
        $companyUserIds = $companyUserIds->merge($spUsers);

        return $companyUserIds->unique()->values()->toArray();
    }

    /**
     * Get service IDs that have active deals (excluding a specific deal ID).
     *
     * @param int|null $excludeDealId Deal ID to exclude from the check
     * @param int|null $userId User ID to filter deals by
     * @return array
     */
    public static function getServiceIdsWithActiveDeals($excludeDealId = null, $userId = null)
    {
        // Get all user IDs in the same company as the given user
        $companyUserIds = [];
        if ($userId) {
            $companyUserIds = self::getCompanyUserIds($userId);
        }

        $query = self::activeOrPending()
            ->whereNotNull('service_ids')
            ->where(function($q) {
                $q->where('applies_to', 'services')
                  ->orWhere('applies_to', 'products_and_services');
            });

        if ($excludeDealId) {
            $query->where('id', '!=', $excludeDealId);
        }

        if (!empty($companyUserIds)) {
            $query->whereIn('user_id', $companyUserIds);
        }

        $deals = $query->get();
        $serviceIds = [];

        foreach ($deals as $deal) {
            if ($deal->service_ids && is_array($deal->service_ids)) {
                $serviceIds = array_merge($serviceIds, $deal->service_ids);
            }
        }

        return array_unique($serviceIds);
    }

    /**
     * Check if specific product IDs have conflicts with existing deals.
     *
     * @param array $productIds
     * @param int|null $excludeDealId
     * @param int|null $userId
     * @return array Array of conflicting product IDs
     */
    public static function getConflictingProductIds(array $productIds, $excludeDealId = null, $userId = null)
    {
        $existingProductIds = self::getProductIdsWithActiveDeals($excludeDealId, $userId);
        return array_intersect($productIds, $existingProductIds);
    }

    /**
     * Check if specific service IDs have conflicts with existing deals.
     *
     * @param array $serviceIds
     * @param int|null $excludeDealId
     * @param int|null $userId
     * @return array Array of conflicting service IDs
     */
    public static function getConflictingServiceIds(array $serviceIds, $excludeDealId = null, $userId = null)
    {
        $existingServiceIds = self::getServiceIdsWithActiveDeals($excludeDealId, $userId);
        return array_intersect($serviceIds, $existingServiceIds);
    }
}
