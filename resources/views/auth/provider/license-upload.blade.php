<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Provider License Upload - Dala3Chic Registration">
    <meta name="robots" content="noindex, nofollow">

    <title>License Upload - Provider Registration - Dala3Chic</title>

    <!-- Preconnect to external domains -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom Styles -->
    @vite(['resources/css/app.css', 'resources/css/animations.css', 'resources/css/modern-auth.css'])

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        .form-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .step-indicator {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        .upload-area {
            border: 2px dashed #d1d5db;
            transition: all 0.3s ease;
        }
        .upload-area.dragover {
            border-color: #10b981;
            background-color: #f0fdf4;
        }
        .file-preview {
            background: #f0fdf4;
            border: 1px solid #d1fae5;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-green-50 via-emerald-50 to-teal-50 min-h-screen">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <img src="{{ asset('assets/logo.png') }}" alt="Dala3Chic Logo" class="mx-auto w-20 h-20 object-contain rounded-2xl mb-6">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    Professional License
                </h1>
                <p class="text-gray-600">
                    Step 2 of 2: Upload your professional license
                </p>

                <!-- Progress Bar -->
                <div class="w-full bg-gray-200 rounded-full h-2 mt-4">
                    <div class="step-indicator h-2 rounded-full" style="width: 100%"></div>
                </div>
            </div>

            <!-- License Upload Form -->
            <div class="form-container rounded-2xl shadow-xl p-8">
                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
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

                <form method="POST" action="{{ route('provider.registration.license.submit') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- Upload Instructions -->
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-green-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-green-800">
                                    Professional License Requirements
                                </h3>
                                <div class="mt-2 text-sm text-green-700">
                                    <ul class="list-disc list-inside space-y-1">
                                        <li>Upload a clear copy of your professional license</li>
                                        <li>Accepted formats: PDF, JPG, PNG</li>
                                        <li>Maximum file size: 10MB</li>
                                        <li>Ensure all text is clearly readable</li>
                                        <li>License must be current and valid</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- File Upload Area -->
                    <div class="upload-area rounded-lg p-8 text-center" id="uploadArea">
                        <div class="upload-content">
                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Upload Professional License</h3>
                            <p class="text-sm text-gray-600 mb-4">
                                Drag and drop your license file here, or click to browse
                            </p>
                            <input
                                type="file"
                                id="license_file"
                                name="license_file"
                                accept=".pdf,.jpg,.jpeg,.png"
                                required
                                class="hidden"
                            >
                            <button
                                type="button"
                                onclick="document.getElementById('license_file').click()"
                                class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors duration-300"
                            >
                                Choose File
                            </button>
                        </div>
                    </div>

                    <!-- File Preview -->
                    <div id="filePreview" class="hidden file-preview rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="fas fa-file-pdf text-red-500 text-2xl mr-3"></i>
                                <div>
                                    <p class="font-medium text-gray-900" id="fileName"></p>
                                    <p class="text-sm text-gray-600" id="fileSize"></p>
                                </div>
                            </div>
                            <button
                                type="button"
                                onclick="removeFile()"
                                class="text-red-600 hover:text-red-700 transition-colors duration-300"
                            >
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <!-- License Type -->
                    <div>
                        <label for="license_type" class="block text-sm font-medium text-gray-700 mb-2">
                            License Type *
                        </label>
                        <select
                            id="license_type"
                            name="license_type"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300"
                        >
                            <option value="">Select license type</option>
                            <option value="medical_license" {{ old('license_type') === 'medical_license' ? 'selected' : '' }}>Medical License</option>
                            <option value="nursing_license" {{ old('license_type') === 'nursing_license' ? 'selected' : '' }}>Nursing License</option>
                            <option value="pharmacy_license" {{ old('license_type') === 'pharmacy_license' ? 'selected' : '' }}>Pharmacy License</option>
                            <option value="dental_license" {{ old('license_type') === 'dental_license' ? 'selected' : '' }}>Dental License</option>
                            <option value="physiotherapy_license" {{ old('license_type') === 'physiotherapy_license' ? 'selected' : '' }}>Physiotherapy License</option>
                            <option value="psychology_license" {{ old('license_type') === 'psychology_license' ? 'selected' : '' }}>Psychology License</option>
                            <option value="nutrition_license" {{ old('license_type') === 'nutrition_license' ? 'selected' : '' }}>Nutrition License</option>
                            <option value="other_healthcare" {{ old('license_type') === 'other_healthcare' ? 'selected' : '' }}>Other Healthcare License</option>
                        </select>
                    </div>

                    <!-- License Number -->
                    <div>
                        <label for="license_number" class="block text-sm font-medium text-gray-700 mb-2">
                            License Number *
                        </label>
                        <input
                            id="license_number"
                            name="license_number"
                            type="text"
                            required
                            value="{{ old('license_number') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300"
                            placeholder="Enter license number"
                        >
                    </div>

                    <!-- Issuing Authority -->
                    <div>
                        <label for="issuing_authority" class="block text-sm font-medium text-gray-700 mb-2">
                            Issuing Authority *
                        </label>
                        <input
                            id="issuing_authority"
                            name="issuing_authority"
                            type="text"
                            required
                            value="{{ old('issuing_authority') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300"
                            placeholder="e.g., Dubai Health Authority (DHA)"
                        >
                    </div>

                    <!-- Expiry Date -->
                    <div>
                        <label for="expiry_date" class="block text-sm font-medium text-gray-700 mb-2">
                            License Expiry Date *
                        </label>
                        <input
                            id="expiry_date"
                            name="expiry_date"
                            type="date"
                            required
                            value="{{ old('expiry_date') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300"
                        >
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        id="submitBtn"
                        disabled
                        class="w-full bg-gray-400 text-white py-3 px-6 rounded-lg font-semibold transition-all duration-300 cursor-not-allowed"
                    >
                        Complete Registration
                        <i class="fas fa-check ml-2"></i>
                    </button>
                </form>

                <!-- Back Link -->
                <div class="text-center mt-4">
                    <a href="{{ route('register.provider') }}" class="text-green-600 hover:text-green-700 text-sm transition-colors duration-300">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back to Personal Information
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('license_file');
        const filePreview = document.getElementById('filePreview');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        const submitBtn = document.getElementById('submitBtn');

        // Drag and drop functionality
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', () => {
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
            const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
            if (!allowedTypes.includes(file.type)) {
                alert('Please upload a PDF, JPG, or PNG file.');
                return;
            }

            // Validate file size (10MB)
            if (file.size > 10 * 1024 * 1024) {
                alert('File size must be less than 10MB.');
                return;
            }

            // Update file input
            const dt = new DataTransfer();
            dt.items.add(file);
            fileInput.files = dt.files;

            // Show preview
            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);

            // Update icon based on file type
            const icon = filePreview.querySelector('i');
            if (file.type === 'application/pdf') {
                icon.className = 'fas fa-file-pdf text-red-500 text-2xl mr-3';
            } else {
                icon.className = 'fas fa-file-image text-blue-500 text-2xl mr-3';
            }

            filePreview.classList.remove('hidden');
            uploadArea.style.display = 'none';

            // Enable submit button
            submitBtn.disabled = false;
            submitBtn.className = 'w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white py-3 px-6 rounded-lg font-semibold hover:from-green-700 hover:to-emerald-700 transition-all duration-300 transform hover:scale-105';
        }

        function removeFile() {
            fileInput.value = '';
            filePreview.classList.add('hidden');
            uploadArea.style.display = 'block';

            // Disable submit button
            submitBtn.disabled = true;
            submitBtn.className = 'w-full bg-gray-400 text-white py-3 px-6 rounded-lg font-semibold transition-all duration-300 cursor-not-allowed';
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