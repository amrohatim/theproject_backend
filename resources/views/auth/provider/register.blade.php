<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Provider Registration - Dala3Chic</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/favicon.ico') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom Styles -->
    @vite(['resources/css/app.css', 'resources/css/animations.css', 'resources/css/modern-auth.css'])

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        .form-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .input-group {
            position: relative;
        }
        .input-group input:focus + label,
        .input-group input:not(:placeholder-shown) + label {
            transform: translateY(-1.5rem) scale(0.85);
            color: #ec4899;
        }
        .input-group label {
            position: absolute;
            left: 1rem;
            top: 1rem;
            transition: all 0.3s ease;
            pointer-events: none;
            color: #6b7280;
        }
        .step-indicator {
            background: linear-gradient(135deg, #ec4899 0%, #be185d 100%);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-pink-50 via-purple-50 to-rose-50 min-h-screen">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <img src="{{ asset('assets/logo.png') }}" alt="Dala3Chic Logo" class="mx-auto w-20 h-20 object-contain rounded-2xl mb-6">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    Provider Registration
                </h1>
                <p class="text-gray-600">
                    Step 1 of 4: Service Provider Information
                </p>

                <!-- Progress Bar -->
                <div class="w-full bg-gray-200 rounded-full h-2 mt-4">
                    <div class="step-indicator h-2 rounded-full" style="width: 25%"></div>
                </div>
            </div>

            <!-- Registration Form -->
            <div class="form-container rounded-2xl shadow-xl p-8">
                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">
                                    Please correct the following errors:
                                </h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc list-inside space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('register.provider.submit') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- Name Field -->
                    <div class="input-group">
                        <input
                            id="name"
                            name="name"
                            type="text"
                            required
                            placeholder=" "
                            value="{{ old('name') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all duration-300"
                        >
                        <label for="name" class="text-gray-500">Full Name *</label>
                    </div>

                    <!-- Email Field -->
                    <div class="input-group">
                        <input
                            id="email"
                            name="email"
                            type="email"
                            required
                            placeholder=" "
                            value="{{ old('email') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all duration-300"
                        >
                        <label for="email" class="text-gray-500">Email Address *</label>
                    </div>

                    <!-- Phone Field -->
                    <div class="input-group">
                        <input
                            id="phone"
                            name="phone"
                            type="tel"
                            required
                            placeholder=" "
                            value="{{ old('phone') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all duration-300"
                        >
                        <label for="phone" class="text-gray-500">Phone Number *</label>
                    </div>

                    <!-- Password Field -->
                    <div class="input-group">
                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            placeholder=" "
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all duration-300"
                        >
                        <label for="password" class="text-gray-500">Password *</label>
                    </div>

                    <!-- Confirm Password Field -->
                    <div class="input-group">
                        <input
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            required
                            placeholder=" "
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all duration-300"
                        >
                        <label for="password_confirmation" class="text-gray-500">Confirm Password *</label>
                    </div>

                    <!-- Business Name Field -->
                    <div class="input-group">
                        <input
                            id="business_name"
                            name="business_name"
                            type="text"
                            placeholder=" "
                            value="{{ old('business_name') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all duration-300"
                        >
                        <label for="business_name" class="text-gray-500">Business Name (Optional)</label>
                    </div>

                    <!-- Description Field -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Service Description (Optional)
                        </label>
                        <textarea
                            id="description"
                            name="description"
                            rows="3"
                            placeholder="Describe your services..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all duration-300"
                        >{{ old('description') }}</textarea>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        class="w-full bg-gradient-to-r from-pink-600 to-rose-600 text-white py-3 px-6 rounded-lg font-semibold hover:from-pink-700 hover:to-rose-700 transition-all duration-300 transform hover:scale-105"
                    >
                        Continue to Email Verification
                        <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </form>

                <!-- Back to Choice -->
                <div class="text-center mt-6">
                    <a href="{{ route('register') }}" class="text-gray-600 hover:text-pink-600 transition-colors duration-300">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to registration choice
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>