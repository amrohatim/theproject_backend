<!DOCTYPE html>
<html lang="{{ $currentLocale }}" dir="{{ $direction }}" {!! $htmlAttributes !!}>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'Laravel') }} - @yield('title', __('messages.welcome'))</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- RTL CSS for Arabic -->
    @if($isRtl)
        <link href="{{ asset('css/rtl.css') }}" rel="stylesheet">
    @endif
    
    <!-- Custom CSS -->
    @stack('styles')
    
    <style>
        body {
            font-family: 'Figtree', 'Segoe UI', Tahoma, Arial, sans-serif;
            background-color: #f8f9fa;
        }
        
        .navbar-brand {
            font-weight: 600;
        }
        
        .main-content {
            min-height: calc(100vh - 120px);
            padding: 2rem 0;
        }
        
        .footer {
            background-color: #343a40;
            color: white;
            padding: 1rem 0;
            margin-top: auto;
        }
        
        /* Language-specific styles */
        @if($isRtl)
            .navbar-nav {
                flex-direction: row-reverse;
            }
            
            .dropdown-menu {
                right: 0;
                left: auto;
            }
        @endif
    </style>
</head>
<body class="{{ $bodyClasses }}">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                {{ config('app.name', 'Laravel') }}
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/') }}">@lang('messages.home')</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">@lang('messages.about')</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">@lang('messages.services')</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">@lang('messages.contact')</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <!-- Language Switcher -->
                    <li class="nav-item">
                        <x-language-switcher />
                    </li>
                    
                    <!-- User Menu -->
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i>
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">@lang('messages.profile')</a></li>
                                <li><a class="dropdown-item" href="#">@lang('messages.settings')</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item">@lang('messages.logout')</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">@lang('messages.login')</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('language_switched'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    @lang('messages.language_switched_to') {{ $currentLanguage['native'] }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            <!-- Page Content -->
            @yield('content')
        </div>
    </main>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">&copy; @arabicNumbers(date('Y')) {{ config('app.name') }}. @lang('messages.all_rights_reserved')</p>
                </div>
                <div class="col-md-6 text-{{ $textAlign === 'right' ? 'start' : 'end' }}">
                    <p class="mb-0">
                        @lang('messages.current_language'): {{ $currentLanguage['native'] }}
                        @if($isRtl)
                            <i class="fas fa-align-right ms-2"></i>
                        @else
                            <i class="fas fa-align-left ms-2"></i>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    @stack('scripts')
    
    <script>
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
        
        // RTL/LTR specific JavaScript adjustments
        @if($isRtl)
            // Add any RTL-specific JavaScript here
            console.log('RTL mode active');
        @else
            // Add any LTR-specific JavaScript here
            console.log('LTR mode active');
        @endif
    </script>
</body>
</html>