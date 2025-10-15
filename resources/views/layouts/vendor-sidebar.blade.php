<nav class="mt-5 space-y-1">
    <a href="{{ route('vendor.dashboard') }}" class="sidebar-item flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('vendor.dashboard') ? 'sidebar-active' : 'text-gray-700 dark:text-gray-300' }}">
        <i class="fas fa-tachometer-alt mr-3"></i>
        {{ __('messages.dashboard') }}
    </a>

    <a href="{{ route('vendor.company.index') }}" class="sidebar-item flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('vendor.company.*') ? 'sidebar-active' : 'text-gray-700 dark:text-gray-300' }}">
        <i class="fas fa-building mr-3"></i>
        {{ __('messages.company') }}
    </a>

    <a href="{{ route('vendor.branches.index') }}" class="sidebar-item flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('vendor.branches.*') ? 'sidebar-active' : 'text-gray-700 dark:text-gray-300' }}">
        <i class="fas fa-store mr-3"></i>
        {{ __('messages.branches') }}
    </a>

    <a href="{{ route('vendor.products.index') }}" class="sidebar-item flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('vendor.products.*') ? 'sidebar-active' : 'text-gray-700 dark:text-gray-300' }}">
        <i class="fas fa-shopping-bag mr-3"></i>
        {{ __('messages.products') }}
    </a>

    <a href="{{ route('vendor.services.index') }}" class="sidebar-item flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('vendor.services.*') ? 'sidebar-active' : 'text-gray-700 dark:text-gray-300' }}">
        <i class="fas fa-concierge-bell mr-3"></i>
        {{ __('messages.services') }}
    </a>

    <a href="{{ route('vendor.deals.index') }}" class="sidebar-item flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('vendor.deals.*') ? 'sidebar-active' : 'text-gray-700 dark:text-gray-300' }}">
        <i class="fas fa-percentage mr-3"></i>
        {{ __('messages.deals') }}
    </a>

    <a href="{{ route('vendor.orders.index') }}" class="sidebar-item flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('vendor.orders.index') || (request()->routeIs('vendor.orders.*') && !request()->routeIs('vendor.orders.pending')) ? 'sidebar-active' : 'text-gray-700 dark:text-gray-300' }}">
        <i class="fas fa-shopping-cart mr-3"></i>
        {{ __('messages.all_orders') }}
    </a>

    <a href="{{ route('vendor.orders.pending') }}" class="sidebar-item flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('vendor.orders.pending') ? 'sidebar-active' : 'text-gray-700 dark:text-gray-300' }}">
        <i class="fas fa-clock mr-3"></i>
        {{ __('messages.pending_orders') }}
    </a>

    <a href="{{ route('vendor.bookings.index') }}" class="sidebar-item flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('vendor.bookings.*') ? 'sidebar-active' : 'text-gray-700 dark:text-gray-300' }}">
        <i class="fas fa-calendar-alt mr-3"></i>
        {{ __('messages.bookings') }}
    </a>

    <a href="{{ route('vendor.subscription.index') }}" class="sidebar-item flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('vendor.subscription.*') ? 'sidebar-active' : 'text-gray-700 dark:text-gray-300' }}">
        <i class="fas fa-credit-card mr-3"></i>
        {{ __('messages.subscription') }}
    </a>

    <a href="{{ route('vendor.settings') }}" class="sidebar-item flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('vendor.settings') ? 'sidebar-active' : 'text-gray-700 dark:text-gray-300' }}">
        <i class="fas fa-cog mr-3"></i>
        {{ __('messages.settings') }}
    </a>
</nav>

<!-- Language Settings -->
<div class="mt-6 px-4">
    <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">
        Language Settings
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
               onclick="switchLanguageVendor('{{ $locale }}'); return false;">
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
// Language switching function for vendor dashboard
function switchLanguageVendor(locale) {
    // Show loading state on the clicked language option
    const languageLinks = document.querySelectorAll('a[href*="/language/"]');
    languageLinks.forEach(link => {
        if (link.href.includes('/language/' + locale)) {
            const originalContent = link.innerHTML;
            link.innerHTML = '<i class="fas fa-spinner fa-spin mr-3"></i><span>Switching...</span>';
        }
    });

    // Make AJAX request to switch language
    fetch('/language/' + locale, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (response.ok) {
            // Update page direction and reload
            const rtlLocales = ['ar', 'he', 'fa', 'ur'];
            document.documentElement.dir = rtlLocales.includes(locale) ? 'rtl' : 'ltr';
            document.documentElement.lang = locale;

            // Reload page to apply language changes
            window.location.reload();
        } else {
            console.error('Language switch failed');
            // Restore original content on error
            languageLinks.forEach(link => {
                if (link.href.includes('/language/' + locale)) {
                    location.reload(); // Simple fallback
                }
            });
        }
    })
    .catch(error => {
        console.error('Error switching language:', error);
        // Fallback to direct navigation
        window.location.href = '/language/' + locale;
    });
}

// Initialize RTL support on page load
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
