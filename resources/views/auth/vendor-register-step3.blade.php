<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Vendor Registration Step 3 - Company Information">
    <meta name="robots" content="noindex, nofollow">
    <title>Vendor Registration - Step 3 | glowlabs</title>
    
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

        .form-section {
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }

        .section-title i {
            margin-right: 8px;
            color: #3b82f6;
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

        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #f9fafb;
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: #3b82f6;
            background: white;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-textarea {
            resize: vertical;
            min-height: 100px;
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
            border-color: #3b82f6;
            background: #f0f9ff;
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
            accent-color: #3b82f6;
        }

        .checkbox-label {
            font-size: 14px;
            color: #374151;
            cursor: pointer;
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
        <a href="/register/vendor/step2" class="back-link">
            <i class="fas fa-arrow-left"></i>
            Back to Verification
        </a>

        <!-- Progress Bar -->
        <div class="progress-bar">
            <div class="progress-step completed">
                <div class="step-circle"><i class="fas fa-check"></i></div>
                <div class="step-label">Basic Info</div>
            </div>
            <div class="progress-step completed">
                <div class="step-circle"><i class="fas fa-check"></i></div>
                <div class="step-label">Verification</div>
            </div>
            <div class="progress-step active">
                <div class="step-circle">3</div>
                <div class="step-label">Company</div>
            </div>
            <div class="progress-step">
                <div class="step-circle">4</div>
                <div class="step-label">License</div>
            </div>
        </div>

        <div class="form-header">
            <h2 class="form-title">Company Information</h2>
            <p class="form-subtitle">Step 3: Tell us about your business</p>
        </div>

        <form id="companyForm" method="POST" enctype="multipart/form-data">
            @csrf
            
            <!-- Company Details Section -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-building"></i>
                    Company Details
                </h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="company_name" class="form-label">Company Name *</label>
                        <input type="text" id="company_name" name="company_name" class="form-input" required 
                               placeholder="Enter your company name">
                    </div>
                    
                    <div class="form-group">
                        <label for="company_email" class="form-label">Company Email *</label>
                        <input type="email" id="company_email" name="company_email" class="form-input" required 
                               placeholder="company@example.com">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="contact_number_1" class="form-label">Primary Contact Number *</label>
                        <input type="tel" id="contact_number_1" name="contact_number_1" class="form-input" required 
                               placeholder="+971 4 123 4567">
                    </div>
                    
                    <div class="form-group">
                        <label for="contact_number_2" class="form-label">Secondary Contact Number</label>
                        <input type="tel" id="contact_number_2" name="contact_number_2" class="form-input" 
                               placeholder="+971 50 123 4567 (Optional)">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description" class="form-label">Company Description</label>
                    <textarea id="description" name="description" class="form-textarea" 
                              placeholder="Tell us about your business, products, and services..."></textarea>
                </div>
            </div>

            <!-- Address Section -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-map-marker-alt"></i>
                    Business Address
                </h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="emirate" class="form-label">Emirate *</label>
                        <select id="emirate" name="emirate" class="form-select" required>
                            <option value="">Select Emirate</option>
                            <option value="Abu Dhabi">Abu Dhabi</option>
                            <option value="Dubai">Dubai</option>
                            <option value="Sharjah">Sharjah</option>
                            <option value="Ajman">Ajman</option>
                            <option value="Umm Al Quwain">Umm Al Quwain</option>
                            <option value="Ras Al Khaimah">Ras Al Khaimah</option>
                            <option value="Fujairah">Fujairah</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="city" class="form-label">City *</label>
                        <input type="text" id="city" name="city" class="form-input" required 
                               placeholder="Enter city name">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="street" class="form-label">Street Address *</label>
                    <input type="text" id="street" name="street" class="form-input" required 
                           placeholder="Enter complete street address">
                </div>
            </div>

            <!-- Business Settings Section -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-cogs"></i>
                    Business Settings
                </h3>
                
                <div class="checkbox-group">
                    <input type="checkbox" id="delivery_capability" name="delivery_capability" 
                           class="checkbox-input" value="1">
                    <label for="delivery_capability" class="checkbox-label">
                        We offer delivery services to customers
                    </label>
                </div>
                
                <div class="form-group">
                    <label for="logo" class="form-label">Company Logo</label>
                    <div class="file-upload">
                        <input type="file" id="logo" name="logo" class="file-upload-input" 
                               accept="image/jpeg,image/png,image/jpg,image/gif">
                        <label for="logo" class="file-upload-label">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <div class="file-upload-text">
                                <div>Click to upload company logo</div>
                                <div style="font-size: 12px; margin-top: 4px;">JPG, PNG, GIF up to 2MB</div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <button type="submit" class="form-button" id="submitBtn">
                <div class="loading-spinner"></div>
                <span class="button-text">Continue to License Upload</span>
            </button>
        </form>
    </div>

    <script>
        // File upload preview
        document.getElementById('logo').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const label = document.querySelector('.file-upload-label .file-upload-text');

            if (file) {
                label.innerHTML = `
                    <div><i class="fas fa-check" style="color: #10b981;"></i> ${file.name}</div>
                    <div style="font-size: 12px; margin-top: 4px;">Click to change</div>
                `;
            }
        });

        // Form submission
        document.getElementById('companyForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const submitBtn = document.getElementById('submitBtn');
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;

            const formData = new FormData(this);

            fetch('/api/vendor/register/company-info', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = '/register/vendor/step4';
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
