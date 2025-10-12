@extends('layouts.dashboard')

@section('title', 'Create Branch')
@section('page-title', 'Create Branch')

@section('styles')
<style>
/* Custom Form Styles - Pixel Perfect Figma Implementation */
.form-input-container {
    position: relative;
    margin-bottom: 1rem;
}

.form-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
    margin-bottom: 0.5rem;
    transition: color 0.2s ease;
}

.dark .form-label {
    color: #D1D5DB;
}

.form-input, .form-textarea, .form-select {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid #D1D5DB;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    line-height: 1.25rem;
    background-color: #FFFFFF;
    color: #111827;
    transition: all 0.2s ease;
    outline: none;
    position: relative;
}

.dark .form-input, .dark .form-textarea, .dark .form-select {
    background-color: #1F2937;
    border-color: #374151;
    color: #F9FAFB;
}

/* Normal State */
.form-input:not(.active):not(.filled):not(.valid):not(.error),
.form-textarea:not(.active):not(.filled):not(.valid):not(.error),
.form-select:not(.active):not(.filled):not(.valid):not(.error) {
    border-color: #D1D5DB;
    background-color: #FFFFFF;
}

.dark .form-input:not(.active):not(.filled):not(.valid):not(.error),
.dark .form-textarea:not(.active):not(.filled):not(.valid):not(.error),
.dark .form-select:not(.active):not(.filled):not(.valid):not(.error) {
    border-color: #374151;
    background-color: #1F2937;
}

/* Active State (Focus) */
.form-input.active, .form-textarea.active, .form-select.active,
.form-input:focus, .form-textarea:focus, .form-select:focus {
    border-color: #3B82F6;
    border-width: 2px;
    background-color: #FFFFFF;
    outline: none;
}

.dark .form-input.active, .dark .form-textarea.active, .dark .form-select.active,
.dark .form-input:focus, .dark .form-textarea:focus, .dark .form-select:focus {
    border-color: #60A5FA;
    border-width: 2px;
    background-color: #1F2937;
    outline: none;
}

/* Filled State */
.form-input.filled, .form-textarea.filled, .form-select.filled {
    border-color: #6B7280;
    background-color: #F9FAFB;
}

.dark .form-input.filled, .dark .form-textarea.filled, .dark .form-select.filled {
    border-color: #4B5563;
    background-color: #111827;
}

/* Valid State */
.form-input.valid, .form-textarea.valid, .form-select.valid {
    border-color: #10B981;
    background-color: #F0FDF4;
}

.dark .form-input.valid, .dark .form-textarea.valid, .dark .form-select.valid {
    border-color: #34D399;
    background-color: #064E3B;
}

/* Error State */
.form-input.error, .form-textarea.error, .form-select.error {
    border-color: #EF4444;
    background-color: #FEF2F2;
}

.dark .form-input.error, .dark .form-textarea.error, .dark .form-select.error {
    border-color: #F87171;
    background-color: #7F1D1D;
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

.form-input.valid ~ .validation-icon.valid-icon,
.form-textarea.valid ~ .validation-icon.valid-icon,
.form-select.valid ~ .validation-icon.valid-icon {
    opacity: 1;
    color: #10B981;
}

.dark .form-input.valid ~ .validation-icon.valid-icon,
.dark .form-textarea.valid ~ .validation-icon.valid-icon,
.dark .form-select.valid ~ .validation-icon.valid-icon {
    color: #34D399;
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

/* Error Messages */
.form-error-message {
    margin-top: 0.25rem;
    font-size: 0.75rem;
    color: #EF4444;
}

.dark .form-error-message {
    color: #F87171;
}

/* Placeholder Styling */
.form-input::placeholder, .form-textarea::placeholder {
    color: #9CA3AF;
}

.dark .form-input::placeholder, .dark .form-textarea::placeholder {
    color: #6B7280;
}

/* Select Arrow Styling */
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

/* File Input Styling */
.form-file-input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid #D1D5DB;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    background-color: #FFFFFF;
    color: #111827;
    transition: all 0.2s ease;
    outline: none;
}

.dark .form-file-input {
    background-color: #1F2937;
    border-color: #374151;
    color: #F9FAFB;
}

.form-file-input:focus {
    border-color: #3B82F6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.dark .form-file-input:focus {
    border-color: #60A5FA;
    box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.1);
}

