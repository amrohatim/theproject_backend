<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BranchLicense extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'branches_licenses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'branch_id',
        'license_file_path',
        'start_date',
        'end_date',
        'status',
        'uploaded_at',
        'verified_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'uploaded_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    /**
     * Get the branch that owns the license.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Check if the license is active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === 'active' &&
               $this->end_date >= Carbon::today();
    }

    /**
     * Check if the license is expired.
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->end_date < Carbon::today() || $this->status === 'expired';
    }

    /**
     * Check if the license is pending.
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the license is rejected.
     *
     * @return bool
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Get the full URL for the license file.
     *
     * @return string|null
     */
    public function getLicenseFileUrlAttribute(): ?string
    {
        if (!$this->license_file_path) {
            return null;
        }

        return asset('storage/' . $this->license_file_path);
    }

    /**
     * Scope to get only active licenses.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('end_date', '>=', Carbon::today());
    }

    /**
     * Scope to get expired licenses.
     */
    public function scopeExpired($query)
    {
        return $query->where(function ($q) {
            $q->where('end_date', '<', Carbon::today())
              ->orWhere('status', 'expired');
        });
    }
}
