<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderProfile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'provider_id',
        'business_name',
        'logo',
        'description',
        'contact_email',
        'contact_phone',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'status',
        'company_name',
        'product_name',
        'stock',
        'price',
        'is_active',
    ];

    /**
     * Get the user that owns the provider profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the products for the provider.
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'user_id', 'user_id');
    }

    /**
     * Get the provider products pivot records.
     */
    public function providerProducts()
    {
        return $this->hasMany(ProviderProduct::class, 'provider_id');
    }

    /**
     * Get the active provider products pivot records.
     */
    public function activeProviderProducts()
    {
        return $this->providerProducts()
            ->join('products', 'provider_products.product_id', '=', 'products.id')
            ->where('products.is_available', true)
            ->select('provider_products.*');
    }

    /**
     * Get the products through the provider_products table.
     */
    public function productsThrough()
    {
        return $this->belongsToMany(Product::class, 'provider_products', 'provider_id', 'product_id');
    }
}
