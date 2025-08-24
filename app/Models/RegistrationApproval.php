<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistrationApproval extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'user_type',
        'status',
        'admin_message',
        'reviewed_by',
        'reviewed_at',
        'registration_data',
        'license_file_path',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'registration_data' => 'array',
        'reviewed_at' => 'datetime',
    ];

    /**
     * Get the user that this approval is for.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who reviewed this registration.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get the company associated with this registration (for vendors).
     */
    public function company()
    {
        return $this->hasOneThrough(
            Company::class,
            User::class,
            'id', // Foreign key on users table
            'user_id', // Foreign key on companies table
            'user_id', // Local key on registration_approvals table
            'id' // Local key on users table
        );
    }

    /**
     * Get the provider associated with this registration (for providers).
     */
    public function provider()
    {
        return $this->hasOneThrough(
            Provider::class,
            User::class,
            'id', // Foreign key on users table
            'user_id', // Foreign key on providers table
            'user_id', // Local key on registration_approvals table
            'id' // Local key on users table
        );
    }

    /**
     * Scope to get pending registrations.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get approved registrations.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope to get declined registrations.
     */
    public function scopeDeclined($query)
    {
        return $query->where('status', 'declined');
    }

    /**
     * Scope to get vendor registrations.
     */
    public function scopeVendors($query)
    {
        return $query->where('user_type', 'vendor');
    }

    /**
     * Scope to get provider registrations.
     */
    public function scopeProviders($query)
    {
        return $query->where('user_type', 'provider');
    }
}
