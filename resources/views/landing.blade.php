@php
    use App\Helpers\LanguageHelper;
    $currentLocale = LanguageHelper::getCurrentLocale();
    $isRtl = LanguageHelper::isRtl();
    $direction = LanguageHelper::getDirection();
@endphp

<!DOCTYPE html>
<html lang="{{ $currentLocale }}" dir="{{ $direction }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Your premier marketplace for products and services from trusted vendors worldwide">
    <meta name="keywords" content="marketplace, e-commerce, products, services, vendors, shopping">
    <meta name="author" content="Dala3Chic">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ __('messages.dala3chic') }} - {{ __('messages.hero_title') }}</title>

    <!-- Preconnect to external domains -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom Styles -->
    @vite(['resources/css/app.css', 'resources/css/animations.css', 'resources/css/modern-landing.css'])
    <link rel="stylesheet" href="{{ asset('css/features-animations.css') }}">

    <!-- RTL CSS for Arabic -->
    @if($isRtl)
        <link href="{{ asset('css/rtl.css') }}" rel="stylesheet">
    @endif

    <!-- Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "Dala3Chic",
        "description": "Your premier marketplace for products and services",
        "url": "{{ url('/') }}"
    }
    </script>

    <!-- Modern Landing Page Styles -->
    <style>
        html {
            font-family: 'Inter', system-ui, sans-serif;
            scroll-behavior: smooth;
        }

        body {
            @apply bg-white text-gray-900;
        }

        .btn-primary {
            @apply bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-lg hover:shadow-xl;
        }

        .btn-secondary {
            @apply border-2 border-blue-600 text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-blue-50 transition-all duration-200;
        }

        .card {
            @apply bg-white rounded-2xl shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Hero Section Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes pulseGlow {
            0%, 100% {
                text-shadow: 0 0 20px rgba(209, 41, 215, 0.8);
            }
            50% {
                text-shadow: 0 0 30px rgba(59, 130, 246, 0.8), 0 0 40px rgba(59, 130, 246, 0.6);
            }
            75% {
                text-shadow: 0 0 40px rgba(246, 218, 59, 0.8), 0 0 50px rgba(246, 202, 59, 0.6);
            }
        }

        @keyframes bounceSubtle {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-5px);
            }
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-20px);
            }
        }

        @keyframes twinkle {
            0%, 100% {
                opacity: 0.3;
                transform: scale(1);
            }
            50% {
                opacity: 1;
                transform: scale(1.5);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
        }

        .animate-slide-in-left {
            animation: slideInLeft 0.8s ease-out forwards;
        }

        .animate-slide-in-right {
            animation: slideInRight 0.8s ease-out forwards;
        }

        .animate-pulse-glow {
            animation: pulseGlow 2s ease-in-out infinite;
        }

        .animate-bounce-subtle {
            animation: bounceSubtle 2s ease-in-out infinite;
        }

        .animate-float {
            animation: float 3s ease-in-out infinite;
        }

        .animate-twinkle {
            animation: twinkle 2s ease-in-out infinite;
        }

        .animation-delay-300 {
            animation-delay: 0.3s;
        }

        .animation-delay-500 {
            animation-delay: 0.5s;
        }

        .animation-delay-700 {
            animation-delay: 0.7s;
        }

        .animation-delay-900 {
            animation-delay: 0.9s;
        }

        .animation-delay-1000 {
            animation-delay: 1s;
        }

        .animation-delay-1100 {
            animation-delay: 1.1s;
        }

        .animation-delay-750 {
            animation-delay: 0.75s;
        }

        .animation-delay-600 {
            animation-delay: 0.6s;
        }

        .shadow-3xl {
            box-shadow: 0 35px 60px -12px rgba(0, 0, 0, 0.25);
        }

        /* Responsive Optimizations */
        @media (max-width: 640px) {
            .animate-fade-in-up,
            .animate-slide-in-left,
            .animate-slide-in-right {
                animation-duration: 0.6s;
            }
            
            .animate-float {
                animation-duration: 2s;
            }
            
            .animate-twinkle {
                animation-duration: 1.5s;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .animate-fade-in-up,
            .animate-slide-in-left,
            .animate-slide-in-right,
            .animate-pulse-glow,
            .animate-bounce-subtle,
            .animate-float,
            .animate-twinkle {
                animation: none;
            }
        }
    </style>
</head>
<body class="min-h-screen bg-white">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <a href="/" class="flex items-center">
                        <div class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text">Dala3Chic</div>
                    </a>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-8">
                        <a href="/" class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors">
                            {{ __('messages.home') }}
                        </a>
                        <a href="#marketplace" class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors">
                            {{ __('messages.marketplace') }}
                        </a>
                        <a href="#vendors" class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors">
                            {{ __('messages.for_vendors') }}
                        </a>
                        <a href="#contact" class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors">
                            {{ __('messages.contact') }}
                        </a>

                        <!-- Language Switcher -->
                        <div class="language-switcher flex items-center space-x-2">
                            <button onclick="switchLanguage('en')" class="lang-btn text-gray-700 hover:text-blue-600 transition-colors px-2 py-1 rounded" data-lang="en">
                                EN
                            </button>
                            <span class="text-gray-400">|</span>
                            <button onclick="switchLanguage('ar')" class="lang-btn text-gray-700 hover:text-blue-600 transition-colors px-2 py-1 rounded" data-lang="ar">
                                AR
                            </button>
                        </div>
                    </div>
                </div>

                <!-- CTA Button -->
                <div class="hidden md:flex items-center space-x-4">
                    @if($isAuthenticated)
                        <a href="{{ $getStartedUrl }}" class="text-gray-700 hover:text-blue-600 px-4 py-2 text-sm font-medium transition-colors">
                            {{ __('messages.dashboard_nav') }}
                        </a>
                    @else
                        <button class="text-gray-700 hover:text-blue-600 px-4 py-2 text-sm font-medium transition-colors">
                            {{ __('messages.sign_in') }}
                        </button>
                        <a href="{{ route('login') }}" class="bg-blue-900 text-white px-6 py-2 rounded-lg text-sm font-medium hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-md hover:shadow-lg">
                            {{ __('messages.join_now') }}
                        </a>
                    @endif
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button class="text-gray-700 hover:text-blue-600 p-2 mobile-menu-toggle">
                        <i class="fas fa-bars h-6 w-6"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation -->
        <div class="mobile-menu md:hidden bg-white border-t border-gray-100" style="display: none;">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="/" class="text-gray-700 hover:text-blue-600 block px-3 py-2 text-base font-medium">
                    {{ __('messages.home') }}
                </a>
                <a href="#marketplace" class="text-gray-700 hover:text-blue-600 block px-3 py-2 text-base font-medium">
                    {{ __('messages.marketplace') }}
                </a>
                <a href="#vendors" class="text-gray-700 hover:text-blue-600 block px-3 py-2 text-base font-medium">
                    {{ __('messages.for_vendors') }}
                </a>
                <a href="#contact" class="text-gray-700 hover:text-blue-600 block px-3 py-2 text-base font-medium">
                    {{ __('messages.contact') }}
                </a>

                <!-- Mobile Language Switcher -->
                <div class="language-switcher flex items-center justify-center space-x-4 py-2">
                    <button onclick="switchLanguage('en')" class="lang-btn text-gray-700 hover:text-blue-600 transition-colors px-3 py-1 rounded border border-gray-300" data-lang="en">
                        {{ __('messages.english') }}
                    </button>
                    <button onclick="switchLanguage('ar')" class="lang-btn text-gray-700 hover:text-blue-600 transition-colors px-3 py-1 rounded border border-gray-300" data-lang="ar">
                        {{ __('messages.arabic') }}
                    </button>
                </div>

                <div class="px-3 py-2 space-y-2">
                    @if($isAuthenticated)
                        <a href="{{ $getStartedUrl }}" class="w-full text-gray-700 hover:text-blue-600 px-4 py-2 text-sm font-medium transition-colors border border-gray-300 rounded-lg block text-center">
                            {{ __('messages.dashboard_nav') }}
                        </a>
                    @else
                        <button class="w-full text-gray-700 hover:text-blue-600 px-4 py-2 text-sm font-medium transition-colors border border-gray-300 rounded-lg">
                            {{ __('messages.sign_in') }}
                        </button>
                        <a href="{{ route('login') }}" class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-2 rounded-lg text-sm font-medium hover:from-blue-700 hover:to-blue-800 transition-all duration-200 block text-center">
                            {{ __('messages.join_now') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-violet-500 via-purple-500 to-pink-500 py-20 lg:py-32 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <!-- Main Headline -->
                <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold text-white mb-6 animate-fade-in-up">
                    <span class="block animate-slide-in-left">{{ __('messages.your_premier') }}</span>
                    <span class="block  bg-clip-text  animate-pulse-glow">Dala3Chic</span>
                    <span class="block animate-slide-in-right">{{ __('messages.experience') }}</span>
                </h1>

                <!-- Subtitle -->
                <p class="text-xl md:text-2xl text-white/90 mb-8 max-w-3xl mx-auto animate-fade-in-up animation-delay-300">
                    {{ __('messages.empowering_women_commerce') }}
                </p>

                <!-- Description -->
                <p class="text-lg text-white/80 mb-12 max-w-4xl mx-auto leading-relaxed animate-fade-in-up animation-delay-500">
                    {{ __('messages.hero_subtitle') }}
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-16 animate-fade-in-up animation-delay-700">
                    <button onclick="window.location.href='{{ $getStartedUrl }}'" class="bg-gradient-to-r from-yellow-400 to-orange-500 text-white px-8 py-4 rounded-xl text-lg font-semibold hover:from-yellow-500 hover:to-orange-600 transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:scale-105 hover:-translate-y-1 flex items-center gap-2 animate-bounce-subtle">
                        @if($isAuthenticated)
                            @switch($userRole)
                                @case('admin')
                                    {{ __('messages.go_to_admin_dashboard') }}
                                    @break
                                @case('vendor')
                                    {{ __('messages.go_to_vendor_dashboard') }}
                                    @break
                                @case('provider')
                                    {{ __('messages.go_to_provider_dashboard') }}
                                    @break
                                @default
                                    {{ __('messages.continue_shopping') }}
                            @endswitch
                        @else
                            {{ __('messages.start_shopping') }}
                        @endif
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                            <path d="M5 12h14"></path>
                            <path d="m12 5 7 7-7 7"></path>
                        </svg>
                    </button>
                    <button onclick="document.getElementById('features').scrollIntoView({behavior: 'smooth'})" class="border-2 border-white text-white px-8 py-4 rounded-xl text-lg font-semibold hover:bg-white/10 backdrop-blur-sm transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                            <polygon points="6 3 20 12 6 21 6 3"></polygon>
                        </svg>
                        {{ __('messages.learn_more') }}
                    </button>
                </div>

                <!-- Feature Tags -->
                <div class="flex flex-wrap justify-center gap-3 mb-12 animate-fade-in-up animation-delay-900">
                    <span class="bg-white/20 backdrop-blur-sm text-white px-4 py-2 rounded-full text-sm font-medium border border-white/30 hover:bg-white/30 transition-all duration-300 transform hover:scale-105">
                        {{ __('messages.vendors_merchants') }}
                    </span>
                    <span class="bg-white/20 backdrop-blur-sm text-white px-4 py-2 rounded-full text-sm font-medium border border-white/30 hover:bg-white/30 transition-all duration-300 transform hover:scale-105">
                        {{ __('messages.wholesale_providers') }}
                    </span>
                    <span class="bg-white/20 backdrop-blur-sm text-white px-4 py-2 rounded-full text-sm font-medium border border-white/30 hover:bg-white/30 transition-all duration-300 transform hover:scale-105">
                        {{ __('messages.secure_transactions') }}
                    </span>
                    <span class="bg-white/20 backdrop-blur-sm text-white px-4 py-2 rounded-full text-sm font-medium border border-white/30 hover:bg-white/30 transition-all duration-300 transform hover:scale-105">
                        {{ __('messages.global_marketplace') }}
                    </span>
                </div>

                <!-- Hero Image -->
                <div class="relative max-w-5xl mx-auto animate-fade-in-up animation-delay-1100">
                    <div class="bg-white/10 backdrop-blur-lg rounded-3xl shadow-2xl p-8 transform rotate-2 hover:rotate-0 transition-all duration-500 border border-white/20 hover:shadow-3xl">
                        <div class="bg-gradient-to-br from-white/20 to-white/10 rounded-2xl overflow-hidden backdrop-blur-sm">
                            <img src="{{ asset('assets/main2.avif') }}" alt="Dala3Chic Marketplace Illustration" class="w-full h-auto object-contain transform hover:scale-105 transition-transform duration-500" />
                        </div>
                    </div>
                    <!-- Floating elements -->
                    <div class="absolute -top-4 -left-4 bg-white/20 backdrop-blur-lg rounded-xl shadow-xl p-3 hidden lg:block border border-white/30 animate-float">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                            <span class="text-sm font-medium text-white">{{ __('messages.live_store') }}</span>
                        </div>
                    </div>
                    <div class="absolute -bottom-4 -right-4 bg-white/20 backdrop-blur-lg rounded-xl shadow-xl p-3 hidden lg:block border border-white/30 animate-float animation-delay-1000">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-pink-400 rounded-full animate-pulse"></div>
                            <span class="text-sm font-medium text-white">{{ __('messages.shopping') }}...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Background decorations -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
            <!-- Geometric shapes -->
            <div class="absolute top-20 left-10 w-24 h-24 bg-gradient-to-br from-yellow-300 to-orange-400 rounded-full opacity-30 animate-float"></div>
            <div class="absolute bottom-20 right-10 w-32 h-32 bg-gradient-to-br from-pink-300 to-purple-400 rounded-full opacity-30 animate-float animation-delay-1000"></div>
            <div class="absolute top-1/2 left-1/4 w-20 h-20 bg-gradient-to-br from-blue-300 to-indigo-400 rounded-full opacity-30 animate-float animation-delay-500"></div>
            <div class="absolute top-1/3 right-1/4 w-16 h-16 bg-gradient-to-br from-green-300 to-teal-400 rounded-full opacity-30 animate-float animation-delay-750"></div>
            <!-- Sparkle effects -->
            <div class="absolute top-1/4 left-1/3 w-2 h-2 bg-white rounded-full animate-twinkle"></div>
            <div class="absolute bottom-1/3 right-1/3 w-3 h-3 bg-yellow-300 rounded-full animate-twinkle animation-delay-300"></div>
            <div class="absolute top-2/3 left-1/5 w-2 h-2 bg-pink-300 rounded-full animate-twinkle animation-delay-600"></div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-24 bg-gradient-to-br from-slate-50 via-white to-blue-50 relative overflow-hidden">
        <!-- Background Decorative Elements -->
        <div class="absolute inset-0 opacity-30">
            <div class="decorative-blob absolute top-20 left-10 w-32 h-32 bg-gradient-to-br from-blue-200 to-indigo-200 rounded-full blur-3xl"></div>
            <div class="decorative-blob absolute bottom-20 right-10 w-40 h-40 bg-gradient-to-br from-purple-200 to-pink-200 rounded-full blur-3xl"></div>
        </div>
        
     

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 lg:pl-24 xl:pl-32 relative z-10">
            <!-- Content Section -->
            <div class="w-full">
                    <!-- Section Header -->
                    <div class="text-center mb-20">
                        <div class="section-badge inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-100 to-indigo-100 rounded-full text-blue-700 font-medium text-sm mb-6">
                            <i class="fas fa-star mr-2"></i>
                            Empowering Every Role
                        </div>
                        <h2 class="section-title text-4xl md:text-5xl lg:text-5xl font-bold text-gray-900 mb-6 leading-tight">
                            {{ __('messages.empowering_every_role') }}
                            <span class="bg-clip-text bg-gradient-to-r from-sky-400 to-indigo-500">{{ __('messages.dala3chic') }}</span>
                        </h2>
                        <p class="section-description text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                            {{ __('messages.platform_designed_for_needs') }}
                        </p>
                    </div>

                    <!-- Features Grid -->
            <div class="features-grid grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 gap-8 sm:gap-10 md:gap-12 lg:gap-16">
                <!-- For Vendors -->
                <div class="feature-card relative group">
                    <div class="absolute inset-0 rounded-2xl  group-hover:opacity-30 transition-opacity duration-500" style="background: linear-gradient(135deg, #4272f5 0%, #6180e6 100%);"></div>
                    <div class="relative bg-white/80 backdrop-blur-sm rounded-3xl p-6 sm:p-8 md:p-10 shadow-xl hover:shadow-2xl transition-all duration-500 border border-white/20 hover:-translate-y-3 h-full">
                        <!-- Number Badge -->
                        <div class="number-badge absolute -top-3 -left-3 sm:-top-4 sm:-left-4 w-12 h-12 sm:w-14 sm:h-14 text-white rounded-full flex items-center justify-center font-bold text-lg sm:text-xl shadow-lg" style="background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);">
                            01
                        </div>

                        <!-- Illustration -->
                        <div class="flex items-center justify-center mb-6 sm:mb-8 mt-3 sm:mt-4">
                            <div class="illustration-container relative">
                                <div class="absolute inset-0 rounded-2xl blur-lg opacity-30" style="background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);"></div>
                                <div class="relative p-4 sm:p-6 rounded-2xl" style="background: linear-gradient(135deg, #dbeafe 0%, #e0e7ff 100%);">
                                    <img src="{{ asset('images/illustrations/vendor-illustration.svg') }}" alt="Vendor Illustration" class="w-12 h-12 sm:w-16 sm:h-16 mx-auto">
                                </div>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="text-center">
                            <h3 class="text-2xl sm:text-3xl font-bold text-gray-900 group-hover:text-blue-500 transition-colors mb-2">
                                {{ __('messages.for_vendors') }}
                            </h3>
                            <h4 class="text-base sm:text-lg font-semibold mb-4 sm:mb-6" style="color: #1d4ed8;">
                                {{ __('messages.established_businesses') }}
                            </h4>
                            <p class="text-gray-600 leading-relaxed text-base sm:text-lg">
                                {{ __('messages.vendors_description') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- For Merchants -->
                <div class="feature-card relative group">
                    <div class=" absolute inset-0 rounded-2xl  group-hover:opacity-30 transition-opacity duration-500" style="background: linear-gradient(135deg, #f79f3a 0%, #fa894ce8 100%);"></div>
                    <div class="relative bg-white/80 backdrop-blur-sm rounded-3xl p-6 sm:p-8 md:p-10 shadow-xl hover:shadow-2xl transition-all duration-500 border border-white/20 hover:-translate-y-3 h-full">
                         <!-- Number Badge -->
                         <div class="number-badge absolute -top-3 -left-3 sm:-top-4 sm:-left-4 w-12 h-12 sm:w-14 sm:h-14 text-white rounded-full flex items-center justify-center font-bold text-lg sm:text-xl shadow-lg" style="background: linear-gradient(135deg, #d97706 0%, #ea580c 100%);">
                             02
                         </div>

                         <!-- Illustration -->
                         <div class="flex items-center justify-center mb-6 sm:mb-8 mt-3 sm:mt-4">
                             <div class="illustration-container relative">
                                 <div class="absolute inset-0 rounded-2xl blur-lg opacity-30" style="background: linear-gradient(135deg, #d97706 0%, #ea580c 100%);"></div>
                                 <div class="relative p-4 sm:p-6 rounded-2xl" style="background: linear-gradient(135deg, #fed7aa 0%, #fde68a 100%);">
                                     <img src="{{ asset('images/illustrations/merchantil.png') }}" alt="Merchant Illustration" class="w-12 h-12 sm:w-16 sm:h-16 mx-auto">
                                 </div>
                             </div>
                         </div>

                         <!-- Content -->
                         <div class="text-center">
                             <h3 class="text-2xl sm:text-3xl font-bold text-gray-900 group-hover:text-orange-500 transition-colors mb-2">
                                 {{ __('messages.for_merchants') }}
                             </h3>
                             <h4 class="text-base sm:text-lg font-semibold mb-4 sm:mb-6" style="color: #d97706;">
                                 {{ __('messages.individual_entrepreneurs') }}
                             </h4>
                             <p class="text-gray-600 leading-relaxed text-base sm:text-lg">
                                 {{ __('messages.merchants_description') }}
                             </p>
                         </div>
                    </div>
                </div>

                <!-- For Providers -->
                <div class="feature-card relative group">
                    <div class="absolute inset-0 rounded-2xl   group-hover:opacity-30 transition-opacity duration-500" style="background: linear-gradient(135deg, #b185fe 0%, #8b5cf6 100%);"></div>
                    <div class="relative bg-white/80 backdrop-blur-sm rounded-3xl p-6 sm:p-8 md:p-10 shadow-xl hover:shadow-2xl transition-all duration-500 border border-white/20 hover:-translate-y-3 h-full">
                         <!-- Number Badge -->
                         <div class="number-badge absolute -top-3 -left-3 sm:-top-4 sm:-left-4 w-12 h-12 sm:w-14 sm:h-14 text-white rounded-full flex items-center justify-center font-bold text-lg sm:text-xl shadow-lg" style="background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%);">
                             03
                         </div>

                         <!-- Illustration -->
                         <div class="flex items-center justify-center mb-6 sm:mb-8 mt-3 sm:mt-4">
                             <div class="illustration-container relative">
                                 <div class="absolute inset-0 rounded-2xl blur-lg opacity-30" style="background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%);"></div>
                                 <div class="relative p-4 sm:p-6 rounded-2xl" style="background: linear-gradient(135deg, #ede9fe 0%, #f3e8ff 100%);">
                                     <img src="{{ asset('images/illustrations/provider-wholesale.svg') }}" alt="Provider Wholesale Illustration" class="w-12 h-12 sm:w-16 sm:h-16 mx-auto">
                                 </div>
                             </div>
                         </div>

                         <!-- Content -->
                         <div class="text-center">
                             <h3 class="text-2xl sm:text-3xl font-bold text-gray-900 group-hover:text-purple-500 transition-colors mb-2">
                                 {{ __('messages.for_providers') }}
                             </h3>
                             <h4 class="text-base sm:text-lg font-semibold mb-4 sm:mb-6" style="color: #7c3aed;">
                                 {{ __('messages.wholesale_b2b') }}
                             </h4>
                             <p class="text-gray-600 leading-relaxed text-base sm:text-lg">
                                 {{ __('messages.providers_description') }}
                             </p>
                         </div>
                    </div>
                </div>

                <!-- For Customers -->
                <div class="feature-card relative group">
                    <div class="bg-blur-effect absolute inset-0 bg-gradient-to-br from-purple-500 to-pink-600 rounded-3xl blur-xl opacity-20 group-hover:opacity-30 transition-opacity duration-500"></div>
                    <div class="relative bg-white/80 backdrop-blur-sm rounded-3xl p-6 sm:p-8 md:p-10 shadow-xl hover:shadow-2xl transition-all duration-500 border border-white/20 hover:-translate-y-3 h-full">
                         <!-- Number Badge -->
                         <div class="number-badge absolute -top-3 -left-3 sm:-top-4 sm:-left-4 w-12 h-12 sm:w-14 sm:h-14 bg-black text-white rounded-full flex items-center justify-center font-bold text-lg sm:text-xl shadow-lg">
                             4
                         </div>

                         <!-- Illustration -->
                         <div class="flex items-center justify-center mb-6 sm:mb-8 mt-3 sm:mt-4">
                             <div class="illustration-container relative">
                                 <div class="absolute inset-0 bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl blur-lg opacity-30"></div>
                                 <div class="relative bg-gradient-to-br from-purple-50 to-pink-50 p-4 sm:p-6 rounded-2xl">
                                     <img src="{{ asset('images/illustrations/mobile-phone-illustration.svg') }}" alt="Mobile Phone Illustration" class="w-12 h-12 sm:w-16 sm:h-16 mx-auto">
                                 </div>
                             </div>
                         </div>

                         <!-- Content -->
                         <div class="text-center">
                             <h3 class="text-2xl sm:text-3xl font-bold text-gray-900 group-hover:text-sky-600 transition-colors mb-2">
                                 {{ __('messages.for_customers') }}
                             </h3>
                             <h4 class="text-base sm:text-lg font-semibold text-sky-600 mb-4 sm:mb-6">
                                 {{ __('messages.discover_shop_confidence') }}
                             </h4>
                             <p class="text-gray-600 leading-relaxed text-base sm:text-lg">
                                 {{ __('messages.customers_description') }}
                             </p>
                         </div>
                    </div>
                </div>
            </div>

                <!-- Bottom CTA -->
                <div class="text-center mt-16">
                    <a href="{{ $getStartedUrl }}" class="bg-blue-600 text-white px-8 py-4 rounded-lg text-lg font-semibold hover:from-sky-500 hover:to-indigo-600 transition-all duration-200 shadow-lg hover:shadow-xl">
                        {{ __('messages.join_marketplace') }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Product Demo Section -->
    <section class="py-20 bg-black/80 from-gray-900 to-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-100 mb-6">
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-sky-100 to-white">{{ __('messages.dala3chic_platform') }}</span>
                </h2>
                <p class="text-2xl md:text-3xl font-semibold text-white/70 mb-4">
                    {{ __('messages.empower_business') }}
                </p>
                <p class="text-xl text-gray-100 max-w-4xl mx-auto">
                    {{ __('messages.platform_description') }}
                </p>
            </div>

            <!-- Demo Content -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center mb-16">
                <!-- Left Content -->
                <div class="space-y-8">
                    <div class="space-y-6">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-gray-800 rounded-xl flex items-center justify-center">
                                <i class="fas fa-bolt text-xl ml-1 mb-1 h-6 w-6 text-blue-600"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-blue-400 mb-2">{{ __('messages.vendor_dashboard') }}</h3>
                                <p class="text-gray-300">
                                    {{ __('messages.dashboard_description') }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-gray-800 rounded-xl flex items-center justify-center">
                                <i class="fas fa-code text-md ml-1 mt-2 h-6 w-6 text-blue-600"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-blue-400 mb-2">{{ __('messages.multi_store_integration') }}</h3>
                                <p class="text-gray-300">
                                    {{ __('messages.integration_description') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <a href="{{ $getStartedUrl }}" class="bg-blue-600 text-white px-8 py-4 rounded-lg text-lg font-semibold hover:from-sky-500 hover:to-indigo-600 transition-all duration-200 shadow-lg hover:shadow-xl flex items-center gap-2 inline-flex">
                            {{ __('messages.start_selling') }}
                            <i class="fas fa-arrow-right h-5 w-5"></i>
                        </a>
                        <p class="text-md text-gray-300">
                            {{ __('messages.join_successful_vendors') }}
                        </p>
                    </div>
                </div>

                <!-- Right Content - Demo Image -->
                <div class="relative">
                    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden border border-gray-200">
                        <img src="{{ asset('assets/vendordash.webp') }}" alt="Dala3Chic Vendor Dashboard" class="w-full h-auto" />
                    </div>

                    <!-- Floating Status Cards -->
                    <div class="absolute -top-4 -right-4 bg-white rounded-lg shadow-lg p-3 border border-gray-200">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-sky-500 rounded-full animate-pulse"></div>
                            <span class="text-sm font-medium text-gray-700">{{ __('messages.store_live') }}</span>
                        </div>
                    </div>

                    <div class="absolute -bottom-4 -left-4 bg-white rounded-lg shadow-lg p-3 border border-gray-200">
                        <div class="text-sm">
                            <div class="font-medium text-gray-900">{{ __('messages.sales_today') }}</div>
                            <div class="text-sky-600 font-semibold">{{ __('messages.sales_amount') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-20 bg-gradient-to-br from-sky-50 to-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                    {{ __('messages.what_vendors_say') }}
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    {{ __('messages.join_entrepreneurs') }}
                </p>
            </div>

            <!-- Testimonial Card -->
            <div class="relative max-w-4xl mx-auto">
                <div class="bg-white rounded-2xl shadow-xl p-8 md:p-12 border border-gray-100 min-h-[300px] flex flex-col justify-center">
                    <div class="text-center">
                        <!-- Quote -->
                        <div class="mb-8">
                            <svg class="w-12 h-12 text-sky-200 mx-auto mb-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-10zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h4v10h-10z"/>
                            </svg>
                            <p class="text-lg md:text-xl text-gray-700 leading-relaxed italic">
                                {{ __('messages.testimonial_text') }}
                            </p>
                        </div>

                        <!-- Author -->
                        <div class="flex items-center justify-center space-x-4">
                            <img src="{{ asset('assets/logo.png') }}" alt="Sarah Ahmed" class="w-16 h-16 rounded-full object-cover border-4 border-sky-100" />
                            <div class="text-left">
                                <h4 class="text-lg font-semibold text-gray-900">
                                    {{ __('messages.testimonial_author') }}
                                </h4>
                                <p class="text-sky-600 font-medium">
                                    {{ __('messages.testimonial_role') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-16">
                <div class="text-center">
                    <div class="text-3xl md:text-4xl font-bold text-sky-600 mb-2">{{ $totalVendors }}+</div>
                    <div class="text-gray-600 font-medium">{{ __('messages.active_vendors') }}</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl md:text-4xl font-bold text-sky-600 mb-2">{{ $totalProducts }}+</div>
                    <div class="text-gray-600 font-medium">{{ __('messages.products_listed') }}</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl md:text-4xl font-bold text-sky-600 mb-2">{{ $satisfactionRate }}%</div>
                    <div class="text-gray-600 font-medium">{{ __('messages.customer_satisfaction') }}</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-black/90 text-white">
        <!-- Main Footer Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Company Info -->
                <div class="lg:col-span-1">
                    <div class="flex items-center mb-6">
                        <div class="text-3xl font-bold bg-gradient-to-r from-blue-400 to-blue-600 bg-clip-text text-transparent">Dala3Chic</div>
                    </div>
                    <p class="text-blue-100 mb-6 leading-relaxed">
                        {{ __('messages.footer_description') }}
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 bg-blue-700 hover:bg-blue-600 rounded-lg flex items-center justify-center transition-colors">
                            <i class="fab fa-github h-5 w-5"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-blue-700 hover:bg-blue-600 rounded-lg flex items-center justify-center transition-colors">
                            <i class="fab fa-twitter h-5 w-5"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-blue-700 hover:bg-blue-600 rounded-lg flex items-center justify-center transition-colors">
                            <i class="fab fa-linkedin-in h-5 w-5"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-blue-700 hover:bg-blue-600 rounded-lg flex items-center justify-center transition-colors">
                            <i class="fab fa-envelope h-5 w-5"></i>
                        </a>
                    </div>
                </div>

                <!-- Marketplace Links -->
                <div>
                    <h3 class="text-lg font-semibold text-white mb-6">{{ __('messages.marketplace') }}</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-pink-100 hover:text-white transition-colors">{{ __('messages.for_vendors') }}</a></li>
                        <li><a href="#" class="text-pink-100 hover:text-white transition-colors">{{ __('messages.for_merchants') }}</a></li>
                        <li><a href="#" class="text-pink-100 hover:text-white transition-colors">{{ __('messages.for_providers') }}</a></li>
                        <li><a href="#" class="text-pink-100 hover:text-white transition-colors">{{ __('messages.browse_products') }}</a></li>
                        <li><a href="#" class="text-pink-100 hover:text-white transition-colors">{{ __('messages.success_stories') }}</a></li>
                    </ul>
                </div>

                <!-- Support Links -->
                <div>
                    <h3 class="text-lg font-semibold text-white mb-6">{{ __('messages.support') }}</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-pink-100 hover:text-white transition-colors">{{ __('messages.help_center') }}</a></li>
                        <li><a href="#" class="text-pink-100 hover:text-white transition-colors">{{ __('messages.seller_guide') }}</a></li>
                        <li><a href="#" class="text-pink-100 hover:text-white transition-colors">{{ __('messages.community') }}</a></li>
                        <li><a href="#" class="text-pink-100 hover:text-white transition-colors">{{ __('messages.safety') }}</a></li>
                        <li><a href="#" class="text-pink-100 hover:text-white transition-colors">{{ __('messages.contact_us') }}</a></li>
                    </ul>
                </div>

                <!-- Newsletter -->
                <div>
                    <h3 class="text-lg font-semibold text-white mb-6">{{ __('messages.stay_connected') }}</h3>
                    <p class="text-blue-100 mb-4">
                        {{ __('messages.newsletter_description') }}
                    </p>
                    <form class="space-y-3">
                        <div class="relative">
                            <input type="email" placeholder="{{ __('messages.enter_email') }}" class="w-full px-4 py-3 bg-blue-800 border border-blue-700 rounded-lg text-white placeholder-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required />
                        </div>
                        <button type="submit" class="w-full bg-gradient-to-r from-sky-400 to-indigo-500 text-white px-4 py-3 rounded-lg font-semibold hover:from-sky-500 hover:to-indigo-600 transition-all duration-200 flex items-center justify-center gap-2">
                            {{ __('messages.subscribe') }}
                            <i class="fas fa-arrow-right h-4 w-4"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="border-t border-blue-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                    <div class="text-blue-100 text-sm">
                        &copy; {{ date('Y') }} Dala3Chic. {{ __('messages.all_rights_reserved') }}
                    </div>
                    <div class="flex space-x-6 text-sm">
                        <a href="#" class="text-blue-100 hover:text-white transition-colors">{{ __('messages.privacy_policy') }}</a>
                        <a href="#" class="text-blue-100 hover:text-white transition-colors">{{ __('messages.terms_service') }}</a>
                        <a href="#" class="text-blue-100 hover:text-white transition-colors">{{ __('messages.seller_agreement') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    @vite(['resources/js/app.js', 'resources/js/modern-interactions.js'])
    
    <!-- Additional Styles for Mobile Menu and Language Switcher -->
    <style>
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

        /* Language Switcher Styles */
        .lang-btn {
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .lang-btn:hover {
            background-color: rgba(59, 130, 246, 0.1);
            transform: translateY(-1px);
        }

        .lang-btn.active {
            background-color: rgba(59, 130, 246, 0.2);
            color: rgb(37, 99, 235) !important;
            font-weight: 600;
        }

        /* RTL Support */
        body.rtl {
            direction: rtl;
            text-align: right;
        }

        body.rtl .flex {
            flex-direction: row-reverse;
        }

        body.rtl .space-x-8 > * + * {
            margin-left: 0;
            margin-right: 2rem;
        }
    </style>
    
    <!-- Mobile Menu and Language Switcher JavaScript -->
    <script>
        // Initialize language on page load
        document.addEventListener('DOMContentLoaded', function() {
            const currentLang = '{{ app()->getLocale() }}';
            setActiveLanguage(currentLang);
            localStorage.setItem('language', currentLang);

            // Mobile menu toggle functionality
            const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
            const mobileMenu = document.querySelector('.mobile-menu');

            if (mobileMenuToggle && mobileMenu) {
                mobileMenuToggle.addEventListener('click', function() {
                    const isOpen = mobileMenu.style.display === 'block';
                    mobileMenu.style.display = isOpen ? 'none' : 'block';
                    mobileMenuToggle.classList.toggle('active');
                    document.body.classList.toggle('menu-open');
                });

                // Close mobile menu when clicking on links
                const mobileLinks = mobileMenu.querySelectorAll('a');
                mobileLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        mobileMenu.style.display = 'none';
                        mobileMenuToggle.classList.remove('active');
                        document.body.classList.remove('menu-open');
                    });
                });
            }
        });
        
        function switchLanguage(lang) {
            // Store language preference immediately
            localStorage.setItem('language', lang);

            // Show loading state (optional visual feedback)
            showLanguageNotification(lang, true);

            // Make AJAX request to switch language
            fetch('{{ route('language.switch.post') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ locale: lang })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Automatically refresh the page to ensure complete translation
                    window.location.reload();
                } else {
                    console.error('Language switch failed:', data.message);
                    showLanguageNotification('en', false, 'Failed to switch language');
                    // Still attempt to reload in case of server-side success but response issue
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
            })
            .catch(error => {
                console.error('Error switching language:', error);
                showLanguageNotification('en', false, 'Error switching language');
                // Still attempt to reload in case the language was actually switched
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            });
        }

        function updatePageLanguage(lang) {
            // Fetch translated content for the new language
            fetch('{{ route('language.translations.landing') }}?locale=' + lang)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update page direction and language attributes
                    document.documentElement.setAttribute('lang', data.locale);
                    document.documentElement.setAttribute('dir', data.direction);
                    document.body.setAttribute('dir', data.direction);

                    // Update RTL class
                    if (data.is_rtl) {
                        document.body.classList.add('rtl');
                    } else {
                        document.body.classList.remove('rtl');
                    }

                    // Update page title
                    document.title = data.translations.page_title;

                    // Update all translatable content
                    updateTranslatableContent(data.translations);

                    // Update active language button
                    setActiveLanguage(lang);

                    // Store language preference
                    localStorage.setItem('language', lang);
                } else {
                    console.error('Failed to fetch translations:', data.message);
                    // Fallback to page reload
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Error fetching translations:', error);
                // Fallback to page reload
                window.location.reload();
            });
        }
        
        function setActiveLanguage(lang) {
            // Remove active class from all buttons
            document.querySelectorAll('.lang-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Add active class to selected language buttons
            document.querySelectorAll(`[data-lang="${lang}"]`).forEach(btn => {
                btn.classList.add('active');
            });
        }

        
        function updateTranslatableContent(translations) {
            // Update navigation links
            const navLinks = {
                'a[href="#features"]': 'services',
                'a[href="#about"]': 'about',
                'a[href="#contact"]': 'contact'
            };

            Object.keys(navLinks).forEach(selector => {
                document.querySelectorAll(selector).forEach(link => {
                    link.textContent = translations[navLinks[selector]];
                });
            });

            // Update language switcher buttons
            document.querySelectorAll('[data-lang="en"]').forEach(btn => {
                if (btn.textContent.includes('English') || btn.textContent.includes('EN')) {
                    btn.textContent = btn.textContent.includes('EN') ? 'EN' : translations.english;
                }
            });

            document.querySelectorAll('[data-lang="ar"]').forEach(btn => {
                if (btn.textContent.includes('العربية') || btn.textContent.includes('AR')) {
                    btn.textContent = btn.textContent.includes('AR') ? 'AR' : translations.arabic;
                }
            });

            // Update hero section
            const heroTitle = document.querySelector('.hero-title');
            if (heroTitle) {
                heroTitle.innerHTML = `
                    ${translations.your_premier}
                    <span class="bg-gradient-to-r from-pink-600 via-violet-400 to-pink-600 bg-clip-text text-white/40 font-bold">
                        ${translations.dala3chic}
                    </span>
                    ${translations.experience}
                `;
            }

            const heroSubtitle = document.querySelector('.hero-subtitle');
            if (heroSubtitle) {
                heroSubtitle.textContent = translations.hero_subtitle;
            }

            // Update CTA buttons - need to handle different user roles
            document.querySelectorAll('.btn-primary, .btn-secondary').forEach(btn => {
                const text = btn.textContent.trim();

                // Map common button texts
                if (text.includes('Get Started') || text.includes('ابدأ الآن')) {
                    btn.innerHTML = btn.innerHTML.replace(/Get Started|ابدأ الآن/, translations.get_started);
                } else if (text.includes('Learn More') || text.includes('اعرف المزيد')) {
                    btn.innerHTML = btn.innerHTML.replace(/Learn More|اعرف المزيد/, translations.learn_more);
                } else if (text.includes('Create Account') || text.includes('إنشاء حساب')) {
                    btn.innerHTML = btn.innerHTML.replace(/Create Account|إنشاء حساب/, translations.create_account);
                } else if (text.includes('Login') || text.includes('تسجيل الدخول')) {
                    btn.innerHTML = btn.innerHTML.replace(/Login|تسجيل الدخول/, translations.login);
                } else if (text.includes('Dashboard') || text.includes('لوحة التحكم')) {
                    btn.innerHTML = btn.innerHTML.replace(/Dashboard|لوحة التحكم/, translations.dashboard_nav);
                }
            });

            // Update stats section
            const statLabels = document.querySelectorAll('.stat-label');
            statLabels.forEach(label => {
                const text = label.textContent.trim();
                if (text.includes('Products Available') || text.includes('منتج متاح')) {
                    label.textContent = translations.products_available;
                } else if (text.includes('Trusted Vendors') || text.includes('بائعون موثوقون')) {
                    label.textContent = translations.trusted_vendors;
                } else if (text.includes('Happy Customers') || text.includes('عملاء سعداء')) {
                    label.textContent = translations.happy_customers;
                } else if (text.includes('Satisfaction Rate') || text.includes('معدل الرضا')) {
                    label.textContent = translations.satisfaction_rate;
                }
            });

            // Update features section
            const featuresTitle = document.querySelector('.features-title');
            if (featuresTitle) {
                featuresTitle.textContent = translations.why_choose_dala3chic;
            }

            const featuresSubtitle = document.querySelector('.features-subtitle');
            if (featuresSubtitle) {
                featuresSubtitle.textContent = translations.next_generation_shopping;
            }

            // Update feature cards
            const featureCards = document.querySelectorAll('.feature-card');
            const featureData = [
                { title: 'secure_transactions', desc: 'secure_transactions_desc' },
                { title: 'fast_delivery', desc: 'fast_delivery_desc' },
                { title: 'customer_support', desc: 'customer_support_desc' },
                { title: 'quality_assurance', desc: 'quality_assurance_desc' },
                { title: 'mobile_responsive', desc: 'mobile_responsive_desc' },
                { title: 'competitive_pricing', desc: 'competitive_pricing_desc' }
            ];

            featureCards.forEach((card, index) => {
                if (featureData[index]) {
                    const title = card.querySelector('.feature-title');
                    const desc = card.querySelector('.feature-description');

                    if (title) title.textContent = translations[featureData[index].title];
                    if (desc) desc.textContent = translations[featureData[index].desc];
                }
            });

            // Update CTA section
            const ctaTitle = document.querySelector('.cta-title');
            if (ctaTitle) {
                ctaTitle.textContent = translations.ready_to_start_shopping;
            }

            const ctaSubtitle = document.querySelector('.cta-subtitle');
            if (ctaSubtitle) {
                ctaSubtitle.textContent = translations.join_thousands_customers;
            }
        }
        
        function showLanguageNotification(lang, isLoading = false, errorMessage = null) {
            let message;
            let backgroundColor = 'rgba(0, 0, 0, 0.9)';

            if (isLoading) {
                message = lang === 'ar' ? 'جاري تغيير اللغة...' : 'Switching language...';
            } else if (errorMessage) {
                message = errorMessage;
                backgroundColor = 'rgba(220, 38, 38, 0.9)'; // Red for errors
            } else {
                message = lang === 'ar' ? 'تم تغيير اللغة إلى العربية' : 'Language changed to English';
                backgroundColor = 'rgba(34, 197, 94, 0.9)'; // Green for success
            }

            // Remove any existing notifications
            const existingNotifications = document.querySelectorAll('.language-notification');
            existingNotifications.forEach(notif => notif.remove());

            // Create notification element
            const notification = document.createElement('div');
            notification.className = 'language-notification';
            notification.style.cssText = `
                position: fixed;
                top: 80px;
                right: 20px;
                background: ${backgroundColor};
                color: white;
                padding: 12px 20px;
                border-radius: 8px;
                z-index: 9999;
                font-size: 14px;
                opacity: 0;
                transition: opacity 0.3s ease;
                display: flex;
                align-items: center;
                gap: 8px;
            `;

            if (isLoading) {
                notification.innerHTML = `
                    <div style="width: 16px; height: 16px; border: 2px solid #ffffff; border-top: 2px solid transparent; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                    <span>${message}</span>
                `;

                // Add spinner animation
                const style = document.createElement('style');
                style.textContent = `
                    @keyframes spin {
                        0% { transform: rotate(0deg); }
                        100% { transform: rotate(360deg); }
                    }
                `;
                document.head.appendChild(style);
            } else {
                notification.textContent = message;
            }

            document.body.appendChild(notification);

            // Show notification
            setTimeout(() => {
                notification.style.opacity = '1';
            }, 100);

            // Hide and remove notification (don't auto-hide loading notifications)
            if (!isLoading) {
                setTimeout(() => {
                    notification.style.opacity = '0';
                    setTimeout(() => {
                        if (notification.parentNode) {
                            document.body.removeChild(notification);
                        }
                    }, 300);
                }, 3000);
            }
        }
    </script>
    
    <!-- Character Scroll Visibility Script -->
    <script src="{{ asset('js/character-scroll.js') }}"></script>
</body>
</html>
