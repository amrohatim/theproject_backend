@extends('layouts.dashboard')

@section('title', __('messages.company_management'))
@section('page-title',  __('messages.company_management'))

@section('styles')
<style>
    /* Custom Form Input Styles - Pixel Perfect Figma Design */
    .form-input-container {
        position: relative;
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        font-size: 14px;
        font-weight: 500;
        color: #374151;
        margin-bottom: 8px;
        line-height: 1.4;
    }

    .form-input, .form-select, .form-textarea {
        width: 100%;
        padding: 12px 16px;
        border: 1.5px solid #D1D5DB;
        border-radius: 8px;
        font-size: 14px;
        line-height: 1.5;
        color: #111827;
        background-color: #FFFFFF;
        transition: all 0.2s ease-in-out;
        outline: none;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }

    .form-input::placeholder, .form-textarea::placeholder {
        color: #9CA3AF;
        font-weight: 400;
    }

    /* Normal State - Default styling above */

    /* Active State - When focused/clicked */
    .form-input:focus, .form-input.active,
    .form-select:focus, .form-select.active,
    .form-textarea:focus, .form-textarea.active {
        border-color: #3B82F6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        background-color: #FFFFFF;
    }

    /* Filled State - When user has entered content */
    .form-input.filled, .form-select.filled, .form-textarea.filled {
        border-color: #6B7280;
        background-color: #F9FAFB;
    }

    /* Valid State - When input passes validation */
    .form-input.valid, .form-select.valid, .form-textarea.valid {
        border-color: #10B981;
        background-color: #F0FDF4;
        padding-right: 48px;
    }

    /* Error State - When input has validation errors */
    .form-input.error, .form-select.error, .form-textarea.error {
        border-color: #EF4444;
        background-color: #FEF2F2;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }

    /* Validation Icon */
    .validation-icon {
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        width: 16px;
        height: 16px;
        display: none;
    }

    .validation-icon::after {
        content: '✓';
        color: #10B981;
        font-weight: bold;
        font-size: 14px;
    }

    /* Error Message */
    .error-message {
        color: #EF4444;
        font-size: 12px;
        margin-top: 4px;
        display: none;
    }

    /* Select Specific Styling */
    .form-select {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 12px center;
        background-repeat: no-repeat;
        background-size: 16px 12px;
        padding-right: 40px;
        appearance: none;
    }

    /* Textarea Specific Styling */
    .form-textarea {
        resize: vertical;
        min-height: 80px;
    }

    /* Dark Mode Support */
    .dark .form-label {
        color: #D1D5DB;
    }

    .dark .form-input, .dark .form-select, .dark .form-textarea {
        background-color: #374151;
        border-color: #4B5563;
        color: #F9FAFB;
    }

    .dark .form-input::placeholder, .dark .form-textarea::placeholder {
        color: #6B7280;
    }

    .dark .form-input:focus, .dark .form-input.active,
    .dark .form-select:focus, .dark .form-select.active,
    .dark .form-textarea:focus, .dark .form-textarea.active {
        background-color: #374151;
        border-color: #3B82F6;
    }

    .dark .form-input.filled, .dark .form-select.filled, .dark .form-textarea.filled {
        background-color: #4B5563;
        border-color: #6B7280;
    }

    .dark .form-input.valid, .dark .form-select.valid, .dark .form-textarea.valid {
        background-color: #064E3B;
        border-color: #10B981;
    }

    .dark .form-input.error, .dark .form-select.error, .dark .form-textarea.error {
        background-color: #7F1D1D;
        border-color: #EF4444;
    }
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get all form inputs, selects, and textareas
    const formElements = document.querySelectorAll('.form-input, .form-select, .form-textarea');
    
    formElements.forEach(element => {
        // Add event listeners for focus, blur, and input events
        element.addEventListener('focus', function() {
            this.classList.add('active');
        });
        
        element.addEventListener('blur', function() {
            this.classList.remove('active');
            
            // Add filled class if element has value
            if (this.value.trim() !== '') {
                this.classList.add('filled');
                
                // Validate the input and add valid class if valid
                if (validateInput(this)) {
                    this.classList.add('valid');
                    this.classList.remove('error');
                } else {
                    this.classList.remove('valid');
                    this.classList.add('error');
                }
            } else {
                this.classList.remove('filled', 'valid');
            }
        });
        
        element.addEventListener('input', function() {
            // Add filled class if element has value
            if (this.value.trim() !== '') {
                this.classList.add('filled');
                
                // Real-time validation
                if (validateInput(this)) {
                    this.classList.add('valid');
                    this.classList.remove('error');
                } else {
                    this.classList.remove('valid');
                    if (this.value.trim() !== '') {
                        this.classList.add('error');
                    }
                }
            } else {
                this.classList.remove('filled', 'valid', 'error');
            }
        });
        
        // Initialize state for elements with existing values
        if (element.value.trim() !== '') {
            element.classList.add('filled');
            if (validateInput(element)) {
                element.classList.add('valid');
            }
        }
    });
    
    // Validation function
    function validateInput(element) {
        const value = element.value.trim();
        const type = element.type;
        const name = element.name;
        
        if (value === '') return false;
        
        switch (type) {
            case 'email':
                return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
            case 'tel':
                return /^[\+]?[1-9][\d]{0,15}$/.test(value.replace(/\s/g, ''));
            case 'url':
                try {
                    new URL(value);
                    return true;
                } catch {
                    return false;
                }
            default:
                if (name === 'company_name') {
                    return value.length >= 2;
                }
                if (name === 'business_type') {
                    return value !== '';
                }
                if (name === 'description') {
                    return value.length >= 10;
                }
                return true;
        }
    }
    
    // Form submission handling
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            let hasErrors = false;
            
            formElements.forEach(element => {
                if (element.hasAttribute('required') && element.value.trim() === '') {
                    element.classList.add('error');
                    hasErrors = true;
                } else if (element.value.trim() !== '' && !validateInput(element)) {
                    element.classList.add('error');
                    hasErrors = true;
                } else {
                    element.classList.remove('error');
                }
            });
            
            if (hasErrors) {
                e.preventDefault();
                // Scroll to first error
                const firstError = document.querySelector('.form-input.error, .form-select.error, .form-textarea.error');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstError.focus();
                }
            }
        });
    }
});
</script>
@endsection

