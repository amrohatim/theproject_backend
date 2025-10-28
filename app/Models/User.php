<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\ResetPasswordNotification;

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
        'phone_verified',
        'phone_verified_at',
        'email_verified_at',
        'registration_step',
        'registration_data',
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
            'phone_verified' => 'boolean',
            'phone_verified_at' => 'datetime',
            'registration_data' => 'array',
        ];
    }

    /**
     * Get the company associated with the user (for vendors).
     */
    public function company()
    {
        return $this->hasOne(Company::class);
    }

    /**
     * Get the provider profile associated with the user.
     */
    public function provider()
    {
        return $this->hasOne(Provider::class);
    }

    /**
     * Get the merchant profile associated with the user.
     */
    public function merchant()
    {
        return $this->hasOne(Merchant::class);
    }

    /**
     * Get the service provider profile associated with the user.
     */
    public function serviceProvider()
    {
        return $this->hasOne(ServiceProvider::class);
    }

    /**
     * Get the products manager profile associated with the user.
     */
    public function productsManager()
    {
        return $this->hasOne(ProductsManager::class);
    }

    /**
     * Get the licenses associated with the user.
     */
    public function licenses()
    {
        return $this->hasMany(License::class);
    }

    /**
     * Get the user's active license.
     */
    public function activeLicense()
    {
        return $this->hasOne(License::class)->where('status', 'active')->latest();
    }

    /**
     * Get the user's latest license regardless of status.
     */
    public function latestLicense()
    {
        return $this->hasOne(License::class)->latest();
    }



    /**
     * Get the vendor locations associated with the user.
     */
    public function vendorLocations()
    {
        return $this->hasMany(VendorLocation::class);
    }

    /**
     * Send the password reset notification.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Check if user is a vendor.
     */
    public function isVendor(): bool
    {
        return $this->role === 'vendor';
    }

    /**
     * Check if user is a provider.
     */
    public function isProvider(): bool
    {
        return $this->role === 'provider';
    }

    /**
     * Check if user is a merchant.
     */
    public function isMerchant(): bool
    {
        return $this->role === 'merchant';
    }

    /**
     * Check if user is a service provider.
     */
    public function isServiceProvider(): bool
    {
        return $this->role === 'service_provider';
    }

    /**
     * Check if user is a products manager.
     */
    public function isProductsManager(): bool
    {
        return $this->role === 'products_manager';
    }



    /**
     * Check if user has completed registration.
     */
    public function hasCompletedRegistration(): bool
    {
        return in_array($this->registration_step, ['license_completed', 'verified']);
    }

    /**
     * Check if user's phone is verified.
     */
    public function hasVerifiedPhone(): bool
    {
        return $this->phone_verified;
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
     * Check if the user has an active license.
     *
     * @return bool
     */
    public function hasActiveLicense(): bool
    {
        return $this->licenses()->where('status', 'active')->exists();
    }

    /**
     * Get the user's license status.
     * Vendors are excluded from license requirements.
     *
     * @return string|null
     */
    public function getLicenseStatus(): ?string
    {
        // Vendors don't require licenses
        if ($this->role === 'vendor') {
            return null;
        }

        $license = $this->latestLicense;
        return $license ? $license->status : null;
    }

    /**
     * Check if the user has any license record.
     * Vendors are excluded from license requirements.
     *
     * @return bool
     */
    public function hasLicense(): bool
    {
        // Vendors don't require licenses
        if ($this->role === 'vendor') {
            return true; // Return true to bypass license checks
        }

        return $this->licenses()->exists();
    }

    /**
     * Get the branches owned by the user.
     */
    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    /**
     * Get the companies owned by the user.
     */
    public function companies()
    {
        return $this->hasMany(Company::class);
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
     * Get the provider record for the user.
     */
    public function providerRecord()
    {
        return $this->hasOne(Provider::class);
    }

    /**
     * Get the merchant record for the user.
     */
    public function merchantRecord()
    {
        return $this->hasOne(Merchant::class);
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

    /**
     * Get the locations for the user.
     */
    public function locations()
    {
        return $this->hasMany(UserLocation::class);
    }

    /**
     * Send email verification notification.
     */
    public function sendEmailVerificationNotification()
    {
        $emailVerificationService = app(\App\Services\EmailVerificationService::class);
        return $emailVerificationService->sendVerificationEmail($this, $this->role);
    }
}
