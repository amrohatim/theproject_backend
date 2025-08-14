@extends('layouts.products-manager')

@section('title', __('products_manager.edit_deal'))
@section('page-title', __('products_manager.edit_deal'))

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
    .deal-image-preview {
        width: 100%;
        max-width: 300px;
        height: 150px;
        object-fit: cover;
        border-radius: 0.375rem;
        margin-top: 0.5rem;
    }
</style>
@endsection

@section('content')
<div class="container mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <form action="{{ route('products-manager.deals.update', $deal->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

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
                            <input type="text" name="title" id="title" value="{{ old('title', $deal->title) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#F46C3F] focus:border-[#F46C3F] dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
                                   placeholder="{{ __('products_manager.enter_deal_title') }}" required>
                            @error('title')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Arabic Title -->
                        <div data-language-field="title" data-language="ar" class="mb-3" style="display: none;">
                            <input type="text" name="title_arabic" id="title_arabic" value="{{ old('title_arabic', $deal->title_arabic) }}"
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
                            <input type="number" name="discount_percentage" id="discount_percentage" value="{{ old('discount_percentage', $deal->discount_percentage) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#F46C3F] focus:border-[#F46C3F] dark:bg-gray-700 dark:border-gray-600 dark:text-white {{ app()->getLocale() == 'ar' ? 'text-right pr-8' : 'text-left pr-8' }}" 
                                   min="1" max="100" step="0.01" required>
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
                                  placeholder="{{ __('products_manager.enter_deal_description') }}">{{ old('description', $deal->description) }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Arabic Description -->
                    <div data-language-field="description" data-language="ar" class="mb-3" style="display: none;">
                        <textarea name="description_arabic" id="description_arabic" rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#F46C3F] focus:border-[#F46C3F] dark:bg-gray-700 dark:border-gray-600 dark:text-white text-right"
                                  placeholder="{{ __('products_manager.enter_deal_description_arabic') }}" dir="rtl">{{ old('description_arabic', $deal->description_arabic) }}</textarea>
                        @error('description_arabic')
                            <p class="text-red-500 text-sm mt-1 text-right">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Date Range -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <!-- Start Date -->
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('products_manager.start_date') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="start_date" id="start_date" value="{{ old('start_date', $deal->start_date->format('Y-m-d')) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#F46C3F] focus:border-[#F46C3F] dark:bg-gray-700 dark:border-gray-600 dark:text-white datepicker {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}" required>
                        @error('start_date')
                            <p class="text-red-500 text-sm mt-1 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- End Date -->
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('products_manager.end_date') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="end_date" id="end_date" value="{{ old('end_date', $deal->end_date->format('Y-m-d')) }}" 
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
                            <input type="radio" name="status" value="active" {{ old('status', $deal->status) === 'active' ? 'checked' : '' }} 
                                   class="mr-2 text-[#F46C3F] focus:ring-[#F46C3F]">
                            <span class="text-gray-700 dark:text-gray-300">{{ __('products_manager.active') }}</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="status" value="inactive" {{ old('status', $deal->status) === 'inactive' ? 'checked' : '' }} 
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
                
                @if($deal->image)
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('products_manager.current_image') }}</label>
                        <img src="{{ $deal->image }}" alt="{{ __('products_manager.current_deal_image') }}" class="deal-image-preview border border-gray-300 rounded">
                    </div>
                @endif
                
                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('products_manager.upload_new_image') }}</label>
                    <input type="file" name="image" id="image" accept="image/*"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#F46C3F] focus:border-[#F46C3F] dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('products_manager.image_requirements') }}</p>
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
                            <label class="flex items-center">
                                <input type="radio" name="applies_to" value="all" {{ old('applies_to', $deal->applies_to) === 'all' ? 'checked' : '' }}
                                       class="mr-2 text-[#F46C3F] focus:ring-[#F46C3F]" onchange="toggleSelectionContainers()">
                                <span class="text-gray-700 dark:text-gray-300">{{ __('products_manager.all_products') }}</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="applies_to" value="products" {{ old('applies_to', $deal->applies_to) === 'products' ? 'checked' : '' }}
                                       class="mr-2 text-[#F46C3F] focus:ring-[#F46C3F]" onchange="toggleSelectionContainers()">
                                <span class="text-gray-700 dark:text-gray-300">{{ __('products_manager.specific_products') }}</span>
                            </label>
                        </div>
                        @error('applies_to')
                            <p class="text-red-500 text-sm mt-1 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Products Selection Container -->
                    <div id="products-container" style="display: {{ old('applies_to', $deal->applies_to) === 'products' ? 'block' : 'none' }};">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('products_manager.select_products') }}</label>
                        <div class="selection-container bg-gray-50 dark:bg-gray-700">
                            @foreach($products as $product)
                                <label class="flex items-center p-2 hover:bg-gray-100 dark:hover:bg-gray-600 rounded">
                                    <input type="checkbox" name="product_ids[]" value="{{ $product->id }}" 
                                           class="mr-2 text-[#F46C3F] focus:ring-[#F46C3F]" 
                                           {{ in_array($product->id, old('product_ids', $deal->product_ids ?? [])) ? 'checked' : '' }}>
                                    <span class="text-gray-700 dark:text-gray-300">{{ $product->name }}</span>
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
                    {{ __('products_manager.update_deal') }}
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
    });

    function toggleSelectionContainers() {
        const checkedRadio = document.querySelector('input[name="applies_to"]:checked');
        if (!checkedRadio) return;

        const appliesTo = checkedRadio.value;
        const productsContainer = document.getElementById('products-container');

        // Hide products container first
        if (productsContainer) productsContainer.style.display = 'none';

        // Show products container if specific products is selected
        if (appliesTo === 'products' && productsContainer) {
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

            if (hasErrors) {
                e.preventDefault();
                showValidationModal(errors);
                return false;
            }
        });
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
</script>
@endsection
