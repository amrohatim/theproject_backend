<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.vendor_license_status') }} - Dala3Chic</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .status-pending { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .status-active { background: linear-gradient(135deg, #10b981, #059669); }
        .status-rejected { background: linear-gradient(135deg, #ef4444, #dc2626); }
        .status-expired { background: linear-gradient(135deg, #6b7280, #4b5563); }
        .pulse-animation { animation: pulse 2s infinite; }
        .status-container { background: linear-gradient(135deg, #ffffff, #f8fafc); border: 1px solid #e2e8f0; }
    </style>
</head>
<body class="bg-gradient-to-br from-purple-50 via-pink-50 to-indigo-50 min-h-screen">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <img src="{{ asset('assets/logo.png') }}" alt="Dala3Chic Logo" class="mx-auto w-20 h-20 object-contain rounded-2xl mb-6">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    {{ __('messages.vendor_license_status') }}
                </h1>
                <p class="text-gray-600">
                    {{ __('messages.track_license_verification_progress') }}
                </p>
            </div>

            <!-- Status Card -->
            <div class="status-container rounded-2xl shadow-xl p-8">
                <!-- Current Status -->
                <div class="text-center mb-8">
                    <div class="inline-block p-6 rounded-full {{ $license_status === 'pending' ? 'status-pending' : ($license_status === 'active' ? 'status-active' : ($license_status === 'expired' ? 'status-expired' : 'status-rejected')) }} text-white mb-4 {{ $license_status === 'pending' ? 'pulse-animation' : '' }}">
                        @if($license_status === 'pending')
                            <i class="fas fa-clock text-4xl"></i>
                        @elseif($license_status === 'active')
                            <i class="fas fa-check-circle text-4xl"></i>
                        @elseif($license_status === 'expired')
                            <i class="fas fa-calendar-times text-4xl"></i>
                        @else
                            <i class="fas fa-times-circle text-4xl"></i>
                        @endif
                    </div>

                    <h2 class="text-2xl font-bold text-gray-900 mb-2">
                        @if($license_status === 'pending')
                            {{ __('messages.license_under_review') }}
                        @elseif($license_status === 'active')
                            {{ __('messages.license_active') }}!
                        @elseif($license_status === 'expired')
                            {{ __('messages.license_expired') }}
                        @else
                            {{ __('messages.license_rejected') }}
                        @endif
                    </h2>

                    <p class="text-gray-600 mb-4">
                        @if($license_status === 'pending')
                            {{ __('messages.license_pending_desc') }}
                        @elseif($license_status === 'active')
                            {{ __('messages.license_active_desc') }}
                        @elseif($license_status === 'expired')
                            {{ __('messages.license_expired_desc') }}
                        @else
                            {{ __('messages.license_rejected_desc') }}
                        @endif
                    </p>

                    @if($license_status === 'active')
                        <a href="{{ route('vendor.dashboard') }}"
                           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-lg hover:from-purple-700 hover:to-indigo-700 transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-tachometer-alt mr-2"></i>
                            {{ __('messages.go_to_dashboard') }}
                        </a>
                    @elseif($can_upload_new)
                        <a href="{{ route('vendor.license.upload') }}"
                           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-700 text-white font-semibold rounded-lg hover:from-blue-600 hover:to-blue-800 transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-upload mr-2"></i>
                            {{ __('messages.upload_new_license') }}
                        </a>
                    @endif
                </div>

                <!-- License Information -->
                @if($license)
                <div class="bg-gray-50 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('messages.license_information') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('messages.license_type') }}</label>
                            <p class="text-gray-900">{{ $license->license_type ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('messages.status') }}</label>
                            <p class="text-gray-900 capitalize">{{ $license->status }}</p>
                        </div>
                        @if($license->start_date)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('messages.start_date') }}</label>
                            <p class="text-gray-900">{{ $license->start_date->format('d-m-Y') }}</p>
                        </div>
                        @endif
                        @if($license->end_date)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('messages.end_date') }}</label>
                            <p class="text-gray-900">{{ $license->end_date->format('d-m-Y') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Contact Support -->
                <div class="bg-blue-50 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('messages.need_help') }}?</h3>
                <p class="text-gray-600 mb-4">
                    {{ __('messages.license_support_message') }}
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

    <script>
        // Auto-refresh page every 30 seconds if status is pending
        @if($license_status === 'pending')
            setTimeout(() => {
                location.reload();
            }, 30000);
        @endif
    </script>
</body>
</html>
