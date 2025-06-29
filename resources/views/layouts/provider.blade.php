<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Provider Dashboard') | Dala3Chic</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts - For Discord-like look -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom styles for Discord-inspired UI -->
    <style>
        :root {
            /* Light theme color palette */
            --discord-primary: #5865F2;
            --discord-primary-hover: #4752c4;
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

        /* Server sidebar (left-most) - removed */
        .server-sidebar {
            display: none; /* Hide the server sidebar */
        }

        .server-icon {
            width: 48px;
            height: 48px;
            background-color: var(--discord-darker);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 8px;
            color: var(--discord-lightest);
            cursor: pointer;
            transition: border-radius 0.2s, background-color 0.2s;
            position: relative;
        }

        .server-icon:hover {
            background-color: var(--discord-primary);
            border-radius: 16px;
        }

        .server-icon.active {
            background-color: var(--discord-primary);
            border-radius: 16px;
        }

        .server-icon.active::before {
            content: '';
            position: absolute;
            left: -15px;
            width: 8px;
            height: 40px;
            background-color: var(--discord-lightest);
            border-radius: 0 4px 4px 0;
        }

        .server-divider {
            width: 32px;
            height: 2px;
            background-color: var(--discord-darker);
            margin: 8px 0;
        }

        /* Channel sidebar */
        .channel-sidebar {
            width: 240px;
            background-color: var(--discord-darker);
            height: 100vh;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            border-right: 1px solid #e0e1e5;
        }

        .server-header {
            padding: 16px;
            border-bottom: 1px solid var(--discord-darkest);
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .server-header h2 {
            font-size: 16px;
            margin: 0;
            color: var(--discord-lightest);
        }

        .dropdown-toggle {
            background: none;
            border: none;
            color: var(--discord-lighter);
            cursor: pointer;
        }

        .channel-category {
            padding: 16px 16px 4px;
            text-transform: uppercase;
            font-size: 12px;
            font-weight: 700;
            color: var(--discord-light);
            letter-spacing: 0.02em;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .channel-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .channel-item {
            padding: 6px 8px;
            margin: 0 8px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            color: var(--discord-light);
            cursor: pointer;
            transition: background-color 0.1s;
        }

        .channel-item:hover {
            background-color: rgba(79, 84, 92, 0.32);
            color: var(--discord-lighter);
        }

        .channel-item.active {
            background-color: rgba(79, 84, 92, 0.32);
            color: var(--discord-lightest);
        }

        .channel-icon {
            margin-right: 8px;
            color: var(--discord-light);
        }

        .channel-name {
            flex-grow: 1;
            font-size: 14px;
        }

        .user-area {
            margin-top: auto;
            padding: 10px;
            display: flex;
            align-items: center;
            border-top: 1px solid #e0e1e5;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            margin-right: 8px;
            background-color: var(--discord-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--discord-lightest);
            font-weight: 600;
        }

        .user-details {
            flex-grow: 1;
        }

        .user-name {
            font-weight: 600;
            font-size: 14px;
            color: var(--discord-lightest);
        }

        .user-status {
            font-size: 12px;
            color: var(--discord-light);
        }

        .user-controls {
            display: flex;
            gap: 8px;
        }

        .user-control-icon {
            color: var(--discord-light);
            cursor: pointer;
        }

        .user-control-icon:hover {
            color: var(--discord-lightest);
        }

        /* Main content area */
        .content-area {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
            margin-left: 0; /* Remove margin since server sidebar is gone */
        }

        .content-header {
            padding: 12px 16px;
            border-bottom: 1px solid var(--discord-darkest);
            display: flex;
            align-items: center;
            flex-shrink: 0;
        }

        .content-header-title {
            font-weight: 600;
            font-size: 16px;
            margin: 0;
            color: var(--discord-lightest);
        }

        .content-body {
            flex-grow: 1;
            padding: 20px;
            overflow-y: auto;
        }

        /* Cards and components */
        .discord-card {
            background-color: var(--discord-darker);
            border-radius: 5px;
            padding: 16px;
            margin-bottom: 20px;
            border: 1px solid #e0e1e5;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.12);
        }

        .discord-card-header {
            padding: 0 0 16px 0;
            margin-bottom: 16px;
            border-bottom: 1px solid #e0e1e5;
            font-weight: 600;
            color: var(--discord-lightest);
            font-size: 16px;
        }

        .discord-input {
            background-color: var(--discord-darkest);
            border: none;
            border-radius: 4px;
            padding: 10px 12px;
            color: var(--discord-lightest);
            margin-bottom: 16px;
        }

        .discord-input:focus {
            outline: none;
            box-shadow: 0 0 0 2px var(--discord-primary);
        }

        .discord-btn {
            background-color: var(--discord-primary);
            color: white;
            border: none;
            border-radius: 4px;
            padding: 8px 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .discord-btn:hover {
            background-color: var(--discord-primary-hover);
        }

        .discord-btn-secondary {
            background-color: var(--discord-darker);
            color: var(--discord-lightest);
        }

        .discord-btn-secondary:hover {
            background-color: #36393f;
        }

        .discord-table {
            width: 100%;
            border-collapse: collapse;
        }

        .discord-table th {
            background-color: var(--discord-darkest);
            padding: 10px 16px;
            text-align: left;
            font-weight: 600;
            color: var(--discord-light);
        }

        .discord-table td {
            padding: 12px 16px;
            border-top: 1px solid var(--discord-darkest);
            color: var(--discord-lighter);
        }

        .discord-table tr:hover td {
            background-color: rgba(79, 84, 92, 0.16);
        }

        /* Active users area (right sidebar) */
        .active-users {
            width: 240px;
            background-color: var(--discord-darker);
            height: 100vh;
            padding: 16px 8px;
            overflow-y: auto;
            flex-shrink: 0;
        }

        .user-category {
            padding: 8px 8px 4px;
            text-transform: uppercase;
            font-size: 12px;
            font-weight: 600;
            color: var(--discord-light);
            margin-bottom: 8px;
        }

        .user-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .user-item {
            padding: 6px 8px;
            margin-bottom: 2px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            color: var(--discord-light);
        }

        .user-item:hover {
            background-color: rgba(79, 84, 92, 0.32);
            color: var(--discord-lighter);
        }

        /* Status indicators */
        .status-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: var(--discord-light);
            position: absolute;
            bottom: 0;
            right: 0;
            border: 2px solid var(--discord-darker);
        }

        .status-online {
            background-color: var(--discord-green);
        }

        .status-idle {
            background-color: var(--discord-yellow);
        }

        .status-dnd {
            background-color: var(--discord-red);
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #202225;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #2a2c30;
        }

        /* Bootstrap overrides */
        .form-control, .form-select {
            background-color: var(--discord-darkest);
            border: none;
            color: var(--discord-lightest);
        }

        .form-control:focus, .form-select:focus {
            background-color: var(--discord-darkest);
            color: var(--discord-lightest);
            box-shadow: 0 0 0 2px var(--discord-primary);
        }

        .btn-primary {
            background-color: var(--discord-primary);
            border: none;
        }

        .btn-primary:hover {
            background-color: var(--discord-primary-hover);
        }

        .card {
            background-color: var(--discord-darker);
            border: none;
        }

        .table {
            color: var(--discord-lightest);
        }

        .modal-content {
            background-color: var(--discord-dark);
            color: var(--discord-lightest);
        }

        /* For loading states */
        .discord-loading {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
        }

        .discord-spinner {
            width: 40px;
            height: 40px;
            border: 4px solid rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            border-top-color: var(--discord-primary);
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Responsive adjustments */
        @media (max-width: 992px) {
            .active-users {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .channel-sidebar {
                position: fixed;
                top: 0;
                left: 0;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                z-index: 1000;
            }

            .channel-sidebar.show {
                transform: translateX(0);
            }

            .content-area {
                margin-left: 0;
            }
        }

        @media (max-width: 576px) {
            .server-sidebar {
                width: 0;
                overflow: hidden;
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
        <!-- Server (left-most) sidebar -->
        <div class="server-sidebar">
            <div class="server-icon active" title="Dashboard">
                <i class="fas fa-store"></i>
            </div>
        </div>

        <!-- Channel (left) sidebar -->
        <div class="channel-sidebar">
            <div class="server-header">
                <h2>Provider Dashboard</h2>
            </div>

            <div class="channel-category">
                <span>Dala3Chic</span>
            </div>
            <ul class="channel-list">
                <li class="channel-item {{ request()->is('provider/dashboard*') ? 'active' : '' }}" onclick="window.location.href='{{ route('provider.dashboard') }}'">
                    <i class="fas fa-tachometer-alt channel-icon"></i>
                    <span class="channel-name">Dashboard</span>
                </li>
                <li class="channel-item {{ request()->is('provider/provider-products*') ? 'active' : '' }}" onclick="window.location.href='{{ route('provider.provider-products.index') }}'">
                    <i class="fas fa-box channel-icon"></i>
                    <span class="channel-name">Products</span>
                </li>
                <li class="channel-item {{ request()->is('provider/locations*') ? 'active' : '' }}" onclick="window.location.href='{{ route('provider.locations.index') }}'">
                    <i class="fas fa-map-marker-alt channel-icon"></i>
                    <span class="channel-name">Locations</span>
                </li>
                <li class="channel-item {{ request()->is('provider/orders*') ? 'active' : '' }}" onclick="window.location.href='{{ route('provider.orders.index') }}'">
                    <i class="fas fa-shopping-cart channel-icon"></i>
                    <span class="channel-name">Orders</span>
                </li>
            </ul>

            <div class="channel-category">
                <span>Settings</span>
            </div>
            <ul class="channel-list">
                <li class="channel-item {{ request()->is('provider/profile*') ? 'active' : '' }}" onclick="window.location.href='{{ route('provider.profile.index') }}'">
                    <i class="fas fa-user channel-icon"></i>
                    <span class="channel-name">Profile</span>
                </li>
                <li class="channel-item" onclick="window.location.href='#'">
                    <i class="fas fa-cog channel-icon"></i>
                    <span class="channel-name">Settings</span>
                </li>
            </ul>

            <!-- User area at bottom of sidebar -->
            <div class="user-area">
                <div class="user-avatar" style="position: relative;">
                    <i class="fas fa-user"></i>
                    <span class="status-indicator status-online"></span>
                </div>
                <div class="user-details">
                    <div class="user-name">{{ Auth::user()->name ?? 'Provider' }}</div>
                    <div class="user-status">Online</div>
                </div>
                <div class="user-controls">
                    <i class="fas fa-sign-out-alt user-control-icon" title="Logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"></i>
                </div>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Custom JavaScript -->
    <script>
        $(document).ready(function() {
            // Mobile menu toggle
            $('.server-icon').click(function() {
                if ($(window).width() <= 768) {
                    $('.channel-sidebar').toggleClass('show');
                }
            });

            // Close mobile menu when clicking outside
            $(document).click(function(event) {
                if ($(window).width() <= 768) {
                    if (!$(event.target).closest('.channel-sidebar, .server-icon').length) {
                        $('.channel-sidebar').removeClass('show');
                    }
                }
            });
        });
    </script>

    @yield('scripts')
</body>
</html>
