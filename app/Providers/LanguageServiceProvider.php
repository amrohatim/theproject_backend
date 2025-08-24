<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Helpers\LanguageHelper;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;

class LanguageServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register LanguageHelper as singleton
        $this->app->singleton('language.helper', function ($app) {
            return new LanguageHelper();
        });
        
        // Create alias for easier access
        $this->app->alias('language.helper', LanguageHelper::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register Blade directives for language support
        $this->registerBladeDirectives();
        
        // Share language data with all views
        $this->shareLanguageDataWithViews();
        
        // Register view composers
        $this->registerViewComposers();
    }
    
    /**
     * Register custom Blade directives for language support.
     */
    protected function registerBladeDirectives(): void
    {
        // @lang directive for quick translations
        Blade::directive('lang', function ($expression) {
            return "<?php echo __($expression); ?>";
        });
        
        // @rtl directive to check if current language is RTL
        Blade::directive('rtl', function () {
            return "<?php if(app('language.helper')->isRtl()): ?>";
        });
        
        // @endrtl directive
        Blade::directive('endrtl', function () {
            return "<?php endif; ?>";
        });
        
        // @ltr directive to check if current language is LTR
        Blade::directive('ltr', function () {
            return "<?php if(!app('language.helper')->isRtl()): ?>";
        });
        
        // @endltr directive
        Blade::directive('endltr', function () {
            return "<?php endif; ?>";
        });
        
        // @direction directive to get text direction
        Blade::directive('direction', function () {
            return "<?php echo app('language.helper')->getDirection(); ?>";
        });
        
        // @textAlign directive to get text alignment
        Blade::directive('textAlign', function () {
            return "<?php echo app('language.helper')->getTextAlign(); ?>";
        });
        
        // @floatStart directive for responsive float start
        Blade::directive('floatStart', function () {
            return "<?php echo app('language.helper')->getFloatStart(); ?>";
        });
        
        // @floatEnd directive for responsive float end
        Blade::directive('floatEnd', function () {
            return "<?php echo app('language.helper')->getFloatEnd(); ?>";
        });
        
        // @arabicNumbers directive to convert numbers to Arabic
        Blade::directive('arabicNumbers', function ($expression) {
            return "<?php echo app('language.helper')->convertToArabicNumbers($expression); ?>";
        });
        
        // @localizedDate directive for localized date formatting
        Blade::directive('localizedDate', function ($expression) {
            return "<?php echo app('language.helper')->formatLocalizedDate($expression); ?>";
        });
        
        // @localizedNumber directive for localized number formatting
        Blade::directive('localizedNumber', function ($expression) {
            return "<?php echo app('language.helper')->formatLocalizedNumber($expression); ?>";
        });
    }
    
    /**
     * Share language data with all views.
     */
    protected function shareLanguageDataWithViews(): void
    {
        View::composer('*', function ($view) {
            // Only execute if the application is properly booted
            if (!app()->isBooted()) {
                return;
            }
            
            try {
                // Debug: Log view composer execution
                \Log::debug('LanguageServiceProvider View Composer executed', [
                    'view_name' => $view->getName(),
                    'current_locale_from_helper' => LanguageHelper::getCurrentLocale(),
                    'current_locale_from_app' => \App::getLocale(),
                    'session_locale' => session('locale'),
                    'request_lang' => request('lang')
                ]);
                
                $view->with([
                    'currentLocale' => LanguageHelper::getCurrentLocale(),
                    'currentLanguage' => LanguageHelper::getCurrentLanguageInfo(),
                    'supportedLocales' => LanguageHelper::getSupportedLocales(),
                    'isRtl' => LanguageHelper::isRtl(),
                    'direction' => LanguageHelper::getDirection(),
                    'textAlign' => LanguageHelper::getTextAlign(),
                    'floatStart' => LanguageHelper::getFloatStart(),
                    'floatEnd' => LanguageHelper::getFloatEnd(),
                    'bodyClasses' => LanguageHelper::getDirectionalClasses(),
                    'htmlAttributes' => LanguageHelper::getHtmlAttributes(),
                ]);
            } catch (\Exception $e) {
                // Log any errors but don't break the application
                \Log::error('LanguageServiceProvider View Composer error: ' . $e->getMessage());
            }
        });
    }
    
    /**
     * Register view composers for specific components.
     */
    protected function registerViewComposers(): void
    {
        // Language switcher component composer
        View::composer('components.language-switcher', function ($view) {
            if (!app()->isBooted()) {
                return;
            }
            
            try {
                $languageHelper = app('language.helper');
                
                $view->with([
                    'currentLocale' => LanguageHelper::getCurrentLocale(),
                    'supportedLocales' => LanguageHelper::getSupportedLocales(),
                    'languageSwitcherData' => LanguageHelper::getLanguageSwitcherData(),
                ]);
            } catch (\Exception $e) {
                \Log::error('Language switcher view composer error: ' . $e->getMessage());
            }
        });
        
        // Layout composer for RTL/LTR classes
        View::composer(['layouts.*', 'app'], function ($view) {
            if (!app()->isBooted()) {
                return;
            }
            
            try {
                $view->with([
                    'bodyClasses' => LanguageHelper::getDirectionalClasses(),
                    'htmlAttributes' => LanguageHelper::getHtmlAttributes(),
                ]);
            } catch (\Exception $e) {
                \Log::error('Layout view composer error: ' . $e->getMessage());
            }
        });
    }
}