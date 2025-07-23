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
</head>
<body class="landing-page">
    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white/10 backdrop-blur-md border-b border-white/20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <div class="w-10 h-7 bg-transparent flex items-center justify-center">
                        <img src="{{ asset('assets/logo.png') }}" alt="Dala3Chic Logo" class="w-20 h-10 object-cover rounded-lg">
                    </div>
                    <span class="ml-3 text-white font-bold text-xl">Dala3Chic</span>
                </div>
                
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="text-white/90 hover:text-white transition-colors">Features</a>
                    <a href="#about" class="text-white/90 hover:text-white transition-colors">About</a>
                    <a href="#contact" class="text-white/90 hover:text-white transition-colors">Contact</a>
                    
                    <!-- Language Switcher -->
                    <div class="language-switcher flex items-center space-x-2">
                        <button onclick="switchLanguage('en')" class="lang-btn text-white/90 hover:text-white transition-colors px-2 py-1 rounded" data-lang="en">
                            EN
                        </button>
                        <span class="text-white/60">|</span>
                        <button onclick="switchLanguage('ar')" class="lang-btn text-white/90 hover:text-white transition-colors px-2 py-1 rounded" data-lang="ar">
                            AR
                        </button>
                    </div>
                    
                    @if($isAuthenticated)
                        <a href="{{ $getStartedUrl }}" class="btn-secondary">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn-secondary">Login</a>
                    @endif
                </div>
                
                <button class="md:hidden text-white mobile-menu-toggle">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
        
        <!-- Mobile Menu -->
        <div class="mobile-menu md:hidden bg-white/10 backdrop-blur-md border-t border-white/20">
            <div class="px-4 py-4 space-y-3">
                <a href="#features" class="block text-white/90 hover:text-white transition-colors">{{ __('messages.services') }}</a>
                <a href="#about" class="block text-white/90 hover:text-white transition-colors">{{ __('messages.about') }}</a>
                <a href="#contact" class="block text-white/90 hover:text-white transition-colors">{{ __('messages.contact') }}</a>

                <!-- Mobile Language Switcher -->
                <div class="language-switcher flex items-center justify-center space-x-4 py-2">
                    <button onclick="switchLanguage('en')" class="lang-btn text-white/90 hover:text-white transition-colors px-3 py-1 rounded border border-white/30" data-lang="en">
                        {{ __('messages.english') }}
                    </button>
                    <button onclick="switchLanguage('ar')" class="lang-btn text-white/90 hover:text-white transition-colors px-3 py-1 rounded border border-white/30" data-lang="ar">
                        {{ __('messages.arabic') }}
                    </button>
                </div>

                @if($isAuthenticated)
                    <a href="{{ $getStartedUrl }}" class="block btn-secondary text-center">{{ __('messages.dashboard_nav') }}</a>
                @else
                    <a href="{{ route('login') }}" class="block btn-secondary text-center">{{ __('messages.login') }}</a>
                @endif
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <div class="hero-text scroll-reveal">
                <h1 class="hero-title animate-fade-in-up">
                    {{ __('messages.your_premier') }}
                    <span class="bg-gradient-to-r from-pink-600 via-violet-400 to-pink-600 bg-clip-text text-white/40 font-bold">
                        {{ __('messages.dala3chic') }}
                    </span>
                    {{ __('messages.experience') }}
                </h1>
                <p class="hero-subtitle animate-fade-in-up animate-delay-200">
                    {{ __('messages.hero_subtitle') }}
                </p>
                <div class="hero-cta animate-fade-in-up animate-delay-400">
                    <a href="{{ $getStartedUrl }}" class="btn-primary hover-lift">
                        <i class="fas fa-rocket"></i>
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
                            {{ __('messages.get_started') }}
                        @endif
                    </a>
                    <a href="#features" class="btn-secondary hover-lift">
                        <i class="fas fa-play"></i>
                        {{ __('messages.learn_more') }}
                    </a>
                </div>
            </div>
            
            <div class="hero-visual scroll-reveal animate-fade-in-right animate-delay-300">
                <div class="relative">
                    <!-- Floating Elements -->
                    <div class="absolute -top-4 -left-4 w-20 h-20 bg-yellow-400/20 rounded-full animate-float"></div>
                    <div class="absolute -bottom-4 -right-4 w-16 h-16 bg-blue-400/20 rounded-full animate-float animate-delay-200"></div>
                    <div class="absolute top-1/2 -right-8 w-12 h-12 bg-pink-400/20 rounded-full animate-float animate-delay-400"></div>
                    
                    <!-- Main Visual -->
                    <div class="w-96 h-66 bg-transparent backdrop-blur-sm rounded-[20px] flex items-center justify-center ">
                        <img src="{{asset('assets/logo.png')}}"  alt="Dala3Chic Logo">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="stats-container stagger-children">
            <div class="stat-item scroll-reveal">
                <div class="stat-number" data-target="{{ $totalProducts }}">0+</div>
                <div class="stat-label">{{ __('messages.products_available') }}</div>
            </div>
            <div class="stat-item scroll-reveal animate-delay-100">
                <div class="stat-number" data-target="{{ $totalVendors }}">0+</div>
                <div class="stat-label">{{ __('messages.trusted_vendors') }}</div>
            </div>
            <div class="stat-item scroll-reveal animate-delay-200">
                <div class="stat-number" data-target="{{ $totalCustomers }}">0+</div>
                <div class="stat-label">{{ __('messages.happy_customers') }}</div>
            </div>
            <div class="stat-item scroll-reveal animate-delay-300">
                <div class="stat-number" data-target="{{ $satisfactionRate }}">0%</div>
                <div class="stat-label">{{ __('messages.satisfaction_rate') }}</div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features-section">
        <div class="features-container">
            <div class="features-header scroll-reveal">
                <h2 class="features-title">{{ __('messages.why_choose_dala3chic') }}</h2>
                <p class="features-subtitle">
                    {{ __('messages.next_generation_shopping') }}
                </p>
            </div>
            
            <div class="features-grid stagger-children">
                <div class="feature-card scroll-reveal hover-lift">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="feature-title">{{ __('messages.secure_transactions') }}</h3>
                    <p class="feature-description">
                        {{ __('messages.secure_transactions_desc') }}
                    </p>
                </div>

                <div class="feature-card scroll-reveal hover-lift animate-delay-100">
                    <div class="feature-icon">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <h3 class="feature-title">{{ __('messages.fast_delivery') }}</h3>
                    <p class="feature-description">
                        {{ __('messages.fast_delivery_desc') }}
                    </p>
                </div>

                <div class="feature-card scroll-reveal hover-lift animate-delay-200">
                    <div class="feature-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3 class="feature-title">{{ __('messages.customer_support') }}</h3>
                    <p class="feature-description">
                        {{ __('messages.customer_support_desc') }}
                    </p>
                </div>
                
                <div class="feature-card scroll-reveal hover-lift animate-delay-300">
                    <div class="feature-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3 class="feature-title">{{ __('messages.quality_assurance') }}</h3>
                    <p class="feature-description">
                        {{ __('messages.quality_assurance_desc') }}
                    </p>
                </div>

                <div class="feature-card scroll-reveal hover-lift animate-delay-400">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3 class="feature-title">{{ __('messages.mobile_responsive') }}</h3>
                    <p class="feature-description">
                        {{ __('messages.mobile_responsive_desc') }}
                    </p>
                </div>

                <div class="feature-card scroll-reveal hover-lift animate-delay-500">
                    <div class="feature-icon">
                        <i class="fas fa-tags"></i>
                    </div>
                    <h3 class="feature-title">{{ __('messages.competitive_pricing') }}</h3>
                    <p class="feature-description">
                        {{ __('messages.competitive_pricing_desc') }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="cta-container scroll-reveal">
            <h2 class="cta-title animate-scale-in">{{ __('messages.ready_to_start_shopping') }}</h2>
            <p class="cta-subtitle animate-fade-in-up animate-delay-200">
                {{ __('messages.join_thousands_customers') }}
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center animate-fade-in-up animate-delay-400">
                <a href="{{ $getStartedUrl }}" class="btn-primary hover-lift">
                    @if($isAuthenticated)
                        <i class="fas fa-tachometer-alt"></i>
                        @switch($userRole)
                            @case('admin')
                                {{ __('messages.admin_dashboard') }}
                                @break
                            @case('vendor')
                                {{ __('messages.vendor_dashboard') }}
                                @break
                            @case('provider')
                                {{ __('messages.provider_dashboard') }}
                                @break
                            @default
                                {{ __('messages.continue_shopping') }}
                        @endswitch
                    @else
                        <i class="fas fa-user-plus"></i>
                        {{ __('messages.create_account') }}
                    @endif
                </a>
                <a href="#features" class="btn-secondary hover-lift">
                    <i class="fas fa-info-circle"></i>
                    {{ __('messages.learn_more') }}
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center">
                            <img src="{{ asset('assets/logo.png') }}" alt="Dala3Chic Logo" class="w-8 h-8 object-contain rounded">
                        </div>
                        <span class="ml-3 text-xl font-bold">Dala3Chic</span>
                    </div>
                    <p class="text-gray-400">
                        Your premier destination for quality products and services from trusted vendors worldwide.
                    </p>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">About Us</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Contact</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Help Center</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Terms of Service</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">Categories</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Electronics</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Fashion</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Home & Garden</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Services</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">Connect With Us</h3>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-facebook-f text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-twitter text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-instagram text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-linkedin-in text-xl"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-8 pt-8 text-center">
                <p class="text-gray-400">
                    &copy; {{ date('Y') }} Dala3Chic. All rights reserved.
                </p>
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
        
        .tooltip {
            position: absolute;
            background: rgba(0, 0, 0, 0.9);
            color: white;
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            font-size: 0.875rem;
            opacity: 0;
            transition: opacity 0.2s ease;
            pointer-events: none;
            z-index: 1000;
        }
        
        .tooltip.visible {
            opacity: 1;
        }
        
        /* Language Switcher Styles */
        .lang-btn {
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .lang-btn:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateY(-1px);
        }
        
        .lang-btn.active {
            background-color: rgba(255, 255, 255, 0.2);
            color: white !important;
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
    
    <!-- Language Switcher JavaScript -->
    <script>
        // Initialize language on page load
        document.addEventListener('DOMContentLoaded', function() {
            const currentLang = '{{ app()->getLocale() }}';
            setActiveLanguage(currentLang);
            localStorage.setItem('language', currentLang);
        });
        
        function switchLanguage(lang) {
            // Show loading state
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
                    // Update language immediately without page reload
                    updatePageLanguage(lang);
                    showLanguageNotification(lang, false);
                } else {
                    console.error('Language switch failed:', data.message);
                    showLanguageNotification('en', false, 'Failed to switch language');
                }
            })
            .catch(error => {
                console.error('Error switching language:', error);
                showLanguageNotification('en', false, 'Error switching language');
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
</body>
</html>
