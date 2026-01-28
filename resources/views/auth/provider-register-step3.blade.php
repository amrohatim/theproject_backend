<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ __('messages.provider_registration_step4_description') }}">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ __('messages.data3chic_provider_registration') }}</title>
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
    @if(app()->getLocale() == 'ar')
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('css/rtl.css') }}">
    @else
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @endif
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
                        {{ __('messages.license_upload') }}
                    </h2>
                    <p class="text-purple-100 text-lg">
                        {{ __('messages.license_upload_description') }}
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
                            <h3 class="font-semibold text-lg mb-1">{{ __('messages.secure_upload') }}</h3>
                            <p class="text-purple-100 text-sm">{{ __('messages.secure_upload_description') }}</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg mb-1">{{ __('messages.quick_verification') }}</h3>
                            <p class="text-purple-100 text-sm">{{ __('messages.quick_verification_description') }}</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg mb-1">{{ __('messages.start_selling') }}</h3>
                            <p class="text-purple-100 text-sm">{{ __('messages.start_selling_description') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - License Upload Form -->
        <div class="w-full lg:w-1/2 bg-white p-8 lg:p-12 flex items-center justify-center">
            <div class="w-full max-w-md space-y-6">
                <div class="text-center space-y-2">
                    <h2 class="text-2xl font-bold text-gray-900">{{ __('messages.upload_business_license') }}</h2>
                    <p class="text-gray-600">{{ __('messages.step_4_of_4_upload_documents') }}</p>
                </div>

                <!-- Step Indicator -->
                <div class="progress-bar">
                    <div class="progress-step completed">
                        <div class="step-circle"><i class="fas fa-check"></i></div>
                        <div class="step-label">{{ __('messages.provider_info') }}</div>
                    </div>
                    <div class="progress-step completed">
                        <div class="step-circle"><i class="fas fa-check"></i></div>
                        <div class="step-label">{{ __('messages.verification') }}</div>
                    </div>
                    <div class="progress-step completed">
                        <div class="step-circle"><i class="fas fa-check"></i></div>
                        <div class="step-label">{{ __('messages.phone') }}</div>
                    </div>
                    <div class="progress-step active">
                        <div class="step-circle">4</div>
                        <div class="step-label">{{ __('messages.license') }}</div>
                    </div>
                </div>

                <!-- Back Link -->
                <div class="mb-4">
                    <a href="/register/provider/phone-verification" class="text-purple-600 hover:text-purple-700 transition-colors duration-300 text-sm font-medium">
                        <i class="fas fa-arrow-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }} {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                        {{ __('messages.back_to_phone_verification') }}
                    </a>
                </div>

                <!-- License Information Card -->
                <div class="rounded-lg p-6 mb-6">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-info-circle text-purple-600 {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                        <h3 class="font-semibold text-gray-900">{{ __('messages.license_information') }}</h3>
                    </div>
                    <p class="text-gray-600 text-sm">
                        {{ __('messages.license_upload_instructions') }}
                    </p>
                </div>

                <!-- License Requirements -->
                <div class="rounded-lg p-4 mb-6">
                    <h4 class="flex items-center text-yellow-800 font-semibold text-sm mb-2">
                        <i class="fas fa-exclamation-triangle {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                        {{ __('messages.requirements') }}:
                    </h4>
                    <ul class="text-yellow-700 text-sm space-y-1 {{ app()->getLocale() == 'ar' ? 'mr-4' : 'ml-4' }}">
                        <li>• {{ __('messages.valid_business_license') }}</li>
                        <li>• {{ __('messages.document_pdf_format') }}</li>
                        <li>• {{ __('messages.max_file_size_10mb') }}</li>
                        <li>• {{ __('messages.document_clear_readable') }}</li>
                        <li>• {{ __('messages.license_currently_valid') }}</li>
                    </ul>
                </div>

                <!-- License Upload Form -->
                <form id="licenseForm" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <!-- License Start Date -->
                    <div class="space-y-2">
                        <label for="license_start_date" class="text-sm font-medium text-gray-700">{{ __('messages.license_start_date') }}</label>
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
                        <label for="license_expiry_date" class="text-sm font-medium text-gray-700">{{ __('messages.license_expiry_date') }}</label>
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
                        <label for="license_file" class="text-sm font-medium text-gray-700">{{ __('messages.business_license_document') }}</label>
                        <div class="relative">
                            <input type="file" id="license_file" name="license_file" accept=".pdf" required class="hidden">
                            <label for="license_file" id="file-upload-label" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-purple-50 hover:border-purple-300 transition-colors">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <i class="fas fa-file-pdf text-4xl text-gray-400 mb-3"></i>
                                    <p class="mb-2 text-sm text-gray-500">
                                        <span class="font-semibold">{{ __('messages.click_to_upload') }}</span> {{ __('messages.license_document') }}
                                    </p>
                                    <p class="text-xs text-gray-500">{{ __('messages.pdf_format_max_10mb') }}</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" id="submitBtn" class="w-full text-white py-3 px-4 rounded-md font-semibold transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                        <span class="loading hidden">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                        </span>
                        <span class="button-text">{{ __('messages.complete_registration') }}</span>
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
                    alert('{{ __('messages.please_select_pdf_file') }}');
                    e.target.value = '';
                    return;
                }

                if (file.size > 10 * 1024 * 1024) { // 10MB
                    alert('{{ __('messages.file_size_less_than_10mb') }}');
                    e.target.value = '';
                    return;
                }

                label.innerHTML = `
                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                        <i class="fas fa-check-circle text-4xl text-green-500 mb-3"></i>
                        <p class="mb-2 text-sm text-green-600 font-semibold">${file.name}</p>
                        <p class="text-xs text-green-500">{{ __('messages.file_selected_successfully') }}</p>
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
                    alert('{{ __('messages.license_expiry_after_start') }}');
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
                alert('{{ __('messages.user_session_not_found') }}');
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
                    alert('{{ __('messages.license_uploaded_successfully') }}');
                    // Clear stored data
                    localStorage.removeItem('provider_registration_token');
                    localStorage.removeItem('provider_user_id');
                    window.location.href = '/login';
                } else {
                    throw new Error(data.message || '{{ __('messages.license_upload_failed') }}');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message || '{{ __('messages.error_occurred_try_again') }}');
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
