<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Provider extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'business_name',
        'business_type',
        'registration_number',
        'description',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'website',
        'logo',
        'status',
        'is_verified',
        'average_rating',
        'total_ratings',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_verified' => 'boolean',
    ];

    /**
     * Get the user that owns the provider.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the provider's products.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the ratings for this provider.
     */
    public function ratings()
    {
        return $this->hasMany(ProviderRating::class, 'provider_id', 'id');
    }
}
