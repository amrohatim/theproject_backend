@php
    $currentLocale = app()->getLocale();
    $rtlLocales = ['ar', 'he', 'fa', 'ur'];
    $isRtl = in_array($currentLocale, $rtlLocales);
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $currentLocale) }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', __('service_provider.dashboard')) - {{ config('app.name', 'glowlabs') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">

    @if($isRtl)
    <!-- RTL Styles -->
    <link rel="stylesheet" href="{{ asset('css/rtl.css') }}">
    @endif

    <!-- RTL Utility Overrides -->
    <style>
        [dir="rtl"] .space-x-1,
        [dir="rtl"] .space-x-2,
        [dir="rtl"] .space-x-3,
        [dir="rtl"] .space-x-4,
        [dir="rtl"] .space-x-5,
        [dir="rtl"] .space-x-6 {
            --tw-space-x-reverse: 1;
        }
        [dir="rtl"] .ml-auto {
            margin-left: 0 !important;
            margin-right: auto !important;
        }
        [dir="rtl"] .mr-auto {
            margin-right: 0 !important;
            margin-left: auto !important;
        }
        [dir="rtl"] .text-left {
            text-align: right !important;
        }
        [dir="rtl"] .text-right {
            text-align: left !important;
        }
    </style>

    <!-- Additional Styles -->
    @yield('styles')
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div id="sp-sidebar" class="fixed inset-y-0 left-0 z-40 w-64 transform -translate-x-full bg-white dark:bg-gray-800 shadow-lg transition-transform duration-200 md:static md:translate-x-0">
            <div class="flex flex-col h-full">
                <!-- Logo -->
                <div class="flex items-center justify-center h-16 px-4 bg-[var(--primary)] dark:bg-[var(--primary)]">
                    <h1 class="text-xl font-bold text-white">{{ __('service_provider.service_provider') }}</h1>
                </div>

                <!-- User Info -->
                <div class="px-4 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-full bg-[var(--primary-light)] dark:bg-[var(--primary)]/20 flex items-center justify-center">
                                <i class="fas fa-user text-[var(--primary)] dark:text-[var(--primary-light)]"></i>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 px-4 py-4 space-y-2">
                    <!-- Dashboard -->
                    <a href="{{ route('service-provider.dashboard') }}" 
                       class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('service-provider.dashboard') ? 'bg-[var(--primary-light)] text-[var(--primary)] dark:bg-[var(--primary)]/20 dark:text-[var(--primary-light)]' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                        <i class="fas fa-tachometer-alt mr-3"></i>
                        {{ __('service_provider.dashboard') }}
                    </a>

                    <!-- Services -->
                    <a href="{{ route('service-provider.services.index') }}"
                       class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('service-provider.services.*') ? 'bg-[var(--primary-light)] text-[var(--primary)] dark:bg-[var(--primary)]/20 dark:text-[var(--primary-light)]' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                        <i class="fas fa-cog mr-3"></i>
                        {{ __('service_provider.services') }}
                    </a>

                    <!-- Bookings -->
                    <a href="{{ route('service-provider.bookings.index') }}"
                       class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('service-provider.bookings.*') ? 'bg-[var(--primary-light)] text-[var(--primary)] dark:bg-[var(--primary)]/20 dark:text-[var(--primary-light)]' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                        <i class="fas fa-calendar-check mr-3"></i>
                        {{ __('service_provider.bookings') }}
                    </a>

                    <!-- Deals -->
                    <a href="{{ route('service-provider.deals.index') }}"
                       class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('service-provider.deals.*') ? 'bg-[var(--primary-light)] text-[var(--primary)] dark:bg-[var(--primary)]/20 dark:text-[var(--primary-light)]' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                        <i class="fas fa-percent mr-3"></i>
                        {{ __('service_provider.deals') }}
                    </a>
                </nav>

                <!-- Language Switcher -->
                <div class="px-4 py-4 border-t border-gray-200 dark:border-gray-700">
                    <x-service-provider-sidebar-language-switcher />
                </div>

                <!-- Logout -->
                <div class="px-4 py-4 border-t border-gray-200 dark:border-gray-700">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center w-full px-3 py-2 text-sm font-medium text-gray-700 rounded-md hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
                            <i class="fas fa-sign-out-alt mr-3"></i>
                            {{ __('service_provider.logout') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar overlay for mobile -->
        <div id="sp-sidebar-overlay" class="fixed inset-0 z-30 hidden bg-black bg-opacity-50 md:hidden"></div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar -->
            <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <button type="button" id="sp-mobile-menu-toggle" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 md:hidden">
                                <i class="fas fa-bars"></i>
                            </button>
                            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">@yield('page-title', __('service_provider.dashboard'))</h1>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="relative group">
                                <a
                                    href="{{ route('service-provider.notifications.index') }}"
                                    class="relative inline-flex h-9 w-9 items-center justify-center rounded-full text-gray-600 hover:text-[var(--primary)] hover:bg-[var(--primary-light)] dark:text-gray-300 dark:hover:text-[var(--primary-light)] dark:hover:bg-gray-700 transition-colors"
                                    aria-label="Service provider notifications"
                                >
                                    <i class="fas fa-bell"></i>
                                    @if(($serviceProviderUnreadCount ?? 0) > 0)
                                        <span class="absolute -top-1 -right-1 inline-flex min-w-[1.05rem] h-[1.05rem] items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-semibold leading-none text-white">
                                            {{ $serviceProviderUnreadCount > 99 ? '99+' : $serviceProviderUnreadCount }}
                                        </span>
                                    @endif
                                </a>

                                <div class="hidden group-hover:block group-focus-within:block absolute right-0 top-10 z-50 w-80 rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-800">
                                    <div class="flex items-center justify-between border-b border-gray-200 px-4 py-2 dark:border-gray-700">
                                        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ __('messages.notifications') }}</h3>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('messages.latest') }} 10</span>
                                    </div>
                                    <div class="max-h-80 overflow-y-auto">
                                        @forelse(($serviceProviderNotificationPreview ?? collect()) as $notification)
                                            <div class="border-b border-gray-100 px-4 py-3 last:border-b-0 dark:border-gray-700">
                                                <p class="text-sm text-gray-700 dark:text-gray-200">
                                                    {{ app()->getLocale() === 'ar' ? $notification->message_arabic : $notification->message }}
                                                </p>
                                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $notification->created_at?->diffForHumans() }}</p>
                                            </div>
                                        @empty
                                            <div class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                                {{ __('messages.no_notifications') }}
                                            </div>
                                        @endforelse
                                    </div>
                                    <a href="{{ route('service-provider.notifications.index') }}" class="block border-t border-gray-200 px-4 py-2 text-center text-sm font-medium text-[var(--primary)] hover:bg-gray-50 dark:border-gray-700 dark:text-[var(--primary)] dark:hover:bg-gray-700">
                                        {{ __('messages.view_all') }}
                                    </a>
                                </div>
                            </div>

                            <!-- User Menu -->
                            <div class="relative">
                                <button class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[var(--primary)]" id="user-menu-button">
                                    <span class="sr-only">Open user menu</span>
                                    <div class="h-8 w-8 rounded-full bg-[var(--primary-light)] dark:bg-[var(--primary)]/20 flex items-center justify-center">
                                        <i class="fas fa-user text-[var(--primary)] dark:text-[var(--primary-light)] text-sm"></i>
                                    </div>
                                    <span class="ml-2 text-gray-700 dark:text-gray-300">{{ Auth::user()->name }}</span>
                                    <i class="fas fa-chevron-down ml-2 text-gray-400"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto p-6">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                        <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                            <i class="fas fa-times cursor-pointer" onclick="this.parentElement.parentElement.style.display='none'"></i>
                        </span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                        <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                            <i class="fas fa-times cursor-pointer" onclick="this.parentElement.parentElement.style.display='none'"></i>
                        </span>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="mb-6 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('warning') }}</span>
                        <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                            <i class="fas fa-times cursor-pointer" onclick="this.parentElement.parentElement.style.display='none'"></i>
                        </span>
                    </div>
                @endif

                @if(session('info'))
                    <div class="mb-6 bg-[var(--primary-light)] border border-[var(--primary)] text-[var(--primary)] px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('info') }}</span>
                        <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                            <i class="fas fa-times cursor-pointer" onclick="this.parentElement.parentElement.style.display='none'"></i>
                        </span>
                    </div>
                @endif

                <!-- Page Content -->
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Auto-hide flash messages after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('[role="alert"]');
            alerts.forEach(function(alert) {
                alert.style.display = 'none';
            });
        }, 5000);

        // Mobile sidebar toggle
        const sidebarToggle = document.getElementById('sp-mobile-menu-toggle');
        const sidebar = document.getElementById('sp-sidebar');
        const sidebarOverlay = document.getElementById('sp-sidebar-overlay');

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            sidebarOverlay.classList.add('hidden');
        }

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            sidebarOverlay.classList.remove('hidden');
        }

        if (sidebarToggle && sidebar && sidebarOverlay) {
            sidebarToggle.addEventListener('click', function() {
                if (sidebar.classList.contains('-translate-x-full')) {
                    openSidebar();
                } else {
                    closeSidebar();
                }
            });

            sidebarOverlay.addEventListener('click', closeSidebar);
        }

        // Dark mode toggle (if needed)
        function toggleDarkMode() {
            document.documentElement.classList.toggle('dark');
            localStorage.setItem('darkMode', document.documentElement.classList.contains('dark'));
        }

        // Load dark mode preference
        if (localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark');
        }
    </script>

    @yield('scripts')
</body>
</html>
