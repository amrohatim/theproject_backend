@props([
    'name' => 'image',
    'label' => 'Image',
    'currentImage' => null,
    'required' => false,
    'accept' => 'image/*',
    'maxSize' => '2MB',
    'allowedFormats' => 'PNG, JPG, JPEG, GIF',
    'previewMaxHeight' => '200px',
    'containerClass' => '',
    'error' => null
])

<div class="image-upload-wrapper {{ $containerClass }}">
    <label class="form-label" style="color: var(--discord-lightest); font-weight: 600; margin-bottom: 8px;">
        {{ $label }}{{ $required ? ' *' : '' }}
    </label>
    
    <div class="image-upload-container" 
         data-name="{{ $name }}"
         style="border: 2px dashed var(--discord-darkest); border-radius: 8px; padding: 20px; text-align: center; background-color: var(--discord-dark); cursor: pointer; transition: border-color 0.3s ease;">
        
        <!-- Image Preview -->
        <div class="image-preview" style="{{ $currentImage ? '' : 'display: none;' }}">
            <img class="preview-img" 
                 src="{{ $currentImage }}" 
                 alt="Preview" 
                 style="max-width: 100%; max-height: {{ $previewMaxHeight }}; border-radius: 8px; margin-bottom: 12px;">
            <div>
                <button type="button" 
                        class="remove-image btn btn-sm" 
                        style="background-color: var(--discord-red); color: white; border: none; padding: 4px 12px; border-radius: 4px;">
                    <i class="fas fa-trash me-1"></i> Remove
                </button>
            </div>
        </div>
        
        <!-- Upload Placeholder -->
        <div class="upload-placeholder" style="{{ $currentImage ? 'display: none;' : '' }}">
            <i class="fas fa-cloud-upload-alt" style="font-size: 48px; color: var(--discord-light); margin-bottom: 12px;"></i>
            <div style="color: var(--discord-light); margin-bottom: 12px;">
                Click to upload or drag and drop
            </div>
            <div style="color: var(--discord-light); font-size: 12px;">
                {{ $allowedFormats }} up to {{ $maxSize }}
            </div>
        </div>
        
        <!-- Hidden File Input -->
        <input type="file" 
               name="{{ $name }}" 
               class="image-input" 
               accept="{{ $accept }}" 
               style="display: none;"
               {{ $required ? 'required' : '' }}>
    </div>
    
    <!-- Error Display -->
    @if($error)
        <div class="image-error" style="color: var(--discord-red); font-size: 12px; margin-top: 4px;">{{ $error }}</div>
    @endif
    
    <!-- Dynamic Error Container -->
    <div class="upload-error-container" style="display: none;"></div>
</div>

@push('scripts')
<script>
(function() {
    'use strict';

    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initImageUpload);
    } else {
        initImageUpload();
    }

    function initImageUpload() {
        const container = document.querySelector('[data-name="{{ $name }}"]');

        if (!container || container.dataset.imageUploadInitialized) {
            return;
        }

        // Mark as initialized
        container.dataset.imageUploadInitialized = 'true';

        const imageInput = container.querySelector('.image-input');
        const imagePreview = container.querySelector('.image-preview');
        const uploadPlaceholder = container.querySelector('.upload-placeholder');
        const previewImg = container.querySelector('.preview-img');
        const removeImageBtn = container.querySelector('.remove-image');
        const errorContainer = container.querySelector('.upload-error-container');

        // Click to upload
        container.addEventListener('click', function(e) {
            // Don't trigger if clicking on remove button
            if (e.target.closest('.remove-image')) {
                return;
            }

            // Don't trigger if preview is visible
            if (imagePreview && imagePreview.style.display !== 'none') {
                return;
            }

            // Don't trigger if clicking on the file input itself
            if (e.target === imageInput) {
                return;
            }

            e.preventDefault();
            e.stopPropagation();

            // Trigger file input click
            if (imageInput) {
                imageInput.click();
            }
        });

        // Drag and drop functionality
        container.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.style.borderColor = 'var(--discord-primary)';
        });

        container.addEventListener('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.style.borderColor = 'var(--discord-darkest)';
        });

        container.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.style.borderColor = 'var(--discord-darkest)';

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleFileSelection(files[0]);
            }
        });

        // Handle file selection
        if (imageInput) {
            imageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    handleFileSelection(file);
                }
            });
        }

        // Remove image functionality
        if (removeImageBtn) {
            removeImageBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                if (imageInput) imageInput.value = '';
                if (previewImg) previewImg.src = '';
                if (imagePreview) imagePreview.style.display = 'none';
                if (uploadPlaceholder) uploadPlaceholder.style.display = '';
                clearError();
            });
        }

        // File selection handler with validation
        function handleFileSelection(file) {
            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
            if (!allowedTypes.includes(file.type)) {
                showError('Please select a valid image file (JPEG, PNG, JPG, GIF).');
                return;
            }

            // Validate file size (2MB limit)
            const maxSize = 2 * 1024 * 1024; // 2MB in bytes
            if (file.size > maxSize) {
                const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
                showError(`File size (${fileSizeMB}MB) exceeds the 2MB limit. Please choose a smaller image.`);
                return;
            }

            // Clear any existing errors
            clearError();

            // Show upload progress
            showUploadProgress();

            const reader = new FileReader();
            reader.onload = function(e) {
                if (previewImg) previewImg.src = e.target.result;
                if (uploadPlaceholder) uploadPlaceholder.style.display = 'none';
                if (imagePreview) imagePreview.style.display = '';
                hideUploadProgress();
            };
            reader.readAsDataURL(file);
        }

        // Show error message
        function showError(message) {
            if (errorContainer) {
                errorContainer.innerHTML = '<div style="color: var(--discord-red); font-size: 12px; margin-top: 8px; padding: 8px; background-color: rgba(220, 38, 38, 0.1); border-radius: 4px;">' + message + '</div>';
                errorContainer.style.display = '';

                // Auto-hide error after 5 seconds
                setTimeout(() => {
                    errorContainer.style.display = 'none';
                }, 5000);
            }
        }

        // Clear error message
        function clearError() {
            if (errorContainer) {
                errorContainer.style.display = 'none';
                errorContainer.innerHTML = '';
            }
        }

        // Show upload progress
        function showUploadProgress() {
            if (uploadPlaceholder) {
                uploadPlaceholder.innerHTML = '<div style="color: var(--discord-primary);"><i class="fas fa-spinner fa-spin" style="font-size: 24px; margin-bottom: 8px;"></i><div>Uploading...</div></div>';
            }
        }

        // Hide upload progress
        function hideUploadProgress() {
            if (uploadPlaceholder) {
                uploadPlaceholder.innerHTML = '<i class="fas fa-cloud-upload-alt" style="font-size: 48px; color: var(--discord-light); margin-bottom: 12px;"></i><div style="color: var(--discord-light); margin-bottom: 12px;">Click to upload or drag and drop</div><div style="color: var(--discord-light); font-size: 12px;">{{ $allowedFormats }} up to {{ $maxSize }}</div>';
            }
        }
    }
})();
</script>
@endpush
