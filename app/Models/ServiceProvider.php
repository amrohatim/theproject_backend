<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ServiceProvider extends Model
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
        'branch_ids',
        'service_ids',
        'number_of_services',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'branch_ids' => 'array',
        'service_ids' => 'array',
        'number_of_services' => 'integer',
    ];

    /**
     * Get the user that owns the service provider profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the company that the service provider belongs to.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the branches that this service provider can manage.
     * This uses a custom relationship through the branch_ids JSON column.
     */
    public function branches()
    {
        if (empty($this->branch_ids)) {
            return collect();
        }
        
        return Branch::whereIn('id', $this->branch_ids)->get();
    }

    /**
     * Get the services that this service provider can manage.
     * This uses a custom relationship through the service_ids JSON column.
     */
    public function services()
    {
        if (empty($this->service_ids)) {
            return collect();
        }
        
        return Service::whereIn('id', $this->service_ids)->get();
    }

    /**
     * Check if the service provider can manage a specific branch.
     *
     * @param int $branchId
     * @return bool
     */
    public function canManageBranch(int $branchId): bool
    {
        return in_array($branchId, $this->branch_ids ?? []);
    }

    /**
     * Check if the service provider can manage a specific service.
     *
     * @param int $serviceId
     * @return bool
     */
    public function canManageService(int $serviceId): bool
    {
        return in_array($serviceId, $this->service_ids ?? []);
    }

    /**
     * Add a branch to the service provider's manageable branches.
     *
     * @param int $branchId
     * @return void
     */
    public function addBranch(int $branchId): void
    {
        $branchIds = $this->branch_ids ?? [];
        if (!in_array($branchId, $branchIds)) {
            $branchIds[] = $branchId;
            $this->update(['branch_ids' => $branchIds]);
        }
    }

    /**
     * Remove a branch from the service provider's manageable branches.
     *
     * @param int $branchId
     * @return void
     */
    public function removeBranch(int $branchId): void
    {
        $branchIds = $this->branch_ids ?? [];
        $branchIds = array_filter($branchIds, fn($id) => $id !== $branchId);
        $this->update(['branch_ids' => array_values($branchIds)]);
    }

    /**
     * Add a service to the service provider's manageable services.
     *
     * @param int $serviceId
     * @return void
     */
    public function addService(int $serviceId): void
    {
        $serviceIds = $this->service_ids ?? [];
        if (!in_array($serviceId, $serviceIds)) {
            $serviceIds[] = $serviceId;
            $this->update([
                'service_ids' => $serviceIds,
                'number_of_services' => count($serviceIds)
            ]);
        }
    }

    /**
     * Remove a service from the service provider's manageable services.
     *
     * @param int $serviceId
     * @return void
     */
    public function removeService(int $serviceId): void
    {
        $serviceIds = $this->service_ids ?? [];
        $serviceIds = array_filter($serviceIds, fn($id) => $id !== $serviceId);
        $serviceIds = array_values($serviceIds);
        $this->update([
            'service_ids' => $serviceIds,
            'number_of_services' => count($serviceIds)
        ]);
    }

    /**
     * Update the number of services count.
     *
     * @return void
     */
    public function updateServiceCount(): void
    {
        $count = count($this->service_ids ?? []);
        $this->update(['number_of_services' => $count]);
    }

    /**
     * Scope to filter service providers by company.
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
     * Scope to filter service providers who can manage a specific branch.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $branchId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCanManageBranch($query, int $branchId)
    {
        return $query->whereJsonContains('branch_ids', $branchId);
    }

    /**
     * Scope to filter service providers who can manage a specific service.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $serviceId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCanManageService($query, int $serviceId)
    {
        return $query->whereJsonContains('service_ids', $serviceId);
    }

    /**
     * Get the vendor user who owns the company this service provider belongs to.
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
}
