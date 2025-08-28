@extends('layouts.dashboard')

@section('title', __('messages.edit_deal'))
@section('page-title', __('messages.edit_deal'))

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
        <form action="{{ route('vendor.deals.update', $deal->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="form-section">
                <h3 class="form-section-title text-xl font-bold text-gray-800 dark:text-white {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('messages.deal_information') }}</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Title (Bilingual) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('messages.title') }} <span class="text-red-500">*</span></label>

                        <!-- Language Switcher for Title -->
                        <x-form-language-switcher field-name="title" />

                        <!-- English Title -->
                        <div data-language-field="title" data-language="en" class="mb-3">
                            <input type="text" name="title" id="title" value="{{ old('title', $deal->title) }}"
                                   class="form-input w-full px-2" placeholder="{{ __('messages.enter_deal_title') }}" required>
                            @error('title')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Arabic Title -->
                        <div data-language-field="title" data-language="ar" class="mb-3" style="display: none;">
                            <input type="text" name="title_arabic" id="title_arabic" value="{{ old('title_arabic', $deal->title_arabic) }}"
                                   class="form-input w-full text-right pr-8" placeholder="أدخل عنوان الصفقة" required dir="rtl">
                            @error('title_arabic')
                                <p class="text-red-500 text-sm mt-1 text-right">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Discount Percentage -->
                    <div>
                        <label for="discount_percentage" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('messages.discount_percentage') }} <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="number" name="discount_percentage" id="discount_percentage" value="{{ old('discount_percentage', $deal->discount_percentage) }}" min="1" max="100" class="form-input pr-14 pl-4 w-full {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}" placeholder="{{ __('messages.enter_discount_percentage') }}" required onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode === 46" oninput="this.value = this.value.replace(/[^0-9.]/g, ''); if(this.value > 100) this.value = 100;">
                            <div class="absolute inset-y-0 {{ app()->getLocale() == 'ar' ? 'left-0 pl-3' : 'right-0 pr-3' }}  flex items-center pointer-events-none">
                                <span class="text-gray-500 pr-6">%</span>
                            </div>
                        </div>
                        @error('discount_percentage')
                            <p class="text-red-500 text-sm mt-1 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Description (Bilingual) -->
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('messages.description') }}</label>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('messages.description_optional_both_or_none') }}</p>

                    <!-- Language Switcher for Description -->
                    <x-form-language-switcher field-name="description" />

                    <!-- English Description -->
                    <div data-language-field="description" data-language="en" class="mb-3">
                        <textarea name="description" id="description" rows="3" class="form-textarea px-4 py-2 w-full">{{ old('description', $deal->description) }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Arabic Description -->
                    <div data-language-field="description" data-language="ar" class="mb-3" style="display: none;">
                        <textarea name="description_arabic" id="description_arabic" rows="3" class="form-textarea w-full text-right px-4 py-2" dir="rtl">{{ old('description_arabic', $deal->description_arabic) }}</textarea>
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

                    <!-- Language Switcher for Promotional Message -->
                    <x-form-language-switcher field-name="promotional_message" />

                    <!-- English Promotional Message -->
                    <div data-language-field="promotional_message" data-language="en" class="mb-3">
                        <div class="relative">
                            <input type="text" name="promotional_message" id="promotional_message" value="{{ old('promotional_message', $deal->promotional_message) }}"
                                   class="form-input px-2 w-full" maxlength="50" placeholder="{{ __('messages.promotional_message_placeholder') }}">
                            
                        </div>
                        @error('promotional_message')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Arabic Promotional Message -->
                    <div data-language-field="promotional_message" data-language="ar" class="mb-3" style="display: none;">
                        <div class="relative">
                            <input type="text" name="promotional_message_arabic" id="promotional_message_arabic" value="{{ old('promotional_message_arabic', $deal->promotional_message_arabic) }}"
                                   class="form-input px-6 w-full text-right" maxlength="50" placeholder="أدخل الرسالة الترويجية" dir="rtl">
                        
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
                        <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('messages.start_date') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="start_date" id="start_date" value="{{ old('start_date', $deal->start_date->format('Y-m-d')) }}" class="form-input w-full datepicker {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}" required>
                        @error('start_date')
                            <p class="text-red-500 text-sm mt-1 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- End Date -->
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('messages.end_date') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="end_date" id="end_date" value="{{ old('end_date', $deal->end_date->format('Y-m-d')) }}" class="form-input w-full datepicker {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}" required>
                        @error('end_date')
                            <p class="text-red-500 text-sm mt-1 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Image -->
                <div class="mt-4">
                    <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('messages.deal_image') }}</label>

                    @if($deal->image)
                        <div class="mb-2">
                            <img src="{{ asset($deal->image) }}" alt="{{ $deal->title }}" class="deal-image-preview">
                        </div>
                    @endif

                    <input type="file" name="image" id="image" class="form-input w-full" accept="image/jpeg,image/png,image/jpg,image/gif,image/svg+xml">
                    <p class="text-gray-500 text-sm mt-1 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('messages.deal_image_requirements_new') }}</p>
                    <div id="image-error" class="text-red-500 text-sm mt-1 hidden {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}"></div>
                    @error('image')
                        <p class="text-red-500 text-sm mt-1 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('messages.status') }} <span class="text-red-500">*</span></label>
                    <div class="flex {{ app()->getLocale() == 'ar' ? 'space-x-reverse' : '' }} space-x-4">
                        <label class="inline-flex items-center {{ app()->getLocale() == 'ar' ? 'flex-row-reverse' : '' }}">
                            <input type="radio" name="status" value="active" class="form-radio" {{ old('status', $deal->status) == 'active' ? 'checked' : '' }}>
                            <span class="{{ app()->getLocale() == 'ar' ? 'mr-2' : 'ml-2' }}">{{ __('messages.active') }}</span>
                        </label>
                        <label class="inline-flex items-center {{ app()->getLocale() == 'ar' ? 'flex-row-reverse' : '' }}">
                            <input type="radio" name="status" value="inactive" class="form-radio" {{ old('status', $deal->status) == 'inactive' ? 'checked' : '' }}>
                            <span class="{{ app()->getLocale() == 'ar' ? 'mr-2' : 'ml-2' }}">{{ __('messages.inactive') }}</span>
                        </label>
                    </div>
                    @error('status')
                        <p class="text-red-500 text-sm mt-1 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Application Scope -->
            <div class="form-section">
                <h3 class="form-section-title text-xl font-bold text-gray-800 dark:text-white {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('messages.deal_application') }}</h3>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('messages.deal_type') }} <span class="text-red-500">*</span></label>
                    <div class="space-y-2">
                        <label class="inline-flex items-center {{ app()->getLocale() == 'ar' ? 'flex-row-reverse' : '' }}">
                            <input type="radio" name="applies_to" value="products" class="form-radio" {{ old('applies_to', $deal->applies_to) == 'products' ? 'checked' : '' }} onchange="toggleSelectionContainers()">
                            <span class="{{ app()->getLocale() == 'ar' ? 'mr-2' : 'ml-2' }}">{{ __('messages.product_deal_description') }}</span>
                        </label>
                        <label class="inline-flex items-center {{ app()->getLocale() == 'ar' ? 'flex-row-reverse' : '' }}">
                            <input type="radio" name="applies_to" value="services" class="form-radio" {{ old('applies_to', $deal->applies_to) == 'services' ? 'checked' : '' }} onchange="toggleSelectionContainers()">
                            <span class="{{ app()->getLocale() == 'ar' ? 'mr-2' : 'ml-2' }}">{{ __('messages.service_deal_description') }}</span>
                        </label>
                        <label class="inline-flex items-center {{ app()->getLocale() == 'ar' ? 'flex-row-reverse' : '' }}">
                            <input type="radio" name="applies_to" value="products_and_services" class="form-radio" {{ old('applies_to', $deal->applies_to) == 'products_and_services' ? 'checked' : '' }} onchange="toggleSelectionContainers()">
                            <span class="{{ app()->getLocale() == 'ar' ? 'mr-2' : 'ml-2' }}">Apply this deal to both products and services</span>
                        </label>
                    </div>
                    @error('applies_to')
                        <p class="text-red-500 text-sm mt-1 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Product Selection -->
                <div id="products-container" class="mt-4" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('messages.select_products') }}</label>
                    <div class="selection-container">
                        @php
                            $selectedProductIds = old('product_ids', $deal->product_ids ?? []);
                            if (is_string($selectedProductIds)) {
                                $selectedProductIds = json_decode($selectedProductIds, true) ?? [];
                            }
                        @endphp

                        @foreach($products as $product)
                            @php
                                $hasActiveDeal = in_array($product->id, $productsWithActiveDeals ?? []);
                                $isCurrentDealProduct = in_array($product->id, $selectedProductIds);
                                $isDisabled = $hasActiveDeal && !$isCurrentDealProduct;
                            @endphp
                             <div class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded {{ $isDisabled ? 'opacity-50' : '' }}">
                                 <label class="inline-flex items-center w-full {{ app()->getLocale() == 'ar' ? 'flex-row-reverse' : '' }} {{ $isDisabled ? 'cursor-not-allowed' : 'cursor-pointer' }}">
                                     <input type="checkbox"
                                            name="product_ids[]"
                                            value="{{ $product->id }}"
                                            class="form-checkbox {{ $isDisabled ? 'cursor-not-allowed' : '' }}"
                                            {{ in_array($product->id, $selectedProductIds) ? 'checked' : '' }}
                                            {{ $isDisabled ? 'disabled' : '' }}>
                                     <span class="{{ app()->getLocale() == 'ar' ? 'mr-2' : 'ml-2' }} {{ $isDisabled ? 'text-gray-400' : '' }}">
                                         {{ $product->name }} - ${{ number_format($product->price, 2) }}
                                         @if($isDisabled)
                                             <span class="text-xs text-red-500 {{ app()->getLocale() == 'ar' ? 'mr-1' : 'ml-1' }}">({{ __('messages.has_active_deal') }})</span>
                                         @endif
                                     </span>
                                     <span class="{{ app()->getLocale() == 'ar' ? 'mr-auto' : 'ml-auto' }} text-sm {{ $isDisabled ? 'text-gray-400' : 'text-gray-500' }}">{{ $product->branch->name }}</span>
                                 </label>
                             </div>
                         @endforeach
                    </div>
                    @error('product_ids')
                         <p class="text-red-500 text-sm mt-1 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ $message }}</p>
                     @enderror
                </div>

                <!-- Service Selection -->
                <div id="services-container" class="mt-4" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('messages.select_services') }}</label>
                    <div class="selection-container">
                        @php
                            $selectedServiceIds = old('service_ids', $deal->service_ids ?? []);
                            if (is_string($selectedServiceIds)) {
                                $selectedServiceIds = json_decode($selectedServiceIds, true) ?? [];
                            }
                        @endphp

                        @foreach($services as $service)
                            @php
                                $hasActiveDeal = in_array($service->id, $servicesWithActiveDeals ?? []);
                                $isCurrentDealService = in_array($service->id, $selectedServiceIds);
                                $isDisabled = $hasActiveDeal && !$isCurrentDealService;
                            @endphp
                             <div class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded {{ $isDisabled ? 'opacity-50' : '' }}">
                                 <label class="inline-flex items-center w-full {{ app()->getLocale() == 'ar' ? 'flex-row-reverse' : '' }} {{ $isDisabled ? 'cursor-not-allowed' : 'cursor-pointer' }}">
                                     <input type="checkbox"
                                            name="service_ids[]"
                                            value="{{ $service->id }}"
                                            class="form-checkbox {{ $isDisabled ? 'cursor-not-allowed' : '' }}"
                                            {{ in_array($service->id, $selectedServiceIds) ? 'checked' : '' }}
                                            {{ $isDisabled ? 'disabled' : '' }}>
                                     <span class="{{ app()->getLocale() == 'ar' ? 'mr-2' : 'ml-2' }} {{ $isDisabled ? 'text-gray-400' : '' }}">
                                         {{ $service->name }} - ${{ number_format($service->price, 2) }} ({{ $service->duration }}{{ __('messages.min') }})
                                         @if($isDisabled)
                                             <span class="text-xs text-red-500 {{ app()->getLocale() == 'ar' ? 'mr-1' : 'ml-1' }}">({{ __('messages.has_active_deal') }})</span>
                                         @endif
                                     </span>
                                     <span class="{{ app()->getLocale() == 'ar' ? 'mr-auto' : 'ml-auto' }} text-sm {{ $isDisabled ? 'text-gray-400' : 'text-gray-500' }}">{{ $service->branch->name }}</span>
                                 </label>
                             </div>
                         @endforeach
                    </div>
                    @error('service_ids')
                         <p class="text-red-500 text-sm mt-1 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ $message }}</p>
                     @enderror
                </div>


            </div>

            <!-- Submit Buttons -->
            <div class="flex {{ app()->getLocale() == 'ar' ? 'justify-end space-x-reverse' : 'justify-end' }}  gap-4 space-x-4 mt-8">
                <a href="{{ route('vendor.deals.index') }}" class="btn-cancel rounded-[6px]">
                   
                    {{ __('messages.cancel') }}
                 
                </a>
                <button type="submit" class="btn-create-deal rounded-[6px]">
                   
                    {{ __('messages.update_deal') }}
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

        // Initialize character counters for promotional messages
        initializeCharacterCounters();

        // Initialize bilingual validation
        setupBilingualValidation();
    });

    function toggleSelectionContainers() {
        const appliesTo = document.querySelector('input[name="applies_to"]:checked').value;
        const productsContainer = document.getElementById('products-container');
        const servicesContainer = document.getElementById('services-container');

        // Hide all containers first
        productsContainer.style.display = 'none';
        servicesContainer.style.display = 'none';

        // Show relevant containers based on selection
        if (appliesTo === 'products') {
            productsContainer.style.display = 'block';
        } else if (appliesTo === 'services') {
            servicesContainer.style.display = 'block';
        } else if (appliesTo === 'products_and_services') {
            productsContainer.style.display = 'block';
            servicesContainer.style.display = 'block';
        }
    }

    function initializeCharacterCounters() {
        // English promotional message counter
        const promotionalMessageEn = document.getElementById('promotional_message');
        const charCountEn = document.getElementById('char-count-en');

        if (promotionalMessageEn && charCountEn) {
            charCountEn.textContent = promotionalMessageEn.value.length;
            promotionalMessageEn.addEventListener('input', function() {
                updateCharacterCount(this, charCountEn);
            });
        }

        // Arabic promotional message counter
        const promotionalMessageAr = document.getElementById('promotional_message_arabic');
        const charCountAr = document.getElementById('char-count-ar');

        if (promotionalMessageAr && charCountAr) {
            charCountAr.textContent = promotionalMessageAr.value.length;
            promotionalMessageAr.addEventListener('input', function() {
                updateCharacterCount(this, charCountAr);
            });
        }

        // Image validation
        const imageInput = document.getElementById('image');
        const imageError = document.getElementById('image-error');

        if (imageInput && imageError) {
            imageInput.addEventListener('change', function() {
                validateImageInput(this, imageError);
            });
        }
    }

    function updateCharacterCount(input, counter) {
        counter.textContent = input.value.length;

        // Change color when approaching limit
        if (input.value.length > 40) {
            counter.classList.add('text-orange-500');
        } else {
            counter.classList.remove('text-orange-500');
        }

        // Change color when at limit
        if (input.value.length >= 50) {
            counter.classList.add('text-red-500');
        } else {
            counter.classList.remove('text-red-500');
        }
    }

    function validateImageInput(input, errorElement) {
        errorElement.classList.add('hidden');
        errorElement.textContent = '';

        if (!input.files || input.files.length === 0) {
            return; // No file selected, which is okay for edit
        }

        const file = input.files[0];
        const maxSize = 20971520; // 20MB in bytes
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/svg+xml'];

        // Check file size
        if (file.size > maxSize) {
            errorElement.textContent = '{{ __('messages.deal_image_too_large') }}';
            errorElement.classList.remove('hidden');
            input.value = ''; // Clear the input
            return false;
        }

        // Check file type
        if (!allowedTypes.includes(file.type)) {
            errorElement.textContent = '{{ __('messages.deal_image_invalid_type') }}';
            errorElement.classList.remove('hidden');
            input.value = ''; // Clear the input
            return false;
        }

        return true;
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
                errors.push('{{ __('messages.title_required_both_languages') }}');
            }

            // Validate description (optional, but if one is filled, both must be filled)
            if (!validateBilingualField('description', false)) {
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
        const errorList = errors.map(error => `<li>${error}</li>`).join('');
        const modalContent = `
            <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="validation-modal">
                <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                    <div class="mt-3 text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mt-2">{{ __('messages.validation_errors') }}</h3>
                        <div class="mt-2 px-7 py-3">
                            <ul class="text-sm text-red-600 text-left">
                                ${errorList}
                            </ul>
                        </div>
                        <div class="items-center px-4 py-3">
                            <button id="close-modal" class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">
                                {{ __('messages.ok') }}
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
