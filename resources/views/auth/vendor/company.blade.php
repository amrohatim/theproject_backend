<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Vendor Company Registration - Dala3Chic">
    <meta name="robots" content="noindex, nofollow">

    <title>Company Information - Vendor Registration - Dala3Chic</title>

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
            color: #667eea;
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .file-upload-area {
            border: 2px dashed #d1d5db;
            transition: all 0.3s ease;
        }
        .file-upload-area:hover {
            border-color: #667eea;
            background-color: #f8fafc;
        }
        .file-upload-area.dragover {
            border-color: #667eea;
            background-color: #eef2ff;
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
                    Company Information
                </h1>
                <p class="text-gray-600">
                    Step 2 of 4: Tell us about your business
                </p>

                <!-- Progress Bar -->
                <div class="w-full bg-gray-200 rounded-full h-2 mt-4">
                    <div class="step-indicator h-2 rounded-full" style="width: 50%"></div>
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

                <form method="POST" action="{{ route('vendor.registration.company.submit') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $user->id }}">

                    <!-- Company Name -->
                    <div class="input-group">
                        <input
                            id="name"
                            name="name"
                            type="text"
                            required
                            placeholder=" "
                            value="{{ old('name') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300"
                        >
                        <label for="name" class="text-gray-500">Company Name *</label>
                    </div>

                    <!-- Company Email -->
                    <div class="input-group">
                        <input
                            id="email"
                            name="email"
                            type="email"
                            required
                            placeholder=" "
                            value="{{ old('email') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300"
                        >
                        <label for="email" class="text-gray-500">Company Email *</label>
                    </div>

                    <!-- Contact Numbers -->
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="input-group">
                            <input
                                id="contact_number_1"
                                name="contact_number_1"
                                type="tel"
                                required
                                placeholder=" "
                                value="{{ old('contact_number_1') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300"
                            >
                            <label for="contact_number_1" class="text-gray-500">Primary Contact *</label>
                        </div>

                        <div class="input-group">
                            <input
                                id="contact_number_2"
                                name="contact_number_2"
                                type="tel"
                                placeholder=" "
                                value="{{ old('contact_number_2') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300"
                            >
                            <label for="contact_number_2" class="text-gray-500">Secondary Contact</label>
                        </div>
                    </div>

                    <!-- Address Information -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">Address Information</h3>

                        <!-- Address -->
                        <div class="input-group">
                            <textarea
                                id="address"
                                name="address"
                                required
                                placeholder=" "
                                rows="3"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300 resize-none"
                            >{{ old('address') }}</textarea>
                            <label for="address" class="text-gray-500">Full Address *</label>
                        </div>

                        <!-- Emirate and City -->
                        <div class="grid md:grid-cols-2 gap-4">
                            <div class="input-group">
                                <select
                                    id="emirate"
                                    name="emirate"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300"
                                >
                                    <option value="">Select Emirate</option>
                                    <option value="Abu Dhabi" {{ old('emirate') == 'Abu Dhabi' ? 'selected' : '' }}>Abu Dhabi</option>
                                    <option value="Dubai" {{ old('emirate') == 'Dubai' ? 'selected' : '' }}>Dubai</option>
                                    <option value="Sharjah" {{ old('emirate') == 'Sharjah' ? 'selected' : '' }}>Sharjah</option>
                                    <option value="Ajman" {{ old('emirate') == 'Ajman' ? 'selected' : '' }}>Ajman</option>
                                    <option value="Umm Al Quwain" {{ old('emirate') == 'Umm Al Quwain' ? 'selected' : '' }}>Umm Al Quwain</option>
                                    <option value="Ras Al Khaimah" {{ old('emirate') == 'Ras Al Khaimah' ? 'selected' : '' }}>Ras Al Khaimah</option>
                                    <option value="Fujairah" {{ old('emirate') == 'Fujairah' ? 'selected' : '' }}>Fujairah</option>
                                </select>
                                <label for="emirate" class="text-gray-500">Emirate *</label>
                            </div>

                            <div class="input-group">
                                <input
                                    id="city"
                                    name="city"
                                    type="text"
                                    required
                                    placeholder=" "
                                    value="{{ old('city') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300"
                                >
                                <label for="city" class="text-gray-500">City *</label>
                            </div>
                        </div>

                        <!-- Street -->
                        <div class="input-group">
                            <input
                                id="street"
                                name="street"
                                type="text"
                                placeholder=" "
                                value="{{ old('street') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300"
                            >
                            <label for="street" class="text-gray-500">Street (Optional)</label>
                        </div>
                    </div>

                    <!-- Business Information -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">Business Information</h3>

                        <!-- Delivery Capability -->
                        <div class="flex items-center space-x-3">
                            <input
                                id="delivery_capability"
                                name="delivery_capability"
                                type="checkbox"
                                value="1"
                                {{ old('delivery_capability') ? 'checked' : '' }}
                                class="w-4 h-4 text-purple-600 bg-gray-100 border-gray-300 rounded focus:ring-purple-500 focus:ring-2"
                            >
                            <label for="delivery_capability" class="text-gray-700 font-medium">
                                We offer delivery services
                            </label>
                        </div>

                        <!-- Description -->
                        <div class="input-group">
                            <textarea
                                id="description"
                                name="description"
                                placeholder=" "
                                rows="4"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300 resize-none"
                            >{{ old('description') }}</textarea>
                            <label for="description" class="text-gray-500">Company Description (Optional)</label>
                        </div>

                        <!-- Logo Upload -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Company Logo (Optional)</label>
                            <div class="file-upload-area rounded-lg p-6 text-center">
                                <input
                                    type="file"
                                    id="logo"
                                    name="logo"
                                    accept="image/*"
                                    class="hidden"
                                    onchange="handleFileSelect(this)"
                                >
                                <div id="upload-content">
                                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                                    <p class="text-gray-600 mb-2">Click to upload or drag and drop</p>
                                    <p class="text-sm text-gray-500">PNG, JPG, GIF up to 2MB</p>
                                </div>
                                <div id="file-preview" class="hidden">
                                    <img id="preview-image" class="mx-auto max-h-32 rounded-lg mb-2" alt="Logo preview">
                                    <p id="file-name" class="text-sm text-gray-600"></p>
                                </div>
                                <button
                                    type="button"
                                    onclick="document.getElementById('logo').click()"
                                    class="mt-4 bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors duration-300"
                                >
                                    Choose File
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 text-white py-3 px-6 rounded-lg font-semibold hover:from-purple-700 hover:to-indigo-700 transition-all duration-300 transform hover:scale-105"
                    >
                        Continue to License Upload
                        <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </form>

                <!-- Back Button -->
                <div class="text-center mt-6">
                    <a href="{{ route('register.vendor') }}" class="text-gray-600 hover:text-purple-600 transition-colors duration-300">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to personal information
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function handleFileSelect(input) {
            const file = input.files[0];
            const uploadContent = document.getElementById('upload-content');
            const filePreview = document.getElementById('file-preview');
            const previewImage = document.getElementById('preview-image');
            const fileName = document.getElementById('file-name');

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    fileName.textContent = file.name;
                    uploadContent.classList.add('hidden');
                    filePreview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        }

        // Drag and drop functionality
        const uploadArea = document.querySelector('.file-upload-area');

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
                document.getElementById('logo').files = files;
                handleFileSelect(document.getElementById('logo'));
            }
        });
    </script>
</body>
</html>