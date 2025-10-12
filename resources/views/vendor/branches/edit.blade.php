@extends('layouts.dashboard')

@section('title', 'Edit Branch')
@section('page-title', 'Edit Branch')

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

    .dark .form-select {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%239ca3af' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
    }

    /* Responsive design */
    @media (max-width: 640px) {
        .form-input,
        .form-textarea,
        .form-select {
            font-size: 0.875rem;
            padding: 0.625rem 0.875rem;
        }
    }

    /* Animation for state transitions */
    .form-input,
    .form-textarea,
    .form-select {
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out, background-image 0.15s ease-in-out;
    }

    /* Modern input styling for better UX */
    .modern-input {
        position: relative;
    }

    .modern-input:focus-visible {
        outline: none;
    }
</style>
@endsection

@section('content')
<div class="container mx-auto">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ __('messages.edit_branch') }}</h2>
        <p class="mt-1 text-gray-600 dark:text-gray-400">{{ __('messages.update_branch_information') }}</p>
    </div>

    <!-- Branch Information Form -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700 mb-6">
        <div class="mb-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('messages.branch_information') }}</h3>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('messages.update_basic_branch_details') }}</p>
        </div>

        <form id="branch-info-form" action="{{ route('vendor.branches.update-info', $branch->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="form-input-container">
                    <label for="name" class="form-label">{{ __('messages.branch_name') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $branch->name) }}" class="form-input modern-input" required>
                    @error('name')
                        <p class="form-error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-input-container">
                    <label for="company_id" class="form-label">{{ __('messages.company') }} <span class="text-red-500">*</span></label>
                    <select id="company_id" name="company_id" class="form-select modern-input" required>
                        <option value="">{{ __('messages.select_company') }}</option>
                        @foreach($companies ?? [] as $company)
                            <option value="{{ $company->id }}" {{ old('company_id', $branch->company_id) == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                        @endforeach
                    </select> 
                    @error('company_id')
                        <p class="form-error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-input-container">
                    <label for="business_type" class="form-label">Business Type <span class="text-red-500">*</span></label>
                    <select id="business_type" name="business_type" class="form-select modern-input" required>
                        <option value="">Select Business Type</option>
                        @foreach($businessTypes ?? [] as $businessType)
                            <option value="{{ $businessType->business_name }}" {{ old('business_type', $branch->business_type) == $businessType->business_name ? 'selected' : '' }}>{{ $businessType->business_name }}</option>
                        @endforeach
                    </select>
                    @error('business_type')
                        <p class="form-error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-input-container">
                    <label for="phone" class="form-label">{{ __('messages.phone_number') }}</label>
                    <input type="tel" name="phone" id="phone" value="{{ old('phone', $branch->phone) }}" class="form-input modern-input">
                    @error('phone')
                        <p class="form-error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-input-container">
                    <label for="email" class="form-label">{{ __('messages.email_address') }}</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $branch->email) }}" class="form-input modern-input">
                    @error('email')
                        <p class="form-error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-span-1 md:col-span-2 form-input-container">
                    <label for="description" class="form-label">{{ __('messages.description') }}</label>
                    <textarea id="description" name="description" rows="3" class="form-textarea modern-input">{{ old('description', $branch->description) }}</textarea>
                    @error('description')
                        <p class="form-error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-span-1 md:col-span-2 form-input-container">
                    <label for="address" class="form-label">{{ __('messages.address') }} <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="text" name="address" id="address" value="{{ old('address', $branch->address) }}" class="form-input modern-input bg-gray-100 dark:bg-gray-600 cursor-not-allowed" readonly required>
                        <div id="address-loading" class="absolute right-3 top-1/2 transform -translate-y-1/2 hidden">
                            <svg class="animate-spin h-4 w-4 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('messages.address_auto_fill_hint') }}</p>
                    @error('address')
                        <p class="form-error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-input-container">
                    <label for="emirate" class="form-label">{{ __('messages.emirate') }} <span class="text-red-500">*</span></label>
                    <select id="emirate" name="emirate" class="form-select modern-input" required>
                        <option value="">{{ __('messages.select_emirate') }}</option>
                        <option value="Dubai" {{ old('emirate', $branch->emirate) == 'Dubai' ? 'selected' : '' }}>{{ __('messages.dubai') }}</option>
                        <option value="Abu Dhabi" {{ old('emirate', $branch->emirate) == 'Abu Dhabi' ? 'selected' : '' }}>{{ __('messages.abu_dhabi') }}</option>
                        <option value="Sharjah" {{ old('emirate', $branch->emirate) == 'Sharjah' ? 'selected' : '' }}>{{ __('messages.sharjah') }}</option>
                        <option value="Ajman" {{ old('emirate', $branch->emirate) == 'Ajman' ? 'selected' : '' }}>{{ __('messages.ajman') }}</option>
                        <option value="Umm Al Quwain" {{ old('emirate', $branch->emirate) == 'Umm Al Quwain' ? 'selected' : '' }}>{{ __('messages.umm_al_quwain') }}</option>
                        <option value="Ras Al Khaimah" {{ old('emirate', $branch->emirate) == 'Ras Al Khaimah' ? 'selected' : '' }}>{{ __('messages.ras_al_khaimah') }}</option>
                        <option value="Fujairah" {{ old('emirate', $branch->emirate) == 'Fujairah' ? 'selected' : '' }}>{{ __('messages.fujairah') }}</option>
                    </select>
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
                    <input type="hidden" name="lat" id="lat" value="{{ old('lat', $branch->lat) }}">
                    <input type="hidden" name="lng" id="lng" value="{{ old('lng', $branch->lng) }}">
                </div>

                <div class="form-input-container">
                    <label for="status" class="form-label">{{ __('messages.status') }}</label>
                    <select id="status" name="status" class="form-select modern-input">
                        <option value="active" {{ old('status', $branch->status) == 'active' ? 'selected' : '' }}>{{ __('messages.active') }}</option>
                        <option value="inactive" {{ old('status', $branch->status) == 'inactive' ? 'selected' : '' }}>{{ __('messages.inactive') }}</option>
                    </select>
                    @error('status')
                        <p class="form-error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-span-1 md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('messages.branch_image') }}</h3>
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md border border-gray-200 dark:border-gray-600">
                        <div class="flex flex-col space-y-4">
                            <div class="flex items-center">
                                <input type="checkbox" name="use_company_image" id="use_company_image" value="1" class="form-checkbox h-5 w-5 text-indigo-600 dark:text-indigo-400" {{ old('use_company_image', $branch->use_company_image) ? 'checked' : '' }}>
                                <label for="use_company_image" class="ml-2 text-gray-700 dark:text-gray-300">{{ __('messages.use_company_image') }}</label>
                            </div>

                            <div id="branch_image_container" class="{{ old('use_company_image', $branch->use_company_image) ? 'hidden' : '' }}">
                                @php
                                    // Get branch image using the accessor method
                                    $branchImage = !$branch->use_company_image ? $branch->getBranchImageAttribute() : null;
                                @endphp
                                @if($branchImage)
                                <div class="mb-3">
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('messages.current_branch_image') }}:</p>
                                    <img src="{{ $branchImage }}" alt="{{ $branch->name }}" class="w-40 h-40 object-cover rounded-md border border-gray-300 dark:border-gray-600">
                                </div>
                                @endif

                                <label for="branch_image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ $branch->branch_image ? __('messages.change_branch_image') : __('messages.upload_branch_image') }}</label>
                                <div class="mt-1 flex items-center">
                                    <input type="file" name="branch_image" id="branch_image" accept="image/jpeg,image/png,image/webp" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
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
                        @php
                            $openingHours = $branch->opening_hours ?? [];
                        @endphp

                        @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                            @php
                                $dayData = $openingHours[$day] ?? ['is_open' => true, 'open' => '09:00', 'close' => '17:00'];
                                $isOpen = $dayData['is_open'] ?? true;
                                $openTime = $dayData['open'] ?? '09:00';
                                $closeTime = $dayData['close'] ?? '17:00';
                            @endphp
                            <div class="flex flex-col space-y-2 p-3 bg-white dark:bg-gray-800 rounded-md shadow-sm">
                                <div class="flex items-center justify-between">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="days_open[{{ $day }}]" value="1" class="form-checkbox h-5 w-5 text-indigo-600 dark:text-indigo-400" {{ $isOpen ? 'checked' : '' }}>
                                        <span class="ml-2 text-gray-700 dark:text-gray-300 capitalize">{{ __('messages.' . $day) }}</span>
                                    </label>
                                    <span class="text-xs {{ $isOpen ? 'text-green-500' : 'text-red-500' }} dark:text-gray-400" id="{{ $day }}_status">{{ $isOpen ? __('messages.open') : __('messages.closed') }}</span>
                                </div>
                                <div class="grid grid-cols-2 gap-2 mt-2 {{ $isOpen ? '' : 'hidden' }}" id="{{ $day }}_hours">
                                    <div>
                                        <label for="{{ $day }}_open" class="block text-xs font-medium text-gray-700 dark:text-gray-300">{{ __('messages.opening_time') }}</label>
                                        <input type="time" name="opening_hours[{{ $day }}][open]" id="{{ $day }}_open" value="{{ $openTime }}" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label for="{{ $day }}_close" class="block text-xs font-medium text-gray-700 dark:text-gray-300">{{ __('messages.closing_time') }}</label>
                                        <input type="time" name="opening_hours[{{ $day }}][close]" id="{{ $day }}_close" value="{{ $closeTime }}" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Submit Button for Branch Information -->
            <div class="mt-6 flex items-center justify-end">
                <a href="{{ route('vendor.branches.show', $branch->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-4">
                    {{ __('messages.cancel') }}
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    {{ __('messages.update_branch_information') }}
                </button>
            </div>
        </form>
    </div>

    <!-- License Management Form -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
        <div class="mb-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('messages.license_management') }}</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('messages.update_license_documents_dates') }}</p>
        </div>

        <form id="license-form" action="{{ route('vendor.branches.update-license', $branch->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @php
                $currentLicense = $branch->licenses()->latest()->first();
            @endphp

                @php
                    $currentLicense = $branch->licenses()->latest()->first();
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-input-container">
                        <label for="license_start_date" class="form-label">
                            {{ __('messages.license_start_date') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="license_start_date" id="license_start_date"
                               value="{{ old('license_start_date', $currentLicense ? $currentLicense->start_date->format('Y-m-d') : '') }}"
                               class="form-input modern-input"
                               required>
                        @error('license_start_date')
                            <p class="form-error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-input-container">
                        <label for="license_end_date" class="form-label">
                            {{ __('messages.license_end_date') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="license_end_date" id="license_end_date"
                               value="{{ old('license_end_date', $currentLicense ? $currentLicense->end_date->format('Y-m-d') : '') }}"
                               class="form-input modern-input"
                               required>
                        @error('license_end_date')
                            <p class="form-error-message">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <label for="license_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('messages.branch_license_document') }} <span class="text-red-500">*</span>
                    </label>

                    @if($currentLicense && $currentLicense->license_file_path)
                        <div class="mt-2 mb-4 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-md">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="ml-2 text-sm text-blue-700 dark:text-blue-300">
                                    {{ __('messages.current_license_file') }}: {{ basename($currentLicense->license_file_path) }}
                                </span>
                                <span class="ml-2 text-xs text-blue-600 dark:text-blue-400">
                                    ({{ __('messages.status') }}: {{ ucfirst($currentLicense->status) }})
                                </span>
                            </div>
                        </div>
                    @endif

                    <div class="mt-2">
                        <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-md hover:border-gray-400 dark:hover:border-gray-500 transition-colors duration-200" id="license-upload-area">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600 dark:text-gray-400">
                                    <label for="license_file" class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                        <span>{{ __('messages.click_to_upload') }}</span>
                                        <input id="license_file" name="license_file" type="file" class="sr-only" accept=".pdf">
                                    </label>
                                    <p class="pl-1">{{ __('messages.or_drag_and_drop') }}</p>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('messages.pdf_files_only_max_10mb') }}</p>
                            </div>
                        </div>
                        <div id="license-file-preview" class="mt-2 hidden">
                            <div class="flex items-center p-2 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-md">
                                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                </svg>
                                <span id="license-file-name" class="ml-2 text-sm text-green-700 dark:text-green-300"></span>
                                <span id="license-file-size" class="ml-2 text-xs text-green-600 dark:text-green-400"></span>
                                <button type="button" id="remove-license-file" class="ml-auto text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-200">
                                    {{ __('messages.remove') }}
                                </button>
                            </div>
                        </div>
                    </div>
                    @error('license_file')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-md">
                    <div class="flex">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700 dark:text-yellow-300">
                                <strong>{{ __('messages.important') }}:</strong> {{ __('messages.license_update_notice') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button for License -->
            <div class="mt-6 flex items-center justify-end">
                <a href="{{ route('vendor.branches.show', $branch->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-4">
                    {{ __('messages.cancel') }}
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    {{ __('messages.update_license') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Modern Form State Management
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize all form inputs
        const inputs = document.querySelectorAll('.modern-input');
        
        inputs.forEach(input => {
            // Set initial state
            updateInputState(input);
            
            // Add event listeners
            input.addEventListener('focus', () => handleFocus(input));
            input.addEventListener('blur', () => handleBlur(input));
            input.addEventListener('input', () => updateInputState(input));
            input.addEventListener('change', () => updateInputState(input));
        });

        function updateInputState(input) {
            const container = input.closest('.form-input-container');
            if (!container) return;

            // Remove all state classes
            container.classList.remove('filled', 'valid', 'error', 'active');

            // Check if input has value (filled state)
            if (input.value && input.value.trim() !== '') {
                container.classList.add('filled');
            }

            // Check validity
            if (input.checkValidity && input.value) {
                if (input.checkValidity()) {
                    container.classList.add('valid');
                } else {
                    container.classList.add('error');
                }
            }

            // Check for existing Laravel validation errors
            const errorMessage = container.querySelector('.form-error-message');
            if (errorMessage && errorMessage.textContent.trim() !== '') {
                container.classList.add('error');
            }
        }

        function handleFocus(input) {
            const container = input.closest('.form-input-container');
            if (container) {
                container.classList.add('active');
            }
        }

        function handleBlur(input) {
            const container = input.closest('.form-input-container');
            if (container) {
                container.classList.remove('active');
                updateInputState(input);
            }
        }

        // Handle form submission validation
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                let hasErrors = false;
                const formInputs = form.querySelectorAll('.modern-input');
                
                formInputs.forEach(input => {
                    updateInputState(input);
                    const container = input.closest('.form-input-container');
                    if (container && container.classList.contains('error')) {
                        hasErrors = true;
                    }
                });

                if (hasErrors) {
                    // Scroll to first error
                    const firstError = form.querySelector('.form-input-container.error');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            });
        });
    });

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
        // Get existing lat/lng values
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

    document.addEventListener('DOMContentLoaded', function() {
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
                statusSpan.textContent = '{{ __('open') }}';
                statusSpan.classList.remove('text-red-500');
                statusSpan.classList.add('text-green-500');
            } else {
                hoursDiv.classList.add('hidden');
                statusSpan.textContent = '{{ __('closed') }}';
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

        // License file upload functionality
        const licenseFileInput = document.getElementById('license_file');
        const licenseUploadArea = document.getElementById('license-upload-area');
        const licenseFilePreview = document.getElementById('license-file-preview');
        const licenseFileName = document.getElementById('license-file-name');
        const licenseFileSize = document.getElementById('license-file-size');
        const removeLicenseFileBtn = document.getElementById('remove-license-file');

        // Handle file input change
        licenseFileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                displayLicenseFile(file);
            }
        });

        // Handle drag and drop
        licenseUploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            licenseUploadArea.classList.add('border-indigo-500', 'bg-indigo-50', 'dark:bg-indigo-900/20');
        });

        licenseUploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            licenseUploadArea.classList.remove('border-indigo-500', 'bg-indigo-50', 'dark:bg-indigo-900/20');
        });

        licenseUploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            licenseUploadArea.classList.remove('border-indigo-500', 'bg-indigo-50', 'dark:bg-indigo-900/20');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                const file = files[0];
                if (file.type === 'application/pdf') {
                    licenseFileInput.files = files;
                    displayLicenseFile(file);
                } else {
                    alert('{{ __('messages.please_select_pdf_file') }}');
                }
            }
        });

        // Remove file functionality
        removeLicenseFileBtn.addEventListener('click', function() {
            licenseFileInput.value = '';
            licenseFilePreview.classList.add('hidden');
        });

        function displayLicenseFile(file) {
            licenseFileName.textContent = file.name;
            licenseFileSize.textContent = formatFileSize(file.size);
            licenseFilePreview.classList.remove('hidden');
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // AJAX Form Handling
        function handleFormSubmission(formId, successMessage) {
            const form = document.getElementById(formId);
            if (!form) return;

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(form);
                const submitButton = form.querySelector('button[type="submit"]');
                const originalText = submitButton ? submitButton.textContent : 'Submit';

                // Show loading state
                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.textContent = 'Updating...';
                }

                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                const headers = {
                    'X-Requested-With': 'XMLHttpRequest'
                };

                if (csrfToken) {
                    headers['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
                }

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: headers
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showSuccessModal(data.message);
                        // Optionally reload the page after a delay
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    } else {
                        showErrorModal(data.message || 'An error occurred while updating.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showErrorModal('An unexpected error occurred. Please try again.');
                })
                .finally(() => {
                    // Reset button state
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.textContent = originalText;
                    }
                });
            });
        }

        // Initialize form handlers
        handleFormSubmission('branch-info-form', 'Branch information updated successfully.');
        handleFormSubmission('license-form', 'License updated successfully.');

        // Form validation functionality
        function initializeFormValidation() {
            const formInputs = document.querySelectorAll('.form-input, .form-textarea, .form-select');
            
            formInputs.forEach(input => {
                const container = input.closest('.form-input-container');
                if (!container) return;

                // Add validation icons if they don't exist (only for error state)
                if (!container.querySelector('.validation-icon')) {
                    const errorIcon = document.createElement('span');
                    errorIcon.className = 'validation-icon error-icon';
                    errorIcon.innerHTML = '✗';
                    
                    container.appendChild(errorIcon);
                }

                // Add validation text container if it doesn't exist (only for error messages)
                if (!container.querySelector('.validation-text')) {
                    const validationText = document.createElement('div');
                    validationText.className = 'validation-text text-sm mt-1 opacity-0 transition-opacity duration-200';
                    container.appendChild(validationText);
                }

                // Add event listeners
                input.addEventListener('input', () => validateField(input));
                input.addEventListener('blur', () => validateField(input));
            });
        }

        function validateField(input) {
            const container = input.closest('.form-input-container');
            const validationText = container.querySelector('.validation-text');
            const value = input.value.trim();
            const isRequired = input.hasAttribute('required');
            
            // Remove existing validation classes
            input.classList.remove('valid', 'error');
            container.classList.remove('valid', 'error');
            
            if (validationText) {
                validationText.classList.add('opacity-0');
                validationText.textContent = '';
            }

            // Check if field is valid
            let isValid = true;
            let errorMessage = '';

            if (isRequired && !value) {
                isValid = false;
                errorMessage = 'This field is required';
            } else if (value) {
                // Additional validation based on input type
                const inputType = input.type || input.tagName.toLowerCase();
                
                switch (inputType) {
                    case 'email':
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!emailRegex.test(value)) {
                            isValid = false;
                            errorMessage = 'Please enter a valid email address';
                        }
                        break;
                    case 'tel':
                        const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
                        if (!phoneRegex.test(value.replace(/\s/g, ''))) {
                            isValid = false;
                            errorMessage = 'Please enter a valid phone number';
                        }
                        break;
                    case 'url':
                        try {
                            new URL(value);
                        } catch {
                            isValid = false;
                            errorMessage = 'Please enter a valid URL';
                        }
                        break;
                }

                // Check minimum length
                const minLength = input.getAttribute('minlength');
                if (minLength && value.length < parseInt(minLength)) {
                    isValid = false;
                    errorMessage = `Minimum ${minLength} characters required`;
                }
            }

            // Apply validation state
            if (value && isValid) {
                input.classList.add('valid');
                container.classList.add('valid');
                // No text or icon for valid state - just the green border styling
            } else if (!isValid) {
                input.classList.add('error');
                container.classList.add('error');
                if (validationText) {
                    validationText.textContent = errorMessage;
                    validationText.className = 'validation-text text-sm mt-1 opacity-100 transition-opacity duration-200 text-red-600 dark:text-red-400';
                }
            }
        }

        // Initialize form validation
        initializeFormValidation();

        // Modal functions
        function showSuccessModal(message) {
            const modal = createModal('Success', message, 'success');
            document.body.appendChild(modal);
            modal.style.display = 'flex';
        }

        function showErrorModal(message) {
            const modal = createModal('Error', message, 'error');
            document.body.appendChild(modal);
            modal.style.display = 'flex';
        }

        function createModal(title, message, type) {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
            modal.style.display = 'none';

            const iconColor = type === 'success' ? 'text-green-400' : 'text-red-400';
            const icon = type === 'success'
                ? '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>'
                : '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>';

            modal.innerHTML = `
                <div class="relative top-20 mx-auto p-5 border h-64 w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
                    <div class="mt-3 text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-${type === 'success' ? 'green' : 'red'}-100 dark:bg-${type === 'success' ? 'green' : 'red'}-900">
                            <svg class="h-6 w-6 ${iconColor}" fill="currentColor" viewBox="0 0 20 20">
                                ${icon}
                            </svg>
                        </div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mt-2">${title}</h3>
                        <div class="mt-2 px-7 py-3">
                            <p class="text-sm text-gray-500 dark:text-gray-400">${message}</p>
                        </div>
                        <div class="items-center px-4 py-3">
                            <button onclick="this.closest('.fixed').remove()" class="px-4 py-2 bg-${type === 'success' ? 'green' : 'red'}-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-${type === 'success' ? 'green' : 'red'}-600 focus:outline-none focus:ring-2 focus:ring-${type === 'success' ? 'green' : 'red'}-300">
                                OK
                            </button>
                        </div>
                    </div>
                </div>
            `;

            // Close modal when clicking outside
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.remove();
                }
            });

            return modal;
        }
    });
</script>
@endsection
