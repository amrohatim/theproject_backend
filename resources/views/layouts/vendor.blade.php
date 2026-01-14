@php
    $currentLocale = app()->getLocale();
    $isRtl = in_array($currentLocale, ['ar', 'he', 'fa', 'ur']);
    $direction = $isRtl ? 'rtl' : 'ltr';
@endphp
<!DOCTYPE html>
<html lang="{{ $currentLocale }}" dir="{{ $direction }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('messages.vendor_dashboard_title') }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts - For modern dashboard -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- RTL CSS for Arabic -->
    @if($isRtl)
        <link href="{{ asset('css/rtl.css') }}" rel="stylesheet">
    @endif

    <!-- Modern Dashboard CSS (inline styles below) -->

    <!-- Custom styles for modern vendor dashboard -->
    <style>
        :root {
            /* Modern color palette matching reference design */
            --primary-blue: #1E5EFF;
            --primary-blue-hover: #1a52e6;
            --primary-blue-light: #eff6ff;

            /* Gray scale */
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;

            /* Status colors */
            --green-100: #dcfce7;
            --green-600: #16a34a;
            --green-800: #166534;
            --red-100: #fee2e2;
            --red-600: #dc2626;
            --red-800: #991b1b;
            --yellow-100: #fef3c7;
            --yellow-600: #d97706;
            --yellow-800: #92400e;
        }

        body {
            font-family: 'Open Sans', sans-serif;
            background-color: var(--gray-50);
            color: var(--gray-900);
        }

        /* Sidebar styles */
        .sidebar {
            background: linear-gradient(180deg, #1E5EFF 0%, #1a52e6 100%);
            min-height: 100vh;
            width: 280px;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
        }
        
        /* RTL Sidebar adjustments */
        [dir="rtl"] .sidebar {
            left: auto;
            right: 0;
            box-shadow: -4px 0 20px rgba(0, 0, 0, 0.1);
        }

        .sidebar .brand {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar .brand h1 {
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
        }

        .sidebar .user-info {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar .user-info .avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            margin-bottom: 0.75rem;
        }

        .sidebar .user-info p {
            color: rgba(255, 255, 255, 0.9);
            margin: 0;
            font-size: 0.875rem;
        }

        .sidebar .user-info p:first-of-type {
            font-weight: 600;
            font-size: 1rem;
        }

        .sidebar nav {
            padding: 1rem 0;
        }

        .sidebar nav a {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }

        .sidebar nav a:hover,
        .sidebar nav a.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border-left-color: white;
        }

        .sidebar nav a i {
            width: 20px;
            margin-right: 0.75rem;
            font-size: 1rem;
        }

        .sidebar .logout-btn {
            position: absolute;
            bottom: 2rem;
            left: 1.5rem;
            right: 1.5rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.75rem;
            border-radius: 0.5rem;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .sidebar .logout-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        /* Main content */
        .main-content {
            margin-left: 280px;
            min-height: 100vh;
            background-color: var(--gray-50);
        }

        .top-bar {
            background: white;
            padding: 1rem 2rem;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            align-items: center;
            justify-content: between;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .top-bar h1 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--gray-900);
            margin: 0;
        }

        .content-area {
            padding: 2rem;
        }

        /* RTL Support */
        [dir="rtl"] .sidebar {
            right: 0;
            left: auto;
            box-shadow: -4px 0 20px rgba(0, 0, 0, 0.1);
        }

        [dir="rtl"] .sidebar nav a {
            border-right: 3px solid transparent;
            border-left: none;
        }

        [dir="rtl"] .sidebar nav a:hover,
        [dir="rtl"] .sidebar nav a.active {
            border-right-color: white;
            border-left-color: transparent;
        }

        [dir="rtl"] .sidebar nav a i {
            margin-left: 0.75rem;
            margin-right: 0;
        }

        [dir="rtl"] .main-content {
            margin-right: 280px;
            margin-left: 0;
        }

        [dir="rtl"] .top-bar {
            justify-content: flex-start;
        }

        [dir="rtl"] .dropdown-menu {
            right: auto;
            left: 0;
        }

        [dir="rtl"] .ms-auto {
            margin-left: 0 !important;
            margin-right: auto !important;
        }

        [dir="rtl"] .me-2 {
            margin-left: 0.5rem !important;
            margin-right: 0 !important;
        }

        [dir="rtl"] .ms-1 {
            margin-left: 0 !important;
            margin-right: 0.25rem !important;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            [dir="rtl"] .sidebar {
                transform: translateX(100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            [dir="rtl"] .main-content {
                margin-right: 0;
            }
        }
    </style>

    @yield('styles')
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="brand">
                <h1>{{ __('messages.dala3chic_admin') }}</h1>
            </div>

            <div class="user-info">
                <div class="avatar">
                    <i class="fas fa-user"></i>
                </div>
                <p>{{ Auth::user()->name }}</p>
                <p>{{ Auth::user()->email }}</p>
            </div>

            <nav>
                <a href="{{ route('vendor.dashboard') }}" class="{{ request()->routeIs('vendor.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    {{ __('messages.dashboard') }}
                </a>
                <a href="{{ route('vendor.company.index') }}" class="{{ request()->routeIs('vendor.company.*') ? 'active' : '' }}">
                    <i class="fas fa-building"></i>
                    {{ __('messages.company') }}
                </a>
                <a href="{{ route('vendor.branches.index') }}" class="{{ request()->routeIs('vendor.branches.*') ? 'active' : '' }}">
                    <i class="fas fa-map-marker-alt"></i>
                    {{ __('messages.branches') }}
                </a>
                <a href="{{ route('vendor.products.index') }}" class="{{ request()->routeIs('vendor.products.*') ? 'active' : '' }}">
                    <i class="fas fa-box"></i>
                    {{ __('messages.products') }}
                </a>
                <a href="{{ route('vendor.services.index') }}" class="{{ request()->routeIs('vendor.services.*') ? 'active' : '' }}">
                    <i class="fas fa-concierge-bell"></i>
                    {{ __('messages.services') }}
                </a>
                <a href="{{ route('vendor.deals.index') }}" class="{{ request()->routeIs('vendor.deals.*') ? 'active' : '' }}">
                    <i class="fas fa-percent"></i>
                    {{ __('messages.deals') }}
                </a>
                <a href="{{ route('vendor.jobs.index') }}" class="{{ request()->routeIs('vendor.jobs.*') ? 'active' : '' }}">
                    <i class="fas fa-briefcase"></i>
                    {{ __('messages.jobs') }}
                </a>
                <a href="{{ route('vendor.orders.index') }}" class="{{ request()->routeIs('vendor.orders.index') ? 'active' : '' }}">
                    <i class="fas fa-shopping-cart"></i>
                    {{ __('messages.all_orders') }}
                </a>
                <a href="{{ route('vendor.orders.pending') }}" class="{{ request()->routeIs('vendor.orders.pending') ? 'active' : '' }}">
                    <i class="fas fa-clock"></i>
                    {{ __('messages.pending_orders') }}
                </a>
                <a href="{{ route('vendor.bookings.index') }}" class="{{ request()->routeIs('vendor.bookings.*') ? 'active' : '' }}">
                    <i class="fas fa-calendar-alt"></i>
                    {{ __('messages.bookings') }}
                </a>
                <a href="{{ route('vendor.settings') }}" class="{{ request()->routeIs('vendor.settings*') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i>
                    {{ __('messages.settings') }}
                </a>
            </nav>

            <!-- Language Settings -->
            <div style="padding: 1rem 1.5rem; border-top: 1px solid rgba(255, 255, 255, 0.1); margin-top: 1rem;">
                <h6 style="color: rgba(255, 255, 255, 0.7); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.75rem; font-weight: 600;">
                    {{ __('vendor.language_settings') }}
                </h6>
                @php
                    $currentLocale = app()->getLocale();
                    $supportedLocales = [
                        'en' => ['name' => 'English', 'native' => 'English', 'flag' => '🇺🇸'],
                        'ar' => ['name' => 'Arabic', 'native' => 'العربية', 'flag' => '🇸🇦']
                    ];
                @endphp
                @foreach($supportedLocales as $locale => $details)
                    <a href="{{ url('/language/' . $locale) }}"
                       class="d-flex align-items-center {{ $locale === $currentLocale ? 'active' : '' }}"
                       style="padding: 0.5rem 0; color: {{ $locale === $currentLocale ? 'white' : 'rgba(255, 255, 255, 0.8)' }}; text-decoration: none; border-left: 3px solid {{ $locale === $currentLocale ? 'white' : 'transparent' }}; padding-left: {{ $locale === $currentLocale ? 'calc(0.75rem - 3px)' : '0.75rem' }}; transition: all 0.2s ease;"
                       onclick="switchLanguageVendor('{{ $locale }}'); return false;"
                       onmouseover="if ('{{ $locale }}' !== '{{ $currentLocale }}') { this.style.color = 'white'; this.style.background = 'rgba(255, 255, 255, 0.1)'; }"
                       onmouseout="if ('{{ $locale }}' !== '{{ $currentLocale }}') { this.style.color = 'rgba(255, 255, 255, 0.8)'; this.style.background = 'transparent'; }">
                        <span style="font-size: 1rem; margin-right: 0.5rem;">{{ $details['flag'] }}</span>
                        <span style="flex: 1;">{{ $details['native'] }}</span>
                        @if($locale === $currentLocale)
                            <i class="fas fa-check" style="color: white; font-size: 0.75rem; margin-left: auto;"></i>
                        @endif
                    </a>
                @endforeach
            </div>

            <form method="POST" action="{{ route('logout') }}" class="logout-form">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fas fa-sign-out-alt me-2"></i>
                    Logout
                </button>
            </form>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="top-bar">
                <button class="btn btn-link d-md-none" id="sidebar-toggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h1>@yield('header', __('messages.vendor_dashboard'))</h1>
                <div class="ms-auto d-flex align-items-center">
                    <!-- Language Switcher -->
                    <x-language-switcher />
                    
                    <div class="dropdown ms-3">
                        <button class="btn btn-link dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            {{ Auth::user()->name }}
                            <i class="fas fa-chevron-down ms-1"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('vendor.settings.profile') }}">{{ __('messages.profile') }}</a></li>
                            <li><a class="dropdown-item" href="{{ route('vendor.settings') }}">{{ __('messages.settings') }}</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">{{ __('messages.logout') }}</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="content-area">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Sidebar toggle for mobile -->
    <script>
        document.getElementById('sidebar-toggle')?.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('show');
        });

        // Language switching function for vendor dashboard
        function switchLanguageVendor(locale) {
            // Show loading state on the clicked language option
            const languageLinks = document.querySelectorAll('a[href*="/language/"]');
            languageLinks.forEach(link => {
                if (link.href.includes('/language/' + locale)) {
                    const originalContent = link.innerHTML;
                    link.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right: 0.5rem;"></i><span>{{ __("vendor.switching") }}</span>';
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

    @yield('scripts')
</body>
</html>
