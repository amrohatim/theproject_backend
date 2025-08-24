@php
    $currentLocale = app()->getLocale();
    $supportedLocales = config('app.supported_locales', [
        'en' => ['name' => 'English', 'native' => 'English', 'flag' => '🇺🇸'],
        'ar' => ['name' => 'Arabic', 'native' => 'العربية', 'flag' => '🇸🇦']
    ]);
@endphp

<div class="language-switcher-sp relative">
    <button class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 rounded-md transition-colors duration-200" 
            type="button" 
            id="languageDropdownSP" 
            onclick="toggleLanguageDropdown()">
        <span class="flag-icon mr-2">{{ $supportedLocales[$currentLocale]['flag'] ?? '🌐' }}</span>
        <span class="hidden md:inline">{{ $supportedLocales[$currentLocale]['native'] ?? __('service_provider.language') }}</span>
        <span class="md:hidden">{{ strtoupper($currentLocale) }}</span>
        <i class="fas fa-chevron-down ml-2 text-xs"></i>
    </button>

    <div class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg border border-gray-200 dark:border-gray-700 z-50 hidden" 
         id="languageDropdownMenuSP">
        @foreach($supportedLocales as $locale => $details)
            <a class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 language-option-sp {{ $locale === $currentLocale ? 'bg-blue-50 dark:bg-blue-900 text-[#53D2DC] dark:text-blue-200' : '' }}"
               href="#"
               onclick="switchLanguageSP('{{ $locale }}'); return false;">
                <span class="flag-icon mr-3">{{ $details['flag'] }}</span>
                <span class="flex-1">{{ $details['native'] }}</span>
                @if($locale === $currentLocale)
                    <i class="fas fa-check text-[#53D2DC] dark:text-blue-200"></i>
                @endif
            </a>
        @endforeach
    </div>
</div>

<script>
function toggleLanguageDropdown() {
    const dropdown = document.getElementById('languageDropdownMenuSP');
    dropdown.classList.toggle('hidden');
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function closeDropdown(e) {
        if (!e.target.closest('.language-switcher-sp')) {
            dropdown.classList.add('hidden');
            document.removeEventListener('click', closeDropdown);
        }
    });
}

function switchLanguageSP(locale) {
    // Show loading state
    const button = document.getElementById('languageDropdownSP');
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>{{ __('service_provider.switching') }}';
    button.disabled = true;
    
    // Hide dropdown
    document.getElementById('languageDropdownMenuSP').classList.add('hidden');
    
    // Make AJAX request to switch language
    fetch('{{ route('language.switch.post') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ locale: locale })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update page direction and reload
            document.documentElement.dir = data.data.direction;
            document.documentElement.lang = locale;
            
            // Reload page to apply language changes
            window.location.reload();
        } else {
            console.error('Language switch failed:', data.message);
            // Restore button state
            button.innerHTML = originalContent;
            button.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error switching language:', error);
        // Restore button state
        button.innerHTML = originalContent;
        button.disabled = false;
    });
}

// Initialize RTL support on page load
document.addEventListener('DOMContentLoaded', function() {
    const currentLocale = '{{ $currentLocale }}';
    const rtlLocales = ['ar', 'he', 'fa', 'ur']; // Add more RTL locales as needed
    
    if (rtlLocales.includes(currentLocale)) {
        document.documentElement.dir = 'rtl';
        document.body.classList.add('rtl');
    } else {
        document.documentElement.dir = 'ltr';
        document.body.classList.remove('rtl');
    }
    
    // Set language attribute
    document.documentElement.lang = currentLocale;
});
</script>

<style>
/* RTL Support for language switcher */
[dir="rtl"] .language-switcher-sp .absolute {
    right: auto;
    left: 0;
}

[dir="rtl"] .language-switcher-sp .flag-icon {
    margin-left: 0.5rem;
    margin-right: 0;
}

[dir="rtl"] .language-switcher-sp .ml-2 {
    margin-left: 0;
    margin-right: 0.5rem;
}

[dir="rtl"] .language-switcher-sp .mr-2 {
    margin-right: 0;
    margin-left: 0.5rem;
}

[dir="rtl"] .language-switcher-sp .mr-3 {
    margin-right: 0;
    margin-left: 0.75rem;
}
</style>
