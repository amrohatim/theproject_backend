<!-- License Renewal Modal -->
<div id="renewalModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('messages.renew_license') }}</h3>
            <button onclick="hideRenewalModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="pt-4">
            <form id="renewalForm" enctype="multipart/form-data">
                @csrf
                
                <!-- License File Upload -->
                <div class="mb-6">
                    <label for="license_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('messages.license_document') }} <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-md hover:border-gray-400 dark:hover:border-gray-500 transition-colors">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600 dark:text-gray-400">
                                <label for="license_file" class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                    <span>{{ __('messages.upload_file') }}</span>
                                    <input id="license_file" name="license_file" type="file" class="sr-only" accept=".pdf" required>
                                </label>
                                <p class="pl-1">{{ __('messages.or_drag_drop') }}</p>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('messages.pdf_files_only_max_10mb') }}</p>
                        </div>
                    </div>
                    <!-- PDF Preview (hidden by default) -->
                    <div id="pdfPreviewRenewal" class="mt-4 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-800 border-2 border-blue-300 dark:border-blue-600 rounded-lg p-6 text-center hidden">
                        <i class="fas fa-file-pdf text-5xl text-red-500 mb-3"></i>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">PDF Uploaded Successfully!</h4>
                        <p id="pdfFileNameRenewal" class="text-sm text-gray-600 dark:text-gray-300 mb-1"></p>
                        <p id="pdfFileSizeRenewal" class="text-xs text-gray-500 dark:text-gray-400 mb-4"></p>
                        <div class="flex justify-center space-x-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-200">
                                <i class="fas fa-check-circle mr-1"></i>
                                Valid PDF Format
                            </span>
                            <button type="button" onclick="clearRenewalFileSelection()"
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                <i class="fas fa-times mr-1"></i>
                                Remove
                            </button>
                        </div>
                    </div>
                    <div id="file-name" class="mt-2 text-sm text-gray-600 dark:text-gray-400 hidden"></div>
                </div>

                <!-- Date Fields -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('messages.start_date') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               id="start_date" 
                               name="start_date" 
                               min="{{ date('Y-m-d') }}"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                               required>
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('messages.end_date') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               id="end_date" 
                               name="end_date" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                               required>
                    </div>
                </div>

                <!-- Notes -->
                <div class="mb-6">
                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('messages.additional_notes_optional') }}
                    </label>
                    <textarea id="notes" 
                              name="notes" 
                              rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                              placeholder="{{ __('messages.renewal_notes_placeholder') }}"></textarea>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" 
                            onclick="hideRenewalModal()" 
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        {{ __('messages.cancel') }}
                    </button>
                    <button type="submit" 
                            id="submitBtn"
                            class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-green-600 hover:bg-green-700 transition-colors">
                        <span id="submitText">{{ __('messages.submit_renewal') }}</span>
                        <span id="submitLoader" class="hidden">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                            {{ __('messages.processing') }}...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div id="errorModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-red-600 dark:text-red-400">{{ __('messages.validation_errors') }}</h3>
            <button onclick="hideErrorModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="pt-4">
            <div id="errorList" class="text-sm text-red-600 dark:text-red-400"></div>
            <div class="flex justify-end mt-4">
                <button onclick="hideErrorModal()" 
                        class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-red-600 hover:bg-red-700 transition-colors">
                    {{ __('messages.close') }}
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// File upload handling
document.getElementById('license_file').addEventListener('change', function(e) {
    const file = e.target.files[0];
    
    if (file) {
        // Validate file type (PDF only)
        if (file.type !== 'application/pdf') {
            showErrors({'license_file': ['{{ __('messages.please_select_pdf_only') }}']}); 
            clearRenewalFileSelection();
            return;
        }
        
        // Validate file size (10MB max)
        const maxSize = 10 * 1024 * 1024; // 10MB in bytes
        if (file.size > maxSize) {
            showErrors({'license_file': ['{{ __('messages.file_size_must_be_less_than_10mb') }}']}); 
            clearRenewalFileSelection();
            return;
        }
        
        // Show PDF preview
        showRenewalPdfPreview(file);
    }
});

function showRenewalPdfPreview(file) {
    const fileSize = (file.size / 1024 / 1024).toFixed(2);
    
    // Update preview content
    document.getElementById('pdfFileNameRenewal').textContent = file.name;
    document.getElementById('pdfFileSizeRenewal').textContent = `${fileSize} MB`;
    
    // Hide upload zone and show preview
    document.querySelector('.border-dashed').style.display = 'none';
    document.getElementById('pdfPreviewRenewal').classList.remove('hidden');
    document.getElementById('file-name').classList.add('hidden');
}

function clearRenewalFileSelection() {
    // Clear file input
    document.getElementById('license_file').value = '';
    
    // Hide preview and show upload zone
    document.getElementById('pdfPreviewRenewal').classList.add('hidden');
    document.querySelector('.border-dashed').style.display = 'flex';
    document.getElementById('file-name').classList.add('hidden');
}

// Date validation
document.getElementById('start_date').addEventListener('change', function() {
    const startDate = this.value;
    const endDateInput = document.getElementById('end_date');
    
    if (startDate) {
        // Set minimum end date to be after start date
        const minEndDate = new Date(startDate);
        minEndDate.setDate(minEndDate.getDate() + 1);
        endDateInput.min = minEndDate.toISOString().split('T')[0];
        
        // Clear end date if it's before the new start date
        if (endDateInput.value && endDateInput.value <= startDate) {
            endDateInput.value = '';
        }
    }
});

// Form submission
document.getElementById('renewalForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const submitLoader = document.getElementById('submitLoader');
    
    // Show loading state
    submitBtn.disabled = true;
    submitText.classList.add('hidden');
    submitLoader.classList.remove('hidden');
    
    const formData = new FormData(this);
    
    fetch('{{ route("vendor.license.renewal.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Redirect to license page with success message
            window.location.href = data.redirect + '?success=' + encodeURIComponent(data.message);
        } else {
            // Show validation errors
            showErrors(data.errors);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrors({'general': ['An error occurred while processing your request. Please try again.']});
    })
    .finally(() => {
        // Reset loading state
        submitBtn.disabled = false;
        submitText.classList.remove('hidden');
        submitLoader.classList.add('hidden');
    });
});

function showErrors(errors) {
    const errorList = document.getElementById('errorList');
    let errorHtml = '<ul class="list-disc list-inside space-y-1">';
    
    for (const field in errors) {
        errors[field].forEach(error => {
            errorHtml += `<li>${error}</li>`;
        });
    }
    
    errorHtml += '</ul>';
    errorList.innerHTML = errorHtml;
    
    document.getElementById('errorModal').classList.remove('hidden');
    hideRenewalModal();
}

function hideErrorModal() {
    document.getElementById('errorModal').classList.add('hidden');
}
</script>
