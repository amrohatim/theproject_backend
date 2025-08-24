# Arabic Language Support for Laravel Application

This document provides comprehensive information about the Arabic language support implementation in your Laravel application.

## 🌟 Features

- ✅ Complete Arabic translation files
- ✅ RTL (Right-to-Left) layout support
- ✅ Language switching functionality
- ✅ Middleware for automatic locale detection
- ✅ Helper functions for language operations
- ✅ Blade directives for easy translation
- ✅ Number and date localization
- ✅ Responsive language switcher component
- ✅ API endpoints for language management

## 📁 File Structure

```
resources/
├── lang/
│   └── ar/
│       ├── auth.php          # Authentication translations
│       ├── validation.php    # Validation messages
│       ├── messages.php      # General UI translations
│       └── passwords.php     # Password-related translations
├── views/
│   ├── components/
│   │   └── language-switcher.blade.php  # Language switcher component
│   ├── layouts/
│   │   └── app-with-language.blade.php  # Layout with language support
│   └── language-demo.blade.php          # Demo page

app/
├── Http/
│   ├── Controllers/
│   │   └── LanguageController.php        # Language management controller
│   └── Middleware/
│       └── SetLocale.php                 # Locale detection middleware
├── Helpers/
│   └── LanguageHelper.php                # Language utility functions
└── Providers/
    └── LanguageServiceProvider.php       # Service provider for language features

public/
└── css/
    └── rtl.css                           # RTL styles for Arabic

config/
└── app.php                               # Updated with supported locales
```

## 🚀 Quick Start

### 1. Language Switching

Use the language switcher component in your Blade templates:

```blade
<x-language-switcher />
```

### 2. Using Translations

```blade
<!-- Standard Laravel translation -->
{{ __('messages.welcome') }}

<!-- Using custom Blade directive -->
@lang('messages.welcome')
```

### 3. RTL/LTR Conditional Content

```blade
@rtl
    <p>This content appears only in RTL languages (Arabic)</p>
@endrtl

@ltr
    <p>This content appears only in LTR languages (English)</p>
@endltr
```

### 4. Number and Date Formatting

```blade
<!-- Convert numbers to Arabic numerals -->
@arabicNumbers(123456)

<!-- Localized number formatting -->
@localizedNumber(1234567.89)

<!-- Localized date formatting -->
@localizedDate(now(), 'Y-m-d')
```

## 🎨 Layout Integration

### Using the Language-Aware Layout

```blade
@extends('layouts.app-with-language')

@section('title', __('messages.page_title'))

@section('content')
    <h1>@lang('messages.welcome')</h1>
    <p>{{ __('messages.description') }}</p>
@endsection
```

### Manual Integration

If you prefer to integrate language support into your existing layout:

```blade
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app('language.helper')->getDirection() }}">
<head>
    <!-- Your head content -->
    
    @if(app('language.helper')->isRtl())
        <link href="{{ asset('css/rtl.css') }}" rel="stylesheet">
    @endif
</head>
<body class="{{ app('language.helper')->getDirectionalClasses() }}">
    <!-- Your content -->
    
    <!-- Language switcher -->
    <x-language-switcher />
</body>
</html>
```

## 🔧 API Endpoints

### Public Language API Routes

```
GET  /api/language/supported              # Get supported languages
POST /api/language/switch                 # Switch language (API)
GET  /api/language/current                # Get current language info
GET  /api/language/rtl-info               # Get RTL information
GET  /api/language/switcher-data          # Get language switcher data
POST /api/language/format-number          # Format numbers
POST /api/language/convert-to-arabic-numbers  # Convert to Arabic numerals
```

### Web Routes

```
GET  /language/{locale}                   # Switch language (web)
POST /language/switch                     # Switch language (AJAX)
GET  /language-demo                       # Demo page
```

## 🛠 Helper Functions

### LanguageHelper Class

```php
// Get current locale
app('language.helper')->getCurrentLocale();

// Check if current language is RTL
app('language.helper')->isRtl();

// Get text direction
app('language.helper')->getDirection(); // 'rtl' or 'ltr'

// Get text alignment
app('language.helper')->getTextAlign(); // 'right' or 'left'

// Get float classes
app('language.helper')->getFloatStart(); // 'float-right' or 'float-left'
app('language.helper')->getFloatEnd();   // 'float-left' or 'float-right'

// Convert numbers to Arabic
app('language.helper')->convertToArabicNumbers('123456');

// Format localized numbers
app('language.helper')->formatLocalizedNumber(1234567.89);
```

## 🎯 Blade Directives

### Available Directives

```blade
@lang('messages.key')                     # Quick translation
@rtl ... @endrtl                          # RTL-only content
@ltr ... @endltr                          # LTR-only content
@direction                                # Get direction (rtl/ltr)
@textAlign                                # Get text alignment
@floatStart                               # Get responsive float start
@floatEnd                                 # Get responsive float end
@arabicNumbers(123)                       # Convert to Arabic numerals
@localizedNumber(1234.56)                 # Format localized number
@localizedDate(now(), 'Y-m-d')            # Format localized date
```

## 🌐 Adding New Languages

### 1. Update Configuration

Add the new locale to `config/app.php`:

```php
'supported_locales' => [
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
    'fr' => [
        'name' => 'French',
        'native' => 'Français',
        'flag' => '🇫🇷',
        'rtl' => false,
    ],
],
```

### 2. Create Translation Files

Create translation files in `resources/lang/{locale}/`:

```
resources/lang/fr/
├── auth.php
├── validation.php
├── messages.php
└── passwords.php
```

### 3. Add RTL Support (if needed)

For RTL languages, update the RTL locales array in the LanguageHelper:

```php
protected $rtlLocales = ['ar', 'he', 'fa', 'ur', 'your_new_rtl_locale'];
```

## 🎨 Customizing Styles

### RTL CSS Customization

Edit `public/css/rtl.css` to customize RTL styles:

```css
/* Custom RTL styles */
[dir="rtl"] .your-custom-class {
    /* Your RTL-specific styles */
}
```

### Language Switcher Styling

Customize the language switcher appearance by modifying the component:

```blade
<!-- resources/views/components/language-switcher.blade.php -->
<style>
.language-switcher .dropdown-toggle {
    /* Your custom styles */
}
</style>
```

## 🔍 Testing

### Demo Page

Visit `/language-demo` to see all language features in action.

### Manual Testing

1. Switch between English and Arabic
2. Verify RTL layout changes
3. Test number and date formatting
4. Check API endpoints
5. Validate form inputs in both languages

## 🐛 Troubleshooting

### Common Issues

1. **Language not switching**: Check middleware registration in `app/Http/Kernel.php`
2. **RTL styles not loading**: Verify `rtl.css` path and ensure it's publicly accessible
3. **Translations not working**: Check translation file syntax and key names
4. **API errors**: Verify CSRF token for POST requests

### Debug Mode

Enable debug logging in your controller:

```php
\Log::info('Language switch attempt', [
    'from' => app()->getLocale(),
    'to' => $request->locale,
    'user_id' => auth()->id(),
]);
```

## 📚 Additional Resources

- [Laravel Localization Documentation](https://laravel.com/docs/localization)
- [RTL CSS Best Practices](https://rtlstyling.com/posts/rtl-styling)
- [Arabic Typography Guidelines](https://arabictypography.com/)

## 🤝 Contributing

To add more translations or improve the language support:

1. Add translations to the appropriate language files
2. Update the demo page with new examples
3. Test thoroughly in both RTL and LTR modes
4. Update this documentation

---

**Note**: This implementation provides a solid foundation for multilingual support. You can extend it further based on your specific requirements.