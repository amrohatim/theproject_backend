<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Vendor Registration Step 4 - License Upload">
    <meta name="robots" content="noindex, nofollow">
    <title>Vendor Registration - Step 4 | Dala3Chic</title>
    
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

        .info-card {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .info-card-header {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
        }

        .info-card-header i {
            color: #0284c7;
            margin-right: 8px;
            font-size: 18px;
        }

        .info-card-title {
            font-weight: 600;
            color: #0c4a6e;
        }

        .info-card-text {
            color: #0369a1;
            font-size: 14px;
            line-height: 1.5;
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
            border-color: #3b82f6;
            background: white;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
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
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            border: 2px dashed #d1d5db;
            border-radius: 12px;
            background: #f9fafb;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }

        .file-upload-label:hover {
            border-color: #3b82f6;
            background: #f0f9ff;
        }

        .file-upload-icon {
            font-size: 48px;
            color: #6b7280;
            margin-bottom: 16px;
        }

        .file-upload-text {
            color: #374151;
            font-weight: 500;
            margin-bottom: 4px;
        }

        .file-upload-subtext {
            color: #6b7280;
            font-size: 12px;
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

        .license-requirements {
            background: #fefce8;
            border: 1px solid #fde047;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 20px;
        }

        .license-requirements h4 {
            color: #a16207;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .license-requirements ul {
            color: #a16207;
            font-size: 13px;
            margin-left: 16px;
        }

        .license-requirements li {
            margin-bottom: 4px;
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
            
            .file-upload-label {
                padding: 30px 15px;
            }
            
            .file-upload-icon {
                font-size: 36px;
            }
        }
    </style>
</head>
<body>
    <div class="registration-container">
        <a href="/register/vendor/step3" class="back-link">
            <i class="fas fa-arrow-left"></i>
            Back to Company Info
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
            <div class="progress-step completed">
                <div class="step-circle"><i class="fas fa-check"></i></div>
                <div class="step-label">Company</div>
            </div>
            <div class="progress-step active">
                <div class="step-circle">4</div>
                <div class="step-label">License</div>
            </div>
        </div>

        <div class="form-header">
            <h2 class="form-title">Upload Business License</h2>
            <p class="form-subtitle">Step 4: Upload your business registration documents</p>
        </div>

        <!-- Info Card -->
        <div class="info-card">
            <div class="info-card-header">
                <i class="fas fa-info-circle"></i>
                <div class="info-card-title">License Information</div>
            </div>
            <div class="info-card-text">
                Please upload your valid business license or trade registration document. 
                This helps us verify your business and ensures compliance with local regulations.
            </div>
        </div>

        <!-- License Requirements -->
        <div class="license-requirements">
            <h4><i class="fas fa-exclamation-triangle"></i> Requirements:</h4>
            <ul>
                <li>Valid business license or trade registration</li>
                <li>Document must be in PDF format</li>
                <li>File size should not exceed 10MB</li>
                <li>Document should be clear and readable</li>
                <li>License should be currently valid</li>
            </ul>
        </div>

        <form id="licenseForm" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="form-group">
                <label for="license_duration_years" class="form-label">License Duration (Years) *</label>
                <select id="license_duration_years" name="license_duration_years" class="form-select" required>
                    <option value="">Select duration</option>
                    <option value="1">1 Year</option>
                    <option value="2">2 Years</option>
                    <option value="3">3 Years</option>
                    <option value="5">5 Years</option>
                    <option value="10">10 Years</option>
                </select>
            </div>

            <div class="form-group">
                <label for="license_file" class="form-label">Business License Document *</label>
                <div class="file-upload">
                    <input type="file" id="license_file" name="license_file" class="file-upload-input" 
                           accept=".pdf" required>
                    <label for="license_file" class="file-upload-label">
                        <div class="file-upload-icon">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <div class="file-upload-text">Click to upload license document</div>
                        <div class="file-upload-subtext">PDF format, max 10MB</div>
                    </label>
                </div>
            </div>

            <button type="submit" class="form-button" id="submitBtn">
                <div class="loading-spinner"></div>
                <span class="button-text">Complete Registration</span>
            </button>
        </form>
    </div>

    <script>
        // File upload preview
        document.getElementById('license_file').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const label = document.querySelector('.file-upload-label');
            
            if (file) {
                label.innerHTML = `
                    <div class="file-upload-icon" style="color: #10b981;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="file-upload-text" style="color: #10b981;">${file.name}</div>
                    <div class="file-upload-subtext">Click to change document</div>
                `;
            }
        });

        // Form submission
        document.getElementById('licenseForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
            
            const formData = new FormData(this);
            
            fetch('/api/vendor/register/upload-license', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Complete registration
                    return fetch('/api/vendor/register/complete', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });
                } else {
                    throw new Error(data.message || 'License upload failed');
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Registration completed successfully! Please wait for admin approval.');
                    window.location.href = '/login';
                } else {
                    throw new Error(data.message || 'Registration completion failed');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message || 'An error occurred. Please try again.');
            })
            .finally(() => {
                submitBtn.classList.remove('loading');
                submitBtn.disabled = false;
            });
        });
    </script>
</body>
</html>
