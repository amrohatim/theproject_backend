<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductsManager extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'company_id',
    ];

    /**
     * Get the user that owns the products manager profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the company that the products manager belongs to.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get all products for the company that this products manager can manage.
     * Products managers have access to all company products across all branches.
     */
    public function products()
    {
        return Product::whereHas('branch', function ($query) {
            $query->where('company_id', $this->company_id);
        })->get();
    }

    /**
     * Get all branches for the company that this products manager can manage.
     * Products managers can add products to all company branches.
     */
    public function branches()
    {
        return Branch::where('company_id', $this->company_id)->get();
    }

    /**
     * Check if the products manager can manage products for a specific branch.
     *
     * @param int $branchId
     * @return bool
     */
    public function canManageBranch(int $branchId): bool
    {
        return Branch::where('id', $branchId)
            ->where('company_id', $this->company_id)
            ->exists();
    }

    /**
     * Check if the products manager can manage a specific product.
     *
     * @param int $productId
     * @return bool
     */
    public function canManageProduct(int $productId): bool
    {
        return Product::whereHas('branch', function ($query) {
            $query->where('company_id', $this->company_id);
        })->where('id', $productId)->exists();
    }

    /**
     * Get the total number of products this manager can manage.
     *
     * @return int
     */
    public function getTotalProductsCount(): int
    {
        return Product::whereHas('branch', function ($query) {
            $query->where('company_id', $this->company_id);
        })->count();
    }

    /**
     * Get the total number of branches this manager can manage.
     *
     * @return int
     */
    public function getTotalBranchesCount(): int
    {
        return Branch::where('company_id', $this->company_id)->count();
    }

    /**
     * Get products by category for this manager's company.
     *
     * @param int $categoryId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getProductsByCategory(int $categoryId)
    {
        return Product::whereHas('branch', function ($query) {
            $query->where('company_id', $this->company_id);
        })->where('category_id', $categoryId)->get();
    }

    /**
     * Get products by branch for this manager's company.
     *
     * @param int $branchId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getProductsByBranch(int $branchId)
    {
        if (!$this->canManageBranch($branchId)) {
            return collect();
        }

        return Product::where('branch_id', $branchId)->get();
    }

    /**
     * Get available products (in stock and available) for this manager's company.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailableProducts()
    {
        return Product::whereHas('branch', function ($query) {
            $query->where('company_id', $this->company_id);
        })->where('is_available', true)
          ->where('stock', '>', 0)
          ->get();
    }

    /**
     * Get low stock products for this manager's company.
     *
     * @param int $threshold
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLowStockProducts(int $threshold = 10)
    {
        return Product::whereHas('branch', function ($query) {
            $query->where('company_id', $this->company_id);
        })->where('stock', '<=', $threshold)
          ->where('stock', '>', 0)
          ->get();
    }

    /**
     * Get out of stock products for this manager's company.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOutOfStockProducts()
    {
        return Product::whereHas('branch', function ($query) {
            $query->where('company_id', $this->company_id);
        })->where('stock', 0)->get();
    }

    /**
     * Scope to filter products managers by company.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $companyId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Get the vendor user who owns the company this products manager belongs to.
     *
     * @return \App\Models\User|null
     */
    public function getVendorUser()
    {
        return $this->company ? $this->company->user : null;
    }

    /**
     * Check if the vendor's license is active.
     *
     * @return bool
     */
    public function hasActiveVendorLicense(): bool
    {
        $vendor = $this->getVendorUser();

        if (!$vendor) {
            return false;
        }

        // Check if vendor has a license
        if (!$vendor->hasLicense()) {
            return false;
        }

        // Check if the license status is active
        return $vendor->getLicenseStatus() === 'active';
    }

    /**
     * Get the vendor's license status.
     *
     * @return string|null
     */
    public function getVendorLicenseStatus(): ?string
    {
        $vendor = $this->getVendorUser();

        if (!$vendor) {
            return null;
        }

        return $vendor->getLicenseStatus();
    }

    /**
     * Get statistics for this products manager.
     *
     * @return array
     */
    public function getStatistics(): array
    {
        return [
            'total_products' => $this->getTotalProductsCount(),
            'total_branches' => $this->getTotalBranchesCount(),
            'available_products' => $this->getAvailableProducts()->count(),
            'low_stock_products' => $this->getLowStockProducts()->count(),
            'out_of_stock_products' => $this->getOutOfStockProducts()->count(),
        ];
    }
}
