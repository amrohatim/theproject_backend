<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Provider License Upload - Dala3Chic">
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
        .input-group {
            position: relative;
        }
        .input-group input:focus + label,
        .input-group input:not(:placeholder-shown) + label,
        .input-group textarea:focus + label,
        .input-group textarea:not(:placeholder-shown) + label,
        .input-group select:focus + label,
        .input-group select:not([value=""]) + label {
            transform: translateY(-1.5rem) scale(0.85);
            color: #f093fb;
        }
        .input-group label {
            position: absolute;
            left: 1rem;
            top: 1rem;
            transition: all 0.3s ease;
            pointer-events: none;
            color: #6b7280;
        }
        .step-indicator {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .file-upload-area {
            border: 2px dashed #d1d5db;
            transition: all 0.3s ease;
        }
        .file-upload-area:hover {
            border-color: #f093fb;
            background-color: #fef7ff;
        }
        .file-upload-area.dragover {
            border-color: #f093fb;
            background-color: #fdf2f8;
        }
        .file-upload-area.has-file {
            border-color: #10b981;
            background-color: #f0fdf4;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-pink-50 via-purple-50 to-rose-50 min-h-screen">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <img src="{{ asset('assets/logo.png') }}" alt="Dala3Chic Logo" class="mx-auto w-20 h-20 object-contain rounded-2xl mb-6">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    License Upload
                </h1>
                <p class="text-gray-600">
                    Step 2 of 3: Upload your professional license or certification
                </p>

                <!-- Progress Bar -->
                <div class="w-full bg-gray-200 rounded-full h-2 mt-4">
                    <div class="step-indicator h-2 rounded-full" style="width: 66%"></div>
                </div>
            </div>

            <!-- Registration Form -->
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

                <!-- User Information Summary -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Registration Summary</h3>
                    <div class="grid md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="font-medium text-gray-700">Name:</span>
                            <span class="text-gray-600">{{ $user->name }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Email:</span>
                            <span class="text-gray-600">{{ $user->email }}</span>
                        </div>
                        @if($user->provider)
                        <div>
                            <span class="font-medium text-gray-700">Business:</span>
                            <span class="text-gray-600">{{ $user->provider->business_name ?? 'Individual Provider' }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Delivery:</span>
                            <span class="text-gray-600">{{ $user->provider->deliver_to_vendor_capability ? 'Available' : 'Not Available' }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <form method="POST" action="{{ route('provider.registration.license.submit') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $user->id }}">

                    <!-- License File Upload -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Professional License or Certification *
                        </label>
                        <p class="text-sm text-gray-600 mb-4">
                            Please upload your professional license, certification, or any document that validates your expertise in providing services.
                        </p>

                        <div class="file-upload-area rounded-lg p-8 text-center" id="license-upload-area">
                            <input
                                type="file"
                                id="license_file"
                                name="license_file"
                                accept=".pdf,.jpg,.jpeg,.png"
                                required
                                class="hidden"
                                onchange="handleLicenseFileSelect(this)"
                            >
                            <div id="upload-content">
                                <i class="fas fa-certificate text-6xl text-pink-400 mb-4"></i>
                                <p class="text-gray-600 mb-2 font-medium">Upload Professional License</p>
                                <p class="text-sm text-gray-500 mb-4">Click to upload or drag and drop</p>
                                <p class="text-xs text-gray-400">PDF, JPG, PNG files up to 10MB</p>
                            </div>
                            <div id="file-preview" class="hidden">
                                <i id="file-icon" class="text-6xl text-green-500 mb-4"></i>
                                <p id="file-name" class="text-sm text-gray-600 font-medium mb-2"></p>
                                <p id="file-size" class="text-xs text-gray-500"></p>
                                <button
                                    type="button"
                                    onclick="clearFile()"
                                    class="mt-2 text-red-600 hover:text-red-700 text-sm"
                                >
                                    <i class="fas fa-times mr-1"></i>Remove file
                                </button>
                            </div>
                            <button
                                type="button"
                                onclick="document.getElementById('license_file').click()"
                                class="mt-4 bg-pink-600 text-white px-6 py-3 rounded-lg hover:bg-pink-700 transition-colors duration-300"
                            >
                                <i class="fas fa-upload mr-2"></i>Choose File
                            </button>
                        </div>
                    </div>

                    <!-- License Duration -->
                    <div class="input-group">
                        <select
                            id="duration_days"
                            name="duration_days"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all duration-300"
                        >
                            <option value="365" {{ old('duration_days', '365') == '365' ? 'selected' : '' }}>1 Year (365 days)</option>
                            <option value="730" {{ old('duration_days') == '730' ? 'selected' : '' }}>2 Years (730 days)</option>
                            <option value="1095" {{ old('duration_days') == '1095' ? 'selected' : '' }}>3 Years (1095 days)</option>
                            <option value="1825" {{ old('duration_days') == '1825' ? 'selected' : '' }}>5 Years (1825 days)</option>
                        </select>
                        <label for="duration_days" class="text-gray-500">License Duration</label>
                    </div>

                    <!-- Notes -->
                    <div class="input-group">
                        <textarea
                            id="notes"
                            name="notes"
                            placeholder=" "
                            rows="3"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all duration-300 resize-none"
                        >{{ old('notes') }}</textarea>
                        <label for="notes" class="text-gray-500">Additional Notes (Optional)</label>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        id="submit-btn"
                        disabled
                        class="w-full bg-gray-400 text-white py-3 px-6 rounded-lg font-semibold transition-all duration-300 cursor-not-allowed"
                    >
                        Complete Registration
                        <i class="fas fa-check ml-2"></i>
                    </button>
                </form>

                <!-- Back Button -->
                <div class="text-center mt-6">
                    <a href="{{ route('register.provider') }}" class="text-gray-600 hover:text-pink-600 transition-colors duration-300">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to provider information
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function handleLicenseFileSelect(input) {
            const file = input.files[0];
            const uploadContent = document.getElementById('upload-content');
            const filePreview = document.getElementById('file-preview');
            const fileName = document.getElementById('file-name');
            const fileSize = document.getElementById('file-size');
            const fileIcon = document.getElementById('file-icon');
            const uploadArea = document.getElementById('license-upload-area');
            const submitBtn = document.getElementById('submit-btn');

            if (file) {
                // Validate file type
                const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Please select a PDF, JPG, or PNG file only.');
                    input.value = '';
                    return;
                }

                // Validate file size (10MB)
                if (file.size > 10 * 1024 * 1024) {
                    alert('File size must be less than 10MB.');
                    input.value = '';
                    return;
                }

                // Set appropriate icon based on file type
                if (file.type === 'application/pdf') {
                    fileIcon.className = 'fas fa-file-pdf text-6xl text-red-500 mb-4';
                } else {
                    fileIcon.className = 'fas fa-file-image text-6xl text-blue-500 mb-4';
                }

                // Show file preview
                fileName.textContent = file.name;
                fileSize.textContent = formatFileSize(file.size);
                uploadContent.classList.add('hidden');
                filePreview.classList.remove('hidden');
                uploadArea.classList.add('has-file');

                // Enable submit button
                submitBtn.disabled = false;
                submitBtn.className = 'w-full bg-gradient-to-r from-pink-600 to-rose-600 text-white py-3 px-6 rounded-lg font-semibold hover:from-pink-700 hover:to-rose-700 transition-all duration-300 transform hover:scale-105';
            }
        }

        function clearFile() {
            const input = document.getElementById('license_file');
            const uploadContent = document.getElementById('upload-content');
            const filePreview = document.getElementById('file-preview');
            const uploadArea = document.getElementById('license-upload-area');
            const submitBtn = document.getElementById('submit-btn');

            input.value = '';
            uploadContent.classList.remove('hidden');
            filePreview.classList.add('hidden');
            uploadArea.classList.remove('has-file');

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

        // Drag and drop functionality
        const uploadArea = document.getElementById('license-upload-area');

        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                document.getElementById('license_file').files = files;
                handleLicenseFileSelect(document.getElementById('license_file'));
            }
        });
    </script>
</body>
</html>