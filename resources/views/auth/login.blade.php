<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('messages.login') }} - {{ __('messages.marketplace_admin') }}</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Modern Forms CSS -->
    <link rel="stylesheet" href="{{ asset('css/modern-forms.css') }}">
    <!-- Global Styles -->
    <link rel="stylesheet" href="{{ asset('css/global-styles.css') }}">
    <style>
        body {
            background-image: url('https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');
            background-size: cover;
            background-position: center;
        }
        .login-container {
            backdrop-filter: blur(10px);
            background-color: rgba(255, 255, 255, 0.8);
        }
        .dark .login-container {
            background-color: rgba(31, 41, 55, 0.8);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="login-container max-w-md w-full space-y-8 p-10 rounded-xl shadow-lg">
        <div>
            <div class="flex justify-center">
                <div class="w-16 h-16 bg-indigo-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-white text-2xl"></i>
                </div>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-white">
                {{ __('messages.marketplace_admin') }}
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-300">
                {{ __('messages.enter_credentials') }}
            </p>
        </div>

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <form class="mt-8 space-y-6" action="{{ route('login.attempt') }}" method="POST">
            @csrf
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="email" class="sr-only">{{ __('messages.email_address') }}</label>
                    <input id="email" name="email" type="email" autocomplete="email" required
                        class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                        placeholder="{{ __('messages.enter_email_address') }}" value="{{ old('email') }}">
                </div>
                <div>
                    <label for="password" class="sr-only">{{ __('messages.password') }}</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required
                        class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                        placeholder="{{ __('messages.enter_password') }}">
                </div>
            </div>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember_me" name="remember" type="checkbox"
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="remember_me" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                        {{ __('messages.remember_me') }}
                    </label>
                </div>

                <div class="text-sm">
                    <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500">
                        {{ __('messages.forgot_password') }}
                    </a>
                </div>
            </div>

            <div>
                <button type="submit"
                    class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-lock"></i>
                    </span>
                    {{ __('messages.sign_in') }}
                </button>
            </div>
        </form>

        <!-- Demo login buttons -->
        <div class="mt-6">
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white dark:bg-gray-800 text-gray-500">Demo Accounts</span>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-3 gap-3">
                <form action="{{ route('login.attempt') }}" method="POST">
                    @csrf
                    <input type="hidden" name="email" value="admin@example.com">
                    <input type="hidden" name="password" value="password123">
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Admin Login
                    </button>
                </form>

                <form action="{{ route('login.attempt') }}" method="POST">
                    @csrf
                    <input type="hidden" name="email" value="vendor@example.com">
                    <input type="hidden" name="password" value="password123">
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Vendor Login
                    </button>
                </form>

                <form action="{{ route('login.attempt') }}" method="POST">
                    @csrf
                    <input type="hidden" name="email" value="merchant@test.com">
                    <input type="hidden" name="password" value="password123">
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Merchant Login
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Modern Forms JS -->
    <script src="{{ asset('js/modern-forms.js') }}"></script>
    <!-- Textarea Enhancements -->
    <script src="{{ asset('js/textarea-enhancements.js') }}"></script>
    <!-- Input Enhancements -->
    <script src="{{ asset('js/input-enhancements.js') }}"></script>
</body>
</html>
