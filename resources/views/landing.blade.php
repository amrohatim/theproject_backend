<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Your premier marketplace for products and services from trusted vendors worldwide">
    <meta name="keywords" content="marketplace, e-commerce, products, services, vendors, shopping">
    <meta name="author" content="Dala3Chic">
    
    <title>Dala3Chic - Your Premier Shopping Destination</title>
    
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
                <a href="#features" class="block text-white/90 hover:text-white transition-colors">Features</a>
                <a href="#about" class="block text-white/90 hover:text-white transition-colors">About</a>
                <a href="#contact" class="block text-white/90 hover:text-white transition-colors">Contact</a>
                @if($isAuthenticated)
                    <a href="{{ $getStartedUrl }}" class="block btn-secondary text-center">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="block btn-secondary text-center">Login</a>
                @endif
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <div class="hero-text scroll-reveal">
                <h1 class="hero-title animate-fade-in-up">
                    Your Premier
                    <span class="bg-gradient-to-r from-pink-600 via-violet-400 to-pink-600 bg-clip-text text-white/40 font-bold">
                        Dala3Chic
                    </span>
                    Experience
                </h1>
                <p class="hero-subtitle animate-fade-in-up animate-delay-200">
                    Discover thousands of products and services from trusted vendors worldwide. 
                    Shop with confidence, enjoy seamless transactions, and experience the future of e-commerce.
                </p>
                <div class="hero-cta animate-fade-in-up animate-delay-400">
                    <a href="{{ $getStartedUrl }}" class="btn-primary hover-lift">
                        <i class="fas fa-rocket"></i>
                        @if($isAuthenticated)
                            @switch($userRole)
                                @case('admin')
                                    Go to Admin Dashboard
                                    @break
                                @case('vendor')
                                    Go to Vendor Dashboard
                                    @break
                                @case('provider')
                                    Go to Provider Dashboard
                                    @break
                                @default
                                    Continue Shopping
                            @endswitch
                        @else
                            Get Started
                        @endif
                    </a>
                    <a href="#features" class="btn-secondary hover-lift">
                        <i class="fas fa-play"></i>
                        Learn More
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
                <div class="stat-label">Products Available</div>
            </div>
            <div class="stat-item scroll-reveal animate-delay-100">
                <div class="stat-number" data-target="{{ $totalVendors }}">0+</div>
                <div class="stat-label">Trusted Vendors</div>
            </div>
            <div class="stat-item scroll-reveal animate-delay-200">
                <div class="stat-number" data-target="{{ $totalCustomers }}">0+</div>
                <div class="stat-label">Happy Customers</div>
            </div>
            <div class="stat-item scroll-reveal animate-delay-300">
                <div class="stat-number" data-target="{{ $satisfactionRate }}">0%</div>
                <div class="stat-label">Satisfaction Rate</div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features-section">
        <div class="features-container">
            <div class="features-header scroll-reveal">
                <h2 class="features-title">Why Choose Our Dala3Chic?</h2>
                <p class="features-subtitle">
                    Experience the next generation of online shopping with our cutting-edge features 
                    designed to make your journey seamless and enjoyable.
                </p>
            </div>
            
            <div class="features-grid stagger-children">
                <div class="feature-card scroll-reveal hover-lift">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="feature-title">Secure Transactions</h3>
                    <p class="feature-description">
                        Shop with confidence knowing that all transactions are protected by 
                        industry-leading security measures and encryption.
                    </p>
                </div>
                
                <div class="feature-card scroll-reveal hover-lift animate-delay-100">
                    <div class="feature-icon">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <h3 class="feature-title">Fast Delivery</h3>
                    <p class="feature-description">
                        Get your orders delivered quickly with our network of reliable 
                        shipping partners and real-time tracking.
                    </p>
                </div>
                
                <div class="feature-card scroll-reveal hover-lift animate-delay-200">
                    <div class="feature-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3 class="feature-title">24/7 Support</h3>
                    <p class="feature-description">
                        Our dedicated customer support team is available around the clock 
                        to assist you with any questions or concerns.
                    </p>
                </div>
                
                <div class="feature-card scroll-reveal hover-lift animate-delay-300">
                    <div class="feature-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3 class="feature-title">Quality Guarantee</h3>
                    <p class="feature-description">
                        Every product and service is carefully vetted to ensure you receive 
                        only the highest quality items from trusted vendors.
                    </p>
                </div>
                
                <div class="feature-card scroll-reveal hover-lift animate-delay-400">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3 class="feature-title">Mobile Optimized</h3>
                    <p class="feature-description">
                        Shop anywhere, anytime with our fully responsive design that works 
                        perfectly on all devices and screen sizes.
                    </p>
                </div>
                
                <div class="feature-card scroll-reveal hover-lift animate-delay-500">
                    <div class="feature-icon">
                        <i class="fas fa-tags"></i>
                    </div>
                    <h3 class="feature-title">Best Prices</h3>
                    <p class="feature-description">
                        Enjoy competitive pricing and exclusive deals from our network of 
                        vendors, ensuring you get the best value for your money.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="cta-container scroll-reveal">
            <h2 class="cta-title animate-scale-in">Ready to Start Shopping?</h2>
            <p class="cta-subtitle animate-fade-in-up animate-delay-200">
                Join thousands of satisfied customers and discover amazing products and services today.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center animate-fade-in-up animate-delay-400">
                <a href="{{ $getStartedUrl }}" class="btn-primary hover-lift">
                    @if($isAuthenticated)
                        <i class="fas fa-tachometer-alt"></i>
                        @switch($userRole)
                            @case('admin')
                                Admin Dashboard
                                @break
                            @case('vendor')
                                Vendor Dashboard
                                @break
                            @case('provider')
                                Provider Dashboard
                                @break
                            @default
                                Continue Shopping
                        @endswitch
                    @else
                        <i class="fas fa-user-plus"></i>
                        Create Account
                    @endif
                </a>
                <a href="#features" class="btn-secondary hover-lift">
                    <i class="fas fa-info-circle"></i>
                    Learn More
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
    
    <!-- Additional Styles for Mobile Menu -->
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
    </style>
</body>
</html>
