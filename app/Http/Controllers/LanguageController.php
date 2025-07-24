<?php

namespace App\Http\Controllers;

use App\Helpers\LanguageHelper;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class LanguageController extends Controller
{
    /**
     * Get all supported languages
     *
     * @return JsonResponse
     */
    public function getSupportedLanguages(): JsonResponse
    {
        $languages = LanguageHelper::getSupportedLocales();
        $currentLocale = App::getLocale();
        
        return response()->json([
            'success' => true,
            'data' => [
                'current_locale' => $currentLocale,
                'current_info' => LanguageHelper::getCurrentLocaleInfo(),
                'supported_languages' => $languages,
                'is_rtl' => LanguageHelper::isRtl(),
                'direction' => LanguageHelper::getDirection(),
            ]
        ]);
    }
    
    /**
     * Switch language via API
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function switchLanguage(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'locale' => 'required|string|in:' . implode(',', array_keys(LanguageHelper::getSupportedLocales()))
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid locale provided',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $locale = $request->input('locale');
        $switched = LanguageHelper::switchLocale($locale);
        
        if (!$switched) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to switch language'
            ], 400);
        }
        
        return response()->json([
            'success' => true,
            'message' => __('messages.language') . ' ' . __('messages.updated'),
            'data' => [
                'current_locale' => App::getLocale(),
                'current_info' => LanguageHelper::getCurrentLocaleInfo(),
                'is_rtl' => LanguageHelper::isRtl(),
                'direction' => LanguageHelper::getDirection(),
            ]
        ]);
    }
    
    /**
     * Switch language via web request (with redirect)
     *
     * @param Request $request
     * @param string $locale
     * @return RedirectResponse
     */
    public function switchLanguageWeb(Request $request, string $locale): RedirectResponse
    {
        $supportedLocales = array_keys(LanguageHelper::getSupportedLocales());
        
        if (!in_array($locale, $supportedLocales)) {
            return redirect()->back()->with('error', 'Unsupported language');
        }
        
        $switched = LanguageHelper::switchLocale($locale);
        
        if (!$switched) {
            return redirect()->back()->with('error', 'Failed to switch language');
        }
        
        $message = __('messages.language') . ' ' . __('messages.updated');
        
        // Redirect back to the previous page or home
        $redirectUrl = $request->header('referer', url('/'));
        
        // Remove any existing lang parameter from the URL
        $redirectUrl = preg_replace('/[?&]lang=[^&]*/', '', $redirectUrl);
        
        return redirect($redirectUrl)->with('success', $message);
    }
    
    /**
     * Get current language information
     *
     * @return JsonResponse
     */
    public function getCurrentLanguage(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'current_locale' => App::getLocale(),
                'current_info' => LanguageHelper::getCurrentLocaleInfo(),
                'is_rtl' => LanguageHelper::isRtl(),
                'direction' => LanguageHelper::getDirection(),
                'text_align' => LanguageHelper::getTextAlign(),
                'date_format' => LanguageHelper::getDateFormat(),
                'time_format' => LanguageHelper::getTimeFormat(),
                'datetime_format' => LanguageHelper::getDateTimeFormat(),
            ]
        ]);
    }
    
    /**
     * Get RTL/LTR information
     *
     * @return JsonResponse
     */
    public function getDirectionInfo(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'is_rtl' => LanguageHelper::isRtl(),
                'direction' => LanguageHelper::getDirection(),
                'text_align' => LanguageHelper::getTextAlign(),
                'float_direction' => LanguageHelper::getFloatDirection(),
                'opposite_float_direction' => LanguageHelper::getOppositeFloatDirection(),
                'rtl_classes' => LanguageHelper::getRtlClasses(),
                'bootstrap_rtl_classes' => LanguageHelper::getBootstrapRtlClasses(),
                'tailwind_rtl_classes' => LanguageHelper::getTailwindRtlClasses(),
            ]
        ]);
    }
    
    /**
     * Get language switcher data for frontend
     *
     * @return JsonResponse
     */
    public function getLanguageSwitcherData(): JsonResponse
    {
        $languages = LanguageHelper::getSupportedLocales();
        $currentLocale = App::getLocale();
        $switcherData = [];
        
        foreach ($languages as $locale => $info) {
            $switcherData[] = [
                'locale' => $locale,
                'name' => $info['name'],
                'native' => $info['native'],
                'flag' => $info['flag'],
                'rtl' => $info['rtl'],
                'is_current' => $locale === $currentLocale,
                'switch_url' => LanguageHelper::getLanguageSwitchUrl($locale),
                'api_switch_url' => route('api.language.switch'),
                'web_switch_url' => route('language.switch.web', ['locale' => $locale]),
            ];
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'current_locale' => $currentLocale,
                'languages' => $switcherData,
                'is_rtl' => LanguageHelper::isRtl(),
            ]
        ]);
    }

    /**
     * Get translated content for landing page
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getLandingPageTranslations(Request $request): JsonResponse
    {
        $locale = $request->input('locale', App::getLocale());

        // Validate locale
        $supportedLocales = array_keys(LanguageHelper::getSupportedLocales());
        if (!in_array($locale, $supportedLocales)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid locale provided'
            ], 422);
        }

        // Temporarily switch locale to get translations
        $originalLocale = App::getLocale();
        App::setLocale($locale);

        try {
            $translations = [
                // Page title
                'page_title' => __('messages.dala3chic') . ' - ' . __('messages.hero_title'),

                // Navigation
                'services' => __('messages.services'),
                'about' => __('messages.about'),
                'contact' => __('messages.contact'),
                'login' => __('messages.login'),
                'dashboard_nav' => __('messages.dashboard_nav'),
                'english' => __('messages.english'),
                'arabic' => __('messages.arabic'),

                // Hero Section
                'your_premier' => __('messages.your_premier'),
                'dala3chic' => __('messages.dala3chic'),
                'experience' => __('messages.experience'),
                'hero_title' => __('messages.hero_title'),
                'hero_subtitle' => __('messages.hero_subtitle'),
                'get_started' => __('messages.get_started'),
                'learn_more' => __('messages.learn_more'),
                'go_to_admin_dashboard' => __('messages.go_to_admin_dashboard'),
                'go_to_vendor_dashboard' => __('messages.go_to_vendor_dashboard'),
                'go_to_provider_dashboard' => __('messages.go_to_provider_dashboard'),
                'continue_shopping' => __('messages.continue_shopping'),

                // Stats Section
                'products_available' => __('messages.products_available'),
                'trusted_vendors' => __('messages.trusted_vendors'),
                'happy_customers' => __('messages.happy_customers'),
                'satisfaction_rate' => __('messages.satisfaction_rate'),

                // Features Section
                'why_choose_dala3chic' => __('messages.why_choose_dala3chic'),
                'next_generation_shopping' => __('messages.next_generation_shopping'),
                'secure_transactions' => __('messages.secure_transactions'),
                'secure_transactions_desc' => __('messages.secure_transactions_desc'),
                'fast_delivery' => __('messages.fast_delivery'),
                'fast_delivery_desc' => __('messages.fast_delivery_desc'),
                'customer_support' => __('messages.customer_support'),
                'customer_support_desc' => __('messages.customer_support_desc'),
                'quality_assurance' => __('messages.quality_assurance'),
                'quality_assurance_desc' => __('messages.quality_assurance_desc'),
                'mobile_responsive' => __('messages.mobile_responsive'),
                'mobile_responsive_desc' => __('messages.mobile_responsive_desc'),
                'competitive_pricing' => __('messages.competitive_pricing'),
                'competitive_pricing_desc' => __('messages.competitive_pricing_desc'),

                // CTA Section
                'ready_to_start_shopping' => __('messages.ready_to_start_shopping'),
                'join_thousands_customers' => __('messages.join_thousands_customers'),
                'create_account' => __('messages.create_account'),
                'admin_dashboard' => __('messages.admin_dashboard'),
                'vendor_dashboard' => __('messages.vendor_dashboard'),
                'provider_dashboard' => __('messages.provider_dashboard'),
            ];

            // Get locale info
            $localeInfo = LanguageHelper::getSupportedLocales()[$locale];

            return response()->json([
                'success' => true,
                'locale' => $locale,
                'direction' => $localeInfo['rtl'] ? 'rtl' : 'ltr',
                'is_rtl' => $localeInfo['rtl'],
                'translations' => $translations
            ]);

        } finally {
            // Restore original locale
            App::setLocale($originalLocale);
        }
    }

    /**
     * Format number according to current locale
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function formatNumber(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'number' => 'required|numeric',
            'decimals' => 'integer|min:0|max:10'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input provided',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $number = $request->input('number');
        $decimals = $request->input('decimals', 2);
        
        $formattedNumber = LanguageHelper::formatNumber($number, $decimals);
        
        return response()->json([
            'success' => true,
            'data' => [
                'original' => $number,
                'formatted' => $formattedNumber,
                'locale' => App::getLocale(),
            ]
        ]);
    }
    
    /**
     * Convert text numbers based on locale
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function convertNumbers(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'text' => 'required|string',
            'to' => 'required|string|in:arabic,english'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input provided',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $text = $request->input('text');
        $to = $request->input('to');
        
        $convertedText = $to === 'arabic' 
            ? LanguageHelper::toArabicNumbers($text)
            : LanguageHelper::toEnglishNumbers($text);
        
        return response()->json([
            'success' => true,
            'data' => [
                'original' => $text,
                'converted' => $convertedText,
                'conversion_type' => $to,
                'locale' => App::getLocale(),
            ]
        ]);
    }
}