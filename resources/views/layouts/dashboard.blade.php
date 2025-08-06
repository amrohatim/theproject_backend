<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Dala3Chic Admin</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Modern Forms CSS -->
    <link rel="stylesheet" href="{{ asset('css/modern-forms.css') }}">
    <!-- Modern Buttons CSS -->
    <link rel="stylesheet" href="{{ asset('css/modern-buttons.css') }}">
    <!-- Global Styles -->
    <link rel="stylesheet" href="{{ asset('css/global-styles.css') }}">
    <!-- Custom styles -->
    <style>
        .sidebar-active {
            background-color: #4f46e5;
            color: white;
        }
        .sidebar-item:hover:not(.sidebar-active) {
            background-color: #f3f4f6;
        }
        .dark .sidebar-item:hover:not(.sidebar-active) {
            background-color: #374151;
        }
    </style>
    @yield('styles')
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transform -translate-x-full transition-transform duration-300 ease-in-out md:translate-x-0 md:static md:inset-0">
            <div class="flex flex-col h-full">
                <!-- Sidebar header -->
                <div class="flex items-center justify-center h-16 px-4 border-b border-gray-200 dark:border-gray-700">
                    <h1 class="text-xl font-bold text-indigo-600 dark:text-indigo-400">Dala3Chic</h1>
                </div>

                <!-- Sidebar content -->
                <div class="flex flex-col flex-grow overflow-y-auto">
                    <div class="flex-grow px-4 py-2">
                        <div class="py-4">
                            <div class="flex items-center mb-4">
                                <div class="w-10 h-10 rounded-full bg-indigo-500 flex items-center justify-center text-white">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium">{{ auth()->user()->name ?? 'Admin User' }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ auth()->user()->email ?? 'admin@example.com' }}</p>
                                </div>
                            </div>

                            @if(auth()->user() && auth()->user()->role === 'admin')
                                @include('layouts.admin-sidebar')
                            @elseif(auth()->user() && auth()->user()->role === 'vendor')
                                @include('layouts.vendor-sidebar')
                            @else
                                <!-- Default sidebar for other roles or guests -->
                                <nav class="mt-5 space-y-1">
                                    <a href="/" class="sidebar-item flex items-center px-4 py-2 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300">
                                        <i class="fas fa-home mr-3"></i>
                                        Home
                                    </a>
                                </nav>
                            @endif
                        </div>
                    </div>

                    <!-- Sidebar footer -->
                    <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center w-full px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md">
                                <i class="fas fa-sign-out-alt mr-3"></i>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar overlay for mobile -->
        <div id="sidebar-overlay" class="fixed inset-0 z-40 bg-black bg-opacity-50 hidden md:hidden"></div>

        <!-- Main content -->
        <div class="flex flex-col flex-1 overflow-hidden">
            <!-- Top navbar -->
            <div class="flex items-center justify-between h-16 px-4 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <!-- Mobile menu button -->
                <button type="button" id="mobile-menu-toggle" class="md:hidden text-gray-500 dark:text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-bars"></i>
                </button>

                <!-- Page title -->
                <h1 class="text-lg font-semibold">@yield('page-title', 'Dashboard')</h1>

                <!-- User dropdown -->
                <div class="relative">
                    <button type="button" class="flex items-center text-sm focus:outline-none">
                        <span class="mr-2 text-gray-700 dark:text-gray-300">{{ auth()->user()->name ?? 'Admin User' }}</span>
                        <div class="w-8 h-8 rounded-full bg-indigo-500 flex items-center justify-center text-white">
                            <i class="fas fa-user"></i>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Page content -->
            <div class="flex-1 overflow-auto p-4">
                <!-- Flash Messages -->
                @if(session('success'))
                <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-md" role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm">{{ session('success') }}</p>
                        </div>
                        <div class="ml-auto pl-3">
                            <div class="-mx-1.5 -my-1.5">
                                <button type="button" class="inline-flex rounded-md p-1.5 text-green-500 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" onclick="this.parentElement.parentElement.parentElement.parentElement.remove()">
                                    <span class="sr-only">Dismiss</span>
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if(session('error'))
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-md" role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm">{{ session('error') }}</p>
                        </div>
                        <div class="ml-auto pl-3">
                            <div class="-mx-1.5 -my-1.5">
                                <button type="button" class="inline-flex rounded-md p-1.5 text-red-500 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" onclick="this.parentElement.parentElement.parentElement.parentElement.remove()">
                                    <span class="sr-only">Dismiss</span>
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if(session('warning'))
                <div class="mb-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded shadow-md" role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm">{{ session('warning') }}</p>
                        </div>
                        <div class="ml-auto pl-3">
                            <div class="-mx-1.5 -my-1.5">
                                <button type="button" class="inline-flex rounded-md p-1.5 text-yellow-500 hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500" onclick="this.parentElement.parentElement.parentElement.parentElement.remove()">
                                    <span class="sr-only">Dismiss</span>
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if(session('info'))
                <div class="mb-4 bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 rounded shadow-md" role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm">{{ session('info') }}</p>
                        </div>
                        <div class="ml-auto pl-3">
                            <div class="-mx-1.5 -my-1.5">
                                <button type="button" class="inline-flex rounded-md p-1.5 text-blue-500 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" onclick="this.parentElement.parentElement.parentElement.parentElement.remove()">
                                    <span class="sr-only">Dismiss</span>
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Modern Forms JS -->
    <script src="{{ asset('js/modern-forms.js') }}"></script>
    <!-- Textarea Enhancements -->
    <script src="{{ asset('js/textarea-enhancements.js') }}"></script>
    <!-- Input Enhancements -->
    <script src="{{ asset('js/input-enhancements.js') }}"></script>
    <script>
        // Toggle dark mode
        function toggleDarkMode() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.theme = 'light';
            } else {
                document.documentElement.classList.add('dark');
                localStorage.theme = 'dark';
            }
        }

        // Check for dark mode preference
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        // Mobile sidebar functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebar-overlay');
            const mobileMenuToggle = document.getElementById('mobile-menu-toggle');

            // Toggle sidebar on mobile
            if (mobileMenuToggle) {
                mobileMenuToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('-translate-x-full');
                    sidebarOverlay.classList.toggle('hidden');
                    document.body.classList.toggle('overflow-hidden');
                });
            }

            // Close sidebar when clicking overlay
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    sidebar.classList.add('-translate-x-full');
                    sidebarOverlay.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                });
            }

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 768) {
                    sidebar.classList.remove('-translate-x-full');
                    sidebarOverlay.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                }
            });
        });
    </script>
    @yield('scripts')
    @stack('scripts')
</body>
</html>
