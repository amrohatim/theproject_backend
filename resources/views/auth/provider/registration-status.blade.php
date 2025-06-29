<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Provider Registration Status - Dala3Chic">
    <meta name="robots" content="noindex, nofollow">

    <title>Registration Status - Provider - Dala3Chic</title>

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
        .status-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .status-pending {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        }
        .status-approved {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        .status-rejected {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }
        .pulse-animation {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        .timeline-item {
            position: relative;
            padding-left: 2rem;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: 0.5rem;
            top: 0.5rem;
            width: 0.75rem;
            height: 0.75rem;
            border-radius: 50%;
            background: #e5e7eb;
        }
        .timeline-item.completed::before {
            background: #10b981;
        }
        .timeline-item.current::before {
            background: #3b82f6;
            animation: pulse 2s infinite;
        }
        .timeline-item:not(:last-child)::after {
            content: '';
            position: absolute;
            left: 0.6875rem;
            top: 1.25rem;
            width: 2px;
            height: calc(100% - 1rem);
            background: #e5e7eb;
        }
        .timeline-item.completed:not(:last-child)::after {
            background: #10b981;
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
                    Provider Registration Status
                </h1>
                <p class="text-gray-600">
                    Track your service provider registration progress
                </p>
            </div>

            <!-- Status Card -->
            <div class="status-container rounded-2xl shadow-xl p-8">
                <!-- Current Status -->
                <div class="text-center mb-8">
                    <div class="inline-block p-6 rounded-full {{ $user->registration_status === 'pending' ? 'status-pending' : ($user->registration_status === 'approved' ? 'status-approved' : 'status-rejected') }} text-white mb-4 {{ $user->registration_status === 'pending' ? 'pulse-animation' : '' }}">
                        @if($user->registration_status === 'pending')
                            <i class="fas fa-clock text-4xl"></i>
                        @elseif($user->registration_status === 'approved')
                            <i class="fas fa-check-circle text-4xl"></i>
                        @else
                            <i class="fas fa-times-circle text-4xl"></i>
                        @endif
                    </div>

                    <h2 class="text-2xl font-bold text-gray-900 mb-2">
                        @if($user->registration_status === 'pending')
                            Registration Under Review
                        @elseif($user->registration_status === 'approved')
                            Registration Approved!
                        @else
                            Registration Rejected
                        @endif
                    </h2>

                    <p class="text-gray-600 mb-4">
                        @if($user->registration_status === 'pending')
                            Your service provider registration is currently being reviewed by our team. This process typically takes 1-3 business days.
                        @elseif($user->registration_status === 'approved')
                            Congratulations! Your service provider registration has been approved. You can now access your provider dashboard.
                        @else
                            Unfortunately, your service provider registration has been rejected. Please contact our support team for more information.
                        @endif
                    </p>

                    @if($user->registration_status === 'approved')
                        <a href="{{ route('provider.dashboard') }}"
                           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-pink-600 to-rose-600 text-white font-semibold rounded-lg hover:from-pink-700 hover:to-rose-700 transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-tachometer-alt mr-2"></i>
                            Go to Dashboard
                        </a>
                    @endif
                </div>

                <!-- Registration Timeline -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Registration Progress</h3>

                    <div class="space-y-4">
                        <!-- Step 1: Registration Submitted -->
                        <div class="timeline-item completed">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-medium text-gray-900">Registration Submitted</h4>
                                    <p class="text-sm text-gray-600">Your service provider application has been submitted</p>
                                </div>
                                <span class="text-sm text-gray-500">{{ $user->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>

                        <!-- Step 2: Email Verification -->
                        <div class="timeline-item {{ $user->email_verified_at ? 'completed' : 'current' }}">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-medium text-gray-900">Email Verification</h4>
                                    <p class="text-sm text-gray-600">
                                        @if($user->email_verified_at)
                                            Email address verified successfully
                                        @else
                                            Please verify your email address
                                        @endif
                                    </p>
                                </div>
                                <span class="text-sm text-gray-500">
                                    @if($user->email_verified_at)
                                        {{ $user->email_verified_at->format('M d, Y') }}
                                    @else
                                        <a href="{{ route('provider.email.verify', ['user_id' => $user->id]) }}"
                                           class="text-blue-600 hover:text-blue-700 font-medium">Verify Now</a>
                                    @endif
                                </span>
                            </div>
                        </div>

                        <!-- Step 3: Phone Verification -->
                        <div class="timeline-item {{ $user->phone_verified_at ? 'completed' : ($user->email_verified_at ? 'current' : '') }}">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-medium text-gray-900">Phone Verification</h4>
                                    <p class="text-sm text-gray-600">
                                        @if($user->phone_verified_at)
                                            Phone number verified successfully
                                        @else
                                            Please verify your phone number
                                        @endif
                                    </p>
                                </div>
                                <span class="text-sm text-gray-500">
                                    @if($user->phone_verified_at)
                                        {{ $user->phone_verified_at->format('M d, Y') }}
                                    @elseif($user->email_verified_at)
                                        <a href="{{ route('provider.otp.verify', ['user_id' => $user->id]) }}"
                                           class="text-blue-600 hover:text-blue-700 font-medium">Verify Now</a>
                                    @else
                                        <span class="text-gray-400">Pending email verification</span>
                                    @endif
                                </span>
                            </div>
                        </div>

                        <!-- Step 4: Admin Review -->
                        <div class="timeline-item {{ $user->registration_status === 'approved' ? 'completed' : ($user->email_verified_at && $user->phone_verified_at ? 'current' : '') }}">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-medium text-gray-900">Admin Review</h4>
                                    <p class="text-sm text-gray-600">
                                        @if($user->registration_status === 'approved')
                                            Application approved by admin
                                        @elseif($user->registration_status === 'rejected')
                                            Application rejected by admin
                                        @else
                                            Waiting for admin review
                                        @endif
                                    </p>
                                </div>
                                <span class="text-sm text-gray-500">
                                    @if($user->registration_status !== 'pending')
                                        {{ $user->updated_at->format('M d, Y') }}
                                    @else
                                        Pending
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Support -->
                <div class="mt-8 text-center">
                    <p class="text-gray-600 mb-4">Need help or have questions about your registration?</p>
                    <div class="flex justify-center space-x-4">
                        <a href="mailto:support@dala3chic.com"
                           class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200">
                            <i class="fas fa-envelope mr-2"></i>
                            Email Support
                        </a>
                        <a href="tel:+971-xxx-xxxx"
                           class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200">
                            <i class="fas fa-phone mr-2"></i>
                            Call Support
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
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-refresh page every 30 seconds if status is pending
        @if($user->registration_status === 'pending')
            setTimeout(() => {
                location.reload();
            }, 30000);
        @endif
    </script>
</body>
</html>