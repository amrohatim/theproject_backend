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

        <form id="providerStep1Form" method="POST" enctype="multipart/form-data">
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
                               value="{{ old('name') }}" placeholder="Enter your full name">
                        @error('name')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address *</label>
                        <input type="email" id="email" name="email" class="form-input" required 
                               value="{{ old('email') }}" placeholder="Enter your email address">
                        @error('email')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="phone" class="form-label">Phone Number *</label>
                        <input type="tel" id="phone" name="phone" class="form-input" required 
                               value="{{ old('phone') }}" placeholder="+971 50 123 4567">
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
                                   placeholder="Create a strong password">
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
                                   class="form-input" required placeholder="Confirm your password">
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
                               value="{{ old('business_name') }}" placeholder="Enter your business name">
                        @error('business_name')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="business_type" class="form-label">Business Type *</label>
                        <select id="business_type" name="business_type" class="form-input" required>
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

        // Phone number formatting
        document.getElementById('phone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, ''); // Remove non-digits

            // If it starts with 971, keep it
            if (value.startsWith('971')) {
                value = '+' + value;
            }
            // If it starts with 0, replace with +971
            else if (value.startsWith('0')) {
                value = '+971' + value.substring(1);
            }
            // If it doesn't start with 971 and has digits, add +971
            else if (value.length > 0 && !value.startsWith('971')) {
                value = '+971' + value;
            }

            // Format the number with spaces for readability
            if (value.startsWith('+971')) {
                const number = value.substring(4); // Remove +971
                if (number.length > 0) {
                    let formatted = '+971';
                    if (number.length > 0) formatted += ' ' + number.substring(0, 2);
                    if (number.length > 2) formatted += ' ' + number.substring(2, 5);
                    if (number.length > 5) formatted += ' ' + number.substring(5, 9);
                    value = formatted;
                }
            }

            e.target.value = value;
        });

        // Ensure phone starts with +971 on focus
        document.getElementById('phone').addEventListener('focus', function(e) {
            if (!e.target.value || e.target.value.trim() === '') {
                e.target.value = '+971 ';
            }
        });

        // Form submission
        document.getElementById('providerStep1Form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
            
            const formData = new FormData(this);
            
            // Collect delivery fees
            if (document.getElementById('delivery_capability').checked) {
                const deliveryFees = {};
                document.querySelectorAll('.fee-input').forEach(input => {
                    const emirate = input.name.replace('delivery_fee_', '');
                    if (input.value) {
                        deliveryFees[emirate] = parseFloat(input.value);
                    }
                });
                formData.append('delivery_fee_by_emirate', JSON.stringify(deliveryFees));
            }
            
            // Ensure CSRF token is included in FormData
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            formData.append('_token', csrfToken);

            fetch('/api/provider/register/validate-info', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Store registration token for step 2
                    if (data.registration_token) {
                        localStorage.setItem('provider_registration_token', data.registration_token);
                    }
                    window.location.href = '/register/provider/step2';
                } else {
                    alert(data.message || 'An error occurred. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            })
            .finally(() => {
                submitBtn.classList.remove('loading');
                submitBtn.disabled = false;
            });
        });
    </script>
</body>
</html>
