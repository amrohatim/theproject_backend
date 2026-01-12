@extends('layouts.provider')

@section('title', __('provider.settings'))

@section('content')
<style>
    [dir="rtl"] .grid {
        direction: rtl;
    }
    [dir="rtl"] .flex {
        direction: rtl;
    }
    [dir="rtl"] .text-left {
        text-align: right;
    }
    [dir="rtl"] .text-right {
        text-align: left;
    }
    [dir="rtl"] .mr-2 {
        margin-right: 0;
        margin-left: 0.5rem;
    }
    [dir="rtl"] .ml-2 {
        margin-left: 0;
        margin-right: 0.5rem;
    }
    [dir="rtl"] .mr-1 {
        margin-right: 0;
        margin-left: 0.25rem;
    }
    [dir="rtl"] .ml-1 {
        margin-left: 0;
        margin-right: 0.25rem;
    }
</style>
<!-- Tailwind CSS -->
<script src="https://cdn.tailwindcss.com"></script>
<!-- Lucide Icons -->
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

<div class="min-h-screen " dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <div class="container mx-auto px-4 py-8 max-w-6xl">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-2">
                {{-- <div class="p-2 bg-blue-100 rounded-lg">
                    <i data-lucide="settings" class="w-6 h-6 text-blue-600"></i>
                </div> --}}
                <h1 class="text-3xl font-bold text-gray-900">{{ __('provider.settings') }}</h1>
            </div>
            <p class="text-gray-600">{{ __('provider.manage_account_settings') }}</p>
        </div>

        <!-- Alert Container -->
        <div id="alert-container" class="mb-6"></div>

        <!-- Success Alert (Static for demo) -->
        <div class="mb-6 border-green-200 bg-green-50 rounded p-4" style="display: none;" id="success-alert">
            <p class="text-green-800">{{ __('provider.delivery_settings_updated_successfully') }}</p>
        </div>

        <!-- Quick Links -->
        <div class="mb-8 shadow-sm border-0 bg-white/70 backdrop-blur-sm rounded-xl">
            <div class="p-6">
                <div class="flex items-center gap-2 mb-2">
                    <i data-lucide="external-link" class="w-5 h-5 text-blue-600"></i>
                    <h2 class="text-xl font-semibold">{{ __('provider.quick_links') }}</h2>
                </div>
                <p class="text-gray-600 mb-4">{{ __('provider.access_frequently_used_settings') }}</p>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Profile -->
                    <a href="{{ route('provider.profile.index') }}" class="block p-4 rounded-xl border-2 border-dashed border-gray-200 hover:border-blue-300 transition-all duration-200 hover:shadow-md hover:-translate-y-1">
                        <div class="inline-flex p-3 rounded-lg bg-blue-50 text-blue-600 border border-blue-200 mb-3">
                            <i data-lucide="user" class="w-6 h-6"></i>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-1">{{ __('provider.profile') }}</h3>
                        <p class="text-sm text-gray-600">{{ __('provider.update_name_email_image') }}</p>
                    </a>
                    <!-- Password -->
                    <a href="{{ route('provider.profile.index') }}#password-section" class="block p-4 rounded-xl border-2 border-dashed border-gray-200 hover:border-blue-300 transition-all duration-200 hover:shadow-md hover:-translate-y-1">
                        <div class="inline-flex p-3 rounded-lg bg-green-50 text-green-600 border border-green-200 mb-3">
                            <i data-lucide="lock" class="w-6 h-6"></i>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-1">{{ __('provider.password') }}</h3>
                        <p class="text-sm text-gray-600">{{ __('provider.change_your_password') }}</p>
                    </a>
                    <!-- Locations -->
                    <a href="{{ route('provider.locations.index') }}" class="block p-4 rounded-xl border-2 border-dashed border-gray-200 hover:border-blue-300 transition-all duration-200 hover:shadow-md hover:-translate-y-1">
                        <div class="inline-flex p-3 rounded-lg bg-purple-50 text-purple-600 border border-purple-200 mb-3">
                            <i data-lucide="map-pin" class="w-6 h-6"></i>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-1">{{ __('provider.locations') }}</h3>
                        <p class="text-sm text-gray-600">{{ __('provider.manage_your_locations') }}</p>
                    </a>
                    <!-- License -->
                    <a href="#license-section" class="block p-4 rounded-xl border-2 border-dashed border-gray-200 hover:border-blue-300 transition-all duration-200 hover:shadow-md hover:-translate-y-1">
                        <div class="inline-flex p-3 rounded-lg bg-orange-50 text-orange-600 border border-orange-200 mb-3">
                            <i data-lucide="award" class="w-6 h-6"></i>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-1">{{ __('provider.license') }}</h3>
                        <p class="text-sm text-gray-600">{{ __('provider.upload_manage_license') }}</p>
                    </a>
                </div>
            </div>
        </div>

        <!-- Delivery Settings -->
        <div class="mb-8 shadow-sm border-0 bg-white/70 backdrop-blur-sm rounded-xl">
            <div class="p-6">
                <div class="flex items-center gap-2 mb-2">
                    <i data-lucide="truck" class="w-5 h-5 text-green-600"></i>
                    <h2 class="text-xl font-semibold">{{ __('provider.delivery_settings') }}</h2>
                </div>
                <p class="text-gray-600 mb-4">{{ __('provider.configure_delivery_options') }}</p>

                <form id="delivery-form">
                    @csrf
                    <div class="space-y-6">
                        <!-- Delivery Toggle (always on) -->
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <label class="text-base font-medium">{{ __('provider.enable_delivery_service') }}</label>
                                <p class="text-sm text-gray-600">{{ __('provider.allow_customers_request_delivery') }}</p>
                            </div>
                            <input type="checkbox" id="delivery_capability" name="delivery_capability"
                                   {{ $provider->delivery_capability ? 'checked' : '' }}
                                   class="toggle toggle-primary" />
                        </div>

                        <!-- Fees by Emirate -->
                        <div id="delivery-fees-section" class="{{ $provider->delivery_capability ? '' : 'hidden' }} space-y-4">
                            <div class="flex items-center gap-2 mb-4">
                                <i data-lucide="map-pin" class="w-4 h-4 text-gray-600"></i>
                                <h4 class="font-semibold text-gray-900">{{ __('provider.delivery_fees_by_emirate') }}</h4>
                            </div>
                            <div class="grid gap-3">
                                @php
                                    $emirates = ['Abu Dhabi', 'Dubai', 'Sharjah', 'Ajman', 'Ras Al Khaimah', 'Fujairah', 'Umm Al Quwain'];
                                    $currentFees = $provider->delivery_fees ?? [];
                                @endphp
                                @foreach($emirates as $emirate)
                                    @php
                                        $emirateKey = strtolower(str_replace(' ', '_', $emirate));
                                        $fee = $currentFees[$emirateKey] ?? ($emirate == 'Abu Dhabi' ? 15.00 : ($emirate == 'Dubai' ? 12.00 : 10.00));
                                    @endphp
                                    <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-lg">
                                        <label class="flex-1 font-medium text-gray-700">{{ $emirate }}</label>
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-medium text-gray-600">AED</span>
                                            <input type="number" name="delivery_fees[{{ $loop->index }}][fee]"
                                                   value="{{ $fee }}" min="0" step="0.01"
                                                   class="w-24 text-right border rounded px-2 py-1" />
                                            <input type="hidden" name="delivery_fees[{{ $loop->index }}][emirate]" value="{{ $emirate }}">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <button type="submit" class="flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">
                            <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                            {{ __('provider.save_delivery_settings') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- License Management -->
        <div id="license-section" class="shadow-sm border-0 bg-white/70 backdrop-blur-sm rounded-xl">
            <div class="p-6">
                <div class="flex items-center gap-2 mb-2">
                    <i data-lucide="award" class="w-5 h-5 text-purple-600"></i>
                    <h2 class="text-xl font-semibold">{{ __('provider.license_management') }}</h2>
                </div>
                <p class="text-gray-600 mb-6">{{ __('provider.manage_business_license') }}</p>
                @if($license)
                    <!-- Current License -->
                    <div class="p-6 bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl border border-blue-100 mb-6">
                        <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <i data-lucide="file-text" class="w-4 h-4"></i>
                            {{ __('provider.current_license') }}
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                            <div>
                                <label class="text-sm font-medium text-gray-600">{{ __('provider.status') }}</label>
                                <div class="mt-1 inline-flex items-center bg-green-100 text-green-800 px-2 py-1 rounded">
                                    <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                                    {{ __('provider.active') }}
                                </div>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">{{ __('provider.start_date') }}</label>
                                <p class="mt-1 font-medium text-gray-900">{{ $license->start_date->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">{{ __('provider.end_date') }}</label>
                                <p class="mt-1 font-medium text-gray-900">{{ $license->end_date->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">{{ __('provider.days_left') }}</label>
                                <p class="mt-1 font-medium text-gray-900">{{ $license->daysUntilExpiration() }} {{ __('provider.days') }}</p>
                            </div>
                        </div>
                        @if($license->license_file_path)
                            <button class="flex items-center px-3 py-1 border border-blue-200 text-blue-700 rounded hover:bg-blue-50" onclick="window.open('{{ route('provider.settings.license.view') }}', '_blank')">
                                <i data-lucide="file-text" class="w-4 h-4 mr-2"></i>
                                {{ __('provider.view_license_file') }}
                            </button>
                        @endif
                    </div>

                    <hr class="my-6"/>
                @endif

                <!-- Upload New License -->
                <div>
                    <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <i data-lucide="upload" class="w-4 h-4"></i>
                        {{ $license ? __('provider.upload_new_license') : __('provider.upload_license') }}
                    </h3>
                    <form id="license-form" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="start_date" class="block font-medium">{{ __('provider.start_date') }}</label>
                                <input type="date" id="start_date" name="start_date" class="mt-1 w-full border rounded px-2 py-1" required />
                            </div>
                            <div>
                                <label for="end_date" class="block font-medium">{{ __('provider.end_date') }}</label>
                                <input type="date" id="end_date" name="end_date" class="mt-1 w-full border rounded px-2 py-1" required />
                            </div>
                        </div>
                        <div>
                            <label for="license_file" class="block font-medium">{{ __('provider.license_file_pdf') }}</label>
                            <input type="file" id="license_file" name="license_file" accept=".pdf" class="mt-1 w-full" required />
                            <p class="text-sm text-gray-600 mt-1">{{ __('provider.maximum_file_size_10mb') }}</p>
                        </div>
                        <div>
                            <label for="notes" class="block font-medium">{{ __('provider.notes_optional') }}</label>
                            <textarea id="notes" name="notes" rows="3" class="mt-1 w-full border rounded px-2 py-1" placeholder="{{ __('provider.additional_notes_placeholder') }}"></textarea>
                        </div>
                        <button type="submit" class="flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded">
                            <i data-lucide="upload" class="w-4 h-4 mr-2"></i>
                            {{ __('provider.upload_license') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
    </div>
</div>

<!-- License Upload Confirmation Modal -->
<div id="license-confirm-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 p-6">
        <div class="flex items-start gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-amber-600"></i>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">{{ __('provider.license_confirm_title') }}</h3>
                <p class="text-sm text-gray-600 mt-1">
                    {{ __('provider.license_confirm_message') }}
                </p>
            </div>
        </div>
        <div class="text-sm text-gray-600 mb-4">
            {{ __('provider.license_confirm_countdown_prefix') }}
            <span id="license-countdown" class="font-semibold text-gray-900">10</span>
            {{ __('provider.license_confirm_countdown_suffix') }}
        </div>
        <div class="flex justify-end gap-2">
            <button type="button" id="license-cancel-btn"
                    class="px-4 py-2 text-sm font-medium rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50">
                {{ __('provider.cancel') }}
            </button>
            <button type="button" id="license-confirm-btn" disabled
                    class="px-4 py-2 text-sm font-medium rounded-md bg-green-600 text-white disabled:bg-gray-300 disabled:text-gray-500">
                {{ __('provider.upload_license') }}
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    if (typeof lucide !== 'undefined' && lucide.createIcons) {
        lucide.createIcons();
    }

    // Handle delivery capability toggle
    const deliveryCapability = document.getElementById('delivery_capability');
    const deliveryFeesSection = document.getElementById('delivery-fees-section');

    deliveryCapability.addEventListener('change', function() {
        if (this.checked) {
            deliveryFeesSection.classList.remove('hidden');
        } else {
            deliveryFeesSection.classList.add('hidden');
        }
    });
    
    // Handle delivery form submission
    document.getElementById('delivery-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitButton = this.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        
        // Show loading state
        submitButton.disabled = true;
        submitButton.innerHTML = `
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Saving...
        `;
        
        const formData = new FormData(this);
        
        // Convert checkbox value to boolean
        const deliveryCapability = document.getElementById('delivery_capability').checked;
        formData.set('delivery_capability', deliveryCapability ? '1' : '0');
        
        fetch('{{ route("provider.settings.delivery") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', '{{ __('provider.delivery_settings_updated_successfully') }}');
            } else {
                showAlert('error', data.message || '{{ __('provider.error_updating_delivery_settings') }}');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', '{{ __('provider.error_updating_delivery_settings') }}');
        })
        .finally(() => {
            // Restore button state
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        });
    });
    
    const licenseForm = document.getElementById('license-form');
    const licenseModal = document.getElementById('license-confirm-modal');
    const licenseCountdown = document.getElementById('license-countdown');
    const licenseConfirmBtn = document.getElementById('license-confirm-btn');
    const licenseCancelBtn = document.getElementById('license-cancel-btn');
    let licenseConfirmed = false;
    let countdownTimer = null;

    function resetLicenseModal() {
        if (!licenseCountdown || !licenseConfirmBtn) {
            return;
        }
        licenseCountdown.textContent = '10';
        licenseConfirmBtn.disabled = true;
        if (countdownTimer) {
            clearInterval(countdownTimer);
            countdownTimer = null;
        }
    }

    function openLicenseModal() {
        if (!licenseModal) {
            return;
        }
        resetLicenseModal();
        licenseModal.classList.remove('hidden');
        licenseModal.classList.add('flex');
        let remaining = 10;
        countdownTimer = setInterval(() => {
            remaining -= 1;
            if (licenseCountdown) {
                licenseCountdown.textContent = String(remaining);
            }
            if (remaining <= 0) {
                clearInterval(countdownTimer);
                countdownTimer = null;
                if (licenseConfirmBtn) {
                    licenseConfirmBtn.disabled = false;
                }
            }
        }, 1000);
    }

    function closeLicenseModal() {
        if (!licenseModal) {
            return;
        }
        licenseModal.classList.add('hidden');
        licenseModal.classList.remove('flex');
        resetLicenseModal();
    }

    function submitLicenseForm() {
        if (!licenseForm) {
            return;
        }
        const submitButton = licenseForm.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;

        // Show loading state
        submitButton.disabled = true;
        submitButton.innerHTML = `
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Uploading...
        `;
        
        const formData = new FormData(licenseForm);
        
        fetch('{{ route("provider.settings.license") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', '{{ __('provider.license_uploaded_successfully') }}');
                // Reset form
                licenseForm.reset();
                // Reload page to show updated license info
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                if (data.errors) {
                    let errorMessages = [];
                    Object.values(data.errors).forEach(errors => {
                        errors.forEach(error => {
                            errorMessages.push(error);
                        });
                    });
                    showAlert('error', 'Please fix the following errors: ' + errorMessages.join(', '));
                } else {
                    showAlert('error', data.message || '{{ __('provider.error_uploading_license') }}');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', '{{ __('provider.error_uploading_license') }}');
        })
        .finally(() => {
            licenseConfirmed = false;
            // Restore button state
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        });
    }

    if (licenseForm) {
        licenseForm.addEventListener('submit', function(e) {
            if (!licenseConfirmed) {
                e.preventDefault();
                openLicenseModal();
                return;
            }
            e.preventDefault();
            submitLicenseForm();
        });
    }

    if (licenseCancelBtn) {
        licenseCancelBtn.addEventListener('click', function() {
            closeLicenseModal();
        });
    }

    if (licenseConfirmBtn) {
        licenseConfirmBtn.addEventListener('click', function() {
            licenseConfirmed = true;
            closeLicenseModal();
            submitLicenseForm();
        });
    }
    
    // File upload preview
    document.getElementById('license_file').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const fileInfo = document.createElement('div');
            fileInfo.className = 'mt-2 text-sm text-gray-600';
            fileInfo.innerHTML = `Selected: ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
            
            // Remove any existing file info
            const existingInfo = this.parentNode.parentNode.querySelector('.file-info');
            if (existingInfo) {
                existingInfo.remove();
            }
            
            fileInfo.className += ' file-info';
            this.parentNode.parentNode.appendChild(fileInfo);
        }
    });
    
    function showAlert(type, message) {
        const alertContainer = document.getElementById('alert-container');
        const alertDiv = document.createElement('div');
        
        const bgColor = type === 'success' ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200';
        const textColor = type === 'success' ? 'text-green-800' : 'text-red-800';
        const iconColor = type === 'success' ? 'text-green-400' : 'text-red-400';
        
        alertDiv.className = `${bgColor} border rounded-lg p-4 mb-4 transition-all duration-300 transform translate-y-0 opacity-100`;
        alertDiv.innerHTML = `
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    ${type === 'success' ? 
                        `<svg class="h-5 w-5 ${iconColor}" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>` :
                        `<svg class="h-5 w-5 ${iconColor}" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>`
                    }
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium ${textColor}">${message}</p>
                </div>
                <div class="ml-auto pl-3">
                    <button type="button" class="inline-flex ${textColor} hover:${textColor.replace('800', '600')} focus:outline-none" onclick="this.parentElement.parentElement.parentElement.remove()">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>
        `;
        
        alertContainer.appendChild(alertDiv);
        
        // Auto dismiss after 5 seconds
        setTimeout(() => {
            alertDiv.style.transform = 'translateY(-100%)';
            alertDiv.style.opacity = '0';
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 300);
        }, 5000);
    }
});
</script>
@endsection
