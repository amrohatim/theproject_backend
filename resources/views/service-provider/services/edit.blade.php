@extends('layouts.service-provider')

@section('title', __('messages.edit_service'))
@section('page-title', __('messages.edit_service'))

@section('content')
<div class="container mx-auto">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ __('messages.edit_service') }}</h2>
                <p class="mt-1 text-gray-600 dark:text-gray-400">{{ __('messages.update_service_information') }}</p>
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
        <form action="{{ route('service-provider.services.update', $service->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('messages.basic_information') }}</h3>

                    <!-- Service Name (Bilingual) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('messages.service_name') }} <span class="text-red-500">*</span></label>

                        <!-- Language Switcher for Service Name -->
                        <x-form-language-switcher field-name="service_name" />

                        <!-- English Service Name -->
                        <div data-language-field="service_name" data-language="en" class="active-language-field">
                            <input type="text" name="name" id="name" value="{{ old('name', $service->name) }}"
                                   class="mt-1 focus:ring-indigo-500 px-2 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md"
                                   placeholder="{{ __('messages.service_name_english') }}" required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Arabic Service Name -->
                        <div data-language-field="service_name" data-language="ar" style="display: none;">
                            <input type="text" name="service_name_arabic" id="service_name_arabic" value="{{ old('service_name_arabic', $service->service_name_arabic) }}"
                                   class="mt-1 focus:ring-indigo-500 pr-10 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md"
                                   placeholder="{{ __('messages.service_name_arabic') }}" dir="rtl" required>
                            @error('service_name_arabic')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.category') }} <span class="text-red-500">*</span></label>
                        <select id="category_id" name="category_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                            <option value="">{{ __('messages.select_category') }}</option>
                            @foreach($categories ?? [] as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $service->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Branch -->
                    <div>
                        <label for="branch_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.branch') }} <span class="text-red-500">*</span></label>
                        <select id="branch_id" name="branch_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                            <option value="">{{ __('messages.select_branch') }}</option>
                            @foreach($allBranches ?? [] as $branch)
                                @php
                                    $hasActiveLicense = $branch->hasActiveLicense();
                                    $licenseStatus = $branch->getLicenseStatus();
                                    $statusText = '';
                                    $isDisabled = !$hasActiveLicense;
                                    $isCurrentBranch = old('branch_id', $service->branch_id) == $branch->id;

                                    if (!$hasActiveLicense) {
                                        $statusText = match($licenseStatus) {
                                            'pending' => ' (License Pending)',
                                            'expired' => ' (License Expired)',
                                            'rejected' => ' (License Rejected)',
                                            default => ' (No Active License)'
                                        };
                                    }
                                @endphp
                                <option
                                    value="{{ $branch->id }}"
                                    {{ $isCurrentBranch ? 'selected' : '' }}
                                    {{ $isDisabled && !$isCurrentBranch ? 'disabled' : '' }}
                                    class="{{ $isDisabled && !$isCurrentBranch ? 'text-gray-400 bg-gray-100' : '' }}"
                                    title="{{ $isDisabled ? 'This branch has an inactive license' : '' }}"
                                >
                                    {{ $branch->name }}{{ $statusText }}
                                </option>
                            @endforeach
                        </select>
                        @error('branch_id')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror

                        <!-- License Status Information -->
                        @if(isset($allBranches) && $allBranches->count() > 0)
                            @php
                                $inactiveBranches = $allBranches->filter(function($branch) {
                                    return !$branch->hasActiveLicense();
                                });
                                $currentBranchInactive = !$allBranches->find($service->branch_id)?->hasActiveLicense();
                            @endphp
                            @if($currentBranchInactive)
                                <div class="mt-2 p-3 bg-red-50 border border-red-200 rounded-md">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-red-800">Current Branch License Issue</h3>
                                            <div class="mt-2 text-sm text-red-700">
                                                <p>This service is currently assigned to a branch with an inactive license. You can still edit the service, but consider moving it to a branch with an active license.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @elseif($inactiveBranches->count() > 0)
                                <div class="mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
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
                                                <ul class="list-disc list-inside mt-1">
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
                        @endif
                    </div>

                    <!-- Service Description (Bilingual) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('messages.description') }}</label>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">{{ __('messages.description_optional_both_or_none') }}</p>

                        <!-- Language Switcher for Description -->
                        <x-form-language-switcher field-name="service_description" />

                        <!-- English Description -->
                        <div data-language-field="service_description" data-language="en" class="active-language-field">
                            <textarea id="description" name="description" rows="4"
                                      class="mt-1 focus:ring-indigo-500 px-4 py-2 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md"
                                      >{{ old('description', $service->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Arabic Description -->
                        <div data-language-field="service_description" data-language="ar" style="display: none;">
                            <textarea id="service_description_arabic" name="service_description_arabic" rows="4"
                                      class="mt-1 focus:ring-indigo-500 px-4 py-2 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md"
                                       dir="rtl">{{ old('service_description_arabic', $service->service_description_arabic) }}</textarea>
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
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.price') }} <span class="text-red-500">*</span></label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input type="number" name="price" id="price" min="0" step="0.01" value="{{ old('price', $service->price) }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" placeholder="0.00" required onkeypress="return (event.charCode !=8 && event.charCode ==0 || (event.charCode >= 48 && event.charCode <= 57))" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');">
                        </div>
                        @error('price')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Duration -->
                    <div>
                        <label for="duration" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.duration_minutes') }} <span class="text-red-500">*</span></label>
                        <input type="number" 
                               name="duration" 
                               id="duration" 
                               min="1" 
                               value="{{ old('duration', $service->duration) }}" 
                               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" 
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

        // Initialize dynamic placeholder functionality

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

            if (hasErrors) {
                e.preventDefault();
                showValidationModal(errors);
                return false;
            }
        });
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
