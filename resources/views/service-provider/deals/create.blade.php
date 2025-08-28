@extends('layouts.service-provider')

@section('title', __('service_provider.create_deal'))
@section('page-title', __('service_provider.create_deal'))

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
    .service-item {
        padding: 0.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.375rem;
        margin-bottom: 0.5rem;
        transition: all 0.2s;
    }
    .service-item:hover {
        background-color: #f8fafc;
        border-color: #53D2DC;
    }
    .service-item.selected {
        background-color: #53D2DC10;
        border-color: #53D2DC;
    }
    .service-item.has-active-deal {
        background-color: #f9f9f9;
        border-color: #e5e5e5;
    }
    .service-item.has-active-deal input[type="checkbox"]:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
</style>
@endsection

@section('content')
<div class="container mx-auto">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ __('service_provider.create_deal') }}</h2>
                <p class="mt-1 text-gray-600 dark:text-gray-400">{{ __('service_provider.create_new_deal_for_services') }}</p>
            </div>
            <div>
                <a href="{{ route('service-provider.deals.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-arrow-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }} mr-2"></i> {{ __('service_provider.back_to_deals') }}
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
        <form action="{{ route('service-provider.deals.store') }}" method="POST" enctype="multipart/form-data" id="dealForm">
            @csrf

            <!-- Basic Information -->
            <div class="form-section">
                <h3 class="form-section-title text-xl font-bold text-gray-800 dark:text-white">{{ __('service_provider.deal_information') }}</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Title (Bilingual) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('service_provider.title') }} <span class="text-red-500">*</span></label>

                        <!-- Language Switcher for Title -->
                        <x-form-language-switcher field-name="title" />

                        <!-- English Title -->
                        <div data-language-field="title" data-language="en" class="mb-3 active-language-field">
                            <input type="text" name="title" id="title" value="{{ old('title') }}"
                                   class="mt-1 border-gray-300 border-[1px]  px-2 py-2 block w-full shadow-sm sm:text-sm  dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md"
                                   placeholder="{{ __('service_provider.enter_deal_title') }}" required>
                            @error('title')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Arabic Title -->
                        <div data-language-field="title" data-language="ar" style="display: none;">
                            <input type="text" name="title_arabic" id="title_arabic" value="{{ old('title_arabic') }}"
                                   class="mt-1 border-gray-300 border-[1px]  px-2 py-2 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md"
                                   placeholder="{{ __('service_provider.enter_deal_title_arabic') }}" dir="rtl" required>
                            @error('title_arabic')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Discount Percentage -->
                    <div>
                        <label for="discount_percentage" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('service_provider.discount_percentage') }} <span class="text-red-500">*</span></label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input type="number" name="discount_percentage" id="discount_percentage" min="1" max="100" step="0.01" value="{{ old('discount_percentage') }}" 
                                   class="border-gray-300 border-[1px]  px-2 py-2 block w-full pr-12 sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" 
                                   placeholder="15" required
                                   onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode === 46"
                                   oninput="this.value = this.value.replace(/[^0-9.]/g, ''); if(this.value > 100) this.value = 100;">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">%</span>
                            </div>
                        </div>
                        @error('discount_percentage')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Description (Bilingual) -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('service_provider.description') }}</label>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">{{ __('service_provider.description_optional_both_or_none') }}</p>

                    <!-- Language Switcher for Description -->
                    <x-form-language-switcher field-name="description" />

                    <!-- English Description -->
                    <div data-language-field="description" data-language="en" class="mb-3 active-language-field">
                        <textarea id="description" name="description" rows="3"
                                  class="mt-1 border-gray-300 border-[1px]  px-4 py-2 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md"
                                  placeholder="{{ __('service_provider.enter_deal_description') }}">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Arabic Description -->
                    <div data-language-field="description" data-language="ar" style="display: none;">
                        <textarea id="description_arabic" name="description_arabic" rows="3"
                                  class="mt-1 border-gray-300 border-[1px]  px-4 py-2 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md"
                                  placeholder="{{ __('service_provider.enter_deal_description_arabic') }}" dir="rtl">{{ old('description_arabic') }}</textarea>
                        @error('description_arabic')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Promotional Message (Bilingual) -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('service_provider.promotional_message') }}</label>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">{{ __('service_provider.promotional_message_optional_both_or_none') }}</p>

                    <!-- Language Switcher for Promotional Message -->
                    <x-form-language-switcher field-name="promotional_message" />

                    <!-- English Promotional Message -->
                    <div data-language-field="promotional_message" data-language="en" class="mb-3 active-language-field">
                        <div class="relative">
                            <input type="text" name="promotional_message" id="promotional_message" maxlength="50" value="{{ old('promotional_message') }}"
                                   class="mt-1 border-gray-300 border-[1px]  px-2 py-2 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md"
                                   placeholder="{{ __('service_provider.enter_promotional_message') }}">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-400 text-xs" id="promo_count_en">0/50</span>
                            </div>
                        </div>
                        @error('promotional_message')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Arabic Promotional Message -->
                    <div data-language-field="promotional_message" data-language="ar" style="display: none;">
                        <div class="relative">
                            <input type="text" name="promotional_message_arabic" id="promotional_message_arabic" maxlength="50" value="{{ old('promotional_message_arabic') }}"
                                   class="mt-1 border-gray-300 border-[1px]  px-2 py-2 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md"
                                   placeholder="{{ __('service_provider.enter_promotional_message_arabic') }}" dir="rtl">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-400 text-xs" id="promo_count_ar">0/50</span>
                            </div>
                        </div>
                        @error('promotional_message_arabic')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Date Range -->
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('service_provider.start_date') }} <span class="text-red-500">*</span></label>
                        <input type="date" name="start_date" id="start_date" value="{{ old('start_date', date('Y-m-d')) }}"
                               class="mt-1 border-gray-300 border-[1px]  px-2 py-2 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" required>
                        @error('start_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('service_provider.end_date') }} <span class="text-red-500">*</span></label>
                        <input type="date" name="end_date" id="end_date" value="{{ old('end_date', date('Y-m-d', strtotime('+30 days'))) }}" 
                               class="mt-1 border-gray-300 border-[1px]  px-2 py-2 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" required>
                        @error('end_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Deal Image -->
                <div class="mt-6">
                    <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('service_provider.deal_image') }}</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-md">
                        <div class="space-y-1 text-center" id="image-upload-container">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true" id="image-placeholder">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div id="image-preview" class="mt-2 hidden">
                                <img src="#" alt="Image Preview" class="mx-auto h-32 w-auto object-cover rounded-md">
                            </div>
                            <div class="flex text-sm text-gray-600 dark:text-gray-400">
                                <label for="image" class="relative cursor-pointer bg-white dark:bg-gray-700 rounded-md font-medium text-[#53D2DC] hover:text-[#53D2DC]/80 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-[#53D2DC]">
                                    <span>{{ __('service_provider.upload_file') }}</span>
                                    <input id="image" name="image" type="file" class="sr-only" onchange="previewImage(this)">
                                </label>
                                <p class="pl-1" id="file-name">{{ __('service_provider.or_drag_drop') }}</p>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ __('service_provider.deal_image_format_size') }}
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
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('service_provider.status') }} <span class="text-red-500">*</span></label>
                    <div class="mt-2 space-y-2">
                        <div class="flex items-center">
                            <input id="status_active" name="status" type="radio" value="active" class="focus:ring-[#53D2DC] h-4 w-4 text-[#53D2DC] border-gray-300 dark:border-gray-600" {{ old('status', 'active') == 'active' ? 'checked' : '' }}>
                            <label for="status_active" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('service_provider.active') }}</label>
                        </div>
                        <div class="flex items-center">
                            <input id="status_inactive" name="status" type="radio" value="inactive" class="focus:ring-[#53D2DC] h-4 w-4 text-[#53D2DC] border-gray-300 dark:border-gray-600" {{ old('status') == 'inactive' ? 'checked' : '' }}>
                            <label for="status_inactive" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('service_provider.inactive') }}</label>
                        </div>
                    </div>
                    @error('status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Service Selection -->
            <div class="form-section">
                <h3 class="form-section-title text-xl font-bold text-gray-800 dark:text-white">{{ __('service_provider.select_services') }}</h3>

                <div class="mb-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('service_provider.select_services_for_deal') }}</p>
                </div>

                @if($services->count())
                    <div class="selection-container">
                        @foreach($services as $service)
                            @php
                                $hasActiveDeal = in_array($service->id, $servicesWithActiveDeals ?? []);
                            @endphp
                            <div class="service-item {{ $hasActiveDeal ? 'has-active-deal' : '' }}">
                                <div class="flex items-center">
                                    <input id="service_{{ $service->id }}" name="service_ids[]" type="checkbox" value="{{ $service->id }}"
                                           class="focus:ring-[#53D2DC] h-4 w-4 text-[#53D2DC] border-gray-300 dark:border-gray-600 rounded"
                                           {{ $hasActiveDeal ? 'disabled' : '' }}
                                           {{ in_array($service->id, old('service_ids', [])) ? 'checked' : '' }}>
                                    <label for="service_{{ $service->id }}" class="ml-3 flex-1 {{ $hasActiveDeal ? 'cursor-not-allowed opacity-50' : 'cursor-pointer' }}">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $service->name }}
                                                    @if($hasActiveDeal)
                                                        <span class="text-red-500 text-xs ml-2">({{ __('service_provider.has_active_deal') }})</span>
                                                    @endif
                                                </div>
                                                @if($service->service_name_arabic)
                                                    <div class="text-xs text-gray-500 dark:text-gray-400" dir="rtl">{{ $service->service_name_arabic }}</div>
                                                @endif
                                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $service->branch->name ?? __('service_provider.no_branch') }}</div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">${{ number_format($service->price, 2) }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $service->duration }} min</div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 text-center">
                        <div class="mx-auto h-14 w-14 rounded-full bg-[#53D2DC]/15 flex items-center justify-center">
                            <i class="fas fa-cog text-[#53D2DC]"></i>
                        </div>
                        <h4 class="mt-3 text-gray-900 dark:text-white font-medium">{{ __('service_provider.no_services_available') }}</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('service_provider.no_services_to_create_deals') }}</p>
                    </div>
                @endif

                @error('service_ids')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('service-provider.deals.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    {{ __('service_provider.cancel') }}
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-[#53D2DC] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#53D2DC]/90 active:bg-[#53D2DC]/80 focus:outline-none focus:border-[#53D2DC] focus:ring ring-[#53D2DC]/30 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-save mr-2"></i> {{ __('service_provider.create_deal') }}
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Deal Image Validation Modal -->
<div id="dealImageValidationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900">
                <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 text-xl"></i>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mt-4">{{ __('service_provider.deal_image_required') }}</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('service_provider.deal_image_required_message') }}</p>
            </div>
            <div class="items-center px-4 py-3">
                <button id="closeDealImageModal" class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300 transition-colors duration-200">
                    {{ __('service_provider.close') }}
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize date pickers
    flatpickr("#start_date", {
        dateFormat: "Y-m-d",
        minDate: "today",
        onChange: function(selectedDates, dateStr, instance) {
            // Update end date minimum to be after start date
            const endDatePicker = document.querySelector("#end_date")._flatpickr;
            if (endDatePicker) {
                endDatePicker.set('minDate', dateStr);
            }
        }
    });

    flatpickr("#end_date", {
        dateFormat: "Y-m-d",
        minDate: "today"
    });

    // Character counters for promotional messages
    const promoEn = document.getElementById('promotional_message');
    const promoAr = document.getElementById('promotional_message_arabic');
    const countEn = document.getElementById('promo_count_en');
    const countAr = document.getElementById('promo_count_ar');

    if (promoEn && countEn) {
        promoEn.addEventListener('input', function() {
            countEn.textContent = `${this.value.length}/50`;
        });
        // Initialize counter
        countEn.textContent = `${promoEn.value.length}/50`;
    }

    if (promoAr && countAr) {
        promoAr.addEventListener('input', function() {
            countAr.textContent = `${this.value.length}/50`;
        });
        // Initialize counter
        countAr.textContent = `${promoAr.value.length}/50`;
    }

    // Service selection functionality
    const serviceCheckboxes = document.querySelectorAll('input[name="service_ids[]"]');
    serviceCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const serviceItem = this.closest('.service-item');
            if (this.checked) {
                serviceItem.classList.add('selected');
            } else {
                serviceItem.classList.remove('selected');
            }
        });

        // Initialize selected state
        if (checkbox.checked) {
            checkbox.closest('.service-item').classList.add('selected');
        }
    });

    // Form validation
    const form = document.getElementById('dealForm');
    form.addEventListener('submit', function(e) {
        let hasErrors = false;
        const errors = [];

        // Validate image upload (mandatory for deal creation)
        const imageInput = document.getElementById('image');
        if (!imageInput || !imageInput.files || imageInput.files.length === 0) {
            e.preventDefault();
            showDealImageValidationModal();
            return false;
        }

        // Validate bilingual fields
        if (!validateBilingualField('title', true)) {
            hasErrors = true;
            errors.push('{{ __('service_provider.title_required_both_languages') }}');
        }

        if (!validateBilingualField('description', false)) {
            hasErrors = true;
            errors.push('{{ __('service_provider.description_both_or_none') }}');
        }

        if (!validateBilingualField('promotional_message', false)) {
            hasErrors = true;
            errors.push('{{ __('service_provider.promotional_message_both_or_none') }}');
        }

        // Validate discount percentage
        const discountPercentage = document.getElementById('discount_percentage').value;
        if (!discountPercentage || discountPercentage < 1 || discountPercentage > 100) {
            hasErrors = true;
            errors.push('{{ __('service_provider.discount_percentage_required') }}');
        }

        // Validate dates
        const startDateValue = document.getElementById('start_date').value;
        const endDateValue = document.getElementById('end_date').value;

        if (!startDateValue) {
            hasErrors = true;
            errors.push('{{ __('service_provider.start_date_required') }}');
        }

        if (!endDateValue) {
            hasErrors = true;
            errors.push('{{ __('service_provider.end_date_required') }}');
        }

        // Validate date range
        if (startDateValue && endDateValue) {
            const startDate = new Date(startDateValue);
            const endDate = new Date(endDateValue);
            if (endDate <= startDate) {
                hasErrors = true;
                errors.push('{{ __('service_provider.end_date_must_be_after_start_date') }}');
            }
        }

        // Validate service selection
        const selectedServices = document.querySelectorAll('input[name="service_ids[]"]:checked');
        if (selectedServices.length === 0) {
            hasErrors = true;
            errors.push('{{ __('service_provider.select_at_least_one_service') }}');
        }

        // Check for services with active deals
        const disabledServices = document.querySelectorAll('input[name="service_ids[]"]:disabled:checked');
        if (disabledServices.length > 0) {
            hasErrors = true;
            errors.push('{{ __('service_provider.cannot_select_services_with_active_deals') }}');
        }

        if (hasErrors) {
            e.preventDefault();
            showValidationModal(errors);
            return false;
        }
    });
});

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
            showImageError(errorContainer, errorText, '{{ __('service_provider.select_valid_image') }}');
            input.value = '';
            return;
        }

        // Enhanced file size validation (20MB limit) with immediate feedback
        if (file.size > 20 * 1024 * 1024) {
            const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
            showImageError(errorContainer, errorText, `{{ __('service_provider.file_size_exceeds') }}`.replace(':size', fileSizeMB + 'MB'));
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
        fileNameElement.textContent = '{{ __('service_provider.or_drag_drop') }}';
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

function validateBilingualField(fieldName, required) {
    const englishField = document.querySelector(`[name="${fieldName}"]`);
    const arabicField = document.querySelector(`[name="${fieldName}_arabic"]`);

    if (!englishField || !arabicField) return true;

    const englishValue = englishField.value.trim();
    const arabicValue = arabicField.value.trim();

    if (required) {
        // Both fields are required
        return englishValue !== '' && arabicValue !== '';
    } else {
        // Both fields must be filled or both must be empty
        return (englishValue === '' && arabicValue === '') || (englishValue !== '' && arabicValue !== '');
    }
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
                    <h3 class="text-lg font-medium text-gray-900 mt-2">{{ __('service_provider.validation_errors') }}</h3>
                    <div class="mt-2 px-7 py-3">
                        <ul class="text-sm text-left list-disc list-inside">
                            ${errorList}
                        </ul>
                    </div>
                    <div class="items-center px-4 py-3">
                        <button id="closeModal" class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">
                            {{ __('service_provider.close') }}
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

// Show deal image validation modal
function showDealImageValidationModal() {
    const modal = document.getElementById('dealImageValidationModal');
    if (modal) {
        modal.classList.remove('hidden');
    }
}

// Hide deal image validation modal
function hideDealImageValidationModal() {
    const modal = document.getElementById('dealImageValidationModal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

// Initialize modal functionality when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Setup modal close functionality
    const closeModalBtn = document.getElementById('closeDealImageModal');
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', hideDealImageValidationModal);
    }

    // Close modal when clicking outside
    const modal = document.getElementById('dealImageValidationModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                hideDealImageValidationModal();
            }
        });
    }
});
</script>
@endsection
