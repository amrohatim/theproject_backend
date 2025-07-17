@extends('layouts.merchant')

@section('title', 'Complete Your Registration - Upload Business License')
@section('header', 'Complete Your Registration')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
    <div class="max-w-4xl w-full">
        <!-- Progress Indicator -->
        <div class="mb-8">
            <div class="flex items-center justify-center space-x-4 mb-4">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <span class="ml-2 text-sm font-medium text-green-600">Phone Verified</span>
                </div>
                <div class="w-16 h-1 bg-green-500"></div>
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                    </div>
                    <span class="ml-2 text-sm font-medium text-blue-600">Upload License</span>
                </div>
                <div class="w-16 h-1 bg-gray-300"></div>
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="ml-2 text-sm font-medium text-gray-500">Dashboard Access</span>
                </div>
            </div>
            <p class="text-center text-gray-600">You're almost done! Upload your business license to complete registration.</p>
        </div>

        <!-- Main Upload Card -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
            <!-- Header -->
            <div class="px-6 py-8 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
                <div class="text-center">
                    <div class="mx-auto w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Upload Your Business License</h1>
                    <p class="text-gray-600">Complete your merchant registration by uploading your business license documents</p>
                    
                    @if($isRejectedLicense && $rejectionReason)
                        <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-red-400 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <h3 class="text-sm font-medium text-red-800">Previous License Rejected</h3>
                                    <p class="text-sm text-red-700 mt-1">{{ $rejectionReason }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Form Content -->
            <div class="px-6 py-8">
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex">
                            <svg class="w-5 h-5 text-red-400 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <h3 class="text-sm font-medium text-red-800">Please correct the following errors:</h3>
                                <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('merchant.license.upload.submit') }}" method="POST" enctype="multipart/form-data" id="license-upload-form">
                    @csrf
                    
                    <!-- File Upload Section -->
                    <div class="mb-8">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Business License Document *</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center cursor-pointer hover:border-blue-400 hover:bg-blue-50/50 transition-all duration-200"
                             onclick="document.getElementById('license_file').click()" id="upload-area">
                            <input type="file" id="license_file" name="license_file" accept=".pdf" class="hidden" required onchange="handleFileSelect(this)">
                            <div id="upload-content">
                                <svg class="mx-auto w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <p class="text-lg font-medium text-gray-800 mb-2">Click to upload your license file</p>
                                <p class="text-sm text-gray-500 mb-2">or drag and drop it here</p>
                                <p class="text-xs text-gray-400">PDF files only (max 10MB)</p>
                            </div>
                            <div id="file-selected" class="hidden">
                                <svg class="mx-auto w-12 h-12 text-green-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-lg font-medium text-green-800 mb-1">File Selected</p>
                                <p class="text-sm text-gray-600" id="file-name"></p>
                                <p class="text-xs text-gray-500" id="file-size"></p>
                                <button type="button" onclick="clearFile()" class="mt-2 text-sm text-blue-600 hover:text-blue-800">Choose different file</button>
                            </div>
                        </div>
                    </div>

                    <!-- License Dates -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="license_start_date" class="block text-sm font-medium text-gray-700 mb-2">License Start Date *</label>
                            <input type="date" id="license_start_date" name="license_start_date" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   value="{{ old('license_start_date') }}" required>
                        </div>
                        <div>
                            <label for="license_end_date" class="block text-sm font-medium text-gray-700 mb-2">License End Date *</label>
                            <input type="date" id="license_end_date" name="license_end_date" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   value="{{ old('license_end_date') }}" required>
                        </div>
                    </div>

                    <!-- Optional Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <label for="license_type" class="block text-sm font-medium text-gray-700 mb-2">License Type (Optional)</label>
                            <input type="text" id="license_type" name="license_type" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="e.g., Trade License, Commercial License"
                                   value="{{ old('license_type') }}">
                        </div>
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Additional Notes (Optional)</label>
                            <textarea id="notes" name="notes" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Any additional information about your license">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-center">
                        <button type="submit" id="submit-btn" 
                                class="px-8 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span id="submit-text">Upload License & Complete Registration</span>
                            <span id="submit-loading" class="hidden">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Uploading...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Information Section -->
        <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Important Information
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-800">Review Time</h4>
                        <p class="text-sm text-gray-600">Your license will be reviewed within 24-48 hours</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.414-4.414a2 2 0 00-2.828 0L9 10.172 7.414 8.586a2 2 0 00-2.828 2.828l3 3a2 2 0 002.828 0l9-9a2 2 0 000-2.828z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-800">Secure Storage</h4>
                        <p class="text-sm text-gray-600">All documents are encrypted and stored securely</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-800">Email Updates</h4>
                        <p class="text-sm text-gray-600">You'll receive email notifications about your status</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function handleFileSelect(input) {
    const file = input.files[0];
    if (file) {
        // Validate file type - only PDF allowed
        if (file.type !== 'application/pdf') {
            showValidationModal('Please select a PDF file only. Other file formats are not allowed.');
            input.value = ''; // Clear the input
            return;
        }

        // Validate file size (10MB max)
        const maxSize = 10 * 1024 * 1024; // 10MB in bytes
        if (file.size > maxSize) {
            showValidationModal('File size must be less than 10MB. Please select a smaller file.');
            input.value = ''; // Clear the input
            return;
        }

        const uploadContent = document.getElementById('upload-content');
        const fileSelected = document.getElementById('file-selected');
        const fileName = document.getElementById('file-name');
        const fileSize = document.getElementById('file-size');

        uploadContent.classList.add('hidden');
        fileSelected.classList.remove('hidden');

        fileName.textContent = file.name;
        fileSize.textContent = formatFileSize(file.size);
    }
}

