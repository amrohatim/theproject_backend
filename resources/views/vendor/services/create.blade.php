@extends('layouts.dashboard')

@section('title', __('messages.add_service'))
@section('page-title', __('messages.add_service'))

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
    .form-error-message {
        color: #ef4444;
        font-size: 0.875rem;
        margin-top: 0.25rem;
        display: flex;
        align-items: center;
    }

    .form-error-message::before {
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
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ __('messages.add_service') }}</h2>
                <p class="mt-1 text-gray-600 dark:text-gray-400">{{ __('messages.create_new_service') }}</p>
            </div>
            <div>
                <a href="{{ route('vendor.services.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-arrow-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }} mr-2"></i> {{ __('messages.back_to_services') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Service form -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
        <form action="{{ route('vendor.services.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('messages.basic_information') }}</h3>

                    <!-- Service Name (Bilingual) -->
                    <div class="form-input-container has-label">
                        <label class="form-label">{{ __('messages.service_name') }} <span class="text-red-500">*</span></label>

                        <!-- Language Switcher for Service Name -->
                        <x-form-language-switcher field-name="service_name" />

                        <!-- English Service Name -->
                        <div data-language-field="service_name" data-language="en" class="active-language-field">
                            <input type="text" name="name" id="name" value="{{ old('name') }}"
                                   class="form-input ltr text-left"
                                   placeholder="{{ __('messages.service_name_english') }}" required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Arabic Service Name -->
                        <div data-language-field="service_name" data-language="ar" style="display: none;">
                            <input type="text" name="service_name_arabic" id="service_name_arabic" value="{{ old('service_name_arabic') }}"
                                   class="form-input"
                                   placeholder="{{ __('messages.service_name_arabic') }}" dir="rtl" required>
                            @error('service_name_arabic')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Category -->
                    <div class="form-input-container has-label">
                        <label for="category_id" class="form-label">{{ __('messages.category') }} <span class="text-red-500">*</span></label>
                        <select id="category_id" name="category_id" class="form-select" required>
                            <option value="">{{ __('messages.select_category') }}</option>
                            @foreach($parentCategories ?? [] as $parentCategory)
                                <optgroup label="{{ $parentCategory->name }}">
                                    <!-- Parent category as disabled option -->
                                    {{-- <option value="{{ $parentCategory->id }}" disabled style="color: #9ca3af; font-weight: bold;">{{ $parentCategory->name }}</option> --}}

                                    <!-- Child categories -->
                                    @foreach($parentCategory->children as $childCategory)
                                        <option value="{{ $childCategory->id }}" {{ old('category_id') == $childCategory->id ? 'selected' : '' }}>&nbsp;&nbsp;{{ $childCategory->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Branch -->
                    <div class="form-input-container has-label">
                        <label for="branch_id" class="form-label">{{ __('messages.branch') }} <span class="text-red-500">*</span></label>
                        <select id="branch_id" name="branch_id" class="form-select" required>
                            <option value="">{{ __('messages.select_branch') }}</option>
                            @foreach($branches ?? [] as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                        @error('branch_id')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Service Description (Bilingual) -->
                    <div class="form-input-container has-label">
                        <label class="form-label">{{ __('messages.description') }}</label>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">{{ __('messages.description_optional_both_or_none') }}</p>

                        <!-- Language Switcher for Description -->
                        <x-form-language-switcher field-name="service_description" />

                        <!-- English Description -->
                        <div data-language-field="service_description" data-language="en" class="active-language-field">
                            <textarea id="description" name="description" rows="4"
                                      class="form-textarea"
                                      >{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Arabic Description -->
                        <div data-language-field="service_description" data-language="ar" style="display: none;">
                            <textarea id="service_description_arabic" name="service_description_arabic" rows="4"
                                      class="form-textarea"
                                      dir="rtl">{{ old('service_description_arabic') }}</textarea>
                            @error('service_description_arabic')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Pricing and Details -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('messages.pricing_details') }}</h3>

                    <!-- Price -->
                    <div class="form-input-container has-label">
                        <label for="price" class="form-label">{{ __('messages.price') }} <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input type="number" name="price" id="price" min="0" step="0.01" value="{{ old('price') }}" class="form-input pl-7" placeholder="0.00" required onkeypress="return (event.charCode !=8 && event.charCode ==0 || (event.charCode >= 48 && event.charCode <= 57))" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');">
                        </div>
                        @error('price')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Duration -->
                    <div class="form-input-container has-label">
                        <label for="duration" class="form-label">{{ __('messages.duration_minutes') }} <span class="text-red-500">*</span></label>
                        <input type="number" 
                               name="duration" 
                               id="duration" 
                               min="1" 
                               value="{{ old('duration', 30) }}" 
                               class="form-input" 
                               required
                               onkeypress="return (event.charCode !=8 && event.charCode ==0 || (event.charCode >= 48 && event.charCode <= 57))"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                        @error('duration')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Availability -->
                    <div>
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="is_available" name="is_available" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 dark:border-gray-600 rounded" value="1" {{ old('is_available', '1') == '1' ? 'checked' : '' }}>
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="is_available" class="font-medium text-gray-700 dark:text-gray-300">{{ __('messages.available_for_booking') }}</label>
                                <p class="text-gray-500 dark:text-gray-400">{{ __('messages.uncheck_if_not_available') }}</p>
                            </div>
                        </div>
                        @error('is_available')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Home Service -->
                    <div>
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="home_service" name="home_service" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 dark:border-gray-600 rounded" value="1" {{ old('home_service') == '1' ? 'checked' : '' }}>
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="home_service" class="font-medium text-gray-700 dark:text-gray-300">{{ __('messages.enable_home_service') }}</label>
                                <p class="text-gray-500 dark:text-gray-400">{{ __('messages.check_if_home_service') }}</p>
                            </div>
                        </div>
                        @error('home_service')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Image -->
                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.service_image') }}</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-md">
                            <div class="space-y-1 text-center" id="image-upload-container">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true" id="image-placeholder">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div id="image-preview" class="mt-2 hidden">
                                    <img src="#" alt="Image Preview" class="mx-auto h-32 w-auto object-cover rounded-md">
                                </div>
                                <div class="flex text-sm text-gray-600 dark:text-gray-400">
                                    <label for="image" class="relative cursor-pointer bg-white dark:bg-gray-700 rounded-md font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                        <span>{{ __('messages.upload_file') }}</span>
                                        <input id="image" name="image" type="file" class="sr-only" onchange="previewImage(this)">
                                    </label>
                                    <p class="pl-1" id="file-name">{{ __('messages.or_drag_drop') }}</p>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ __('messages.image_format_size') }}
                                </p>
                            </div>
                        </div>

                        <!-- Error message container for image validation -->
                        <div id="image-error-message" class="hidden mt-2 p-3 bg-red-50 border border-red-200 rounded-md">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-700" id="error-text"></p>
                                </div>
                            </div>
                        </div>

                        @error('image')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-save mr-2"></i> {{ __('messages.save_service') }}
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
        setupCategoryValidation();
        setupBilingualValidation();
    });

    function initializeFormValidation() {
        const formInputs = document.querySelectorAll('.form-input, .form-textarea, .form-select');
        
        formInputs.forEach(input => {
            // Add event listeners for validation
            input.addEventListener('blur', function() {
                validateField(this);
            });
            
            input.addEventListener('input', function() {
                // Clear error state on input
                clearFieldError(this);
                
                // Add filled class if input has value
                if (this.value.trim() !== '') {
                    this.classList.add('filled');
                } else {
                    this.classList.remove('filled');
                }
            });
            
            // Set initial filled state
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
        // URL validation
        else if (fieldType === 'url' && value !== '') {
            try {
                new URL(value);
            } catch {
                isValid = false;
                errorMessage = 'Please enter a valid URL';
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
            const errorDiv = document.createElement('div');
            errorDiv.className = 'field-error-message text-red-600 text-sm mt-1';
            errorDiv.textContent = message;
            container.appendChild(errorDiv);
        }
    }

    function previewImage(input) {
        const preview = document.getElementById('image-preview');
        const placeholder = document.getElementById('image-placeholder');
        const fileNameElement = document.getElementById('file-name');
        const errorContainer = document.getElementById('image-error-message');
        const errorText = document.getElementById('error-text');

        // Hide any existing error messages
        errorContainer.classList.add('hidden');

        if (input.files && input.files[0]) {
            const file = input.files[0];

            // Validate file type
            if (!file.type.startsWith('image/')) {
                showImageError(errorContainer, errorText, '{{ __('messages.select_valid_image') }}');
                input.value = '';
                return;
            }

            // Enhanced file size validation (20MB limit) with immediate feedback
            if (file.size > 20 * 1024 * 1024) {
                const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
                showImageError(errorContainer, errorText, `{{ __('messages.file_size_exceeds') }}`.replace(':size', fileSizeMB + 'MB'));
                input.value = '';
                return;
            }

            const reader = new FileReader();

            reader.onload = function(e) {
                // Show preview
                preview.classList.remove('hidden');
                preview.querySelector('img').src = e.target.result;

                // Hide placeholder
                placeholder.classList.add('hidden');

                // Update file name
                fileNameElement.textContent = file.name;
            }

            reader.readAsDataURL(file);
        } else {
            // Hide preview
            preview.classList.add('hidden');

            // Show placeholder
            placeholder.classList.remove('hidden');

            // Reset file name
            fileNameElement.textContent = '{{ __('messages.or_drag_drop') }}';
        }
    }

    function showImageError(errorContainer, errorText, message) {
        errorText.textContent = message;
        errorContainer.classList.remove('hidden');

        // Auto-hide error after 5 seconds
        setTimeout(() => {
            errorContainer.classList.add('hidden');
        }, 5000);
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Category selection validation
        function setupCategoryValidation() {
            const categorySelect = document.getElementById('category_id');
            if (categorySelect) {
                categorySelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    if (selectedOption && selectedOption.disabled) {
                        alert('{{ __('messages.select_subcategory') }}');
                        this.value = '';
                    }
                });
            }
        }

        // Initialize category validation
        setupCategoryValidation();

        // Initialize bilingual form validation
        setupBilingualValidation();

        // Initialize dynamic placeholder functionality
        
    });

    function setupBilingualValidation() {
        const form = document.querySelector('form');
        if (!form) return;

        form.addEventListener('submit', function(e) {
            let hasErrors = false;
            const errors = [];

            // Validate service name (required in both languages)
            if (!validateBilingualField('service_name', true)) {
                hasErrors = true;
                errors.push('{{ __('messages.service_name_required_both_languages') }}');
            }

            // Validate description (optional, but if one is filled, both must be filled)
            if (!validateBilingualField('service_description', false)) {
                hasErrors = true;
                errors.push('{{ __('messages.description_both_or_none') }}');
            }

            if (hasErrors) {
                e.preventDefault();
                showValidationModal(errors);
                return false;
            }
        });
    }

    function showValidationModal(errors) {
        const errorList = errors.map(error => `<li class="text-red-600">${error}</li>`).join('');
        const modalHtml = `
            <div id="validationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                    <div class="mt-3 text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mt-2">{{ __('messages.validation_errors') }}</h3>
                        <div class="mt-2 px-7 py-3">
                            <ul class="text-sm text-left list-disc list-inside">
                                ${errorList}
                            </ul>
                        </div>
                        <div class="items-center px-4 py-3">
                            <button id="closeModal" class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">
                                {{ __('messages.close') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);

        document.getElementById('closeModal').addEventListener('click', function() {
            document.getElementById('validationModal').remove();
        });
    }


</script>
@endsection
