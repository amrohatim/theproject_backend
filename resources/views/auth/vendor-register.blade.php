<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Register as a Vendor on glowlabs - Join our marketplace as a retail seller">
    <meta name="robots" content="noindex, nofollow">

    <title>Vendor Registration - glowlabs</title>

    <!-- Preconnect to external domains for performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Modern Typography - Inter font for clean, modern look -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        /* Modern Registration Form Styles */
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
            line-height: 1.6;
        }

        .registration-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 480px;
            padding: 40px;
            position: relative;
            overflow: hidden;
        }

        .registration-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
        }

        .logo-section {
            text-align: center;
            margin-bottom: 32px;
        }

        .logo {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
        }

        .logo i {
            color: white;
            font-size: 28px;
        }

        .welcome-text {
            color: #1a1a1a;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .subtitle {
            color: #6b7280;
            font-size: 16px;
            font-weight: 400;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            color: #374151;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-input {
            width: 100%;
            padding: 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 400;
            background: #f9fafb;
            transition: all 0.2s ease;
            color: #1f2937;
        }

        .form-input:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-input.error {
            border-color: #ef4444;
            background: #fef2f2;
        }

        .password-container {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6b7280;
            cursor: pointer;
            padding: 4px;
            border-radius: 4px;
            transition: color 0.2s ease;
        }

        .password-toggle:hover {
            color: #374151;
        }

        .error-message {
            color: #ef4444;
            font-size: 14px;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .password-strength {
            margin-top: 8px;
        }

        .strength-bar {
            height: 4px;
            background: #e5e7eb;
            border-radius: 2px;
            overflow: hidden;
            margin-bottom: 8px;
        }

        .strength-fill {
            height: 100%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .strength-weak { background: #ef4444; width: 25%; }
        .strength-fair { background: #f59e0b; width: 50%; }
        .strength-good { background: #10b981; width: 75%; }
        .strength-strong { background: #059669; width: 100%; }

        .strength-text {
            font-size: 12px;
            color: #6b7280;
        }

        .requirements-list {
            list-style: none;
            margin-top: 8px;
        }

        .requirements-list li {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .requirements-list li.valid {
            color: #10b981;
        }

        .requirements-list li i {
            font-size: 10px;
        }

        .checkbox-container {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 16px;
            background: #f9fafb;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            margin-bottom: 24px;
        }

        .checkbox {
            width: 18px;
            height: 18px;
            accent-color: #667eea;
            margin-top: 2px;
        }

        .checkbox-label {
            font-size: 14px;
            color: #374151;
            line-height: 1.5;
        }

        .checkbox-label a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .checkbox-label a:hover {
            text-decoration: underline;
        }

        .submit-btn {
            width: 100%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 16px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            text-transform: none;
            letter-spacing: 0;
        }

        .submit-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .login-link {
            text-align: center;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid #e5e7eb;
        }

        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        /* Loading State */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
            margin-right: 8px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Responsive Design */
        @media (max-width: 640px) {
            body {
                padding: 16px;
            }

            .registration-container {
                padding: 24px;
            }

            .welcome-text {
                font-size: 24px;
            }

            .subtitle {
                font-size: 14px;
            }
        }

        /* Success Animation */
        .success-checkmark {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #10b981;
            margin: 0 auto 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: scaleIn 0.3s ease-out;
        }

        .success-checkmark i {
            color: white;
            font-size: 32px;
        }

        @keyframes scaleIn {
            from { transform: scale(0); }
            to { transform: scale(1); }
        }

        .success-message {
            text-align: center;
        }

        .success-title {
            font-size: 24px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 8px;
        }

        .success-subtitle {
            color: #6b7280;
            margin-bottom: 24px;
        }
    </style>
</head>
<body>
    <div class="registration-container">
        <!-- Logo Section -->
        <div class="logo-section">
            <div class="logo">
                <i class="fas fa-store"></i>
            </div>
            <h1 class="welcome-text">Join as Vendor</h1>
            <p class="subtitle">Start selling on glowlabs marketplace</p>
        </div>

        <!-- Registration Form -->
        <form id="vendorRegistrationForm" method="POST" action="{{ route('vendor.register') }}">
            @csrf

            <!-- Full Name -->
            <div class="form-group">
                <label for="name" class="form-label">Full Name *</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    class="form-input"
                    placeholder="Enter your full name"
                    value="{{ old('name') }}"
                    required
                >
                <div class="error-message" id="name-error"></div>
            </div>

            <!-- Email -->
            <div class="form-group">
                <label for="email" class="form-label">Email Address *</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-input"
                    placeholder="Enter your email address"
                    value="{{ old('email') }}"
                    required
                >
                <div class="error-message" id="email-error"></div>
            </div>

            <!-- Phone -->
            <div class="form-group">
                <label for="phone" class="form-label">Phone Number *</label>
                <input
                    type="tel"
                    id="phone"
                    name="phone"
                    class="form-input"
                    placeholder="+971 50 123 4567"
                    value="{{ old('phone') }}"
                    required
                >
                <div class="error-message" id="phone-error"></div>
            </div>

            <!-- Password -->
            <div class="form-group">
                <label for="password" class="form-label">Password *</label>
                <div class="password-container">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-input"
                        placeholder="Create a strong password"
                        required
                    >
                    <button type="button" class="password-toggle" data-target="password">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="error-message" id="password-error"></div>

                <!-- Password Strength Indicator -->
                <div class="password-strength" id="password-strength" style="display: none;">
                    <div class="strength-bar">
                        <div class="strength-fill" id="strength-fill"></div>
                    </div>
                    <div class="strength-text" id="strength-text"></div>
                    <ul class="requirements-list" id="requirements-list">
                        <li id="req-length"><i class="fas fa-times"></i> At least 8 characters</li>
                        <li id="req-uppercase"><i class="fas fa-times"></i> One uppercase letter</li>
                        <li id="req-lowercase"><i class="fas fa-times"></i> One lowercase letter</li>
                        <li id="req-number"><i class="fas fa-times"></i> One number</li>
                        <li id="req-special"><i class="fas fa-times"></i> One special character</li>
                    </ul>
                </div>
            </div>

            <!-- Confirm Password -->
            <div class="form-group">
                <label for="password_confirmation" class="form-label">Confirm Password *</label>
                <div class="password-container">
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        class="form-input"
                        placeholder="Confirm your password"
                        required
                    >
                    <button type="button" class="password-toggle" data-target="password_confirmation">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="error-message" id="password_confirmation-error"></div>
            </div>

            <!-- Terms and Conditions -->
            <div class="checkbox-container">
                <input type="checkbox" id="terms" name="terms" class="checkbox" required>
                <label for="terms" class="checkbox-label">
                    I agree to the <a href="#" target="_blank">Terms of Service</a> and
                    <a href="#" target="_blank">Privacy Policy</a>
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="submit-btn" id="submit-btn">
                <span id="btn-text">Create Vendor Account</span>
            </button>
        </form>

        <!-- Login Link -->
        <div class="login-link">
            <p>Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
        </div>
    </div>

    <!-- Modern JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initializeForm();
        });

        function initializeForm() {
            setupPasswordToggles();
            setupPasswordValidation();
            setupFormValidation();
            setupFormSubmission();
        }

        // Password toggle functionality
        function setupPasswordToggles() {
            document.querySelectorAll('.password-toggle').forEach(toggle => {
                toggle.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const targetInput = document.getElementById(targetId);
                    const icon = this.querySelector('i');

                    if (targetInput.type === 'password') {
                        targetInput.type = 'text';
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    } else {
                        targetInput.type = 'password';
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                });
            });
        }

        // Password strength validation
        function setupPasswordValidation() {
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('password_confirmation');
            const strengthIndicator = document.getElementById('password-strength');

            passwordInput.addEventListener('input', function() {
                const password = this.value;
                if (password.length > 0) {
                    strengthIndicator.style.display = 'block';
                    updatePasswordStrength(password);
                } else {
                    strengthIndicator.style.display = 'none';
                }
            });

            confirmPasswordInput.addEventListener('input', function() {
                validatePasswordMatch();
            });

            passwordInput.addEventListener('blur', function() {
                validatePasswordMatch();
            });
        }

        function updatePasswordStrength(password) {
            const requirements = {
                length: password.length >= 8,
                uppercase: /[A-Z]/.test(password),
                lowercase: /[a-z]/.test(password),
                number: /\d/.test(password),
                special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
            };

            // Update requirement indicators
            updateRequirement('req-length', requirements.length);
            updateRequirement('req-uppercase', requirements.uppercase);
            updateRequirement('req-lowercase', requirements.lowercase);
            updateRequirement('req-number', requirements.number);
            updateRequirement('req-special', requirements.special);

            // Calculate strength
            const score = Object.values(requirements).filter(Boolean).length;
            const strengthFill = document.getElementById('strength-fill');
            const strengthText = document.getElementById('strength-text');

            strengthFill.className = 'strength-fill';

            if (score < 2) {
                strengthFill.classList.add('strength-weak');
                strengthText.textContent = 'Weak password';
            } else if (score < 4) {
                strengthFill.classList.add('strength-fair');
                strengthText.textContent = 'Fair password';
            } else if (score < 5) {
                strengthFill.classList.add('strength-good');
                strengthText.textContent = 'Good password';
            } else {
                strengthFill.classList.add('strength-strong');
                strengthText.textContent = 'Strong password';
            }
        }

        function updateRequirement(id, isValid) {
            const element = document.getElementById(id);
            const icon = element.querySelector('i');

            if (isValid) {
                element.classList.add('valid');
                icon.className = 'fas fa-check';
            } else {
                element.classList.remove('valid');
                icon.className = 'fas fa-times';
            }
        }

        function validatePasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('password_confirmation').value;
            const errorElement = document.getElementById('password_confirmation-error');
            const confirmInput = document.getElementById('password_confirmation');

            if (confirmPassword && password !== confirmPassword) {
                showFieldError('password_confirmation', 'Passwords do not match');
            } else {
                clearFieldError('password_confirmation');
            }
        }

        // Form validation
        function setupFormValidation() {
            const inputs = document.querySelectorAll('.form-input');
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    validateField(this);
                });

                input.addEventListener('input', function() {
                    if (this.classList.contains('error')) {
                        clearFieldError(this.id);
                    }
                });
            });
        }

        function validateField(input) {
            const value = input.value.trim();
            const fieldName = input.name;

            switch (fieldName) {
                case 'name':
                    if (!value || value.length < 2) {
                        showFieldError(input.id, 'Name must be at least 2 characters');
                        return false;
                    }
                    break;
                case 'email':
                    if (!value || !isValidEmail(value)) {
                        showFieldError(input.id, 'Please enter a valid email address');
                        return false;
                    }
                    break;
                case 'phone':
                    if (!value || !isValidPhone(value)) {
                        showFieldError(input.id, 'Please enter a valid UAE phone number');
                        return false;
                    }
                    break;
                case 'password':
                    if (!value || value.length < 8) {
                        showFieldError(input.id, 'Password must be at least 8 characters');
                        return false;
                    }
                    break;
            }

            clearFieldError(input.id);
            return true;
        }

        function showFieldError(fieldId, message) {
            const input = document.getElementById(fieldId);
            const errorElement = document.getElementById(fieldId + '-error');

            input.classList.add('error');
            if (errorElement) {
                errorElement.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
            }
        }

        function clearFieldError(fieldId) {
            const input = document.getElementById(fieldId);
            const errorElement = document.getElementById(fieldId + '-error');

            input.classList.remove('error');
            if (errorElement) {
                errorElement.innerHTML = '';
            }
        }
        // Form submission
        function setupFormSubmission() {
            const form = document.getElementById('vendorRegistrationForm');
            const submitBtn = document.getElementById('submit-btn');
            const btnText = document.getElementById('btn-text');

            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                // Validate all fields
                const inputs = form.querySelectorAll('.form-input');
                let isValid = true;

                inputs.forEach(input => {
                    if (!validateField(input)) {
                        isValid = false;
                    }
                });

                // Check terms
                const termsCheckbox = document.getElementById('terms');
                if (!termsCheckbox.checked) {
                    alert('Please agree to the Terms of Service and Privacy Policy');
                    isValid = false;
                }

                if (!isValid) {
                    return;
                }

                // Show loading state
                submitBtn.disabled = true;
                btnText.innerHTML = '<span class="loading"></span>Creating Account...';

                try {
                    // Simulate form submission (replace with actual endpoint)
                    const formData = new FormData(form);

                    // For demo purposes, we'll show success after 2 seconds
                    // In production, replace this with actual API call
                    await new Promise(resolve => setTimeout(resolve, 2000));

                    showSuccessMessage();

                } catch (error) {
                    console.error('Registration error:', error);
                    alert('Registration failed. Please try again.');
                } finally {
                    submitBtn.disabled = false;
                    btnText.textContent = 'Create Vendor Account';
                }
            });
        }

        function showSuccessMessage() {
            const container = document.querySelector('.registration-container');
            container.innerHTML = `
                <div class="success-message">
                    <div class="success-checkmark">
                        <i class="fas fa-check"></i>
                    </div>
                    <h2 class="success-title">Account Created!</h2>
                    <p class="success-subtitle">Your vendor registration has been submitted for review. You'll receive an email confirmation shortly.</p>
                    <a href="{{ route('login') }}" class="submit-btn" style="display: inline-block; text-decoration: none; text-align: center; margin-top: 16px;">
                        Continue to Login
                    </a>
                </div>
            `;
        }

        // Utility functions
        function isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        }

        function isValidPhone(phone) {
            return /^(\+971|971|0)?[0-9]{9}$/.test(phone.replace(/\s/g, ''));
        }

    </script>

    <!-- Handle server-side validation errors -->
    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                @foreach ($errors->all() as $error)
                    console.error('{{ $error }}');
                @endforeach
            });
        </script>
    @endif
</body>
</html>
    </script>
</body>
</html>
