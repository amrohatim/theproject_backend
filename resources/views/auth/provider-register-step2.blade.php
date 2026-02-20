<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ __('messages.provider_registration_step2_description') }}">
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
        .step-content {
            display: none;
        }
        .step-content.active {
            display: block;
        }
        .progress-line {
            transition: all 0.3s ease;
        }
        .step-circle {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }
        .step-circle:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
        }
        .step-circle.completed {
            animation: checkmark-bounce 0.6s ease-in-out;
        }
        @keyframes checkmark-bounce {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        .step-circle.clickable:hover {
            background-color: rgba(139, 92, 246, 0.1);
        }
        .step-circle:focus {
            outline: 2px solid #8b5cf6;
            outline-offset: 2px;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }
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
                    <h1 class="text-3xl font-bold mb-8">glowlabs</h1>
                </div>

                <!-- Main Heading -->
                <div class="text-center space-y-4">
                    <h2 class="text-4xl font-bold leading-tight">
                        {{ __('messages.email_verification') }}
                    </h2>
                    <p class="text-purple-100 text-lg">
                        {{ __('messages.email_verification_description') }}
                    </p>
                </div>

                <!-- Features -->
                <div class="space-y-6 mt-12">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg mb-1">{{ __('messages.secure_verification') }}</h3>
                            <p class="text-purple-100 text-sm">{{ __('messages.secure_verification_description') }}</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg mb-1">{{ __('messages.quick_process') }}</h3>
                            <p class="text-purple-100 text-sm">{{ __('messages.quick_process_description') }}</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg mb-1">{{ __('messages.almost_done') }}</h3>
                            <p class="text-purple-100 text-sm">{{ __('messages.halfway_through_registration') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Verification Form -->
        <div class="w-full lg:w-1/2 bg-white p-8 lg:p-12 flex items-center justify-center">
            <div class="w-full max-w-md space-y-6">
                <div class="text-center space-y-2">
                    <h2 class="text-2xl font-bold text-gray-900">{{ __('messages.verify_your_email') }}</h2>
                    <p class="text-gray-600">{{ __('messages.step_2_of_4_email_verification') }}</p>
                </div>

                <!-- Step Indicator -->
                <div class="progress-bar">
                    <div class="progress-step completed">
                        <div class="step-circle"><i class="fas fa-check"></i></div>
                        <div class="step-label">{{ __('messages.provider_info') }}</div>
                    </div>
                    <div class="progress-step active">
                        <div class="step-circle">2</div>
                        <div class="step-label">{{ __('messages.verification') }}</div>
                    </div>
                    <div class="progress-step">
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
                    <a href="/register/provider/step1" class="text-purple-600 hover:text-purple-700 transition-colors duration-300 text-sm font-medium">
                        <i class="fas fa-arrow-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }} {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                        {{ __('messages.back_to_provider_info') }}
                    </a>
                </div>

                <!-- Email Verification Section -->
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <i class="fas fa-envelope text-purple-600 {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                            <span class="font-medium text-gray-900">{{ __('messages.email_verification') }}</span>
                        </div>
                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full" id="emailStatus">{{ __('messages.pending') }}</span>
                    </div>
                    <p class="text-gray-600 text-sm mb-4">
                        {{ __('messages.email_verification_code_description') }}
                    </p>
                </div>

                <!-- Verification Code Form -->
                <form id="emailVerificationForm" class="space-y-4">
                    <!-- Verification Code Field -->
                    <div class="space-y-2">
                        <label for="verification_code" class="text-sm font-medium text-gray-700">{{ __('messages.verification_code') }}</label>
                        <div class="relative">
                            <input id="verification_code" name="verification_code" type="text"
                                   placeholder="{{ __('messages.enter_6_digit_code') }}" required maxlength="6"
                                   class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent text-center text-lg font-mono tracking-widest">
                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <div class="text-red-600 text-sm hidden" id="codeError"></div>
                        <div class="text-green-600 text-sm hidden" id="codeSuccess"></div>
                    </div>

                    <!-- Verify Button -->
                    <button type="submit" id="verifyCodeBtn" class="w-full text-white py-3 px-4 rounded-md font-semibold transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                        <span class="loading hidden">
                            <i class="fas fa-spinner fa-spin {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                        </span>
                        <span class="button-text">{{ __('messages.verify_code') }}</span>
                    </button>
                </form>

                <!-- Resend Section -->
                <div class="text-center mt-4">
                    <p class="text-gray-600 text-sm">
                        {{ __('messages.didnt_receive_code') }}
                        <button type="button" id="resendCodeBtn" class="text-purple-600 hover:text-purple-700 font-medium underline">
                            {{ __('messages.resend_code') }}
                        </button>
                    </p>
                </div>

                <!-- Continue Button (hidden until email verification complete) -->
                <button type="button" id="continueBtn" class="w-full text-white py-3 px-4 rounded-md font-semibold transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 hidden" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                    {{ __('messages.continue_to_phone_verification') }}
                    <i class="fas fa-arrow-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }} {{ app()->getLocale() == 'ar' ? 'mr-2' : 'ml-2' }}"></i>
                </button>
            </div>
        </div>
    </div>

    <script>
        let emailVerified = false;
        let registrationToken = null;

        // Initialize on page load
        window.addEventListener('load', function() {
            // Get registration token from session/localStorage if available
            registrationToken = localStorage.getItem('provider_registration_token');

            // Debug logging
            console.log('Step 2 initialization - Registration token:', registrationToken ? registrationToken.substring(0, 20) + '...' : 'NOT FOUND');
            console.log('All localStorage keys:', Object.keys(localStorage));

            // Check if we have a registration token
            if (registrationToken) {
                // Don't automatically send verification email - user should use the code from step 1
                // Only show the form and let user manually resend if needed
                console.log('Registration token found. Please use the verification code sent to your email.');
            } else {
                console.error('No registration token found in localStorage');
                showError('Registration session not found. Please start registration again from step 1.');
            }
        });

        // Send verification email (used for manual resend only)
        function sendVerificationEmail() {
            // Check if we have a registration token
            if (!registrationToken) {
                console.error('No registration token found. Cannot send verification email.');
                showError('Registration session not found. Please start registration again.');
                return;
            }

            fetch('/api/provider-registration/resend-email-verification', {
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
                if (data.success) {
                    console.log('Verification email sent successfully');
                    if (data.registration_token) {
                        registrationToken = data.registration_token;
                        localStorage.setItem('provider_registration_token', registrationToken);
                    }
                } else {
                    console.error('Failed to send verification email:', data.message);
                    showError(data.message || 'Failed to send verification email. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error sending verification email:', error);
                showError('Failed to send verification email. Please check your connection and try again.');
            });
        }

        // Handle verification code form submission
        document.getElementById('emailVerificationForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const verifyBtn = document.getElementById('verifyCodeBtn');
            const codeInput = document.getElementById('verification_code');
            const errorDiv = document.getElementById('codeError');
            const code = codeInput.value.trim();

            // Prevent duplicate submissions
            if (verifyBtn.disabled) {
                return;
            }

            // Clear previous messages
            clearMessages();

            // Validate code format
            if (!/^\d{6}$/.test(code)) {
                showError('Please enter a valid 6-digit code');
                return;
            }

            if (!registrationToken) {
                showError('Registration token not found. Please refresh the page and try again.');
                return;
            }

            const loadingSpan = verifyBtn.querySelector('.loading');
            const buttonText = verifyBtn.querySelector('.button-text');
            loadingSpan.classList.remove('hidden');
            verifyBtn.disabled = true;

            console.log('Sending email verification request with token:', registrationToken ? registrationToken.substring(0, 20) + '...' : 'NULL');
            console.log('Verification code:', code);

            fetch('/api/provider-registration/verify-email', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    registration_token: registrationToken,
                    verification_code: code
                })
            })
            .then(response => {
                console.log('Email verification response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Email verification response data:', data);
                if (data.success) {
                    emailVerified = true;
                    updateEmailVerificationStatus();
                    document.getElementById('continueBtn').classList.remove('hidden');
                    // Keep the registration token for phone verification
                    // localStorage.removeItem('provider_registration_token');
                } else {
                    console.error('Email verification failed:', data.message);
                    showError(data.message || 'Invalid verification code. Please try again.');
                }
            })
            .catch(error => {
                console.error('Email verification error:', error);
                showError('Failed to verify code. Please try again.');
            })
            .finally(() => {
                const loadingSpan = verifyBtn.querySelector('.loading');
                loadingSpan.classList.add('hidden');
                verifyBtn.disabled = false;
            });
        });

        // Resend verification code
        document.getElementById('resendCodeBtn').addEventListener('click', function() {
            const resendBtn = this;

            // Check if we have a registration token
            if (!registrationToken) {
                showError('Registration session not found. Please start registration again.');
                return;
            }

            resendBtn.disabled = true;
            resendBtn.textContent = '{{ __('messages.sending') }}...';

            // Clear any previous messages
            clearMessages();

            fetch('/api/provider-registration/resend-email-verification', {
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
                if (data.success) {
                    console.log('Verification email resent successfully');
                    showSuccess('{{ __('messages.verification_code_sent_successfully') }}');

                    // Disable resend button for 60 seconds
                    let countdown = 60;
                    const interval = setInterval(() => {
                        resendBtn.textContent = `{{ __('messages.resend_code') }} (${countdown}s)`;
                        countdown--;

                        if (countdown < 0) {
                            clearInterval(interval);
                            resendBtn.disabled = false;
                            resendBtn.textContent = '{{ __('messages.resend_code') }}';
                        }
                    }, 1000);
                } else {
                    console.error('Failed to resend verification email:', data.message);
                    showError(data.message || 'Failed to resend verification code. Please try again.');
                    resendBtn.disabled = false;
                    resendBtn.textContent = '{{ __('messages.resend_code') }}';
                }
            })
            .catch(error => {
                console.error('Error resending verification email:', error);
                showError('Failed to resend verification code. Please check your connection and try again.');
                resendBtn.disabled = false;
                resendBtn.textContent = '{{ __('messages.resend_code') }}';
            });
        });

        // Continue to next step
        document.getElementById('continueBtn').addEventListener('click', function() {
            const registrationToken = localStorage.getItem('provider_registration_token');
            if (registrationToken) {
                window.location.href = `/register/provider/phone-verification?token=${registrationToken}`;
            } else {
                window.location.href = '/register/provider/phone-verification';
            }
        });

        // Helper functions
        function updateEmailVerificationStatus() {
            const emailStatus = document.getElementById('emailStatus');
            emailStatus.textContent = '{{ __('messages.verified') }}';
            emailStatus.className = 'px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full';
        }

        function showError(message) {
            const errorDiv = document.getElementById('codeError');
            const successDiv = document.getElementById('codeSuccess');

            // Hide success message and show error
            successDiv.classList.add('hidden');
            errorDiv.textContent = message;
            errorDiv.classList.remove('hidden');
        }

        function showSuccess(message) {
            const errorDiv = document.getElementById('codeError');
            const successDiv = document.getElementById('codeSuccess');

            // Hide error message and show success
            errorDiv.classList.add('hidden');
            successDiv.textContent = message;
            successDiv.classList.remove('hidden');
        }

        function clearMessages() {
            const errorDiv = document.getElementById('codeError');
            const successDiv = document.getElementById('codeSuccess');

            errorDiv.classList.add('hidden');
            successDiv.classList.add('hidden');
        }

        // Auto-format verification code input (numbers only, max 6 digits)
        document.getElementById('verification_code').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, ''); // Remove non-digits
            if (value.length > 6) {
                value = value.substring(0, 6);
            }
            e.target.value = value;
        });
    </script>
</body>
</html>
