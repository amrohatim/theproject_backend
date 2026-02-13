@extends('layouts.dashboard')

@section('title', 'Create Business Type')
@section('page-title', 'Create Business Type')

@section('content')
<div class="container mx-auto">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Create Business Type</h2>
                <p class="mt-1 text-gray-600 dark:text-gray-400">Add a new business type for vendor branches</p>
            </div>
            <div>
                <a href="{{ route('admin.business-types.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-arrow-left mr-2"></i> Back to List
                </a>
            </div>
        </div>
    </div>

    @if($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <strong class="font-bold">Error!</strong>
        <ul class="mt-2 list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
        <form action="{{ route('admin.business-types.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            
            <div class="space-y-6">
                <div>
                    <label for="business_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Business Name <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1">
                        <input type="text" 
                               name="business_name" 
                               id="business_name" 
                               value="{{ old('business_name') }}"
                               class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('business_name') border-red-500 @enderror" 
                               placeholder="Enter business type name"
                               required>
                    </div>
                    @error('business_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Enter a unique business type name (e.g., Restaurant, Retail Store, Beauty Salon, etc.)
                    </p>
                </div>

                <div>
                    <label for="name_arabic" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Business Name (Arabic)
                    </label>
                    <div class="mt-1">
                        <input type="text"
                               name="name_arabic"
                               id="name_arabic"
                               value="{{ old('name_arabic') }}"
                               class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('name_arabic') border-red-500 @enderror"
                               placeholder="أدخل اسم نوع العمل بالعربية"
                               dir="rtl">
                    </div>
                    @error('name_arabic')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Enter the Arabic translation of the business type name (optional)
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Business Type Image</label>

                    <!-- Image Preview Container (initially hidden) -->
                    <div id="imagePreviewContainer" class="mt-2 mb-4 hidden">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                <img id="imagePreview" class="h-20 w-20 rounded-lg object-cover border border-gray-300 dark:border-gray-600"
                                     src="" alt="Preview">
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-green-600 dark:text-green-400">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Image selected successfully
                                </p>
                                <p id="fileInfo" class="text-xs text-gray-500 dark:text-gray-400"></p>
                                <button type="button" id="removeImage" class="mt-1 text-xs text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                    <i class="fas fa-times mr-1"></i>Remove image
                                </button>
                            </div>
                        </div>
                    </div>

                    <div id="uploadArea" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-md transition-colors duration-200">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600 dark:text-gray-400">
                                <label for="image" class="relative cursor-pointer bg-white dark:bg-gray-700 rounded-md font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                    <span>Upload a file</span>
                                    <input id="image" name="image" type="file" class="sr-only" accept="image/*">
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                PNG, JPG, GIF, SVG up to 20MB
                            </p>
                        </div>
                    </div>
                    @error('image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Optional: Upload an icon or image to represent this business type
                    </p>
                </div>

                <!-- Product Categories Section -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                        Product Categories
                    </label>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                        Select categories that are relevant for products in this business type
                    </p>

                    @if($productCategories->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 max-h-60 overflow-y-auto border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                            @foreach($productCategories as $category)
                                <label class="flex items-center space-x-3 p-2 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                                    <input type="checkbox"
                                           name="product_categories[]"
                                           value="{{ $category->id }}"
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">
                                        {{ $category->name }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <div class="text-sm text-gray-500 dark:text-gray-400 p-4 border border-gray-200 dark:border-gray-600 rounded-lg">
                            No product categories available.
                        </div>
                    @endif
                </div>

                <!-- Service Categories Section -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                        Service Categories
                    </label>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                        Select categories that are relevant for services in this business type
                    </p>

                    @if($serviceCategories->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 max-h-60 overflow-y-auto border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                            @foreach($serviceCategories as $category)
                                <label class="flex items-center space-x-3 p-2 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                                    <input type="checkbox"
                                           name="service_categories[]"
                                           value="{{ $category->id }}"
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">
                                        {{ $category->name }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <div class="text-sm text-gray-500 dark:text-gray-400 p-4 border border-gray-200 dark:border-gray-600 rounded-lg">
                            No service categories available.
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-8 flex justify-end space-x-3">
                <a href="{{ route('admin.business-types.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-save mr-2"></i>
                    Create Business Type
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Focus on the business name input
        document.getElementById('business_name').focus();

        // Image upload functionality
        const imageInput = document.getElementById('image');
        const imagePreviewContainer = document.getElementById('imagePreviewContainer');
        const imagePreview = document.getElementById('imagePreview');
        const fileInfo = document.getElementById('fileInfo');
        const removeImageBtn = document.getElementById('removeImage');
        const uploadArea = document.getElementById('uploadArea');

        // Handle file selection
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                handleImageSelection(file);
            }
        });

        // Handle drag and drop
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            uploadArea.classList.add('border-indigo-500', 'bg-indigo-50', 'dark:bg-indigo-900/20');
        });

        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            uploadArea.classList.remove('border-indigo-500', 'bg-indigo-50', 'dark:bg-indigo-900/20');
        });

        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            uploadArea.classList.remove('border-indigo-500', 'bg-indigo-50', 'dark:bg-indigo-900/20');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                const file = files[0];
                if (file.type.startsWith('image/')) {
                    // Set the file to the input
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    imageInput.files = dt.files;
                    handleImageSelection(file);
                }
            }
        });

        // Remove image functionality
        removeImageBtn.addEventListener('click', function() {
            imageInput.value = '';
            imagePreviewContainer.classList.add('hidden');
            uploadArea.classList.remove('hidden');
        });

        function handleImageSelection(file) {
            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/svg+xml'];
            if (!allowedTypes.includes(file.type)) {
                alert('Please select a valid image file (PNG, JPG, GIF, SVG).');
                return;
            }

            // Validate file size (20MB)
            const maxSize = 20 * 1024 * 1024;
            if (file.size > maxSize) {
                alert('File size must be less than 20MB.');
                return;
            }

            // Create preview
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;

                // Format file size
                const fileSize = formatFileSize(file.size);
                fileInfo.textContent = `${file.name} (${fileSize})`;

                // Show preview, hide upload area
                imagePreviewContainer.classList.remove('hidden');
                uploadArea.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // Add client-side validation
        const form = document.querySelector('form');
        const businessNameInput = document.getElementById('business_name');

        form.addEventListener('submit', function(e) {
            const businessName = businessNameInput.value.trim();

            if (!businessName) {
                e.preventDefault();
                alert('Please enter a business name.');
                businessNameInput.focus();
                return false;
            }

            if (businessName.length < 2) {
                e.preventDefault();
                alert('Business name must be at least 2 characters long.');
                businessNameInput.focus();
                return false;
            }

            if (businessName.length > 255) {
                e.preventDefault();
                alert('Business name must not exceed 255 characters.');
                businessNameInput.focus();
                return false;
            }
        });

        // Real-time character count
        businessNameInput.addEventListener('input', function() {
            const length = this.value.length;
            const maxLength = 255;

            // Remove existing character count if any
            const existingCount = document.getElementById('char-count');
            if (existingCount) {
                existingCount.remove();
            }

            // Add character count
            const charCount = document.createElement('p');
            charCount.id = 'char-count';
            charCount.className = 'mt-1 text-sm text-gray-500 dark:text-gray-400';
            charCount.textContent = `${length}/${maxLength} characters`;

            if (length > maxLength) {
                charCount.className = 'mt-1 text-sm text-red-600';
            }

            this.parentNode.appendChild(charCount);
        });
    });
</script>
@endsection