/* Responsive Design */
@media (max-width: 768px) {
    .form-input, .form-textarea, .form-select {
        font-size: 1rem;
        padding: 0.875rem 1rem;
    }
}

/* Animation for state transitions */
.form-input, .form-textarea, .form-select {
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out, background-color 0.15s ease-in-out;
}

/* Focus visible for accessibility - handled by our custom focus styles */
.form-input:focus-visible, .form-textarea:focus-visible, .form-select:focus-visible {
    outline: none;
}
</style>
@endsection

@section('content')
<div class="container mx-auto">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ __('messages.create_new_branch') }}</h2>
        <p class="mt-1 text-gray-600 dark:text-gray-400">{{ __('messages.add_new_branch_to_company') }}</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
        <form action="{{ route('vendor.branches.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="form-input-container has-label">
                    <label for="name" class="form-label">{{ __('messages.branch_name') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-input" required>
                    <svg class="validation-icon valid-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <svg class="validation-icon error-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    @error('name')
                        <p class="form-error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-input-container has-label">
                    <label for="company_id" class="form-label">{{ __('messages.company') }} <span class="text-red-500">*</span></label>
                    <select id="company_id" name="company_id" class="form-select" required>
                        <option value="">{{ __('messages.select_company') }}</option>
                        @foreach($companies ?? [] as $company)
                            <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                        @endforeach
                    </select>
                    <svg class="validation-icon valid-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <svg class="validation-icon error-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    @error('company_id')
                        <p class="form-error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-input-container has-label">
                    <label for="business_type" class="form-label">Business Type <span class="text-red-500">*</span></label>
                    <select id="business_type" name="business_type" class="form-select" required onchange="updateBusinessTypeHidden(this.value)">
                        <option value="">Select Business Type</option>
                        @php
                            // Ensure business types are available
                            if (!isset($businessTypes) || (isset($businessTypes) && $businessTypes->isEmpty())) {
                                $businessTypes = \App\Models\BusinessType::orderBy('business_name')->get();
                            }
                        @endphp
                        @foreach($businessTypes ?? [] as $businessType)
                            <option value="{{ $businessType->business_name }}" {{ old('business_type') == $businessType->business_name ? 'selected' : '' }}>{{ $businessType->business_name }}</option>
                        @endforeach
                    </select>
                    <!-- Hidden backup field to ensure business type is always submitted -->
                    <input type="hidden" id="business_type_hidden" name="business_type_backup" value="{{ old('business_type') }}">
                    <svg class="validation-icon valid-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <svg class="validation-icon error-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    @error('business_type')
                        <p class="form-error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-input-container has-label">
                    <label for="phone" class="form-label">{{ __('messages.phone_number') }}</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="form-input">
                    <svg class="validation-icon valid-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <svg class="validation-icon error-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    @error('phone')
                        <p class="form-error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-input-container has-label">
                    <label for="email" class="form-label">{{ __('messages.email_address') }}</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-input">
                    <svg class="validation-icon valid-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <svg class="validation-icon error-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    @error('email')
                        <p class="form-error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-span-1 md:col-span-2 form-input-container has-label">
                    <label for="description" class="form-label">{{ __('messages.description') }}</label>
                    <textarea id="description" name="description" rows="3" class="form-textarea">{{ old('description') }}</textarea>
                    <svg class="validation-icon valid-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <svg class="validation-icon error-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    @error('description')
                        <p class="form-error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-span-1 md:col-span-2 form-input-container has-label">
                    <label for="address" class="form-label">{{ __('messages.address') }} <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="text" name="address" id="address" value="{{ old('address') }}" class="form-input cursor-not-allowed" readonly required>
                        <div id="address-loading" class="absolute right-3 top-1/2 transform -translate-y-1/2 hidden">
                            <svg class="animate-spin h-4 w-4 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                        <svg class="validation-icon valid-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <svg class="validation-icon error-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('messages.address_auto_fill_hint') }}</p>
                    @error('address')
                        <p class="form-error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-input-container has-label">
                    <label for="emirate" class="form-label">{{ __('messages.emirate') }} <span class="text-red-500">*</span></label>
                    <select id="emirate" name="emirate" class="form-select" required>
                        <option value="">{{ __('messages.select_emirate') }}</option>
                        <option value="Dubai" {{ old('emirate') == 'Dubai' ? 'selected' : '' }}>{{ __('messages.dubai') }}</option>
                        <option value="Abu Dhabi" {{ old('emirate') == 'Abu Dhabi' ? 'selected' : '' }}>{{ __('messages.abu_dhabi') }}</option>
                        <option value="Sharjah" {{ old('emirate') == 'Sharjah' ? 'selected' : '' }}>{{ __('messages.sharjah') }}</option>
                        <option value="Ajman" {{ old('emirate') == 'Ajman' ? 'selected' : '' }}>{{ __('messages.ajman') }}</option>
                        <option value="Umm Al Quwain" {{ old('emirate') == 'Umm Al Quwain' ? 'selected' : '' }}>{{ __('messages.umm_al_quwain') }}</option>
                        <option value="Ras Al Khaimah" {{ old('emirate') == 'Ras Al Khaimah' ? 'selected' : '' }}>{{ __('messages.ras_al_khaimah') }}</option>
                        <option value="Fujairah" {{ old('emirate') == 'Fujairah' ? 'selected' : '' }}>{{ __('messages.fujairah') }}</option>
                    </select>
                    <svg class="validation-icon valid-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <svg class="validation-icon error-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    @error('emirate')
                        <p class="form-error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('messages.location_on_map') }} <span class="text-red-500">*</span></label>
                    <div class="mb-2">
                        <input id="pac-input" type="text" placeholder="{{ __('messages.search_for_location') }}" class="w-full p-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                    </div>
                    <div id="map" style="height: 400px; width: 100%; border-radius: 0.375rem;" class="border border-gray-300 dark:border-gray-600"></div>
                    <input type="hidden" name="lat" id="lat" value="{{ old('lat', 25.2048) }}">
                    <input type="hidden" name="lng" id="lng" value="{{ old('lng', 55.2708) }}">
                </div>

                <div class="form-input-container has-label">
                    <label for="status" class="form-label">{{ __('messages.status') }}</label>
                    <select id="status" name="status" class="form-select">
                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>{{ __('messages.active') }}</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>{{ __('messages.inactive') }}</option>
                    </select>
                    <svg class="validation-icon valid-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <svg class="validation-icon error-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    @error('status')
                        <p class="form-error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-span-1 md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('messages.branch_image') }}</h3>
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md border border-gray-200 dark:border-gray-600">
                        <div class="flex flex-col space-y-4">
                            <div class="flex items-center">
                                <input type="checkbox" name="use_company_image" id="use_company_image" value="1" class="form-checkbox h-5 w-5 text-indigo-600 dark:text-indigo-400" {{ old('use_company_image', '1') ? 'checked' : '' }}>
                                <label for="use_company_image" class="ml-2 text-gray-700 dark:text-gray-300">{{ __('messages.use_company_image') }}</label>
                            </div>

                            <div id="branch_image_container" class="{{ old('use_company_image', '1') ? 'hidden' : '' }}">
                                <label for="branch_image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.branch_image') }}</label>
                                <div class="mt-1 flex items-center">
                                    <input type="file" name="branch_image" id="branch_image" accept="image/jpeg,image/png,image/webp" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                </div>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('messages.image_upload_requirements') }}</p>
                                @error('branch_image')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Business Hours Section -->
            <div class="mt-8 col-span-1 md:col-span-2">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('messages.business_hours') }}</h3>
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md border border-gray-200 dark:border-gray-600">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                            <div class="flex flex-col space-y-2 p-3 bg-white dark:bg-gray-800 rounded-md shadow-sm">
                                <div class="flex items-center justify-between">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="days_open[{{ $day }}]" value="1" class="form-checkbox h-5 w-5 text-indigo-600 dark:text-indigo-400" checked>
                                        <span class="ml-2 text-gray-700 dark:text-gray-300 capitalize">{{ __('messages.' . $day) }}</span>
                                    </label>
                                    <span class="text-xs text-gray-500 dark:text-gray-400" id="{{ $day }}_status">{{ __('messages.open') }}</span>
                                </div>
                                <div class="grid grid-cols-2 gap-2 mt-2" id="{{ $day }}_hours">
                                    <div>
                                        <label for="{{ $day }}_open" class="block text-xs font-medium text-gray-700 dark:text-gray-300">{{ __('messages.opening_time') }}</label>
                                        <input type="time" name="opening_hours[{{ $day }}][open]" id="{{ $day }}_open" value="09:00" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white bg-white rounded-md shadow-sm focus:outline-none sm:text-sm">
                                    </div>
                                    <div>
                                        <label for="{{ $day }}_close" class="block text-xs font-medium text-gray-700 dark:text-gray-300">{{ __('messages.closing_time') }}</label>
                                        <input type="time" name="opening_hours[{{ $day }}][close]" id="{{ $day }}_close" value="17:00" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white bg-white rounded-md shadow-sm focus:outline-none sm:text-sm">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- License Upload Section -->
            <div class="mt-8 border-t border-gray-200 dark:border-gray-600 pt-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('messages.branch_license_information') }}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">{{ __('messages.branch_license_requirement_description') }}</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- License Start Date -->
                    <div class="form-input-container has-label">
                        <label for="license_start_date" class="form-label">{{ __('messages.license_start_date') }} <span class="text-red-500">*</span></label>
                        <input type="date" name="license_start_date" id="license_start_date" value="{{ old('license_start_date') }}" class="form-input" required>
                        <svg class="validation-icon valid-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <svg class="validation-icon error-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        @error('license_start_date')
                            <p class="form-error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- License End Date -->
                    <div class="form-input-container has-label">
                        <label for="license_end_date" class="form-label">{{ __('messages.license_end_date') }} <span class="text-red-500">*</span></label>
                        <input type="date" name="license_end_date" id="license_end_date" value="{{ old('license_end_date') }}" class="form-input" required>
                        <svg class="validation-icon valid-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <svg class="validation-icon error-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        @error('license_end_date')
                            <p class="form-error-message">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- License File Upload -->
                <div class="mt-6">
                    <label for="license_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.branch_license_document') }} <span class="text-red-500">*</span></label>
                    <div class="mt-1">
                        <div class="license-upload-area border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center cursor-pointer hover:border-gray-400 dark:hover:border-gray-500 transition-colors" onclick="document.getElementById('license_file').click()">
                            <div id="license-upload-content">
                                <svg class="w-8 h-8 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <p class="text-gray-600 dark:text-gray-400 mb-2">
                                    <span class="font-medium text-indigo-600 dark:text-indigo-400 cursor-pointer">{{ __('messages.click_to_upload') }}</span> {{ __('messages.or_drag_and_drop') }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.pdf_files_only_max_10mb') }}</p>
                            </div>
                            <div id="license-preview" class="hidden">
                                <svg class="w-8 h-8 text-green-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p id="license-file-name" class="text-sm text-gray-600 dark:text-gray-300 font-medium mb-2"></p>
                                <p id="license-file-size" class="text-xs text-gray-500 dark:text-gray-400 mb-2"></p>
                                <button type="button" onclick="removeLicenseFile(event)" class="text-sm text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">{{ __('messages.remove') }}</button>
                            </div>
                            <input type="file" class="hidden" accept=".pdf" id="license_file" name="license_file" required>
                        </div>
                    </div>
                    @error('license_file')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- License Requirements Notice -->
                <div class="mt-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                <strong>{{ __('messages.important') }}:</strong> {{ __('messages.branch_license_requirements_notice') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end">
                <a href="{{ route('vendor.branches.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-4">
                    {{ __('messages.cancel') }}
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    {{ __('messages.create_branch') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Global error handler for Google Maps authentication
    window.gm_authFailure = function() {
        console.error('Google Maps authentication failed. Please check your API key.');
        const mapElement = document.getElementById('map');
        if (mapElement) {
            mapElement.innerHTML = '<div style="padding: 20px; text-align: center; color: red; background: #fee; border: 1px solid #fcc; border-radius: 8px;">Google Maps authentication failed. Please check your API key.</div>';
        }
    };

    // Global error handler for script loading
    function handleGoogleMapsError() {
        console.error('Failed to load Google Maps API script');
        const mapElement = document.getElementById('map');
        if (mapElement) {
            mapElement.innerHTML = '<div style="padding: 20px; text-align: center; color: red; background: #fee; border: 1px solid #fcc; border-radius: 8px;">Failed to load Google Maps. Please check your internet connection and try again.</div>';
        }
    }

  
</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('googlemaps.api_key') }}&libraries=places&callback=initMap" async defer onerror="handleGoogleMapsError()"></script>
<script>
    // Google Maps functionality
    let map;
    let marker;
    let searchBox;

    function initMap() {
        // Default center (Dubai)
        const defaultLat = parseFloat(document.getElementById('lat').value) || 25.2048;
        const defaultLng = parseFloat(document.getElementById('lng').value) || 55.2708;
        const center = { lat: defaultLat, lng: defaultLng };

        // Create the map
        map = new google.maps.Map(document.getElementById('map'), {
            center: center,
            zoom: 12,
            styles: [
                { elementType: "geometry", stylers: [{ color: "#242f3e" }] },
                { elementType: "labels.text.stroke", stylers: [{ color: "#242f3e" }] },
                { elementType: "labels.text.fill", stylers: [{ color: "#746855" }] },
                {
                    featureType: "administrative.locality",
                    elementType: "labels.text.fill",
                    stylers: [{ color: "#d59563" }],
                },
                {
                    featureType: "poi",
                    elementType: "labels.text.fill",
                    stylers: [{ color: "#d59563" }],
                },
                {
                    featureType: "poi.park",
                    elementType: "geometry",
                    stylers: [{ color: "#263c3f" }],
                },
                {
                    featureType: "poi.park",
                    elementType: "labels.text.fill",
                    stylers: [{ color: "#6b9a76" }],
                },
                {
                    featureType: "road",
                    elementType: "geometry",
                    stylers: [{ color: "#38414e" }],
                },
                {
                    featureType: "road",
                    elementType: "geometry.stroke",
                    stylers: [{ color: "#212a37" }],
                },
                {
                    featureType: "road",
                    elementType: "labels.text.fill",
                    stylers: [{ color: "#9ca5b3" }],
                },
                {
                    featureType: "road.highway",
                    elementType: "geometry",
                    stylers: [{ color: "#746855" }],
                },
                {
                    featureType: "road.highway",
                    elementType: "geometry.stroke",
                    stylers: [{ color: "#1f2835" }],
                },
                {
                    featureType: "road.highway",
                    elementType: "labels.text.fill",
                    stylers: [{ color: "#f3d19c" }],
                },
                {
                    featureType: "transit",
                    elementType: "geometry",
                    stylers: [{ color: "#2f3948" }],
                },
                {
                    featureType: "transit.station",
                    elementType: "labels.text.fill",
                    stylers: [{ color: "#d59563" }],
                },
                {
                    featureType: "water",
                    elementType: "geometry",
                    stylers: [{ color: "#17263c" }],
                },
                {
                    featureType: "water",
                    elementType: "labels.text.fill",
                    stylers: [{ color: "#515c6d" }],
                },
                {
                    featureType: "water",
                    elementType: "labels.text.stroke",
                    stylers: [{ color: "#17263c" }],
                },
            ],
        });

        // Add initial marker
        marker = new google.maps.Marker({
            position: center,
            map: map,
            draggable: true,
            animation: google.maps.Animation.DROP
        });

        // Update lat/lng when marker is dragged
        marker.addListener('dragend', function() {
            const position = marker.getPosition();
            document.getElementById('lat').value = position.lat();
            document.getElementById('lng').value = position.lng();

            // Reverse geocode to update address
            reverseGeocode(position);
        });

        // Add click listener to map
        map.addListener('click', function(event) {
            marker.setPosition(event.latLng);
            document.getElementById('lat').value = event.latLng.lat();
            document.getElementById('lng').value = event.latLng.lng();

            // Reverse geocode to update address
            reverseGeocode(event.latLng);
        });

        // Initialize search box
        const input = document.getElementById('pac-input');
        searchBox = new google.maps.places.SearchBox(input);

        // Bias the SearchBox results towards current map's viewport
        map.addListener('bounds_changed', function() {
            searchBox.setBounds(map.getBounds());
        });

        // Listen for the event fired when the user selects a prediction and retrieve
        // more details for that place
        searchBox.addListener('places_changed', function() {
            const places = searchBox.getPlaces();

            if (places.length === 0) {
                return;
            }

            // For each place, get the location and update the marker
            const bounds = new google.maps.LatLngBounds();
            places.forEach(function(place) {
                if (!place.geometry || !place.geometry.location) {
                    console.log("Returned place contains no geometry");
                    return;
                }

                // Update marker position
                marker.setPosition(place.geometry.location);

                // Update form fields
                document.getElementById('lat').value = place.geometry.location.lat();
                document.getElementById('lng').value = place.geometry.location.lng();
                document.getElementById('address').value = place.formatted_address || '';

                if (place.geometry.viewport) {
                    bounds.union(place.geometry.viewport);
                } else {
                    bounds.extend(place.geometry.location);
                }
            });
            map.fitBounds(bounds);
        });
    }

    // Reverse geocode a position to get address
    function reverseGeocode(position) {
        const geocoder = new google.maps.Geocoder();
        const addressField = document.getElementById('address');
        const loadingIndicator = document.getElementById('address-loading');

        // Show loading indicator
        if (loadingIndicator) {
            loadingIndicator.classList.remove('hidden');
        }

        // Clear current address
        addressField.value = 'Resolving address...';
        addressField.style.color = '#9CA3AF'; // Gray color for loading text

        geocoder.geocode({ location: position }, function(results, status) {
            // Hide loading indicator
            if (loadingIndicator) {
                loadingIndicator.classList.add('hidden');
            }

            if (status === 'OK' && results[0]) {
                // Success - update address field
                addressField.value = results[0].formatted_address;
                addressField.style.color = ''; // Reset to default color

                // Try to extract emirate from address components
                for (const component of results[0].address_components) {
                    if (component.types.includes('administrative_area_level_1')) {
                        const emirateSelect = document.getElementById('emirate');
                        const emirateName = component.long_name;

                        // Find matching option in select
                        for (let i = 0; i < emirateSelect.options.length; i++) {
                            if (emirateSelect.options[i].text.includes(emirateName)) {
                                emirateSelect.selectedIndex = i;
                                break;
                            }
                        }
                        break;
                    }
                }

                console.log('Address resolved successfully:', results[0].formatted_address);
            } else {
                // Error handling
                let errorMessage = 'Unable to resolve address';

                switch (status) {
                    case 'ZERO_RESULTS':
                        errorMessage = 'No address found for this location';
                        break;
                    case 'OVER_QUERY_LIMIT':
                        errorMessage = 'Address lookup limit exceeded. Please try again later.';
                        break;
                    case 'REQUEST_DENIED':
                        errorMessage = 'Address lookup denied. Please check API configuration.';
                        break;
                    case 'INVALID_REQUEST':
                        errorMessage = 'Invalid address lookup request';
                        break;
                    case 'UNKNOWN_ERROR':
                        errorMessage = 'Unknown error occurred during address lookup';
                        break;
                }

                addressField.value = errorMessage;
                addressField.style.color = '#EF4444'; // Red color for error

                console.error('Geocoding failed:', status, errorMessage);

                // Show user-friendly error notification
                if (typeof showNotification === 'function') {
                    showNotification('Address resolution failed: ' + errorMessage, 'error');
                }
            }
        });
    }

    // Business type backup function
    function updateBusinessTypeHidden(value) {
        const hiddenField = document.getElementById('business_type_hidden');
        if (hiddenField) {
            hiddenField.value = value;
        }
        console.log('Business type updated:', value);
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize business type backup on page load
        const businessTypeSelect = document.getElementById('business_type');
        if (businessTypeSelect && businessTypeSelect.value) {
            updateBusinessTypeHidden(businessTypeSelect.value);
        }

        // Add form submission debugging
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const businessType = document.getElementById('business_type').value;
                const businessTypeHidden = document.getElementById('business_type_hidden').value;
                console.log('Form submission - Business Type Select:', businessType);
                console.log('Form submission - Business Type Hidden:', businessTypeHidden);

                // Ensure business type is set before submission
                if (!businessType && businessTypeHidden) {
                    document.getElementById('business_type').value = businessTypeHidden;
                    console.log('Restored business type from hidden field:', businessTypeHidden);
                }
            });
        }

        // Initialize business hours functionality
        const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        days.forEach(day => {
            const checkbox = document.querySelector(`input[name="days_open[${day}]"]`);
            const hoursDiv = document.getElementById(`${day}_hours`);
            const statusSpan = document.getElementById(`${day}_status`);

            // Set initial state
            updateHoursVisibility(checkbox, hoursDiv, statusSpan);

            // Add event listener for changes
            checkbox.addEventListener('change', function() {
                updateHoursVisibility(this, hoursDiv, statusSpan);
            });
        });

        function updateHoursVisibility(checkbox, hoursDiv, statusSpan) {
            if (checkbox.checked) {
                hoursDiv.classList.remove('hidden');
                statusSpan.textContent = '{{ __('messages.open') }}';
                statusSpan.classList.remove('text-red-500');
                statusSpan.classList.add('text-green-500');
            } else {
                hoursDiv.classList.add('hidden');
                statusSpan.textContent = '{{ __('messages.closed') }}';
                statusSpan.classList.remove('text-green-500');
                statusSpan.classList.add('text-red-500');
            }
        }

        // Branch image toggle functionality
        const useCompanyImageCheckbox = document.getElementById('use_company_image');
        const branchImageContainer = document.getElementById('branch_image_container');

        useCompanyImageCheckbox.addEventListener('change', function() {
            if (this.checked) {
                branchImageContainer.classList.add('hidden');
            } else {
                branchImageContainer.classList.remove('hidden');
            }
        });

        // License date validation
        const startDateInput = document.getElementById('license_start_date');
        const endDateInput = document.getElementById('license_end_date');

        function validateLicenseDates() {
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);

            if (startDate && endDate && endDate <= startDate) {
                endDateInput.setCustomValidity('{{ __("messages.license_end_date_must_be_after_start_date") }}');
            } else {
                endDateInput.setCustomValidity('');
            }
        }

        startDateInput.addEventListener('change', validateLicenseDates);
        endDateInput.addEventListener('change', validateLicenseDates);

        // License file upload functionality
        document.getElementById('license_file').addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                const file = e.target.files[0];
                const fileName = file.name;
                const fileExtension = fileName.split('.').pop().toLowerCase();

                // Check if file is PDF
                if (fileExtension !== 'pdf') {
                    alert('{{ __("messages.pdf_files_only_message") }}');
                    e.target.value = ''; // Clear the file input
                    return;
                }

                // Check file size (10MB limit)
                const maxSize = 10 * 1024 * 1024; // 10MB
                if (file.size > maxSize) {
                    alert('{{ __("messages.file_too_large_10mb") }}');
                    e.target.value = ''; // Clear the file input
                    return;
                }

                // Show preview
                document.getElementById('license-upload-content').classList.add('hidden');
                document.getElementById('license-preview').classList.remove('hidden');
                document.getElementById('license-file-name').textContent = fileName;
                document.getElementById('license-file-size').textContent = formatFileSize(file.size);
            }
        });

        // License drag and drop functionality
        const licenseUploadArea = document.querySelector('.license-upload-area');

        licenseUploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('border-indigo-400', 'bg-indigo-50', 'dark:bg-indigo-900/20');
        });

        licenseUploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('border-indigo-400', 'bg-indigo-50', 'dark:bg-indigo-900/20');
        });

        licenseUploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('border-indigo-400', 'bg-indigo-50', 'dark:bg-indigo-900/20');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                document.getElementById('license_file').files = files;
                document.getElementById('license_file').dispatchEvent(new Event('change'));
            }
        });
    });

    function removeLicenseFile(event) {
        event.stopPropagation();
        document.getElementById('license_file').value = '';
        document.getElementById('license-upload-content').classList.remove('hidden');
        document.getElementById('license-preview').classList.add('hidden');
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Dynamic form state management
    document.addEventListener('DOMContentLoaded', function() {
        // Get all form inputs, selects, and textareas
        const formInputs = document.querySelectorAll('.form-input, .form-select, .form-textarea');
        
        formInputs.forEach(input => {
            const container = input.closest('.form-input-container');
            if (!container) return;

            // Handle initial state for inputs with values
            updateInputState(input, container);

            // Add event listeners for different input types
            if (input.type === 'text' || input.type === 'email' || input.type === 'tel' || input.type === 'date' || input.tagName === 'TEXTAREA') {
                input.addEventListener('input', () => updateInputState(input, container));
                input.addEventListener('focus', () => handleFocus(input, container));
                input.addEventListener('blur', () => handleBlur(input, container));
            } else if (input.tagName === 'SELECT') {
                input.addEventListener('change', () => updateInputState(input, container));
                input.addEventListener('focus', () => handleFocus(input, container));
                input.addEventListener('blur', () => handleBlur(input, container));
            }
        });

        function updateInputState(input, container) {
            const hasValue = input.value.trim() !== '';
            const isValid = input.checkValidity();
            const hasError = container.querySelector('.form-error-message');

            // Remove all state classes from input
            input.classList.remove('filled', 'valid', 'error', 'active');

            // Add appropriate state classes to input
            if (hasValue) {
                input.classList.add('filled');
                
                if (isValid && !hasError) {
                    input.classList.add('valid');
                }
            }

            if (hasError) {
                input.classList.add('error');
            }
        }

        function handleFocus(input, container) {
            input.classList.add('active');
        }

        function handleBlur(input, container) {
            input.classList.remove('active');
            updateInputState(input, container);
        }

        // Handle form submission validation
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                let hasErrors = false;

                formInputs.forEach(input => {
                    const container = input.closest('.form-input-container');
                    if (!container) return;

                    if (input.hasAttribute('required') && !input.value.trim()) {
                        input.classList.add('error');
                        hasErrors = true;
                    } else if (!input.checkValidity()) {
                        input.classList.add('error');
                        hasErrors = true;
                    } else {
                        input.classList.remove('error');
                    }
                });

                if (hasErrors) {
                    e.preventDefault();
                    // Scroll to first error
                    const firstError = document.querySelector('.form-input.error, .form-textarea.error, .form-select.error');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            });
        }
    });
</script>
@endsection
