<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'logo',
        'website',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'tax_id',
        'status',
        'business_type',
        'registration_number',
        'can_deliver',
        'view_count',
        'order_count',
        'average_rating',
        'rating_count',
        'vendor_score',
        'last_score_calculation',
        // New registration fields
        'contact_number_1',
        'contact_number_2',
        'emirate',
        'street',
        'delivery_capability',
        'delivery_areas',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'string',
        'can_deliver' => 'boolean',
        'view_count' => 'integer',
        'order_count' => 'integer',
        'average_rating' => 'float',
        'rating_count' => 'integer',
        'vendor_score' => 'integer',
        'last_score_calculation' => 'datetime',
        'delivery_capability' => 'boolean',
        'delivery_areas' => 'array',
    ];

    /**
     * Get the user that owns the company.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the branches for the company.
     */
    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    /**
     * Get the logo attribute with full URL.
     *
     * @param  string|null  $value
     * @return string|null
     */
    public function getLogoAttribute($value)
    {
        return \App\Helpers\ImageHelper::getFullImageUrl($value);
    }

    /**
     * Override the toArray method to include user rating data.
     */
    public function toArray()
    {
        $array = parent::toArray();

        // If user relationship is loaded, use user's rating data
        if ($this->relationLoaded('user') && $this->user) {
            $array['average_rating'] = $this->user->average_rating ?? 0.0;
            $array['rating_count'] = $this->user->total_ratings ?? 0;
        }

        return $array;
    }
}
