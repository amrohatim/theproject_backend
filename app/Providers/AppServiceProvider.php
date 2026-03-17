<?php

namespace App\Providers;

use App\Models\VendorNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Add a blade directive for correctly displaying image paths
        Blade::directive('imagePath', function ($expression) {
            return "<?php echo asset(\App\Helpers\ImageHelper::fixPath($expression)); ?>";
        });

        // Add a blade directive for provider dashboard image paths
        Blade::directive('providerImagePath', function ($expression) {
            return "<?php echo \App\Helpers\ProviderImageHelper::fixPath($expression); ?>";
        });

        // Add a blade directive for provider product images
        Blade::directive('providerProductImage', function ($expression) {
            return "<?php echo \App\Helpers\ProviderImageHelper::getProviderProductImageUrl($expression); ?>";
        });

        // Add a blade directive for user profile images
        Blade::directive('userProfileImage', function ($expression) {
            return "<?php echo \App\Helpers\ProviderImageHelper::getUserProfileImageUrl($expression); ?>";
        });

        View::composer('layouts.dashboard', function ($view) {
            $vendorNotificationPreview = collect();
            $vendorUnreadCount = 0;
            $vendorNotificationsHasAny = false;
            $providerNotificationPreview = collect();
            $providerUnreadCount = 0;
            $providerNotificationsHasAny = false;

            $user = Auth::user();

            if ($user && $user->role === 'vendor') {
                $company = $user->company;

                if ($company) {
                    $baseQuery = VendorNotification::where('recipient_type', VendorNotification::RECIPIENT_VENDOR)
                        ->where('recipient_id', $company->id)
                        ->latest();

                    $vendorNotificationPreview = (clone $baseQuery)->take(10)->get();
                    $vendorUnreadCount = VendorNotification::where('recipient_type', VendorNotification::RECIPIENT_VENDOR)
                        ->where('recipient_id', $company->id)
                        ->where('is_opened', false)
                        ->count();
                    $vendorNotificationsHasAny = $vendorNotificationPreview->isNotEmpty();
                }
            }

            if ($user && $user->role === 'provider') {
                $provider = $user->providerRecord;

                if ($provider) {
                    $baseQuery = VendorNotification::where('recipient_type', VendorNotification::RECIPIENT_PROVIDER)
                        ->where('recipient_id', $provider->id)
                        ->latest();

                    $providerNotificationPreview = (clone $baseQuery)->take(10)->get();
                    $providerUnreadCount = VendorNotification::where('recipient_type', VendorNotification::RECIPIENT_PROVIDER)
                        ->where('recipient_id', $provider->id)
                        ->where('is_opened', false)
                        ->count();
                    $providerNotificationsHasAny = $providerNotificationPreview->isNotEmpty();
                }
            }

            $view->with([
                'vendorNotificationPreview' => $vendorNotificationPreview,
                'vendorUnreadCount' => $vendorUnreadCount,
                'vendorNotificationsHasAny' => $vendorNotificationsHasAny,
                'providerNotificationPreview' => $providerNotificationPreview,
                'providerUnreadCount' => $providerUnreadCount,
                'providerNotificationsHasAny' => $providerNotificationsHasAny,
            ]);
        });

        View::composer('layouts.merchant', function ($view) {
            $merchantNotificationPreview = collect();
            $merchantUnreadCount = 0;
            $merchantNotificationsHasAny = false;

            $user = Auth::user();

            if ($user && $user->role === 'merchant') {
                $merchant = $user->merchantRecord;

                if ($merchant) {
                    $baseQuery = VendorNotification::where('recipient_type', VendorNotification::RECIPIENT_MERCHANT)
                        ->where('recipient_id', $merchant->id)
                        ->latest();

                    $merchantNotificationPreview = (clone $baseQuery)->take(10)->get();
                    $merchantUnreadCount = VendorNotification::where('recipient_type', VendorNotification::RECIPIENT_MERCHANT)
                        ->where('recipient_id', $merchant->id)
                        ->where('is_opened', false)
                        ->count();
                    $merchantNotificationsHasAny = $merchantNotificationPreview->isNotEmpty();
                }
            }

            $view->with([
                'merchantNotificationPreview' => $merchantNotificationPreview,
                'merchantUnreadCount' => $merchantUnreadCount,
                'merchantNotificationsHasAny' => $merchantNotificationsHasAny,
            ]);
        });

        View::composer('layouts.products-manager', function ($view) {
            $productsManagerNotificationPreview = collect();
            $productsManagerUnreadCount = 0;
            $productsManagerNotificationsHasAny = false;

            $user = Auth::user();
            $productsManager = $user?->productsManager;
            $companyId = $productsManager?->company_id;

            if ($user && $user->role === 'products_manager' && $companyId) {
                $baseQuery = VendorNotification::forVendorCompanyRecipient($companyId)->latest();

                $productsManagerNotificationPreview = (clone $baseQuery)->take(10)->get();
                $productsManagerUnreadCount = VendorNotification::forVendorCompanyRecipient($companyId)
                    ->unreadByUser($user->id)
                    ->count();
                $productsManagerNotificationsHasAny = $productsManagerNotificationPreview->isNotEmpty();
            }

            $view->with([
                'productsManagerNotificationPreview' => $productsManagerNotificationPreview,
                'productsManagerUnreadCount' => $productsManagerUnreadCount,
                'productsManagerNotificationsHasAny' => $productsManagerNotificationsHasAny,
            ]);
        });

        View::composer('layouts.service-provider', function ($view) {
            $serviceProviderNotificationPreview = collect();
            $serviceProviderUnreadCount = 0;
            $serviceProviderNotificationsHasAny = false;

            $user = Auth::user();
            $serviceProvider = $user?->serviceProvider;
            $companyId = $serviceProvider?->company_id;
            $branchIds = array_values($serviceProvider?->branch_ids ?? []);

            if ($user && $user->role === 'service_provider' && $companyId && !empty($branchIds)) {
                $baseQuery = VendorNotification::forVendorCompanyRecipient($companyId)
                    ->visibleToServiceProviderBranches($branchIds)
                    ->latest();

                $serviceProviderNotificationPreview = (clone $baseQuery)->take(10)->get();
                $serviceProviderUnreadCount = VendorNotification::forVendorCompanyRecipient($companyId)
                    ->visibleToServiceProviderBranches($branchIds)
                    ->unreadByUser($user->id)
                    ->count();
                $serviceProviderNotificationsHasAny = $serviceProviderNotificationPreview->isNotEmpty();
            }

            $view->with([
                'serviceProviderNotificationPreview' => $serviceProviderNotificationPreview,
                'serviceProviderUnreadCount' => $serviceProviderUnreadCount,
                'serviceProviderNotificationsHasAny' => $serviceProviderNotificationsHasAny,
            ]);
        });
    }
}
