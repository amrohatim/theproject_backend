<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>License Status - Provider Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .status-pending {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }
        .status-approved {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        .status-rejected {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }
        .status-expired {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
        }
        .pulse-animation {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <div class="mx-auto h-16 w-16 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-certificate text-blue-600 text-2xl"></i>
                </div>
                <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                    License Status
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Current status of your professional license
                </p>
            </div>

            <!-- Status Card -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                @php
                    $license = $user->latestLicense;
                    $licenseStatus = $license ? $license->status : null;
                @endphp

                <!-- Current Status -->
                <div class="text-center mb-8">
                    <div class="inline-block p-6 rounded-full {{ 
                        $licenseStatus === 'pending' ? 'status-pending' : 
                        ($licenseStatus === 'active' ? 'status-approved' : 
                        ($licenseStatus === 'expired' ? 'status-expired' : 'status-rejected')) 
                    }} text-white mb-4 {{ $licenseStatus === 'pending' ? 'pulse-animation' : '' }}">
                        @if($licenseStatus === 'pending')
                            <i class="fas fa-clock text-4xl"></i>
                        @elseif($licenseStatus === 'active')
                            <i class="fas fa-check-circle text-4xl"></i>
                        @elseif($licenseStatus === 'expired')
                            <i class="fas fa-calendar-times text-4xl"></i>
                        @else
                            <i class="fas fa-times-circle text-4xl"></i>
                        @endif
                    </div>

                    <h2 class="text-2xl font-bold text-gray-900 mb-2">
                        @if($licenseStatus === 'pending')
                            License Under Review
                        @elseif($licenseStatus === 'active')
                            License Active
                        @elseif($licenseStatus === 'expired')
                            License Expired
                        @else
                            License Rejected
                        @endif
                    </h2>

                    <p class="text-gray-600 mb-4">
                        @if($licenseStatus === 'pending')
                            Your license is currently being reviewed by our team. This process typically takes 1-3 business days.
                        @elseif($licenseStatus === 'active')
                            Your license is active and valid. You can access the provider dashboard.
                        @elseif($licenseStatus === 'expired')
                            Your license has expired. Please upload a renewed license to continue accessing the provider area.
                        @else
                            Your license application was rejected. Please contact support or upload updated documentation.
                        @endif
                    </p>

                    @if($licenseStatus === 'active')
                        <a href="{{ route('provider.dashboard') }}" 
                           class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <i class="fas fa-tachometer-alt mr-2"></i>
                            Go to Dashboard
                        </a>
                    @elseif($licenseStatus === 'expired' || $licenseStatus === 'rejected')
                        <a href="{{ route('provider.license.upload') }}" 
                           class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-upload mr-2"></i>
                            Upload New License
                        </a>
                    @endif
                </div>

                @if($license)
                <!-- License Details -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">License Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Upload Date</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $license->created_at->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Expiry Date</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $license->end_date->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">License Type</label>
                            <p class="mt-1 text-sm text-gray-900">{{ ucfirst($license->license_type) }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">File Name</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $license->license_file_name }}</p>
                        </div>
                        @if($license->notes)
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500">Notes</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $license->notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Contact Support -->
                <div class="border-t border-gray-200 pt-6 mt-6">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-question-circle text-gray-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-gray-800">
                                    Need Help?
                                </h3>
                                <div class="mt-2 text-sm text-gray-600">
                                    <p>If you have questions about your license status or need assistance, please contact our support team.</p>
                                    <div class="mt-3">
                                        <a href="mailto:support@example.com" class="text-blue-600 hover:text-blue-500">
                                            <i class="fas fa-envelope mr-1"></i>
                                            support@example.com
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Logout -->
                <div class="mt-6 text-center">
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit"
                                class="text-gray-500 hover:text-gray-700 text-sm transition-colors duration-200">
                            <i class="fas fa-sign-out-alt mr-1"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-refresh page every 30 seconds if status is pending
        @if($licenseStatus === 'pending')
            setTimeout(() => {
                location.reload();
            }, 30000);
        @endif
    </script>
</body>
</html>
