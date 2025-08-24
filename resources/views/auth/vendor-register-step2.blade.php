<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Vendor Registration Step 2 - Phone & Email Verification">
    <meta name="robots" content="noindex, nofollow">
    <title>Vendor Registration - Step 2 | Dala3Chic</title>
    
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            background: #3b82f6;
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
            background: #3b82f6;
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
            color: #3b82f6;
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
            border-color: #3b82f6;
            background: white;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }



        .form-button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
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
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
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
        <a href="/register/vendor/step1" class="back-link">
            <i class="fas fa-arrow-left"></i>
            Back to Basic Info
        </a>

        <!-- Progress Bar -->
        <div class="progress-bar">
            <div class="progress-step completed">
                <div class="step-circle"><i class="fas fa-check"></i></div>
                <div class="step-label">Basic Info</div>
            </div>
            <div class="progress-step active">
                <div class="step-circle">2</div>
                <div class="step-label">Verification</div>
            </div>
            <div class="progress-step">
                <div class="step-circle">3</div>
                <div class="step-label">Company</div>
            </div>
            <div class="progress-step">
                <div class="step-circle">4</div>
                <div class="step-label">License</div>
            </div>
        </div>

        <div class="form-header">
            <h2 class="form-title">Verify Your Email</h2>
            <p class="form-subtitle">Step 2: Verify your email address using Firebase Authentication</p>
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
                We'll send a verification email to your registered email address. Please check your inbox and click the verification link.
            </div>

            <button type="button" class="form-button" id="sendEmailBtn">
                <div class="loading-spinner"></div>
                <span class="button-text">Send Verification Email</span>
            </button>

            <div class="info-text" style="margin-top: 15px; font-style: italic;">
                After clicking the verification link in your email, click the button below to continue.
            </div>

            <button type="button" class="form-button secondary" id="checkVerificationBtn" style="margin-top: 10px; display: none;">
                <div class="loading-spinner"></div>
                <span class="button-text">Check Verification Status</span>
            </button>
        </div>

        <!-- Continue Button (hidden until email verification complete) -->
        <button type="button" class="form-button" id="continueBtn" style="display: none;">
            Continue to Company Information
        </button>
    </div>

    <script>
        let emailVerified = false;

        // Initialize Firebase email verification check on page load
        window.addEventListener('load', function() {
            checkEmailVerificationStatus();
        });

        // Send Email Verification
        document.getElementById('sendEmailBtn').addEventListener('click', function() {
            this.classList.add('loading');
            this.disabled = true;

            fetch('/api/vendor/register/send-firebase-email-verification', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Verification email sent! Please check your inbox and click the verification link.');
                    document.getElementById('checkVerificationBtn').style.display = 'block';
                } else {
                    alert(data.message || 'Failed to send verification email');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to send verification email. Please try again.');
            })
            .finally(() => {
                this.classList.remove('loading');
                this.disabled = false;
            });
        });

        // Check Email Verification Status
        document.getElementById('checkVerificationBtn').addEventListener('click', function() {
            this.classList.add('loading');
            this.disabled = true;

            fetch('/api/vendor/register/check-firebase-email-verification', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.verified) {
                    emailVerified = true;
                    updateEmailVerificationStatus();
                    document.getElementById('continueBtn').style.display = 'block';
                    alert('Email verified successfully!');
                } else {
                    alert(data.message || 'Email not yet verified. Please check your inbox and click the verification link.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to check verification status. Please try again.');
            })
            .finally(() => {
                this.classList.remove('loading');
                this.disabled = false;
            });
        });

        // Continue to next step
        document.getElementById('continueBtn').addEventListener('click', function() {
            window.location.href = '/register/vendor/step3';
        });

        function updateEmailVerificationStatus() {
            const emailSection = document.getElementById('emailSection');
            const emailStatus = document.getElementById('emailStatus');

            emailSection.classList.add('completed');
            emailStatus.textContent = 'Verified';
            emailStatus.className = 'verification-status status-completed';
        }

        function checkEmailVerificationStatus() {
            // Check if email is already verified from previous session
            fetch('/api/vendor/register/check-firebase-email-verification', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.verified) {
                    emailVerified = true;
                    updateEmailVerificationStatus();
                    document.getElementById('continueBtn').style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error checking email verification status:', error);
            });
        }
    </script>
</body>
</html>
