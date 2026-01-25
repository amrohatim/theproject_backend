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
    <title>@yield('title', 'Merchant Dashboard') | Dala3Chic</title>

    <!-- Access Control Meta Tags -->
    @if(session('show_access_modal'))
        <meta name="show-access-modal" content="true">
        <meta name="modal-title" content="{{ session('modal_title', 'Access Restricted') }}">
        <meta name="modal-message" content="{{ session('modal_message', 'Access denied') }}">
        <meta name="license-status" content="{{ session('license_status', '') }}">
        <meta name="registration-step" content="{{ session('registration_step', '') }}">
    @endif

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts - For modern dashboard -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Modern Dashboard CSS -->
    <link href="{{ asset('css/modern-merchant-dashboard.css') }}" rel="stylesheet">
    
    <!-- RTL CSS for Arabic -->
    @if($isRtl)
    <link href="{{ asset('css/rtl.css') }}" rel="stylesheet">
    @endif

    <!-- Custom styles for modern merchant dashboard -->
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

            /* Legacy compatibility */
            --discord-primary: var(--primary-blue);
            --discord-primary-hover: var(--primary-blue-hover);
            --discord-dark: var(--gray-50);
            --discord-darker: #ffffff;
            --discord-darkest: var(--gray-200);
            --discord-light: var(--gray-500);
            --discord-lighter: var(--gray-600);
            --discord-lightest: var(--gray-900);
            --discord-green: var(--green-600);
            --discord-red: var(--red-600);
            --discord-yellow: var(--yellow-600);
            --discord-dark-hover: var(--gray-100);
        }

        body {
            font-family: 'Open Sans', sans-serif;
            background-color: var(--gray-50);
            color: var(--gray-900);
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        /* Main layout structure */
        .dashboard-layout {
            min-height: 100vh;
            background-color: var(--gray-50);
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 50;
            width: 256px;
            height: 100vh;
            background-color: white;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            -webkit-overflow-scrolling: touch;
        }

        .sidebar.open {
            transform: translateX(0);
        }

        @media (min-width: 1024px) {
            .sidebar {
                transform: translateX(0);
            }
        }

        /* Sidebar header */
        .sidebar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 64px;
            padding: 0 24px;
            border-bottom: 1px solid var(--gray-200);
        }

        .sidebar-brand {
            font-size: 20px;
            font-weight: 700;
            color: var(--gray-900);
            margin: 0;
        }

        .sidebar-close {
            display: block;
            padding: 8px;
            border-radius: 6px;
            color: var(--gray-400);
            background: none;
            border: none;
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .sidebar-close:hover {
            color: var(--gray-600);
        }

        @media (min-width: 1024px) {
            .sidebar-close {
                display: none;
            }
        }

        /* Navigation */
        .sidebar-nav {
            margin-top: 24px;
            padding: 0 12px;
            flex: 1;
            overflow-y: auto;
            padding-bottom: 24px;
        }

        .nav-section {
            margin-bottom: 32px;
        }

        .nav-section-title {
            padding: 0 12px;
            font-size: 12px;
            font-weight: 600;
            color: var(--gray-500);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 12px;
        }

        .nav-items {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .nav-item {
            margin-bottom: 4px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 8px 12px;
            font-size: 14px;
            font-weight: 500;
            border-radius: 8px;
            color: var(--gray-600);
            text-decoration: none;
            transition: all 0.2s ease;
            position: relative;
        }

        .nav-link:hover {
            color: var(--gray-900);
            background-color: var(--gray-100);
            text-decoration: none;
        }

        .nav-link.active {
            background-color: var(--primary-blue-light);
            color: var(--primary-blue);
            border-right: 2px solid var(--primary-blue);
        }

        .nav-link i {
            width: 20px;
            height: 20px;
            margin-right: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Logout section */
        .sidebar-footer {
            margin-top: auto;
            padding-top: 24px;
            border-top: 1px solid var(--gray-200);
            padding-bottom: 16px;
        }

        /* Mobile sidebar overlay */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 40;
        }

        @media (min-width: 1024px) {
            .sidebar-overlay {
                display: none !important;
            }
        }

        /* Main content area */
        .main-content {
            margin-left: 0;
            transition: margin-left 0.3s ease-in-out;
        }

        @media (min-width: 1024px) {
            .main-content {
                margin-left: 256px;
            }
        }

        /* Top header */
        .top-header {
            background-color: white;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            border-bottom: 1px solid var(--gray-200);
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 64px;
            padding: 0 16px;
        }

        @media (min-width: 640px) {
            .header-content {
                padding: 0 24px;
            }
        }

        @media (min-width: 1024px) {
            .header-content {
                padding: 0 32px;
            }
        }

        .header-left {
            display: flex;
            align-items: center;
        }

        .mobile-menu-toggle {
            display: block;
            padding: 8px;
            border-radius: 6px;
            color: var(--gray-400);
            background: none;
            border: none;
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .mobile-menu-toggle:hover {
            color: var(--gray-600);
        }

        @media (min-width: 1024px) {
            .mobile-menu-toggle {
                display: none;
            }
        }

        .page-title {
            margin-left: 16px;
            font-size: 24px;
            font-weight: 700;
            color: var(--gray-900);
        }

        @media (min-width: 1024px) {
            .page-title {
                margin-left: 0;
            }
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .header-action-btn {
            padding: 8px;
            color: var(--gray-400);
            background: none;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .header-action-btn:hover {
            color: var(--gray-600);
            background-color: var(--gray-100);
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            background-color: var(--primary-blue);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
            font-weight: 500;
        }

        /* Page content */
        .page-content {
            padding: 16px;
        }

        @media (min-width: 640px) {
            .page-content {
                padding: 24px;
            }
        }

        @media (min-width: 1024px) {
            .page-content {
                padding: 32px;
            }
        }

        /* Modern cards */
        .discord-card {
            background-color: white;
            border: 1px solid var(--gray-200);
            border-radius: 12px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            margin-bottom: 24px;
            overflow: hidden;
        }

        .discord-card-header {
            padding: 16px 24px;
            border-bottom: 1px solid var(--gray-200);
            font-weight: 600;
            color: var(--gray-900);
            background-color: white;
        }

        .discord-card-body {
            padding: 24px;
        }

        /* Modern buttons */
        .discord-btn {
            display: inline-flex;
            align-items: center;
            padding: 8px 16px;
            background-color: var(--primary-blue);
            color: white;
            font-size: 14px;
            font-weight: 500;
            border-radius: 8px;
            border: none;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .discord-btn:hover {
            background-color: var(--primary-blue-hover);
            color: white;
            text-decoration: none;
        }

        .discord-btn:focus {
            outline: none;
            box-shadow: 0 0 0 2px var(--primary-blue-light);
        }

        .discord-btn-secondary {
            background-color: var(--gray-300);
            color: var(--gray-700);
            border: 1px solid var(--gray-300);
        }

        .discord-btn-secondary:hover {
            background-color: var(--gray-100);
            color: var(--gray-700);
        }

        /* Modern tables */
        .discord-table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
        }

        .discord-table th {
            background-color: var(--gray-50);
            color: var(--gray-500);
            padding: 16px 24px;
            text-align: left;
            font-weight: 500;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid var(--gray-200);
        }

        .discord-table td {
            padding: 16px 24px;
            border-bottom: 1px solid var(--gray-200);
            color: var(--gray-900);
        }

        .discord-table tr:hover {
            background-color: var(--gray-50);
        }

        /* Stats cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background-color: var(--discord-darker);
            border: 1px solid var(--discord-darkest);
            border-radius: 8px;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .stat-info h3 {
            margin: 0 0 4px 0;
            font-size: 24px;
            font-weight: 700;
            color: var(--discord-lightest);
        }

        .stat-info p {
            margin: 0;
            font-size: 14px;
            color: var(--discord-light);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
        }

        /* RTL Layout Adjustments */
        [dir="rtl"] .sidebar {
            left: auto;
            right: 0;
            transform: translateX(100%);
        }

        [dir="rtl"] .sidebar.open {
            transform: translateX(0);
        }

        @media (min-width: 1024px) {
            [dir="rtl"] .sidebar {
                transform: translateX(0);
            }
        }

        [dir="rtl"] .main-content {
            margin-left: 0;
            margin-right: 0;
        }

        @media (min-width: 1024px) {
            [dir="rtl"] .main-content {
                margin-left: 0;
                margin-right: 256px;
            }
        }

        [dir="rtl"] .nav-link.active {
            border-right: none;
            border-left: 2px solid var(--primary-blue);
        }

        [dir="rtl"] .nav-link i {
            margin-right: 0;
            margin-left: 12px;
        }

        [dir="rtl"] .page-title {
            margin-left: 0;
            margin-right: 16px;
        }

        @media (min-width: 1024px) {
            [dir="rtl"] .page-title {
                margin-right: 0;
            }
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .channel-sidebar {
                position: fixed;
                left: -240px;
                top: 0;
                height: 100vh;
                z-index: 1000;
                transition: left 0.3s ease;
            }

            .channel-sidebar.show {
                left: 0;
            }

            .content-area {
                margin-left: 0;
            }

            [dir="rtl"] .channel-sidebar {
                left: auto;
                right: -240px;
                transition: right 0.3s ease;
            }

            [dir="rtl"] .channel-sidebar.show {
                right: 0;
            }
        }
    </style>

    <!-- Merchant Search CSS -->
    <link rel="stylesheet" href="{{ asset('css/merchant-search.css') }}">
    <!-- Enhanced Merchant Search CSS -->
    <link rel="stylesheet" href="{{ asset('css/enhanced-merchant-search.css') }}">
    <!-- Enhanced Filter Components CSS -->
    <link rel="stylesheet" href="{{ asset('css/enhanced-filter-components.css') }}">
    <!-- Enhanced Results Display CSS -->
    <link rel="stylesheet" href="{{ asset('css/enhanced-results-display.css') }}">
    <!-- Advanced Animations CSS -->
    <link rel="stylesheet" href="{{ asset('css/advanced-animations.css') }}">
    <!-- Interactive States CSS -->
    <link rel="stylesheet" href="{{ asset('css/interactive-states.css') }}">
    <!-- Mobile Responsive Enhancements CSS -->
    <link rel="stylesheet" href="{{ asset('css/mobile-responsive-enhancements.css') }}">
    <!-- Accessibility and Performance CSS -->
    <link rel="stylesheet" href="{{ asset('css/accessibility-performance.css') }}">

    <!-- Modern Forms CSS -->
    <link rel="stylesheet" href="{{ asset('css/modern-forms.css') }}">
    <!-- Modern Buttons CSS -->
    <link rel="stylesheet" href="{{ asset('css/modern-buttons.css') }}">

    <!-- Access Control CSS -->
    <link rel="stylesheet" href="{{ asset('css/merchant-access-control.css') }}">

    <!-- Vite Tailwind CSS -->
    @vite(['resources/css/app.css'])

    <!-- Temporary CDN Tailwind CSS for testing -->
    <script src="https://cdn.tailwindcss.com"></script>

    @yield('styles')
    @stack('styles')
</head>
<body>
    <div class="dashboard-layout">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h1 class="sidebar-brand font-it text-[14px]">Merchant | <span class="text-blue-600">Dashboard</span></h1>
                <button class="sidebar-close" id="sidebarClose">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section">
                    <h3 class="nav-section-title">{{ __('merchant.main') }}</h3>
                    <ul class="nav-items">
                        <li class="nav-item">
                            <a href="{{ route('merchant.dashboard') }}" class="nav-link {{ request()->routeIs('merchant.dashboard') ? 'active' : '' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                </svg>
                                {{ __('merchant.dashboard') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('merchant.products.index') }}" class="nav-link {{ request()->routeIs('merchant.products.*') ? 'active' : '' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"></path>
                                </svg>
                                {{ __('merchant.products') }}
                            </a>
                        </li>
                        {{-- <li class="nav-item">
                            <a href="{{ route('merchant.services.index') }}" class="nav-link {{ request()->routeIs('merchant.services.*') ? 'active' : '' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                {{ __('merchant.services') }}
                            </a>
                        </li> --}}
                        <li class="nav-item">
                            <a href="{{ route('merchant.deals.index') }}" class="nav-link {{ request()->routeIs('merchant.deals.*') ? 'active' : '' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                {{ __('merchant.deals') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('merchant.orders.index') }}" class="nav-link {{ request()->routeIs('merchant.orders.*') ? 'active' : '' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                                {{ __('merchant.orders') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('merchant.jobs.index') }}" class="nav-link {{ request()->routeIs('merchant.jobs.*') ? 'active' : '' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m2 0a2 2 0 012 2v6H5v-6a2 2 0 012-2m2-7h6m-6 0V7a2 2 0 012-2h2a2 2 0 012 2v2"></path>
                                </svg>
                                {{ __('merchant.jobs') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('merchant.customers.index') }}" class="nav-link {{ request()->routeIs('merchant.customers.*') ? 'active' : '' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                                {{ __('merchant.customers') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('merchant.reports.index') }}" class="nav-link {{ request()->routeIs('merchant.reports.*') ? 'active' : '' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                {{ __('merchant.reports') }}
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="nav-section">
                    <h3 class="nav-section-title">{{ __('merchant.settings') }}</h3>
                    <ul class="nav-items">
                        <li class="nav-item">
                            <a href="{{ route('merchant.subscription.index') }}" class="nav-link {{ request()->routeIs('merchant.subscription.*') ? 'active' : '' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                                {{ __('messages.subscription') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('merchant.settings.personal') }}" class="nav-link {{ request()->routeIs('merchant.settings.personal') ? 'active' : '' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                {{ __('merchant.personal_settings') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('merchant.settings.global') }}" class="nav-link {{ request()->routeIs('merchant.settings.global') ? 'active' : '' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                {{ __('merchant.global_settings') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('merchant.mini-store') }}" class="nav-link {{ request()->routeIs('merchant.mini-store') ? 'active' : '' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                                {{ __('merchant.mini_store') }}
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Language Settings -->
                <div class="nav-section">
                    <h3 class="nav-section-title">{{ __('merchant.language_settings') }}</h3>
                    @php
                        $currentLocale = app()->getLocale();
                        $supportedLocales = [
                            'en' => ['name' => 'English', 'native' => 'English', 'flag' => '🇺🇸'],
                            'ar' => ['name' => 'Arabic', 'native' => 'العربية', 'flag' => '🇸🇦']
                        ];
                    @endphp
                    <ul class="nav-items">
                        @foreach($supportedLocales as $locale => $details)
                            <li class="nav-item">
                                <a href="{{ url('/language/' . $locale) }}"
                                   class="nav-link {{ $locale === $currentLocale ? 'active' : '' }}"
                                   onclick="switchLanguageMerchant('{{ $locale }}'); return false;">
                                    <span class="text-lg mr-3 rtl:mr-0 rtl:ml-3">{{ $details['flag'] }}</span>
                                    <span>{{ $details['native'] }}</span>
                                    @if($locale === $currentLocale)
                                        <i class="fas fa-check ml-auto rtl:ml-0 rtl:mr-auto text-blue-600"></i>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="sidebar-footer">
                    <ul class="nav-items">
                        <li class="nav-item">
                            <a href="{{ route('logout') }}" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                {{ __('merchant.logout') }}
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Header -->
            <header class="top-header">
                <div class="header-content">
                    <div class="header-left">
                        <button class="mobile-menu-toggle" id="mobileMenuToggle">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                        <h1 class="page-title">@yield('header', 'Dashboard')</h1>
                    </div>
                    <div class="header-right">
                        <button class="header-action-btn">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM9 7H4l5-5v5z"></path>
                            </svg>
                        </button>
                        <div class="user-avatar">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="page-content">
                @if(session('success'))
                    <div class="alert alert-success" role="alert" style="background-color: var(--green-100); color: var(--green-800); border: 1px solid var(--green-600); border-radius: 8px; padding: 12px 16px; margin-bottom: 24px;">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger" role="alert" style="background-color: var(--red-100); color: var(--red-800); border: 1px solid var(--red-600); border-radius: 8px; padding: 12px 16px; margin-bottom: 24px;">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>

        <!-- Mobile sidebar overlay -->
        <div class="sidebar-overlay" id="sidebarOverlay" style="display: none;"></div>
    </div>

    <!-- Logout form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Custom JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const sidebarClose = document.getElementById('sidebarClose');

            // Mobile menu toggle
            if (mobileMenuToggle) {
                mobileMenuToggle.addEventListener('click', function() {
                    sidebar.classList.add('open');
                    sidebarOverlay.style.display = 'block';
                    document.body.style.overflow = 'hidden';
                });
            }

            // Close sidebar
            function closeSidebar() {
                sidebar.classList.remove('open');
                sidebarOverlay.style.display = 'none';
                document.body.style.overflow = '';
            }

            if (sidebarClose) {
                sidebarClose.addEventListener('click', closeSidebar);
            }

            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', closeSidebar);
            }

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1024) {
                    closeSidebar();
                }
            });
        });

        // Language switching function for merchant dashboard
        function switchLanguageMerchant(locale) {
            // Show loading state on the clicked language option
            const languageLinks = document.querySelectorAll('a[href*="/language/"]');
            languageLinks.forEach(link => {
                if (link.href.includes('/language/' + locale)) {
                    const originalContent = link.innerHTML;
                    link.innerHTML = '<i class="fas fa-spinner fa-spin mr-3 rtl:mr-0 rtl:ml-3"></i><span>{{ __("merchant.switching") }}</span>';
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

    <!-- Merchant Search JavaScript -->
    <script src="{{ asset('js/merchant-search.js') }}"></script>
    <!-- Enhanced Merchant Search JavaScript -->
    <script src="{{ asset('js/enhanced-merchant-search.js') }}"></script>
    <!-- Enhanced Filter Components JavaScript -->
    <script src="{{ asset('js/enhanced-filter-components.js') }}"></script>
    <!-- Enhanced Results Display JavaScript -->
    <script src="{{ asset('js/enhanced-results-display.js') }}"></script>
    <!-- Advanced Animations JavaScript -->
    <script src="{{ asset('js/advanced-animations.js') }}"></script>
    <!-- Interactive States JavaScript -->
    <script src="{{ asset('js/interactive-states.js') }}"></script>
    <!-- Accessibility and Performance JavaScript -->
    <script src="{{ asset('js/accessibility-performance.js') }}"></script>

    <!-- Access Control JavaScript -->
    <script src="{{ asset('js/merchant-access-control.js') }}"></script>

    @yield('scripts')
    @stack('scripts')
</body>
</html>
