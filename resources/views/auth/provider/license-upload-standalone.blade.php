<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('provider.upload_license_title') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .upload-area {
            border: 2px dashed #d1d5db;
            transition: all 0.3s ease;
        }
        .upload-area:hover {
            border-color: #3b82f6;
            background-color: #f8fafc;
        }
        .upload-area.dragover {
            border-color: #3b82f6;
            background-color: #eff6ff;
        }
        .file-preview {
            border: 2px solid #10b981;
            background-color: #f0fdf4;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <div class="mx-auto h-12 w-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-certificate text-blue-600 text-xl"></i>
                </div>
                <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                    {{ __('provider.upload_your_license') }}
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    {{ __('provider.upload_professional_license_description') }}
                </p>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">
                                {{ __('provider.please_correct_following_errors') }}
                            </h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Success Messages -->
            @if (session('success'))
                <div class="bg-green-50 border border-green-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">
                                {{ session('success') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Upload Form -->
            <form method="POST" action="{{ route('provider.license.upload.submit') }}" enctype="multipart/form-data" class="mt-8 space-y-6">
                @csrf

                <!-- Upload Instructions -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">
                                {{ __('provider.license_requirements') }}
                            </h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <ul class="list-disc list-inside space-y-1">
                                    <li>{{ __('provider.upload_clear_copy_professional_license') }}</li>
                                    <li>{{ __('provider.accepted_format_pdf_only') }}</li>
                                    <li>{{ __('provider.maximum_file_size_10mb') }}</li>
                                    <li>{{ __('provider.ensure_text_clearly_readable') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- File Upload Area -->
                <div class="upload-area rounded-lg p-8 text-center" id="uploadArea">
                    <div class="upload-content" id="uploadContent">
                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('provider.upload_professional_license') }}</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            {{ __('provider.drag_drop_license_file') }}
                        </p>
                        <input
                            type="file"
                            id="license_file"
                            name="license_file"
                            accept=".pdf"
                            required
                            class="hidden"
                        >
                        <button
                            type="button"
                            onclick="document.getElementById('license_file').click()"
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-300"
                        >
                            {{ __('provider.choose_file') }}
                        </button>
                    </div>
                    <div class="file-preview hidden" id="filePreview">
                        <i class="fas fa-file-pdf text-4xl text-red-500 mb-4"></i>
                        <p class="text-sm font-medium text-gray-900 mb-2" id="fileName"></p>
                        <p class="text-xs text-gray-500 mb-4" id="fileSize"></p>
                        <button
                            type="button"
                            onclick="removeFile()"
                            class="text-red-600 hover:text-red-800 text-sm"
                        >
                            <i class="fas fa-times mr-1"></i>{{ __('provider.remove') }}
                        </button>
                    </div>
                </div>

                <!-- License Dates -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- License Start Date -->
                    <div>
                        <label for="license_start_date" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('provider.license_start_date') }}
                        </label>
                        <input
                            type="date"
                            id="license_start_date"
                            name="license_start_date"
                            required
                            min="{{ date('Y-m-d') }}"
                            value="{{ old('license_start_date') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        >
                    </div>

                    <!-- License Expiry Date -->
                    <div>
                        <label for="license_expiry_date" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('provider.license_expiry_date') }}
                        </label>
                        <input
                            type="date"
                            id="license_expiry_date"
                            name="license_expiry_date"
                            required
                            min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                            value="{{ old('license_expiry_date') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        >
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('provider.additional_notes_optional') }}
                    </label>
                    <textarea
                        id="notes"
                        name="notes"
                        rows="3"
                        maxlength="500"
                        placeholder="{{ __('provider.additional_info_license_placeholder') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    >{{ old('notes') }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">{{ __('provider.maximum_500_characters') }}</p>
                </div>

                <!-- Submit Button -->
                <div>
                    <button
                        type="submit"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        id="submitBtn"
                        disabled
                    >
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-upload text-blue-500 group-hover:text-blue-400"></i>
                        </span>
                        {{ __('provider.upload_license') }}
                    </button>
                </div>

                <!-- Back to Login -->
                <div class="text-center">
                    <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:text-blue-500">
                        <i class="fas fa-arrow-left mr-1"></i>
                        {{ __('provider.back_to_login') }}
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('license_file');
        const uploadContent = document.getElementById('uploadContent');
        const filePreview = document.getElementById('filePreview');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        const submitBtn = document.getElementById('submitBtn');

        // Drag and drop events
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleFile(files[0]);
            }
        });

        // File input change
        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                handleFile(e.target.files[0]);
            }
        });

        function handleFile(file) {
            // Validate file type
            if (file.type !== 'application/pdf') {
                alert('{{ __('provider.upload_pdf_file_only') }}');
                return;
            }

            // Validate file size (10MB)
            if (file.size > 10 * 1024 * 1024) {
                alert('{{ __('provider.file_size_less_than_10mb') }}');
                return;
            }

            // Show file preview
            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);
            
            uploadContent.classList.add('hidden');
            filePreview.classList.remove('hidden');
            uploadArea.classList.add('file-preview');
            
            submitBtn.disabled = false;
        }

        function removeFile() {
            fileInput.value = '';
            uploadContent.classList.remove('hidden');
            filePreview.classList.add('hidden');
            uploadArea.classList.remove('file-preview');
            submitBtn.disabled = true;
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // Date validation
        const startDateInput = document.getElementById('license_start_date');
        const endDateInput = document.getElementById('license_expiry_date');

        // Set minimum start date to today
        const today = new Date().toISOString().split('T')[0];
        startDateInput.min = today;

        // Validate start date and update end date minimum
        startDateInput.addEventListener('change', function() {
            const startDate = new Date(this.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            // Check if start date is in the past
            if (startDate < today) {
                showValidationModal('{{ __('provider.license_start_date_cannot_past') }}');
                this.value = '';
                return;
            }

            // Set minimum end date to be after start date
            const minEndDate = new Date(startDate);
            minEndDate.setDate(minEndDate.getDate() + 1);
            endDateInput.min = minEndDate.toISOString().split('T')[0];

            // Clear end date if it's before or equal to start date
            if (endDateInput.value && new Date(endDateInput.value) <= startDate) {
                endDateInput.value = '';
            }
        });

        // Validate end date
        endDateInput.addEventListener('change', function() {
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(this.value);

            if (startDateInput.value && endDate <= startDate) {
                showValidationModal('{{ __('provider.license_end_date_after_start') }}');
                this.value = '';
                return;
            }
        });

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
                            <h3 class="text-lg font-medium text-gray-900 mt-2">{{ __('provider.validation_error') }}</h3>
                            <div class="mt-2 px-7 py-3">
                                <p class="text-sm text-gray-500" id="modalMessage"></p>
                            </div>
                            <div class="items-center px-4 py-3">
                                <button id="modalCloseBtn" class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">
                                    {{ __('provider.ok') }}
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
</body>
</html>
