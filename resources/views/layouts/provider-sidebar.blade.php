<nav class="mt-5 space-y-1">
    <a href="{{ route('provider.dashboard') }}" class="sidebar-item flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('provider.dashboard') ? 'sidebar-active' : 'text-gray-700 dark:text-gray-300' }}">
        <i class="fas fa-tachometer-alt mr-3"></i>
        {{ __('provider.dashboard') }}
    </a>

    <a href="{{ route('provider.provider-products.index') }}" class="sidebar-item flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('provider.provider-products.*') ? 'sidebar-active' : 'text-gray-700 dark:text-gray-300' }}">
        <i class="fas fa-box mr-3"></i>
        {{ __('provider.products') }}
    </a>

    <a href="{{ route('provider.locations.index') }}" class="sidebar-item flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('provider.locations.*') ? 'sidebar-active' : 'text-gray-700 dark:text-gray-300' }}">
        <i class="fas fa-map-marker-alt mr-3"></i>
        {{ __('provider.locations') }}
    </a>

    <a href="{{ route('provider.orders.index') }}" class="sidebar-item flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('provider.orders.*') ? 'sidebar-active' : 'text-gray-700 dark:text-gray-300' }}">
        <i class="fas fa-shopping-cart mr-3"></i>
        {{ __('provider.orders') }}
    </a>

    <a href="{{ route('provider.jobs.index') }}" class="sidebar-item flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('provider.jobs.*') ? 'sidebar-active' : 'text-gray-700 dark:text-gray-300' }}">
        <i class="fas fa-briefcase mr-3"></i>
        {{ __('provider.jobs') }}
    </a>

    <a href="{{ route('provider.subscription.index') }}" class="sidebar-item flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('provider.subscription.*') ? 'sidebar-active' : 'text-gray-700 dark:text-gray-300' }}">
        <i class="fas fa-credit-card mr-3"></i>
        {{ __('messages.subscription') }}
    </a>

    <a href="{{ route('provider.profile.index') }}" class="sidebar-item flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('provider.profile.*') ? 'sidebar-active' : 'text-gray-700 dark:text-gray-300' }}">
        <i class="fas fa-user mr-3"></i>
        {{ __('provider.profile') }}
    </a>

    <a href="{{ route('provider.settings.index') }}" class="sidebar-item flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('provider.settings.*') ? 'sidebar-active' : 'text-gray-700 dark:text-gray-300' }}">
        <i class="fas fa-cog mr-3"></i>
        {{ __('provider.settings') }}
    </a>
</nav>

<!-- Language Settings -->
<div class="mt-6 px-4">
    <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">
        {{ __('provider.language_settings') }}
    </h3>
    @php
        $currentLocale = app()->getLocale();
        $supportedLocales = [
            'en' => ['name' => 'English', 'native' => 'English', 'flag' => '🇺🇸'],
            'ar' => ['name' => 'Arabic', 'native' => 'العربية', 'flag' => '🇸🇦']
        ];
    @endphp
    <div class="space-y-1">
        @foreach($supportedLocales as $locale => $details)
            <a href="{{ url('/language/' . $locale) }}"
               class="flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ $locale === $currentLocale ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
               onclick="switchLanguageProvider('{{ $locale }}'); return false;">
                <span class="text-lg mr-3">{{ $details['flag'] }}</span>
                <span class="flex-1">{{ $details['native'] }}</span>
                @if($locale === $currentLocale)
                    <i class="fas fa-check text-indigo-600 dark:text-indigo-400 text-xs ml-auto"></i>
                @endif
            </a>
        @endforeach
    </div>
</div>

<script>
// Language switching function for provider dashboard
function switchLanguageProvider(locale) {
    const languageLinks = document.querySelectorAll('a[href*="/language/"]');
    languageLinks.forEach(link => {
        if (link.href.includes('/language/' + locale)) {
            link.innerHTML = '<i class="fas fa-spinner fa-spin mr-3"></i><span>{{ __("provider.switching") }}</span>';
        }
    });

    fetch('/language/' + locale, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (response.ok) {
            const rtlLocales = ['ar', 'he', 'fa', 'ur'];
            document.documentElement.dir = rtlLocales.includes(locale) ? 'rtl' : 'ltr';
            document.documentElement.lang = locale;
            window.location.reload();
        } else {
            console.error('Language switch failed');
            window.location.href = '/language/' + locale;
        }
    })
    .catch(error => {
        console.error('Error switching language:', error);
        window.location.href = '/language/' + locale;
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const currentLocale = '{{ app()->getLocale() }}';
    const rtlLocales = ['ar', 'he', 'fa', 'ur'];

    if (rtlLocales.includes(currentLocale)) {
        document.documentElement.dir = 'rtl';
        document.body.classList.add('rtl');
    } else {
        document.documentElement.dir = 'ltr';
        document.body.classList.remove('rtl');
    }

    document.documentElement.lang = currentLocale;
});
</script>
