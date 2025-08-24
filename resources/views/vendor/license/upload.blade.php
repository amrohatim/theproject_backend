<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.upload_vendor_license') }} - Dala3Chic</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .form-container { background: linear-gradient(135deg, #ffffff, #f8fafc); border: 1px solid #e2e8f0; }
        .file-drop-zone {
            border: 2px dashed #d1d5db;
            transition: all 0.3s ease;
        }
        .file-drop-zone.dragover {
            border-color: #6366f1;
            background-color: #f0f9ff;
        }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            backdrop-filter: blur(4px);
        }

        .modal-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            max-width: 500px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            animation: modalSlideIn 0.3s ease-out;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-20px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 24px;
            border-bottom: 1px solid #e5e7eb;
        }

        .modal-body {
            padding: 20px 24px;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            padding: 16px 24px;
            border-top: 1px solid #e5e7eb;
            background-color: #f9fafb;
        }

        /* PDF Preview Styles */
        .pdf-preview {
            background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
            border: 2px solid #3b82f6;
            border-radius: 12px;
            padding: 24px;
            text-align: center;
            margin: 16px 0;
            animation: fadeInScale 0.4s ease-out;
        }

        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .pdf-icon {
            font-size: 4rem;
            color: #dc2626;
            margin-bottom: 16px;
            animation: bounceIn 0.6s ease-out;
        }

        @keyframes bounceIn {
            0% { transform: scale(0.3); opacity: 0; }
            50% { transform: scale(1.05); }
            70% { transform: scale(0.9); }
            100% { transform: scale(1); opacity: 1; }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-purple-50 via-pink-50 to-indigo-50 min-h-screen">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <img src="{{ asset('assets/logo.png') }}" alt="Dala3Chic Logo" class="mx-auto w-20 h-20 object-contain rounded-2xl mb-6">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    {{ __('messages.upload_vendor_license') }}
                </h1>
                <p class="text-gray-600">
                    {{ __('messages.upload_license_description') }}
                </p>
            </div>

            <!-- Upload Form -->
            <div class="form-container rounded-2xl shadow-xl p-8">
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex">
                            <i class="fas fa-exclamation-circle text-red-400 mr-3 mt-1"></i>
                            <div>
                                <h3 class="text-sm font-medium text-red-800">{{ __('messages.please_correct_errors') }}:</h3>
                                <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                @if (session('success'))
                    <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex">
                            <i class="fas fa-check-circle text-green-400 mr-3 mt-1"></i>
                            <div>
                                <h3 class="text-sm font-medium text-green-800">{{ __('messages.success') }}!</h3>
                                <p class="mt-1 text-sm text-green-700">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('vendor.license.upload.submit') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf



                    <!-- License File Upload -->
                    <div>
                        <label for="license_file" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('messages.license_document') }} <span class="text-red-500">*</span>
                        </label>
                        <div class="file-drop-zone rounded-lg p-6 text-center" id="fileDropZone">
                            <input type="file" id="license_file" name="license_file" accept=".pdf" required class="hidden">
                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                            <p class="text-gray-600 mb-2">{{ __('messages.click_upload_or_drag') }}</p>
                            <p class="text-sm text-gray-500">{{ __('messages.pdf_files_only_max_10mb') }}</p>
                            <button type="button" onclick="document.getElementById('license_file').click()"
                                    class="mt-4 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200">
                                {{ __('messages.choose_file') }}
                            </button>
                        </div>

                        <!-- PDF Preview (hidden by default) -->
                        <div id="pdfPreview" class="pdf-preview hidden">
                            <i class="fas fa-file-pdf pdf-icon"></i>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('messages.pdf_uploaded_successfully') }}!</h3>
                            <p id="pdfFileName" class="text-sm text-gray-600 mb-1"></p>
                            <p id="pdfFileSize" class="text-xs text-gray-500 mb-4"></p>
                            <div class="flex justify-center space-x-3">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    {{ __('messages.valid_pdf_format') }}
                                </span>
                                <button type="button" onclick="clearFileSelection()"
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors">
                                    <i class="fas fa-times mr-1"></i>
                                    {{ __('messages.remove') }}
                                </button>
                            </div>
                        </div>

                        <div id="fileInfo" class="mt-2 text-sm text-gray-600 hidden"></div>
                    </div>

                    <!-- License Dates -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('messages.license_start_date') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="start_date" name="start_date" required
                                   value="{{ old('start_date') }}"
                                   min="{{ date('Y-m-d') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('messages.license_end_date') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="end_date" name="end_date" required
                                   value="{{ old('end_date') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('messages.additional_notes') }} ({{ __('messages.optional') }})
                        </label>
                        <textarea id="notes" name="notes" rows="3" maxlength="1000"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                  placeholder="{{ __('messages.enter_additional_license_info') }}...">{{ old('notes') }}</textarea>
                        <div class="mt-1 text-sm text-gray-500">
                            <span id="notes-count">0</span>/1000 characters
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-between items-center">
                        <a href="{{ route('vendor.dashboard') }}" 
                           class="text-gray-600 hover:text-gray-800 transition-colors duration-200">
                            <i class="fas fa-arrow-left mr-2"></i>
                            {{ __('messages.back_to_dashboard') }}
                        </a>
                        <button type="submit" 
                                class="px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-lg hover:from-purple-700 hover:to-indigo-700 transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-upload mr-2"></i>
                            {{ __('messages.upload_license') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Validation Error Modal -->
    <div id="validationErrorModal" class="modal-overlay" style="display: none;">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                    {{ __('messages.validation_error') }}
                </h3>
                <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeValidationModal()">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="modal-body">
                <p id="validationErrorMessage" class="text-gray-600"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors" onclick="closeValidationModal()">
                    {{ __('messages.ok') }}
                </button>
            </div>
        </div>
    </div>

    <script>
        // File upload handling
        const fileInput = document.getElementById('license_file');
        const fileDropZone = document.getElementById('fileDropZone');
        const fileInfo = document.getElementById('fileInfo');

        fileInput.addEventListener('change', handleFileSelect);

        // Drag and drop functionality
        fileDropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            fileDropZone.classList.add('dragover');
        });

        fileDropZone.addEventListener('dragleave', () => {
            fileDropZone.classList.remove('dragover');
        });

        fileDropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            fileDropZone.classList.remove('dragover');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                handleFileSelect();
            }
        });

        function handleFileSelect() {
            const file = fileInput.files[0];
            if (file) {
                // Validate file type (PDF only)
                if (file.type !== 'application/pdf') {
                    showValidationModal('{{ __('messages.please_select_pdf_only') }}');
                    clearFileSelection();
                    return;
                }

                // Validate file size (10MB max)
                const maxSize = 10 * 1024 * 1024; // 10MB in bytes
                if (file.size > maxSize) {
                    showValidationModal('{{ __('messages.file_size_must_be_less_than_10mb') }}');
                    clearFileSelection();
                    return;
                }

                // Show PDF preview
                showPdfPreview(file);
            }
        }

        function showPdfPreview(file) {
            const fileSize = (file.size / 1024 / 1024).toFixed(2);

            // Update preview content
            document.getElementById('pdfFileName').textContent = file.name;
            document.getElementById('pdfFileSize').textContent = `${fileSize} MB`;

            // Hide upload zone and show preview
            document.getElementById('fileDropZone').style.display = 'none';
            document.getElementById('pdfPreview').classList.remove('hidden');
            document.getElementById('fileInfo').classList.add('hidden');
        }

        function clearFileSelection() {
            // Clear file input
            fileInput.value = '';

            // Hide preview and show upload zone
            document.getElementById('pdfPreview').classList.add('hidden');
            document.getElementById('fileDropZone').style.display = 'block';
            document.getElementById('fileInfo').classList.add('hidden');
        }

        function showValidationModal(message) {
            document.getElementById('validationErrorMessage').textContent = message;
            document.getElementById('validationErrorModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeValidationModal() {
            document.getElementById('validationErrorModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Date validation
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');

        startDateInput.addEventListener('change', () => {
            endDateInput.min = startDateInput.value;
            if (endDateInput.value && endDateInput.value <= startDateInput.value) {
                endDateInput.value = '';
            }
        });

        // Notes character count
        const notesTextarea = document.getElementById('notes');
        const notesCount = document.getElementById('notes-count');

        function updateNotesCount() {
            const count = notesTextarea.value.length;
            notesCount.textContent = count;

            // Change color based on character count
            if (count > 900) {
                notesCount.className = 'text-red-500 font-medium';
            } else if (count > 750) {
                notesCount.className = 'text-yellow-500 font-medium';
            } else {
                notesCount.className = '';
            }
        }

        notesTextarea.addEventListener('input', updateNotesCount);

        // Initialize count on page load
        updateNotesCount();

        // Form submission validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const file = fileInput.files[0];

            // Check if a file is selected
            if (!file) {
                e.preventDefault();
                showValidationModal('{{ __('messages.please_select_pdf_file') }}');
                return;
            }

            // Double-check file type
            if (file.type !== 'application/pdf') {
                e.preventDefault();
                showValidationModal('{{ __('messages.please_select_valid_pdf') }}');
                return;
            }

            // Double-check file size
            const maxSize = 10 * 1024 * 1024; // 10MB in bytes
            if (file.size > maxSize) {
                e.preventDefault();
                showValidationModal('{{ __('messages.file_size_must_be_less_than_10mb') }}');
                return;
            }
        });
    </script>
</body>
</html>
