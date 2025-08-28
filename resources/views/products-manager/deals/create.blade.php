@extends('layouts.products-manager')

@section('title', __('products_manager.create_deal'))
@section('page-title', __('products_manager.create_deal'))

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .form-section {
        margin-bottom: 2rem;
    }
    .form-section-title {
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #e2e8f0;
    }
    .selection-container {
        max-height: 300px;
        overflow-y: auto;
        border: 1px solid #e2e8f0;
        border-radius: 0.375rem;
        padding: 0.5rem;
    }
    .language-tab {
        padding: 0.5rem 1rem;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        cursor: pointer;
        transition: all 0.2s;
    }
    .language-tab.active {
        background: #f59e0b;
        color: white;
        border-color: #f59e0b;
    }
    .language-tab:hover {
        background: #fef3c7;
    }
    .language-tab.active:hover {
        background: #d97706;
    }
</style>
@endsection

@section('content')
<div class="container mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <form action="{{ route('products-manager.deals.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Basic Information -->
            <div class="form-section">
                <h3 class="form-section-title text-xl font-bold text-gray-800 dark:text-white {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('products_manager.deal_information') }}</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Title (Bilingual) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('products_manager.title') }} <span class="text-red-500">*</span></label>

                        <!-- Language Switcher for Title -->
                        <div class="flex mb-2">
                            <button type="button" class="language-tab active rounded-l-md" data-language="en" data-field="title">
                                🇺🇸 English
                            </button>
                            <button type="button" class="language-tab rounded-r-md" data-language="ar" data-field="title">
                                🇸🇦 العربية
                            </button>
                        </div>

                        <!-- English Title -->
                        <div data-language-field="title" data-language="en" class="mb-3">
                            <input type="text" name="title" id="title" value="{{ old('title') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#F46C3F] focus:border-[#F46C3F] dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
                                   placeholder="{{ __('products_manager.enter_deal_title') }}" required>
                            @error('title')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Arabic Title -->
                        <div data-language-field="title" data-language="ar" class="mb-3" style="display: none;">
                            <input type="text" name="title_arabic" id="title_arabic" value="{{ old('title_arabic') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#F46C3F] focus:border-[#F46C3F] dark:bg-gray-700 dark:border-gray-600 dark:text-white text-right" 
                                   placeholder="{{ __('products_manager.enter_deal_title_arabic') }}" required dir="rtl">
                            @error('title_arabic')
                                <p class="text-red-500 text-sm mt-1 text-right">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Discount Percentage -->
                    <div>
                        <label for="discount_percentage" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('products_manager.discount_percentage') }} <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="number" name="discount_percentage" id="discount_percentage" value="{{ old('discount_percentage') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#F46C3F] focus:border-[#F46C3F] dark:bg-gray-700 dark:border-gray-600 dark:text-white {{ app()->getLocale() == 'ar' ? 'text-right pr-8' : 'text-left pr-8' }}" 
                                   min="1" max="100" step="0.01" required
                                   onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode === 46"
                                   oninput="this.value = this.value.replace(/[^0-9.]/g, ''); if(this.value > 100) this.value = 100;">
                            <div class="absolute inset-y-0 {{ app()->getLocale() == 'ar' ? 'left-0 pl-3' : 'right-0 pr-3' }} flex items-center pointer-events-none">
                                <span class="text-gray-500">%</span>
                            </div>
                        </div>
                        @error('discount_percentage')
                            <p class="text-red-500 text-sm mt-1 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Description (Bilingual) -->
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('products_manager.description') }}</label>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('products_manager.description_optional_both_or_none') }}</p>

                    <!-- Language Switcher for Description -->
                    <div class="flex mb-2">
                        <button type="button" class="language-tab active rounded-l-md" data-language="en" data-field="description">
                            🇺🇸 English
                        </button>
                        <button type="button" class="language-tab rounded-r-md" data-language="ar" data-field="description">
                            🇸🇦 العربية
                        </button>
                    </div>

                    <!-- English Description -->
                    <div data-language-field="description" data-language="en" class="mb-3">
                        <textarea name="description" id="description" rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#F46C3F] focus:border-[#F46C3F] dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                  placeholder="{{ __('products_manager.enter_deal_description') }}">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Arabic Description -->
                    <div data-language-field="description" data-language="ar" class="mb-3" style="display: none;">
                        <textarea name="description_arabic" id="description_arabic" rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#F46C3F] focus:border-[#F46C3F] dark:bg-gray-700 dark:border-gray-600 dark:text-white text-right"
                                  placeholder="{{ __('products_manager.enter_deal_description_arabic') }}" dir="rtl">{{ old('description_arabic') }}</textarea>
                        @error('description_arabic')
                            <p class="text-red-500 text-sm mt-1 text-right">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Promotional Message (Bilingual) -->
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                        {{ __('messages.promotional_message') }}
                        <span class="{{ app()->getLocale() == 'ar' ? 'mr-1' : 'ml-1' }} text-gray-500 text-xs" title="{{ __('messages.promotional_message_help') }}">
                            ({{ __('messages.optional') }})
                        </span>
                    </label>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('messages.promotional_message_both_or_none') }}</p>

                    <!-- Language Switcher for Promotional Message -->
                    <div class="flex mb-2">
                        <button type="button" class="language-tab active rounded-l-md" data-language="en" data-field="promotional_message">
                            🇺🇸 English
                        </button>
                        <button type="button" class="language-tab rounded-r-md" data-language="ar" data-field="promotional_message">
                            🇸🇦 العربية
                        </button>
                    </div>

                    <!-- English Promotional Message -->
                    <div data-language-field="promotional_message" data-language="en" class="mb-3">
                        <div class="relative">
                            <input type="text" name="promotional_message" id="promotional_message" value="{{ old('promotional_message') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#F46C3F] focus:border-[#F46C3F] dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                   maxlength="50" placeholder="{{ __('messages.promotional_message_placeholder') }}">
                            <div class="absolute right-2 bottom-2 text-xs text-gray-500">
                                <span id="char-count-en">0</span>/50
                            </div>
                        </div>
                        @error('promotional_message')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Arabic Promotional Message -->
                    <div data-language-field="promotional_message" data-language="ar" class="mb-3" style="display: none;">
                        <div class="relative">
                            <input type="text" name="promotional_message_arabic" id="promotional_message_arabic" value="{{ old('promotional_message_arabic') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#F46C3F] focus:border-[#F46C3F] dark:bg-gray-700 dark:border-gray-600 dark:text-white text-right"
                                   maxlength="50" placeholder="أدخل الرسالة الترويجية" dir="rtl">
                            <div class="absolute left-2 bottom-2 text-xs text-gray-500">
                                <span id="char-count-ar">0</span>/50
                            </div>
                        </div>
                        @error('promotional_message_arabic')
                            <p class="text-red-500 text-sm mt-1 text-right">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Date Range -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <!-- Start Date -->
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('products_manager.start_date') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="start_date" id="start_date" value="{{ old('start_date') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#F46C3F] focus:border-[#F46C3F] dark:bg-gray-700 dark:border-gray-600 dark:text-white datepicker {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}" required>
                        @error('start_date')
                            <p class="text-red-500 text-sm mt-1 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- End Date -->
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('products_manager.end_date') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="end_date" id="end_date" value="{{ old('end_date') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#F46C3F] focus:border-[#F46C3F] dark:bg-gray-700 dark:border-gray-600 dark:text-white datepicker {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}" required>
                        @error('end_date')
                            <p class="text-red-500 text-sm mt-1 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Status -->
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('products_manager.status') }} <span class="text-red-500">*</span></label>
                    <div class="flex gap-4">
                        <label class="flex items-center">
                            <input type="radio" name="status" value="active" {{ old('status', 'active') === 'active' ? 'checked' : '' }} 
                                   class="mr-2 text-[#F46C3F] focus:ring-[#F46C3F]">
                            <span class="text-gray-700 dark:text-gray-300">{{ __('products_manager.active') }}</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="status" value="inactive" {{ old('status') === 'inactive' ? 'checked' : '' }} 
                                   class="mr-2 text-[#F46C3F] focus:ring-[#F46C3F]">
                            <span class="text-gray-700 dark:text-gray-300">{{ __('products_manager.inactive') }}</span>
                        </label>
                    </div>
                    @error('status')
                        <p class="text-red-500 text-sm mt-1 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Deal Image -->
            <div class="form-section">
                <h3 class="form-section-title text-xl font-bold text-gray-800 dark:text-white {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('products_manager.deal_image') }}</h3>
                
                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('products_manager.upload_image') }} <span class="text-red-500">*</span></label>
                    <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/jpg,image/gif,image/svg+xml" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#F46C3F] focus:border-[#F46C3F] dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('products_manager.image_requirements_new') }}</p>
                    <div id="image-error" class="text-red-500 text-sm mt-1 hidden {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}"></div>
                    @error('image')
                        <p class="text-red-500 text-sm mt-1 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Applies To Section -->
            <div class="form-section">
                <h3 class="form-section-title text-xl font-bold text-gray-800 dark:text-white {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('products_manager.deal_applies_to') }}</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('products_manager.applies_to') }} <span class="text-red-500">*</span></label>
                        <div class="space-y-2">
                            <input type="hidden" name="applies_to" value="products">
                            <span class="text-gray-700 dark:text-gray-300">{{ __('products_manager.specific_products') }}</span>
                        </div>
                        @error('applies_to')
                            <p class="text-red-500 text-sm mt-1 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Products Selection Container -->
                    <div id="products-container">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('products_manager.select_products') }}</label>
                        <div class="selection-container bg-gray-50 dark:bg-gray-700">
                            @foreach($products as $product)
                                @php
                                    $hasActiveDeal = in_array($product->id, $productsWithActiveDeals ?? []);
                                @endphp
                                <label class="flex items-center p-2 hover:bg-gray-100 dark:hover:bg-gray-600 rounded {{ $hasActiveDeal ? 'opacity-50' : '' }} {{ $hasActiveDeal ? 'cursor-not-allowed' : 'cursor-pointer' }}">
                                    <input type="checkbox"
                                           name="product_ids[]"
                                           value="{{ $product->id }}"
                                           class="mr-2 text-[#F46C3F] focus:ring-[#F46C3F] {{ $hasActiveDeal ? 'cursor-not-allowed' : '' }}"
                                           {{ in_array($product->id, old('product_ids', [])) ? 'checked' : '' }}
                                           {{ $hasActiveDeal ? 'disabled' : '' }}>
                                    <span class="text-gray-700 dark:text-gray-300 {{ $hasActiveDeal ? 'text-gray-400' : '' }}">
                                        {{ $product->name }}
                                        @if($hasActiveDeal)
                                            <span class="text-xs text-red-500 ml-1">({{ __('messages.has_active_deal') }})</span>
                                        @endif
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        @error('product_ids')
                            <p class="text-red-500 text-sm mt-1 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ $message }}</p>
                        @enderror
                    </div>


                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex {{ app()->getLocale() == 'ar' ? 'justify-end space-x-reverse' : 'justify-end' }} space-x-4 gap-4 mt-8">
                <a href="{{ route('products-manager.deals.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors duration-200">
                    {{ __('products_manager.cancel') }}
                </a>
                <button type="submit" class="px-4 py-2 bg-[#F46C3F] text-white rounded-md hover:opacity-90 transition-opacity duration-200">
                    {{ __('products_manager.create_deal') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize date pickers
        flatpickr(".datepicker", {
            dateFormat: "Y-m-d",
            minDate: "today"
        });

        // Initialize selection containers
        toggleSelectionContainers();

        // Initialize language switchers
        initializeLanguageSwitchers();

        // Initialize bilingual validation
        setupBilingualValidation();

        // Initialize image validation
        setupImageValidation();
    });

    function toggleSelectionContainers() {
        // Since we only support products now, always show the products container
        const productsContainer = document.getElementById('products-container');
        if (productsContainer) {
            productsContainer.style.display = 'block';
        }
    }

    function initializeLanguageSwitchers() {
        document.querySelectorAll('.language-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                const field = this.dataset.field;
                const language = this.dataset.language;

                // Update tab states
                document.querySelectorAll(`[data-field="${field}"]`).forEach(t => t.classList.remove('active'));
                this.classList.add('active');

                // Show/hide language fields
                document.querySelectorAll(`[data-language-field="${field}"]`).forEach(fieldDiv => {
                    fieldDiv.style.display = fieldDiv.dataset.language === language ? 'block' : 'none';
                });
            });
        });
    }

    function validateBilingualField(fieldName, required) {
        const englishField = document.querySelector(`[name="${fieldName}"]`);
        const arabicField = document.querySelector(`[name="${fieldName}_arabic"]`);

        const englishValue = englishField ? englishField.value.trim() : '';
        const arabicValue = arabicField ? arabicField.value.trim() : '';

        if (required) {
            return englishValue !== '' && arabicValue !== '';
        } else {
            // Optional: both empty or both filled
            return (englishValue === '' && arabicValue === '') || (englishValue !== '' && arabicValue !== '');
        }
    }

    function setupBilingualValidation() {
        const form = document.querySelector('form');
        if (!form) return;

        form.addEventListener('submit', function(e) {
            let hasErrors = false;
            const errors = [];

            // Validate title (required in both languages)
            if (!validateBilingualField('title', true)) {
                hasErrors = true;
                errors.push('{{ __('products_manager.title_required_both_languages') }}');
            }

            // Validate description (optional, but if one is filled, both must be filled)
            if (!validateBilingualField('description', false)) {
                hasErrors = true;
                errors.push('{{ __('products_manager.description_both_or_none') }}');
            }

            // Validate promotional message (optional, but if one is filled, both must be filled)
            if (!validateBilingualField('promotional_message', false)) {
                hasErrors = true;
                errors.push('{{ __('messages.promotional_message_both_or_none') }}');
            }

            // Validate image (required)
            const imageInput = document.getElementById('image');
            if (!imageInput || !imageInput.files || imageInput.files.length === 0) {
                hasErrors = true;
                errors.push('{{ __('products_manager.deal_image_required') }}');
            } else {
                // Validate file size (20MB = 20971520 bytes)
                const file = imageInput.files[0];
                const maxSize = 20971520; // 20MB in bytes

                if (file.size > maxSize) {
                    hasErrors = true;
                    errors.push('{{ __('products_manager.deal_image_too_large') }}');
                }

                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/svg+xml'];
                if (!allowedTypes.includes(file.type)) {
                    hasErrors = true;
                    errors.push('{{ __('products_manager.deal_image_invalid_type') }}');
                }
            }

            if (hasErrors) {
                e.preventDefault();
                showValidationModal(errors);
                return false;
            }
        });
    }

    function setupImageValidation() {
        const imageInput = document.getElementById('image');
        const imageError = document.getElementById('image-error');

        if (imageInput && imageError) {
            imageInput.addEventListener('change', function() {
                validateImageInput(this, imageError);
            });
        }
    }

    function validateImageInput(input, errorElement) {
        errorElement.classList.add('hidden');
        errorElement.textContent = '';

        if (!input.files || input.files.length === 0) {
            return; // No file selected, will be caught by form validation
        }

        const file = input.files[0];
        const maxSize = 20971520; // 20MB in bytes
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/svg+xml'];

        // Check file size
        if (file.size > maxSize) {
            errorElement.textContent = '{{ __('products_manager.deal_image_too_large') }}';
            errorElement.classList.remove('hidden');
            input.value = ''; // Clear the input
            return false;
        }

        // Check file type
        if (!allowedTypes.includes(file.type)) {
            errorElement.textContent = '{{ __('products_manager.deal_image_invalid_type') }}';
            errorElement.classList.remove('hidden');
            input.value = ''; // Clear the input
            return false;
        }

        return true;
    }

    function showValidationModal(errors) {
        const errorList = errors.map(error => `<li>${error}</li>`).join('');
        const modalContent = `
            <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="validation-modal">
                <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                    <div class="mt-3 text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mt-2">{{ __('products_manager.validation_errors') }}</h3>
                        <div class="mt-2 px-7 py-3">
                            <ul class="text-sm text-red-600 text-left">
                                ${errorList}
                            </ul>
                        </div>
                        <div class="items-center px-4 py-3">
                            <button id="close-modal" class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">
                                {{ __('products_manager.ok') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalContent);

        document.getElementById('close-modal').addEventListener('click', function() {
            document.getElementById('validation-modal').remove();
        });
    }

    function setupCharacterCounting() {
        // Setup character counting for promotional message fields
        const promotionalMessageEn = document.getElementById('promotional_message');
        const promotionalMessageAr = document.getElementById('promotional_message_arabic');
        const charCountEn = document.getElementById('char-count-en');
        const charCountAr = document.getElementById('char-count-ar');

        if (promotionalMessageEn && charCountEn) {
            // Update character count on input
            promotionalMessageEn.addEventListener('input', function() {
                charCountEn.textContent = this.value.length;
            });
            // Initialize count
            charCountEn.textContent = promotionalMessageEn.value.length;
        }

        if (promotionalMessageAr && charCountAr) {
            // Update character count on input
            promotionalMessageAr.addEventListener('input', function() {
                charCountAr.textContent = this.value.length;
            });
            // Initialize count
            charCountAr.textContent = promotionalMessageAr.value.length;
        }
    }

    // Initialize everything when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        initializeLanguageSwitchers();
        setupBilingualValidation();
        setupImageValidation();
        setupCharacterCounting();
    });
</script>
@endsection
