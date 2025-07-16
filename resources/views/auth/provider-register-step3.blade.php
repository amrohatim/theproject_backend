<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Provider Registration Step 4 - License Upload">
    <meta name="robots" content="noindex, nofollow">
    <title>Data3Chic - Provider Registration</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        purple: {
                            50: '#faf5ff',
                            100: '#f3e8ff',
                            200: '#e9d5ff',
                            300: '#d8b4fe',
                            400: '#c084fc',
                            500: '#a855f7',
                            600: '#9333ea',
                            700: '#7c3aed',
                            800: '#6b21a8',
                            900: '#581c87'
                        }
                    }
                }
            }
        }
    </script>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        .loading {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid #ffffff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s ease-in-out infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Step Indicator Styles - Matching Vendor Registration */
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
            background: #8b5cf6;
        }

        .progress-step.completed::after {
            background: #10b981;
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
            transition: all 0.2s ease;
        }

        .progress-step.active .step-circle {
            background: #8b5cf6;
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
            color: #8b5cf6;
            font-weight: 600;
        }

        .progress-step.completed .step-label {
            color: #10b981;
            font-weight: 600;
        }

        /* Responsive adjustments for step indicator */
        @media (max-width: 640px) {
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
<body class="min-h-screen bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Left Side - Marketing Content -->
        <div class="hidden lg:flex lg:w-1/2 text-white p-12 flex-col justify-top" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
            <div class="max-w-md mx-auto space-y-8">
                <div class="text-center">
                    <h1 class="text-3xl font-bold mb-8">Data3Chic</h1>
                </div>

                <!-- Main Heading -->
                <div class="text-center space-y-4">
                    <h2 class="text-4xl font-bold leading-tight">
                        License<br>Upload
                    </h2>
                    <p class="text-purple-100 text-lg">
                        Upload your business license to complete your provider registration and start selling.
                    </p>
                </div>

                <!-- Features -->
                <div class="space-y-6 mt-12">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg mb-1">Secure Upload</h3>
                            <p class="text-purple-100 text-sm">Your business documents are encrypted and stored securely.</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg mb-1">Quick Verification</h3>
                            <p class="text-purple-100 text-sm">Our team will review your license within 24-48 hours.</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg mb-1">Start Selling</h3>
                            <p class="text-purple-100 text-sm">Once approved, you can immediately start listing products.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - License Upload Form -->
        <div class="w-full lg:w-1/2 bg-white p-8 lg:p-12 flex items-center justify-center">
            <div class="w-full max-w-md space-y-6">
                <div class="text-center space-y-2">
                    <h2 class="text-2xl font-bold text-gray-900">Upload Business License</h2>
                    <p class="text-gray-600">Step 4 of 4: Upload your business registration documents</p>
                </div>

                <!-- Step Indicator -->
                <div class="progress-bar">
                    <div class="progress-step completed">
                        <div class="step-circle"><i class="fas fa-check"></i></div>
                        <div class="step-label">Provider Info</div>
                    </div>
                    <div class="progress-step completed">
                        <div class="step-circle"><i class="fas fa-check"></i></div>
                        <div class="step-label">Verification</div>
                    </div>
                    <div class="progress-step completed">
                        <div class="step-circle"><i class="fas fa-check"></i></div>
                        <div class="step-label">Phone</div>
                    </div>
                    <div class="progress-step active">
                        <div class="step-circle">4</div>
                        <div class="step-label">License</div>
                    </div>
                </div>

                <!-- Back Link -->
                <div class="mb-4">
                    <a href="/register/provider/phone-verification" class="text-purple-600 hover:text-purple-700 transition-colors duration-300 text-sm font-medium">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Phone Verification
                    </a>
                </div>

                <!-- License Information Card -->
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-6 mb-6">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-info-circle text-purple-600 mr-2"></i>
                        <h3 class="font-semibold text-gray-900">License Information</h3>
                    </div>
                    <p class="text-gray-600 text-sm">
                        Please upload your valid business license or trade registration document.
                        This helps us verify your business and ensures compliance with local regulations.
                    </p>
                </div>

                <!-- License Requirements -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <h4 class="flex items-center text-yellow-800 font-semibold text-sm mb-2">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Requirements:
                    </h4>
                    <ul class="text-yellow-700 text-sm space-y-1 ml-4">
                        <li>• Valid business license or trade registration</li>
                        <li>• Document must be in PDF format</li>
                        <li>• File size should not exceed 10MB</li>
                        <li>• Document should be clear and readable</li>
                        <li>• License should be currently valid</li>
                    </ul>
                </div>

                <!-- License Upload Form -->
                <form id="licenseForm" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <!-- License Start Date -->
                    <div class="space-y-2">
                        <label for="license_start_date" class="text-sm font-medium text-gray-700">License Start Date</label>
                        <div class="relative">
                            <input id="license_start_date" name="license_start_date" type="date" required
                                   class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- License Expiry Date -->
                    <div class="space-y-2">
                        <label for="license_expiry_date" class="text-sm font-medium text-gray-700">License Expiry Date</label>
                        <div class="relative">
                            <input id="license_expiry_date" name="license_expiry_date" type="date" required
                                   class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- File Upload -->
                    <div class="space-y-2">
                        <label for="license_file" class="text-sm font-medium text-gray-700">Business License Document</label>
                        <div class="relative">
                            <input type="file" id="license_file" name="license_file" accept=".pdf" required class="hidden">
                            <label for="license_file" id="file-upload-label" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-purple-50 hover:border-purple-300 transition-colors">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <i class="fas fa-file-pdf text-4xl text-gray-400 mb-3"></i>
                                    <p class="mb-2 text-sm text-gray-500">
                                        <span class="font-semibold">Click to upload</span> license document
                                    </p>
                                    <p class="text-xs text-gray-500">PDF format, max 10MB</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" id="submitBtn" class="w-full text-white py-3 px-4 rounded-md font-semibold transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                        <span class="loading hidden">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                        </span>
                        <span class="button-text">Complete Registration</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Set default start date to today
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('license_start_date').value = today;
        });

        // File upload preview
        document.getElementById('license_file').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const label = document.getElementById('file-upload-label');

            if (file) {
                if (file.type !== 'application/pdf') {
                    alert('Please select a PDF file.');
                    e.target.value = '';
                    return;
                }

                if (file.size > 10 * 1024 * 1024) { // 10MB
                    alert('File size must be less than 10MB.');
                    e.target.value = '';
                    return;
                }

                label.innerHTML = `
                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                        <i class="fas fa-check-circle text-4xl text-green-500 mb-3"></i>
                        <p class="mb-2 text-sm text-green-600 font-semibold">${file.name}</p>
                        <p class="text-xs text-green-500">File selected successfully - Click to change</p>
                    </div>
                `;
                label.classList.remove('border-gray-300', 'bg-gray-50', 'hover:bg-purple-50', 'hover:border-purple-300');
                label.classList.add('border-green-300', 'bg-green-50');
            }
        });

        // Date validation
        document.getElementById('license_start_date').addEventListener('change', function() {
            validateDates();
        });

        document.getElementById('license_expiry_date').addEventListener('change', function() {
            validateDates();
        });

        function validateDates() {
            const startDate = document.getElementById('license_start_date').value;
            const expiryDate = document.getElementById('license_expiry_date').value;

            if (startDate && expiryDate) {
                const start = new Date(startDate);
                const expiry = new Date(expiryDate);

                if (expiry <= start) {
                    alert('License expiry date must be after the start date.');
                    document.getElementById('license_expiry_date').value = '';
                }
            }
        }

        // Form submission
        document.getElementById('licenseForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // Get user_id from localStorage (should be set after phone verification)
            const userId = localStorage.getItem('provider_user_id');
            if (!userId) {
                alert('User session not found. Please complete phone verification first.');
                window.location.href = '/register/provider/phone-verification';
                return;
            }

            const submitBtn = document.getElementById('submitBtn');
            const loadingSpan = submitBtn.querySelector('.loading');
            loadingSpan.classList.remove('hidden');
            submitBtn.disabled = true;

            const formData = new FormData(this);
            formData.append('user_id', userId);

            fetch('/api/provider-registration/license', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('License uploaded successfully! Registration completed. Please wait for admin approval.');
                    // Clear stored data
                    localStorage.removeItem('provider_registration_token');
                    localStorage.removeItem('provider_user_id');
                    window.location.href = '/login';
                } else {
                    throw new Error(data.message || 'License upload failed');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message || 'An error occurred. Please try again.');
            })
            .finally(() => {
                const loadingSpan = submitBtn.querySelector('.loading');
                loadingSpan.classList.add('hidden');
                submitBtn.disabled = false;
            });
        });
    </script>
</body>
</html>
