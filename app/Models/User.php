<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'profile_image',
        'status',
        'average_rating',
        'total_ratings',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if the user is an admin.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if the user is a vendor.
     *
     * @return bool
     */
    public function isVendor(): bool
    {
        return $this->role === 'vendor';
    }

    /**
     * Check if the user is a customer.
     *
     * @return bool
     */
    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    /**
     * Check if the user is a provider.
     *
     * @return bool
     */
    public function isProvider(): bool
    {
        return $this->role === 'provider';
    }

    /**
     * Get the branches owned by the user.
     */
    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    /**
     * Get the provider profile associated with the user.
     */
    public function provider()
    {
        return $this->hasOne(Provider::class);
    }

    /**
     * Get the companies owned by the user.
     */
    public function companies()
    {
        return $this->hasMany(Company::class);
    }

    /**
     * Get the primary company owned by the user.
     */
    public function company()
    {
        return $this->hasOne(Company::class);
    }

    /**
     * Get the payment methods for the user.
     */
    public function paymentMethods()
    {
        return $this->hasMany(PaymentMethod::class);
    }

    /**
     * Get the payout methods for the user.
     */
    public function payoutMethods()
    {
        return $this->hasMany(PayoutMethod::class);
    }

    /**
     * Get the payout preference for the user.
     */
    public function payoutPreference()
    {
        return $this->hasOne(PayoutPreference::class);
    }

    /**
     * Get the payment transactions for the user.
     */
    public function paymentTransactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    /**
     * Get the products for the user.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the provider profile for the user.
     */
    public function providerProfile()
    {
        return $this->hasOne(ProviderProfile::class);
    }

    /**
     * Get the ratings given by this user as a customer to vendors.
     */
    public function vendorRatingsGiven()
    {
        return $this->hasMany(VendorRating::class, 'customer_id');
    }

    /**
     * Get the ratings received by this user as a vendor from customers.
     */
    public function vendorRatingsReceived()
    {
        return $this->hasMany(VendorRating::class, 'vendor_id');
    }

    /**
     * Get the ratings given by this user as a customer to branches.
     */
    public function branchRatingsGiven()
    {
        return $this->hasMany(BranchRating::class, 'customer_id');
    }

    /**
     * Get the ratings given by this user as a vendor to providers.
     */
    public function providerRatingsGiven()
    {
        return $this->hasMany(ProviderRating::class, 'vendor_id');
    }

    /**
     * Get the ratings received by this user as a provider from vendors.
     */
    public function providerRatingsReceived()
    {
        return $this->hasMany(ProviderRating::class, 'provider_id');
    }
}
