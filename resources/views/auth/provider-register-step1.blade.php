@php
    $currentLocale = app()->getLocale();
    $isRtl = in_array($currentLocale, ['ar', 'he', 'fa', 'ur']);
    $direction = $isRtl ? 'rtl' : 'ltr';
@endphp
<!DOCTYPE html>
<html lang="{{ $currentLocale }}" dir="{{ $direction }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Data3Chic - {{ __('messages.provider_registration') }}</title>
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

    <style>
        .step-content {
            display: none;
        }
        .step-content.active {
            display: block;
        }
        .progress-line {
            transition: all 0.3s ease;
        }
        .step-circle {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }
        .step-circle:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
        }
        .step-circle.completed {
            animation: checkmark-bounce 0.6s ease-in-out;
        }
        @keyframes checkmark-bounce {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        .step-circle.clickable:hover {
            background-color: rgba(139, 92, 246, 0.1);
        }
        .step-circle:focus {
            outline: 2px solid #8b5cf6;
            outline-offset: 2px;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }
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

    <!-- RTL Support -->
    @if($isRtl)
    <style>
        body {
            direction: rtl;
            text-align: right;
        }
        .rtl-flip {
            transform: scaleX(-1);
        }
        .rtl-margin-left {
            margin-right: auto;
            margin-left: 0;
        }
        .rtl-margin-right {
            margin-left: auto;
            margin-right: 0;
        }
        .rtl-text-left {
            text-align: right;
        }
        .rtl-text-right {
            text-align: left;
        }
        .rtl-float-left {
            float: right;
        }
        .rtl-float-right {
            float: left;
        }
        .rtl-border-left {
            border-right: 1px solid;
            border-left: none;
        }
        .rtl-border-right {
            border-left: 1px solid;
            border-right: none;
        }
        .rtl-padding-left {
            padding-right: 1rem;
            padding-left: 0;
        }
        .rtl-padding-right {
            padding-left: 1rem;
            padding-right: 0;
        }
    </style>
    @endif
</head>
<body class="min-h-screen bg-gray-50 {{ $isRtl ? 'rtl' : '' }}">
    <div class="min-h-screen flex">
        <!-- Left Side - Marketing Content -->
        <div class="hidden lg:flex lg:w-1/2 text-white p-12 flex-col justify-top" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
            <div class="max-w-md mx-auto space-y-8">
                <div class="text-center">
                    <h1 class="text-3xl font-bold mb-8">glowlabs</h1>
                </div>

                <!-- Main Heading -->
                <div class="text-center space-y-4">
                    <h2 class="text-4xl font-bold leading-tight">
                        {{ __('messages.provider_registration') }}
                    </h2>
                    <p class="text-purple-100 text-lg">
                        {{ __('messages.provider_registration_desc') }}
                    </p>
                </div>

                <!-- Features -->
                <div class="space-y-6 mt-12">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg mb-1">{{ __('messages.grow_your_business_title') }}</h3>
                            <p class="text-purple-100 text-sm">{{ __('messages.grow_your_business_desc') }}</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg mb-1">{{ __('messages.professional_tools_title') }}</h3>
                            <p class="text-purple-100 text-sm">{{ __('messages.professional_tools_desc') }}</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg mb-1">{{ __('messages.dedicated_support_title') }}</h3>
                            <p class="text-purple-100 text-sm">{{ __('messages.dedicated_support_desc') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Registration Form -->
        <div class="w-full lg:w-1/2 bg-white p-8 lg:p-12 flex items-center justify-center">
            <div class="w-full max-w-md space-y-6">
                <div class="text-center space-y-2">
                    <h2 class="text-2xl font-bold text-gray-900">{{ __('messages.provider_registration') }}</h2>
                    <p class="text-gray-600">{{ __('messages.personal_info') }}</p>
                </div>

                <!-- Step Indicator -->
                <div class="progress-bar">
                    <div class="progress-step active">
                        <div class="step-circle">1</div>
                        <div class="step-label">{{ __('messages.personal_info') }}</div>
                    </div>
                    <div class="progress-step">
                        <div class="step-circle">2</div>
                        <div class="step-label">{{ __('messages.email_verification') }}</div>
                    </div>
                    <div class="progress-step">
                        <div class="step-circle">3</div>
                        <div class="step-label">{{ __('messages.phone_verification') }}</div>
                    </div>
                    <div class="progress-step">
                        <div class="step-circle">4</div>
                        <div class="step-label">{{ __('messages.license_upload') }}</div>
                    </div>
                </div>

                <!-- Back Link -->
                <div class="mb-4">
                    <a href="{{ route('register') }}" class="text-purple-600 hover:text-purple-700 transition-colors duration-300 text-sm font-medium">
                        <i class="fas fa-arrow-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }} {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                        {{ __('messages.back_to_registration_options') }}
                    </a>
                </div>
                <form method="POST" action="/api/provider/register/validate-info" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <!-- Name Field -->
                    <div class="space-y-2">
                        <label for="name" class="text-sm font-medium text-gray-700">{{ __('messages.full_name') }}</label>
                        <div class="relative">
                            <input id="name" name="name" type="text" placeholder="{{ __('messages.enter_full_name') }}" value="{{ old('name') }}" required class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Email Field -->
                    <div class="space-y-2">
                        <label for="email" class="text-sm font-medium text-gray-700">{{ __('messages.email_address') }}</label>
                        <div class="relative">
                            <input id="email" name="email" type="email" placeholder="{{ __('messages.enter_email_address') }}" value="{{ old('email') }}" required class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Phone Field -->
                    <div class="space-y-2">
                        <label for="phone" class="text-sm font-medium text-gray-700">{{ __('messages.phone_number') }}</label>
                        <div class="flex border border-gray-300 rounded-md focus-within:ring-2 focus-within:ring-purple-500 focus-within:border-transparent" style="direction: ltr; flex-direction: row;">
                            <div class="flex items-center px-3 bg-gray-50 border-r border-gray-300 rounded-l-md" dir="ltr" style="direction: ltr; flex-direction: row;">
                                <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMTgiIHZpZXdCb3g9IjAgMCAyNCAxOCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjYiIGhlaWdodD0iMTgiIGZpbGw9IiNGRjAwMDAiLz4KPHJlY3QgeD0iNiIgd2lkdGg9IjE4IiBoZWlnaHQ9IjYiIGZpbGw9IiMwMDczMkYiLz4KPHJlY3QgeD0iNiIgeT0iNiIgd2lkdGg9IjE4IiBoZWlnaHQ9IjYiIGZpbGw9IiNGRkZGRkYiLz4KPHJlY3QgeD0iNiIgeT0iMTIiIHdpZHRoPSIxOCIgaGVpZ2h0PSI2IiBmaWxsPSIjMDAwMDAwIi8+Cjwvc3ZnPg==" alt="UAE Flag" class="w-5 h-4 mr-2">
                                <span class="text-sm font-medium text-gray-700">+971</span>
                            </div>
                            <input style = "direction: ltr; text-align: left;" id="phone" name="phone" type="tel" placeholder="50 123 4567" value="{{ old('phone') }}" required class="flex-1 px-3 py-2 border-0 rounded-r-md focus:outline-none focus:ring-0" maxlength="11">
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div class="space-y-2">
                        <label for="password" class="text-sm font-medium text-gray-700">{{ __('messages.password') }}</label>
                        <div class="relative">
                            <input id="password" name="password" type="password" placeholder="{{ __('messages.create_strong_password') }}" required class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <button type="button" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600" onclick="togglePasswordVisibility('password', this)" aria-label="Toggle password visibility">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Confirm Password Field -->
                    <div class="space-y-2">
                        <label for="password_confirmation" class="text-sm font-medium text-gray-700">{{ __('messages.confirm_password') }}</label>
                        <div class="relative">
                            <input id="password_confirmation" name="password_confirmation" type="password" placeholder="{{ __('messages.confirm_your_password') }}" required class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <button type="button" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600" onclick="togglePasswordVisibility('password_confirmation', this)" aria-label="Toggle password visibility">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Business Name Field -->
                    <div class="space-y-2">
                        <label for="business_name" class="text-sm font-medium text-gray-700">{{ __('messages.business_name') }}</label>
                        <div class="relative">
                            <input id="business_name" name="business_name" type="text" placeholder="{{ __('messages.enter_business_name') }}" value="{{ old('business_name') }}" class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Provider Logo Upload Field -->
                    <div class="space-y-2">
                        <label for="logo" class="text-sm font-medium text-gray-700">{{ __('messages.company_logo_optional') }}</label>
                        <div class="logo-upload-container border-2 border-dashed border-gray-300 rounded-md p-6 text-center hover:border-purple-500 transition-colors duration-300 cursor-pointer" id="logoUploadContainer">
                            <!-- Upload Placeholder -->
                            <div class="upload-placeholder" id="logoUploadPlaceholder">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <p class="text-sm text-gray-600 mb-2">{{ __('messages.click_upload_drag_drop') }}</p>
                                <p class="text-xs text-gray-500">{{ __('messages.png_jpg_gif_5mb') }}</p>
                            </div>
                            <!-- Image Preview -->
                            <div class="image-preview hidden" id="logoImagePreview">
                                <img class="mx-auto max-h-32 rounded-md mb-3" id="logoPreviewImg" alt="Logo preview">
                                <div class="flex justify-center space-x-2">
                                    <button type="button" class="text-sm text-purple-600 hover:text-purple-700" onclick="changeLogo()">Change</button>
                                    <button type="button" class="text-sm text-red-600 hover:text-red-700" onclick="removeLogo()">Remove</button>
                                </div>
                            </div>
                            <input type="file" id="logo" name="logo" accept="image/jpeg,image/png,image/jpg,image/gif" class="hidden" required>
                        </div>
                    </div>

                    <!-- Description Field -->
                    <div class="space-y-2">
                        <label for="description" class="text-sm font-medium text-gray-700">{{ __('messages.description_optional') }}</label>
                        <textarea id="description" name="description" rows="3" placeholder="{{ __('messages.describe_your_business') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">{{ old('description') }}</textarea>
                    </div>

                    <!-- Delivery Capability & Emirates Fee Options -->
                    <div class="space-y-4">
                        <div class="border-t pt-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('messages.delivery_service_configuration') }}</h3>

                            <!-- Delivery Capability Toggle -->
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="checkbox" id="delivery_capability" name="delivery_capability" value="1" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                    <span class="ml-2 text-sm font-medium text-gray-700">{{ __('messages.i_provide_delivery_services') }}</span>
                                </label>
                            </div>

                            <!-- Emirates Selection and Fee Configuration -->
                            <div class="delivery-config hidden mt-4" id="deliveryConfig">
                                <label class="text-sm font-medium text-gray-700 mb-3 block">{{ __('messages.select_emirates_and_set_delivery_fees') }}</label>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Abu Dhabi -->
                                    <div class="emirate-option">
                                        <label class="flex items-center mb-2">
                                            <input type="checkbox" class="emirate-checkbox rounded border-gray-300 text-purple-600 focus:ring-purple-500" data-emirate="abu_dhabi">
                                            <span class="ml-2 text-sm font-medium text-gray-700">{{ __('messages.abu_dhabi') }}</span>
                                        </label>
                                        <div class="fee-input-container hidden">
                                            <div class="relative">
                                                <input type="number" step="1" min="0" placeholder="0" class="w-full pl-20 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent" data-fee-input="abu_dhabi" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-sm">{{ __('messages.aed') }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Dubai -->
                                    <div class="emirate-option">
                                        <label class="flex items-center mb-2">
                                            <input type="checkbox" class="emirate-checkbox rounded border-gray-300 text-purple-600 focus:ring-purple-500" data-emirate="dubai">
                                            <span class="ml-2 text-sm font-medium text-gray-700">{{ __('messages.dubai') }}</span>
                                        </label>
                                        <div class="fee-input-container hidden">
                                            <div class="relative">
                                                <input type="number" step="1" min="0" placeholder="0" class="w-full pl-20 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent" data-fee-input="dubai" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-sm">{{ __('messages.aed') }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Sharjah -->
                                    <div class="emirate-option">
                                        <label class="flex items-center mb-2">
                                            <input type="checkbox" class="emirate-checkbox rounded border-gray-300 text-purple-600 focus:ring-purple-500" data-emirate="sharjah">
                                            <span class="ml-2 text-sm font-medium text-gray-700">{{ __('messages.sharjah') }}</span>
                                        </label>
                                        <div class="fee-input-container hidden">
                                            <div class="relative">
                                                <input type="number" step="1" min="0" placeholder="0" class="w-full pl-20 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent" data-fee-input="sharjah" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-sm">{{ __('messages.aed') }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Ajman -->
                                    <div class="emirate-option">
                                        <label class="flex items-center mb-2">
                                            <input type="checkbox" class="emirate-checkbox rounded border-gray-300 text-purple-600 focus:ring-purple-500" data-emirate="ajman">
                                            <span class="ml-2 text-sm font-medium text-gray-700">{{ __('messages.ajman') }}</span>
                                        </label>
                                        <div class="fee-input-container hidden">
                                            <div class="relative">
                                                <input type="number" step="1" min="0" placeholder="0" class="w-full pl-20 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent" data-fee-input="ajman" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-sm">{{ __('messages.aed') }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Umm Al Quwain -->
                                    <div class="emirate-option">
                                        <label class="flex items-center mb-2">
                                            <input type="checkbox" class="emirate-checkbox rounded border-gray-300 text-purple-600 focus:ring-purple-500" data-emirate="uaq">
                                            <span class="ml-2 text-sm font-medium text-gray-700">{{ __('messages.umm_al_quwain') }}</span>
                                        </label>
                                        <div class="fee-input-container hidden">
                                            <div class="relative">
                                                <input type="number" step="1" min="0" placeholder="0" class="w-full pl-20 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent" data-fee-input="uaq" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-sm">{{ __('messages.aed') }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Ras Al Khaimah -->
                                    <div class="emirate-option">
                                        <label class="flex items-center mb-2">
                                            <input type="checkbox" class="emirate-checkbox rounded border-gray-300 text-purple-600 focus:ring-purple-500" data-emirate="rak">
                                            <span class="ml-2 text-sm font-medium text-gray-700">{{ __('messages.ras_al_khaimah') }}</span>
                                        </label>
                                        <div class="fee-input-container hidden">
                                            <div class="relative">
                                                <input type="number" step="1" min="0" placeholder="0" class="w-full pl-20 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent" data-fee-input="rak" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500  text-sm">{{ __('messages.aed') }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Fujairah -->
                                    <div class="emirate-option">
                                        <label class="flex items-center mb-2">
                                            <input type="checkbox" class="emirate-checkbox rounded border-gray-300 text-purple-600 focus:ring-purple-500" data-emirate="fujairah">
                                            <span class="ml-2 text-sm font-medium text-gray-700">{{ __('messages.fujairah') }}</span>
                                        </label>
                                        <div class="fee-input-container hidden">
                                            <div class="relative">
                                                <input type="number" step="1" min="0" placeholder="0" class="w-full pl-20 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent" data-fee-input="fujairah" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-sm">{{ __('messages.aed') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" id="delivery_fee_by_emirate" name="delivery_fee_by_emirate">
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full text-white py-3 px-4 rounded-md font-semibold transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                        {{ __('messages.continue_to_email_verification') }}
                        @if ($isRtl)
                            <i class="fas fa-arrow-left ml-2"></i>
                        @else
                            <i class="fas fa-arrow-right ml-2"></i>
                        @endif
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for validation errors -->
    <div id="validationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-2">{{__('messages.correct_the_errors') }}</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500" id="validationMessage"></p>
                </div>
                <div class="items-center px-4 py-3">
                    <button id="closeValidationModal" class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">
                        {{ __('messages.close') }}
                    </button>
                    <button id="loginButton" class="mt-2 px-4 py-2 bg-blue-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300 hidden">
                        {{ __('messages.go_to_login') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Logo upload functionality
        const logoUploadContainer = document.getElementById('logoUploadContainer');
        const logoInput = document.getElementById('logo');
        const logoUploadPlaceholder = document.getElementById('logoUploadPlaceholder');
        const logoImagePreview = document.getElementById('logoImagePreview');
        const logoPreviewImg = document.getElementById('logoPreviewImg');

        // Click to upload logo
        logoUploadContainer.addEventListener('click', function(e) {
            if (!e.target.closest('.image-preview')) {
                logoInput.click();
            }
        });

        // Drag and drop for logo
        logoUploadContainer.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('border-purple-500', 'bg-purple-50');
        });

        logoUploadContainer.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('border-purple-500', 'bg-purple-50');
        });

        logoUploadContainer.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('border-purple-500', 'bg-purple-50');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleLogoFile(files[0]);
            }
        });

        // Handle logo file selection
        logoInput.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                handleLogoFile(e.target.files[0]);
            }
        });

        function handleLogoFile(file) {
            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
            if (!allowedTypes.includes(file.type)) {
                showValidationModal('Please select a valid image file (PNG, JPG, GIF)');
                return;
            }

            // Validate file size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                showValidationModal('File size must be less than 5MB');
                return;
            }

            // Create file reader to preview image
            const reader = new FileReader();
            reader.onload = function(e) {
                logoPreviewImg.src = e.target.result;
                logoUploadPlaceholder.classList.add('hidden');
                logoImagePreview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }

        function changeLogo() {
            logoInput.click();
        }

        function removeLogo() {
            logoInput.value = '';
            logoUploadPlaceholder.classList.remove('hidden');
            logoImagePreview.classList.add('hidden');
        }

        // Delivery capability functionality
        const deliveryCapabilityCheckbox = document.getElementById('delivery_capability');
        const deliveryConfig = document.getElementById('deliveryConfig');
        const emirateCheckboxes = document.querySelectorAll('.emirate-checkbox');
        const deliveryFeeInput = document.getElementById('delivery_fee_by_emirate');

        deliveryCapabilityCheckbox.addEventListener('change', function() {
            if (this.checked) {
                deliveryConfig.classList.remove('hidden');
            } else {
                deliveryConfig.classList.add('hidden');
                // Clear all emirate selections
                emirateCheckboxes.forEach(checkbox => {
                    checkbox.checked = false;
                    const feeContainer = checkbox.closest('.emirate-option').querySelector('.fee-input-container');
                    feeContainer.classList.add('hidden');
                });
                updateDeliveryFees();
            }
        });

        // Handle emirate checkbox changes
        emirateCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const emirate = this.dataset.emirate;
                const feeContainer = this.closest('.emirate-option').querySelector('.fee-input-container');
                const feeInput = feeContainer.querySelector(`[data-fee-input="${emirate}"]`);

                if (this.checked) {
                    feeContainer.classList.remove('hidden');
                    feeInput.required = true;
                } else {
                    feeContainer.classList.add('hidden');
                    feeInput.required = false;
                    feeInput.value = '';
                }
                updateDeliveryFees();
            });
        });

        // Handle fee input changes
        document.querySelectorAll('[data-fee-input]').forEach(input => {
            input.addEventListener('input', updateDeliveryFees);
        });

        function updateDeliveryFees() {
            const fees = {};
            emirateCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const emirate = checkbox.dataset.emirate;
                    const feeInput = document.querySelector(`[data-fee-input="${emirate}"]`);
                    fees[emirate] = parseFloat(feeInput.value) || 0;
                }
            });
            deliveryFeeInput.value = JSON.stringify(fees);
        }

        // Phone number formatting for UAE
        const phoneInput = document.getElementById('phone');

        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, ''); // Remove non-digits

            // Limit to 9 digits
            if (value.length > 9) {
                value = value.substring(0, 9);
            }

            // Format the display value with spaces for readability
            let formattedValue = value;
            if (value.length > 2) {
                formattedValue = value.substring(0, 2) + ' ' + value.substring(2);
            }
            if (value.length > 5) {
                formattedValue = value.substring(0, 2) + ' ' + value.substring(2, 5) + ' ' + value.substring(5);
            }

            e.target.value = formattedValue;
        });

        phoneInput.addEventListener('focus', function(e) {
            if (!e.target.value || e.target.value.trim() === '') {
                e.target.value = '';
            }
        });

        // Validation modal functions
        function showValidationModal(message, showLogin = false) {
            document.getElementById('validationMessage').textContent = message;
            const loginButton = document.getElementById('loginButton');
            if (showLogin) {
                loginButton.classList.remove('hidden');
            } else {
                loginButton.classList.add('hidden');
            }
            document.getElementById('validationModal').classList.remove('hidden');
        }

        document.getElementById('closeValidationModal').addEventListener('click', function() {
            document.getElementById('validationModal').classList.add('hidden');
        });

        document.getElementById('loginButton').addEventListener('click', function() {
            window.location.href = '/login';
        });

        // Email validation utility function (kept for potential future use)
        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        function togglePasswordVisibility(inputId, buttonEl) {
            const input = document.getElementById(inputId);
            if (!input) return;
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            const icon = buttonEl.querySelector('i');
            if (icon) {
                icon.className = isHidden ? 'fas fa-eye-slash' : 'fas fa-eye';
            }
        }

        function getLogoValidationError(file) {
            if (!file) return 'Company logo is required.';
            const maxSize = 5 * 1024 * 1024; // 5MB
            if (file.size > maxSize) return 'Company logo must be 5MB or less.';
            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
            if (!allowedTypes.includes(file.type)) return 'Company logo must be PNG, JPG, or GIF format.';
            return null;
        }

        // Form submission - server-side validation only
        document.querySelector('form').addEventListener('submit', async function(e) {
            e.preventDefault();

            const logoFile = document.getElementById('logo').files[0];
            const logoError = getLogoValidationError(logoFile);
            if (logoError) {
                showValidationModal(logoError);
                return;
            }

            // Submit form via AJAX for server-side validation
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;

            // Show loading state
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';

            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    // Store registration token for step 2
                    if (result.registration_token) {
                        localStorage.setItem('provider_registration_token', result.registration_token);
                        console.log('Registration token stored:', result.registration_token.substring(0, 20) + '...');
                    }

                    // Success - redirect to next step
                    if (result.redirect_url) {
                        window.location.href = result.redirect_url;
                    } else {
                        window.location.href = '/register/provider/step2';
                    }
                } else {
                    // Handle server validation errors
                    if (result.errors) {
                        const firstError = Object.values(result.errors)[0];
                        const errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;

                        // Check if it's a login-related error
                        const loginErrors = [
                            'You have a registered company with this email you cannot create two accounts with the same email',
                            'You have a registered company with this phone you cannot create two accounts with the same phone',
                            'You have a submit company information wait for admin approval you will receive an email or a call from our support team , Thank you for your patience.'
                        ];

                        const showLogin = loginErrors.some(error => errorMessage.includes(error));
                        showValidationModal(errorMessage, showLogin);
                    } else {
                        showValidationModal(result.message || 'Registration failed. Please try again.');
                    }
                }
            } catch (error) {
                console.error('Registration error:', error);
                showValidationModal('Network error. Please check your connection and try again.');
            } finally {
                // Reset button state
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            }
        });
    </script>
</body>
</html>