@section('content')
<div class="container mx-auto">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ __('messages.company_management') }}</h2>
        <p class="mt-1 text-gray-600 dark:text-gray-400">{{ __('messages.manage_your_company_information_and_settings') }}</p>
    </div>

    <!-- Company Information -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 border border-gray-200 dark:border-gray-700">
        <div class="md:grid md:grid-cols-3 md:gap-6">
            <div class="md:col-span-1 p-6 border-r border-gray-200 dark:border-gray-700">
                <div class="px-4 sm:px-0">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">{{ __('messages.company_information') }}</h3>
                    <p class="mt-1 text-gray-600 dark:text-gray-400">
                        {{ __('messages.company_info_description') }}
                    </p>
                </div>
            </div>
            <div class="mt-5 md:mt-0 md:col-span-2 p-6">
                <form action="{{ route('vendor.company.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-6 gap-6">
                        <div class="col-span-6 sm:col-span-4">
                            <div class="form-input-container">
                                <label for="name" class="form-label">{{ __('messages.company_name') }}</label>
                                <input type="text" name="name" id="name" value="{{ $company->name ?? old('name') }}" class="form-input @error('name') error @enderror" placeholder="Enter company name" required>
                                <div class="validation-icon"></div>
                                @error('name')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-span-6 sm:col-span-3">
                            <div class="form-input-container">
                                <label for="business_type" class="form-label">{{ __('messages.business_type') }}</label>
                                <select id="business_type" name="business_type" class="form-select @error('business_type') error @enderror" required>
                                    <option value="">Select Business Type</option>
                                    <option value="retail" {{ ($company->business_type ?? old('business_type')) == 'retail' ? 'selected' : '' }}>Retail</option>
                                    <option value="service" {{ ($company->business_type ?? old('business_type')) == 'service' ? 'selected' : '' }}>Service</option>
                                    <option value="manufacturing" {{ ($company->business_type ?? old('business_type')) == 'manufacturing' ? 'selected' : '' }}>Manufacturing</option>
                                    <option value="food" {{ ($company->business_type ?? old('business_type')) == 'food' ? 'selected' : '' }}>Food & Beverage</option>
                                    <option value="technology" {{ ($company->business_type ?? old('business_type')) == 'technology' ? 'selected' : '' }}>Technology</option>
                                    <option value="other" {{ ($company->business_type ?? old('business_type')) == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                <div class="validation-icon"></div>
                                @error('business_type')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- <div class="col-span-6 sm:col-span-3">
                            <label for="registration_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.registration_number') }}</label>
                            <input type="text" name="registration_number" id="registration_number" value="{{ $company->registration_number ?? old('registration_number') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                        </div> --}}

                        <div class="col-span-6 sm:col-span-4">
                            <div class="form-input-container">
                                <label for="email" class="form-label">{{ __('messages.email') }}</label>
                                <input type="email" name="email" id="email" value="{{ $company->email ?? old('email') }}" class="form-input @error('email') error @enderror" placeholder="Enter email address" required>
                                <div class="validation-icon"></div>
                                @error('email')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-span-6 sm:col-span-3">
                            <div class="form-input-container">
                                <label for="phone" class="form-label">{{ __('messages.phone') }}</label>
                                <input type="tel" name="phone" id="phone" value="{{ $company->phone ?? old('phone') }}" class="form-input @error('phone') error @enderror" placeholder="Enter phone number" required>
                                <div class="validation-icon"></div>
                                @error('phone')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-span-6 sm:col-span-3">
                            <div class="form-input-container">
                                <label for="website" class="form-label">{{ __('messages.website') }}</label>
                                <input type="url" name="website" id="website" value="{{ $company->website ?? old('website') }}" class="form-input @error('website') error @enderror" placeholder="Enter website URL">
                                <div class="validation-icon"></div>
                                @error('website')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-span-6 sm:col-span-3">
                            <label for="can_deliver" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.delivery_capability') }}</label>
                            <select id="can_deliver" name="can_deliver" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="1" {{ ($company->can_deliver ?? old('can_deliver', '1')) == '1' || ($company->can_deliver ?? old('can_deliver', true)) === true ? 'selected' : '' }}>{{ __('messages.yes_we_can_handle_our_own_deliveries') }}</option>
                                <option value="0" {{ ($company->can_deliver ?? old('can_deliver')) == '0' || ($company->can_deliver ?? old('can_deliver')) === false ? 'selected' : '' }}>{{ __('messages.no_we_need_a_third_party_delivery_service') }}</option>
                            </select>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ __('messages.can_deliver_description') }}</p>
                        </div>

                        <div class="col-span-6">
                            <div class="form-input-container">
                                <label for="description" class="form-label">{{ __('messages.description') }}</label>
                                <textarea id="description" name="description" rows="3" class="form-textarea @error('description') error @enderror">{{ $company->description ?? old('description') }}</textarea>
                                <div class="validation-icon"></div>
                                @error('description')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ __('messages.brief_description_of_your_company_for_your_customers') }}</p>
                        </div>

                        <div class="col-span-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.company_logo') }}</label>
                            <div class="mt-1 flex items-center">
                                @if(isset($company) && $company->logo)
                                    @php
                                        // Check both storage URL and direct public path
                                        $storageUrl = $company->logo;
                                        $imagePath = 'images/companies/' . basename($company->logo ?? '');
                                        $fileExistsInPublic = file_exists(public_path($imagePath));
                                        $fileExistsInStorage = file_exists(storage_path('app/public/companies/' . basename($company->logo ?? '')));

                                        // Log for debugging
                                        \Illuminate\Support\Facades\Log::info('Company logo display check', [
                                            'company_id' => $company->id,
                                            'logo_path' => $company->logo,
                                            'storage_path' => storage_path('app/public/companies/' . basename($company->logo ?? '')),
                                            'public_path' => public_path($imagePath),
                                            'exists_in_storage' => $fileExistsInStorage,
                                            'exists_in_public' => $fileExistsInPublic
                                        ]);
                                    @endphp
                                    <div class="mr-4">
                                        @if($fileExistsInPublic)
                                            <img src="/{{ $imagePath }}" alt="{{ $company->name }}" class="h-16 w-16 object-cover rounded-md">
                                        @elseif($fileExistsInStorage)
                                            <img src="{{ $storageUrl }}" alt="{{ $company->name }}" class="h-16 w-16 object-cover rounded-md">
                                        @else
                                            <img src="{{ $storageUrl }}" alt="{{ $company->name }}" class="h-16 w-16 object-cover rounded-md">
                                        @endif
                                    </div>
                                @else
                                    <div class="mr-4">
                                        <div class="h-16 w-16 rounded-md bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                            <i class="fas fa-building text-gray-400"></i>
                                        </div>
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-md">
                                        <div class="space-y-1 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div class="flex text-sm text-gray-600 dark:text-gray-400">
                                                <label for="logo" class="relative cursor-pointer bg-white dark:bg-gray-700 rounded-md font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                    <span>{{ __('messages.upload_file') }}</span>
                                                    <input id="logo" name="logo" type="file" class="sr-only" onchange="showFileName(this)">
                                                </label>
                                                <p class="pl-1" id="file-name">{{ __('messages.or_drag_drop') }}</p>
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ __('messages.file_format_limit') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <i class="fas fa-save mr-2"></i> {{ __('messages.save_company_info') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Address Information -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 border border-gray-200 dark:border-gray-700">
        <div class="md:grid md:grid-cols-3 md:gap-6">
            <div class="md:col-span-1 p-6 border-r border-gray-200 dark:border-gray-700">
                <div class="px-4 sm:px-0">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">{{ __('messages.address_information') }}</h3>
                    <p class="mt-1 text-gray-600 dark:text-gray-400">
                        {{ __('messages.address_info_description') }}
                    </p>
                </div>
            </div>
            <div class="mt-5 md:mt-0 md:col-span-2 p-6">
                <form action="{{ route('vendor.company.address.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-6 gap-6">
                        <div class="col-span-6">
                            <div class="form-input-container">
                                <label for="address" class="form-label">{{ __('messages.street_address') }}</label>
                                <input type="text" name="address" id="address" value="{{ $company->address ?? old('address') }}" class="form-input @error('address') error @enderror" placeholder="Enter street address">
                                <div class="validation-icon"></div>
                                @error('address')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-span-6 sm:col-span-6 lg:col-span-2">
                            <div class="form-input-container">
                                <label for="city" class="form-label">{{ __('messages.city') }}</label>
                                <input type="text" name="city" id="city" value="{{ $company->city ?? old('city') }}" class="form-input @error('city') error @enderror" placeholder="Enter city">
                                <div class="validation-icon"></div>
                                @error('city')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-span-6 sm:col-span-3 lg:col-span-2">
                            <div class="form-input-container">
                                <label for="state" class="form-label">{{ __('messages.state_province') }}</label>
                                <input type="text" name="state" id="state" value="{{ $company->state ?? old('state') }}" class="form-input @error('state') error @enderror" placeholder="Enter state/province">
                                <div class="validation-icon"></div>
                                @error('state')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- <div class="col-span-6 sm:col-span-3 lg:col-span-2">
                            <label for="postal_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.zip_postal_code') }}</label>
                            <input type="text" name="postal_code" id="postal_code" value="{{ $company->zip_code ?? old('postal_code') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                        </div> --}}

                        {{-- <div class="col-span-6 sm:col-span-3">
                            <label for="country" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.country') }}</label>
                            <select id="country" name="country" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Select Country</option>
                                <option value="US" {{ ($company->country ?? old('country')) == 'US' ? 'selected' : '' }}>United States</option>
                                <option value="CA" {{ ($company->country ?? old('country')) == 'CA' ? 'selected' : '' }}>Canada</option>
                                <option value="UK" {{ ($company->country ?? old('country')) == 'UK' ? 'selected' : '' }}>United Kingdom</option>
                                <option value="AU" {{ ($company->country ?? old('country')) == 'AU' ? 'selected' : '' }}>Australia</option>
                                <!-- Add more countries as needed -->
                            </select>
                        </div> --}}
                    </div>
                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <i class="fas fa-save mr-2"></i> {{ __('messages.save_address_info') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function showFileName(input) {
        if (input.files && input.files[0]) {
            document.getElementById('file-name').textContent = input.files[0].name;

            // Preview image if possible
            var reader = new FileReader();
            reader.onload = function(e) {
                // If there's an existing image, update it, otherwise create a new one
                var existingImg = document.querySelector('.mr-4 img');
                if (existingImg) {
                    existingImg.src = e.target.result;
                } else {
                    var imgContainer = document.createElement('div');
                    imgContainer.className = 'mr-4';

                    var img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'h-16 w-16 object-cover rounded-md';
                    img.alt = 'Company Logo Preview';

                    imgContainer.appendChild(img);
                    document.querySelector('.mt-1.flex.items-center').prepend(imgContainer);
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Address form field event listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Address form fields
        const addressFields = ['street_address', 'city', 'state'];
        
        addressFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                const container = field.closest('.form-input-container');
                
                // Focus event
                field.addEventListener('focus', function() {
                    container.classList.add('active');
                });
                
                // Blur event
                field.addEventListener('blur', function() {
                    container.classList.remove('active');
                    
                    // Add filled state if has value
                    if (this.value.trim() !== '') {
                        container.classList.add('filled');
                        
                        // Validate field
                        if (validateAddressField(this)) {
                            container.classList.add('valid');
                            container.classList.remove('error');
                        } else {
                            container.classList.add('error');
                            container.classList.remove('valid');
                        }
                    } else {
                        container.classList.remove('filled', 'valid', 'error');
                    }
                });
                
                // Input event for real-time validation
                field.addEventListener('input', function() {
                    const container = this.closest('.form-input-container');
                    
                    if (this.value.trim() !== '') {
                        container.classList.add('filled');
                        
                        if (validateAddressField(this)) {
                            container.classList.add('valid');
                            container.classList.remove('error');
                        } else {
                            container.classList.remove('valid');
                        }
                    } else {
                        container.classList.remove('filled', 'valid', 'error');
                    }
                });
                
                // Initialize state on page load
                if (field.value.trim() !== '') {
                    container.classList.add('filled');
                    if (validateAddressField(field)) {
                        container.classList.add('valid');
                    }
                }
            }
        });
    });
    
    // Address field validation function
    function validateAddressField(field) {
        const value = field.value.trim();
        
        switch(field.id) {
            case 'street_address':
                return value.length >= 5; // Minimum 5 characters for address
            case 'city':
                return value.length >= 2 && /^[a-zA-Z\s\-']+$/.test(value); // Letters, spaces, hyphens, apostrophes only
            case 'state':
                return value.length >= 2; // Minimum 2 characters for state/province
            default:
                return value.length > 0;
        }
    }
</script>
@endsection
