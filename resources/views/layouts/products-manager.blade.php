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

    <title>@yield('title', __('products_manager.dashboard_title')) - {{ config('app.name', 'Dala3Chic') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Modern Forms CSS -->
    <link rel="stylesheet" href="{{ asset('css/modern-forms.css') }}">
    <!-- Modern Buttons CSS -->
    <link rel="stylesheet" href="{{ asset('css/modern-buttons.css') }}">
    <!-- Global Styles -->
    <link rel="stylesheet" href="{{ asset('css/global-styles.css') }}">
    <!-- Vue Styles -->
    <link rel="stylesheet" href="{{ asset('css/vue-styles.css') }}">
    @if($isRtl)
    <!-- RTL Styles -->
    <link rel="stylesheet" href="{{ asset('css/rtl.css') }}">
    @endif

    <!-- Additional Styles -->
    @yield('styles')
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div id="pm-sidebar" class="fixed inset-y-0 left-0 z-40 w-64 transform -translate-x-full bg-white shadow-lg transition-transform duration-200 dark:bg-gray-800 md:static md:translate-x-0">
            <div class="flex flex-col h-full">
                <!-- Logo -->
                <div class="flex items-center justify-center h-16 px-4 bg-orange-500 dark:bg-orange-600">
                    <h1 class="text-xl font-bold text-white">{{__('products_manager.products_manager_role')}}</h1>
                </div>

                <!-- User Info -->
                <div class="px-4 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-full bg-purple-100 dark:bg-purple-900 flex items-center justify-center">
                                <i class="fas fa-user text-orange-600 dark:text-orange-400"></i>
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
                    <a href="{{ route('products-manager.dashboard') }}"
                       class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ request()->routeIs('products-manager.dashboard') ? 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-200 border-r-4 border-orange-500' : 'text-gray-700 hover:bg-orange-50 hover:text-orange-600 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-orange-400' }}">
                        <i class="fas fa-tachometer-alt mr-3"></i>
                        {{ __('products_manager.dashboard') }}
                    </a>

                    <!-- Products -->
                    <a href="{{ route('products-manager.products.index') }}"
                       class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ request()->routeIs('products-manager.products.*') ? 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-200 border-r-4 border-orange-500' : 'text-gray-700 hover:bg-orange-50 hover:text-orange-600 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-orange-400' }}">
                        <i class="fas fa-box mr-3"></i>
                        {{ __('products_manager.products') }}
                    </a>

                    <!-- All Orders -->
                    <a href="{{ route('products-manager.orders.index') }}"
                       class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ request()->routeIs('products-manager.orders.index') ? 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-200 border-r-4 border-orange-500' : 'text-gray-700 hover:bg-orange-50 hover:text-orange-600 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-orange-400' }}">
                        <i class="fas fa-shopping-cart mr-3"></i>
                        {{ __('products_manager.all_orders') }}
                    </a>

                    <!-- Pending Orders -->
                    <a href="{{ route('products-manager.orders.pending') }}"
                       class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ request()->routeIs('products-manager.orders.pending') ? 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-200 border-r-4 border-orange-500' : 'text-gray-700 hover:bg-orange-50 hover:text-orange-600 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-orange-400' }}">
                        <i class="fas fa-clock mr-3"></i>
                        {{ __('products_manager.pending_orders') }}
                    </a>

                    <!-- Deals -->
                    <a href="{{ route('products-manager.deals.index') }}"
                       class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ request()->routeIs('products-manager.deals.*') ? 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-200 border-r-4 border-orange-500' : 'text-gray-700 hover:bg-orange-50 hover:text-orange-600 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-orange-400' }}">
                        <i class="fas fa-percent mr-3"></i>
                        {{ __('products_manager.deals') }}
                    </a>
                </nav>

                <!-- Language Settings -->
                <div class="px-4 py-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="mb-3">
                        <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            {{ __('products_manager.language_settings') }}
                        </h3>
                    </div>
                    <div class="space-y-1">
                        @php
                            $currentLocale = app()->getLocale();
                            $supportedLocales = [
                                'en' => ['name' => 'English', 'native' => 'English', 'flag' => '🇺🇸'],
                                'ar' => ['name' => 'Arabic', 'native' => 'العربية', 'flag' => '🇸🇦']
                            ];
                        @endphp

                        @foreach($supportedLocales as $locale => $details)
                            <a href="{{ url('/language/' . $locale) }}"
                               class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ $locale === $currentLocale ? 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-200 border-r-4 border-orange-500' : 'text-gray-700 hover:bg-orange-50 hover:text-orange-600 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-orange-400' }}"
                               onclick="switchLanguage('{{ $locale }}'); return false;">
                                <span class="text-lg mr-3">{{ $details['flag'] }}</span>
                                <span>{{ $details['native'] }}</span>
                                @if($locale === $currentLocale)
                                    <i class="fas fa-check ml-auto text-orange-600 dark:text-orange-400"></i>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Logout -->
                <div class="px-4 py-4 border-t border-gray-200 dark:border-gray-700">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center w-full px-3 py-2 text-sm font-medium text-gray-700 rounded-md hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
                            <i class="fas fa-sign-out-alt mr-3"></i>
                            {{ __('products_manager.logout') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar overlay for mobile -->
        <div id="pm-sidebar-overlay" class="fixed inset-0 z-30 hidden bg-black bg-opacity-50 md:hidden"></div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar -->
            <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <button type="button" id="pm-mobile-menu-toggle" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 md:hidden">
                                <i class="fas fa-bars"></i>
                            </button>
                            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">@yield('page-title', __('products_manager.dashboard_title'))</h1>
                        </div>
                        <div class="flex items-center space-x-4">
                            <!-- Notifications -->
                            <button class="p-2 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                                <i class="fas fa-bell"></i>
                            </button>

                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto p-0" id="main-content-area">
                <!-- Loading Indicator -->
                <div id="ajax-loading-indicator" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white rounded-lg p-6 flex items-center space-x-3">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-orange-500"></div>
                        <span class="text-gray-700">Loading...</span>
                    </div>
                </div>

                <!-- Flash Messages Container -->
                <div id="flash-messages-container">
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
                        <div class="mb-6 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('info') }}</span>
                            <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                                <i class="fas fa-times cursor-pointer" onclick="this.parentElement.parentElement.style.display='none'"></i>
                            </span>
                        </div>
                    @endif
                </div>

                <!-- Dynamic Page Content -->
                <div id="dynamic-content" class="p-0">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Orange Theme Styles for Products Manager -->
    <style>
        /* Products Manager Orange Theme */
        .products-manager-theme .bg-indigo-600 { background-color: #f59e0b !important; }
        .products-manager-theme .bg-indigo-700 { background-color: #d97706 !important; }
        .products-manager-theme .bg-indigo-500 { background-color: #f59e0b !important; }
        .products-manager-theme .text-indigo-600 { color: #f59e0b !important; }
        .products-manager-theme .text-indigo-700 { color: #d97706 !important; }
        .products-manager-theme .border-indigo-500 { border-color: #f59e0b !important; }
        .products-manager-theme .focus\:ring-indigo-500:focus { --tw-ring-color: #f59e0b !important; }
        .products-manager-theme .focus\:border-indigo-500:focus { border-color: #f59e0b !important; }

        /* Ensure proper styling for AJAX-loaded content */
        .products-manager-theme .container { max-width: 100% !important; }
        .products-manager-theme table { width: 100% !important; }
        .products-manager-theme .overflow-x-auto { overflow-x: auto !important; }

        /* RTL spacing helpers for Tailwind utilities */
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

        /* Responsive table for products list */
        @media (max-width: 768px) {
            .pm-responsive-table thead {
                display: none;
            }

            .pm-responsive-table,
            .pm-responsive-table tbody,
            .pm-responsive-table tr,
            .pm-responsive-table td {
                display: block;
                width: 100%;
            }

            .pm-responsive-table tbody tr {
                margin-bottom: 1rem;
                border: 1px solid #f59e0b !important;
                border-radius: 0.375rem;
                overflow: hidden;
                background-color: #ffffff;
                box-shadow: 0 0 0 1px #f59e0b !important;
            }

            .pm-responsive-table tbody td {
                display: flex;
                align-items: flex-start;
                gap: 0.75rem;
                padding: 0.75rem 1rem !important;
                border-top: 1px solid #e9ebe5ff;
            }

            .pm-responsive-table tbody tr td:first-child {
                border-top: 0;
            }

            .pm-responsive-table tbody td::before {
                content: attr(data-label);
                flex: 0 0 38%;
                font-weight: 600;
                color: #6b7280;
                text-transform: uppercase;
                letter-spacing: 0.03em;
                font-size: 0.7rem;
            }

            .pm-responsive-table tbody td > * {
                flex: 1;
            }

            .dark .pm-responsive-table tbody tr {
                border-color: #d97706;
                background-color: #1f2937;
            }

            .dark .pm-responsive-table tbody td {
                border-top-color: #374151;
            }
        }
    </style>

    <!-- Scripts -->
    <script>
        // Auto-hide flash messages after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('[role="alert"]');
            alerts.forEach(function(alert) {
                alert.style.display = 'none';
            });
        }, 5000);

        // Dark mode toggle (if needed)
        function toggleDarkMode() {
            document.documentElement.classList.toggle('dark');
            localStorage.setItem('darkMode', document.documentElement.classList.contains('dark'));
        }

        // Load dark mode preference
        if (localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark');
        }

        // Products Manager AJAX Navigation System
        class ProductsManagerAjaxNavigation {
            constructor() {
                this.baseUrl = '{{ url("/") }}';
                this.csrfToken = '{{ csrf_token() }}';
                this.currentUrl = window.location.href;
                this.loadingIndicator = document.getElementById('ajax-loading-indicator');
                this.dynamicContent = document.getElementById('dynamic-content');
                this.flashMessagesContainer = document.getElementById('flash-messages-container');

                this.init();
            }

            init() {
                // Apply orange theme
                document.body.classList.add('products-manager-theme');

                // Intercept navigation clicks
                this.interceptNavigationClicks();

                // Handle browser back/forward
                window.addEventListener('popstate', (e) => {
                    if (e.state && e.state.url) {
                        this.loadContent(e.state.url, false);
                    }
                });

                // Set initial state
                history.replaceState({ url: window.location.href }, '', window.location.href);

                // Check if we need to load content for the current page
                this.checkAndLoadInitialContent();

                // Set up auto-refresh cleanup
                this.setupAutoRefreshCleanup();
            }

            interceptNavigationClicks() {
                document.addEventListener('click', (e) => {
                    const link = e.target.closest('a');
                    if (!link) return;

                    const href = link.getAttribute('href');
                    if (!href) return;

                    // Check if this is a products-manager product-related URL
                    if (this.shouldInterceptUrl(href)) {
                        e.preventDefault();
                        this.loadContent(href, true);
                    }
                });
            }

            checkAndLoadInitialContent() {
                // Check if the current URL should have AJAX-loaded content
                const currentUrl = window.location.href;
                if (this.shouldInterceptUrl(currentUrl)) {
                    // Check if the dynamic content container is empty or has placeholder content
                    const contentContainer = this.dynamicContent;
                    if (contentContainer) {
                        const content = contentContainer.innerHTML.trim();
                        const hasPlaceholder = content.includes('This will be populated via AJAX') ||
                                             content.includes('Loading product creation form') ||
                                             content.length < 200; // Very minimal content

                        if (hasPlaceholder) {
                            console.log('🔄 Loading initial AJAX content for:', currentUrl);
                            // Load content without updating history since we're already on this page
                            this.loadContent(currentUrl, false);
                        }
                    }
                }
            }

            shouldInterceptUrl(url) {
                const isProductsManagerList = url.includes('/products-manager/products')
                    && !url.includes('/products-manager/products/create')
                    && !url.match(/\/products-manager\/products\/\d/);

                if (isProductsManagerList) {
                    return false;
                }

                const productUrls = [
                    '/products-manager/products/create',
                    '/products-manager/products/',
                    '/vendor/products/create',
                    '/vendor/products/'
                ];

                return productUrls.some(pattern => url.includes(pattern));
            }

            async loadContent(url, updateHistory = true) {
                try {
                    this.showLoading();

                    // Use the original URL for AJAX requests (Products Manager routes are configured correctly)
                    let ajaxUrl = url;

                    const response = await fetch(ajaxUrl, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': this.csrfToken,
                            'X-Products-Manager-Context': 'true',
                            'Accept': 'text/html'
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const html = await response.text();
                    this.updateContent(html);

                    // After content update, ensure Vite assets are applied for AJAX-loaded pages
                    this.ensureViteAssets(url);

                    if (updateHistory) {
                        history.pushState({ url: url }, '', url);
                    }

                    // Store previous URL before updating current URL for auto-refresh check
                    const previousUrl = this.currentUrl;
                    this.currentUrl = url;

                    // Check for auto-refresh after URL is updated, passing previous URL
                    this.checkAutoRefresh(previousUrl);

                } catch (error) {
                    console.error('AJAX navigation error:', error);
                    this.showError('Failed to load content. Please refresh the page.');
                } finally {
                    this.hideLoading();
                }
            }

            ensureViteAssets(url) {
                // If the requested page is a product create/edit page, ensure the Vite chunks are loaded
                const needsCreate = url.includes('/products-manager/products/create');
                const needsEdit = url.includes('/products-manager/products/') && url.includes('/edit');
                if (!(needsCreate || needsEdit)) {
                    // For products list page, ensure proper styling
                    if (url.includes('/products-manager/products') && !url.includes('/create') && !url.includes('/edit')) {
                        console.log('🎨 Ensuring proper styling for products list page');
                        setTimeout(() => {
                            this.ensureProperStyling();
                        }, 100);
                    }
                    return;
                }

                console.log('🔧 Ensuring Vite assets for:', url);

                // Look for script tags injected by the Vite directive
                const hasVendorCreate = !!document.querySelector('script[src*="vendor-product-create"]');
                const hasVendorEdit = !!document.querySelector('script[src*="vendor-product-edit"]');

                console.log('Has vendor create script:', hasVendorCreate);
                console.log('Has vendor edit script:', hasVendorEdit);

                // Force reload Vite scripts to ensure they execute in AJAX context
                const head = document.head;
                if (needsCreate) {
                    console.log('📦 Force loading vendor-product-create script...');

                    // Clean up any existing Vue apps first
                    if (window.cleanupVendorProductCreateApp) {
                        window.cleanupVendorProductCreateApp();
                    }

                    // Remove existing script if present
                    const existingScript = document.querySelector('script[src*="vendor-product-create"]');
                    if (existingScript) {
                        console.log('Removing existing script...');
                        existingScript.remove();
                    }

                    const s = document.createElement('script');
                    s.type = 'module';
                    s.src = '{{ Vite::asset("resources/js/vendor-product-create.js") }}';
                    s.onload = () => {
                        console.log('✅ vendor-product-create script force loaded and executed');
                        // Try to initialize with force cleanup after a short delay
                        setTimeout(() => {
                            if (window.initVendorProductCreateApp) {
                                console.log('🔄 Attempting Vue app initialization with force cleanup...');
                                window.initVendorProductCreateApp(true);
                            }
                        }, 100);
                    };
                    s.onerror = (e) => console.error('❌ Failed to force load vendor-product-create script:', e);
                    head.appendChild(s);
                }
                if (needsEdit) {
                    console.log('📦 Force loading vendor-product-edit script...');

                    // Clean up any existing Vue apps first
                    if (window.cleanupVendorProductEditApp) {
                        window.cleanupVendorProductEditApp();
                    }

                    // Remove existing script if present
                    const existingScript = document.querySelector('script[src*="vendor-product-edit"]');
                    if (existingScript) {
                        console.log('Removing existing script...');
                        existingScript.remove();
                    }

                    const s = document.createElement('script');
                    s.type = 'module';
                    s.src = '{{ Vite::asset("resources/js/vendor-product-edit.js") }}';
                    s.onload = () => {
                        console.log('✅ vendor-product-edit script force loaded and executed');
                        // Let the script handle its own initialization to prevent conflicts
                        console.log('🔧 Allowing script to handle its own initialization...');
                    };
                    s.onerror = (e) => console.error('❌ Failed to force load vendor-product-edit script:', e);
                    head.appendChild(s);
                }
            }

            updateContent(html) {
                // Clean up any existing Vue apps before loading new content
                this.cleanupVueApps();

                // Create a temporary container to parse the response
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = html;

                // Extract the main content from the vendor layout
                const vendorContent = tempDiv.querySelector('.container') || tempDiv.querySelector('main') || tempDiv;

                if (vendorContent) {
                    this.dynamicContent.innerHTML = vendorContent.innerHTML;
                } else {
                    this.dynamicContent.innerHTML = html;
                }

                // Apply orange theme to new content
                this.dynamicContent.classList.add('products-manager-theme');

                // Ensure proper styling for tables and containers
                this.ensureProperStyling();

                // Execute any inline scripts that were inserted
                this.executeInlineScripts();

                // Reinitialize any JavaScript components
                this.reinitializeComponents();
            }

            ensureProperStyling() {
                // Ensure tables have proper styling
                const tables = this.dynamicContent.querySelectorAll('table');
                tables.forEach(table => {
                    if (!table.classList.contains('min-w-full')) {
                        table.classList.add('min-w-full', 'divide-y', 'divide-gray-200', 'dark:divide-gray-700');
                    }
                });

                // Ensure containers have proper styling
                const containers = this.dynamicContent.querySelectorAll('.container');
                containers.forEach(container => {
                    if (!container.classList.contains('mx-auto')) {
                        container.classList.add('mx-auto');
                    }
                });

                // Ensure form elements have proper styling
                const inputs = this.dynamicContent.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    if (!input.classList.contains('rounded-md')) {
                        input.classList.add('rounded-md', 'border-gray-300', 'dark:border-gray-600', 'dark:bg-gray-700', 'dark:text-white');
                    }
                });

                // Ensure buttons have proper styling
                const buttons = this.dynamicContent.querySelectorAll('button, .btn');
                buttons.forEach(button => {
                    if (button.classList.contains('bg-indigo-600') || button.classList.contains('bg-indigo-500')) {
                        button.classList.remove('bg-indigo-600', 'bg-indigo-500', 'hover:bg-indigo-700');
                        button.classList.add('bg-orange-500', 'hover:bg-orange-600');
                    }
                });
            }

            cleanupVueApps() {
                console.log('🧹 Cleaning up existing Vue apps before navigation...');

                // Find all Vue app containers
                const vueContainers = this.dynamicContent.querySelectorAll('[id*="vue"], [id*="app"], .vue-app-container');

                vueContainers.forEach(container => {
                    if (container.__vue_app__) {
                        console.log(`🧹 Cleaning up Vue app in container: ${container.id}`);
                        try {
                            if (typeof container.__vue_app__.unmount === 'function') {
                                container.__vue_app__.unmount();
                            }
                        } catch (e) {
                            console.warn('Error unmounting Vue app:', e);
                        }
                        container.__vue_app__ = null;
                    }
                });

                // Call global cleanup functions if they exist
                if (window.cleanupVendorProductCreateApp) {
                    try {
                        window.cleanupVendorProductCreateApp();
                    } catch (e) {
                        console.warn('Error calling global Vue create cleanup:', e);
                    }
                }

                if (window.cleanupVendorProductEditApp) {
                    try {
                        window.cleanupVendorProductEditApp();
                    } catch (e) {
                        console.warn('Error calling global Vue edit cleanup:', e);
                    }
                }

                // Clear any Vue-related timeouts or intervals
                this.clearVueTimeouts();

                console.log('✅ Vue apps cleanup completed');
            }

            clearVueTimeouts() {
                // Clear any timeouts that might be running from Vue initialization
                for (let i = 1; i < 10000; i++) {
                    clearTimeout(i);
                }
            }

            executeInlineScripts() {
                const scripts = this.dynamicContent.querySelectorAll('script:not([src])');
                console.log('🔧 Found', scripts.length, 'inline scripts to execute');

                scripts.forEach((script, index) => {
                    if (script.textContent.trim()) {
                        try {
                            console.log(`🔧 Executing inline script ${index + 1}...`);
                            // Create a new script element and execute it
                            const newScript = document.createElement('script');
                            newScript.textContent = script.textContent;
                            document.head.appendChild(newScript);
                            document.head.removeChild(newScript);
                            console.log(`✅ Inline script ${index + 1} executed successfully`);
                        } catch (error) {
                            console.error(`❌ Error executing inline script ${index + 1}:`, error);
                        }
                    }
                });
            }

            reinitializeComponents() {
                // Reinitialize Vue components if they exist
                if (window.Vue && window.createApp) {
                    this.initializeVueComponents();
                }

                // Reinitialize any other JavaScript components
                this.initializeOtherComponents();
            }

            initializeVueComponents() {
                // Look for Vue component mounting points
                const vueElements = this.dynamicContent.querySelectorAll('[data-vue-component]');
                vueElements.forEach(element => {
                    const componentName = element.getAttribute('data-vue-component');
                    if (window[componentName]) {
                        window.createApp(window[componentName]).mount(element);
                    }
                });
            }

            initializeOtherComponents() {
                // Reinitialize any other JavaScript components that might be needed
                // This could include form validation, autocomplete, etc.

                // Reinitialize form submissions to use AJAX
                this.interceptFormSubmissions();
            }

            interceptFormSubmissions() {
                const forms = this.dynamicContent.querySelectorAll('form');
                forms.forEach(form => {
                    form.addEventListener('submit', (e) => {
                        if (this.shouldInterceptForm(form)) {
                            e.preventDefault();
                            this.submitForm(form);
                        }
                    });
                });
            }

            shouldInterceptForm(form) {
                const action = form.getAttribute('action');
                return action && (
                    action.includes('/vendor/products') ||
                    action.includes('/products-manager/products')
                );
            }

            async submitForm(form) {
                try {
                    this.showLoading();

                    const formData = new FormData(form);
                    const method = form.getAttribute('method') || 'POST';
                    let action = form.getAttribute('action');

                    // Convert products-manager URLs to vendor URLs for form submissions
                    if (action.includes('/products-manager/products')) {
                        action = action.replace('/products-manager/products', '/vendor/products');
                    }

                    const response = await fetch(action, {
                        method: method,
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': this.csrfToken
                        }
                    });

                    if (response.redirected) {
                        // Handle redirect after form submission
                        let redirectUrl = response.url;
                        if (redirectUrl.includes('/vendor/products')) {
                            redirectUrl = redirectUrl.replace('/vendor/products', '/products-manager/products');
                        }
                        this.loadContent(redirectUrl, true);
                    } else if (response.ok) {
                        const html = await response.text();
                        this.updateContent(html);
                    } else {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                } catch (error) {
                    console.error('Form submission error:', error);
                    this.showError('Failed to submit form. Please try again.');
                } finally {
                    this.hideLoading();
                }
            }

            setupAutoRefreshCleanup() {
                // Clean up session storage when navigating away from the page
                window.addEventListener('beforeunload', () => {
                    // Only clear if we're actually navigating away (not refreshing)
                    if (!performance.getEntriesByType('navigation')[0] ||
                        performance.getEntriesByType('navigation')[0].type !== 'reload') {
                        sessionStorage.removeItem('pm_create_ajax_refreshed');
                    }
                });

                // Also clean up when the page becomes hidden (user switches tabs, etc.)
                document.addEventListener('visibilitychange', () => {
                    if (document.visibilityState === 'hidden') {
                        // Set a timeout to clear the flag after a reasonable time
                        setTimeout(() => {
                            if (document.visibilityState === 'hidden') {
                                sessionStorage.removeItem('pm_create_ajax_refreshed');
                            }
                        }, 30000); // 30 seconds
                    }
                });
            }

            checkAutoRefresh(previousUrl = null) {
                // Check if this is the create page loaded via AJAX navigation
                const currentUrl = window.location.href;
                const targetPath = '/products-manager/products/create';



                // Only proceed if we're on the exact target URL
                if (!currentUrl.includes(targetPath)) {
                    console.log('🔄 Auto-refresh: Not on target URL, skipping');
                    return;
                }

                // Check if page has already been refreshed to prevent infinite loops
                const sessionRefreshKey = 'pm_create_ajax_refreshed';

                // Use both sessionStorage and a URL parameter to track refresh state
                const urlParams = new URLSearchParams(window.location.search);
                const hasRefreshParam = urlParams.has('auto_refreshed');
                const hasSessionFlag = sessionStorage.getItem(sessionRefreshKey) === 'true';

                if (hasRefreshParam || hasSessionFlag) {
                    console.log('🔄 Auto-refresh: Page already refreshed, skipping to prevent loop');
                    // Clean up the URL parameter if it exists
                    if (hasRefreshParam) {
                        urlParams.delete('auto_refreshed');
                        const newUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() : '');
                        window.history.replaceState({}, '', newUrl);
                    }
                    return;
                }

                // Check if we came from the products listing page (AJAX navigation)
                // Use the previousUrl parameter passed from the navigation
                const isFromProductsListing = previousUrl && previousUrl.includes('/products-manager/products') &&
                                            !previousUrl.includes('/products-manager/products/create');



                // Only refresh if we navigated from the products listing page via AJAX
                if (!isFromProductsListing) {
                    console.log('🔄 Auto-refresh: Skipping - not from AJAX navigation from products listing');
                    return;
                }

                // Set flags to prevent future refreshes
                sessionStorage.setItem(sessionRefreshKey, 'true');

                // Perform the refresh after a small delay to ensure content is fully loaded
                setTimeout(() => {
                    this.performAutoRefresh();
                }, 100);
            }

            performAutoRefresh() {
                console.log('🔄 Auto-refresh: Performing automatic page refresh for Products Manager create page (from AJAX navigation)');

                // Add a parameter to track that we've refreshed
                const currentUrl = new URL(window.location);
                currentUrl.searchParams.set('auto_refreshed', '1');

                // Perform the refresh
                window.location.href = currentUrl.toString();
            }

            showLoading() {
                this.loadingIndicator.classList.remove('hidden');
            }

            hideLoading() {
                this.loadingIndicator.classList.add('hidden');
            }

            showError(message) {
                this.flashMessagesContainer.innerHTML = `
                    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">${message}</span>
                        <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                            <i class="fas fa-times cursor-pointer" onclick="this.parentElement.parentElement.style.display='none'"></i>
                        </span>
                    </div>
                `;
            }
        }

        // Language switching function
        function switchLanguage(locale) {
            // Show loading state on the clicked language option
            const languageLinks = document.querySelectorAll('a[href*="/language/"]');
            languageLinks.forEach(link => {
                if (link.href.includes('/language/' + locale)) {
                    const originalContent = link.innerHTML;
                    link.innerHTML = '<i class="fas fa-spinner fa-spin mr-3"></i><span>{{ __("products_manager.switching") }}</span>';
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

        // Mobile sidebar toggle
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('pm-sidebar');
            const sidebarOverlay = document.getElementById('pm-sidebar-overlay');
            const mobileMenuToggle = document.getElementById('pm-mobile-menu-toggle');

            if (!sidebar || !sidebarOverlay || !mobileMenuToggle) {
                return;
            }

            const openSidebar = () => {
                sidebar.classList.remove('-translate-x-full');
                sidebarOverlay.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            };

            const closeSidebar = () => {
                sidebar.classList.add('-translate-x-full');
                sidebarOverlay.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            };

            mobileMenuToggle.addEventListener('click', function() {
                if (sidebar.classList.contains('-translate-x-full')) {
                    openSidebar();
                } else {
                    closeSidebar();
                }
            });

            sidebarOverlay.addEventListener('click', closeSidebar);

            window.addEventListener('resize', function() {
                if (window.innerWidth >= 768) {
                    closeSidebar();
                }
            });
        });

        // Initialize AJAX navigation when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            window.productsManagerAjaxNav = new ProductsManagerAjaxNavigation();
        });
    </script>

    @yield('scripts')
</body>
</html>
