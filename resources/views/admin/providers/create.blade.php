@extends('layouts.dashboard')

@section('title', 'Create Provider')
@section('page-title', 'Create Provider')

@section('styles')
<style>
    .form-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        position: relative;
    }
    
    .form-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #667eea, #764ba2, #f093fb, #f5576c);
        background-size: 400% 400%;
        animation: gradient 15s ease infinite;
    }
    
    @keyframes gradient {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    
    .form-section {
        background: white;
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
    }
    
    .form-section:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
    }
    
    .dark .form-section {
        background: rgba(31, 41, 55, 0.8);
        border-color: rgba(75, 85, 99, 0.3);
    }
    
    .section-header {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        border-bottom: 1px solid #e2e8f0;
        padding: 1.5rem;
        border-radius: 16px 16px 0 0;
    }
    
    .dark .section-header {
        background: linear-gradient(135deg, #374151 0%, #4b5563 100%);
        border-bottom-color: #4b5563;
    }
    
    .section-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1f2937;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .dark .section-title {
        color: #f9fafb;
    }
    
    .section-icon {
        width: 2.5rem;
        height: 2.5rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.125rem;
    }
    
    .form-group {
        position: relative;
        margin-bottom: 1.5rem;
    }
    
    .form-input {
        width: 100%;
        padding: 1rem 1rem 1rem 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: #f9fafb;
    }

    .form-input:focus {
        outline: none;
        border-color: #667eea;
        background: white;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .dark .form-input {
        background: #374151;
        border-color: #4b5563;
        color: #f9fafb;
    }

    .dark .form-input:focus {
        background: #4b5563;
        border-color: #667eea;
    }

    /* Hide placeholder when input has value or is focused */
    .form-input:not(:placeholder-shown)::placeholder,
    .form-input.has-value::placeholder,
    .form-input:focus::placeholder {
        opacity: 0;
        visibility: hidden;
    }

    /* Show placeholder only when input is empty and not focused */
    .form-input:placeholder-shown:not(:focus)::placeholder {
        opacity: 0.5;
        visibility: visible;
    }
    
    .form-label {
        position: absolute;
        left: 1rem;
        top: 1rem;
        color: #6b7280;
        font-size: 1rem;
        transition: all 0.3s ease;
        pointer-events: none;
        background: transparent;
        padding: 0 0.25rem;
    }
    
    .form-input:focus + .form-label,
    .form-input:not(:placeholder-shown) + .form-label,
    .form-input.has-value + .form-label,
    .form-group.focused .form-label {
        top: -0.5rem;
        left: 0.75rem;
        font-size: 0.875rem;
        color: #667eea;
        background: white;
        padding: 0 0.5rem;
    }

    .dark .form-input:focus + .form-label,
    .dark .form-input:not(:placeholder-shown) + .form-label,
    .dark .form-input.has-value + .form-label,
    .dark .form-group.focused .form-label {
        background: #1f2937;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        padding: 1rem 2rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
    }
    
    .btn-secondary {
        background: white;
        border: 2px solid #e5e7eb;
        color: #6b7280;
        padding: 1rem 2rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-secondary:hover {
        border-color: #d1d5db;
        background: #f9fafb;
        transform: translateY(-1px);
    }
    
    .dark .btn-secondary {
        background: #374151;
        border-color: #4b5563;
        color: #d1d5db;
    }
    
    .dark .btn-secondary:hover {
        background: #4b5563;
        border-color: #6b7280;
    }
    
    .file-upload-area {
        border: 2px dashed #d1d5db;
        border-radius: 12px;
        padding: 2rem;
        text-align: center;
        transition: all 0.3s ease;
        background: #f9fafb;
        cursor: pointer;
    }
    
    .file-upload-area:hover {
        border-color: #667eea;
        background: #f0f4ff;
    }
    
    .dark .file-upload-area {
        background: #374151;
        border-color: #4b5563;
    }
    
    .dark .file-upload-area:hover {
        border-color: #667eea;
        background: #4b5563;
    }
    
    .progress-steps {
        display: flex;
        justify-content: space-between;
        margin-bottom: 2rem;
        position: relative;
    }
    
    .progress-step {
        flex: 1;
        text-align: center;
        position: relative;
    }
    
    .progress-step::before {
        content: '';
        position: absolute;
        top: 1.25rem;
        left: 50%;
        right: -50%;
        height: 2px;
        background: #e5e7eb;
        z-index: 1;
    }
    
    .progress-step:last-child::before {
        display: none;
    }
    
    .progress-step.active::before {
        background: #667eea;
    }
    
    .step-circle {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        background: #e5e7eb;
        color: #6b7280;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 0.5rem;
        font-weight: 600;
        position: relative;
        z-index: 2;
        transition: all 0.3s ease;
    }
    
    .progress-step.active .step-circle {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }
    
    .error-message {
        color: #ef4444;
        font-size: 0.875rem;
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    .breadcrumb {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        border-radius: 12px;
        padding: 1rem 1.5rem;
        margin-bottom: 2rem;
        border: 1px solid #e2e8f0;
    }
    
    .dark .breadcrumb {
        background: linear-gradient(135deg, #374151 0%, #4b5563 100%);
        border-color: #4b5563;
    }
</style>
@endsection

@section('content')
<div class="max-w-6xl mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Create New Provider</h1>
            <p class="text-lg text-gray-600 dark:text-gray-400">Add a new provider to expand your marketplace</p>
        </div>
        <div class="mt-4 lg:mt-0">
            <a href="{{ route('admin.providers.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Back to Providers
            </a>
        </div>
    </div>

    <!-- Breadcrumb -->
    <nav class="breadcrumb">
        <ol class="flex items-center space-x-2 text-sm">
            <li><a href="{{ route('admin.dashboard') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 font-medium">Dashboard</a></li>
            <li><i class="fas fa-chevron-right text-gray-400 mx-2"></i></li>
            <li><a href="{{ route('admin.providers.index') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 font-medium">Providers</a></li>
            <li><i class="fas fa-chevron-right text-gray-400 mx-2"></i></li>
            <li class="text-gray-700 dark:text-gray-300 font-medium">Create</li>
        </ol>
    </nav>

    <!-- Progress Steps -->
    <div class="progress-steps">
        <div class="progress-step active">
            <div class="step-circle">1</div>
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Account Info</span>
        </div>
        <div class="progress-step">
            <div class="step-circle">2</div>
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Business Details</span>
        </div>
        <div class="progress-step">
            <div class="step-circle">3</div>
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Additional Info</span>
        </div>
    </div>

    <!-- Main Form Card -->
    <div class="form-card">
        <div class="bg-white dark:bg-gray-800 p-8">
            @if ($errors->any())
                <div class="mb-6 bg-red-50 dark:bg-red-900/30 border-l-4 border-red-500 rounded-r-lg p-4">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                        <h3 class="text-lg font-medium text-red-800 dark:text-red-200">Please fix the following errors:</h3>
                    </div>
                    <ul class="list-disc pl-6 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li class="text-red-700 dark:text-red-300">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form action="{{ route('admin.providers.store') }}" method="POST" enctype="multipart/form-data" id="providerForm">
                @csrf
                
                <!-- Step 1: User Account Information -->
                <div class="form-section mb-8" id="step-1">
                    <div class="section-header">
                        <h2 class="section-title">
                            <div class="section-icon">
                                <i class="fas fa-user"></i>
                            </div>
                            User Account Information
                        </h2>
                        <p class="text-gray-600 dark:text-gray-400 mt-2">Create the user account for this provider</p>
                    </div>
                    
                    <div class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="form-group">
                                <input type="text" class="form-input {{ old('name') ? 'has-value' : '' }}" id="name" name="name" value="{{ old('name') }}" placeholder="Enter full name" required>
                                {{-- <label for="name" class="form-label">Full Name <span class="text-red-500">*</span></label> --}}
                                @error('name')
                                    <div class="error-message">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <input type="email" class="form-input {{ old('email') ? 'has-value' : '' }}" id="email" name="email" value="{{ old('email') }}" placeholder="Enter email address" required>
                                {{-- <label for="email" class="form-label">Email Address <span class="text-red-500">*</span></label> --}}
                                @error('email')
                                    <div class="error-message">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <input type="tel" class="form-input {{ old('phone') ? 'has-value' : '' }}" id="phone" name="phone" value="{{ old('phone') }}" placeholder="Enter phone number">
                                {{-- <label for="phone" class="form-label">Phone Number</label> --}}
                                @error('phone')
                                    <div class="error-message">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <input type="password" class="form-input" id="password" name="password" placeholder="Enter password" required>
                                {{-- <label for="password" class="form-label">Password <span class="text-red-500">*</span></label> --}}
                                @error('password')
                                    <div class="error-message">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group lg:col-span-2">
                                <input type="password" class="form-input" id="password_confirmation" name="password_confirmation" placeholder="Confirm password" required>
                                {{-- <label for="password_confirmation" class="form-label">Confirm Password <span class="text-red-500">*</span></label> --}}
                            </div>
                        </div>
                        
                        <div class="flex justify-end mt-6">
                            <button type="button" class="btn-primary" onclick="nextStep(2)">
                                Next Step
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Business Information -->
                <div class="form-section mb-8 hidden" id="step-2">
                    <div class="section-header">
                        <h2 class="section-title">
                            <div class="section-icon">
                                <i class="fas fa-building"></i>
                            </div>
                            Business Information
                        </h2>
                        <p class="text-gray-600 dark:text-gray-400 mt-2">Provide details about the business</p>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="form-group">
                                <input type="text" class="form-input {{ old('business_name') ? 'has-value' : '' }}" id="business_name" name="business_name" value="{{ old('business_name') }}" placeholder="Enter business name" required>
                                {{-- <label for="business_name" class="form-label">Business Name <span class="text-red-500">*</span></label> --}}
                                @error('business_name')
                                    <div class="error-message">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <input type="text" class="form-input {{ old('business_type') ? 'has-value' : '' }}" id="business_type" name="business_type" value="{{ old('business_type') }}" placeholder="e.g., Technology, Retail, Healthcare">
                                {{-- <label for="business_type" class="form-label">Business Type</label> --}}
                                @error('business_type')
                                    <div class="error-message">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <input type="text" class="form-input {{ old('registration_number') ? 'has-value' : '' }}" id="registration_number" name="registration_number" value="{{ old('registration_number') }}" placeholder="Enter registration number">
                                {{-- <label for="registration_number" class="form-label">Registration Number</label> --}}
                                @error('registration_number')
                                    <div class="error-message">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <select class="form-input {{ old('status') ? 'has-value' : '' }}" id="status" name="status" required>
                                    <option value="" disabled {{ !old('status') ? 'selected' : '' }}>Status</option>
                                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                <label for="status" class="form-label">Status <span class="text-red-500">*</span></label>
                                @error('status')
                                    <div class="error-message">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="lg:col-span-2">
                                <div class="flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <input class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded" type="checkbox" value="1" id="is_verified" name="is_verified" {{ old('is_verified') ? 'checked' : '' }}>
                                    {{-- <label class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300" for="is_verified"> --}}
                                        <i class="fas fa-shield-alt text-green-500 mr-2"></i>
                                        Mark as Verified Provider
                                    </label>
                                </div>
                                @error('is_verified')
                                    <div class="error-message">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="lg:col-span-2">
                                {{-- <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Business Logo</label> --}}
                                <div class="file-upload-area" onclick="document.getElementById('logo').click()">
                                    <div class="text-center">
                                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                                        <p class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-2">Click to upload logo</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">PNG, JPG, GIF up to 2MB</p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">Recommended size: 400x400px</p>
                                    </div>
                                    <input type="file" class="hidden" id="logo" name="logo" accept="image/jpeg,image/png,image/jpg,image/gif" onchange="previewImage(this)">
                                </div>
                                <div id="logo-preview" class="mt-4 hidden">
                                    <img id="logo-preview-img" class="h-32 w-32 object-cover rounded-lg border-4 border-white shadow-lg mx-auto" alt="Logo preview">
                                </div>
                                @error('logo')
                                    <div class="error-message">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="flex justify-between mt-6">
                            <button type="button" class="btn-secondary" onclick="prevStep(1)">
                                <i class="fas fa-arrow-left"></i>
                                Previous
                            </button>
                            <button type="button" class="btn-primary" onclick="nextStep(3)">
                                Next Step
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Additional Information -->
                <div class="form-section mb-8 hidden" id="step-3">
                    <div class="section-header">
                        <h2 class="section-title">
                            <div class="section-icon">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            Additional Information
                        </h2>
                        <p class="text-gray-600 dark:text-gray-400 mt-2">Optional details and location information</p>
                    </div>

                    <div class="p-6">
                        <div class="space-y-6">
                            <div class="form-group">
                                <input type="text" class="form-input min-h-[120px] resize-y {{ old('description') ? 'has-value' : '' }}" id="description" name="description" rows="4" value="{{ old('description') }}" placeholder="Describe your business, services, and what makes you unique..."/>
                                {{-- <label for="description" class="form-label">Business Description</label> --}}
                                @error('description')
                                    <div class="error-message">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div class="form-group">
                                    <input type="text" class="form-input {{ old('address') ? 'has-value' : '' }}" id="address" name="address" value="{{ old('address') }}" placeholder="Enter business address">
                                    {{-- <label for="address" class="form-label">Business Address</label> --}}
                                    @error('address')
                                        <div class="error-message">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <input type="text" class="form-input {{ old('city') ? 'has-value' : '' }}" id="city" name="city" value="{{ old('city') }}" placeholder="Enter city">
                                    {{-- <label for="city" class="form-label">City</label> --}}
                                    @error('city')
                                        <div class="error-message">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-between mt-8">
                            <button type="button" class="btn-secondary" onclick="prevStep(2)">
                                <i class="fas fa-arrow-left"></i>
                                Previous
                            </button>
                            <div class="flex gap-4">
                                <a href="{{ route('admin.providers.index') }}" class="btn-secondary">
                                    <i class="fas fa-times"></i>
                                    Cancel
                                </a>
                                <button type="submit" class="btn-primary" id="submit-btn">
                                    <i class="fas fa-save"></i>
                                    Create Provider
                                    <div class="hidden ml-2" id="loading-spinner">
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/browser-image-compression/2.0.0/browser-image-compression.min.js"></script>
<script>
    let currentStep = 1;

    function nextStep(step) {
        if (validateCurrentStep()) {
            document.getElementById(`step-${currentStep}`).classList.add('hidden');
            document.getElementById(`step-${step}`).classList.remove('hidden');

            // Update progress
            document.querySelector(`.progress-step:nth-child(${currentStep})`).classList.remove('active');
            document.querySelector(`.progress-step:nth-child(${step})`).classList.add('active');

            currentStep = step;

            // Smooth scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }

    function prevStep(step) {
        document.getElementById(`step-${currentStep}`).classList.add('hidden');
        document.getElementById(`step-${step}`).classList.remove('hidden');

        // Update progress
        document.querySelector(`.progress-step:nth-child(${currentStep})`).classList.remove('active');
        document.querySelector(`.progress-step:nth-child(${step})`).classList.add('active');

        currentStep = step;

        // Smooth scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function validateCurrentStep() {
        const currentStepElement = document.getElementById(`step-${currentStep}`);
        const requiredFields = currentStepElement.querySelectorAll('input[required], select[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('border-red-500');
                isValid = false;
            } else {
                field.classList.remove('border-red-500');
            }
        });

        if (!isValid) {
            // Show error message
            const errorDiv = document.createElement('div');
            errorDiv.className = 'bg-red-50 dark:bg-red-900/30 border-l-4 border-red-500 rounded-r-lg p-4 mb-4';
            errorDiv.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                    <span class="text-red-700 dark:text-red-300">Please fill in all required fields before proceeding.</span>
                </div>
            `;

            const existingError = currentStepElement.querySelector('.bg-red-50');
            if (existingError) {
                existingError.remove();
            }

            currentStepElement.querySelector('.p-6').insertBefore(errorDiv, currentStepElement.querySelector('.p-6').firstChild);

            setTimeout(() => {
                errorDiv.remove();
            }, 5000);
        }

        return isValid;
    }

    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('logo-preview').classList.remove('hidden');
                document.getElementById('logo-preview-img').src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('providerForm');
        const logoInput = document.querySelector('input[name="logo"]');
        const submitBtn = document.getElementById('submit-btn');
        const loadingSpinner = document.getElementById('loading-spinner');

        if (form && logoInput) {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                // Show loading state
                submitBtn.disabled = true;
                loadingSpinner.classList.remove('hidden');

                try {
                    // Check if there's a file selected for upload
                    if (logoInput.files && logoInput.files[0]) {
                        // Compress the image
                        const imageFile = logoInput.files[0];
                        console.log('Original file size:', imageFile.size / 1024 / 1024, 'MB');

                        const options = {
                            maxSizeMB: 0.5,
                            maxWidthOrHeight: 1024,
                            useWebWorker: true
                        };

                        const compressedFile = await imageCompression(imageFile, options);
                        console.log('Compressed file size:', compressedFile.size / 1024 / 1024, 'MB');

                        // Create a new file with proper name and type
                        const compressedBlob = new File([compressedFile], imageFile.name, {
                            type: imageFile.type,
                            lastModified: new Date().getTime()
                        });

                        // Replace the original file with the compressed one
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(compressedBlob);
                        logoInput.files = dataTransfer.files;
                    }

                    // Submit the form
                    form.submit();
                } catch (error) {
                    console.error('Error during image compression:', error);
                    // Reset loading state
                    submitBtn.disabled = false;
                    loadingSpinner.classList.add('hidden');
                    // Submit the form anyway if compression fails
                    form.submit();
                }
            });
        }

        // Add floating label animation
        const inputs = document.querySelectorAll('.form-input');
        inputs.forEach(input => {
            // Function to check if input should be focused
            function checkInputState() {
                const hasValue = input.value && input.value.trim() !== '';
                const hasClass = input.classList.contains('has-value');

                if (hasValue || hasClass) {
                    input.parentElement.classList.add('focused');
                    input.classList.add('has-value');
                } else {
                    input.parentElement.classList.remove('focused');
                    input.classList.remove('has-value');
                }
            }

            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });

            input.addEventListener('blur', function() {
                checkInputState();
            });

            input.addEventListener('input', function() {
                checkInputState();
            });

            // Check initial state on page load
            checkInputState();
        });
    });
</script>
@endsection
@endsection
