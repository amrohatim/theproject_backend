@php
    $currentLocale = app()->getLocale();
    $supportedLocales = config('app.supported_locales', [
        'en' => ['name' => 'English', 'native' => 'English', 'flag' => '🇺🇸'],
        'ar' => ['name' => 'Arabic', 'native' => 'العربية', 'flag' => '🇸🇦']
    ]);
@endphp

<div class="language-switcher dropdown">
    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <span class="flag-icon">{{ $supportedLocales[$currentLocale]['flag'] ?? '🌐' }}</span>
        <span class="d-none d-md-inline">{{ $supportedLocales[$currentLocale]['native'] ?? 'Language' }}</span>
        <span class="d-md-none">{{ strtoupper($currentLocale) }}</span>
    </button>

    <ul class="dropdown-menu" aria-labelledby="languageDropdown">
        @foreach($supportedLocales as $locale => $details)
            <li>
                <a class="dropdown-item language-option {{ $locale === $currentLocale ? 'active' : '' }}"
                   href="{{ route('language.switch', $locale) }}"
                   onclick="switchLanguage('{{ $locale }}'); return false;">
                    <span class="flag-icon">{{ $details['flag'] }}</span>
                    <span>{{ $details['native'] }}</span>
                    @if($locale === $currentLocale)
                        <i class="fas fa-check ms-auto"></i>
                    @endif
                </a>
            </li>
        @endforeach
    </ul>
</div>

<script>
function switchLanguage(locale) {
    // Show loading state
    const button = document.getElementById('languageDropdown');
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __('messages.switching') }}';
    button.disabled = true;
    
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
            document.documentElement.dir = data.direction;
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
        
        // Load RTL CSS if not already loaded
        if (!document.querySelector('link[href*="rtl.css"]')) {
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = '{{ asset('css/rtl.css') }}';
            document.head.appendChild(link);
        }
    } else {
        document.documentElement.dir = 'ltr';
        document.body.classList.remove('rtl');
    }
    
    // Set language attribute
    document.documentElement.lang = currentLocale;
});
</script>

<style>
.language-switcher .dropdown-toggle {
    border: 1px solid #dee2e6;
    background: white;
    color: #495057;
    min-width: 120px;
}

.language-switcher .dropdown-toggle:hover {
    background: #f8f9fa;
    border-color: #adb5bd;
}

.language-switcher .flag-icon {
    font-size: 1.2em;
    margin-right: 0.5rem;
}

[dir="rtl"] .language-switcher .flag-icon {
    margin-right: 0;
    margin-left: 0.5rem;
}

.language-switcher .language-option {
    display: flex;
    align-items: center;
    padding: 0.5rem 1rem;
    transition: all 0.2s ease;
}

.language-switcher .language-option:hover {
    background-color: #f8f9fa;
}

.language-switcher .language-option.active {
    background-color: #007bff;
    color: white;
}

.language-switcher .language-option.active:hover {
    background-color: #0056b3;
}

.language-switcher .ms-auto {
    margin-left: auto;
}

[dir="rtl"] .language-switcher .ms-auto {
    margin-left: 0;
    margin-right: auto;
}

@media (max-width: 768px) {
    .language-switcher .dropdown-toggle {
        min-width: 80px;
        padding: 0.375rem 0.5rem;
    }
}
</style>