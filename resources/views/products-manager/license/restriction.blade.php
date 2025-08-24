<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.store_access_restricted') }} - Dala3Chic</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .status-pending { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .status-active { background: linear-gradient(135deg, #10b981, #059669); }
        .status-rejected { background: linear-gradient(135deg, #ef4444, #dc2626); }
        .status-expired { background: linear-gradient(135deg, #6b7280, #4b5563); }
        .status-inactive { background: linear-gradient(135deg, #ef4444, #dc2626); }
        .pulse-animation { animation: pulse 2s infinite; }
        .status-container { background: linear-gradient(135deg, #ffffff, #f8fafc); border: 1px solid #e2e8f0; }
        .orange-theme { background: linear-gradient(135deg, #f97316, #ea580c); }
    </style>
</head>
<body class="bg-white/80 min-h-screen">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <img src="{{ asset('assets/logo.png') }}" alt="Dala3Chic Logo" class="mx-auto w-20 h-20 object-contain rounded-2xl mb-6">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    {{ __('messages.products_manager_access') }}
                </h1>
                <p class="text-gray-600">
                    {{ __('messages.store_status_check') }}
                </p>
            </div>

            <!-- Status Card -->
            <div class="status-container rounded-2xl shadow-xl p-8">
                <!-- Current Status -->
                <div class="text-center mb-8">
                    <div class="inline-block p-6 rounded-full status-inactive text-white mb-4">
                        <i class="fas fa-store-slash text-4xl"></i>
                    </div>

                    <h2 class="text-2xl font-bold text-gray-900 mb-2">
                        {{ __('messages.store_status_not_active') }}
                    </h2>

                    <p class="text-gray-600 mb-4">
                        {{ session('license_message', __('messages.store_inactive_desc')) }}
                    </p>

                    @if(session('license_status'))
                        <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-4">
                            <div class="flex items-center">
                                <i class="fas fa-info-circle text-orange-500 mr-2"></i>
                                <span class="text-orange-800 font-medium">
                                    {{ __('messages.vendor_license_status') }}: 
                                    <span class="capitalize">{{ session('license_status') }}</span>
                                </span>
                            </div>
                        </div>
                    @endif

                    <a href="{{ route('products-manager.dashboard') }}"
                       class="inline-flex items-center px-6 py-3 orange-theme text-white font-semibold rounded-lg hover:from-orange-600 hover:to-orange-800 transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-arrow-left mr-2"></i>
                        {{ __('messages.back_to_dashboard') }}
                    </a>
                </div>

                <!-- Information Section -->
                <div class="bg-orange-50 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-exclamation-triangle text-orange-500 mr-2"></i>
                        {{ __('messages.access_restriction_info') }}
                    </h3>
                    <div class="space-y-3 text-gray-700">
                        <p class="flex items-start">
                            <i class="fas fa-circle text-orange-400 text-xs mt-2 mr-3"></i>
                            {{ __('messages.product_creation_restricted') }}
                        </p>
                        <p class="flex items-start">
                            <i class="fas fa-circle text-orange-400 text-xs mt-2 mr-3"></i>
                            {{ __('messages.vendor_license_required') }}
                        </p>
                        <p class="flex items-start">
                            <i class="fas fa-circle text-orange-400 text-xs mt-2 mr-3"></i>
                            {{ __('messages.contact_vendor_admin') }}
                        </p>
                    </div>
                </div>

                <!-- Contact Support -->
                <div class="bg-blue-50 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('messages.need_help') }}</h3>
                    <p class="text-gray-600 mb-4">
                        {{ __('messages.products_manager_support_message') }}
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="mailto:support@dala3chic.com" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                            <i class="fas fa-envelope mr-2"></i>
                            {{ __('messages.email_support') }}
                        </a>
                        <a href="tel:+971501234567" 
                           class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                            <i class="fas fa-phone mr-2"></i>
                            {{ __('messages.call_support') }}
                        </a>
                    </div>
                </div>

                <!-- Logout -->
                <div class="mt-6 text-center">
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit"
                                class="text-gray-500 hover:text-gray-700 text-sm transition-colors duration-200">
                            <i class="fas fa-sign-out-alt mr-1"></i>
                            {{ __('messages.logout') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
