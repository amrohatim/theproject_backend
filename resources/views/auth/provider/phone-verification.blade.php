<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ __('messages.provider_registration_phone_verification_description') }}">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ __('messages.data3chic_provider_registration') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        purple: {
                            50: '#faf5ff',
                            100: '#f3e8ff',
                            200: '#e9d5ff',
                            300: '#d8b4fe',
                            400: '#c084fc',
                            500: '#a855f7',
                            600: '#9333ea',
                            700: '#7c3aed',
                            800: '#6b21a8',
                            900: '#581c87'
                        }
                    }
                }
            }
        }
    </script>

    <!-- Fonts -->
    @if(app()->getLocale() == 'ar')
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('css/rtl.css') }}">
    @else
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @endif
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        .loading {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid #ffffff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s ease-in-out infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Step Indicator Styles - Matching Vendor Registration */
        .progress-bar {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            position: relative;
        }

        .progress-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            position: relative;
        }

        .progress-step::after {
            content: '';
            position: absolute;
            top: 15px;
            left: 50%;
            width: 100%;
            height: 2px;
            background: #e5e7eb;
            z-index: 1;
        }

        .progress-step:last-child::after {
            display: none;
        }

        .progress-step.active::after {
            background: #8b5cf6;
        }

        .progress-step.completed::after {
            background: #10b981;
        }

        .step-circle {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
            color: #6b7280;
            position: relative;
            z-index: 2;
            transition: all 0.2s ease;
        }

        .progress-step.active .step-circle {
            background: #8b5cf6;
            color: white;
        }

        .progress-step.completed .step-circle {
            background: #10b981;
            color: white;
        }

        .step-label {
            margin-top: 8px;
            font-size: 12px;
            color: #6b7280;
            text-align: center;
        }

        .progress-step.active .step-label {
            color: #8b5cf6;
            font-weight: 600;
        }

        .progress-step.completed .step-label {
            color: #10b981;
            font-weight: 600;
        }

        /* Responsive adjustments for step indicator */
        @media (max-width: 640px) {
            .step-circle {
                width: 25px;
                height: 25px;
                font-size: 12px;
            }

            .step-label {
                font-size: 10px;
            }
        }
    </style>
