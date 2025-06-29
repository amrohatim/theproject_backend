<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Vendor Company Information - Dala3Chic Registration">
    <meta name="robots" content="noindex, nofollow">

    <title>Company Information - Vendor Registration - Dala3Chic</title>

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

    <style>
        .form-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .step-indicator {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-purple-50 via-pink-50 to-indigo-50 min-h-screen">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <img src="{{ asset('assets/logo.png') }}" alt="Dala3Chic Logo" class="mx-auto w-20 h-20 object-contain rounded-2xl mb-6">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    Company Information
                </h1>
                <p class="text-gray-600">
                    Step 2 of 3: Tell us about your business
                </p>

                <!-- Progress Bar -->
                <div class="w-full bg-gray-200 rounded-full h-2 mt-4">
                    <div class="step-indicator h-2 rounded-full" style="width: 66%"></div>
                </div>
            </div>

            <!-- Company Form -->
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

                <form method="POST" action="{{ route('vendor.registration.company.submit') }}" class="space-y-6">
                    @csrf

                    <!-- Company Name -->
                    <div>
                        <label for="company_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Company Name *
                        </label>
                        <input
                            id="company_name"
                            name="company_name"
                            type="text"
                            required
                            value="{{ old('company_name') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300"
                            placeholder="Enter your company name"
                        >
                    </div>

                    <!-- Business Type -->
                    <div>
                        <label for="business_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Business Type
                        </label>
                        <select
                            id="business_type"
                            name="business_type"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300"
                        >
                            <option value="">Select business type</option>
                            <option value="retail" {{ old('business_type') === 'retail' ? 'selected' : '' }}>Retail</option>
                            <option value="wholesale" {{ old('business_type') === 'wholesale' ? 'selected' : '' }}>Wholesale</option>
                            <option value="manufacturing" {{ old('business_type') === 'manufacturing' ? 'selected' : '' }}>Manufacturing</option>
                            <option value="services" {{ old('business_type') === 'services' ? 'selected' : '' }}>Services</option>
                            <option value="other" {{ old('business_type') === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <!-- Company Email -->
                    <div>
                        <label for="company_email" class="block text-sm font-medium text-gray-700 mb-2">
                            Company Email *
                        </label>
                        <input
                            id="company_email"
                            name="company_email"
                            type="email"
                            required
                            value="{{ old('company_email') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300"
                            placeholder="company@example.com"
                        >
                    </div>

                    <!-- Contact Numbers -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="contact_number_1" class="block text-sm font-medium text-gray-700 mb-2">
                                Primary Contact *
                            </label>
                            <input
                                id="contact_number_1"
                                name="contact_number_1"
                                type="tel"
                                required
                                value="{{ old('contact_number_1') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300"
                                placeholder="+971 50 123 4567"
                            >
                        </div>
                        <div>
                            <label for="contact_number_2" class="block text-sm font-medium text-gray-700 mb-2">
                                Secondary Contact
                            </label>
                            <input
                                id="contact_number_2"
                                name="contact_number_2"
                                type="tel"
                                value="{{ old('contact_number_2') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300"
                                placeholder="+971 50 123 4567"
                            >
                        </div>
                    </div>

                    <!-- Address -->
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                            Business Address *
                        </label>
                        <textarea
                            id="address"
                            name="address"
                            rows="3"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300"
                            placeholder="Enter your complete business address"
                        >{{ old('address') }}</textarea>
                    </div>

                    <!-- City and Emirate -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                                City *
                            </label>
                            <input
                                id="city"
                                name="city"
                                type="text"
                                required
                                value="{{ old('city') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300"
                                placeholder="Dubai"
                            >
                        </div>
                        <div>
                            <label for="emirate" class="block text-sm font-medium text-gray-700 mb-2">
                                Emirate *
                            </label>
                            <select
                                id="emirate"
                                name="emirate"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300"
                            >
                                <option value="">Select Emirate</option>
                                <option value="Abu Dhabi" {{ old('emirate') === 'Abu Dhabi' ? 'selected' : '' }}>Abu Dhabi</option>
                                <option value="Dubai" {{ old('emirate') === 'Dubai' ? 'selected' : '' }}>Dubai</option>
                                <option value="Sharjah" {{ old('emirate') === 'Sharjah' ? 'selected' : '' }}>Sharjah</option>
                                <option value="Ajman" {{ old('emirate') === 'Ajman' ? 'selected' : '' }}>Ajman</option>
                                <option value="Umm Al Quwain" {{ old('emirate') === 'Umm Al Quwain' ? 'selected' : '' }}>Umm Al Quwain</option>
                                <option value="Ras Al Khaimah" {{ old('emirate') === 'Ras Al Khaimah' ? 'selected' : '' }}>Ras Al Khaimah</option>
                                <option value="Fujairah" {{ old('emirate') === 'Fujairah' ? 'selected' : '' }}>Fujairah</option>
                            </select>
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Business Description
                        </label>
                        <textarea
                            id="description"
                            name="description"
                            rows="4"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300"
                            placeholder="Tell us about your business, products, and services..."
                        >{{ old('description') }}</textarea>
                    </div>

                    <!-- Delivery Capability -->
                    <div class="flex items-center">
                        <input
                            id="delivery_capability"
                            name="delivery_capability"
                            type="checkbox"
                            value="1"
                            {{ old('delivery_capability') ? 'checked' : '' }}
                            class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                        >
                        <label for="delivery_capability" class="ml-2 block text-sm text-gray-900">
                            We offer delivery services to customers
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 text-white py-3 px-6 rounded-lg font-semibold hover:from-purple-700 hover:to-indigo-700 transition-all duration-300 transform hover:scale-105"
                    >
                        Continue to License Upload
                        <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </form>

                <!-- Back Link -->
                <div class="text-center mt-4">
                    <a href="{{ route('register.vendor') }}" class="text-purple-600 hover:text-purple-700 text-sm transition-colors duration-300">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back to Personal Information
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>