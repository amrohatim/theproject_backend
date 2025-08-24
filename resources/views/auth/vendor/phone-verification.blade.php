<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phone Verification - Vendor Registration</title>

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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        .loading {
            opacity: 0.6;
            pointer-events: none;
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
                    Phone Verification
                </h1>
                <p class="text-gray-600">
                    Step 2 of 4: Verify Your Phone Number
                </p>

                <!-- Progress Bar -->
                <div class="w-full bg-gray-200 rounded-full h-2 mt-4">
                    <div class="step-indicator h-2 rounded-full" style="width: 50%"></div>
                </div>
            </div>

            <!-- Verification Form -->
            <div class="form-container rounded-2xl shadow-xl p-8">
                <!-- Alert Messages -->
                <div id="alert-container" class="hidden mb-6"></div>

                <!-- Phone Icon and Message -->
                <div class="text-center mb-8">
                    <div class="phone-icon inline-block p-4 bg-purple-100 rounded-full mb-4">
                        <i class="fas fa-mobile-alt text-4xl text-purple-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Check Your Phone</h3>
                    <p class="text-gray-600 mb-4">
                        We'll send a verification code to your phone number.
                    </p>
                    <p class="text-lg font-semibold text-purple-600 mb-4" id="phone-display">{{ $phoneNumber ?? 'Your Phone' }}</p>
                    <p class="text-sm text-gray-500">
                        Enter the 6-digit code to continue your registration.
                    </p>
                </div>

                <!-- Send OTP Button (shown initially) -->
                <div id="send-otp-section" class="text-center mb-6">
                    <button
                        onclick="sendPhoneOTP()"
                        id="send-otp-btn"
                        class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 text-white py-3 px-6 rounded-lg font-semibold hover:from-purple-700 hover:to-indigo-700 transition-all duration-300 transform hover:scale-105"
                    >
                        <i class="fas fa-paper-plane mr-2"></i>
                        Send Verification Code
                    </button>
                </div>

                <!-- OTP Form (hidden initially) -->
                <div id="otp-form-section" class="hidden">
                    <div class="text-center mb-6">
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
                            class="otp-code w-full text-center px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300"
                        >
                    </div>

                    <!-- Submit Button -->
                    <button
                        onclick="verifyPhoneOTP()"
                        id="verify-otp-btn"
                        class="w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white py-3 px-6 rounded-lg font-semibold hover:from-green-700 hover:to-emerald-700 transition-all duration-300 transform hover:scale-105 mb-4"
                    >
                        <i class="fas fa-check mr-2"></i>
                        Verify Phone Number
                    </button>

                    <!-- Resend OTP -->
                    <div class="text-center">
                        <p class="text-sm text-gray-600 mb-2">Didn't receive the code?</p>
                        <button
                            onclick="resendPhoneOTP()"
                            id="resend-otp-btn"
                            class="text-purple-600 hover:text-purple-700 font-medium transition-colors duration-300"
                        >
                            Resend OTP
                        </button>
                    </div>
                </div>
            </div>

            <!-- Back Button -->
            <div class="text-center">
                <a href="{{ url()->previous() }}" class="text-gray-600 hover:text-gray-800 transition-colors duration-300">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Email Verification
                </a>
            </div>
        </div>
    </div>

    <script>
        const registrationToken = '{{ $registrationToken }}';
        let currentRequestId = null;

        function showAlert(message, type = 'error') {
            const alertContainer = document.getElementById('alert-container');
            const alertClass = type === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700';
            const iconClass = type === 'success' ? 'fas fa-check-circle text-green-400' : 'fas fa-exclamation-circle text-red-400';
            
            alertContainer.innerHTML = `
                <div class="${alertClass} border rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="${iconClass}"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm">${message}</p>
                        </div>
                    </div>
                </div>
            `;
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
            if (loading) {
                element.classList.add('loading');
                element.disabled = true;
            } else {
                element.classList.remove('loading');
                element.disabled = false;
            }
        }

        function sendPhoneOTP() {
            setLoading('send-otp-btn', true);
            
            fetch('{{ route("register.send-phone-otp") }}', {
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
            
            fetch('{{ route("register.verify-phone-otp") }}', {
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
                    
                    // Redirect based on next step
                    setTimeout(() => {
                        if (data.next_step === 'company_information') {
                            window.location.href = '{{ route("vendor.register.step2") }}';
                        } else if (data.next_step === 'license_upload') {
                            window.location.href = '{{ route("vendor.register.step3") }}';
                        } else {
                            window.location.href = '{{ route("vendor.dashboard") }}';
                        }
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
            
            fetch('{{ route("register.resend-phone-otp") }}', {
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
