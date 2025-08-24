<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phone Verification - Provider Registration</title>

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
        .step-indicator {
            background: linear-gradient(135deg, #ec4899 0%, #be185d 100%);
        }
        .phone-icon {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        .otp-code {
            letter-spacing: 0.5rem;
            font-size: 1.5rem;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-pink-50 via-purple-50 to-rose-50 min-h-screen">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <img src="{{ asset('assets/logo.png') }}" alt="Dala3Chic Logo" class="mx-auto w-20 h-20 object-contain rounded-2xl mb-6">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    Phone Verification
                </h1>
                <p class="text-gray-600">
                    Step 3 of 5: Verify Your Phone Number
                </p>

                <!-- Progress Bar -->
                <div class="w-full bg-gray-200 rounded-full h-2 mt-4">
                    <div class="step-indicator h-2 rounded-full" style="width: 60%"></div>
                </div>
            </div>

            <!-- Verification Form -->
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

                @if (session('success'))
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-green-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-700">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Phone Icon and Message -->
                <div class="text-center mb-8">
                    <div class="phone-icon inline-block p-4 bg-pink-100 rounded-full mb-4">
                        <i class="fas fa-mobile-alt text-4xl text-pink-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Check Your Phone</h3>
                    <p class="text-gray-600 mb-4">
                        We've sent a verification code to:
                    </p>
                    <p class="text-lg font-semibold text-pink-600 mb-4">{{ $user->phone }}</p>
                    <p class="text-sm text-gray-500">
                        Enter the 6-digit code to complete your registration.
                    </p>
                </div>

                <!-- OTP Form -->
                <form method="POST" action="{{ route('provider.otp.verify.submit') }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $user->id }}">

                    <div class="text-center">
                        <label for="otp_code" class="block text-sm font-medium text-gray-700 mb-2">
                            Enter OTP Code
                        </label>
                        <input
                            id="otp_code"
                            name="otp_code"
                            type="text"
                            required
                            maxlength="6"
                            placeholder="000000"
                            class="otp-code w-full text-center px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all duration-300"
                        >
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        class="w-full bg-gradient-to-r from-pink-600 to-rose-600 text-white py-3 px-6 rounded-lg font-semibold hover:from-pink-700 hover:to-rose-700 transition-all duration-300 transform hover:scale-105"
                    >
                        Verify Phone Number
                        <i class="fas fa-check ml-2"></i>
                    </button>
                </form>

                <!-- Resend OTP -->
                <div class="text-center mt-6">
                    <p class="text-sm text-gray-600 mb-2">Didn't receive the code?</p>
                    <button
                        onclick="resendOtp()"
                        class="text-pink-600 hover:text-pink-700 font-medium transition-colors duration-300"
                    >
                        Resend OTP
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-send OTP when page loads
        window.addEventListener('load', function() {
            sendOtp();
        });

        function sendOtp() {
            fetch('{{ route("provider.otp.resend") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    user_id: {{ $user->id }}
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('OTP sent:', data);
            })
            .catch(error => {
                console.error('Error sending OTP:', error);
            });
        }

        function resendOtp() {
            sendOtp();
            alert('OTP sent successfully!');
        }
    </script>
</body>
</html>