<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload License - Provider Portal</title>
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
                    Upload Your License
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Please upload your professional license to access the provider dashboard
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
                                Please correct the following errors:
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
                                License Requirements
                            </h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Upload a clear copy of your professional license</li>
                                    <li>Accepted format: PDF only</li>
                                    <li>Maximum file size: 10MB</li>
                                    <li>Ensure all text is clearly readable</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- File Upload Area -->
                <div class="upload-area rounded-lg p-8 text-center" id="uploadArea">
                    <div class="upload-content" id="uploadContent">
                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Upload Professional License</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            Drag and drop your license file here, or click to browse
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
                            Choose File
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
                            <i class="fas fa-times mr-1"></i>Remove
                        </button>
                    </div>
                </div>

                <!-- License Expiry Date -->
                <div>
                    <label for="license_expiry_date" class="block text-sm font-medium text-gray-700 mb-2">
                        License Expiry Date *
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

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Additional Notes (Optional)
                    </label>
                    <textarea
                        id="notes"
                        name="notes"
                        rows="3"
                        maxlength="500"
                        placeholder="Any additional information about your license..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    >{{ old('notes') }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">Maximum 500 characters</p>
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
                        Upload License
                    </button>
                </div>

                <!-- Back to Login -->
                <div class="text-center">
                    <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:text-blue-500">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back to Login
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
                alert('Please upload a PDF file only.');
                return;
            }

            // Validate file size (10MB)
            if (file.size > 10 * 1024 * 1024) {
                alert('File size must be less than 10MB.');
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
    </script>
</body>
</html>
