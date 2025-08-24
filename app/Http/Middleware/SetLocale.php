<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Always log that middleware is running
        \Log::info('SetLocale Middleware EXECUTED for URL: ' . $request->fullUrl());
        
        // Get the locale from the session, URL parameter, or user preference
        $locale = $this->getLocale($request);
        
        // Debug logging
        \Log::info('SetLocale Middleware Debug', [
            'requested_locale' => $locale,
            'url_lang_param' => $request->get('lang'),
            'session_locale' => Session::get('locale'),
            'current_locale_before' => App::getLocale(),
            'is_valid' => $this->isValidLocale($locale)
        ]);
        
        // Validate the locale
        if ($this->isValidLocale($locale)) {
            App::setLocale($locale);
            Session::put('locale', $locale);
            \Log::info('Locale successfully set to: ' . $locale . ', current locale is now: ' . App::getLocale());
        } else {
            \Log::warning('Invalid locale attempted: ' . $locale);
        }
        
        return $next($request);
    }
    
    /**
     * Get the locale from various sources
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    private function getLocale(Request $request): string
    {
        // Priority order:
        // 1. URL parameter 'lang'
        // 2. Session stored locale
        // 3. User's preferred language (if authenticated)
        // 4. Browser's Accept-Language header
        // 5. Default application locale
        
        // Check URL parameter
        if ($request->has('lang')) {
            return $request->get('lang');
        }
        
        // Check session
        if (Session::has('locale')) {
            return Session::get('locale');
        }
        
        // Check authenticated user's preference
        if (auth()->check() && auth()->user()->locale) {
            return auth()->user()->locale;
        }
        
        // Check browser's Accept-Language header
        $browserLocale = $this->getBrowserLocale($request);
        if ($browserLocale) {
            return $browserLocale;
        }
        
        // Return default locale
        return config('app.locale', 'en');
    }
    
    /**
     * Check if the locale is valid
     *
     * @param  string  $locale
     * @return bool
     */
    private function isValidLocale(string $locale): bool
    {
        $supportedLocales = config('app.supported_locales', [
            'en' => ['name' => 'English'],
            'ar' => ['name' => 'Arabic']
        ]);
        return array_key_exists($locale, $supportedLocales);
    }
    
    /**
     * Get locale from browser's Accept-Language header
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    private function getBrowserLocale(Request $request): ?string
    {
        $acceptLanguage = $request->header('Accept-Language');
        
        if (!$acceptLanguage) {
            return null;
        }
        
        // Parse Accept-Language header
        $languages = [];
        foreach (explode(',', $acceptLanguage) as $lang) {
            $parts = explode(';', trim($lang));
            $locale = trim($parts[0]);
            $quality = 1.0;
            
            if (isset($parts[1]) && strpos($parts[1], 'q=') === 0) {
                $quality = (float) substr($parts[1], 2);
            }
            
            $languages[$locale] = $quality;
        }
        
        // Sort by quality
        arsort($languages);
        
        // Find the first supported locale
        foreach (array_keys($languages) as $locale) {
            // Handle locale codes like 'ar-SA' -> 'ar'
            $shortLocale = substr($locale, 0, 2);
            
            if ($this->isValidLocale($shortLocale)) {
                return $shortLocale;
            }
            
            if ($this->isValidLocale($locale)) {
                return $locale;
            }
        }
        
        return null;
    }
}