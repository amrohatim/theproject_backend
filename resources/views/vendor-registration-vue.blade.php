<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Data3Chic - Vendor Registration</title>
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
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
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
            background-color: rgba(59, 130, 246, 0.1);
        }
        .step-circle:focus {
            outline: 2px solid #3b82f6;
            outline-offset: 2px;
        }
        .upload-area {
            transition: border-color 0.3s ease;
        }
        .upload-area:hover {
            border-color: #3b82f6;
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
    </style>
</head>
<body class="min-h-screen bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Left Side - Marketing Content -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-blue-600 via-blue-700 to-blue-800 text-white p-12 flex-col justify-center">
            <div class="max-w-md mx-auto space-y-8">
                <!-- Logo -->
                <div class="text-center">
                    <h1 class="text-3xl font-bold mb-8">Data3Chic</h1>
                </div>

                <!-- Main Heading -->
                <div class="text-center space-y-4">
                    <h2 class="text-4xl font-bold leading-tight">
                        Join Our Vendor<br>Community
                    </h2>
                    <p class="text-blue-100 text-lg">
                        Expand your business reach and connect with thousands of customers<br>
                        looking for unique products like yours.
                    </p>
                </div>

                <!-- Features -->
                <div class="space-y-6 mt-12">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg mb-1">Grow Your Sales</h3>
                            <p class="text-blue-100 text-sm">Access to a large customer base and increase your revenue.</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg mb-1">Powerful Tools</h3>
                            <p class="text-blue-100 text-sm">Easy-to-use dashboard to manage your products and orders.</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg mb-1">Dedicated Support</h3>
                            <p class="text-blue-100 text-sm">Our team is here to help you succeed on our platform.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Registration Form -->
        <div class="w-full lg:w-1/2 bg-white p-8 lg:p-12 flex items-center justify-center">
            <div class="w-full max-w-md space-y-6">
                <div class="text-center space-y-2">
                    <h2 class="text-2xl font-bold text-gray-900">Create Vendor Account</h2>
                    <p class="text-gray-600">Complete all steps to get started</p>
                </div>

                <!-- Progress Indicator -->
                <div class="w-full mb-8">
                    <!-- Desktop Progress Indicator -->
                    <div class="hidden md:flex items-center justify-between" id="desktop-progress">
                        <div class="flex items-center">
                            <div class="flex flex-col items-center group">
                                <div class="step-circle w-12 h-12 rounded-full flex items-center justify-center text-sm font-semibold border-2 bg-blue-100 border-blue-600 text-blue-600"
                                     data-step="1"
                                     tabindex="0"
                                     role="button"
                                     aria-label="Step 1: Personal Info - Basic information"
                                     title="Personal Info - Basic information">
                                    1F
                                </div>
                                <div class="mt-3 text-center">
                                    <div class="text-xs font-semibold text-blue-600">Personal Info</div>
                                </div>
                            </div>
                            <div class="progress-line flex-1 h-1 mx-4 bg-gray-300 rounded-full"></div>
                        </div>
                        <div class="flex items-center">
                            <div class="flex flex-col items-center group">
                                <div class="step-circle w-12 h-12 rounded-full flex items-center justify-center text-sm font-semibold border-2 bg-gray-100 border-gray-300 text-gray-400"
                                     data-step="2"
                                     tabindex="0"
                                     role="button"
                                     aria-label="Step 2: Email Verification - Verify your email"
                                     title="Email Verification - Verify your email">
                                    2
                                </div>
                                <div class="mt-3 text-center">
                                    <div class="text-xs font-semibold text-gray-400">Email Verification</div>
                                </div>
                            </div>
                            <div class="progress-line flex-1 h-1 mx-4 bg-gray-300 rounded-full"></div>
                        </div>
                        <div class="flex items-center">
                            <div class="flex flex-col items-center group">
                                <div class="step-circle w-12 h-12 rounded-full flex items-center justify-center text-sm font-semibold border-2 bg-gray-100 border-gray-300 text-gray-400"
                                     data-step="3"
                                     tabindex="0"
                                     role="button"
                                     aria-label="Step 3: Phone Verification - Verify your phone"
                                     title="Phone Verification - Verify your phone">
                                    3
                                </div>
                                <div class="mt-3 text-center">
                                    <div class="text-xs font-semibold text-gray-400">Phone Verification</div>
                                </div>
                            </div>
                            <div class="progress-line flex-1 h-1 mx-4 bg-gray-300 rounded-full"></div>
                        </div>
                        <div class="flex items-center">
                            <div class="flex flex-col items-center group">
                                <div class="step-circle w-12 h-12 rounded-full flex items-center justify-center text-sm font-semibold border-2 bg-gray-100 border-gray-300 text-gray-400"
                                     data-step="4"
                                     tabindex="0"
                                     role="button"
                                     aria-label="Step 4: Company Info - Business details"
                                     title="Company Info - Business details">
                                    4
                                </div>
                                <div class="mt-3 text-center">
                                    <div class="text-xs font-semibold text-gray-400">Company Info</div>
                                </div>
                            </div>
                            <div class="progress-line flex-1 h-1 mx-4 bg-gray-300 rounded-full"></div>
                        </div>
                        <div class="flex items-center">
                            <div class="flex flex-col items-center group">
                                <div class="step-circle w-12 h-12 rounded-full flex items-center justify-center text-sm font-semibold border-2 bg-gray-100 border-gray-300 text-gray-400"
                                     data-step="5"
                                     tabindex="0"
                                     role="button"
                                     aria-label="Step 5: License Upload - Upload documents"
                                     title="License Upload - Upload documents">
                                    5
                                </div>
                                <div class="mt-3 text-center">
                                    <div class="text-xs font-semibold text-gray-400">License Upload</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Progress Indicator -->
                    <div class="md:hidden">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm font-semibold text-blue-600" id="mobile-step">Step 1 of 5</span>
                            <span class="text-xs text-gray-500" id="mobile-progress">20% Complete</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3 mb-4 shadow-inner">
                            <div class="h-3 rounded-full transition-all duration-500 ease-out shadow-sm" id="progress-bar" style="width: 20%; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);"></div>
                        </div>
                        <div class="text-center">
                            <h3 class="text-base font-semibold text-gray-900" id="mobile-title">Personal Info</h3>
                            <p class="text-xs text-gray-500" id="mobile-description">Basic information</p>
                        </div>

                        <!-- Mobile Step Dots -->
                        <div class="flex justify-center mt-4 space-x-2" id="mobile-dots">
                            <div class="w-2 h-2 rounded-full bg-blue-600 transition-all duration-300" data-mobile-step="1"></div>
                            <div class="w-2 h-2 rounded-full bg-gray-300 transition-all duration-300" data-mobile-step="2"></div>
                            <div class="w-2 h-2 rounded-full bg-gray-300 transition-all duration-300" data-mobile-step="3"></div>
                            <div class="w-2 h-2 rounded-full bg-gray-300 transition-all duration-300" data-mobile-step="4"></div>
                            <div class="w-2 h-2 rounded-full bg-gray-300 transition-all duration-300" data-mobile-step="5"></div>
                        </div>
                    </div>
                </div>

                <form id="registration-form" class="space-y-6">
                    <!-- Step 1: Personal Info -->
                    <div class="step-content active" id="step-1">
                        <div class="space-y-4">
                            <div class="space-y-2">
                                <label for="fullName" class="text-sm font-medium text-gray-700">Full Name</label>
                                <div class="relative">
                                    <input id="fullName" name="name" type="text" placeholder="Enter your full name" class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label for="email" class="text-sm font-medium text-gray-700">Email Address</label>
                                <div class="relative">
                                    <input id="email" name="email" type="email" placeholder="Enter your email" class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label for="phone" class="text-sm font-medium text-gray-700">Phone Number</label>
                                <div class="relative">
                                    <div class="absolute left-3 top-1/2 transform -translate-y-1/2 flex items-center">
                                        <span class="text-sm text-gray-600 mr-2">🇦🇪 +971</span>
                                    </div>
                                    <input id="phone" name="phone" type="tel" placeholder="Enter 9-digit number" maxlength="9" class="w-full pl-20 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label for="password" class="text-sm font-medium text-gray-700">Password</label>
                                <div class="relative">
                                    <input id="password" name="password" type="password" placeholder="Create a password" class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                    <svg class="absolute right-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 cursor-pointer" fill="none" stroke="currentColor" viewBox="0 0 24 24" onclick="togglePassword('password')">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label for="confirmPassword" class="text-sm font-medium text-gray-700">Confirm Password</label>
                                <input id="confirmPassword" name="password_confirmation" type="password" placeholder="Confirm your password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Email Verification -->
                    <div class="step-content" id="step-2">
                        <div class="space-y-4 text-center">
                            <div class="bg-blue-50 p-6 rounded-lg">
                                <svg class="w-12 h-12 text-blue-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Verify Your Email</h3>
                                <p class="text-gray-600 mb-4">
                                    We've sent a verification code to <strong id="email-display">your email</strong>
                                </p>
                            </div>

                            <div class="space-y-2">
                                <label for="verificationCode" class="text-sm font-medium text-gray-700">Verification Code</label>
                                <input id="verificationCode" name="verification_code" type="text" placeholder="Enter 6-digit code" maxlength="6" class="w-full px-3 py-2 border border-gray-300 rounded-md text-center text-lg tracking-widest focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <button type="button" id="resend-email-btn" class="text-blue-600 border border-blue-600 hover:bg-blue-50 bg-transparent px-4 py-2 rounded-md font-medium">
                                Resend Code
                            </button>
                        </div>
                    </div>

                    <!-- Step 3: Phone Verification -->
                    <div class="step-content" id="step-3">
                        <div class="space-y-4">
                            <div class="bg-blue-50 p-4 rounded-lg text-center">
                                <svg class="w-8 h-8 text-blue-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                <p class="text-sm text-gray-600">We'll send a verification code to your phone number</p>
                            </div>

                            <div class="space-y-2">
                                <label for="phoneCode" class="text-sm font-medium text-gray-700">SMS Verification Code</label>
                                <input id="phoneCode" name="otp_code" type="text" placeholder="Enter SMS code" maxlength="6" class="w-full px-3 py-2 border border-gray-300 rounded-md text-center focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <button type="button" id="resend-sms-btn" class="w-full text-blue-600 border border-blue-600 hover:bg-blue-50 bg-transparent px-4 py-2 rounded-md font-medium">
                                Resend SMS Code
                            </button>
                        </div>
                    </div>

                    <!-- Step 4: Company Info -->
                    <div class="step-content" id="step-4">
                        <div class="space-y-4">
                            <div class="space-y-2">
                                <label for="businessName" class="text-sm font-medium text-gray-700">Business Name</label>
                                <div class="relative">
                                    <input id="businessName" name="company_name" type="text" placeholder="Enter your business name" class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label for="companyEmail" class="text-sm font-medium text-gray-700">Company Email</label>
                                <div class="relative">
                                    <input id="companyEmail" name="company_email" type="email" placeholder="Enter company email" class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label for="primaryContact" class="text-sm font-medium text-gray-700">Primary Contact</label>
                                    <div class="relative">
                                        <div class="absolute left-3 top-1/2 transform -translate-y-1/2 flex items-center">
                                            <span class="text-sm text-gray-600 mr-2">🇦🇪 +971</span>
                                        </div>
                                        <input id="primaryContact" name="contact_number_1" type="tel" placeholder="9-digit number" maxlength="9" class="w-full pl-20 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label for="secondaryContact" class="text-sm font-medium text-gray-700">Secondary Contact (Optional)</label>
                                    <div class="relative">
                                        <div class="absolute left-3 top-1/2 transform -translate-y-1/2 flex items-center">
                                            <span class="text-sm text-gray-600 mr-2">🇦🇪 +971</span>
                                        </div>
                                        <input id="secondaryContact" name="contact_number_2" type="tel" placeholder="9-digit number" maxlength="9" class="w-full pl-20 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label for="emirate" class="text-sm font-medium text-gray-700">Emirate</label>
                                    <select id="emirate" name="emirate" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">Select emirate</option>
                                        <option value="Abu Dhabi">Abu Dhabi</option>
                                        <option value="Dubai">Dubai</option>
                                        <option value="Sharjah">Sharjah</option>
                                        <option value="Ajman">Ajman</option>
                                        <option value="Umm Al Quwain">Umm Al Quwain</option>
                                        <option value="Ras Al Khaimah">Ras Al Khaimah</option>
                                        <option value="Fujairah">Fujairah</option>
                                    </select>
                                </div>

                                <div class="space-y-2">
                                    <label for="city" class="text-sm font-medium text-gray-700">City</label>
                                    <input id="city" name="city" type="text" placeholder="Enter city name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label for="address" class="text-sm font-medium text-gray-700">Address</label>
                                <textarea id="address" name="address" rows="3" placeholder="Enter your business address" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                            </div>

                            <div class="space-y-2">
                                <label for="companyLogo" class="text-sm font-medium text-gray-700">Company Logo (Optional)</label>
                                <input id="companyLogo" name="logo" type="file" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <p class="text-xs text-gray-500">Upload your company logo (JPG, PNG, max 2MB)</p>
                            </div>
                        </div>
                    </div>

                    <!-- Step 5: License Upload -->
                    <div class="step-content" id="step-5">
                        <div class="space-y-4">
                            <div class="text-center mb-6">
                                <svg class="w-12 h-12 text-blue-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Upload Business License</h3>
                                <p class="text-gray-600">Please upload your business license or registration documents</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div class="space-y-2">
                                    <label for="licenseStartDate" class="text-sm font-medium text-gray-700">License Start Date</label>
                                    <input id="licenseStartDate" name="license_start_date" type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <div class="space-y-2">
                                    <label for="licenseEndDate" class="text-sm font-medium text-gray-700">License End Date</label>
                                    <input id="licenseEndDate" name="license_end_date" type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>

                            <div class="upload-area border-2 border-dashed border-gray-300 rounded-lg p-8 text-center cursor-pointer" onclick="document.getElementById('license-upload').click()">
                                <svg class="w-8 h-8 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <p class="text-gray-600 mb-2">
                                    <span class="font-medium text-blue-600 cursor-pointer">Click to upload</span> or drag and drop
                                </p>
                                <p class="text-sm text-gray-500">PDF files only, up to 10MB</p>
                                <input type="file" class="hidden" accept=".pdf" id="license-upload" name="license_file">
                            </div>

                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-yellow-800">
                                            <strong>Important:</strong> Make sure your business license is current and clearly visible.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between pt-4">
                        <button type="button" id="prev-btn" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                            Previous
                        </button>

                        <button type="button" id="next-btn" class="px-6 py-2 text-white rounded-md font-medium flex items-center space-x-2" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);" onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 16px rgba(59, 130, 246, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(59, 130, 246, 0.3)'">
                            <span id="next-btn-text">Next</span>
                            <span id="next-btn-loading" class="loading hidden"></span>
                            <svg id="next-btn-arrow" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>
                </form>

                <div class="text-center">
                    <p class="text-sm text-gray-600">
                        Already have an account?
                        <a href="/login" class="text-blue-600 hover:text-blue-700 font-medium">Log In</a>
                    </p>
                </div>
            </div>
        </div>

        <!-- Mobile Header for smaller screens -->
        <div class="lg:hidden absolute top-0 left-0 right-0 text-white p-4 text-center" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
            <h1 class="text-xl font-bold">Data3Chic</h1>
        </div>
    </div>

    <!-- Validation Error Modal -->
    <div id="error-modal" class="modal">
        <div class="bg-white rounded-lg p-6 max-w-md mx-auto">
            <div class="flex items-center mb-4">
                <svg class="w-6 h-6 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900">Validation Error</h3>
            </div>
            <p id="error-message" class="text-gray-600 mb-4"></p>
            <div class="flex justify-end space-x-3">
                <button id="error-modal-close" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                    Close
                </button>
                <button id="error-modal-login" class="px-4 py-2 text-white rounded-md hidden" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                    Go to Login
                </button>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="success-modal" class="modal">
        <div class="bg-white rounded-lg p-6 max-w-md mx-auto">
            <div class="text-center">
                <svg class="w-12 h-12 text-green-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Registration Complete!</h3>
                <p class="text-gray-600 mb-4">Your vendor registration has been submitted successfully. You will receive an email or call from our support team.</p>
                <button onclick="window.location.href='/login'" class="px-6 py-2 text-white rounded-md" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                    Go to Login
                </button>
            </div>
        </div>
    </div>

    <script>
        let currentStep = 1;
        const totalSteps = 5;
        let sessionData = {};

        const steps = [
            { name: "Personal Info", description: "Basic information" },
            { name: "Email Verification", description: "Verify your email" },
            { name: "Phone Verification", description: "Verify your phone" },
            { name: "Company Info", description: "Business details" },
            { name: "License Upload", description: "Upload documents" }
        ];

        // CSRF Token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Utility Functions
        function showModal(modalId) {
            document.getElementById(modalId).classList.add('show');
        }

        function hideModal(modalId) {
            document.getElementById(modalId).classList.remove('show');
        }

        function showErrorModal(message, showLoginButton = false) {
            document.getElementById('error-message').textContent = message;
            const loginBtn = document.getElementById('error-modal-login');
            if (showLoginButton) {
                loginBtn.classList.remove('hidden');
                loginBtn.onclick = () => window.location.href = '/login';
            } else {
                loginBtn.classList.add('hidden');
            }
            showModal('error-modal');
        }

        function formatPhoneNumber(input) {
            // Remove any non-digit characters
            let value = input.value.replace(/\D/g, '');

            // Limit to 9 digits
            if (value.length > 9) {
                value = value.substring(0, 9);
            }

            input.value = value;
        }

        function formatDateForDisplay(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${day}-${month}-${year}`;
        }

        function formatDateForStorage(dateString) {
            if (!dateString) return '';
            const parts = dateString.split('-');
            if (parts.length === 3) {
                return `${parts[2]}-${parts[1]}-${parts[0]}`; // Convert DD-MM-YYYY to YYYY-MM-DD
            }
            return dateString;
        }

        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
            field.setAttribute('type', type);
        }

        // Phone number formatting
        document.getElementById('phone').addEventListener('input', function() {
            formatPhoneNumber(this);
        });

        document.getElementById('primaryContact').addEventListener('input', function() {
            formatPhoneNumber(this);
        });

        document.getElementById('secondaryContact').addEventListener('input', function() {
            formatPhoneNumber(this);
        });

        // Validation Functions
        function validateEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }

        function validatePhone(phone) {
            return phone && phone.length === 9 && /^\d+$/.test(phone);
        }

        function validateStep1() {
            const name = document.getElementById('fullName').value.trim();
            const email = document.getElementById('email').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

            if (!name || name.length < 2) {
                showErrorModal('Name must be at least 2 characters long.');
                return false;
            }

            if (!email || !validateEmail(email)) {
                showErrorModal('Please enter a valid email address.');
                return false;
            }

            if (!phone || !validatePhone(phone)) {
                showErrorModal('Please enter a valid 9-digit UAE phone number.');
                return false;
            }

            if (!password || password.length < 8) {
                showErrorModal('Password must be at least 8 characters long.');
                return false;
            }

            if (password !== confirmPassword) {
                showErrorModal('Passwords do not match.');
                return false;
            }

            return true;
        }

        function validateStep4() {
            const businessName = document.getElementById('businessName').value.trim();
            const companyEmail = document.getElementById('companyEmail').value.trim();
            const primaryContact = document.getElementById('primaryContact').value.trim();
            const emirate = document.getElementById('emirate').value;
            const city = document.getElementById('city').value.trim();
            const address = document.getElementById('address').value.trim();
            const logoFile = document.getElementById('companyLogo').files[0];

            if (!businessName) {
                showErrorModal('Business name is required.');
                return false;
            }

            if (!companyEmail || !validateEmail(companyEmail)) {
                showErrorModal('Please enter a valid company email address.');
                return false;
            }

            if (!primaryContact || !validatePhone(primaryContact)) {
                showErrorModal('Please enter a valid primary contact number.');
                return false;
            }

            if (!emirate) {
                showErrorModal('Please select an emirate.');
                return false;
            }

            if (!city) {
                showErrorModal('City is required.');
                return false;
            }

            if (!address) {
                showErrorModal('Address is required.');
                return false;
            }

            // Validate logo file size if uploaded
            if (logoFile) {
                const maxSize = 2 * 1024 * 1024; // 2MB
                if (logoFile.size > maxSize) {
                    showErrorModal('Company logo file must be less than 2MB.');
                    return false;
                }

                const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/gif'];
                if (!allowedTypes.includes(logoFile.type)) {
                    showErrorModal('Company logo must be PNG, JPG, or GIF format.');
                    return false;
                }
            }

            return true;
        }

        function validateStep5() {
            const startDate = document.getElementById('licenseStartDate').value;
            const endDate = document.getElementById('licenseEndDate').value;
            const licenseFile = document.getElementById('license-upload').files[0];

            if (!startDate) {
                showErrorModal('License start date is required.');
                return false;
            }

            if (!endDate) {
                showErrorModal('License end date is required.');
                return false;
            }

            const today = new Date();
            const start = new Date(startDate);
            const end = new Date(endDate);

            if (start < today.setHours(0, 0, 0, 0)) {
                showErrorModal('License start date cannot be in the past.');
                return false;
            }

            if (end <= start) {
                showErrorModal('License end date must be after the start date.');
                return false;
            }

            if (!licenseFile) {
                showErrorModal('Please upload your business license.');
                return false;
            }

            const maxSize = 10 * 1024 * 1024; // 10MB
            if (licenseFile.size > maxSize) {
                showErrorModal('License file must be less than 10MB.');
                return false;
            }

            // Only allow PDF files
            const fileName = licenseFile.name.toLowerCase();
            const fileExtension = fileName.split('.').pop();
            if (fileExtension !== 'pdf' && licenseFile.type !== 'application/pdf') {
                showErrorModal('License file must be in PDF format only. Other file formats are not allowed.');
                return false;
            }

            return true;
        }

        function updateProgressIndicator() {
            // Update desktop progress indicator
            const desktopProgress = document.getElementById('desktop-progress');
            const circles = desktopProgress.querySelectorAll('.step-circle');
            const lines = desktopProgress.querySelectorAll('.progress-line');
            const labels = desktopProgress.querySelectorAll('.text-xs.font-semibold');

            circles.forEach((circle, index) => {
                const stepNumber = index + 1;
                const isCompleted = stepNumber < currentStep;
                const isCurrent = stepNumber === currentStep;
                const isClickable = stepNumber < currentStep; // Only completed steps are clickable

                // Remove existing classes and add base classes
                circle.className = 'step-circle w-12 h-12 rounded-full flex items-center justify-center text-sm font-semibold border-2 transition-all duration-300';

                if (isCompleted) {
                    // Completed step
                    circle.className += ' text-white completed clickable';
                    circle.style.background = 'linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%)';
                    circle.style.borderColor = '#3b82f6';
                    circle.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
                    labels[index].className = 'text-xs font-semibold text-blue-600';
                    circle.style.cursor = 'pointer';
                    circle.setAttribute('aria-disabled', 'false');
                } else if (isCurrent) {
                    // Current step
                    circle.className += ' bg-blue-100 border-blue-600 text-blue-600 ring-4 ring-blue-200';
                    circle.textContent = stepNumber;
                    labels[index].className = 'text-xs font-semibold text-blue-600';
                    circle.style.cursor = 'default';
                    circle.setAttribute('aria-disabled', 'true');
                } else {
                    // Future step
                    circle.className += ' bg-gray-100 border-gray-300 text-gray-400';
                    circle.textContent = stepNumber;
                    labels[index].className = 'text-xs font-semibold text-gray-400';
                    circle.style.cursor = 'not-allowed';
                    circle.setAttribute('aria-disabled', 'true');
                }

                // Update data attributes
                circle.setAttribute('data-step', stepNumber);
                circle.setAttribute('data-completed', isCompleted);
                circle.setAttribute('data-current', isCurrent);
            });

            lines.forEach((line, index) => {
                if (index + 1 < currentStep) {
                    line.className = 'progress-line flex-1 h-1 mx-4 rounded-full transition-all duration-500';
                    line.style.background = 'linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%)';
                } else {
                    line.className = 'progress-line flex-1 h-1 mx-4 bg-gray-300 rounded-full transition-all duration-500';
                    line.style.background = '';
                }
            });

            // Update mobile progress indicator
            const progressPercentage = Math.round((currentStep / totalSteps) * 100);
            document.getElementById('mobile-step').textContent = `Step ${currentStep} of ${totalSteps}`;
            document.getElementById('mobile-progress').textContent = `${progressPercentage}% Complete`;
            document.getElementById('progress-bar').style.width = `${progressPercentage}%`;
            document.getElementById('mobile-title').textContent = steps[currentStep - 1].name;
            document.getElementById('mobile-description').textContent = steps[currentStep - 1].description;

            // Update mobile dots
            const mobileDots = document.querySelectorAll('[data-mobile-step]');
            mobileDots.forEach((dot, index) => {
                const stepNumber = index + 1;
                if (stepNumber < currentStep) {
                    dot.className = 'w-2 h-2 rounded-full bg-blue-600 transition-all duration-300';
                } else if (stepNumber === currentStep) {
                    dot.className = 'w-3 h-3 rounded-full bg-blue-600 transition-all duration-300 ring-2 ring-blue-200';
                } else {
                    dot.className = 'w-2 h-2 rounded-full bg-gray-300 transition-all duration-300';
                }
            });
        }

        function showStep(step) {
            // Hide all steps
            document.querySelectorAll('.step-content').forEach(content => {
                content.classList.remove('active');
            });

            // Show current step
            document.getElementById(`step-${step}`).classList.add('active');

            // Update buttons
            const prevBtn = document.getElementById('prev-btn');
            const nextBtn = document.getElementById('next-btn');
            const nextBtnText = document.getElementById('next-btn-text');

            prevBtn.disabled = step === 1;

            if (step === totalSteps) {
                nextBtnText.textContent = 'Complete Registration';
            } else {
                nextBtnText.textContent = 'Next';
            }

            updateProgressIndicator();
        }

        function setLoading(loading) {
            const nextBtn = document.getElementById('next-btn');
            const nextBtnText = document.getElementById('next-btn-text');
            const nextBtnLoading = document.getElementById('next-btn-loading');
            const nextBtnArrow = document.getElementById('next-btn-arrow');

            nextBtn.disabled = loading;

            if (loading) {
                nextBtnText.style.display = 'none';
                nextBtnArrow.style.display = 'none';
                nextBtnLoading.classList.remove('hidden');
            } else {
                nextBtnText.style.display = 'inline';
                nextBtnArrow.style.display = 'inline';
                nextBtnLoading.classList.add('hidden');
            }
        }

        // API Functions
        async function submitPersonalInfo() {
            const formData = new FormData();
            formData.append('name', document.getElementById('fullName').value.trim());
            formData.append('email', document.getElementById('email').value.trim());
            formData.append('phone', '+971' + document.getElementById('phone').value.trim());
            formData.append('password', document.getElementById('password').value);
            formData.append('password_confirmation', document.getElementById('confirmPassword').value);

            try {
                const response = await fetch('/api/vendor-registration/info', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    sessionData.personalInfo = data.data;

                    // Check if we should skip to license upload step (rule 6 from validation guide)
                    if (data.skip_to_step) {
                        currentStep = data.skip_to_step;
                        showStep(currentStep);
                        return false; // Don't proceed to next step normally
                    }

                    // Update email display for step 2
                    document.getElementById('email-display').textContent = document.getElementById('email').value.trim();
                    return true;
                } else {
                    // Handle specific validation errors from the guide
                    if (data.errors) {
                        if (data.errors.name && data.errors.name.includes('already taken')) {
                            showErrorModal('Full name is already taken.');
                        } else if (data.errors.email) {
                            const emailError = data.errors.email[0];
                            if (emailError.includes('registered company') && emailError.includes('verified')) {
                                showErrorModal('You have a registered company with this email you cannot create two accounts with the same email, please log in', data.show_login || false);
                            } else if (emailError.includes('license_completed')) {
                                showErrorModal('You have a submit company information wait for admin approval you will receive an email or a call from our support team, Thank you for your patience.', data.show_login || false);
                            } else {
                                showErrorModal(emailError);
                            }
                        } else if (data.errors.phone && data.errors.phone.includes('registered company')) {
                            showErrorModal('You have a registered company with this phone you cannot create two accounts with the same phone', data.show_login || false);
                        } else {
                            showErrorModal(data.message || 'Validation failed. Please check your information.');
                        }
                    } else {
                        showErrorModal(data.message || 'Registration failed. Please try again.');
                    }
                    return false;
                }
            } catch (error) {
                console.error('Error:', error);
                showErrorModal('Network error. Please check your connection and try again.');
                return false;
            }
        }

        async function verifyEmail() {
            const verificationCode = document.getElementById('verificationCode').value.trim();

            if (!verificationCode || verificationCode.length !== 6) {
                showErrorModal('Please enter a valid 6-digit verification code.');
                return false;
            }

            try {
                const response = await fetch('/api/vendor-registration/verify-email', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        verification_code: verificationCode
                    })
                });

                const data = await response.json();

                if (data.success) {
                    sessionData.emailVerified = true;
                    return true;
                } else {
                    showErrorModal(data.message || 'Email verification failed. Please try again.');
                    return false;
                }
            } catch (error) {
                console.error('Error:', error);
                showErrorModal('Network error. Please check your connection and try again.');
                return false;
            }
        }

        async function sendPhoneOtp() {
            try {
                const response = await fetch('/api/vendor-registration/send-otp', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                return data.success;
            } catch (error) {
                console.error('Error:', error);
                return false;
            }
        }

        async function verifyPhone() {
            const otpCode = document.getElementById('phoneCode').value.trim();

            if (!otpCode || otpCode.length !== 6) {
                showErrorModal('Please enter a valid 6-digit SMS code.');
                return false;
            }

            try {
                const response = await fetch('/api/vendor-registration/verify-otp', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        otp_code: otpCode
                    })
                });

                const data = await response.json();

                if (data.success) {
                    sessionData.phoneVerified = true;
                    return true;
                } else {
                    showErrorModal(data.message || 'Phone verification failed. Please try again.');
                    return false;
                }
            } catch (error) {
                console.error('Error:', error);
                showErrorModal('Network error. Please check your connection and try again.');
                return false;
            }
        }

        async function submitCompanyInfo() {
            const formData = new FormData();
            formData.append('name', document.getElementById('businessName').value.trim());
            formData.append('email', document.getElementById('companyEmail').value.trim());
            formData.append('contact_number_1', '+971' + document.getElementById('primaryContact').value.trim());

            const secondaryContact = document.getElementById('secondaryContact').value.trim();
            if (secondaryContact) {
                formData.append('contact_number_2', '+971' + secondaryContact);
            }

            formData.append('emirate', document.getElementById('emirate').value);
            formData.append('city', document.getElementById('city').value.trim());
            formData.append('address', document.getElementById('address').value.trim());

            // Add optional company logo
            const logoFile = document.getElementById('companyLogo').files[0];
            if (logoFile) {
                formData.append('logo', logoFile);
            }

            try {
                const response = await fetch('/api/vendor-registration/company', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    sessionData.companyInfo = data.data;
                    sessionData.userId = data.user_id;
                    return true;
                } else {
                    showErrorModal(data.message || 'Company information submission failed. Please try again.');
                    return false;
                }
            } catch (error) {
                console.error('Error:', error);
                showErrorModal('Network error. Please check your connection and try again.');
                return false;
            }
        }

        async function submitLicense() {
            const formData = new FormData();
            const licenseFile = document.getElementById('license-upload').files[0];
            const startDate = document.getElementById('licenseStartDate').value;
            const endDate = document.getElementById('licenseEndDate').value;

            formData.append('license_file', licenseFile);
            formData.append('start_date', startDate); // This will be saved to licenses.start_date
            formData.append('end_date', endDate);

            if (sessionData.userId) {
                formData.append('user_id', sessionData.userId);
            }

            try {
                const response = await fetch('/api/vendor-registration/license', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    showModal('success-modal');
                    return true;
                } else {
                    showErrorModal(data.message || 'License upload failed. Please try again.');
                    return false;
                }
            } catch (error) {
                console.error('Error:', error);
                showErrorModal('Network error. Please check your connection and try again.');
                return false;
            }
        }

        // Main Navigation Functions
        async function nextStep() {
            setLoading(true);

            try {
                let success = false;

                switch (currentStep) {
                    case 1:
                        if (validateStep1()) {
                            success = await submitPersonalInfo();
                        }
                        break;
                    case 2:
                        success = await verifyEmail();
                        break;
                    case 3:
                        success = await verifyPhone();
                        break;
                    case 4:
                        if (validateStep4()) {
                            success = await submitCompanyInfo();
                        }
                        break;
                    case 5:
                        if (validateStep5()) {
                            success = await submitLicense();
                            return; // Don't proceed to next step, show success modal instead
                        }
                        break;
                }

                if (success && currentStep < totalSteps) {
                    // Add completion animation to current step
                    const currentCircle = document.querySelector(`[data-step="${currentStep}"]`);
                    if (currentCircle) {
                        currentCircle.classList.add('completed');
                        setTimeout(() => {
                            currentCircle.classList.remove('completed');
                        }, 600);
                    }

                    currentStep++;
                    showStep(currentStep);

                    // Send OTP when moving to phone verification step
                    if (currentStep === 3) {
                        await sendPhoneOtp();
                    }
                }
            } finally {
                setLoading(false);
            }
        }

        function prevStep() {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
            }
        }

        // Step Navigation Functions
        function goToStep(targetStep) {
            if (targetStep < currentStep && targetStep >= 1) {
                // Only allow going back to completed steps
                currentStep = targetStep;
                showStep(currentStep);
            }
        }

        // Event Listeners
        document.getElementById('next-btn').addEventListener('click', nextStep);
        document.getElementById('prev-btn').addEventListener('click', prevStep);

        // Add click listeners for step indicators
        document.addEventListener('click', function(e) {
            if (e.target.closest('.step-circle')) {
                const circle = e.target.closest('.step-circle');
                const targetStep = parseInt(circle.getAttribute('data-step'));
                const isCompleted = circle.getAttribute('data-completed') === 'true';

                if (isCompleted) {
                    goToStep(targetStep);
                }
            }
        });

        // Add keyboard navigation for step indicators
        document.addEventListener('keydown', function(e) {
            if (e.target.classList.contains('step-circle')) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    const targetStep = parseInt(e.target.getAttribute('data-step'));
                    const isCompleted = e.target.getAttribute('data-completed') === 'true';

                    if (isCompleted) {
                        goToStep(targetStep);
                    }
                }

                // Arrow key navigation
                if (e.key === 'ArrowLeft' || e.key === 'ArrowRight') {
                    e.preventDefault();
                    const allCircles = Array.from(document.querySelectorAll('.step-circle'));
                    const currentIndex = allCircles.indexOf(e.target);
                    let nextIndex;

                    if (e.key === 'ArrowLeft') {
                        nextIndex = Math.max(0, currentIndex - 1);
                    } else {
                        nextIndex = Math.min(allCircles.length - 1, currentIndex + 1);
                    }

                    allCircles[nextIndex].focus();
                }
            }
        });

        // Modal event listeners
        document.getElementById('error-modal-close').addEventListener('click', function() {
            hideModal('error-modal');
        });

        // Resend buttons
        document.getElementById('resend-email-btn').addEventListener('click', async function() {
            this.disabled = true;
            this.textContent = 'Sending...';

            try {
                const response = await fetch('/api/vendor-registration/verify-email', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ resend: true })
                });

                const data = await response.json();
                if (data.success) {
                    alert('Verification code sent successfully!');
                } else {
                    alert('Failed to send verification code. Please try again.');
                }
            } catch (error) {
                alert('Network error. Please try again.');
            } finally {
                this.disabled = false;
                this.textContent = 'Resend Code';
            }
        });

        document.getElementById('resend-sms-btn').addEventListener('click', async function() {
            this.disabled = true;
            this.textContent = 'Sending...';

            try {
                const success = await sendPhoneOtp();
                if (success) {
                    alert('SMS code sent successfully!');
                } else {
                    alert('Failed to send SMS code. Please try again.');
                }
            } catch (error) {
                alert('Network error. Please try again.');
            } finally {
                this.disabled = false;
                this.textContent = 'Resend SMS Code';
            }
        });

        // File upload functionality with PDF validation
        document.getElementById('license-upload').addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                const file = e.target.files[0];
                const fileName = file.name;
                const fileExtension = fileName.split('.').pop().toLowerCase();
                const uploadArea = document.querySelector('.upload-area p');

                // Check if file is PDF
                if (fileExtension !== 'pdf') {
                    showErrorModal('Please select a PDF file only. Other file formats are not allowed for license upload.');
                    e.target.value = ''; // Clear the file input
                    uploadArea.innerHTML = `<span class="font-medium text-blue-600 cursor-pointer">Click to upload</span> or drag and drop`;
                    return;
                }

                // Check file size (10MB limit)
                const maxSize = 10 * 1024 * 1024; // 10MB
                if (file.size > maxSize) {
                    showErrorModal('File size must be less than 10MB.');
                    e.target.value = ''; // Clear the file input
                    uploadArea.innerHTML = `<span class="font-medium text-blue-600 cursor-pointer">Click to upload</span> or drag and drop`;
                    return;
                }

                uploadArea.innerHTML = `<span class="font-medium text-blue-600">Selected: ${fileName}</span>`;
            }
        });

        // Initialize
        showStep(currentStep);
    </script>
</body>
</html>
