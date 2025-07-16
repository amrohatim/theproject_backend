<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Vendor License - Dala3Chic</title>
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
    </style>
</head>
<body class="bg-gradient-to-br from-purple-50 via-pink-50 to-indigo-50 min-h-screen">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <img src="{{ asset('assets/logo.png') }}" alt="Dala3Chic Logo" class="mx-auto w-20 h-20 object-contain rounded-2xl mb-6">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    Upload Vendor License
                </h1>
                <p class="text-gray-600">
                    Please upload your business license to complete your vendor registration
                </p>
            </div>

            <!-- Upload Form -->
            <div class="form-container rounded-2xl shadow-xl p-8">
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex">
                            <i class="fas fa-exclamation-circle text-red-400 mr-3 mt-1"></i>
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

                @if (session('success'))
                    <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex">
                            <i class="fas fa-check-circle text-green-400 mr-3 mt-1"></i>
                            <div>
                                <h3 class="text-sm font-medium text-green-800">Success!</h3>
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
                            License Document <span class="text-red-500">*</span>
                        </label>
                        <div class="file-drop-zone rounded-lg p-6 text-center" id="fileDropZone">
                            <input type="file" id="license_file" name="license_file" accept=".pdf,.jpg,.jpeg,.png" required class="hidden">
                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                            <p class="text-gray-600 mb-2">Click to upload or drag and drop</p>
                            <p class="text-sm text-gray-500">PDF, JPG, JPEG, PNG (Max: 10MB)</p>
                            <button type="button" onclick="document.getElementById('license_file').click()" 
                                    class="mt-4 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200">
                                Choose File
                            </button>
                        </div>
                        <div id="fileInfo" class="mt-2 text-sm text-gray-600 hidden"></div>
                    </div>

                    <!-- License Dates -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                                License Start Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="start_date" name="start_date" required
                                   value="{{ old('start_date') }}"
                                   min="{{ date('Y-m-d') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                                License End Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="end_date" name="end_date" required
                                   value="{{ old('end_date') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Additional Notes (Optional)
                        </label>
                        <textarea id="notes" name="notes" rows="3" maxlength="1000"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                  placeholder="Any additional information about your license...">{{ old('notes') }}</textarea>
                        <div class="mt-1 text-sm text-gray-500">
                            <span id="notes-count">0</span>/1000 characters
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-between items-center">
                        <a href="{{ route('vendor.dashboard') }}" 
                           class="text-gray-600 hover:text-gray-800 transition-colors duration-200">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to Dashboard
                        </a>
                        <button type="submit" 
                                class="px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-lg hover:from-purple-700 hover:to-indigo-700 transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-upload mr-2"></i>
                            Upload License
                        </button>
                    </div>
                </form>
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
                const fileSize = (file.size / 1024 / 1024).toFixed(2);
                fileInfo.innerHTML = `Selected: ${file.name} (${fileSize} MB)`;
                fileInfo.classList.remove('hidden');
            }
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
    </script>
</body>
</html>
