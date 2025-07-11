<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Provider Registration Step 2 - Phone & Email Verification">
    <meta name="robots" content="noindex, nofollow">
    <title>Provider Registration - Step 2 | Dala3Chic</title>
    
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
            background: #f9fafb;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
        }

        .verification-section.completed {
            background: #f0fdf4;
            border-color: #10b981;
        }

        .verification-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .verification-title {
            font-weight: 600;
            color: #374151;
            display: flex;
            align-items: center;
        }

        .verification-title i {
            margin-right: 8px;
            color: #6b7280;
        }

        .verification-section.completed .verification-title i {
            color: #10b981;
        }

        .verification-status {
            font-size: 12px;
            padding: 4px 8px;
            border-radius: 12px;
            font-weight: 500;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-completed {
            background: #d1fae5;
            color: #065f46;
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
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #f9fafb;
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
            margin-top: 10px;
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
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #d1d5db;
        }

        .form-button.secondary:hover {
            background: #e5e7eb;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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

        .info-text {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 15px;
            line-height: 1.5;
        }

        .resend-link {
            background: none;
            border: none;
            color: #7c3aed;
            text-decoration: underline;
            cursor: pointer;
            font-size: 13px;
            padding: 0;
            margin: 0;
        }

        .resend-link:hover {
            color: #5b21b6;
        }

        .resend-link:disabled {
            color: #9ca3af;
            cursor: not-allowed;
        }

        .error-message {
            color: #dc2626;
            font-size: 12px;
            margin-top: 5px;
            display: none;
        }

        .success-message {
            color: #059669;
            font-size: 12px;
            margin-top: 5px;
            display: none;
            background: #d1fae5;
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid #a7f3d0;
        }

        @media (max-width: 640px) {
            .registration-container {
                margin: 10px;
                padding: 30px 20px;
            }
            
            .form-title {
                font-size: 20px;
            }
            
            .progress-bar {
                margin-bottom: 20px;
            }
            
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
<body>
    <div class="registration-container">
        <a href="/register/provider/step1" class="back-link">
            <i class="fas fa-arrow-left"></i>
            Back to Provider Info
        </a>

        <!-- Progress Bar -->
        <div class="progress-bar">
            <div class="progress-step completed">
                <div class="step-circle"><i class="fas fa-check"></i></div>
                <div class="step-label">Provider Info</div>
            </div>
            <div class="progress-step active">
                <div class="step-circle">2</div>
                <div class="step-label">Email Verification</div>
            </div>
            <div class="progress-step">
                <div class="step-circle">3</div>
                <div class="step-label">Phone Verification</div>
            </div>
            <div class="progress-step">
                <div class="step-circle">4</div>
                <div class="step-label">License</div>
            </div>
        </div>

        <div class="form-header">
            <h2 class="form-title">Verify Your Email</h2>
            <p class="form-subtitle">Step 2: Enter the verification code sent to your email</p>
        </div>

        <!-- Email Verification Section -->
        <div class="verification-section" id="emailSection">
            <div class="verification-header">
                <div class="verification-title">
                    <i class="fas fa-envelope"></i>
                    Email Verification
                </div>
                <div class="verification-status status-pending" id="emailStatus">Pending</div>
            </div>

            <div class="info-text">
                We've sent a 6-digit verification code to your email address. Please enter the code below to verify your email.
            </div>

            <!-- Verification Code Input -->
            <form id="emailVerificationForm">
                <div class="form-group">
                    <label for="verification_code" class="form-label">Verification Code *</label>
                    <input type="text" id="verification_code" name="verification_code" class="form-input"
                           required maxlength="6" placeholder="Enter 6-digit code"
                           style="text-align: center; font-size: 18px; letter-spacing: 2px;">
                    <div class="error-message" id="codeError" style="display: none;"></div>
                    <div class="success-message" id="codeSuccess" style="display: none;"></div>
                </div>

                <button type="submit" class="form-button" id="verifyCodeBtn">
                    <div class="loading-spinner"></div>
                    <span class="button-text">Verify Code</span>
                </button>
            </form>

            <div class="info-text" style="margin-top: 15px; text-align: center;">
                Didn't receive the code?
                <button type="button" class="resend-link" id="resendCodeBtn">Resend Code</button>
            </div>
        </div>

        <!-- Continue Button (hidden until email verification complete) -->
        <button type="button" class="form-button" id="continueBtn" style="display: none;">
            Continue to Phone Verification
        </button>
    </div>

    <script>
        let emailVerified = false;
        let registrationToken = null;

        // Initialize on page load
        window.addEventListener('load', function() {
            // Get registration token from session/localStorage if available
            registrationToken = localStorage.getItem('provider_registration_token');

            // Check if we have a registration token
            if (registrationToken) {
                // Don't automatically send verification email - user should use the code from step 1
                // Only show the form and let user manually resend if needed
                console.log('Registration token found. Please use the verification code sent to your email.');
            } else {
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

            verifyBtn.classList.add('loading');
            verifyBtn.disabled = true;

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
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    emailVerified = true;
                    updateEmailVerificationStatus();
                    document.getElementById('continueBtn').style.display = 'block';
                    // Keep the registration token for phone verification
                    // localStorage.removeItem('provider_registration_token');
                } else {
                    showError(data.message || 'Invalid verification code. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('Failed to verify code. Please try again.');
            })
            .finally(() => {
                verifyBtn.classList.remove('loading');
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
            resendBtn.textContent = 'Sending...';

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
                    showSuccess('Verification code sent successfully! Please check your email.');

                    // Disable resend button for 60 seconds
                    let countdown = 60;
                    const interval = setInterval(() => {
                        resendBtn.textContent = `Resend Code (${countdown}s)`;
                        countdown--;

                        if (countdown < 0) {
                            clearInterval(interval);
                            resendBtn.disabled = false;
                            resendBtn.textContent = 'Resend Code';
                        }
                    }, 1000);
                } else {
                    console.error('Failed to resend verification email:', data.message);
                    showError(data.message || 'Failed to resend verification code. Please try again.');
                    resendBtn.disabled = false;
                    resendBtn.textContent = 'Resend Code';
                }
            })
            .catch(error => {
                console.error('Error resending verification email:', error);
                showError('Failed to resend verification code. Please check your connection and try again.');
                resendBtn.disabled = false;
                resendBtn.textContent = 'Resend Code';
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
            const emailSection = document.getElementById('emailSection');
            const emailStatus = document.getElementById('emailStatus');

            emailSection.classList.add('completed');
            emailStatus.textContent = 'Verified';
            emailStatus.className = 'verification-status status-completed';
        }

        function showError(message) {
            const errorDiv = document.getElementById('codeError');
            const successDiv = document.getElementById('codeSuccess');

            // Hide success message and show error
            successDiv.style.display = 'none';
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
        }

        function showSuccess(message) {
            const errorDiv = document.getElementById('codeError');
            const successDiv = document.getElementById('codeSuccess');

            // Hide error message and show success
            errorDiv.style.display = 'none';
            successDiv.textContent = message;
            successDiv.style.display = 'block';
        }

        function clearMessages() {
            const errorDiv = document.getElementById('codeError');
            const successDiv = document.getElementById('codeSuccess');

            errorDiv.style.display = 'none';
            successDiv.style.display = 'none';
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
