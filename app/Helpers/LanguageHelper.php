<?php

namespace App\Helpers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageHelper
{
    /**
     * Get all supported locales
     *
     * @return array
     */
    public static function getSupportedLocales(): array
    {
        return config('app.supported_locales', [
            'en' => [
                'name' => 'English',
                'native' => 'English',
                'flag' => '🇺🇸',
                'rtl' => false,
            ],
            'ar' => [
                'name' => 'Arabic',
                'native' => 'العربية',
                'flag' => '🇸🇦',
                'rtl' => true,
            ],
        ]);
    }
    
    /**
     * Get current locale
     *
     * @return string
     */
    public static function getCurrentLocale(): string
    {
        return App::getLocale();
    }
    
    /**
     * Get current locale information
     *
     * @return array
     */
    public static function getCurrentLocaleInfo(): array
    {
        $currentLocale = App::getLocale();
        $supportedLocales = self::getSupportedLocales();
        
        return $supportedLocales[$currentLocale] ?? $supportedLocales['en'];
    }
    
    /**
     * Get current language information (alias for getCurrentLocaleInfo)
     *
     * @return array
     */
    public static function getCurrentLanguageInfo(): array
    {
        return self::getCurrentLocaleInfo();
    }
    
    /**
     * Check if current locale is RTL
     *
     * @return bool
     */
    public static function isRtl(): bool
    {
        $localeInfo = self::getCurrentLocaleInfo();
        return $localeInfo['rtl'] ?? false;
    }
    
    /**
     * Get direction attribute for HTML
     *
     * @return string
     */
    public static function getDirection(): string
    {
        return self::isRtl() ? 'rtl' : 'ltr';
    }
    
    /**
     * Get text alignment class for current locale
     *
     * @return string
     */
    public static function getTextAlign(): string
    {
        return self::isRtl() ? 'text-right' : 'text-left';
    }
    
    /**
     * Get float direction for current locale
     *
     * @return string
     */
    public static function getFloatDirection(): string
    {
        return self::isRtl() ? 'float-right' : 'float-left';
    }
    
    /**
     * Get opposite float direction for current locale
     *
     * @return string
     */
    public static function getOppositeFloatDirection(): string
    {
        return self::isRtl() ? 'float-left' : 'float-right';
    }
    
    /**
     * Get margin/padding direction classes
     *
     * @param string $property (margin or padding)
     * @param string $side (left or right)
     * @param string $size (sm, md, lg, etc.)
     * @return string
     */
    public static function getDirectionalClass(string $property, string $side, string $size = ''): string
    {
        $actualSide = $side;
        
        if (self::isRtl()) {
            $actualSide = $side === 'left' ? 'right' : ($side === 'right' ? 'left' : $side);
        }
        
        $prefix = $property === 'margin' ? 'm' : 'p';
        $sidePrefix = $actualSide === 'left' ? 'l' : ($actualSide === 'right' ? 'r' : $actualSide);
        $sizeClass = $size ? "-{$size}" : '';
        
        return "{$prefix}{$sidePrefix}{$sizeClass}";
    }
    
    /**
     * Get language switcher URL
     *
     * @param string $locale
     * @return string
     */
    public static function getLanguageSwitchUrl(string $locale): string
    {
        $currentUrl = request()->fullUrl();
        $separator = strpos($currentUrl, '?') !== false ? '&' : '?';
        
        return $currentUrl . $separator . 'lang=' . $locale;
    }
    
    /**
     * Switch application locale
     *
     * @param string $locale
     * @return bool
     */
    public static function switchLocale(string $locale): bool
    {
        $supportedLocales = array_keys(self::getSupportedLocales());
        
        if (!in_array($locale, $supportedLocales)) {
            return false;
        }
        
        App::setLocale($locale);
        Session::put('locale', $locale);
        
        // Update user preference if authenticated
        if (auth()->check()) {
            auth()->user()->update(['locale' => $locale]);
        }
        
        return true;
    }
    
    /**
     * Get localized date format
     *
     * @return string
     */
    public static function getDateFormat(): string
    {
        $formats = [
            'en' => 'M d, Y',
            'ar' => 'd/m/Y',
        ];
        
        return $formats[App::getLocale()] ?? $formats['en'];
    }
    
    /**
     * Get localized time format
     *
     * @return string
     */
    public static function getTimeFormat(): string
    {
        $formats = [
            'en' => 'h:i A',
            'ar' => 'H:i',
        ];
        
        return $formats[App::getLocale()] ?? $formats['en'];
    }
    
    /**
     * Get localized datetime format
     *
     * @return string
     */
    public static function getDateTimeFormat(): string
    {
        return self::getDateFormat() . ' ' . self::getTimeFormat();
    }
    
    /**
     * Format number according to locale
     *
     * @param float $number
     * @param int $decimals
     * @return string
     */
    public static function formatNumber(float $number, int $decimals = 2): string
    {
        $locale = App::getLocale();
        
        if ($locale === 'ar') {
            // Arabic number formatting
            return number_format($number, $decimals, '٫', '٬');
        }
        
        // Default English formatting
        return number_format($number, $decimals, '.', ',');
    }
    
