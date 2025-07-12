<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Provider Registration Step 1 - Basic Information">
    <meta name="robots" content="noindex, nofollow">
    <title>Provider Registration - Step 1 | Dala3Chic</title>
    
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
            max-width: 600px;
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

        .progress-step.active::after {
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

        .form-section {
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 16px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }

        .section-title i {
            margin-right: 8px;
            color: #7c3aed;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            color: #374151;
            font-size: 14px;
        }

        .form-input, .form-select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #f9fafb;
        }

        .form-input:focus, .form-select:focus {
            outline: none;
            border-color: #7c3aed;
            background: white;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
        }

        .form-help {
            font-size: 12px;
            color: #6b7280;
            margin-top: 5px;
            line-height: 1.4;
        }

        textarea.form-input {
            resize: vertical;
            min-height: 100px;
        }

        .password-container {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6b7280;
            cursor: pointer;
            padding: 4px;
        }

        .password-toggle:hover {
            color: #374151;
        }

        .file-upload {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .file-upload-input {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-upload-label {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            background: #f9fafb;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }

        .file-upload-label:hover {
            border-color: #7c3aed;
            background: #faf5ff;
        }

        .file-upload-label i {
            margin-right: 8px;
            color: #6b7280;
        }

        .file-upload-text {
            color: #6b7280;
            font-size: 14px;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .checkbox-input {
            margin-right: 12px;
            width: 18px;
            height: 18px;
            accent-color: #7c3aed;
        }

        .checkbox-label {
            font-size: 14px;
            color: #374151;
            cursor: pointer;
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

        /* Enhanced error message styles */
        .error-message {
            color: #ef4444;
            font-size: 14px;
            margin-top: 4px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .form-input.error {
            border-color: #ef4444;
            background-color: #fef2f2;
        }

        .form-help {
            color: #6b7280;
            font-size: 12px;
            margin-top: 4px;
            display: block;
            line-height: 1.4;
        }

        .form-button:disabled {
            background: #9ca3af;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
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

        .error-message {
            color: #ef4444;
            font-size: 12px;
            margin-top: 4px;
        }

        .delivery-section {
            background: #faf5ff;
            border: 1px solid #e9d5ff;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .emirate-fee {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px 12px;
            background: white;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
        }

        .emirate-fee:last-child {
            margin-bottom: 0;
        }

        .emirate-name {
            font-weight: 500;
            color: #374151;
        }

        .fee-input {
            width: 100px;
            padding: 6px 8px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            font-size: 13px;
        }

        /* Enhanced Phone Input Styles */
        .phone-input-container {
            display: flex;
            align-items: center;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            background: #f9fafb;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .phone-input-container:focus-within {
            border-color: #8b5cf6;
            background: white;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
        }

        .phone-input-container:hover:not(:focus-within) {
            border-color: #d1d5db;
            background: white;
        }

        .country-code-section {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            background: #f3f4f6;
            border-right: 1px solid #e5e7eb;
            gap: 8px;
            min-width: 100px;
            flex-shrink: 0;
        }

        .uae-flag {
            font-size: 20px;
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 18px;
            border-radius: 2px;
            overflow: hidden;
        }

        .country-code {
            font-weight: 600;
            color: #374151;
            font-size: 14px;
            user-select: none;
        }

        .phone-number-input {
            flex: 1;
            border: none;
            outline: none;
            padding: 12px 16px;
            font-size: 14px;
            background: transparent;
            color: #1f2937;
            font-weight: 400;
        }

        .phone-number-input::placeholder {
            color: #9ca3af;
        }

        .phone-input-container.error {
            border-color: #ef4444;
            background: #fef2f2;
        }

        .phone-input-container.error .country-code-section {
            background: #fee2e2;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }
            
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
        }
    </style>
</head>
<body>
    <div class="registration-container">
        <a href="{{ route('register') }}" class="back-link">
            <i class="fas fa-arrow-left"></i>
            Back to Registration Options
        </a>

        <!-- Progress Bar -->
        <div class="progress-bar">
            <div class="progress-step active">
                <div class="step-circle">1</div>
                <div class="step-label">Provider Info</div>
            </div>
            <div class="progress-step">
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
            <h2 class="form-title">Provider Registration</h2>
            <p class="form-subtitle">Step 1: Enter your provider information</p>
        </div>

        <form id="providerStep1Form" method="POST" action="/api/provider/register/validate-info" enctype="multipart/form-data">
            @csrf
            
            <!-- Basic Information Section -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-user"></i>
                    Basic Information
                </h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="name" class="form-label">Full Name *</label>
                        <input type="text" id="name" name="name" class="form-input" required
                               value="{{ old('name') }}" placeholder="Enter your full name"
                               minlength="2" maxlength="255"
                               aria-describedby="name-error" autocomplete="name">
                        @error('name')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address *</label>
                        <input type="email" id="email" name="email" class="form-input" required
                               value="{{ old('email') }}" placeholder="Enter your email address"
                               maxlength="255" aria-describedby="email-error" autocomplete="email"
                               pattern="[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$"
                               title="Please enter a valid email address">
                        @error('email')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="phone" class="form-label">Phone Number *</label>
                        <div class="phone-input-container">
                            <div class="country-code-section">
                                <div class="uae-flag">🇦🇪</div>
                                <span class="country-code">+971</span>
                            </div>
                            <input type="tel" id="phone-display" class="phone-number-input"
                                   value=""
                                   placeholder="50 123 4567"
                                   maxlength="11" aria-describedby="phone-error" autocomplete="tel"
                                   pattern="[0-9]{9}"
                                   title="Please enter a valid 9-digit UAE phone number">
                            <input type="hidden" id="phone" name="phone" class="form-input" value="{{ old('phone', '') }}" required>
                        </div>
                        @error('phone')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="logo" class="form-label">Company Logo</label>
                        <div class="file-upload">
                            <input type="file" id="logo" name="logo" class="file-upload-input" 
                                   accept="image/jpeg,image/png,image/jpg,image/gif">
                            <label for="logo" class="file-upload-label">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <div class="file-upload-text">Upload Logo</div>
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="password" class="form-label">Password *</label>
                        <div class="password-container">
                            <input type="password" id="password" name="password" class="form-input" required
                                   placeholder="Create a strong password" minlength="8"
                                   aria-describedby="password-error" autocomplete="new-password"
                                   title="Password must be at least 8 characters long">
                            <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                <i class="fas fa-eye" id="password-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">Confirm Password *</label>
                        <div class="password-container">
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   class="form-input" required placeholder="Confirm your password"
                                   minlength="8" aria-describedby="password_confirmation-error"
                                   autocomplete="new-password" title="Please confirm your password">
                            <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')">
                                <i class="fas fa-eye" id="password_confirmation-eye"></i>
                            </button>
                        </div>
                        @error('password_confirmation')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Business Information Section -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-building"></i>
                    Business Information
                </h3>

                <div class="form-row">
                    <div class="form-group">
                        <label for="business_name" class="form-label">Business Name *</label>
                        <input type="text" id="business_name" name="business_name" class="form-input" required
                               value="{{ old('business_name') }}" placeholder="Enter your business name"
                               minlength="2" maxlength="255" aria-describedby="business_name-error business_name-help"
                               autocomplete="organization">
                        <small id="business_name-help" class="form-help">This should match your official business registration name</small>
                        @error('business_name')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="business_type" class="form-label">Business Type *</label>
                        <select id="business_type" name="business_type" class="form-input" required
                                aria-describedby="business_type-error">
                            <option value="">Select business type</option>
                            <option value="Food & Beverages" {{ old('business_type') == 'Food & Beverages' ? 'selected' : '' }}>Food & Beverages</option>
                            <option value="Electronics" {{ old('business_type') == 'Electronics' ? 'selected' : '' }}>Electronics</option>
                            <option value="Fashion & Clothing" {{ old('business_type') == 'Fashion & Clothing' ? 'selected' : '' }}>Fashion & Clothing</option>
                            <option value="Health & Beauty" {{ old('business_type') == 'Health & Beauty' ? 'selected' : '' }}>Health & Beauty</option>
                            <option value="Home & Garden" {{ old('business_type') == 'Home & Garden' ? 'selected' : '' }}>Home & Garden</option>
                            <option value="Sports & Recreation" {{ old('business_type') == 'Sports & Recreation' ? 'selected' : '' }}>Sports & Recreation</option>
                            <option value="Automotive" {{ old('business_type') == 'Automotive' ? 'selected' : '' }}>Automotive</option>
                            <option value="Books & Media" {{ old('business_type') == 'Books & Media' ? 'selected' : '' }}>Books & Media</option>
                            <option value="Toys & Games" {{ old('business_type') == 'Toys & Games' ? 'selected' : '' }}>Toys & Games</option>
                            <option value="Services" {{ old('business_type') == 'Services' ? 'selected' : '' }}>Services</option>
                            <option value="Other" {{ old('business_type') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('business_type')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Business Description</label>
                    <textarea id="description" name="description" class="form-input" rows="4"
                              placeholder="Describe your business, products, and services...">{{ old('description') }}</textarea>
                    <div class="form-help">Tell customers about your business and what makes you unique.</div>
                    @error('description')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Delivery Configuration Section -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-truck"></i>
                    Delivery Configuration
                </h3>
                
                <div class="checkbox-group">
                    <input type="checkbox" id="delivery_capability" name="delivery_capability" 
                           class="checkbox-input" value="1">
                    <label for="delivery_capability" class="checkbox-label">
                        We offer delivery services to vendors
                    </label>
                </div>
                
                <div class="delivery-section" id="deliverySection" style="display: none;">
                    <h4 style="margin-bottom: 15px; color: #374151; font-weight: 600;">Delivery Fees by Emirate (AED)</h4>
                    
                    <div class="emirate-fee">
                        <span class="emirate-name">Abu Dhabi</span>
                        <input type="number" name="delivery_fee_abu_dhabi" class="fee-input" 
                               placeholder="0.00" step="0.01" min="0">
                    </div>
                    
                    <div class="emirate-fee">
                        <span class="emirate-name">Dubai</span>
                        <input type="number" name="delivery_fee_dubai" class="fee-input" 
                               placeholder="0.00" step="0.01" min="0">
                    </div>
                    
                    <div class="emirate-fee">
                        <span class="emirate-name">Sharjah</span>
                        <input type="number" name="delivery_fee_sharjah" class="fee-input" 
                               placeholder="0.00" step="0.01" min="0">
                    </div>
                    
                    <div class="emirate-fee">
                        <span class="emirate-name">Ajman</span>
                        <input type="number" name="delivery_fee_ajman" class="fee-input" 
                               placeholder="0.00" step="0.01" min="0">
                    </div>
                    
                    <div class="emirate-fee">
                        <span class="emirate-name">Umm Al Quwain</span>
                        <input type="number" name="delivery_fee_uaq" class="fee-input" 
                               placeholder="0.00" step="0.01" min="0">
                    </div>
                    
                    <div class="emirate-fee">
                        <span class="emirate-name">Ras Al Khaimah</span>
                        <input type="number" name="delivery_fee_rak" class="fee-input" 
                               placeholder="0.00" step="0.01" min="0">
                    </div>
                    
                    <div class="emirate-fee">
                        <span class="emirate-name">Fujairah</span>
                        <input type="number" name="delivery_fee_fujairah" class="fee-input" 
                               placeholder="0.00" step="0.01" min="0">
                    </div>
                </div>
            </div>

            <button type="submit" class="form-button" id="submitBtn">
                <div class="loading-spinner"></div>
                <span class="button-text">Continue to Verification</span>
            </button>
        </form>
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
                <p class="modal-description">Please fix the following errors before continuing:</p>
                <ul id="validationErrorList" class="error-list"></ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="modal-btn modal-btn-primary" onclick="closeValidationErrorModal()">
                    Fix Errors
                </button>
            </div>
        </div>
    </div>

    <script>


        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const eye = document.getElementById(fieldId + '-eye');
            
            if (field.type === 'password') {
                field.type = 'text';
                eye.classList.remove('fa-eye');
                eye.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                eye.classList.remove('fa-eye-slash');
                eye.classList.add('fa-eye');
            }
        }

        // Toggle delivery section
        document.getElementById('delivery_capability').addEventListener('change', function() {
            const deliverySection = document.getElementById('deliverySection');
            deliverySection.style.display = this.checked ? 'block' : 'none';
        });

        // File upload preview
        document.getElementById('logo').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const label = document.querySelector('.file-upload-label .file-upload-text');
            
            if (file) {
                label.textContent = file.name;
            }
        });

        // Enhanced Phone number formatting for new input structure
        const phoneDisplayInput = document.getElementById('phone-display');
        const phoneHiddenInput = document.getElementById('phone');
        const phoneContainer = document.querySelector('.phone-input-container');

        // Initialize phone input on page load
        function initializePhoneInput() {
            // Get the current display value (might be set from old() in HTML)
            let displayValue = phoneDisplayInput.value;
            const hiddenValue = phoneHiddenInput.value;

            // If we have a hidden value, use it as the source of truth
            if (hiddenValue && hiddenValue.startsWith('+971')) {
                const digits = hiddenValue.substring(4);
                if (digits.length === 9) {
                    let formatted = digits.substring(0, 2);
                    if (digits.length > 2) {
                        formatted += ' ' + digits.substring(2, 5);
                    }
                    if (digits.length > 5) {
                        formatted += ' ' + digits.substring(5, 9);
                    }
                    phoneDisplayInput.value = formatted;
                }
            } else if (displayValue) {
                // If we only have a display value, format it and update hidden
                const digits = displayValue.replace(/\D/g, '');
                if (digits.length === 9) {
                    let formatted = digits.substring(0, 2);
                    if (digits.length > 2) {
                        formatted += ' ' + digits.substring(2, 5);
                    }
                    if (digits.length > 5) {
                        formatted += ' ' + digits.substring(5, 9);
                    }
                    phoneDisplayInput.value = formatted;
                    phoneHiddenInput.value = '+971' + digits;
                }
            }
        }

        // Call initialization
        initializePhoneInput();

        phoneDisplayInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, ''); // Remove non-digits

            // Limit to 9 digits
            if (value.length > 9) {
                value = value.substring(0, 9);
            }

            // Format with spaces for readability (XX XXX XXXX)
            let formatted = '';
            if (value.length > 0) {
                formatted = value.substring(0, 2);
                if (value.length > 2) {
                    formatted += ' ' + value.substring(2, 5);
                }
                if (value.length > 5) {
                    formatted += ' ' + value.substring(5, 9);
                }
            }

            e.target.value = formatted;

            // Update the hidden phone field with +971 prefix for form submission
            // Only set the full phone if we have exactly 9 digits
            const fullPhone = value.length === 9 ? '+971' + value : '';
            phoneHiddenInput.value = fullPhone;

            console.log('Phone input updated:', {
                display: formatted,
                digits: value,
                hidden: fullPhone,
                length: value.length
            });

            // Clear any error states immediately when user types
            phoneContainer.classList.remove('error');
            clearFieldError('phone-display');
        });

        // Enhanced focus handling for new phone input
        phoneDisplayInput.addEventListener('focus', function(e) {
            // No need to add +971 as it's already displayed in the UI
            // Just ensure the container shows focus state
            phoneContainer.classList.add('focused');
        });

        phoneDisplayInput.addEventListener('blur', function(e) {
            phoneContainer.classList.remove('focused');
            // Trigger validation for the phone input
            validateField(phoneHiddenInput);
        });

        // Simplified input validation - just clear errors when user types
        phoneDisplayInput.addEventListener('input', function(e) {
            // Clear any error states immediately when user types
            phoneContainer.classList.remove('error');
            clearFieldError('phone-display');
        });

        // Validation timeouts for debouncing
        let validationTimeouts = {};

        // Real-time validation functions
        function validateField(input) {
            const value = input.value.trim();
            const fieldName = input.name;

            switch (fieldName) {
                case 'name':
                    if (!value) {
                        showFieldError(input.id, 'Full name is required');
                        return false;
                    }
                    if (value.length < 2) {
                        showFieldError(input.id, 'Full name must be at least 2 characters');
                        return false;
                    }
                    if (value.length > 255) {
                        showFieldError(input.id, 'Full name cannot exceed 255 characters');
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
                    // Skip async validation for now - backend will handle uniqueness
                    clearFieldError(input.id);
                    return true;
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
                    // Skip async validation for now - backend will handle registration status
                    clearFieldError(input.id);
                    return true;
                    break;

                case 'phone':
                    // Simple validation for the hidden phone input only
                    if (input.id === 'phone-display') {
                        return true; // Skip validation for display input
                    }

                    // Validate the hidden phone input - keep it simple
                    if (!value || value.length === 0) {
                        showFieldError('phone-display', 'Phone number is required');
                        phoneContainer.classList.add('error');
                        return false;
                    }

                    // Basic format check - should start with +971 and be 13 characters
                    if (!value.startsWith('+971') || value.length !== 13) {
                        showFieldError('phone-display', 'Please enter a valid 9-digit UAE phone number');
                        phoneContainer.classList.add('error');
                        return false;
                    }

                    // Additional check: ensure the remaining 9 digits are all numeric
                    const phoneDigits = value.substring(4); // Remove +971
                    if (!/^[0-9]{9}$/.test(phoneDigits)) {
                        showFieldError('phone-display', 'Please enter a valid 9-digit UAE phone number');
                        phoneContainer.classList.add('error');
                        return false;
                    }

                    // Remove error state if validation passes
                    phoneContainer.classList.remove('error');
                    clearFieldError('phone-display');
                    return true; // Return true immediately, skip async validation
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

                case 'business_type':
                    if (!value) {
                        showFieldError(input.id, 'Business type is required');
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

        // Enhanced form submission with proper validation
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('providerStep1Form');
            if (!form) {
                console.error('Form not found!');
                return;
            }

            form.addEventListener('submit', function(e) {
            e.preventDefault();

            console.log('Form submission started...');

            // Ensure phone field is properly set
            const phoneHidden = document.getElementById('phone');
            const phoneDisplay = document.getElementById('phone-display');

            if (phoneDisplay && phoneDisplay.value) {
                const digits = phoneDisplay.value.replace(/\D/g, '');
                console.log('Phone digits extracted:', digits);
                if (digits.length === 9) {
                    phoneHidden.value = '+971' + digits;
                    console.log('Phone hidden field set to:', phoneHidden.value);
                } else {
                    console.log('Invalid phone digits length:', digits.length);
                }
            }

            // Enhanced required field validation
            const requiredFields = [
                { id: 'name', name: 'Full Name' },
                { id: 'email', name: 'Email' },
                { id: 'password', name: 'Password' },
                { id: 'password_confirmation', name: 'Confirm Password' },
                { id: 'business_name', name: 'Business Name' },
                { id: 'business_type', name: 'Business Type' }
            ];

            let missingFields = [];

            requiredFields.forEach(field => {
                const input = document.getElementById(field.id);
                console.log(`Checking field ${field.id}:`, input ? input.value : 'not found');

                if (!input) {
                    console.log(`Field ${field.id} not found in DOM`);
                    missingFields.push(field.name);
                } else if (!input.value || !input.value.trim()) {
                    console.log(`Field ${field.id} is empty`);
                    missingFields.push(field.name);
                } else if (field.id === 'business_type' && input.value === 'Select business type') {
                    console.log('Business type not selected');
                    missingFields.push(field.name);
                }
            });

            // Enhanced phone validation
            if (!phoneHidden || !phoneHidden.value) {
                console.log('Phone hidden field is empty');
                missingFields.push('Phone Number');
            } else if (phoneHidden.value.length !== 13 || !phoneHidden.value.startsWith('+971')) {
                console.log('Phone format invalid:', phoneHidden.value);
                missingFields.push('Phone Number (invalid format)');
            }

            if (missingFields.length > 0) {
                console.log('Validation failed. Missing fields:', missingFields);
                showValidationErrorModal([{
                    field: 'general',
                    message: 'Please fill in all required fields: ' + missingFields.join(', ')
                }]);
                return;
            }

            console.log('All validation passed, submitting form...');

            const submitBtn = document.querySelector('button[type="submit"], .submit-btn, #submitBtn');
            if (submitBtn) {
                submitBtn.classList.add('loading');
                submitBtn.disabled = true;
                console.log('Submit button found and disabled');
            } else {
                console.log('Submit button not found, looking for Continue button');
                const continueBtn = document.querySelector('button:contains("Continue")');
                if (continueBtn) {
                    continueBtn.disabled = true;
                    console.log('Continue button disabled');
                }
            }

            const formData = new FormData(this);

            console.log('Form data created, checking contents...');
            for (let [key, value] of formData.entries()) {
                console.log(`FormData: ${key} = ${value}`);
            }

            // Collect delivery fees
            if (document.getElementById('delivery_capability') && document.getElementById('delivery_capability').checked) {
                const deliveryFees = {};
                document.querySelectorAll('.fee-input').forEach(input => {
                    const emirate = input.name.replace('delivery_fee_', '');
                    if (input.value) {
                        deliveryFees[emirate] = parseFloat(input.value);
                    }
                });
                formData.append('delivery_fee_by_emirate', JSON.stringify(deliveryFees));
                console.log('Delivery fees added:', deliveryFees);
            }

            // Ensure CSRF token is included in FormData
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            if (csrfToken) {
                formData.append('_token', csrfToken);
                console.log('CSRF token added:', csrfToken);
            } else {
                console.error('CSRF token not found!');
            }

            fetch('/api/provider/register/validate-info', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(async response => {
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Server returned non-JSON response');
                }

                const data = await response.json();

                if (response.ok && data.success) {
                    // Store registration token for step 2
                    if (data.registration_token) {
                        localStorage.setItem('provider_registration_token', data.registration_token);
                    }
                    window.location.href = '/register/provider/step2';
                } else if (response.status === 422) {
                    // Handle validation errors (422 Unprocessable Entity)
                    if (data.errors) {
                        const serverErrors = [];
                        Object.keys(data.errors).forEach(field => {
                            data.errors[field].forEach(message => {
                                serverErrors.push({ field, message });
                            });
                        });
                        showValidationErrorModal(serverErrors);
                    } else {
                        showValidationErrorModal([{
                            field: 'general',
                            message: data.message || 'Please check your input and try again.'
                        }]);
                    }
                } else {
                    // Handle other errors
                    showValidationErrorModal([{
                        field: 'general',
                        message: data.message || `Server error (${response.status}). Please try again.`
                    }]);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showValidationErrorModal([{
                    field: 'general',
                    message: 'Network error. Please check your connection and try again.'
                }]);
            })
            .finally(() => {
                submitBtn.classList.remove('loading');
                submitBtn.disabled = false;
            });
        });
        }); // Close DOMContentLoaded

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

        function getFieldDisplayName(fieldName) {
            const fieldNames = {
                'name': 'Full Name',
                'business_name': 'Business Name',
                'email': 'Email Address',
                'phone': 'Phone Number',
                'password': 'Password',
                'password_confirmation': 'Password Confirmation',
                'business_type': 'Business Type',
                'description': 'Description',
                'general': 'General Error'
            };

            return fieldNames[fieldName] || fieldName.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
        }

        function showFieldError(fieldId, message) {
            const input = document.getElementById(fieldId);
            let errorElement = document.getElementById(fieldId + '-error');

            // Handle phone input container error state
            if (fieldId === 'phone-display' || fieldId === 'phone') {
                phoneContainer.classList.add('error');
                // Use phone-error as the error element ID for consistency
                errorElement = document.getElementById('phone-error');
                fieldId = 'phone'; // Use phone for error element consistency
            } else {
                input.classList.add('error');
            }

            if (!errorElement) {
                // Create error element if it doesn't exist
                errorElement = document.createElement('div');
                errorElement.id = fieldId + '-error';
                errorElement.className = 'error-message';
                errorElement.setAttribute('role', 'alert');
                errorElement.setAttribute('aria-live', 'polite');

                // For phone input, append to the form group, not the container
                if (fieldId === 'phone') {
                    phoneContainer.parentNode.appendChild(errorElement);
                } else {
                    input.parentNode.appendChild(errorElement);
                }
            }

            errorElement.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
        }

        function clearFieldError(fieldId) {
            const input = document.getElementById(fieldId);
            let errorElement = document.getElementById(fieldId + '-error');

            // Handle phone input container error state
            if (fieldId === 'phone-display' || fieldId === 'phone') {
                phoneContainer.classList.remove('error');
                errorElement = document.getElementById('phone-error');
            } else {
                input.classList.remove('error');
            }

            if (errorElement) {
                errorElement.innerHTML = '';
            }
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

        // Utility functions
        function isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        }

        function isValidPhone(phone) {
            // Remove spaces for validation
            const cleanPhone = phone.replace(/\s/g, '');

            // UAE phone number validation patterns (matching backend)
            const validPatterns = [
                /^\+971[0-9]{9}$/,  // +971XXXXXXXXX (13 chars) - preferred format
                /^971[0-9]{9}$/,    // 971XXXXXXXXX (12 chars)
                /^0[0-9]{9}$/,      // 0XXXXXXXXX (10 chars)
                /^[0-9]{9}$/        // XXXXXXXXX (9 chars)
            ];

            return validPatterns.some(pattern => pattern.test(cleanPhone));
        }

        // Add real-time validation event listeners
        document.addEventListener('DOMContentLoaded', function() {
            const formInputs = document.querySelectorAll('.form-input, select');

            formInputs.forEach(input => {
                // Add blur event for validation
                input.addEventListener('blur', function() {
                    validateField(this);
                });

                // Add input event for some fields
                if (input.type === 'email' || input.type === 'text' || input.type === 'password') {
                    input.addEventListener('input', function() {
                        // Clear error on input if field was previously invalid
                        if (this.classList.contains('error')) {
                            clearFieldError(this.id);
                        }
                    });
                }
            });
        });

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
    </script>
</body>
</html>
