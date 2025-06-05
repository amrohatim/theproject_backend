<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

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
    }
}
