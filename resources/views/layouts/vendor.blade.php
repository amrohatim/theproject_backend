<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Vendor Dashboard') - Dala3Chic Admin</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts - For modern dashboard -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

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

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
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
                <h1>Dala3Chic</h1>
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
                    Dashboard
                </a>
                <a href="{{ route('vendor.company.index') }}" class="{{ request()->routeIs('vendor.company.*') ? 'active' : '' }}">
                    <i class="fas fa-building"></i>
                    Company
                </a>
                <a href="{{ route('vendor.branches.index') }}" class="{{ request()->routeIs('vendor.branches.*') ? 'active' : '' }}">
                    <i class="fas fa-map-marker-alt"></i>
                    Branches
                </a>
                <a href="{{ route('vendor.products.index') }}" class="{{ request()->routeIs('vendor.products.*') ? 'active' : '' }}">
                    <i class="fas fa-box"></i>
                    Products
                </a>
                <a href="{{ route('vendor.services.index') }}" class="{{ request()->routeIs('vendor.services.*') ? 'active' : '' }}">
                    <i class="fas fa-concierge-bell"></i>
                    Services
                </a>
                <a href="{{ route('vendor.deals.index') }}" class="{{ request()->routeIs('vendor.deals.*') ? 'active' : '' }}">
                    <i class="fas fa-percent"></i>
                    Deals
                </a>
                <a href="{{ route('vendor.orders.index') }}" class="{{ request()->routeIs('vendor.orders.index') ? 'active' : '' }}">
                    <i class="fas fa-shopping-cart"></i>
                    All Orders
                </a>
                <a href="{{ route('vendor.orders.pending') }}" class="{{ request()->routeIs('vendor.orders.pending') ? 'active' : '' }}">
                    <i class="fas fa-clock"></i>
                    Pending Orders
                </a>
                <a href="{{ route('vendor.bookings.index') }}" class="{{ request()->routeIs('vendor.bookings.*') ? 'active' : '' }}">
                    <i class="fas fa-calendar-alt"></i>
                    Bookings
                </a>
                <a href="{{ route('vendor.license.index') }}" class="{{ request()->routeIs('vendor.license.*') ? 'active' : '' }}">
                    <i class="fas fa-certificate"></i>
                    License Management
                </a>
                <a href="{{ route('vendor.settings') }}" class="{{ request()->routeIs('vendor.settings*') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i>
                    Settings
                </a>
            </nav>

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
                <h1>@yield('header', 'Vendor Dashboard')</h1>
                <div class="ms-auto">
                    <div class="dropdown">
                        <button class="btn btn-link dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            {{ Auth::user()->name }}
                            <i class="fas fa-chevron-down ms-1"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('vendor.settings.profile') }}">Profile</a></li>
                            <li><a class="dropdown-item" href="{{ route('vendor.settings') }}">Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Logout</button>
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
    </script>

    @yield('scripts')
</body>
</html>
