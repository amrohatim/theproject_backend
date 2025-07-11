<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Provider Registration Step 3 - Phone Verification">
    <meta name="robots" content="noindex, nofollow">
    <title>Provider Registration - Step 3 | Dala3Chic</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #8b5cf6 0%, #a855f7 50%, #c084fc 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .registration-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            padding: 40px;
            position: relative;
        }

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

        .progress-step.active::after,
        .progress-step.completed::after {
            background: #7c3aed;
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
        }

        .progress-step.active .step-circle {
            background: #7c3aed;
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
            color: #7c3aed;
            font-weight: 600;
        }

        .form-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-title {
            font-size: 24px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 8px;
        }

        .form-subtitle {
            color: #6b7280;
            font-size: 14px;
        }

        .verification-section {
            margin-bottom: 30px;
            padding: 20px;
            background: #faf5ff;
            border-radius: 12px;
            border: 1px solid #e9d5ff;
            text-align: center;
        }

        .verification-icon {
            width: 60px;
            height: 60px;
            background: #f3e8ff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .verification-icon i {
            font-size: 24px;
            color: #7c3aed;
        }

        .verification-title {
            font-size: 18px;
            font-weight: 600;
            color: #581c87;
            margin-bottom: 8px;
        }

        .verification-text {
            color: #7c3aed;
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 16px;
        }

        .phone-display {
            font-size: 16px;
            font-weight: 600;
            color: #7c3aed;
            margin-bottom: 16px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            color: #374151;
            font-size: 14px;
            text-align: center;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 18px;
            text-align: center;
            letter-spacing: 0.5rem;
            transition: all 0.3s ease;
            background: #f9fafb;
            font-weight: 600;
        }

        .form-input:focus {
            outline: none;
            border-color: #7c3aed;
            background: white;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
        }

        .form-button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-button:hover {
            background: linear-gradient(135deg, #5b21b6 0%, #4c1d95 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3);
        }

        .form-button:disabled {
            background: #9ca3af;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .form-button.secondary {
            background: transparent;
            color: #7c3aed;
            border: 2px solid #7c3aed;
        }

        .form-button.secondary:hover {
            background: #7c3aed;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            color: #6b7280;
            text-decoration: none;
            font-size: 14px;
            margin-bottom: 20px;
            transition: color 0.3s ease;
        }

        .back-link:hover {
            color: #374151;
        }

        .back-link i {
            margin-right: 8px;
        }

        .loading-spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid #ffffff;
            border-top: 2px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 8px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .form-button.loading .loading-spinner {
            display: inline-block;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 500;
        }

        .alert.success {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert.error {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .hidden {
            display: none;
        }

        @media (max-width: 640px) {
            .registration-container {
                margin: 10px;
                padding: 30px 20px;
            }

            .form-title {
                font-size: 20px;
            }

            .step-circle {
                width: 25px;
                height: 25px;
                font-size: 12px;
            }

            .step-label {
                font-size: 10px;
            }

            .verification-icon {
                width: 50px;
                height: 50px;
            }

            .verification-icon i {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="registration-container">
        <a href="/register/provider/step2" class="back-link">
            <i class="fas fa-arrow-left"></i>
            Back to Email Verification
        </a>

        <!-- Progress Bar -->
        <div class="progress-bar">
            <div class="progress-step completed">
                <div class="step-circle"><i class="fas fa-check"></i></div>
                <div class="step-label">Provider Info</div>
            </div>
            <div class="progress-step completed">
                <div class="step-circle"><i class="fas fa-check"></i></div>
                <div class="step-label">Email Verification</div>
            </div>
            <div class="progress-step active">
                <div class="step-circle">3</div>
                <div class="step-label">Phone Verification</div>
            </div>
            <div class="progress-step">
                <div class="step-circle">4</div>
                <div class="step-label">License</div>
            </div>
        </div>

        <div class="form-header">
            <h2 class="form-title">Phone Verification</h2>
            <p class="form-subtitle">Step 3 of 4: Verify your phone number</p>
        </div>

        <!-- Alert Messages -->
        <div id="alert-container" class="hidden"></div>

        <!-- Verification Section -->
        <div class="verification-section">
            <div class="verification-icon">
                <i class="fas fa-mobile-alt"></i>
            </div>
            <div class="verification-title">Check Your Phone</div>
            <div class="verification-text">
                We'll send a verification code to your phone number.
            </div>
            <div class="phone-display" id="phone-display">{{ $phoneNumber ?? 'Your Phone' }}</div>
            <div class="verification-text">
                Enter the 6-digit code to continue your registration.
            </div>
        </div>

        <!-- Send OTP Section -->
        <div id="send-otp-section">
            <button type="button" onclick="sendPhoneOTP()" id="send-otp-btn" class="form-button">
                <div class="loading-spinner"></div>
                <i class="fas fa-paper-plane" style="margin-right: 8px;"></i>
                <span class="button-text">Send Verification Code</span>
            </button>
        </div>

        <!-- OTP Form Section (hidden initially) -->
        <div id="otp-form-section" class="hidden">
            <div class="form-group">
                <label for="otp_code" class="form-label">Enter OTP Code</label>
                <input
                    id="otp_code"
                    name="otp_code"
                    type="text"
                    required
                    maxlength="6"
                    placeholder="000000"
                    class="form-input"
                >
            </div>

            <button type="button" onclick="verifyPhoneOTP()" id="verify-otp-btn" class="form-button">
                <div class="loading-spinner"></div>
                <i class="fas fa-check" style="margin-right: 8px;"></i>
                <span class="button-text">Verify Phone Number</span>
            </button>

            <div style="text-align: center; margin-top: 15px;">
                <p style="color: #6b7280; font-size: 14px; margin-bottom: 8px;">Didn't receive the code?</p>
                <button type="button" onclick="resendPhoneOTP()" id="resend-otp-btn" class="form-button secondary">
                    <div class="loading-spinner"></div>
                    <span class="button-text">Resend OTP</span>
                </button>
            </div>
        </div>
    </div>

    <script>
        const registrationToken = '{{ $registrationToken }}';
        let currentRequestId = null;

        function showAlert(message, type = 'error') {
            const alertContainer = document.getElementById('alert-container');
            const alertClass = type === 'success' ? 'alert success' : 'alert error';

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
