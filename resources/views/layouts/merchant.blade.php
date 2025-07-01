<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Merchant Dashboard') | Dala3Chic</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts - For Discord-like look -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom styles for Discord-inspired UI -->
    <style>
        :root {
            /* Light theme color palette - using merchant-specific colors */
            --discord-primary: #1E5EFF; /* Primary blue from Figma */
            --discord-primary-hover: #1a52e6;
            --discord-dark: #f2f3f5;
            --discord-darker: #ffffff;
            --discord-darkest: #e3e5e8;
            --discord-light: #747f8d;
            --discord-lighter: #4e5d94;
            --discord-lightest: #2e3338;
            --discord-green: #3ba55d;
            --discord-red: #ed4245;
            --discord-yellow: #faa81a;
            --discord-dark-hover: #e9eaeb;
        }

        body {
            font-family: 'Open Sans', sans-serif;
            background-color: var(--discord-dark);
            color: var(--discord-lightest);
            min-height: 100vh;
            display: flex;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        /* Main container */
        .app-container {
            display: flex;
            width: 100%;
            height: 100vh;
        }



        /* Channel (left) sidebar */
        .channel-sidebar {
            width: 240px;
            background-color: var(--discord-darker);
            display: flex;
            flex-direction: column;
            border-right: 1px solid var(--discord-darkest);
            overflow-y: auto;
        }

        .server-header {
            padding: 16px;
            border-bottom: 1px solid var(--discord-darkest);
            background-color: var(--discord-darker);
        }

        .server-header h2 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
            color: var(--discord-lightest);
        }

        .channel-list {
            flex: 1;
            padding: 16px 8px;
        }

        .channel-category {
            margin-bottom: 24px;
        }

        .category-header {
            font-size: 12px;
            font-weight: 600;
            color: var(--discord-light);
            text-transform: uppercase;
            margin-bottom: 8px;
            padding: 0 8px;
        }

        .channel-item {
            display: flex;
            align-items: center;
            padding: 8px 12px;
            margin: 2px 0;
            border-radius: 4px;
            color: var(--discord-light);
            text-decoration: none;
            transition: all 0.2s ease;
            font-size: 14px;
        }

        .channel-item:hover {
            background-color: var(--discord-dark-hover);
            color: var(--discord-lightest);
            text-decoration: none;
        }

        .channel-item.active {
            background-color: var(--discord-primary);
            color: white;
        }

        .channel-item i {
            margin-right: 12px;
            width: 16px;
            text-align: center;
        }

        /* Main content area */
        .content-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            background-color: var(--discord-darker);
            overflow: hidden;
        }

        .content-header {
            padding: 16px 24px;
            border-bottom: 1px solid var(--discord-darkest);
            background-color: var(--discord-darker);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .content-header-title {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
            color: var(--discord-lightest);
        }

        .content-body {
            flex: 1;
            padding: 24px;
            overflow-y: auto;
            background-color: var(--discord-dark);
        }

        /* Discord-style cards */
        .discord-card {
            background-color: var(--discord-darker);
            border: 1px solid var(--discord-darkest);
            border-radius: 8px;
            margin-bottom: 16px;
            overflow: hidden;
        }

        .discord-card-header {
            padding: 16px 20px;
            border-bottom: 1px solid var(--discord-darkest);
            font-weight: 600;
            color: var(--discord-lightest);
            background-color: var(--discord-darker);
        }

        .discord-card-body {
            padding: 20px;
        }

        /* Discord-style buttons */
        .discord-btn {
            background-color: var(--discord-primary);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .discord-btn:hover {
            background-color: var(--discord-primary-hover);
            color: white;
            text-decoration: none;
        }

        .discord-btn-secondary {
            background-color: var(--discord-light);
            color: white;
        }

        .discord-btn-secondary:hover {
            background-color: var(--discord-lighter);
            color: white;
        }

        /* Discord-style tables */
        .discord-table {
            width: 100%;
            border-collapse: collapse;
            background-color: var(--discord-darker);
        }

        .discord-table th {
            background-color: var(--discord-darkest);
            color: var(--discord-lightest);
            padding: 12px;
            text-align: left;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            border-bottom: 1px solid var(--discord-darkest);
        }

        .discord-table td {
            padding: 12px;
            border-bottom: 1px solid var(--discord-darkest);
            color: var(--discord-lightest);
        }

        .discord-table tr:hover {
            background-color: var(--discord-dark-hover);
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
        }
    </style>

    @yield('styles')
</head>
<body>
    <div class="app-container">


        <!-- Channel (left) sidebar -->
        <div class="channel-sidebar">
            <div class="server-header">
                <h2>Merchant Dashboard</h2>
            </div>

            <div class="channel-list">
                <!-- Main Navigation -->
                <div class="channel-category">
                    <div class="category-header">Main</div>
                    <a href="{{ route('merchant.dashboard') }}" class="channel-item {{ request()->routeIs('merchant.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                    <a href="{{ route('merchant.products.index') }}" class="channel-item {{ request()->routeIs('merchant.products.*') ? 'active' : '' }}">
                        <i class="fas fa-box"></i>
                        Products
                    </a>
                    <a href="{{ route('merchant.services.index') }}" class="channel-item {{ request()->routeIs('merchant.services.*') ? 'active' : '' }}">
                        <i class="fas fa-concierge-bell"></i>
                        Services
                    </a>
                    <a href="{{ route('merchant.orders.index') }}" class="channel-item {{ request()->routeIs('merchant.orders.*') ? 'active' : '' }}">
                        <i class="fas fa-shopping-cart"></i>
                        Orders
                    </a>
                    <a href="{{ route('merchant.customers.index') }}" class="channel-item {{ request()->routeIs('merchant.customers.*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i>
                        Customers
                    </a>
                    <a href="{{ route('merchant.reports.index') }}" class="channel-item {{ request()->routeIs('merchant.reports.*') ? 'active' : '' }}">
                        <i class="fas fa-chart-bar"></i>
                        Reports
                    </a>
                </div>

                <!-- Settings -->
                <div class="channel-category">
                    <div class="category-header">Settings</div>
                    <a href="{{ route('merchant.settings.personal') }}" class="channel-item {{ request()->routeIs('merchant.settings.personal') ? 'active' : '' }}">
                        <i class="fas fa-user-cog"></i>
                        Personal Settings
                    </a>
                    <a href="{{ route('merchant.settings.global') }}" class="channel-item {{ request()->routeIs('merchant.settings.global') ? 'active' : '' }}">
                        <i class="fas fa-cogs"></i>
                        Global Settings
                    </a>
                    <a href="{{ route('merchant.mini-store') }}" class="channel-item {{ request()->routeIs('merchant.mini-store') ? 'active' : '' }}">
                        <i class="fas fa-store-alt"></i>
                        Mini Store
                    </a>
                </div>

                <!-- Account -->
                <div class="channel-category">
                    <div class="category-header">Account</div>
                    <a href="{{ route('logout') }}" class="channel-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>
                </div>
            </div>
        </div>

        <!-- Main content area -->
        <div class="content-area">
            <div class="content-header">
                <h1 class="content-header-title">@yield('header', 'Dashboard')</h1>
            </div>

            <div class="content-body">
                @if(session('success'))
                    <div class="alert alert-success" role="alert" style="background-color: var(--discord-green); color: white; border: none;">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger" role="alert" style="background-color: var(--discord-red); color: white; border: none;">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </div>

        <!-- Optional right sidebar for store stats -->
        @hasSection('right_sidebar')
            <div class="active-users">
                @yield('right_sidebar')
            </div>
        @endif
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
        $(document).ready(function() {
            // Add mobile menu toggle button
            if ($(window).width() <= 768) {
                $('.content-header').prepend('<button class="mobile-menu-toggle" style="background: none; border: none; color: var(--discord-lightest); font-size: 18px; margin-right: 12px;"><i class="fas fa-bars"></i></button>');
            }

            // Mobile menu toggle
            $(document).on('click', '.mobile-menu-toggle', function() {
                $('.channel-sidebar').toggleClass('show');
            });

            // Close mobile menu when clicking outside
            $(document).click(function(event) {
                if ($(window).width() <= 768) {
                    if (!$(event.target).closest('.channel-sidebar, .mobile-menu-toggle').length) {
                        $('.channel-sidebar').removeClass('show');
                    }
                }
            });

            // Handle window resize
            $(window).resize(function() {
                if ($(window).width() > 768) {
                    $('.mobile-menu-toggle').remove();
                    $('.channel-sidebar').removeClass('show');
                } else if ($('.mobile-menu-toggle').length === 0) {
                    $('.content-header').prepend('<button class="mobile-menu-toggle" style="background: none; border: none; color: var(--discord-lightest); font-size: 18px; margin-right: 12px;"><i class="fas fa-bars"></i></button>');
                }
            });
        });
    </script>

    @yield('scripts')
    @stack('scripts')
</body>
</html>