    /**
     * Convert numbers to Arabic-Indic digits
     *
     * @param string $text
     * @return string
     */
    public static function toArabicNumbers(string $text): string
    {
        $arabicNumbers = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $englishNumbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        
        return str_replace($englishNumbers, $arabicNumbers, $text);
    }
    
    /**
     * Convert Arabic-Indic digits to English numbers
     *
     * @param string $text
     * @return string
     */
    public static function toEnglishNumbers(string $text): string
    {
        $arabicNumbers = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $englishNumbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        
        return str_replace($arabicNumbers, $englishNumbers, $text);
    }
    
    /**
     * Get CSS classes for RTL support
     *
     * @return string
     */
    public static function getRtlClasses(): string
    {
        if (self::isRtl()) {
            return 'rtl text-right';
        }
        
        return 'ltr text-left';
    }
    
    /**
     * Get Bootstrap RTL classes
     *
     * @return string
     */
    public static function getBootstrapRtlClasses(): string
    {
        if (self::isRtl()) {
            return 'text-end';
        }
        
        return 'text-start';
    }
    
    /**
     * Get Tailwind RTL classes
     *
     * @return string
     */
    public static function getTailwindRtlClasses(): string
    {
        if (self::isRtl()) {
            return 'text-right rtl';
        }
        
        return 'text-left ltr';
    }
    
    /**
     * Get float start direction for current locale
     *
     * @return string
     */
    public static function getFloatStart(): string
    {
        return self::isRtl() ? 'float-right' : 'float-left';
    }
    
    /**
     * Get float end direction for current locale
     *
     * @return string
     */
    public static function getFloatEnd(): string
    {
        return self::isRtl() ? 'float-left' : 'float-right';
    }
    
    /**
     * Convert numbers to Arabic-Indic digits (alias)
     *
     * @param string $text
     * @return string
     */
    public static function convertToArabicNumbers(string $text): string
    {
        return self::toArabicNumbers($text);
    }
    
    /**
     * Format localized date
     *
     * @param mixed $expression - Can be a date object, string, or expression like "now(), 'Y-m-d'"
     * @param string $format
     * @return string
     */
    public static function formatLocalizedDate($expression, string $format = null): string
    {
        // If expression contains comma, it's likely "date, format" format
        if (is_string($expression) && strpos($expression, ',') !== false) {
            // Parse expression like "now(), 'Y-m-d'"
            $parts = array_map('trim', explode(',', $expression));
            $dateStr = trim($parts[0], "'\"");
            $format = isset($parts[1]) ? trim($parts[1], "'\"()") : null;
            
            // Handle now() function
            if ($dateStr === 'now()' || $dateStr === 'now') {
                $date = now();
            } else {
                $date = $dateStr;
            }
        } else {
            $date = $expression;
        }
        
        $format = $format ?: self::getDateFormat();
        
        // Convert to Carbon/DateTime if needed
        if (is_string($date)) {
            if ($date === 'now()' || $date === 'now') {
                $dateObj = now();
            } else {
                $dateObj = new \DateTime($date);
            }
        } elseif ($date instanceof \DateTime || $date instanceof \Carbon\Carbon) {
            $dateObj = $date;
        } else {
            $dateObj = now();
        }
        
        return $dateObj->format($format);
    }
    
    /**
     * Format localized number
     *
     * @param float $number
     * @param int $decimals
     * @return string
     */
    public static function formatLocalizedNumber(float $number, int $decimals = 2): string
    {
        return self::formatNumber($number, $decimals);
    }
    
    /**
     * Get language switcher data
     *
     * @return array
     */
    public static function getLanguageSwitcherData(): array
    {
        $supportedLocales = self::getSupportedLocales();
        $currentLocale = self::getCurrentLocale();
        
        $data = [];
        foreach ($supportedLocales as $locale => $info) {
            $data[] = [
                'code' => $locale,
                'name' => $info['name'],
                'native' => $info['native'],
                'flag' => $info['flag'],
                'active' => $locale === $currentLocale,
                'url' => self::getLanguageSwitchUrl($locale),
            ];
        }
        
        return $data;
    }
    
    /**
     * Get directional CSS classes
     *
     * @return string
     */
    public static function getDirectionalClasses(): string
    {
        $classes = [];
        
        if (self::isRtl()) {
            $classes[] = 'rtl';
            $classes[] = 'text-right';
        } else {
            $classes[] = 'ltr';
            $classes[] = 'text-left';
        }
        
        return implode(' ', $classes);
    }
    
    /**
     * Get HTML direction attributes
     *
     * @return array
     */
    public static function getDirectionAttributes(): array
    {
        return [
            'dir' => self::getDirection(),
            'lang' => self::getCurrentLocale(),
        ];
    }
    
    /**
     * Get HTML direction attributes as formatted string
     *
     * @return string
     */
    public static function getHtmlAttributes(): string
    {
        $attributes = self::getDirectionAttributes();
        $formatted = [];
        
        foreach ($attributes as $key => $value) {
            $formatted[] = $key . '="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '"';
        }
        
        return implode(' ', $formatted);
    }
}