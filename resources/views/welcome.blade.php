<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Your one-stop marketplace for products and services">

        <title>Marketplace App</title>

        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        
        <script src="{{ asset('js/debug.js') }}" defer></script>

        <style>
            body {
                font-family: 'Inter', sans-serif;
                background-color: #f8f9fa;
            }
            
            .dark-mode {
                background-color: #121212;
                color: #f8f9fa;
            }
            
            .welcome-container {
                background-color: white;
                border-radius: 12px;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
                transition: all 0.3s ease;
            }
            
            .dark-mode .welcome-container {
                background-color: #1e1e1e;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            }
            
            .btn-primary {
                background-color: #4f46e5;
                transition: all 0.3s ease;
            }
            
            .btn-primary:hover {
                background-color: #4338ca;
                transform: translateY(-2px);
            }
            
            .btn-secondary {
                background-color: #6b7280;
                transition: all 0.3s ease;
            }
            
            .btn-secondary:hover {
                background-color: #4b5563;
                transform: translateY(-2px);
            }
            
            .feature-card {
                transition: all 0.3s ease;
            }
            
            .feature-card:hover {
                transform: translateY(-5px);
            }
            
            .dark-mode .feature-card {
                background-color: #2d2d2d;
            }
            
            .theme-toggle {
                position: absolute;
                top: 20px;
                right: 20px;
                cursor: pointer;
            }
        </style>
    </head>
    <body class="min-h-screen py-12 px-4 sm:px-6 lg:px-8" id="body">
        <!-- Theme Toggle Button -->
        <div class="theme-toggle">
            <button onclick="toggleDarkMode()" class="p-2 rounded-full bg-gray-200 dark-mode:bg-gray-700">
                <i class="fas fa-moon text-gray-700 dark-mode:hidden"></i>
                <i class="fas fa-sun text-yellow-400 hidden dark-mode:block"></i>
            </button>
        </div>

        <!-- Hero Section -->
        <div class="max-w-7xl mx-auto">
            <div class="welcome-container p-8 md:p-12 mb-12">
                <div class="flex flex-col md:flex-row items-center">
                    <div class="md:w-1/2 mb-8 md:mb-0">
                        <div class="flex justify-center md:justify-start mb-6">
                            <div class="w-20 h-20 bg-indigo-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-shopping-cart text-white text-3xl"></i>
                            </div>
                        </div>
                        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark-mode:text-white mb-4 text-center md:text-left">
                            Welcome to Marketplace App
                        </h1>
                        <p class="text-xl text-gray-600 dark-mode:text-gray-300 mb-8 text-center md:text-left">
                            Your one-stop destination for products and services from trusted vendors
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                            <a href="{{ route('admin.demo') }}" class="btn-primary px-6 py-3 rounded-lg text-white font-medium flex items-center justify-center">
                                <i class="fas fa-user-shield mr-2"></i> Admin Dashboard
                            </a>
                            <a href="{{ route('vendor.demo') }}" class="btn-primary px-6 py-3 rounded-lg text-white font-medium flex items-center justify-center">
                                <i class="fas fa-store mr-2"></i> Vendor Dashboard
                            </a>
                        </div>
                    </div>
                    <div class="md:w-1/2 flex justify-center">
                        <img src="https://placehold.co/600x400/indigo/white?text=Marketplace" alt="Marketplace Illustration" class="rounded-lg shadow-lg max-w-full h-auto" />
                    </div>
                </div>
            </div>

            <!-- Features Section -->
            <div class="mb-12">
                <h2 class="text-3xl font-bold text-center text-gray-900 dark-mode:text-white mb-12">Key Features</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="feature-card bg-white dark-mode:bg-gray-800 p-6 rounded-xl shadow">
                        <div class="w-12 h-12 bg-indigo-100 dark-mode:bg-indigo-900 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-store text-indigo-600 dark-mode:text-indigo-400 text-xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark-mode:text-white mb-2">Multi-vendor Platform</h3>
                        <p class="text-gray-600 dark-mode:text-gray-300">Allow multiple vendors to sell their products and services through a single platform.</p>
                    </div>
                    
                    <!-- Feature 2 -->
                    <div class="feature-card bg-white dark-mode:bg-gray-800 p-6 rounded-xl shadow">
                        <div class="w-12 h-12 bg-indigo-100 dark-mode:bg-indigo-900 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-chart-line text-indigo-600 dark-mode:text-indigo-400 text-xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark-mode:text-white mb-2">Advanced Analytics</h3>
                        <p class="text-gray-600 dark-mode:text-gray-300">Comprehensive analytics and reporting tools for vendors and administrators.</p>
                    </div>
                    
                    <!-- Feature 3 -->
                    <div class="feature-card bg-white dark-mode:bg-gray-800 p-6 rounded-xl shadow">
                        <div class="w-12 h-12 bg-indigo-100 dark-mode:bg-indigo-900 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-mobile-alt text-indigo-600 dark-mode:text-indigo-400 text-xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark-mode:text-white mb-2">Mobile Responsive</h3>
                        <p class="text-gray-600 dark-mode:text-gray-300">Fully responsive design that works seamlessly on desktop, tablet, and mobile devices.</p>
                    </div>
                </div>
            </div>

            <!-- CTA Section -->
            <div class="welcome-container p-8 md:p-12 text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 dark-mode:text-white mb-4">Ready to get started?</h2>
                <p class="text-xl text-gray-600 dark-mode:text-gray-300 mb-8 max-w-3xl mx-auto">
                    Join our marketplace today and start exploring the wide range of products and services offered by our trusted vendors.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('login') }}" class="btn-primary px-8 py-3 rounded-lg text-white font-medium flex items-center justify-center">
                        <i class="fas fa-sign-in-alt mr-2"></i> Login
                    </a>
                    <a href="#" class="btn-secondary px-8 py-3 rounded-lg text-white font-medium flex items-center justify-center">
                        <i class="fas fa-info-circle mr-2"></i> Learn More
                    </a>
                </div>
            </div>

            <!-- Footer -->
            <footer class="text-center">
                <p class="text-gray-600 dark-mode:text-gray-400">
                    &copy; {{ date('Y') }} Marketplace App. All rights reserved.
                </p>
                <div class="flex justify-center mt-4 space-x-4">
                    <a href="#" class="text-gray-500 hover:text-indigo-600 dark-mode:hover:text-indigo-400">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="text-gray-500 hover:text-indigo-600 dark-mode:hover:text-indigo-400">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="text-gray-500 hover:text-indigo-600 dark-mode:hover:text-indigo-400">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="text-gray-500 hover:text-indigo-600 dark-mode:hover:text-indigo-400">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                </div>
            </footer>
        </div>

        <script>
            function toggleDarkMode() {
                const body = document.getElementById('body');
                body.classList.toggle('dark-mode');
                
                // Store user preference
                if (body.classList.contains('dark-mode')) {
                    localStorage.setItem('darkMode', 'enabled');
                } else {
                    localStorage.setItem('darkMode', 'disabled');
                }
            }
            
            // Check for saved user preference
            document.addEventListener('DOMContentLoaded', function() {
                const darkModePreference = localStorage.getItem('darkMode');
                if (darkModePreference === 'enabled') {
                    document.getElementById('body').classList.add('dark-mode');
                }
            });
        </script>
    </body>
</html>