</head>
<body class="min-h-screen bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Left Side - Marketing Content -->
        <div class="hidden lg:flex lg:w-1/2 text-white p-12 flex-col justify-top" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
            <div class="max-w-md mx-auto space-y-8">
                <div class="text-center">
                    <h1 class="text-3xl font-bold mb-8">Dala3Chic</h1>
                </div>

                <!-- Main Heading -->
                <div class="text-center space-y-4">
                    <h2 class="text-4xl font-bold leading-tight">
                        {{ __('messages.verification') }} {{ __('messages.phone') }}
                    </h2>
                    <p class="text-purple-100 text-lg">
                        {{ __('messages.phone_verification_description') }}
                    </p>
                </div>

                <!-- Features -->
                <div class="space-y-6 mt-12">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg mb-1">{{ __('messages.sms_verification') }}</h3>
                            <p class="text-purple-100 text-sm">{{ __('messages.secure_verification_code_sent_directly_to_your_phone') }}</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg mb-1">{{ __('messages.account_security') }}</h3>
                            <p class="text-purple-100 text-sm">{{ __('messages.account_security_description') }}</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg mb-1">{{ __('messages.almost_complete') }}</h3>
                            <p class="text-purple-100 text-sm">{{ __('messages.just_one_more_step_to_complete_your_registration') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Verification Form -->
        <div class="w-full lg:w-1/2 bg-white p-8 lg:p-12 flex items-center justify-center">
            <div class="w-full max-w-md space-y-6">
                <div class="text-center space-y-2">
                    <h2 class="text-2xl font-bold text-gray-900">{{ __('messages.phone_verification') }}</h2>
                    <p class="text-gray-600">{{ __('messages.step_3_of_4_verify_phone') }}</p>
                </div>

                <!-- Step Indicator -->
                <div class="progress-bar">
                    <div class="progress-step completed">
                        <div class="step-circle"><i class="fas fa-check"></i></div>
                        <div class="step-label">{{ __('messages.provider_info') }}</div>
                    </div>
                    <div class="progress-step completed">
                        <div class="step-circle"><i class="fas fa-check"></i></div>
                        <div class="step-label">{{ __('messages.verification') }}</div>
                    </div>
                    <div class="progress-step active">
                        <div class="step-circle">3</div>
                        <div class="step-label">{{ __('messages.phone') }}</div>
                    </div>
                    <div class="progress-step">
                        <div class="step-circle">4</div>
                        <div class="step-label">{{ __('messages.license') }}</div>
                    </div>
                </div>

                <!-- Back Link -->
                <div class="mb-4">
                    <a href="/register/provider/step2" class="text-purple-600 hover:text-purple-700 transition-colors duration-300 text-sm font-medium">
                        <i class="fas fa-arrow-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }} {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                        {{ __('messages.back_to_email_verification') }}
                    </a>
                </div>

                <!-- Alert Messages -->
                <div id="alert-container" class="hidden mb-4"></div>

                <!-- Phone Verification Section -->
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-6 mb-6">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-mobile-alt text-purple-600 text-2xl"></i>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-2">{{ __('messages.check_your_phone') }}</h3>
                        <p class="text-gray-600 text-sm mb-4">
                            {{ __('messages.phone_verification_send_description') }}
                        </p>
                        <div class="font-semibold text-purple-600 mb-4" id="phone-display">{{ $phoneNumber ?? 'Your Phone' }}</div>
                        <p class="text-gray-600 text-sm">
                            {{ __('messages.enter_6_digit_code_continue') }}
                        </p>
                    </div>
                </div>

                <!-- Send OTP Section -->
                <div id="send-otp-section">
                    <button type="button" onclick="sendPhoneOTP()" id="send-otp-btn" class="w-full text-white py-3 px-4 rounded-md font-semibold transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                        <span class="loading hidden">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                        </span>
                        <i class="fas fa-paper-plane mr-2"></i>
                        <span class="button-text">{{ __('messages.send_verification_code') }}</span>
                    </button>
                </div>

                <!-- OTP Form Section (hidden initially) -->
                <div id="otp-form-section" class="hidden space-y-4">
                    <!-- OTP Code Field -->
                    <div class="space-y-2">
                        <label for="otp_code" class="text-sm font-medium text-gray-700">{{ __('messages.enter_otp_code') }}</label>
                        <div class="relative">
                            <input id="otp_code" name="otp_code" type="text"
                                   placeholder="{{ __('messages.enter_6_digit_code') }}" required maxlength="6"
                                   class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent text-center text-lg font-mono tracking-widest">
                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Verify Button -->
                    <button type="button" onclick="verifyPhoneOTP()" id="verify-otp-btn" class="w-full text-white py-3 px-4 rounded-md font-semibold transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                        <span class="loading hidden">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                        </span>
                        <i class="fas fa-check mr-2"></i>
                        <span class="button-text">{{ __('messages.verify_phone_number') }}</span>
                    </button>

                    <!-- Resend Section -->
                    <div class="text-center">
                        <p class="text-gray-600 text-sm mb-2">{{ __('messages.didnt_receive_code') }}</p>
                        <button type="button" onclick="resendPhoneOTP()" id="resend-otp-btn" class="text-purple-600 hover:text-purple-700 font-medium underline">
                            <span class="loading hidden">
                                <i class="fas fa-spinner fa-spin mr-2"></i>
                            </span>
                            <span class="button-text">{{ __('messages.resend_otp') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const registrationToken = '{{ $registrationToken }}';
        let currentRequestId = null;

        function showAlert(message, type = 'error') {
            const alertContainer = document.getElementById('alert-container');
            const alertClass = type === 'success'
                ? 'bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-md'
                : 'bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-md';

            alertContainer.innerHTML = `<div class="${alertClass}">${message}</div>`;
            alertContainer.classList.remove('hidden');

            // Auto-hide success messages after 5 seconds
            if (type === 'success') {
                setTimeout(() => {
                    alertContainer.classList.add('hidden');
                }, 5000);
            }
        }

        function setLoading(elementId, loading) {
            const element = document.getElementById(elementId);
            const loadingSpan = element.querySelector('.loading');

            if (loading) {
                loadingSpan.classList.remove('hidden');
                element.disabled = true;
            } else {
                loadingSpan.classList.add('hidden');
                element.disabled = false;
            }
        }

        function sendPhoneOTP() {
            setLoading('send-otp-btn', true);
            
            fetch('/api/provider-registration/send-phone-otp', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    registration_token: registrationToken
                })
            })
            .then(response => response.json())
            .then(data => {
                setLoading('send-otp-btn', false);
                
                if (data.success) {
                    currentRequestId = data.request_id;
                    showAlert('OTP sent successfully! Please check your phone.', 'success');
                    
                    // Hide send button and show OTP form
                    document.getElementById('send-otp-section').classList.add('hidden');
                    document.getElementById('otp-form-section').classList.remove('hidden');
                    
                    // Focus on OTP input
                    document.getElementById('otp_code').focus();
                } else {
                    showAlert(data.message || 'Failed to send OTP. Please try again.');
                }
            })
            .catch(error => {
                setLoading('send-otp-btn', false);
                console.error('Error sending OTP:', error);
                showAlert('Network error. Please check your connection and try again.');
            });
        }

        function verifyPhoneOTP() {
            const otpCode = document.getElementById('otp_code').value.trim();
            
            if (!otpCode || otpCode.length !== 6) {
                showAlert('Please enter a valid 6-digit OTP code.');
                return;
            }
            
            setLoading('verify-otp-btn', true);
            
            fetch('/api/provider-registration/verify-phone-otp', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    registration_token: registrationToken,
                    otp_code: otpCode
                })
            })
            .then(response => response.json())
            .then(data => {
                setLoading('verify-otp-btn', false);
                
                if (data.success) {
                    showAlert('Phone verified successfully! Redirecting...', 'success');

                    // Store user_id for license upload step
                    if (data.user_id) {
                        localStorage.setItem('provider_user_id', data.user_id);
                    }

                    // Clear the registration token as it's no longer needed
                    localStorage.removeItem('provider_registration_token');

                    // Redirect to license upload step
                    setTimeout(() => {
                        window.location.href = '/register/provider/step3';
                    }, 2000);
                } else {
                    showAlert(data.message || 'Invalid OTP. Please try again.');
                    document.getElementById('otp_code').value = '';
                    document.getElementById('otp_code').focus();
                }
            })
            .catch(error => {
                setLoading('verify-otp-btn', false);
                console.error('Error verifying OTP:', error);
                showAlert('Network error. Please check your connection and try again.');
            });
        }

        function resendPhoneOTP() {
            setLoading('resend-otp-btn', true);
            
            fetch('/api/provider-registration/resend-phone-otp', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    registration_token: registrationToken
                })
            })
            .then(response => response.json())
            .then(data => {
                setLoading('resend-otp-btn', false);
                
                if (data.success) {
                    currentRequestId = data.request_id;
                    showAlert('OTP resent successfully! Please check your phone.', 'success');
                    document.getElementById('otp_code').value = '';
                    document.getElementById('otp_code').focus();
                } else {
                    showAlert(data.message || 'Failed to resend OTP. Please try again.');
                }
            })
            .catch(error => {
                setLoading('resend-otp-btn', false);
                console.error('Error resending OTP:', error);
                showAlert('Network error. Please check your connection and try again.');
            });
        }

        // Auto-format OTP input
        document.getElementById('otp_code').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, ''); // Remove non-digits
            if (value.length > 6) value = value.slice(0, 6); // Limit to 6 digits
            e.target.value = value;
            
            // Auto-submit when 6 digits are entered
            if (value.length === 6) {
                setTimeout(() => verifyPhoneOTP(), 500);
            }
        });

        // Handle Enter key press
        document.getElementById('otp_code').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                verifyPhoneOTP();
            }
        });
    </script>
</body>
</html>
