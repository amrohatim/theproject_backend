<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Register as a Provider on Dala3Chic - Join our marketplace as a wholesale supplier">
    <meta name="robots" content="noindex, nofollow">

    <title>Provider Registration - Dala3Chic</title>

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
        /* Modern Provider Registration Form Styles */
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
            line-height: 1.6;
        }

        .registration-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 520px;
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
            background: linear-gradient(90deg, #8b5cf6, #a855f7, #c084fc);
        }

        .logo-section {
            text-align: center;
            margin-bottom: 32px;
        }

        .logo {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #8b5cf6, #a855f7);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            box-shadow: 0 8px 16px rgba(139, 92, 246, 0.3);
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
            border-color: #8b5cf6;
            background: white;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
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

        .form-help {
            color: #6b7280;
            font-size: 12px;
            margin-top: 4px;
            display: block;
            line-height: 1.4;
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

        /* Delivery Options Styling */
        .delivery-options {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 20px;
        }

        .delivery-option {
            display: flex;
            align-items: center;
            padding: 12px;
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            margin-bottom: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .delivery-option:last-child {
            margin-bottom: 0;
        }

        .delivery-option:hover {
            border-color: #8b5cf6;
            background: #faf5ff;
        }

        .delivery-option input[type="radio"] {
            width: 18px;
            height: 18px;
            accent-color: #8b5cf6;
            margin-right: 12px;
        }

        .delivery-option-content h4 {
            font-size: 16px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 4px;
        }

        .delivery-option-content p {
            font-size: 14px;
            color: #6b7280;
        }

        /* File Upload Styling */
        .file-upload-area {
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            padding: 24px;
            text-align: center;
            background: #f9fafb;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .file-upload-area:hover {
            border-color: #8b5cf6;
            background: #faf5ff;
        }

        .file-upload-area.dragover {
            border-color: #8b5cf6;
            background: #faf5ff;
        }

        .upload-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #8b5cf6, #a855f7);
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
        }

        .upload-icon i {
            color: white;
            font-size: 20px;
        }

        .upload-text {
            font-size: 16px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 4px;
        }

        .upload-subtext {
            font-size: 14px;
            color: #6b7280;
        }

        .file-preview {
            display: none;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            margin-top: 12px;
        }

        .file-preview.show {
            display: flex;
        }

        .file-preview-icon {
            width: 40px;
            height: 40px;
            background: #10b981;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .file-preview-icon i {
            color: white;
            font-size: 16px;
        }

        .file-preview-info {
            flex: 1;
        }

        .file-preview-name {
            font-size: 14px;
            font-weight: 600;
            color: #065f46;
        }

        .file-preview-size {
            font-size: 12px;
            color: #047857;
        }

        .file-remove {
            background: none;
            border: none;
            color: #ef4444;
            cursor: pointer;
            padding: 4px;
            border-radius: 4px;
            transition: background 0.2s ease;
        }

        .file-remove:hover {
            background: #fee2e2;
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
            accent-color: #8b5cf6;
            margin-top: 2px;
        }

        .checkbox-label {
            font-size: 14px;
            color: #374151;
            line-height: 1.5;
        }

        .checkbox-label a {
            color: #8b5cf6;
            text-decoration: none;
            font-weight: 600;
        }

        .checkbox-label a:hover {
            text-decoration: underline;
        }

        .submit-btn {
            width: 100%;
            background: linear-gradient(135deg, #8b5cf6, #a855f7);
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
            box-shadow: 0 8px 16px rgba(139, 92, 246, 0.3);
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
            color: #8b5cf6;
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

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            backdrop-filter: blur(4px);
        }

        .modal-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            max-width: 500px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            animation: modalSlideIn 0.3s ease-out;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-20px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .modal-header {
            padding: 24px 24px 16px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header.success-header {
            border-bottom-color: #d1fae5;
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        }

        .modal-title {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0;
        }

        .modal-title i {
            color: #ef4444;
        }

        .success-header .modal-title i {
            color: #10b981;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 18px;
            color: #6b7280;
            cursor: pointer;
            padding: 4px;
            border-radius: 4px;
            transition: all 0.2s;
        }

        .modal-close:hover {
            background-color: #f3f4f6;
            color: #374151;
        }

        .modal-body {
            padding: 16px 24px;
        }

        .modal-description {
            color: #6b7280;
            margin-bottom: 16px;
            line-height: 1.5;
        }

        .error-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .error-list li {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 8px;
            color: #dc2626;
            display: flex;
            align-items: flex-start;
            gap: 8px;
            font-size: 14px;
        }

        .error-list li:last-child {
            margin-bottom: 0;
        }

        .error-list li i {
            margin-top: 2px;
            flex-shrink: 0;
        }

        .modal-footer {
            padding: 16px 24px 24px;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        .modal-btn {
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            font-size: 14px;
        }

        .modal-btn-primary {
            background: #3b82f6;
            color: white;
        }

        .modal-btn-primary:hover {
            background: #2563eb;
        }

        .modal-btn-success {
            background: #10b981;
            color: white;
        }

        .modal-btn-success:hover {
            background: #059669;
        }

        /* Responsive modal */
        @media (max-width: 640px) {
            .modal-container {
                width: 95%;
                margin: 20px;
            }

            .modal-header,
            .modal-body,
            .modal-footer {
                padding-left: 16px;
                padding-right: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="registration-container">
        <!-- Logo Section -->
        <div class="logo-section">
            <div class="logo">
                <i class="fas fa-warehouse"></i>
            </div>
            <h1 class="welcome-text">Join as Provider</h1>
            <p class="subtitle">Supply products to Dala3Chic marketplace</p>
        </div>

        <!-- Registration Form -->
        <form id="providerRegistrationForm" method="POST" action="{{ route('register.provider.submit') }}" enctype="multipart/form-data">
            @csrf

            <!-- Company Name -->
            <div class="form-group">
                <label for="name" class="form-label">@lang('messages.company_supplier_name') *</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    class="form-input"
                    placeholder="@lang('messages.enter_company_supplier_name')"
                    value="{{ old('name') }}"
                    required
                    minlength="2"
                    maxlength="255"
                    aria-describedby="name-error"
                    autocomplete="organization"
                >
                <div class="error-message" id="name-error" role="alert" aria-live="polite"></div>
            </div>

            <!-- Business Name -->
            <div class="form-group">
                <label for="business_name" class="form-label">Business Name *</label>
                <input
                    type="text"
                    id="business_name"
                    name="business_name"
                    class="form-input"
                    placeholder="Enter your business name (as registered)"
                    value="{{ old('business_name') }}"
                    required
                    minlength="2"
                    maxlength="255"
                    aria-describedby="business_name-error business_name-help"
                    autocomplete="organization"
                >
                <small id="business_name-help" class="form-help">This should match your official business registration name</small>
                <div class="error-message" id="business_name-error" role="alert" aria-live="polite"></div>
            </div>

            <!-- Email -->
            <div class="form-group">
                <label for="email" class="form-label">Email Address *</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-input"
                    placeholder="Enter your business email address"
                    value="{{ old('email') }}"
                    required
                    maxlength="255"
                    aria-describedby="email-error"
                    autocomplete="email"
                    pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                    title="Please enter a valid email address"
                >
                <div class="error-message" id="email-error" role="alert" aria-live="polite"></div>
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
                    maxlength="20"
                    aria-describedby="phone-error"
                    autocomplete="tel"
                    pattern="(\+971|971|0)?[0-9]{9}"
                    title="Please enter a valid UAE phone number (e.g., +971501234567)"
                >
                <div class="error-message" id="phone-error" role="alert" aria-live="polite"></div>
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
                        minlength="8"
                        aria-describedby="password-error password-requirements"
                        autocomplete="new-password"
                        title="Password must be at least 8 characters long"
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
                        minlength="8"
                        aria-describedby="password_confirmation-error"
                        autocomplete="new-password"
                        title="Please confirm your password"
                    >
                    <button type="button" class="password-toggle" data-target="password_confirmation">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="error-message" id="password_confirmation-error" role="alert" aria-live="polite"></div>
            </div>

            <!-- Delivery Options -->
            <div class="form-group">
                <fieldset>
                    <legend class="form-label">Supply & Delivery Options *</legend>
                    <div class="delivery-options" role="radiogroup" aria-required="true" aria-describedby="delivery-error">
                        <div class="delivery-option">
                            <input type="radio" id="pickup_only" name="delivery_capability" value="pickup_only" checked aria-describedby="pickup_only_desc">
                            <div class="delivery-option-content">
                                <h4>Pickup Only</h4>
                                <p id="pickup_only_desc">Vendors collect products from your warehouse</p>
                            </div>
                        </div>
                        <div class="delivery-option">
                            <input type="radio" id="delivery_available" name="delivery_capability" value="delivery_available" aria-describedby="delivery_available_desc">
                            <div class="delivery-option-content">
                                <h4>Delivery Available</h4>
                                <p id="delivery_available_desc">You deliver products to vendors</p>
                            </div>
                        </div>
                        <div class="delivery-option">
                            <input type="radio" id="both_options" name="delivery_capability" value="both" aria-describedby="both_options_desc">
                            <div class="delivery-option-content">
                                <h4>Both Options</h4>
                                <p id="both_options_desc">Pickup and delivery available</p>
                            </div>
                        </div>
                    </div>
                    <div class="error-message" id="delivery-error" role="alert" aria-live="polite"></div>
                </fieldset>
            </div>

            <!-- Company Logo Upload -->
            <div class="form-group">
                <label for="logo" class="form-label">Company Logo (Optional)</label>
                <div class="file-upload-area" id="file-upload-area">
                    <input
                        type="file"
                        id="logo"
                        name="logo"
                        accept="image/jpeg,image/png,image/jpg,image/gif"
                        style="display: none;"
                        aria-describedby="logo-error logo-help"
                    >
                    <div class="upload-icon">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <div class="upload-text">Click to upload company logo</div>
                    <div class="upload-subtext">PNG, JPG up to 2MB • Recommended: 400x400px</div>
                </div>
                <div class="file-preview" id="file-preview">
                    <div class="file-preview-icon">
                        <i class="fas fa-image"></i>
                    </div>
                    <div class="file-preview-info">
                        <div class="file-preview-name" id="file-name"></div>
                        <div class="file-preview-size" id="file-size"></div>
                    </div>
                    <button type="button" class="file-remove" id="file-remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="error-message" id="logo-error"></div>
            </div>

            <!-- Terms and Conditions -->
            <div class="checkbox-container">
                <input
                    type="checkbox"
                    id="terms"
                    name="terms"
                    class="checkbox"
                    required
                    aria-describedby="terms-error"
                    aria-required="true"
                >
                <label for="terms" class="checkbox-label">
                    I agree to the <a href="#" target="_blank" rel="noopener">Terms of Service</a> and
                    <a href="#" target="_blank" rel="noopener">Privacy Policy</a>
                </label>
                <div class="error-message" id="terms-error" role="alert" aria-live="polite"></div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="submit-btn" id="submit-btn">
                <span id="btn-text">Create Provider Account</span>
            </button>
        </form>

        <!-- Login Link -->
        <div class="login-link">
            <p>Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
        </div>
    </div>

    <!-- Validation Error Modal -->
    <div id="validationErrorModal" class="modal-overlay" style="display: none;">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">
                    <i class="fas fa-exclamation-triangle"></i>
                    Validation Errors
                </h3>
                <button type="button" class="modal-close" onclick="closeValidationErrorModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <p class="modal-description">Please fix the following errors before submitting:</p>
                <ul id="validationErrorList" class="error-list"></ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="modal-btn modal-btn-primary" onclick="closeValidationErrorModal()">
                    Fix Errors
                </button>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="modal-overlay" style="display: none;">
        <div class="modal-container">
            <div class="modal-header success-header">
                <h3 class="modal-title">
                    <i class="fas fa-check-circle"></i>
                    Registration Successful
                </h3>
            </div>
            <div class="modal-body">
                <p class="modal-description">Your provider registration has been submitted successfully. You will receive an email confirmation shortly.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="modal-btn modal-btn-success" onclick="redirectToLogin()">
                    Continue to Login
                </button>
            </div>
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
            setupFileUpload();
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

            if (confirmPassword && password !== confirmPassword) {
                showFieldError('password_confirmation', 'Passwords do not match');
            } else {
                clearFieldError('password_confirmation');
            }
        }

        // File upload functionality
        function setupFileUpload() {
            const fileUploadArea = document.getElementById('file-upload-area');
            const fileInput = document.getElementById('logo');
            const filePreview = document.getElementById('file-preview');
            const fileName = document.getElementById('file-name');
            const fileSize = document.getElementById('file-size');
            const fileRemove = document.getElementById('file-remove');

            // Click to upload
            fileUploadArea.addEventListener('click', function() {
                fileInput.click();
            });

            // Drag and drop
            fileUploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('dragover');
            });

            fileUploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');
            });

            fileUploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    handleFileSelect(files[0]);
                }
            });

            // File input change
            fileInput.addEventListener('change', function(e) {
                if (e.target.files.length > 0) {
                    handleFileSelect(e.target.files[0]);
                }
            });

            // Remove file
            fileRemove.addEventListener('click', function(e) {
                e.stopPropagation();
                fileInput.value = '';
                filePreview.classList.remove('show');
                clearFieldError('logo');
            });

            function handleFileSelect(file) {
                // Validate file
                if (!file.type.startsWith('image/')) {
                    showFieldError('logo', 'Please select an image file');
                    return;
                }

                if (file.size > 2 * 1024 * 1024) { // 2MB
                    showFieldError('logo', 'File size must be less than 2MB');
                    return;
                }

                // Show preview
                fileName.textContent = file.name;
                fileSize.textContent = formatFileSize(file.size);
                filePreview.classList.add('show');
                clearFieldError('logo');
            }

            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
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
                    if (!value) {
                        showFieldError(input.id, 'Company/supplier name is required');
                        return false;
                    }
                    if (value.length < 2) {
                        showFieldError(input.id, 'Company/supplier name must be at least 2 characters');
                        return false;
                    }
                    if (value.length > 255) {
                        showFieldError(input.id, 'Company/supplier name cannot exceed 255 characters');
                        return false;
                    }
                    break;

                case 'business_name':
                    if (!value) {
                        showFieldError(input.id, 'Business name is required');
                        return false;
                    }
                    if (value.length < 2) {
                        showFieldError(input.id, 'Business name must be at least 2 characters');
                        return false;
                    }
                    if (value.length > 255) {
                        showFieldError(input.id, 'Business name cannot exceed 255 characters');
                        return false;
                    }
                    // Check business name uniqueness (async validation)
                    validateBusinessNameUniqueness(input, value);
                    break;

                case 'email':
                    if (!value) {
                        showFieldError(input.id, 'Email address is required');
                        return false;
                    }
                    if (!isValidEmail(value)) {
                        showFieldError(input.id, 'Please enter a valid email address');
                        return false;
                    }
                    if (value.length > 255) {
                        showFieldError(input.id, 'Email address cannot exceed 255 characters');
                        return false;
                    }
                    // Check email registration status (async validation)
                    validateEmailRegistrationStatus(input, value);
                    break;

                case 'phone':
                    if (!value) {
                        showFieldError(input.id, 'Phone number is required');
                        return false;
                    }
                    if (!isValidPhone(value)) {
                        showFieldError(input.id, 'Please enter a valid UAE phone number (+971XXXXXXXXX)');
                        return false;
                    }
                    if (value.length > 20) {
                        showFieldError(input.id, 'Phone number cannot exceed 20 characters');
                        return false;
                    }
                    // Check phone registration status (async validation)
                    validatePhoneRegistrationStatus(input, value);
                    break;

                case 'password':
                    if (!value) {
                        showFieldError(input.id, 'Password is required');
                        return false;
                    }
                    if (value.length < 8) {
                        showFieldError(input.id, 'Password must be at least 8 characters');
                        return false;
                    }
                    // Check password strength
                    const passwordStrength = checkPasswordStrength(value);
                    if (!passwordStrength.isValid) {
                        showFieldError(input.id, passwordStrength.message);
                        return false;
                    }
                    break;

                case 'password_confirmation':
                    const passwordField = document.getElementById('password');
                    if (!value) {
                        showFieldError(input.id, 'Password confirmation is required');
                        return false;
                    }
                    if (value !== passwordField.value) {
                        showFieldError(input.id, 'Password confirmation does not match');
                        return false;
                    }
                    break;

                case 'description':
                    if (value && value.length > 1000) {
                        showFieldError(input.id, 'Description cannot exceed 1000 characters');
                        return false;
                    }
                    break;
            }

            clearFieldError(input.id);
            return true;
        }

        // Async validation functions
        let validationTimeouts = {};

        function validateBusinessNameUniqueness(input, businessName) {
            // Clear previous timeout
            if (validationTimeouts.business_name) {
                clearTimeout(validationTimeouts.business_name);
            }

            // Debounce the validation
            validationTimeouts.business_name = setTimeout(async () => {
                try {
                    const response = await fetch('/api/validate/business-name', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ business_name: businessName })
                    });

                    const data = await response.json();

                    if (!data.available) {
                        showFieldError(input.id, 'Business name is already taken');
                    } else {
                        clearFieldError(input.id);
                    }
                } catch (error) {
                    console.error('Business name validation error:', error);
                }
            }, 500);
        }

        function validateEmailRegistrationStatus(input, email) {
            // Clear previous timeout
            if (validationTimeouts.email) {
                clearTimeout(validationTimeouts.email);
            }

            // Debounce the validation
            validationTimeouts.email = setTimeout(async () => {
                try {
                    const response = await fetch('/api/validate/email-status', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ email: email })
                    });

                    const data = await response.json();

                    if (!data.available) {
                        showFieldError(input.id, data.message);
                    } else {
                        clearFieldError(input.id);
                    }
                } catch (error) {
                    console.error('Email validation error:', error);
                }
            }, 500);
        }

        function validatePhoneRegistrationStatus(input, phone) {
            // Clear previous timeout
            if (validationTimeouts.phone) {
                clearTimeout(validationTimeouts.phone);
            }

            // Debounce the validation
            validationTimeouts.phone = setTimeout(async () => {
                try {
                    const response = await fetch('/api/validate/phone-status', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ phone: phone })
                    });

                    const data = await response.json();

                    if (!data.available) {
                        showFieldError(input.id, data.message);
                    } else {
                        clearFieldError(input.id);
                    }
                } catch (error) {
                    console.error('Phone validation error:', error);
                }
            }, 500);
        }

        function checkPasswordStrength(password) {
            const minLength = 8;
            const hasUpperCase = /[A-Z]/.test(password);
            const hasLowerCase = /[a-z]/.test(password);
            const hasNumbers = /\d/.test(password);
            const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);

            if (password.length < minLength) {
                return { isValid: false, message: 'Password must be at least 8 characters long' };
            }

            let strength = 0;
            if (hasUpperCase) strength++;
            if (hasLowerCase) strength++;
            if (hasNumbers) strength++;
            if (hasSpecialChar) strength++;

            if (strength < 2) {
                return {
                    isValid: false,
                    message: 'Password must contain at least 2 of: uppercase letters, lowercase letters, numbers, special characters'
                };
            }

            return { isValid: true, message: 'Password strength is good' };
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
            const form = document.getElementById('providerRegistrationForm');
            const submitBtn = document.getElementById('submit-btn');
            const btnText = document.getElementById('btn-text');

            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                // Clear any previous validation timeouts
                Object.values(validationTimeouts).forEach(timeout => clearTimeout(timeout));

                // Validate all fields synchronously first
                const inputs = form.querySelectorAll('.form-input');
                let isValid = true;
                let validationErrors = [];

                inputs.forEach(input => {
                    if (!validateField(input)) {
                        isValid = false;
                        const errorElement = document.getElementById(input.id + '-error');
                        if (errorElement && errorElement.innerHTML) {
                            validationErrors.push({
                                field: input.name || input.id,
                                message: errorElement.textContent.replace('⚠', '').trim()
                            });
                        }
                    }
                });

                // Validate business name field specifically
                const businessNameField = form.querySelector('input[name="business_name"]');
                if (businessNameField && businessNameField.value.trim()) {
                    if (!validateField(businessNameField)) {
                        isValid = false;
                    }
                }

                // Check delivery capability
                const deliveryCapability = form.querySelector('input[name="delivery_capability"]:checked');
                if (!deliveryCapability) {
                    validationErrors.push({
                        field: 'delivery_capability',
                        message: 'Please select a delivery option'
                    });
                    isValid = false;
                }

                // Check terms
                const termsCheckbox = document.getElementById('terms');
                if (!termsCheckbox || !termsCheckbox.checked) {
                    validationErrors.push({
                        field: 'terms',
                        message: 'Please agree to the Terms of Service and Privacy Policy'
                    });
                    isValid = false;
                }

                if (!isValid) {
                    showValidationErrorModal(validationErrors);
                    return;
                }

                // Show loading state
                submitBtn.disabled = true;
                btnText.innerHTML = '<span class="loading"></span>Creating Account...';

                try {
                    // Submit form to server
                    const formData = new FormData(form);

                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });

                    const result = await response.json();

                    if (response.ok && result.success) {
                        showSuccessModal();
                    } else {
                        // Handle server validation errors
                        if (result.errors) {
                            const serverErrors = [];
                            Object.keys(result.errors).forEach(field => {
                                result.errors[field].forEach(message => {
                                    serverErrors.push({ field, message });
                                });
                            });
                            showValidationErrorModal(serverErrors);
                        } else {
                            showValidationErrorModal([{
                                field: 'general',
                                message: result.message || 'Registration failed. Please try again.'
                            }]);
                        }
                    }

                } catch (error) {
                    console.error('Registration error:', error);
                    showValidationErrorModal([{
                        field: 'general',
                        message: 'Network error. Please check your connection and try again.'
                    }]);
                } finally {
                    submitBtn.disabled = false;
                    btnText.textContent = '@lang('messages.create_provider_account')';
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
                    <h2 class="success-title">@lang('messages.account_created')</h2>
                    <p class="success-subtitle">@lang('messages.provider_registration_submitted')</p>
                    <a href="{{ route('login') }}" class="submit-btn" style="display: inline-block; text-decoration: none; text-align: center; margin-top: 16px;">
                        @lang('messages.continue_to_login')
                    </a>
                </div>
            `;
        }

        // Modal functions
        function showValidationErrorModal(errors) {
            const modal = document.getElementById('validationErrorModal');
            const errorList = document.getElementById('validationErrorList');

            // Clear previous errors
            errorList.innerHTML = '';

            // Add each error to the list
            errors.forEach(error => {
                const li = document.createElement('li');
                li.innerHTML = `
                    <i class="fas fa-exclamation-circle"></i>
                    <div>
                        <strong>${getFieldDisplayName(error.field)}:</strong> ${error.message}
                    </div>
                `;
                errorList.appendChild(li);
            });

            // Show modal
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';

            // Focus first error field
            if (errors.length > 0) {
                const firstErrorField = document.getElementById(errors[0].field) ||
                                      document.querySelector(`[name="${errors[0].field}"]`);
                if (firstErrorField) {
                    setTimeout(() => {
                        firstErrorField.focus();
                        firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }, 300);
                }
            }
        }

        function closeValidationErrorModal() {
            const modal = document.getElementById('validationErrorModal');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function showSuccessModal() {
            const modal = document.getElementById('successModal');
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function redirectToLogin() {
            window.location.href = '{{ route("login") }}';
        }

        function getFieldDisplayName(fieldName) {
            const fieldNames = {
                'name': 'Company/Supplier Name',
                'business_name': 'Business Name',
                'email': 'Email Address',
                'phone': 'Phone Number',
                'password': 'Password',
                'password_confirmation': 'Password Confirmation',
                'description': 'Description',
                'delivery_capability': 'Delivery Capability',
                'terms': 'Terms and Conditions'
            };

            return fieldNames[fieldName] || fieldName.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal-overlay')) {
                closeValidationErrorModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeValidationErrorModal();
            }
        });

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