function clearFile() {
    const input = document.getElementById('license_file');
    const uploadContent = document.getElementById('upload-content');
    const fileSelected = document.getElementById('file-selected');
    
    input.value = '';
    uploadContent.classList.remove('hidden');
    fileSelected.classList.add('hidden');
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Form submission handling
document.getElementById('license-upload-form').addEventListener('submit', function(e) {
    const fileInput = document.getElementById('license_file');
    const file = fileInput.files[0];

    // Validate file before submission
    if (file) {
        // Check file type
        if (file.type !== 'application/pdf') {
            e.preventDefault();
            showValidationModal('Please select a PDF file only. Other file formats are not allowed.');
            return;
        }

        // Check file size (10MB max)
        const maxSize = 10 * 1024 * 1024; // 10MB in bytes
        if (file.size > maxSize) {
            e.preventDefault();
            showValidationModal('File size must be less than 10MB. Please select a smaller file.');
            return;
        }
    }

    const submitBtn = document.getElementById('submit-btn');
    const submitText = document.getElementById('submit-text');
    const submitLoading = document.getElementById('submit-loading');

    submitBtn.disabled = true;
    submitText.classList.add('hidden');
    submitLoading.classList.remove('hidden');
});

// Date validation
document.getElementById('license_start_date').addEventListener('change', function() {
    const startDate = new Date(this.value);
    const endDateInput = document.getElementById('license_end_date');
    
    // Set minimum end date to be after start date
    const minEndDate = new Date(startDate);
    minEndDate.setDate(minEndDate.getDate() + 1);
    endDateInput.min = minEndDate.toISOString().split('T')[0];
    
    // Clear end date if it's before start date
    if (endDateInput.value && new Date(endDateInput.value) <= startDate) {
        endDateInput.value = '';
    }
});

// Set minimum start date to today
document.getElementById('license_start_date').min = new Date().toISOString().split('T')[0];

// Modal for validation errors
function showValidationModal(message) {
    // Create modal if it doesn't exist
    let modal = document.getElementById('validationModal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'validationModal';
        modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
        modal.innerHTML = `
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mt-2">File Upload Error</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500" id="modalMessage"></p>
                    </div>
                    <div class="items-center px-4 py-3">
                        <button id="modalCloseBtn" class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">
                            OK
                        </button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);

        // Close modal event
        document.getElementById('modalCloseBtn').addEventListener('click', function() {
            modal.style.display = 'none';
        });

        // Close modal when clicking outside
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    }

    // Show modal with message
    document.getElementById('modalMessage').textContent = message;
    modal.style.display = 'block';
}
</script>
@endsection
