@php
    $currentLocale = app()->getLocale();
    $supportedLocales = config('app.supported_locales', [
        'en' => ['name' => 'English', 'native' => 'English', 'flag' => '🇺🇸'],
        'ar' => ['name' => 'Arabic', 'native' => 'العربية', 'flag' => '🇸🇦']
    ]);
@endphp

<div class="language-switcher-sidebar">
    <!-- Language Section Header -->
    <div class="px-3 py-2 mb-2">
        <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
            {{ __('service_provider.language') }}
        </h3>
    </div>
    
    <!-- Language Options -->
    <div class="space-y-1">
        @foreach($supportedLocales as $locale => $details)
            <button class="flex items-center w-full px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 language-option-sidebar {{ $locale === $currentLocale ? 'bg-blue-100 text-[#53D2DC] dark:bg-blue-900 dark:text-blue-200' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}"
                    onclick="switchLanguageSidebar('{{ $locale }}'); return false;">
                <span class="flag-icon mr-3 text-base">{{ $details['flag'] }}</span>
                <span class="flex-1 text-left">{{ $details['native'] }}</span>
                @if($locale === $currentLocale)
                    <i class="fas fa-check text-[#53D2DC] dark:text-blue-200 text-xs"></i>
                @endif
            </button>
        @endforeach
    </div>
</div>

<script>
function switchLanguageSidebar(locale) {
    // Show loading state for the clicked button
    const buttons = document.querySelectorAll('.language-option-sidebar');
    buttons.forEach(btn => {
        if (btn.onclick.toString().includes(locale)) {
            const originalContent = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-3"></i>{{ __('service_provider.switching') }}';
            btn.disabled = true;
        }
    });
    
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
            // Restore button states
            buttons.forEach(btn => {
                if (btn.onclick.toString().includes(locale)) {
                    btn.innerHTML = originalContent;
                    btn.disabled = false;
                }
            });
        }
    })
    .catch(error => {
        console.error('Error switching language:', error);
        // Restore button states
        buttons.forEach(btn => {
            if (btn.onclick.toString().includes(locale)) {
                btn.innerHTML = originalContent;
                btn.disabled = false;
            }
        });
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
/* RTL Support for sidebar language switcher */
[dir="rtl"] .language-switcher-sidebar .flag-icon {
    margin-left: 0.75rem;
    margin-right: 0;
}

[dir="rtl"] .language-switcher-sidebar .mr-3 {
    margin-left: 0.75rem;
    margin-right: 0;
}

[dir="rtl"] .language-switcher-sidebar .text-left {
    text-align: right;
}

/* Ensure proper spacing and alignment */
.language-switcher-sidebar .language-option-sidebar {
    justify-content: flex-start;
}

.language-switcher-sidebar .language-option-sidebar:hover {
    transform: translateX(2px);
}

[dir="rtl"] .language-switcher-sidebar .language-option-sidebar:hover {
    transform: translateX(-2px);
}
</style>
