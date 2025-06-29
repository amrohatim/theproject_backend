<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login to your Dala3Chic account - Access your dashboard, orders, and more">
    <meta name="robots" content="noindex, nofollow">
    
    <title>Login - Dala3Chic</title>
    
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
    @vite(['resources/css/app.css', 'resources/css/animations.css', 'resources/css/modern-auth.css'])
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="auth-container">
        <!-- Left Side - Branding -->
        <div class="auth-branding">
            <div class="scroll-reveal animate-fade-in-left">
                
                    <img src="{{ asset('assets/logo.png') }}" alt="Dala3Chic Logo" style="width: 150px; height: 150px;  object-fit: contain; border-radius: 14px;">
               
                
                <h1 class="auth-brand-title">
                    Welcome Back to
                    <span class="bg-gradient-to-r from-pink-600 via-violet-400 to-pink-600 bg-clip-text text-white/40 font-bold">
                        Dala3Chic
                    </span>
                </h1>
                
                <p class="auth-brand-subtitle">
                    Access your account to manage orders, track shipments, and discover amazing products 
                    from trusted vendors worldwide.
                </p>
                
                <div class="auth-features">
                    <div class="auth-feature animate-fade-in-up animate-delay-200">
                        <i class="fas fa-shield-alt"></i>
                        <span>Secure & Protected</span>
                    </div>
                    <div class="auth-feature animate-fade-in-up animate-delay-300">
                        <i class="fas fa-bolt"></i>
                        <span>Lightning Fast</span>
                    </div>
                    <div class="auth-feature animate-fade-in-up animate-delay-400">
                        <i class="fas fa-heart"></i>
                        <span>Loved by Thousands</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Side - Login Form -->
        <div class="auth-form-container">
            <div class="auth-form-card scroll-reveal animate-fade-in-right">
                <div class="auth-form-header">
                    <h2 class="auth-form-title">Sign In</h2>
                    <p class="auth-form-subtitle">Enter your credentials to access your account</p>
                </div>
                
                <!-- Global errors removed - using individual field errors for stable layout -->
                
                <!-- Display Success Messages -->
                @if (session('success'))
                    <div class="auth-success-message mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <i class="fas fa-check-circle"></i>
                        <span class="ml-2 text-sm">{{ session('success') }}</span>
                    </div>
                @endif
                
                <form class="auth-form" action="{{ route('login.attempt') }}" method="POST" novalidate>
                    @csrf
                    
                    <!-- Email Field -->
                    <div class="auth-form-group">
                        <label for="email" class="auth-form-label">Email Address</label>
                        <div class="auth-input-group">
                            <i class="auth-input-icon fas fa-envelope"></i>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="auth-form-input @error('email') error @enderror"
                                placeholder="Enter your email address"
                                value="{{ old('email') }}"
                                required
                                autocomplete="email"
                                autofocus
                            >
                        </div>
                        <!-- Fixed height error container to prevent layout shifts -->
                        <div class="auth-error-container">
                            @error('email')
                                <div class="auth-error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div class="auth-form-group">
                        <label for="password" class="auth-form-label">Password</label>
                        <div class="auth-input-group">
                            <i class="auth-input-icon fas fa-lock"></i>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="auth-form-input @error('password') error @enderror"
                                placeholder="Enter your password"
                                required
                                autocomplete="current-password"
                            >
                            <button type="button" class="auth-password-toggle" tabindex="-1">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <!-- Fixed height error container to prevent layout shifts -->
                        <div class="auth-error-container">
                            @error('password')
                                <div class="auth-error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <div class="auth-checkbox-group">
                            <input 
                                type="checkbox" 
                                id="remember" 
                                name="remember" 
                                class="auth-checkbox"
                                {{ old('remember') ? 'checked' : '' }}
                            >
                            <label for="remember" class="auth-checkbox-label">Remember me</label>
                        </div>
                        
                        <a href="#" class="auth-link text-sm">Forgot password?</a>
                    </div>
                    
                    <!-- Submit Button -->
                    <button type="submit" class="auth-submit-btn w-full">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Sign In
                    </button>
                </form>
                
                <!-- Divider -->
                <!-- <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">Or continue with</span>
                    </div>
                </div> -->
                
                <!-- Social Login Buttons -->
                <!-- <div class="grid grid-cols-2 gap-3">
                    <button type="button" class="flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-700 hover:bg-gray-50 transition-colors">
                        <i class="fab fa-google mr-2 text-red-500"></i>
                        Google
                    </button>
                    <button type="button" class="flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-700 hover:bg-gray-50 transition-colors">
                        <i class="fab fa-facebook-f mr-2 text-blue-600"></i>
                        Facebook
                    </button>
                </div> -->
                
                <!-- Register Link -->
                <div class="auth-form-footer">
                    <p class="text-sm text-gray-600">
                        Don't have an account?
                        <a href="{{ route('register') }}" class="auth-link">Create one now</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Back to Home Link -->
    <div class="fixed top-4 left-4 z-50">
        <a href="{{ url('/') }}" class="flex items-center gap-2 text-white/80 hover:text-white transition-colors">
            <i class="fas fa-arrow-left"></i>
            <span class="hidden sm:inline">Back to Home</span>
        </a>
    </div>
    
    <!-- Scripts -->
    @vite(['resources/js/app.js', 'resources/js/modern-interactions.js'])
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Unified form validation system
            const form = document.querySelector('.auth-form');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const submitBtn = document.querySelector('.auth-submit-btn');

            // Password toggle functionality
            const passwordToggle = document.querySelector('.auth-password-toggle');
            if (passwordToggle) {
                passwordToggle.addEventListener('click', function() {
                    const passwordField = document.getElementById('password');
                    const icon = this.querySelector('i');

                    if (passwordField.type === 'password') {
                        passwordField.type = 'text';
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    } else {
                        passwordField.type = 'password';
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                });
            }

            // Form submission with loading state
            form.addEventListener('submit', function(e) {
                // Show loading state
                showLoadingState(submitBtn);
            });

            function showLoadingState(button) {
                button.disabled = true;
                button.innerHTML = `
                    <span class="auth-loading">
                        <span class="auth-spinner"></span>
                        Signing In...
                    </span>
                `;
            }

            // Auto-hide flash messages
            setTimeout(() => {
                const flashMessages = document.querySelectorAll('.auth-error-message, .auth-success-message');
                flashMessages.forEach(message => {
                    if (message.parentElement.classList.contains('mb-4')) {
                        message.style.transition = 'opacity 0.5s ease';
                        message.style.opacity = '0';
                        setTimeout(() => message.remove(), 500);
                    }
                });
            }, 5000);
        });
    </script>
    
    <!-- Additional Styles -->
    <style>
        /* Custom checkbox styling */
        .auth-checkbox {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
        }
        
        /* Loading spinner animation */
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .auth-spinner {
            animation: spin 1s linear infinite;
        }
        
        /* Enhanced focus states - removed transform to prevent layout shifts */
        .auth-form-input:focus {
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1), 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        /* Social button hover effects */
        .grid button:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        /* Mobile responsiveness */
        @media (max-width: 640px) {
            .auth-form-card {
                margin: 1rem;
                padding: 2rem 1.5rem;
            }
            
            .auth-brand-title {
                font-size: 1.75rem;
            }
            
            .auth-features {
                margin-top: 2rem;
            }
        }
    </style>
</body>
</html>
