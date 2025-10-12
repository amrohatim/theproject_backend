@extends('layouts.dashboard')

@section('title', __('vendor.create_service_provider'))
@section('page-title', __('vendor.create_service_provider'))

@section('styles')
<style>
    /* Modern Form Styling */
    .form-input-container {
        position: relative;
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.5rem;
        transition: color 0.2s ease-in-out;
    }

    .dark .form-label {
        color: #d1d5db;
    }

    .form-input,
    .form-textarea,
    .form-select {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid #d1d5db;
        border-radius: 0.5rem;
        font-size: 1rem;
        line-height: 1.5;
        color: #111827;
        background-color: #ffffff;
        transition: all 0.2s ease-in-out;
        outline: none;
        position: relative;
    }

    .dark .form-input,
    .dark .form-textarea,
    .dark .form-select {
        background-color: #374151;
        border-color: #4b5563;
        color: #f9fafb;
    }

    /* Normal State */
    .form-input:hover,
    .form-textarea:hover,
    .form-select:hover {
        border-color: #9ca3af;
    }

    .dark .form-input:hover,
    .dark .form-textarea:hover,
    .dark .form-select:hover {
        border-color: #6b7280;
    }

    /* Active State (Focus) */
    .form-input.active,
    .form-textarea.active,
    .form-select.active,
    .form-input:focus,
    .form-textarea:focus,
    .form-select:focus {
        border-color: #3b82f6;
        box-shadow: none;
        outline: none;
    }

    .dark .form-input.active,
    .dark .form-textarea.active,
    .dark .form-select.active,
    .dark .form-input:focus,
    .dark .form-textarea:focus,
    .dark .form-select:focus {
        border-color: #3b82f6;
    }

    /* Filled State */
    .form-input.filled,
    .form-textarea.filled,
    .form-select.filled {
        border-color: #10b981;
    }

    .dark .form-input.filled,
    .dark .form-textarea.filled,
    .dark .form-select.filled {
        border-color: #10b981;
    }

    /* Valid State */
    .form-input.valid, .form-textarea.valid, .form-select.valid {
        border-color: #32936f;
        background-color: #F0FDF4;
    }

    .dark .form-input.valid, .dark .form-textarea.valid, .dark .form-select.valid {
        border-color: #32936f;
        background-color: #064E3B;
    }

    /* Valid State Label */
    .form-input-container.valid .form-label,
    .form-textarea-container.valid .form-label,
    .form-select-container.valid .form-label {
        color: #32936f;
    }

    .dark .form-input-container.valid .form-label,
    .dark .form-textarea-container.valid .form-label,
    .dark .form-select-container.valid .form-label {
        color: #32936f;
    }

    /* Validation Icons */
    .form-input-container .validation-icon {
        position: absolute;
        right: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        width: 1rem;
        height: 1rem;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    .form-input-container.has-label .validation-icon {
        top: calc(50% + 0.75rem);
    }

    .form-input.error ~ .validation-icon.error-icon,
    .form-textarea.error ~ .validation-icon.error-icon,
    .form-select.error ~ .validation-icon.error-icon {
        opacity: 1;
        color: #EF4444;
    }

    .dark .form-input.error ~ .validation-icon.error-icon,
    .dark .form-textarea.error ~ .validation-icon.error-icon,
    .dark .form-select.error ~ .validation-icon.error-icon {
        color: #F87171;
    }

    /* Error State */
    .form-input.error,
    .form-textarea.error,
    .form-select.error {
        border-color: #ef4444;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='%23ef4444'%3e%3cpath fill-rule='evenodd' d='M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z' clip-rule='evenodd'/%3e%3c/svg%3e");
        background-position: right 0.75rem center;
        background-repeat: no-repeat;
        background-size: 1.25rem 1.25rem;
        padding-right: 3rem;
    }

    /* Error Message */
    .field-error-message {
        color: #ef4444;
        font-size: 0.875rem;
        margin-top: 0.25rem;
        display: flex;
        align-items: center;
    }

    .field-error-message::before {
        content: "⚠";
        margin-right: 0.25rem;
    }

    /* Placeholder styling */
    .form-input::placeholder,
    .form-textarea::placeholder {
        color: #9ca3af;
        opacity: 1;
    }

    .dark .form-input::placeholder,
    .dark .form-textarea::placeholder {
        color: #6b7280;
    }

    /* Select arrow styling */
    .form-select {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 0.75rem center;
        background-repeat: no-repeat;
        background-size: 1.25rem 1.25rem;
        padding-right: 3rem;
        appearance: none;
    }

    /* Responsive Design */
    @media (max-width: 640px) {
        .form-input-container {
            margin-bottom: 1rem;
        }

        .form-input,
        .form-textarea,
        .form-select {
            padding: 0.625rem 0.875rem;
            font-size: 0.875rem;
        }
    }
</style>
@endsection

@section('content')
<div class="container mx-auto">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ __('vendor.create_service_provider') }}</h2>
            <p class="mt-1 text-gray-600 dark:text-gray-400">{{ __('vendor.create_service_provider_description') }}</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('vendor.settings.service-providers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left {{ app()->getLocale() === 'ar' ? 'ml-2 rtl:rotate-180' : 'mr-2' }}"></i> {{ __('vendor.back_to_service_providers') }}
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
        <form method="POST" action="{{ route('vendor.settings.service-providers.store') }}">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('vendor.basic_information') }}</h3>
                </div>

                <!-- Name -->
                <div class="form-input-container has-label">
                    <label for="name" class="form-label {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('vendor.full_name') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="form-input {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}"
                           dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
                    @error('name')
                        <span class="field-error-message {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email -->
                <div class="form-input-container has-label">
                    <label for="email" class="form-label {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('vendor.email_address') }} <span class="text-red-500">*</span></label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                           class="form-input {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}"
                           dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
                    @error('email')
                        <span class="field-error-message {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Phone -->
                <div class="form-input-container has-label">
                    <label for="phone" class="form-label {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('vendor.phone_number') }}</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                           class="form-input {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}"
                           dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
                    @error('phone')
                        <span class="field-error-message {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-input-container has-label">
                    <label for="password" class="form-label {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('vendor.password') }} <span class="text-red-500">*</span></label>
                    <input type="password" name="password" id="password" required
                           class="form-input {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}"
                           dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
                    @error('password')
                        <span class="field-error-message {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="form-input-container has-label">
                    <label for="password_confirmation" class="form-label {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('vendor.confirm_password') }} <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                           class="form-input {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}"
                           dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
                </div>

                <!-- Permissions Section -->
                <div class="md:col-span-2 mt-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('vendor.permissions_and_access') }}</h3>
                </div>

                <!-- Branch Access -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('vendor.branch_access') }}</label>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-3 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('vendor.branch_access_description') }}</p>

                    @if($branches->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach($branches as $branch)
                                <div class="flex items-center gap-2 {{ app()->getLocale() === 'ar' ? 'flex-row-reverse' : '' }}">
                                    <input type="checkbox" name="branch_ids[]" value="{{ $branch->id }}" id="branch_{{ $branch->id }}"
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                           {{ in_array($branch->id, old('branch_ids', [])) ? 'checked' : '' }}>
                                    <label for="branch_{{ $branch->id }}" class="{{ app()->getLocale() === 'ar' ? 'mr-2' : 'ml-2' }} block text-sm text-gray-900 dark:text-white">
                                        {{ $branch->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('vendor.no_branches_available') }}</p>
                    @endif

                    @error('branch_ids')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Service Access -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('vendor.service_access') }}</label>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-3 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('vendor.service_access_description') }}</p>

                    @if($services->count() > 0)
                        <div class="max-h-48 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-md p-3">
                            <div class="grid grid-cols-1 gap-2">
                                @foreach($services as $service)
                                    <div class="flex items-center {{ app()->getLocale() === 'ar' ? 'flex-row-reverse' : '' }}">
                                        <input type="checkbox" name="service_ids[]" value="{{ $service->id }}" id="service_{{ $service->id }}"
                                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                               {{ in_array($service->id, old('service_ids', [])) ? 'checked' : '' }}>
                                        <label for="service_{{ $service->id }}" class="{{ app()->getLocale() === 'ar' ? 'mr-2' : 'ml-2' }} block text-sm text-gray-900 dark:text-white">
                                            {{ $service->name }}
                                            <span class="text-gray-500 dark:text-gray-400">({{ $service->branch->name }})</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('vendor.no_services_available') }}</p>
                    @endif

                    @error('service_ids')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-8 flex {{ app()->getLocale() === 'ar' ? 'justify-start' : 'justify-end' }} space-x-3 {{ app()->getLocale() === 'ar' ? 'rtl:space-x-reverse' : '' }}">
                <a href="{{ route('vendor.settings.service-providers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    {{ __('vendor.cancel') }}
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none disabled:opacity-25 transition ease-in-out duration-150" style="background-color: #53D2DC; border-color: #53D2DC;" onmouseover="this.style.backgroundColor='#42B8C2'" onmouseout="this.style.backgroundColor='#53D2DC'" onfocus="this.style.boxShadow='0 0 0 3px rgba(83, 210, 220, 0.3)'" onblur="this.style.boxShadow='none'">
                    <i class="fas fa-save {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i> {{ __('vendor.create_service_provider_button') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Modern form validation functionality
    document.addEventListener('DOMContentLoaded', function() {
        initializeFormValidation();
    });

    function initializeFormValidation() {
        const formInputs = document.querySelectorAll('.form-input, .form-textarea, .form-select');

        formInputs.forEach(input => {
            // Track if user has interacted with this element
            let hasInteracted = false;

            // Add event listeners for validation
            input.addEventListener('blur', function() {
                if (hasInteracted) {
                    validateField(this);
                }
            });

            input.addEventListener('input', function() {
                hasInteracted = true;
                // Clear error state on input
                clearFieldError(this);

                // Add filled class if input has value
                if (this.value.trim() !== '') {
                    this.classList.add('filled');
                } else {
                    this.classList.remove('filled');
                }
            });

            // Set initial filled state for pre-filled fields
            if (input.value.trim() !== '') {
                input.classList.add('filled');
            }
        });
    }

    function validateField(field) {
        const value = field.value.trim();
        const isRequired = field.hasAttribute('required');
        const fieldType = field.type;
        let isValid = true;
        let errorMessage = '';

        // Clear previous error state
        clearFieldError(field);

        // Required field validation
        if (isRequired && value === '') {
            isValid = false;
            errorMessage = 'This field is required';
        }
        // Email validation
        else if (fieldType === 'email' && value !== '') {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                isValid = false;
                errorMessage = 'Please enter a valid email address';
            }
        }
        // Phone validation
        else if (fieldType === 'tel' && value !== '') {
            const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
            if (!phoneRegex.test(value.replace(/\s/g, ''))) {
                isValid = false;
                errorMessage = 'Please enter a valid phone number';
            }
        }
        // Password confirmation validation
        else if (field.name === 'password_confirmation' && value !== '') {
            const passwordField = document.getElementById('password');
            if (passwordField && value !== passwordField.value) {
                isValid = false;
                errorMessage = 'Passwords do not match';
            }
        }

        if (isValid) {
            field.classList.add('valid');
            field.classList.remove('error');
        } else {
            field.classList.add('error');
            field.classList.remove('valid');
            showFieldError(field, errorMessage);
        }

        return isValid;
    }

    function clearFieldError(field) {
        field.classList.remove('error', 'valid');
        const container = field.closest('.form-input-container');
        if (container) {
            const existingError = container.querySelector('.field-error-message');
            if (existingError) {
                existingError.remove();
            }
        }
    }

    function showFieldError(field, message) {
        const container = field.closest('.form-input-container');
        if (container) {
            const errorDiv = document.createElement('span');
            errorDiv.className = 'field-error-message text-red-600 text-sm mt-1';
            errorDiv.textContent = message;
            container.appendChild(errorDiv);
        }
    }
</script>
@endsection
