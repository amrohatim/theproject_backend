<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('messages.choose_registration_type') }} - {{ config('app.name') }}</title>

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

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom Styles -->
    @vite(['resources/css/app.css', 'resources/css/animations.css', 'resources/css/modern-landing.css'])
       <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">

    <style>
        :root {
            --register-bg-blue: #14d0f0e6;
            --register-bg-pink: #a46bc177;
            --register-bg-white: rgba(0, 0, 0, 0.57);
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            position: relative;
            min-height: 100vh;
            background: transparent;
        }

        .background {
            /* background-image: url('https://images.unsplash.com/photo-1554692760-4b7e52978fb6?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1650&q=80'); */
            background-image:url('assets/registerBG.webp');
            background-size: cover;
            background-repeat: no-repeat;
            background-color: #252746;
            background-position: center;
            width: 105vw;
            height: 105vh;
            position: fixed;
            top: 0px;
            left: 0;
            z-index: 0;
            pointer-events: none;
        }

        .background-texture {
            background: linear-gradient(
                to top,
                var(--register-bg-pink),
                var(--register-bg-white)
            );
            width: 100vw;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 0;
            pointer-events: none;
        }

        .register-choice-content {
            position: relative;
            z-index: 1;
        }

        /* Pricing-style card styles */
        .modern-card {
            position: relative;
            border-radius: 1.5rem;
            overflow: hidden;
            box-shadow: 0 20px 40px -18px rgba(15, 23, 42, 0.18);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid #e5e7eb;
            min-height: 560px;
            background: #ffffff;
        }

        .modern-card:hover,
        .modern-card:focus-within {
            transform: translateY(-8px);
            border-color: var(--primary);
            background: var(--primary);
            box-shadow: 0 35px 70px -20px var(--primary);
        }

        /* Keep overlay layer so text stays visually lifted over each card */
        .card-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(165deg, rgba(248, 250, 252, 0.85) 0%, rgba(241, 245, 249, 0.4) 55%, rgba(226, 232, 240, 0.85) 100%);
            transition: all 0.3s ease;
            border-radius: 1.5rem;
            opacity: 0.85;
            z-index: 1;
        }

        .modern-card:hover .card-overlay,
        .modern-card:focus-within .card-overlay {
            background: linear-gradient(140deg, rgba(255, 255, 255, 0.2) 0%, rgba(217, 101, 122, 0.18) 45%, rgba(30, 37, 54, 0.24) 100%);
            opacity: 1;
        }

        .card-badge {
            position: absolute;
            top:  1rem;
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

        .card-badge-ar {
            position: absolute;
            top:  1rem;
            left:  1rem;
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

        .modern-card:hover .card-badge,
        .modern-card:focus-within .card-badge,
        .modern-card:hover .card-badge-ar,
        .modern-card:focus-within .card-badge-ar {
            transform: scale(1.1) rotate(5deg);
            background: rgba(255, 255, 255, 0.25);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.2);
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
            color: #1e2536;
            transition: color 0.3s ease;
        }

        .card-subtitle-modern {
            color: #64748b;
            font-size: 0.95rem;
            margin-bottom: 1.5rem;
            line-height: 1.5;
            transition: color 0.3s ease;
        }

        .card-price-modern {
            margin-bottom: 1.5rem;
            position: relative;
            z-index: 5;
        }

        .card-price-value {
            font-size: 2rem;
            font-weight: 700;
            color: #1e2536;
            line-height: 1;
            transition: color 0.3s ease;
        }

        .card-price-note {
            color: #64748b;
            font-size: 0.85rem;
            margin-top: 0.35rem;
            transition: color 0.3s ease;
        }

        .feature-list-modern {
            list-style: none;
            padding: 0;
            margin: 0 0 2rem 0;
            position: relative;
            z-index: 5;
        }

        .feature-item-modern {
            display: flex;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .feature-item-modern:last-child {
            border-bottom: none;
        }

        .feature-item-modern:hover {
            transform: translateX(4px);
            background: rgba(148, 163, 184, 0.15);
            border-radius: 0.5rem;
            padding-left: 0.5rem;
            margin: 0 -0.5rem;
        }

        .feature-icon-modern {
            width: 1.25rem;
            height: 1.25rem;
            margin-right: 0.75rem;
            transition: all 0.3s ease;
            color: var(--primary);
        }

        .feature-item-modern:hover .feature-icon-modern {
            transform: scale(1.2);
        }

        .feature-text-modern {
            font-weight: 500;
            color: #1e2536;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }

        .modern-button {
            width: 100%;
            padding: 1rem 2rem;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #1e2536;
            background: #ffffff;
            box-shadow: 0 8px 22px -14px rgba(15, 23, 42, 0.35);
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

        .modern-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 28px -12px rgba(15, 23, 42, 0.45);
        }

        /* Color-coded icons for each card type */
        .vendor-icon {
            color: var(--primary);
        }

        .provider-icon {
            color:  var(--primary);
        }

        .merchant-icon {
            color:  var(--primary);
        }

        .modern-card:hover .card-title-modern,
        .modern-card:focus-within .card-title-modern,
        .modern-card:hover .card-subtitle-modern,
        .modern-card:focus-within .card-subtitle-modern,
        .modern-card:hover .card-price-value,
        .modern-card:focus-within .card-price-value,
        .modern-card:hover .card-price-note,
        .modern-card:focus-within .card-price-note,
        .modern-card:hover .feature-text-modern,
        .modern-card:focus-within .feature-text-modern {
            color: #ffffff;
        }

        .modern-card:hover .feature-item-modern,
        .modern-card:focus-within .feature-item-modern {
            border-bottom-color: rgba(255, 255, 255, 0.28);
        }

        .modern-card:hover .feature-item-modern:hover,
        .modern-card:focus-within .feature-item-modern:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        .modern-card:hover .feature-icon-modern,
        .modern-card:focus-within .feature-icon-modern,
        .modern-card:hover .vendor-icon,
        .modern-card:focus-within .vendor-icon,
        .modern-card:hover .provider-icon,
        .modern-card:focus-within .provider-icon,
        .modern-card:hover .merchant-icon,
        .modern-card:focus-within .merchant-icon {
            color: #ffffff;
        }

        .modern-card:hover .modern-button,
        .modern-card:focus-within .modern-button {
            background: #ffffff;
            color: var(--primary);
            border-color: #ffffff;
            box-shadow: 0 16px 30px -14px rgba(30, 37, 54, 0.45);
        }

        /* Responsive design improvements */
        @media (max-width: 768px) {
            .modern-card {
                margin-bottom: 2rem;
                min-height: 500px;
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
                min-height: 460px;
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
            outline: 3px solid var(--primary);
            outline-offset: 2px;
        }

        .modern-button:focus {
            outline: 3px solid var(--primary);
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
            background: white;
            backdrop-filter: blur(10px);
            color: var(--primary);
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: 1px solid var(--primary);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }

        .btn-secondary-light:hover {
            background: var(--primary-light)/50;
            transform: translateY(-2px);
            color: var(--primary);
            text-decoration: none;
            box-shadow: 0 1px 2px -2px var(--primary-light);
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
<body class="font-sans">
    <div class="background" aria-hidden="true"></div>
    <div class="background-texture" aria-hidden="true"></div>

    <div class="register-choice-content">
    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-transparent">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <div class="w-10 h-7 bg-transparent flex items-center justify-center">
                        <img src="{{ asset('assets/logo.png') }}" alt="glowlabs Logo" class="w-16 h-16 object-cover rounded-lg">
                    </div>
                    <span class="ml-3 text-white/80 font-bold text-xl">glowlabs</span>
                </div>

                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ url('/') }}#features" class="text-white font-bold">{{ __('messages.services') }}</a>
                    <a href="{{ url('/') }}#about" class="text-white font-bold">{{ __('messages.about') }}</a>
                    <a href="{{ url('/') }}#contact" class="text-white font-bold">{{ __('messages.contact') }}</a>

                    <!-- Language Switcher -->
                    <div class="language-switcher flex items-center space-x-2">
                        <button onclick="switchLanguage('en')" class="lang-btn text-gray-600 hover:text-gray-800 transition-colors px-2 py-1 rounded {{ app()->getLocale() === 'en' ? 'bg-gray-200 font-semibold' : 'text-white' }}" data-lang="en">
                            EN
                        </button>
                        <span class="text-gray-400">|</span>
                        <button onclick="switchLanguage('ar')" class="lang-btn text-gray-600 hover:text-gray-800 transition-colors px-2 py-1 rounded {{ app()->getLocale() === 'ar' ? 'bg-gray-200 font-semibold' : 'text-white' }}" data-lang="ar">
                            AR
                        </button>
                    </div>

                    @auth
                        @if(auth()->user()->role === 'vendor')
                            <a href="{{ route('vendor.dashboard') }}" class="btn-secondary-light">{{ __('messages.dashboard') }}</a>
                        @elseif(auth()->user()->role === 'provider')
                            <a href="{{ route('provider.dashboard') }}" class="btn-secondary-light">{{ __('messages.dashboard') }}</a>
                        @elseif(auth()->user()->role === 'merchant')
                            <a href="{{ route('merchant.dashboard') }}" class="btn-secondary-light">{{ __('messages.dashboard') }}</a>
                        @elseif(auth()->user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="btn-secondary-light">{{ __('messages.dashboard') }}</a>
                        @else
                            <a href="{{ route('login') }}" class="btn-secondary-light">{{ __('messages.login') }}</a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn-secondary-light">{{ __('messages.login') }}</a>
                    @endauth
                </div>

                <button class="md:hidden text-gray-600 mobile-menu-toggle">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div class="mobile-menu md:hidden">
            <div class="px-4 py-4 space-y-3">
                <a href="{{ url('/') }}#features" class="block text-white font-bold">{{ __('messages.services') }}</a>
                <a href="{{ url('/') }}#about" class="block text-white font-bold">{{ __('messages.about') }}</a>
                <a href="{{ url('/') }}#contact" class="block text-white font-bold">{{ __('messages.contact') }}</a>

                <!-- Mobile Language Switcher -->
                <div class="language-switcher flex items-center justify-center gap-2 space-x-4 py-2">
                    <button onclick="switchLanguage('en')" class="lang-btn text-gray-600 hover:text-gray-800 transition-colors px-3 py-1 rounded border border-gray-300 {{ app()->getLocale() === 'en' ? 'bg-gray-200 font-semibold' : 'text-white' }}" data-lang="en">
                        {{ __('messages.english') }}
                    </button>
                    <button onclick="switchLanguage('ar')" class="lang-btn text-gray-600 hover:text-gray-800 transition-colors px-3 py-1 rounded border border-gray-300 {{ app()->getLocale() === 'ar' ? 'bg-gray-200 font-semibold' : 'text-white' }}" data-lang="ar">
                        {{ __('messages.arabic') }}
                    </button>
                </div>

                @auth
                    @if(auth()->user()->role === 'vendor')
                        <a href="{{ route('vendor.dashboard') }}" class="block btn-secondary-light text-center">{{ __('messages.dashboard') }}</a>
                    @elseif(auth()->user()->role === 'provider')
                        <a href="{{ route('provider.dashboard') }}" class="block btn-secondary-light text-center">{{ __('messages.dashboard') }}</a>
                    @elseif(auth()->user()->role === 'merchant')
                        <a href="{{ route('merchant.dashboard') }}" class="block btn-secondary-light text-center">{{ __('messages.dashboard') }}</a>
                    @elseif(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="block btn-secondary-light text-center">{{ __('messages.dashboard') }}</a>
                    @else
                        <a href="{{ route('login') }}" class="block btn-secondary-light text-center">{{ __('messages.login') }}</a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="block btn-secondary-light text-center">{{ __('messages.login') }}</a>
                @endauth
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8 pt-24">
        <header class="mb-12 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">
            <h1 class="text-4xl font-bold text-white/70 mb-4">{{ __('messages.choose_registration_type') }}</h1>
            <p class="text-lg font-semibold text-white/80 max-w-3xl {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('messages.registration_type_desc') }}</p>
        </header>

        <main class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Vendor Registration Card -->
            <div class="modern-card cursor-pointer" onclick="selectChoice('vendor')" data-testid="vendor-registration-link" role="button" tabindex="0" onkeydown="if(event.key === 'Enter' || event.key === ' '){ event.preventDefault(); selectChoice('vendor'); }">
                <!-- Semi-transparent overlay -->
                <div class="card-overlay"></div>

                <!-- Badge -->
                <div class="{{ app()->getLocale() === 'ar' ? 'card-badge-ar' : 'card-badge' }}">
                    <span class="material-icons text-2xl vendor-icon">store</span>
                </div>

                <!-- Content Section with text overlay -->
                <div class="card-content-modern">
                    <h2 class="card-title-modern">{{ __('messages.vendor_registration') }}</h2>
                    <p class="card-subtitle-modern">{{ __('messages.vendor_registration_desc') }}</p>
                    <div class="card-price-modern">
                        <div class="card-price-value">99 AED</div>
                        <div class="card-price-note">{{ __('messages.gogo_bill_monthly') }}</div>
                    </div>

                    <ul class="feature-list-modern">
                        <li class="feature-item-modern">
                            <span class="material-icons feature-icon-modern">check_circle</span>
                            <span class="feature-text-modern">{{ __('messages.product_catalog_management') }}</span>
                        </li>
                        <li class="feature-item-modern">
                            <span class="material-icons feature-icon-modern">check_circle</span>
                            <span class="feature-text-modern">{{ __('messages.inventory_tracking_analytics') }}</span>
                        </li>
                        <li class="feature-item-modern">
                            <span class="material-icons feature-icon-modern">check_circle</span>
                            <span class="feature-text-modern">{{ __('messages.order_management_system') }}</span>
                        </li>
                        <li class="feature-item-modern">
                            <span class="material-icons feature-icon-modern">check_circle</span>
                            <span class="feature-text-modern">{{ __('messages.multi_channel_delivery') }}</span>
                        </li>
                    </ul>

                    <button class="modern-button">
                        <span>{{ __('messages.register_as_vendor') }}</span>
                    </button>
                </div>
            </div>

            <!-- Provider Registration Card -->
            <div class="modern-card cursor-pointer" onclick="selectChoice('provider')" data-testid="provider-registration-link" role="button" tabindex="0" onkeydown="if(event.key === 'Enter' || event.key === ' '){ event.preventDefault(); selectChoice('provider'); }">
                <!-- Semi-transparent overlay -->
                <div class="card-overlay"></div>

                <!-- Badge -->
                <div class="{{ app()->getLocale() === 'ar' ? 'card-badge-ar' : 'card-badge' }}">
                    <span class="material-icons text-2xl provider-icon">local_shipping</span>
                </div>

                <!-- Content Section with text overlay -->
                <div class="card-content-modern">
                    <h2 class="card-title-modern">{{ __('messages.provider_registration') }}</h2>
                    <p class="card-subtitle-modern">{{ __('messages.provider_registration_desc') }}</p>
                    <div class="card-price-modern">
                        <div class="card-price-value">99 AED</div>
                        <div class="card-price-note">{{ __('messages.gogo_bill_monthly') }}</div>
                    </div>

                    <ul class="feature-list-modern">
                        <li class="feature-item-modern">
                            <span class="material-icons feature-icon-modern">check_circle</span>
                            <span class="feature-text-modern">{{ __('messages.wholesale_product_catalog') }}</span>
                        </li>
                        <li class="feature-item-modern">
                            <span class="material-icons feature-icon-modern">check_circle</span>
                            <span class="feature-text-modern">{{ __('messages.bulk_order_management') }}</span>
                        </li>
                        <li class="feature-item-modern">
                            <span class="material-icons feature-icon-modern">check_circle</span>
                            <span class="feature-text-modern">{{ __('messages.vendor_relationship_management') }}</span>
                        </li>
                        <li class="feature-item-modern">
                            <span class="material-icons feature-icon-modern">check_circle</span>
                            <span class="feature-text-modern">{{ __('messages.supply_chain_tracking') }}</span>
                        </li>
                    </ul>

                    <button class="modern-button">
                        <span>{{ __('messages.register_as_provider') }}</span>
                    </button>
                </div>
            </div>

            <!-- Merchant Registration Card -->
            <div class="modern-card cursor-pointer" onclick="selectChoice('merchant')" data-testid="merchant-registration-link" role="button" tabindex="0" onkeydown="if(event.key === 'Enter' || event.key === ' '){ event.preventDefault(); selectChoice('merchant'); }">
                <!-- Semi-transparent overlay -->
                <div class="card-overlay"></div>

                <!-- Badge -->
                <div class="{{ app()->getLocale() === 'ar' ? 'card-badge-ar' : 'card-badge' }}">
                    <span class="material-icons text-2xl merchant-icon">person</span>
                </div>

                <!-- Content Section with text overlay -->
                <div class="card-content-modern">
                    <h2 class="card-title-modern">{{ __('messages.merchant_registration') }}</h2>
                    <p class="card-subtitle-modern">{{ __('messages.merchant_registration_desc') }}</p>
                    <div class="card-price-modern">
                        <div class="card-price-value">99 AED</div>
                        <div class="card-price-note">{{ __('messages.gogo_bill_monthly') }}</div>
                    </div>

                    <ul class="feature-list-modern">
                        <li class="feature-item-modern">
                            <span class="material-icons feature-icon-modern">check_circle</span>
                            <span class="feature-text-modern">{{ __('messages.individual_business_setup') }}</span>
                        </li>
                        <li class="feature-item-modern">
                            <span class="material-icons feature-icon-modern">check_circle</span>
                            <span class="feature-text-modern">{{ __('messages.direct_customer_sales') }}</span>
                        </li>
                        <li class="feature-item-modern">
                            <span class="material-icons feature-icon-modern">check_circle</span>
                            <span class="feature-text-modern">{{ __('messages.flexible_delivery_options') }}</span>
                        </li>
                        <li class="feature-item-modern">
                            <span class="material-icons feature-icon-modern">check_circle</span>
                            <span class="feature-text-modern">{{ __('messages.small_store_management') }}</span>
                        </li>
                    </ul>

                    <button class="modern-button">
                        <span>{{ __('messages.register_as_merchant') }}</span>
                    </button>
                </div>
            </div>
        </main>

        <footer class="text-center mt-12">
           
        </footer>
    </div>
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

        // Language switching functionality
        function switchLanguage(locale) {
            // Show loading state
            const buttons = document.querySelectorAll('.lang-btn');
            buttons.forEach(btn => {
                if (btn.dataset.lang === locale) {
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                    btn.disabled = true;
                }
            });

            // Make AJAX request to switch language
            fetch('{{ route('language.switch.post') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ locale: locale })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Store language preference
                    localStorage.setItem('language', locale);
                    // Reload page to apply new language
                    window.location.reload();
                } else {
                    console.error('Language switch failed:', data.message);
                    // Restore button state
                    buttons.forEach(btn => {
                        if (btn.dataset.lang === locale) {
                            btn.innerHTML = locale.toUpperCase();
                            btn.disabled = false;
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error switching language:', error);
                // Restore button state
                buttons.forEach(btn => {
                    if (btn.dataset.lang === locale) {
                        btn.innerHTML = locale.toUpperCase();
                        btn.disabled = false;
                    }
                });
            });
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
