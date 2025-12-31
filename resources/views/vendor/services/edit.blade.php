@extends('layouts.dashboard')

@section('title', __('messages.edit_service'))
@section('page-title', __('messages.edit_service'))

@section('styles')
<style>
    /* Modern Form Styling */
    .form-input-container {
        position: relative;
        margin-bottom: 1.5rem;
    }

    .form-input-container.has-label {
        margin-top: 0.5rem;
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

    .form-input, .form-textarea, .form-select {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 0.5rem;
        background-color: #ffffff;
        color: #111827;
        font-size: 0.875rem;
        transition: all 0.2s ease-in-out;
        outline: none;
    }

    .dark .form-input, .dark .form-textarea, .dark .form-select {
        background-color: #374151;
        border-color: #4b5563;
        color: #f9fafb;
    }

    /* Hover State */
    .form-input:hover, .form-textarea:hover, .form-select:hover {
        border-color: #d1d5db;
    }

    .dark .form-input:hover, .dark .form-textarea:hover, .dark .form-select:hover {
        border-color: #6b7280;
    }

    /* Active/Focus State */
    .form-input:focus, .form-textarea:focus, .form-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    /* Filled State */
    .form-input.filled, .form-textarea.filled, .form-select.filled {
        border-color: #d1d5db;
        background-color: #f9fafb;
    }

    .dark .form-input.filled, .dark .form-textarea.filled, .dark .form-select.filled {
        border-color: #6b7280;
        background-color: #4b5563;
    }

    /* Valid State */
    .form-input.valid, .form-textarea.valid, .form-select.valid {
        border-color: #10b981;
    }

    .form-input.valid:focus, .form-textarea.valid:focus, .form-select.valid:focus {
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }

    /* Error State */
    .form-input.error, .form-textarea.error, .form-select.error {
        border-color: #ef4444;
    }

    .form-input.error:focus, .form-textarea.error:focus, .form-select.error:focus {
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }

    /* Validation Icons */
    .form-input-container .validation-icon {
        position: absolute;
        right: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.2s ease-in-out;
    }

    .form-input-container.has-label .validation-icon {
        top: calc(50% + 0.875rem);
    }

    .form-input.valid + .validation-icon.valid,
    .form-textarea.valid + .validation-icon.valid,
    .form-select.valid + .validation-icon.valid {
        opacity: 1;
        color: #10b981;
    }

    .form-input.error + .validation-icon.error,
    .form-textarea.error + .validation-icon.error,
    .form-select.error + .validation-icon.error {
        opacity: 1;
        color: #ef4444;
    }

    /* Error Messages */
    .field-error-message {
        color: #ef4444;
        font-size: 0.75rem;
        margin-top: 0.25rem;
        display: block;
    }

    .dark .field-error-message {
        color: #fca5a5;
    }

    /* Textarea specific styling */
    .form-textarea {
        resize: vertical;
        min-height: 100px;
    }

    /* Select specific styling */
    .form-select {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 0.5rem center;
        background-repeat: no-repeat;
        background-size: 1.5em 1.5em;
        padding-right: 2.5rem;
        appearance: none;
    }

    .dark .form-select {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%239ca3af' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
    }

    /* Day Selection Styling */
    .day-checkbox-container .day-checkbox:checked + .day-label {
        background-color: #3b82f6;
        border-color: #3b82f6;
        color: white;
    }

    .dark .day-checkbox-container .day-checkbox:checked + .day-label {
        background-color: #3b82f6;
        border-color: #3b82f6;
        color: white;
    }

    .day-label:hover {
        background-color: #f3f4f6;
    }

    .dark .day-label:hover {
        background-color: #374151;
    }

    .day-checkbox-container .day-checkbox:checked + .day-label:hover {
        background-color: #2563eb;
    }

    /* RTL Support for Days */
    [dir="rtl"] .day-name-en {
        display: none;
    }

    [dir="rtl"] .day-name-ar {
        display: inline !important;
    }

    [dir="ltr"] .day-name-ar {
        display: none;
    }

    [dir="ltr"] .day-name-en {
        display: inline;
    }

    /* Time Input Styling */
    input[type="time"] {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M10 6v4l3 3m5-3a8 8 0 11-16 0 8 8 0 0116 0z'/%3e%3c/svg%3e");
        background-position: right 0.75rem center;
        background-repeat: no-repeat;
        background-size: 1.25rem 1.25rem;
        padding-right: 3rem;
    }

    .dark input[type="time"] {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%239ca3af' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M10 6v4l3 3m5-3a8 8 0 11-16 0 8 8 0 0116 0z'/%3e%3c/svg%3e");
    }
    /* Time picker overlay styles */
    .time-picker-overlay {
        position: fixed;
        inset: 0;
        background-color: rgba(15, 23, 42, 0.45);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 50;
    }

    .time-picker-overlay.hidden {
        display: none;
    }

    .time-picker-dialog {
        background-color: #ffffff;
        color: #111827;
        border-radius: 0.75rem;
        box-shadow: 0 20px 45px rgba(15, 23, 42, 0.18);
        padding: 1.5rem;
        width: 320px;
        max-width: 90vw;
    }

    .dark .time-picker-dialog {
        background-color: #1f2937;
        color: #f9fafb;
    }

    .time-picker-title {
        font-size: 1.125rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .time-picker-selectors {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .time-picker-selectors label {
        display: flex;
        flex-direction: column;
        font-size: 0.875rem;
        color: #4b5563;
        gap: 0.5rem;
    }

    .dark .time-picker-selectors label {
        color: #d1d5db;
    }

    .time-picker-selectors select {
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        padding: 0.5rem 0.75rem;
        font-size: 1rem;
        background-color: #ffffff;
        color: #111827;
    }

    .dark .time-picker-selectors select {
        background-color: #374151;
        border-color: #4b5563;
        color: #f9fafb;
    }

    .time-picker-actions {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
    }

    .time-picker-actions button {
        border-radius: 0.5rem;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .time-picker-cancel {
        background-color: #e5e7eb;
        color: #111827;
    }

    .time-picker-apply {
        background-color: #3b82f6;
        color: #ffffff;
    }

    .time-picker-cancel:focus,
    .time-picker-apply:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.35);
    }

    .dark .time-picker-cancel {
        background-color: #4b5563;
        color: #f9fafb;
    }

    /* Keep language switcher visible and tappable on mobile */
    @media (max-width: 768px) {
        .vendor-services-form .form-language-switcher .language-name {
            display: inline;
            font-size: 0.8rem;
        }

        .vendor-services-form .form-language-switcher .language-tab {
            padding: 0.625rem 0.875rem;
            min-height: 2.5rem;
        }
    }
</style>
@endsection

@section('content')
<div class="container mx-auto vendor-services-form">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ __('messages.edit_service') }}</h2>
                <p class="mt-1 text-gray-600 dark:text-gray-400">{{ __('messages.update_service_information') }}</p>
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
        <form action="{{ route('vendor.services.update', $service->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

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
                            <input type="text" name="name" id="name" value="{{ old('name', $service->name) }}"
                                   class="form-input"
                                   placeholder="{{ __('messages.service_name_english') }}" required>
                            @error('name')
                                <span class="field-error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Arabic Service Name -->
                        <div data-language-field="service_name" data-language="ar" style="display: none;">
                            <input type="text" name="service_name_arabic" id="service_name_arabic" value="{{ old('service_name_arabic', $service->service_name_arabic) }}"
                                   class="form-input"
                                   placeholder="{{ __('messages.service_name_arabic') }}" dir="rtl" required>
                            @error('service_name_arabic')
                                <span class="field-error-message">{{ $message }}</span>
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
                                        <option value="{{ $childCategory->id }}" {{ old('category_id', $service->category_id) == $childCategory->id ? 'selected' : '' }}>&nbsp;&nbsp;{{ $childCategory->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        @error('category_id')
                            <span class="field-error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Branch -->
                    <div class="form-input-container has-label">
                        <label for="branch_id" class="form-label">{{ __('messages.branch') }} <span class="text-red-500">*</span></label>
                        <select id="branch_id" name="branch_id" class="form-select" required>
                            <option value="">{{ __('messages.select_branch') }}</option>
                            @foreach($branches ?? [] as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id', $service->branch_id) == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                        @error('branch_id')
                            <span class="field-error-message">{{ $message }}</span>
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
                                      >{{ old('description', $service->description) }}</textarea>
                            @error('description')
                                <span class="field-error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Arabic Description -->
                        <div data-language-field="service_description" data-language="ar" style="display: none;">
                            <textarea id="service_description_arabic" name="service_description_arabic" rows="4"
                                      class="form-textarea"
                                       dir="rtl">{{ old('service_description_arabic', $service->service_description_arabic) }}</textarea>
                            @error('service_description_arabic')
                                <span class="field-error-message">{{ $message }}</span>
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
                            <input type="number" name="price" id="price" min="0" step="0.01" value="{{ old('price', $service->price) }}" class="form-input pl-7" placeholder="0.00" required>
                        </div>
                        @error('price')
                            <span class="field-error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Duration -->
                    <div class="form-input-container has-label">
                        <label for="duration" class="form-label">{{ __('messages.duration_minutes') }} <span class="text-red-500">*</span></label>
                        <input type="number" name="duration" id="duration" min="1" value="{{ old('duration', $service->duration) }}" class="form-input" required>
                        @error('duration')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Availability -->
                    <div>
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="is_available" name="is_available" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 dark:border-gray-600 rounded" {{ old('is_available', $service->is_available) ? 'checked' : '' }}>
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
                                <input id="home_service" name="home_service" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 dark:border-gray-600 rounded" value="1" {{ old('home_service', $service->home_service ?? false) ? 'checked' : '' }}>
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

                    <!-- Service Availability -->
                    <div class="space-y-4">
                        <h4 class="text-md font-medium text-gray-900 dark:text-white">{{ __('messages.service_availability') }}</h4>
                        
                        <!-- Available Days -->
                        <div class="form-input-container has-label">
                            <label class="form-label">{{ __('messages.available_days') }} <span class="text-red-500">*</span></label>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">{{ __('messages.select_available_days') }}</p>
                            
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3" id="days-selection">
                                @php
                                    $days = [
                                        0 => ['en' => 'Sunday', 'ar' => 'الأحد'],
                                        1 => ['en' => 'Monday', 'ar' => 'الإثنين'],
                                        2 => ['en' => 'Tuesday', 'ar' => 'الثلاثاء'],
                                        3 => ['en' => 'Wednesday', 'ar' => 'الأربعاء'],
                                        4 => ['en' => 'Thursday', 'ar' => 'الخميس'],
                                        5 => ['en' => 'Friday', 'ar' => 'الجمعة'],
                                        6 => ['en' => 'Saturday', 'ar' => 'السبت']
                                    ];
                                    $selectedDays = old('available_days', $service->available_days ?? []);
                                @endphp
                                
                                @foreach($days as $dayIndex => $dayNames)
                                    <div class="day-checkbox-container">
                                        <input type="checkbox" 
                                               id="day_{{ $dayIndex }}" 
                                               name="available_days[]" 
                                               value="{{ $dayIndex }}" 
                                               class="day-checkbox sr-only"
                                               {{ in_array($dayIndex, $selectedDays) ? 'checked' : '' }}>
                                        <label for="day_{{ $dayIndex }}" 
                                               class="day-label flex items-center justify-center p-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer transition-all duration-200 hover:border-blue-400 dark:hover:border-blue-500 text-sm font-medium text-gray-700 dark:text-gray-300">
                                            <span class="day-name-en">{{ $dayNames['en'] }}</span>
                                            <span class="day-name-ar hidden">{{ $dayNames['ar'] }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            
                            @error('available_days')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Service Hours -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Start Time -->
                            <div class="form-input-container has-label">
                                <label for="start_time" class="form-label">{{ __('messages.start_time') }} <span class="text-red-500">*</span></label>
                                @php
                                    $startTimeValue = old('start_time');
                                    if ($startTimeValue === null) {
                                        $startTimeValue = $service->start_time ? substr($service->start_time, 0, 5) : '09:00';
                                    } elseif (is_string($startTimeValue) && strlen($startTimeValue) >= 5) {
                                        $startTimeValue = substr($startTimeValue, 0, 5);
                                    }
                                @endphp
                                <input type="time" 
                                       name="start_time" 
                                       id="start_time" 
                                       value="{{ $startTimeValue }}" 
                                       class="form-input" 
                                       required>
                                @error('start_time')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- End Time -->
                            <div class="form-input-container has-label">
                                <label for="end_time" class="form-label">{{ __('messages.end_time') }} <span class="text-red-500">*</span></label>
                                @php
                                    $endTimeValue = old('end_time');
                                    if ($endTimeValue === null) {
                                        $endTimeValue = $service->end_time ? substr($service->end_time, 0, 5) : '17:00';
                                    } elseif (is_string($endTimeValue) && strlen($endTimeValue) >= 5) {
                                        $endTimeValue = substr($endTimeValue, 0, 5);
                                    }
                                @endphp
                                <input type="time" 
                                       name="end_time" 
                                       id="end_time" 
                                       value="{{ $endTimeValue }}" 
                                       class="form-input" 
                                       required>
                                @error('end_time')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Current Image -->
                    @if($service->image)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.current_image') }}</label>
                        <div class="mt-2">
                            <img src="{{ $service->image }}" alt="{{ $service->name }}" class="h-32 w-32 object-cover rounded-md">
                        </div>
                    </div>
                    @endif

                    <!-- Image -->
                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ $service->image ? __('messages.change_image') : __('messages.service_image') }}</label>
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
                    <i class="fas fa-save mr-2"></i> {{ __('messages.update_service') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Modern Form Validation Functions
    function initializeFormValidation() {
        const inputs = document.querySelectorAll('.form-input, .form-textarea, .form-select');
        
        inputs.forEach(input => {
            // Add event listeners for real-time validation
            input.addEventListener('input', function() {
                validateField(this);
            });
            
            input.addEventListener('blur', function() {
                validateField(this);
            });
            
            input.addEventListener('focus', function() {
                clearFieldError(this);
            });
        });
    }

    function validateField(field) {
        const value = field.value.trim();
        const isRequired = field.hasAttribute('required');
        const fieldType = field.type;
        
        // Clear previous validation states
        field.classList.remove('valid', 'error', 'filled');
        
        // Check if field has content
        if (value) {
            field.classList.add('filled');
        }
        
        // Validate required fields
        if (isRequired && !value) {
            showFieldError(field, '{{ __('messages.field_required') }}');
            return false;
        }
        
        // Type-specific validation
        if (value) {
            switch (fieldType) {
                case 'email':
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(value)) {
                        showFieldError(field, '{{ __('messages.invalid_email') }}');
                        return false;
                    }
                    break;
                    
                case 'tel':
                    const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
                    if (!phoneRegex.test(value.replace(/\s/g, ''))) {
                        showFieldError(field, '{{ __('messages.invalid_phone') }}');
                        return false;
                    }
                    break;
                    
                case 'url':
                    try {
                        new URL(value);
                    } catch {
                        showFieldError(field, '{{ __('messages.invalid_url') }}');
                        return false;
                    }
                    break;
                    
                case 'number':
                    if (isNaN(value) || value < 0) {
                        showFieldError(field, '{{ __('messages.invalid_number') }}');
                        return false;
                    }
                    break;
            }
        }
        
        // If we get here, field is valid
        if (value || !isRequired) {
            field.classList.add('valid');
            clearFieldError(field);
            return true;
        }
        
        return false;
    }

    function clearFieldError(field) {
        field.classList.remove('error');
        const container = field.closest('.form-input-container');
        if (container) {
            const errorMsg = container.querySelector('.field-error-message');
            if (errorMsg && !errorMsg.textContent.includes('{{ __('messages.field_required') }}')) {
                errorMsg.style.display = 'none';
            }
        }
    }

    function showFieldError(field, message) {
        field.classList.add('error');
        field.classList.remove('valid');
        
        const container = field.closest('.form-input-container');
        if (container) {
            let errorMsg = container.querySelector('.field-error-message');
            if (!errorMsg) {
                errorMsg = document.createElement('span');
                errorMsg.className = 'field-error-message';
                container.appendChild(errorMsg);
            }
            errorMsg.textContent = message;
            errorMsg.style.display = 'block';
        }
    }

    // Initialize validation when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        initializeFormValidation();
    });
</script>
<script>
    function previewImage(input) {
        const preview = document.getElementById('image-preview');
        const placeholder = document.getElementById('image-placeholder');
        const fileNameElement = document.getElementById('file-name');
        const errorContainer = document.getElementById('image-error-message');
        const errorText = document.getElementById('error-text');

        // Hide any existing error messages
        if (errorContainer) {
            errorContainer.classList.add('hidden');
        }

        if (input.files && input.files[0]) {
            const file = input.files[0];

            // Validate file type
            if (!file.type.startsWith('image/')) {
                if (errorContainer && errorText) {
                    showImageError(errorContainer, errorText, '{{ __('messages.select_valid_image') }}');
                }
                input.value = '';
                return;
            }

            // Enhanced file size validation (20MB limit) with immediate feedback
            if (file.size > 20 * 1024 * 1024) {
                const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
                if (errorContainer && errorText) {
                    showImageError(errorContainer, errorText, `{{ __('messages.file_size_exceeds') }} (${fileSizeMB}MB) {{ __('messages.choose_smaller_image') }}`);
                }
                input.value = '';
                return;
            }

            const reader = new FileReader();

            reader.onload = function(e) {
                // Show preview if elements exist
                if (preview) {
                    preview.classList.remove('hidden');
                    const img = preview.querySelector('img');
                    if (img) {
                        img.src = e.target.result;
                    }
                }

                // Hide placeholder if it exists
                if (placeholder) {
                    placeholder.classList.add('hidden');
                }

                // Update file name if element exists
                if (fileNameElement) {
                    fileNameElement.textContent = file.name;
                }
            }

            reader.readAsDataURL(file);
        } else {
            // Hide preview if it exists
            if (preview) {
                preview.classList.add('hidden');
            }

            // Show placeholder if it exists
            if (placeholder) {
                placeholder.classList.remove('hidden');
            }

            // Reset file name if element exists
            if (fileNameElement) {
                fileNameElement.textContent = '{{ __('messages.or_drag_drop') }}';
            }
        }
    }

    function showImageError(errorContainer, errorText, message) {
        if (errorContainer && errorText) {
            errorText.textContent = message;
            errorContainer.classList.remove('hidden');

            // Auto-hide error after 5 seconds
            setTimeout(() => {
                errorContainer.classList.add('hidden');
            }, 5000);
        }
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

        // Initialize availability validation
        setupAvailabilityValidation();
        initializeTimePickerControls();

        // Add image change listener
        const imageInput = document.getElementById('image');
        if (imageInput) {
            imageInput.addEventListener('change', function() {
                previewImage(this);
            });
        }
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

            // Validate availability
            if (!validateAvailability()) {
                hasErrors = true;
                errors.push('{{ __('messages.availability_validation_error') }}');
            }

            if (hasErrors) {
                e.preventDefault();
                showValidationModal(errors);
                return false;
            }
        });
    }

    function setupAvailabilityValidation() {
        const startTimeInput = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');
        const dayCheckboxes = document.querySelectorAll('.day-checkbox');

        // Time validation
        function validateTimes() {
            if (startTimeInput.value && endTimeInput.value) {
                const startTime = new Date('2000-01-01 ' + startTimeInput.value);
                const endTime = new Date('2000-01-01 ' + endTimeInput.value);
                
                if (endTime <= startTime) {
                    endTimeInput.setCustomValidity('{{ __('messages.end_time_after_start_time') }}');
                    return false;
                } else {
                    endTimeInput.setCustomValidity('');
                    return true;
                }
            }
            return true;
        }

        startTimeInput.addEventListener('change', validateTimes);
        endTimeInput.addEventListener('change', validateTimes);

        // Day selection validation
        dayCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const checkedDays = document.querySelectorAll('.day-checkbox:checked');
                const daysContainer = document.getElementById('days-selection');
                
                if (checkedDays.length === 0) {
                    daysContainer.classList.add('border-red-500');
                } else {
                    daysContainer.classList.remove('border-red-500');
                }
            });
        });
    }

    function validateAvailability() {
        const checkedDays = document.querySelectorAll('.day-checkbox:checked');
        const startTime = document.getElementById('start_time').value;
        const endTime = document.getElementById('end_time').value;

        // Check if at least one day is selected
        if (checkedDays.length === 0) {
            return false;
        }

        // Check if times are provided and valid
        if (!startTime || !endTime) {
            return false;
        }

        const startTimeDate = new Date('2000-01-01 ' + startTime);
        const endTimeDate = new Date('2000-01-01 ' + endTime);

        if (endTimeDate <= startTimeDate) {
            return false;
        }

        return true;
    }

    function initializeTimePickerControls() {
        ['start_time', 'end_time'].forEach(id => {
            const input = document.getElementById(id);
            if (!input) {
                return;
            }
            setupTimePickerInput(input);
        });
    }

    function setupTimePickerInput(input) {
        input.setAttribute('readonly', 'readonly');

        const allowedKeys = new Set(['Tab', 'Shift', 'ArrowLeft', 'ArrowRight', 'Home', 'End', 'Escape']);

        input.addEventListener('keydown', function(event) {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                openTimePicker(input);
                return;
            }

            if (!allowedKeys.has(event.key)) {
                event.preventDefault();
            }
        });

        const handlePointer = function(event) {
            event.preventDefault();
            input.focus();
            if (typeof input.showPicker === 'function') {
                try {
                    input.showPicker();
                    return;
                } catch (error) {
                    // Ignore and fall back to custom picker
                }
            }
            openTimePicker(input);
        };

        input.addEventListener('pointerdown', handlePointer);
        input.addEventListener('focus', function() {
            if (document.activeElement === input) {
                if (typeof input.showPicker === 'function') {
                    try {
                        input.showPicker();
                        return;
                    } catch (error) {
                        // Fall back to custom picker
                    }
                }
                openTimePicker(input);
            }
        });

        ['paste', 'drop'].forEach(evt => {
            input.addEventListener(evt, function(event) {
                event.preventDefault();
            });
        });
    }

    function openTimePicker(input) {
        const overlay = getOrCreateTimePickerOverlay(input);
        const { hourSelect, minuteSelect, close } = overlay._timePicker;

        const [hourValue, minuteValue] = (input.value || input.getAttribute('value') || '09:00').split(':');
        hourSelect.value = hourValue.padStart(2, '0');
        minuteSelect.value = minuteValue.padStart(2, '0');

        overlay.classList.remove('hidden');
        overlay.dataset.activeInput = input.id;

        const dialog = overlay.querySelector('.time-picker-dialog');
        dialog.setAttribute('tabindex', '-1');

        requestAnimationFrame(() => {
            dialog.focus();
            hourSelect.focus();
        });

        const handleEscape = function(event) {
            if (event.key === 'Escape') {
                event.preventDefault();
                close();
                document.removeEventListener('keydown', handleEscape);
            }
        };

        document.addEventListener('keydown', handleEscape);
        overlay._timePicker.handleEscape = handleEscape;
    }

    function getOrCreateTimePickerOverlay(input) {
        const overlayId = `${input.id}-time-picker-overlay`;
        let overlay = document.getElementById(overlayId);

        if (overlay) {
            return overlay;
        }

        overlay = document.createElement('div');
        overlay.id = overlayId;
        overlay.className = 'time-picker-overlay hidden';
        overlay.innerHTML = `
            <div class="time-picker-dialog" role="dialog" aria-modal="true">
                <h3 class="time-picker-title">Select Time</h3>
                <div class="time-picker-selectors">
                    <label>
                        <span>Hour</span>
                        <select class="time-picker-hour"></select>
                    </label>
                    <label>
                        <span>Minute</span>
                        <select class="time-picker-minute"></select>
                    </label>
                </div>
                <div class="time-picker-actions">
                    <button type="button" class="time-picker-cancel">Cancel</button>
                    <button type="button" class="time-picker-apply">Apply</button>
                </div>
            </div>
        `;

        document.body.appendChild(overlay);

        const hourSelect = overlay.querySelector('.time-picker-hour');
        const minuteSelect = overlay.querySelector('.time-picker-minute');
        const cancelButton = overlay.querySelector('.time-picker-cancel');
        const applyButton = overlay.querySelector('.time-picker-apply');

        for (let hour = 0; hour < 24; hour++) {
            const option = document.createElement('option');
            option.value = String(hour).padStart(2, '0');
            option.textContent = String(hour).padStart(2, '0');
            hourSelect.appendChild(option);
        }

        for (let minute = 0; minute < 60; minute++) {
            const option = document.createElement('option');
            option.value = String(minute).padStart(2, '0');
            option.textContent = String(minute).padStart(2, '0');
            minuteSelect.appendChild(option);
        }

        const close = function() {
            overlay.classList.add('hidden');
            const escapeHandler = overlay._timePicker.handleEscape;
            if (escapeHandler) {
                document.removeEventListener('keydown', escapeHandler);
                delete overlay._timePicker.handleEscape;
            }
            delete overlay.dataset.activeInput;
        };

        cancelButton.addEventListener('click', function() {
            close();
        });

        applyButton.addEventListener('click', function() {
            const hour = hourSelect.value.padStart(2, '0');
            const minute = minuteSelect.value.padStart(2, '0');
            const formatted = `${hour}:${minute}`;

            input.value = formatted;
            input.dispatchEvent(new Event('input', { bubbles: true }));
            input.dispatchEvent(new Event('change', { bubbles: true }));
            close();
        });

        overlay.addEventListener('click', function(event) {
            if (event.target === overlay) {
                close();
            }
        });

        overlay._timePicker = {
            hourSelect,
            minuteSelect,
            close
        };

        return overlay;
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
