@extends('layouts.dashboard')

@section('title', __('messages.edit_deal'))
@section('page-title', __('messages.edit_deal'))

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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

    /* Original form section styles */
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
                        <label class="form-label {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('messages.title') }} <span class="text-red-500">*</span></label>

                        <!-- Language Switcher for Title -->
                        <x-form-language-switcher field-name="title" />

                        <!-- English Title -->
                        <div data-language-field="title" data-language="en" class="form-input-container has-label mb-3">
                            <input type="text" name="title" id="title" value="{{ old('title', $deal->title) }}"
                               class="form-input" placeholder="{{ __('messages.enter_deal_title') }}" required>
                            @error('title')
                                <span class="field-error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Arabic Title -->
                        <div data-language-field="title" data-language="ar" class="form-input-container has-label mb-3" style="display: none;">
                            <input type="text" name="title_arabic" id="title_arabic" value="{{ old('title_arabic', $deal->title_arabic) }}"
                               class="form-input text-right" placeholder="أدخل عنوان الصفقة" required dir="rtl">
                            @error('title_arabic')
                                <span class="field-error-message text-right">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Discount Percentage -->
                    <div class="form-input-container has-label">
                        <label for="discount_percentage" class="form-label {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('messages.discount_percentage') }} <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="number" name="discount_percentage" id="discount_percentage" value="{{ old('discount_percentage', $deal->discount_percentage) }}" min="0" max="100" step="0.01" class="form-input pr-14 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}" placeholder="{{ __('messages.enter_discount_percentage') }}" required onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode === 46" oninput="this.value = this.value.replace(/[^0-9.]/g, ''); if(this.value > 100) this.value = 100;">
                            <div class="absolute inset-y-0 {{ app()->getLocale() == 'ar' ? 'left-0 pl-3' : 'right-0 pr-3' }} flex items-center pointer-events-none">
                                <span class="text-gray-500 pr-6">%</span>
                            </div>
                        </div>
                        @error('discount_percentage')
                            <span class="field-error-message {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Description (Bilingual) -->
                <div class="mt-4">
                    <label class="form-label {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('messages.description') }}</label>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('messages.description_optional_both_or_none') }}</p>

                    <!-- Language Switcher for Description -->
                    <x-form-language-switcher field-name="description" />

                    <!-- English Description -->
                    <div data-language-field="description" data-language="en" class="form-input-container has-label mb-3">
                        <textarea name="description" id="description" rows="3" class="form-textarea">{{ old('description', $deal->description) }}</textarea>
                        @error('description')
                            <span class="field-error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Arabic Description -->
                    <div data-language-field="description" data-language="ar" class="form-input-container has-label mb-3" style="display: none;">
                        <textarea name="description_arabic" id="description_arabic" rows="3" class="form-textarea text-right"  dir="rtl">{{ old('description_arabic', $deal->description_arabic) }}</textarea>
                        @error('description_arabic')
                            <span class="field-error-message text-right">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Promotional Message (Bilingual) -->
                <div class="mt-4">
                    <label class="form-label {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                        {{ __('messages.promotional_message') }}
                        <span class="{{ app()->getLocale() == 'ar' ? 'mr-1' : 'ml-1' }} text-gray-500 text-xs" title="{{ __('messages.promotional_message_help') }}">
                            ({{ __('messages.optional') }})
                        </span>
                    </label>

                    <!-- Language Switcher for Promotional Message -->
                    <x-form-language-switcher field-name="promotional_message" />

                    <!-- English Promotional Message -->
                    <div data-language-field="promotional_message" data-language="en" class="form-input-container has-label mb-3">
                        <div class="relative">
                            <input type="text" name="promotional_message" id="promotional_message" value="{{ old('promotional_message', $deal->promotional_message) }}"
                                   class="form-input px-2 w-full" maxlength="50" placeholder="{{ __('messages.promotional_message_placeholder') }}">

                        </div>
                        @error('promotional_message')
                            <span class="field-error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Arabic Promotional Message -->
                    <div data-language-field="promotional_message" data-language="ar" class="form-input-container has-label mb-3" style="display: none;">
                        <div class="relative">
                            <input type="text" name="promotional_message_arabic" id="promotional_message_arabic" value="{{ old('promotional_message_arabic', $deal->promotional_message_arabic) }}"
                                   class="form-input w-full px-2  text-right" maxlength="50" placeholder="أدخل الرسالة الترويجية" dir="rtl">
                            <div class="absolute left-2 bottom-2 text-xs text-gray-500">
                                <span id="char-count-ar">0</span>/50
                            </div>
                        </div>
                        @error('promotional_message_arabic')
                            <span class="field-error-message text-right">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Date Range -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <!-- Start Date -->
                    <div class="form-input-container has-label">
                        <label for="start_date" class="form-label {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('messages.start_date') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="start_date" id="start_date" value="{{ old('start_date', $deal->start_date->format('Y-m-d')) }}" class="form-input datepicker {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}" placeholder="{{ __('messages.select_start_date') }}" min="{{ date('Y-m-d') }}" required>
                        @error('start_date')
                            <span class="field-error-message {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- End Date -->
                    <div class="form-input-container has-label">
                        <label for="end_date" class="form-label {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('messages.end_date') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="end_date" id="end_date" value="{{ old('end_date', $deal->end_date->format('Y-m-d')) }}" class="form-input datepicker {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}" placeholder="{{ __('messages.select_end_date') }}" min="{{ date('Y-m-d') }}" required>
                        @error('end_date')
                            <span class="field-error-message {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Image -->
                <div class="form-input-container has-label mt-4">
                    <label for="image" class="form-label {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('messages.deal_image') }}</label>

                    @if($deal->image)
                        <div class="mb-2">
                            <img src="{{ asset($deal->image) }}" alt="{{ $deal->title }}" class="w-32 h-32 object-cover rounded-lg border border-gray-300 dark:border-gray-600">
                        </div>
                    @endif

                    <input type="file" name="image" id="image" class="form-input" accept="image/jpeg,image/png,image/jpg,image/gif,image/svg+xml" onchange="previewImage(event)">
                    <p class="text-gray-500 text-sm mt-1 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('messages.deal_image_requirements_new') }}</p>
                    <div id="image-error" class="text-red-500 text-sm mt-1 hidden {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}"></div>
                    @error('image')
                        <span class="field-error-message {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ $message }}</span>
                    @enderror

                    <!-- Image Preview -->
                    <div id="image-preview" class="mt-3 hidden">
                        <img id="preview-img" src="" alt="Preview" class="w-32 h-32 object-cover rounded-lg border border-gray-300 dark:border-gray-600">
                    </div>
                </div>

                <!-- Status -->
                <div class="form-input-container has-label mt-4">
                    <label class="form-label {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('messages.status') }} <span class="text-red-500">*</span></label>
                    <div class="flex {{ app()->getLocale() == 'ar' ? 'space-x-reverse space-x-4' : 'space-x-4' }}">
                        <label class="inline-flex items-center">
                            <input type="radio" name="status" value="active" class="form-radio" {{ old('status', $deal->status) == 'active' ? 'checked' : '' }}>
                            <span class="{{ app()->getLocale() == 'ar' ? 'mr-2' : 'ml-2' }}">{{ __('messages.active') }}</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="status" value="inactive" class="form-radio" {{ old('status', $deal->status) == 'inactive' ? 'checked' : '' }}>
                            <span class="{{ app()->getLocale() == 'ar' ? 'mr-2' : 'ml-2' }}">{{ __('messages.inactive') }}</span>
                        </label>
                    </div>
                    @error('status')
                        <span class="field-error-message {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Application Scope -->
            <div class="form-section">
                <h3 class="form-section-title text-xl font-bold text-gray-800 dark:text-white {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('messages.deal_application') }}</h3>

                <div class="form-input-container has-label mt-4">
                    <label class="form-label {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('messages.deal_type') }} <span class="text-red-500">*</span></label>
                    <div class="space-y-2">
                        <label class="inline-flex items-center">
                            <input type="radio" name="applies_to" value="products" class="form-radio" {{ old('applies_to', $deal->applies_to) == 'products' ? 'checked' : '' }} onchange="toggleSelectionContainers()">
                            <span class="{{ app()->getLocale() == 'ar' ? 'mr-2' : 'ml-2' }}">{{ __('messages.product_deal_description') }}</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="applies_to" value="services" class="form-radio" {{ old('applies_to', $deal->applies_to) == 'services' ? 'checked' : '' }} onchange="toggleSelectionContainers()">
                            <span class="{{ app()->getLocale() == 'ar' ? 'mr-2' : 'ml-2' }}">{{ __('messages.service_deal_description') }}</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="applies_to" value="products_and_services" class="form-radio" {{ old('applies_to', $deal->applies_to) == 'products_and_services' ? 'checked' : '' }} onchange="toggleSelectionContainers()">
                            <span class="{{ app()->getLocale() == 'ar' ? 'mr-2' : 'ml-2' }}">Apply this deal to both products and services</span>
                        </label>
                    </div>
                    @error('applies_to')
                        <span class="field-error-message {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Product Selection -->
                <div id="products-container" class="form-input-container has-label mt-4" style="display: none;">
                    <label class="form-label {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('messages.select_products') }}</label>
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
                        <span class="field-error-message {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Service Selection -->
                <div id="services-container" class="form-input-container has-label mt-4" style="display: none;">
                    <label class="form-label {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('messages.select_services') }}</label>
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
                        <span class="field-error-message {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ $message }}</span>
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

        // Add event listeners for applies_to radio buttons
        const appliesRadios = document.querySelectorAll('input[name="applies_to"]');
        appliesRadios.forEach(radio => {
            radio.addEventListener('change', toggleSelectionContainers);
        });

        // Initialize character counters for promotional messages
        initializeCharacterCounters();

        // Initialize bilingual validation
        setupBilingualValidation();

        // Initialize form validation states
        initializeFormValidation();
    });

    function toggleSelectionContainers() {
        const checkedRadio = document.querySelector('input[name="applies_to"]:checked');
        const productsContainer = document.getElementById('products-container');
        const servicesContainer = document.getElementById('services-container');

        // Hide all containers first
        if (productsContainer) productsContainer.style.display = 'none';
        if (servicesContainer) servicesContainer.style.display = 'none';

        // Show relevant containers based on selection
        if (checkedRadio) {
            const appliesTo = checkedRadio.value;
            if (appliesTo === 'products' && productsContainer) {
                productsContainer.style.display = 'block';
            } else if (appliesTo === 'services' && servicesContainer) {
                servicesContainer.style.display = 'block';
            } else if (appliesTo === 'products_and_services') {
                if (productsContainer) productsContainer.style.display = 'block';
                if (servicesContainer) servicesContainer.style.display = 'block';
            }
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

    function initializeFormValidation() {
        // Get all form inputs, textareas, and selects
        const formElements = document.querySelectorAll('.form-input, .form-textarea, .form-select');

        formElements.forEach(element => {
            // Track if user has interacted with this element
            let hasInteracted = false;

            // Add event listeners for real-time validation
            element.addEventListener('input', function() {
                hasInteracted = true;
                validateFormElement(this);
            });

            element.addEventListener('blur', function() {
                if (hasInteracted) {
                    validateFormElement(this);
                }
            });

            element.addEventListener('focus', function() {
                // Remove error state on focus
                this.classList.remove('error');
                const container = this.closest('.form-input-container');
                if (container) {
                    container.classList.remove('error');
                }
            });

            // Don't run initial validation - let fields start in neutral state
        });
    }

    function validateFormElement(element) {
        const container = element.closest('.form-input-container');
        const value = element.value.trim();
        const isRequired = element.hasAttribute('required') || element.closest('[data-required="true"]');

        // Remove existing validation classes
        element.classList.remove('valid', 'error');
        if (container) {
            container.classList.remove('valid', 'error');
        }

        // Check if element has value
        if (value !== '') {
            // Element has value - check if it's valid
            let isValid = true;

            // Basic validation based on input type
            if (element.type === 'email') {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                isValid = emailRegex.test(value);
            } else if (element.type === 'number') {
                isValid = !isNaN(value) && value >= 0;
            } else if (element.type === 'url') {
                try {
                    new URL(value);
                    isValid = true;
                } catch {
                    isValid = false;
                }
            }

            // Special validation for specific fields
            if (element.id === 'discount_percentage') {
                const num = parseFloat(value);
                isValid = !isNaN(num) && num >= 0 && num <= 100;
            }

            if (isValid) {
                element.classList.add('valid');
                if (container) {
                    container.classList.add('valid');
                }
            }
        } else if (isRequired) {
            // Required field is empty
            element.classList.add('error');
            if (container) {
                container.classList.add('error');
            }
        }
    }

    function validateBilingualField(fieldName, required) {
        const englishField = document.getElementById(fieldName);
        const arabicField = document.getElementById(fieldName + '_arabic');

        if (!englishField || !arabicField) return true;

        const englishValue = englishField.value.trim();
        const arabicValue = arabicField.value.trim();

        if (required) {
            // Both fields are required
            return englishValue !== '' && arabicValue !== '';
        } else {
            // Either both filled or both empty
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

    function previewImage(event) {
        const file = event.target.files[0];
        const preview = document.getElementById('image-preview');
        const previewImg = document.getElementById('preview-img');

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                preview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            preview.classList.add('hidden');
        }
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
