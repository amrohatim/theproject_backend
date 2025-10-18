@extends('layouts.service-provider')

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
                <a href="{{ route('service-provider.services.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-arrow-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }} mr-2"></i> {{ __('messages.back_to_services') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Service form -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
        <form action="{{ route('service-provider.services.store') }}" method="POST" enctype="multipart/form-data">
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
                            @foreach(($allBranches ?? collect()) as $branch)
                                @php
                                    $hasActiveLicense = $branch->hasActiveLicense();
                                    $licenseStatus = $branch->getLicenseStatus();
                                    $statusText = '';

                                    if (! $hasActiveLicense) {
                                        $statusText = match ($licenseStatus) {
                                            'pending' => ' (License Pending)',
                                            'expired' => ' (License Expired)',
                                            'rejected' => ' (License Rejected)',
                                            default => ' (No Active License)',
                                        };
                                    }
                                @endphp
                                <option
                                    value="{{ $branch->id }}"
                                    {{ old('branch_id') == $branch->id ? 'selected' : '' }}
                                    {{ $hasActiveLicense ? '' : 'disabled' }}
                                    class="{{ $hasActiveLicense ? '' : 'text-gray-400 bg-gray-100' }}"
                                    title="{{ $hasActiveLicense ? '' : 'This branch has an inactive license and cannot be selected' }}"
                                >
                                    {{ $branch->name }}{{ $statusText }}
                                </option>
                            @endforeach
                        </select>
                        @error('branch_id')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror

                        @php
                            $inactiveBranches = ($allBranches ?? collect())->filter(fn($branch) => ! $branch->hasActiveLicense());
                        @endphp

                        @if($inactiveBranches->isNotEmpty())
                            <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-yellow-800">Branch License Notice</h3>
                                        <div class="mt-2 text-sm text-yellow-700">
                                            <p>Some branches are unavailable due to inactive licenses:</p>
                                            <ul class="list-disc list-inside mt-1 space-y-1">
                                                @foreach($inactiveBranches as $branch)
                                                    <li>{{ $branch->name }} - {{ ucfirst($branch->getLicenseStatus() ?? 'No license') }}</li>
                                                @endforeach
                                            </ul>
                                            <p class="mt-2">Contact your vendor to resolve license issues.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
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
                                @endphp
                                
                                @foreach($days as $dayIndex => $dayNames)
                                    <div class="day-checkbox-container">
                                        <input type="checkbox" 
                                               id="day_{{ $dayIndex }}" 
                                               name="available_days[]" 
                                               value="{{ $dayIndex }}" 
                                               class="day-checkbox sr-only"
                                               {{ in_array($dayIndex, old('available_days', [])) ? 'checked' : '' }}>
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
                                <input type="time" 
                                       name="start_time" 
                                       id="start_time" 
                                       value="{{ old('start_time', '09:00') }}" 
                                       class="form-input" 
                                       required>
                                @error('start_time')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- End Time -->
                            <div class="form-input-container has-label">
                                <label for="end_time" class="form-label">{{ __('messages.end_time') }} <span class="text-red-500">*</span></label>
                                <input type="time" 
                                       name="end_time" 
                                       id="end_time" 
                                       value="{{ old('end_time', '17:00') }}" 
                                       class="form-input" 
                                       required>
                                @error('end_time')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
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
                                        <input id="image" name="image" type="file" class="sr-only" onchange="previewImage(this)" required>
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
        setupAvailabilityValidation();
        initializeTimePickerControls();
        
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

            // Ensure an image has been selected
            const imageInput = document.getElementById('image');
            if (imageInput && imageInput.files.length === 0) {
                hasErrors = true;
                const imageRequiredMessage = '{{ __('messages.service_image') }} is required.';
                errors.push(imageRequiredMessage);

                const errorContainer = document.getElementById('image-error-message');
                const errorText = document.getElementById('error-text');
                if (errorContainer && errorText) {
                    showImageError(errorContainer, errorText, imageRequiredMessage);
                }
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
                    // Native picker failed, fall back to custom picker
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
                        // Fall back to custom picker when native picker is unavailable
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
