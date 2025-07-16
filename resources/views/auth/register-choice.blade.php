<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Choose Registration Type - {{ config('app.name') }}</title>

    <!-- Preconnect to external domains -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet"/>

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom Styles -->
    @vite(['resources/css/app.css', 'resources/css/animations.css', 'resources/css/modern-landing.css'])

    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        /* Modern background image card styles */
        .modern-card {
            position: relative;
            border-radius: 1.5rem;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            min-height: 500px;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .modern-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 35px 80px -12px rgba(0, 0, 0, 0.35);
        }

        /* Background images for each card type */
        .vendor-card {
            background-image: url('{{ asset("assets/vendor.jpg") }}');
        }

        .provider-card {
            background-image: url('{{ asset("assets/provider.jpg") }}');
        }

        .merchant-card {
            background-image: url('{{ asset("assets/merchant.jpg") }}');
        }

        /* Semi-transparent overlay for text readability */
        .card-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.4) 0%, rgba(0, 0, 0, 0.2) 50%, rgba(0, 0, 0, 0.6) 100%);
            transition: all 0.3s ease;
            border-radius: 1.5rem;
        }

        .modern-card:hover .card-overlay {
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.5) 0%, rgba(0, 0, 0, 0.3) 50%, rgba(0, 0, 0, 0.7) 100%);
        }

        .card-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 50%;
            width: 3.5rem;
            height: 3.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
            z-index: 10;
        }

        .modern-card:hover .card-badge {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.25);
        }

        .card-content-modern {
            position: relative;
            z-index: 5;
            padding: 2rem;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            background: transparent;
        }

        .card-title-modern {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #ffffff;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
        }

        .card-subtitle-modern {
            color: #f1f5f9;
            font-size: 0.95rem;
            margin-bottom: 1.5rem;
            line-height: 1.5;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.7);
        }

        .feature-list-modern {
            list-style: none;
            padding: 0;
            margin: 0 0 2rem 0;
        }

        .feature-item-modern {
            display: flex;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .feature-item-modern:last-child {
            border-bottom: none;
        }

        .feature-item-modern:hover {
            transform: translateX(4px);
            background: rgba(255, 255, 255, 0.1);
            border-radius: 0.5rem;
            padding-left: 0.5rem;
            margin: 0 -0.5rem;
        }

        .feature-icon-modern {
            width: 1.25rem;
            height: 1.25rem;
            margin-right: 0.75rem;
            transition: all 0.3s ease;
            color: #ffffff;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.7);
        }

        .feature-item-modern:hover .feature-icon-modern {
            transform: scale(1.2);
        }

        .feature-text-modern {
            font-weight: 500;
            color: #f1f5f9;
            font-size: 0.9rem;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.7);
        }

        .modern-button {
            width: 100%;
            padding: 1rem 2rem;
            border: none;
            border-radius: 1rem;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .modern-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.6s ease;
        }

        .modern-button:hover::before {
            left: 100%;
        }

        .vendor-button {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.4);
        }

        .vendor-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(59, 130, 246, 0.6);
        }

        .provider-button {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            color: white;
            box-shadow: 0 10px 25px rgba(139, 92, 246, 0.4);
        }

        .provider-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(139, 92, 246, 0.6);
        }

        .merchant-button {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            box-shadow: 0 10px 25px rgba(245, 158, 11, 0.4);
        }

        .merchant-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(245, 158, 11, 0.6);
        }

        /* Color-coded icons for each card type */
        .vendor-icon {
            color: #3b82f6;
        }

        .provider-icon {
            color: #8b5cf6;
        }

        .merchant-icon {
            color: #f59e0b;
        }

        /* Responsive design improvements */
        @media (max-width: 768px) {
            .modern-card {
                margin-bottom: 2rem;
                min-height: 450px;
            }

            .card-content-modern {
                padding: 1.5rem;
            }

            .card-title-modern {
                font-size: 1.25rem;
            }

            .card-badge {
                width: 3rem;
                height: 3rem;
                top: 0.75rem;
                right: 0.75rem;
            }
        }

        @media (max-width: 640px) {
            .modern-card {
                min-height: 400px;
            }

            .card-content-modern {
                padding: 1.25rem;
            }

            .modern-button {
                padding: 0.875rem 1.5rem;
                font-size: 0.9rem;
            }
        }

        /* Accessibility improvements */
        .modern-card:focus-within {
            outline: 3px solid #3b82f6;
            outline-offset: 2px;
        }

        .modern-button:focus {
            outline: 3px solid rgba(59, 130, 246, 0.5);
            outline-offset: 2px;
        }

        /* Animation for page load */
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modern-card {
            animation: slideInUp 0.6s ease-out;
        }

        .modern-card:nth-child(1) {
            animation-delay: 0.1s;
        }

        .modern-card:nth-child(2) {
            animation-delay: 0.2s;
        }

        .modern-card:nth-child(3) {
            animation-delay: 0.3s;
        }

        /* Navigation styles for light theme */
        .btn-secondary-light {
            background: rgba(59, 130, 246, 0.1);
            backdrop-filter: blur(10px);
            color: #3b82f6;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: 1px solid rgba(59, 130, 246, 0.2);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }

        .btn-secondary-light:hover {
            background: rgba(59, 130, 246, 0.2);
            transform: translateY(-2px);
            color: #2563eb;
            text-decoration: none;
            box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.25);
        }

        /* Mobile menu styles */
        .mobile-menu {
            display: none;
        }

        .mobile-menu.active {
            display: block;
        }

        .mobile-menu-toggle.active i {
            transform: rotate(90deg);
        }

        body.menu-open {
            overflow: hidden;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans" background="{{ asset('assets/background.jpg') }}">
    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white/1 backdrop-blur-md border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <div class="w-10 h-7 bg-transparent flex items-center justify-center">
                        <img src="{{ asset('assets/logo.png') }}" alt="Dala3Chic Logo" class="w-20 h-10 object-cover rounded-lg">
                    </div>
                    <span class="ml-3 text-gray-800 font-bold text-xl">Dala3Chic</span>
                </div>

                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ url('/') }}#features" class="text-gray-600 hover:text-gray-800 transition-colors">Features</a>
                    <a href="{{ url('/') }}#about" class="text-gray-600 hover:text-gray-800 transition-colors">About</a>
                    <a href="{{ url('/') }}#contact" class="text-gray-600 hover:text-gray-800 transition-colors">Contact</a>
                    @auth
                        @if(auth()->user()->role === 'vendor')
                            <a href="{{ route('vendor.dashboard') }}" class="btn-secondary-light">Dashboard</a>
                        @elseif(auth()->user()->role === 'provider')
                            <a href="{{ route('provider.dashboard') }}" class="btn-secondary-light">Dashboard</a>
                        @elseif(auth()->user()->role === 'merchant')
                            <a href="{{ route('merchant.dashboard') }}" class="btn-secondary-light">Dashboard</a>
                        @elseif(auth()->user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="btn-secondary-light">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="btn-secondary-light">Login</a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn-secondary-light">Login</a>
                    @endauth
                </div>

                <button class="md:hidden text-gray-600 mobile-menu-toggle">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div class="mobile-menu md:hidden bg-white/95 backdrop-blur-md border-t border-gray-200">
            <div class="px-4 py-4 space-y-3">
                <a href="{{ url('/') }}#features" class="block text-gray-600 hover:text-gray-800 transition-colors">Features</a>
                <a href="{{ url('/') }}#about" class="block text-gray-600 hover:text-gray-800 transition-colors">About</a>
                <a href="{{ url('/') }}#contact" class="block text-gray-600 hover:text-gray-800 transition-colors">Contact</a>
                @auth
                    @if(auth()->user()->role === 'vendor')
                        <a href="{{ route('vendor.dashboard') }}" class="block btn-secondary-light text-center">Dashboard</a>
                    @elseif(auth()->user()->role === 'provider')
                        <a href="{{ route('provider.dashboard') }}" class="block btn-secondary-light text-center">Dashboard</a>
                    @elseif(auth()->user()->role === 'merchant')
                        <a href="{{ route('merchant.dashboard') }}" class="block btn-secondary-light text-center">Dashboard</a>
                    @elseif(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="block btn-secondary-light text-center">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="block btn-secondary-light text-center">Login</a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="block btn-secondary-light text-center">Login</a>
                @endauth
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8 pt-24">
        <header class="text-left mb-12">
            <h1 class="text-4xl font-bold text-gray-800 mb-4">Choose Registration Type</h1>
            <p class="text-lg font-semibold text-gray-900 max-w-3xl text-left">Whether you're selling products or supplying to vendors, we have the perfect solution for you.</p>
        </header>

        <main class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Vendor Registration Card -->
            <div class="modern-card vendor-card cursor-pointer" onclick="selectChoice('vendor')" data-testid="vendor-registration-link">
                <!-- Semi-transparent overlay -->
                <div class="card-overlay"></div>

                <!-- Badge -->
                <div class="card-badge">
                    <span class="material-icons text-2xl vendor-icon">store</span>
                </div>

                <!-- Content Section with text overlay -->
                <div class="card-content-modern">
                    <h2 class="card-title-modern">Vendor Registration</h2>
                    <p class="card-subtitle-modern">Perfect for businesses selling physical products and managing inventory.</p>

                    <ul class="feature-list-modern">
                        <li class="feature-item-modern">
                            <span class="material-icons feature-icon-modern">check_circle</span>
                            <span class="feature-text-modern">Product catalog management</span>
                        </li>
                        <li class="feature-item-modern">
                            <span class="material-icons feature-icon-modern">check_circle</span>
                            <span class="feature-text-modern">Inventory tracking & analytics</span>
                        </li>
                        <li class="feature-item-modern">
                            <span class="material-icons feature-icon-modern">check_circle</span>
                            <span class="feature-text-modern">Order management system</span>
                        </li>
                        <li class="feature-item-modern">
                            <span class="material-icons feature-icon-modern">check_circle</span>
                            <span class="feature-text-modern">Multi-channel delivery options</span>
                        </li>
                    </ul>

                    <button class="modern-button vendor-button">
                        <span>Register as Vendor</span>
                    </button>
                </div>
            </div>

            <!-- Provider Registration Card -->
            <div class="modern-card provider-card cursor-pointer" onclick="selectChoice('provider')" data-testid="provider-registration-link">
                <!-- Semi-transparent overlay -->
                <div class="card-overlay"></div>

                <!-- Badge -->
                <div class="card-badge">
                    <span class="material-icons text-2xl provider-icon">local_shipping</span>
                </div>

                <!-- Content Section with text overlay -->
                <div class="card-content-modern">
                    <h2 class="card-title-modern">Provider Registration</h2>
                    <p class="card-subtitle-modern">Perfect for suppliers providing wholesale products to vendors.</p>

                    <ul class="feature-list-modern">
                        <li class="feature-item-modern">
                            <span class="material-icons feature-icon-modern">check_circle</span>
                            <span class="feature-text-modern">Wholesale product catalog</span>
                        </li>
                        <li class="feature-item-modern">
                            <span class="material-icons feature-icon-modern">check_circle</span>
                            <span class="feature-text-modern">Bulk order management</span>
                        </li>
                        <li class="feature-item-modern">
                            <span class="material-icons feature-icon-modern">check_circle</span>
                            <span class="feature-text-modern">Vendor relationship management</span>
                        </li>
                        <li class="feature-item-modern">
                            <span class="material-icons feature-icon-modern">check_circle</span>
                            <span class="feature-text-modern">Supply chain tracking</span>
                        </li>
                    </ul>

                    <button class="modern-button provider-button">
                        <span>Register as Provider</span>
                    </button>
                </div>
            </div>

            <!-- Merchant Registration Card -->
            <div class="modern-card merchant-card cursor-pointer" onclick="selectChoice('merchant')" data-testid="merchant-registration-link">
                <!-- Semi-transparent overlay -->
                <div class="card-overlay"></div>

                <!-- Badge -->
                <div class="card-badge">
                    <span class="material-icons text-2xl merchant-icon">person</span>
                </div>

                <!-- Content Section with text overlay -->
                <div class="card-content-modern">
                    <h2 class="card-title-modern">Merchant Registration</h2>
                    <p class="card-subtitle-modern">Perfect for individual women merchants and small business owners.</p>

                    <ul class="feature-list-modern">
                        <li class="feature-item-modern">
                            <span class="material-icons feature-icon-modern">check_circle</span>
                            <span class="feature-text-modern">Individual business setup</span>
                        </li>
                        <li class="feature-item-modern">
                            <span class="material-icons feature-icon-modern">check_circle</span>
                            <span class="feature-text-modern">Direct customer sales</span>
                        </li>
                        <li class="feature-item-modern">
                            <span class="material-icons feature-icon-modern">check_circle</span>
                            <span class="feature-text-modern">Flexible delivery options</span>
                        </li>
                        <li class="feature-item-modern">
                            <span class="material-icons feature-icon-modern">check_circle</span>
                            <span class="feature-text-modern">Small store management</span>
                        </li>
                    </ul>

                    <button class="modern-button merchant-button">
                        <span>Register as Merchant</span>
                    </button>
                </div>
            </div>
        </main>

        <footer class="text-center mt-12">
           
        </footer>
    </div>

    <script>
        function selectChoice(type) {
            // Redirect after a short delay for visual feedback
            setTimeout(() => {
                if (type === 'vendor') {
                    window.location.href = '{{ route("register.vendor") }}';
                } else if (type === 'provider') {
                    window.location.href = '{{ route("register.provider") }}';
                } else if (type === 'merchant') {
                    window.location.href = '{{ route("register.merchant") }}';
                }
            }, 300);
        }

        // Mobile Menu functionality
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
            const mobileMenu = document.querySelector('.mobile-menu');

            if (mobileMenuToggle && mobileMenu) {
                mobileMenuToggle.addEventListener('click', function() {
                    mobileMenu.classList.toggle('active');
                    this.classList.toggle('active');

                    // Prevent body scroll when menu is open
                    document.body.classList.toggle('menu-open');
                });

                // Close menu when clicking outside
                document.addEventListener('click', function(e) {
                    if (!mobileMenu.contains(e.target) && !mobileMenuToggle.contains(e.target)) {
                        mobileMenu.classList.remove('active');
                        mobileMenuToggle.classList.remove('active');
                        document.body.classList.remove('menu-open');
                    }
                });
            }
        });
    </script>
</body>
</html>