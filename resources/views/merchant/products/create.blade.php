@extends('layouts.merchant')

@section('title', 'Add New Product')
@section('header', 'Add New Product')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/mdbassit/Coloris@latest/dist/coloris.min.css"/>
<style>
    .image-preview-container {
        transition: all 0.3s ease;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    }

    .image-preview-container:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transform: translateY(-1px);
    }

    .image-preview-container.has-image {
        border-color: var(--discord-primary) !important;
        background-color: rgba(88, 101, 242, 0.1) !important;
        box-shadow: 0 4px 6px -1px rgba(88, 101, 242, 0.1), 0 2px 4px -1px rgba(88, 101, 242, 0.06);
    }

    .image-preview {
        transition: opacity 0.3s ease;
    }

    .image-placeholder {
        transition: opacity 0.3s ease;
    }

    .section-card {
        transition: all 0.3s ease;
        margin-bottom: 2rem;
        background-color: var(--discord-dark);
        border: 1px solid var(--discord-darker);
        border-radius: 8px;
    }

    .section-card:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    .color-swatch {
        cursor: pointer;
        transition: transform 0.2s;
    }

    .color-swatch:hover {
        transform: scale(1.1) translateY(-50%) !important;
    }

    /* Coloris customization for Discord theme */
    .clr-field button {
        width: 28px;
        height: 28px;
        left: auto;
        right: 8px;
        border-radius: 5px;
    }

    /* Discord-themed form controls */
    .form-control, .form-select {
        background-color: var(--discord-darkest) !important;
        border: 1px solid var(--discord-darkest) !important;
        color: var(--discord-lightest) !important;
    }

    .form-control:focus, .form-select:focus {
        background-color: var(--discord-darkest) !important;
        border-color: var(--discord-primary) !important;
        color: var(--discord-lightest) !important;
        box-shadow: 0 0 0 0.2rem rgba(88, 101, 242, 0.25) !important;
    }
</style>
@endsection

@section('content')
<!-- Header Section -->
<div class="discord-card">
    <div class="discord-card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 style="margin: 0; color: var(--discord-lightest); font-size: 24px; font-weight: 600;">
                    <i class="fas fa-plus me-2" style="color: var(--discord-primary);"></i>
                    Add New Product
                </h2>
                <p style="margin: 8px 0 0 0; color: var(--discord-light); font-size: 14px;">
                    Create a new product with colors, sizes, and specifications
                </p>
            </div>
            <div>
                <a href="{{ route('merchant.products.index') }}" class="discord-btn discord-btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Products
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Product Form -->
<div class="discord-card">
    <div class="discord-card-body">
        <form action="{{ route('merchant.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Section Navigation -->
            <div class="mb-4">
                <div style="border-bottom: 1px solid var(--discord-darker); padding-bottom: 12px;">
                    <h3 style="color: var(--discord-lightest); font-size: 18px; font-weight: 600; margin: 0;">
                        Product Information Sections
                    </h3>
                    <p style="color: var(--discord-light); font-size: 14px; margin: 8px 0 0 0;">
                        All sections are displayed below for easy editing
                    </p>
                </div>
            </div>

            <!-- Basic Information Section -->
            <div id="basic-panel" class="section-card p-4 mb-4">
                <div style="border-bottom: 1px solid var(--discord-darker); padding-bottom: 16px; margin-bottom: 24px;">
                    <h3 style="color: var(--discord-lightest); font-size: 20px; font-weight: 600; margin: 0;">
                        1. Basic Information
                    </h3>
                </div>
                <div class="row">
                    <!-- Basic Information -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label" style="color: var(--discord-lightest); font-weight: 600;">
                                Product Name <span style="color: var(--discord-red);">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}"
                                   class="form-control @error('name') is-invalid @enderror" required>
                            @error('name')
                                <div style="color: var(--discord-red); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div class="mb-3">
                            <label for="category_id" class="form-label" style="color: var(--discord-lightest); font-weight: 600;">
                                Category <span style="color: var(--discord-red);">*</span>
                            </label>
                            <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">Select Category</option>
                                @foreach($parentCategories ?? [] as $parentCategory)
                                    <optgroup label="{{ $parentCategory->name }}">
                                        @foreach($parentCategory->children as $childCategory)
                                            <option value="{{ $childCategory->id }}" {{ old('category_id') == $childCategory->id ? 'selected' : '' }}>
                                                &nbsp;&nbsp;{{ $childCategory->name }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div style="color: var(--discord-red); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Branch -->
                        <div class="mb-3">
                            <label for="branch_id" class="form-label" style="color: var(--discord-lightest); font-weight: 600;">
                                Branch <span style="color: var(--discord-red);">*</span>
                            </label>
                            <select id="branch_id" name="branch_id" class="form-select @error('branch_id') is-invalid @enderror" required>
                                <option value="">Select Branch</option>
                                @foreach($branches ?? [] as $branch)
                                    <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('branch_id')
                                <div style="color: var(--discord-red); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label" style="color: var(--discord-lightest); font-weight: 600;">
                                Description
                            </label>
                            <textarea id="description" name="description" rows="4"
                                      class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <div style="color: var(--discord-red); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Pricing and Inventory -->
                    <div class="col-md-6">
                        <h4 style="color: var(--discord-lightest); font-size: 16px; font-weight: 600; margin-bottom: 16px;">
                            Pricing & Inventory
                        </h4>

                        <!-- Price -->
                        <div class="mb-3">
                            <label for="price" class="form-label" style="color: var(--discord-lightest); font-weight: 600;">
                                Price <span style="color: var(--discord-red);">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text" style="background-color: var(--discord-darker); border: 1px solid var(--discord-darkest); color: var(--discord-light);">$</span>
                                <input type="number" name="price" id="price" min="0" step="0.01" value="{{ old('price') }}"
                                       class="form-control @error('price') is-invalid @enderror" placeholder="0.00" required>
                            </div>
                            @error('price')
                                <div style="color: var(--discord-red); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Original Price -->
                        <div class="mb-3">
                            <label for="original_price" class="form-label" style="color: var(--discord-lightest); font-weight: 600;">
                                Original Price (if on sale)
                            </label>
                            <div class="input-group">
                                <span class="input-group-text" style="background-color: var(--discord-darker); border: 1px solid var(--discord-darkest); color: var(--discord-light);">$</span>
                                <input type="number" name="original_price" id="original_price" min="0" step="0.01" value="{{ old('original_price') }}"
                                       class="form-control @error('original_price') is-invalid @enderror" placeholder="0.00">
                            </div>
                            @error('original_price')
                                <div style="color: var(--discord-red); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Stock -->
                        <div class="mb-3">
                            <label for="stock" class="form-label" style="color: var(--discord-lightest); font-weight: 600;">
                                General Stock <span style="color: var(--discord-red);">*</span>
                            </label>
                            <input type="number" name="stock" id="stock" min="0" value="{{ old('stock', 0) }}"
                                   class="form-control @error('stock') is-invalid @enderror" required>
                            <small style="color: var(--discord-light); font-size: 12px;">
                                Total stock quantity available for all color variants
                            </small>
                            @error('stock')
                                <div style="color: var(--discord-red); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                            @enderror

                            <!-- Stock Allocation Summary -->
                            <div id="stock-summary" class="mt-3 p-3 rounded" style="background-color: var(--discord-darker); border: 1px solid var(--discord-darkest); display: none;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span style="color: var(--discord-lightest); font-weight: 600; font-size: 14px;">Stock Allocation</span>
                                    <span id="stock-status-badge" class="badge"></span>
                                </div>
                                <div style="font-size: 12px; color: var(--discord-light);">
                                    <div class="d-flex justify-content-between">
                                        <span>Total Available:</span>
                                        <span id="total-available-stock" style="font-weight: 600;">0</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Allocated to Colors:</span>
                                        <span id="total-allocated-stock" style="font-weight: 600;">0</span>
                                    </div>
                                    <div class="d-flex justify-content-between" style="border-top: 1px solid var(--discord-darkest); padding-top: 4px; margin-top: 4px;">
                                        <span>Remaining:</span>
                                        <span id="remaining-stock" style="font-weight: 600;"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Availability -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input id="is_available" name="is_available" type="checkbox" class="form-check-input"
                                       {{ old('is_available', '1') == '1' ? 'checked' : '' }} value="1">
                                <label for="is_available" class="form-check-label" style="color: var(--discord-lightest); font-weight: 600;">
                                    Available for purchase
                                </label>
                                <div style="color: var(--discord-light); font-size: 12px;">
                                    Uncheck if this product is not available for purchase.
                                </div>
                            </div>
                            @error('is_available')
                                <div style="color: var(--discord-red); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Note about product image -->
                        <div class="alert" style="background-color: rgba(88, 101, 242, 0.1); border: 1px solid var(--discord-primary); border-radius: 6px; padding: 12px;">
                            <div class="d-flex">
                                <div style="color: var(--discord-primary); margin-right: 8px;">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                <div style="color: var(--discord-lightest); font-size: 14px;">
                                    Product images must be associated with colors. Please add at least one color with an image in the Colors section below.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colors Section -->
            <div id="colors-panel" class="section-card p-4 mb-4">
                <div style="border-bottom: 1px solid var(--discord-darker); padding-bottom: 16px; margin-bottom: 24px;">
                    <h3 style="color: var(--discord-lightest); font-size: 20px; font-weight: 600; margin: 0;">
                        2. Product Colors, Images, and Sizes
                    </h3>
                </div>
                <div class="mb-4">
                    <div class="alert" style="background-color: rgba(255, 193, 7, 0.1); border: 1px solid #ffc107; border-radius: 6px; padding: 12px;">
                        <div class="d-flex">
                            <div style="color: #ffc107; margin-right: 8px;">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div style="color: var(--discord-lightest); font-size: 14px;">
                                <strong>Required:</strong> Each product must have at least one color with an associated image. The color marked as default will have its image used as the main product image.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-4 d-flex justify-content-between align-items-center">
                    <p style="color: var(--discord-light); font-size: 14px; margin: 0;">
                        Add color options with images, sizes, and stock allocation. Each color can have its own size variants.
                    </p>
                    <div class="position-relative">
                        <button type="button" id="add-color" class="discord-btn bg">
                            <i class="fas fa-plus me-1"></i> Add Color
                        </button>
                        <!-- Tooltip for disabled state -->
                        <div id="add-color-tooltip" class="position-absolute" style="bottom: 100%; left: 50%; transform: translateX(-50%); margin-bottom: 8px; padding: 6px 12px; font-size: 12px; color: white; background-color: rgba(0,0,0,0.8); border-radius: 4px; opacity: 0; pointer-events: none; transition: opacity 0.2s; white-space: nowrap; display: none;">
                            All available stock has been allocated to color variants
                            <div style="position: absolute; top: 100%; left: 50%; transform: translateX(-50%); border: 4px solid transparent; border-top-color: rgba(0,0,0,0.8);"></div>
                        </div>
                    </div>
                </div>

                <div id="colors-container" class="mb-4">
                    <div class="color-item p-3 mb-3 rounded">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label" style="color: var(--discord-lightest); font-weight: 600;">
                                    Color Name <span style="color: var(--discord-red);">*</span>
                                </label>
                                <select name="colors[0][name]" class="color-name-select form-select" required>
                                    <option value="">Select Color</option>
                                    <option value="Red">Red</option>
                                    <option value="Blue">Blue</option>
                                    <option value="Green">Green</option>
                                    <option value="Yellow">Yellow</option>
                                    <option value="Orange">Orange</option>
                                    <option value="Purple">Purple</option>
                                    <option value="Pink">Pink</option>
                                    <option value="Brown">Brown</option>
                                    <option value="Black">Black</option>
                                    <option value="White">White</option>
                                    <option value="Gray">Gray</option>
                                    <option value="Silver">Silver</option>
                                    <option value="Gold">Gold</option>
                                    <option value="Navy">Navy</option>
                                    <option value="Maroon">Maroon</option>
                                    <option value="Teal">Teal</option>
                                    <option value="Olive">Olive</option>
                                    <option value="Lime">Lime</option>
                                    <option value="Aqua">Aqua</option>
                                    <option value="Fuchsia">Fuchsia</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label" style="color: var(--discord-lightest); font-weight: 600;">Color Code</label>
                                <input type="text" name="colors[0][color_code]" placeholder="#FF0000" class="form-control">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label" style="color: var(--discord-lightest); font-weight: 600;">Price Adjustment</label>
                                <input type="number" step="0.01" name="colors[0][price_adjustment]" placeholder="0.00" value="0" class="form-control">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label" style="color: var(--discord-lightest); font-weight: 600;">Total Stock</label>
                                <input type="number" name="colors[0][stock]" placeholder="10" value="0" min="0"
                                       class="color-stock-input form-control" style="transition: all 0.2s;">
                                <small style="color: var(--discord-light); font-size: 12px;">Total stock to allocate across sizes</small>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label" style="color: var(--discord-lightest); font-weight: 600;">Display Order</label>
                                <input type="number" name="colors[0][display_order]" placeholder="0" value="0" class="form-control">
                            </div>
                            <div class="col-md-1 mb-3 d-flex align-items-end justify-content-center">
                                <button type="button" class="remove-item btn btn-link text-danger p-0">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label" style="color: var(--discord-lightest); font-weight: 600;">
                                        Color Image <span style="color: var(--discord-red);">*</span>
                                    </label>

                                    <!-- Image Preview Container -->
                                    <div class="image-preview-container mb-3 d-flex align-items-center justify-content-center position-relative"
                                         style="width: 300px; height: 400px; border: 2px dashed var(--discord-darker); border-radius: 8px; background-color: var(--discord-darkest); overflow: hidden;">
                                        <img class="image-preview d-none" style="width: 100%; height: 100%; object-fit: cover; border-radius: 6px;" alt="Image Preview">
                                        <div class="image-placeholder text-center">
                                            <i class="fas fa-image" style="color: var(--discord-light); font-size: 48px; margin-bottom: 8px;"></i>
                                            <p style="color: var(--discord-light); font-size: 14px; margin: 0;">No image selected</p>
                                            <p style="color: var(--discord-light); font-size: 12px; margin: 4px 0 0 0;">300x400px preview</p>
                                        </div>
                                    </div>

                                    <input type="file" name="color_images[0]" class="color-image-input form-control mb-2" required accept="image/*" style="max-width: 300px;">
                                    <input type="hidden" name="colors[0][image]" value="">

                                    <!-- Error message container for image validation -->
                                    <div class="image-error-message d-none mt-2 p-3 rounded" style="max-width: 300px; background-color: rgba(220, 53, 69, 0.1); border: 1px solid #dc3545;">
                                        <div class="d-flex">
                                            <div style="color: #dc3545; margin-right: 8px;">
                                                <i class="fas fa-exclamation-circle"></i>
                                            </div>
                                            <div>
                                                <p class="error-text" style="color: #dc3545; font-size: 14px; margin: 0;"></p>
                                            </div>
                                        </div>
                                    </div>

                                    <small style="color: var(--discord-light); font-size: 12px; max-width: 300px; display: block;">
                                        PNG, JPG, GIF up to 2MB
                                    </small>
                                </div>

                                <div class="col-md-6">
                                    <!-- Default Color Checkbox -->
                                    <div class="mt-2">
                                        <label class="form-label" style="color: var(--discord-lightest); font-weight: 600; margin-bottom: 8px;">Default Color</label>
                                        <div class="form-check">
                                            <input type="checkbox" name="colors[0][is_default]" value="1" class="default-color-checkbox form-check-input" checked>
                                            <label class="form-check-label" style="color: var(--discord-light); font-size: 14px;">
                                                This image will be the main product image
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Specifications Section -->
            <div id="specifications-panel" class="section-card lighgreen p-4 mb-4">
                <div style="border-bottom: 1px solid lighgreen; padding-bottom: 16px; margin-bottom: 24px;">
                    <h3 style="color: var(--discord-lightest); font-size: 20px; font-weight: 600; margin: 0;">
                        3. Product Specifications
                    </h3>
                    <p style="color: var(--discord-light); font-size: 14px; margin: 8px 0 0 0;">
                        Add key-value specifications for your product
                    </p>
                </div>
                <div class="mb-4 d-flex justify-content-between align-items-center">
                    <p style="color: var(--discord-light); font-size: 14px; margin: 0;">
                        Add technical specifications, features, or other product details
                    </p>
                    <button type="button" id="add-specification" class="discord-btn">
                        <i class="fas fa-plus me-1"></i> Add Specification
                    </button>
                </div>

                <div id="specifications-container">
                    <div class="specification-item row mb-3 align-items-center">
                        <div class="col-md-5">
                            <label class="form-label" style="color: var(--discord-lightest); font-weight: 600;">Key</label>
                            <input type="text" name="specifications[0][key]" placeholder="Material" class="form-control">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label" style="color: var(--discord-lightest); font-weight: 600;">Value</label>
                            <input type="text" name="specifications[0][value]" placeholder="Cotton" class="form-control">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label" style="color: var(--discord-lightest); font-weight: 600;">Order</label>
                            <input type="number" name="specifications[0][display_order]" placeholder="0" value="0" class="form-control">
                        </div>
                        <div class="col-md-1 d-flex align-items-end justify-content-center">
                            <button type="button" class="remove-item btn btn-link text-danger p-0">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-4 d-flex justify-content-end gap-2">
                <a href="{{ route('merchant.products.index') }}" class="discord-btn discord-btn-secondary">
                    <i class="fas fa-times me-1"></i> Cancel
                </a>
                <button type="submit" class="discord-btn">
                    <i class="fas fa-save me-1"></i> Save Product
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/gh/mdbassit/Coloris@latest/dist/coloris.min.js"></script>
<script src="{{ asset('js/color-picker.js') }}"></script>
<script src="{{ asset('js/enhanced-size-selection.js') }}"></script>
<script src="{{ asset('js/enhanced-color-selection.js') }}"></script>
<script src="{{ asset('js/color-specific-size-selection.js') }}"></script>
<script src="{{ asset('js/dynamic-color-size-management.js') }}"></script>
<script src="{{ asset('js/merchant-stock-validation.js') }}"></script>
@endpush

@section('scripts')
<!-- Color Picker Scripts - Load directly to ensure they work -->
<script src="https://cdn.jsdelivr.net/gh/mdbassit/Coloris@latest/dist/coloris.min.js"></script>
<script src="{{ asset('js/color-picker.js') }}"></script>
<script>
    function previewImage(input) {
        const preview = document.getElementById('image-preview');
        const placeholder = document.getElementById('image-placeholder');
        const fileNameElement = document.getElementById('file-name');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                // Show preview
                preview.classList.remove('d-none');
                preview.querySelector('img').src = e.target.result;

                // Hide placeholder
                placeholder.classList.add('d-none');

                // Update file name
                fileNameElement.textContent = input.files[0].name;
            }

            reader.readAsDataURL(input.files[0]);
        } else {
            // Hide preview
            preview.classList.add('d-none');

            // Show placeholder
            placeholder.classList.remove('d-none');

            // Reset file name
            fileNameElement.textContent = 'or drag and drop';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Image preview functionality
        function setupImagePreview() {
            // Handle existing and new color image inputs
            document.querySelectorAll('.color-image-input').forEach(function(input) {
                // Remove existing event listeners to prevent duplicates
                input.removeEventListener('change', handleImagePreview);
                // Add event listener
                input.addEventListener('change', handleImagePreview);
            });
        }

        function handleImagePreview(event) {
            const input = event.target;
            const colorItem = input.closest('.color-item');
            const previewContainer = colorItem.querySelector('.image-preview-container');
            const previewImg = previewContainer.querySelector('.image-preview');
            const placeholder = previewContainer.querySelector('.image-placeholder');
            const errorContainer = colorItem.querySelector('.image-error-message');
            const errorText = errorContainer.querySelector('.error-text');

            // Hide any existing error messages
            errorContainer.classList.add('d-none');

            if (input.files && input.files[0]) {
                const file = input.files[0];

                // Validate file type
                if (!file.type.startsWith('image/')) {
                    showImageError(errorContainer, errorText, 'Please select a valid image file.');
                    input.value = '';
                    return;
                }

                // Enhanced file size validation (2MB limit) with immediate feedback
                if (file.size > 2 * 1024 * 1024) {
                    const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
                    showImageError(errorContainer, errorText, `File size (${fileSizeMB}MB) exceeds the 2MB limit. Please choose a smaller image.`);
                    input.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewImg.classList.remove('d-none');
                    placeholder.style.display = 'none';

                    // Add CSS class for selected state
                    previewContainer.classList.add('has-image');
                };
                reader.readAsDataURL(file);
            } else {
                // Reset to placeholder state
                previewImg.classList.add('d-none');
                previewImg.src = '';
                placeholder.style.display = 'block';

                // Remove CSS class for selected state
                previewContainer.classList.remove('has-image');
            }
        }

        function showImageError(errorContainer, errorText, message) {
            errorText.textContent = message;
            errorContainer.classList.remove('d-none');

            // Auto-hide error after 5 seconds
            setTimeout(() => {
                errorContainer.classList.add('d-none');
            }, 5000);
        }

        // Initialize image preview for existing inputs
        setupImagePreview();

        // Single default selection enforcement for colors
        function setupDefaultColorSelection() {
            document.querySelectorAll('.default-color-checkbox').forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    if (this.checked) {
                        // Uncheck all other default color checkboxes
                        document.querySelectorAll('.default-color-checkbox').forEach(function(otherCheckbox) {
                            if (otherCheckbox !== checkbox) {
                                otherCheckbox.checked = false;
                                // Visual feedback
                                otherCheckbox.closest('.color-item').style.transition = 'all 0.3s ease';
                                otherCheckbox.closest('.color-item').style.opacity = '0.8';
                                setTimeout(() => {
                                    otherCheckbox.closest('.color-item').style.opacity = '1';
                                }, 300);
                            }
                        });

                        // Visual feedback for selected item
                        this.closest('.color-item').style.transition = 'all 0.3s ease';
                        this.closest('.color-item').style.transform = 'scale(1.02)';
                        setTimeout(() => {
                            this.closest('.color-item').style.transform = 'scale(1)';
                        }, 300);
                    }
                });
            });
        }

        // Initialize default color selection
        setupDefaultColorSelection();

        // Function to calculate the next display order value
        function calculateNextDisplayOrder() {
            const container = document.getElementById('colors-container');
            const displayOrderInputs = container.querySelectorAll('input[name*="[display_order]"]');
            let maxOrder = -1;

            // Find the highest existing display order value
            displayOrderInputs.forEach(function(input) {
                const value = parseInt(input.value) || 0;
                if (value > maxOrder) {
                    maxOrder = value;
                }
            });

            // Return the next sequential order value
            return maxOrder + 1;
        }

        // Add Color functionality
        document.getElementById('add-color').addEventListener('click', function() {
            const container = document.getElementById('colors-container');
            const colorItems = container.querySelectorAll('.color-item');
            const newIndex = colorItems.length;

            // Calculate the next display order value
            const nextDisplayOrder = calculateNextDisplayOrder();

            // Clone the first color item
            const firstColorItem = colorItems[0];
            const newColorItem = firstColorItem.cloneNode(true);

            // Update all name attributes and IDs
            newColorItem.querySelectorAll('[name]').forEach(function(input) {
                const name = input.getAttribute('name');
                const newName = name.replace(/\[0\]/, `[${newIndex}]`);
                input.setAttribute('name', newName);
                input.value = input.type === 'checkbox' ? false : '';
                if (input.type === 'checkbox') {
                    input.checked = false;
                }

                // Set automatic display order for new color forms
                if (name.includes('[display_order]')) {
                    input.value = nextDisplayOrder;
                }
            });

            // Reset image preview
            const previewContainer = newColorItem.querySelector('.image-preview-container');
            const previewImg = previewContainer.querySelector('.image-preview');
            const placeholder = previewContainer.querySelector('.image-placeholder');
            previewImg.classList.add('d-none');
            previewImg.src = '';
            placeholder.style.display = 'block';
            previewContainer.classList.remove('has-image');

            // Reset file input
            const fileInput = newColorItem.querySelector('.color-image-input');
            fileInput.value = '';

            // Hide error message
            const errorContainer = newColorItem.querySelector('.image-error-message');
            errorContainer.classList.add('d-none');

            // Add to container
            container.appendChild(newColorItem);

            // Re-initialize event listeners
            setupImagePreview();
            setupDefaultColorSelection();

            // Enhance the new color dropdown with visual styling
            if (window.enhancedColorSelection) {
                // Direct initialization approach for better reliability
                const dropdown = newColorItem.querySelector('.custom-color-dropdown');
                if (dropdown) {
                    console.log('Initializing dropdown for new color item...');
                    window.enhancedColorSelection.initializeCustomDropdown(dropdown);
                } else {
                    console.log('No dropdown found, calling handleDynamicColorItem...');
                    window.enhancedColorSelection.handleDynamicColorItem(newColorItem);
                }
            }
        });

        // Remove Color functionality
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-item')) {
                const colorItem = e.target.closest('.color-item');
                const container = document.getElementById('colors-container');

                // Don't allow removing the last color
                if (container.querySelectorAll('.color-item').length > 1) {
                    colorItem.remove();
                } else {
                    alert('At least one color is required.');
                }
            }
        });

        // Add Specification functionality
        document.getElementById('add-specification').addEventListener('click', function() {
            const container = document.getElementById('specifications-container');
            const specItems = container.querySelectorAll('.specification-item');
            const newIndex = specItems.length;

            // Clone the first specification item
            const firstSpecItem = specItems[0];
            const newSpecItem = firstSpecItem.cloneNode(true);

            // Update all name attributes
            newSpecItem.querySelectorAll('[name]').forEach(function(input) {
                const name = input.getAttribute('name');
                const newName = name.replace(/\[0\]/, `[${newIndex}]`);
                input.setAttribute('name', newName);
                input.value = '';
            });

            // Add to container
            container.appendChild(newSpecItem);
        });

        // Remove Specification functionality
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-item') && e.target.closest('.specification-item')) {
                const specItem = e.target.closest('.specification-item');
                const container = document.getElementById('specifications-container');

                // Don't allow removing the last specification
                if (container.querySelectorAll('.specification-item').length > 1) {
                    specItem.remove();
                }
            }
        });

        // Category selection validation
        function setupCategoryValidation() {
            const categorySelect = document.getElementById('category_id');
            if (categorySelect) {
                categorySelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    if (selectedOption && selectedOption.disabled) {
                        alert('Please select a subcategory, not a main category.');
                        this.value = '';
                    }
                });
            }
        }

        setupCategoryValidation();

        // Initialize stock validation integration
        function setupStockValidationIntegration() {
            // Refresh stock validation when colors are added/removed
            const addColorBtn = document.getElementById('add-color');
            if (addColorBtn) {
                addColorBtn.addEventListener('click', function() {
                    setTimeout(() => {
                        if (window.merchantStockValidator) {
                            window.merchantStockValidator.refreshValidation();
                        }
                    }, 100);
                });
            }

            // Refresh validation when colors are removed
            document.addEventListener('click', function(e) {
                if (e.target.closest('.remove-item') && e.target.closest('.color-item')) {
                    setTimeout(() => {
                        if (window.merchantStockValidator) {
                            window.merchantStockValidator.refreshValidation();
                        }
                    }, 100);
                }
            });

            // Add stock summary display
            const stockInput = document.getElementById('stock');
            if (stockInput) {
                const summaryContainer = document.createElement('div');
                summaryContainer.id = 'stock-summary-container';
                summaryContainer.className = 'mt-2 p-3 rounded-md border border-gray-200 bg-gray-50';
                summaryContainer.style.display = 'none';
                stockInput.parentNode.appendChild(summaryContainer);

                // Show/hide summary on focus/blur
                stockInput.addEventListener('focus', function() {
                    updateStockSummaryDisplay();
                    summaryContainer.style.display = 'block';
                });

                stockInput.addEventListener('blur', function() {
                    setTimeout(() => {
                        summaryContainer.style.display = 'none';
                    }, 200);
                });
            }
        }

        function updateStockSummaryDisplay() {
            if (!window.merchantStockValidator) return;

            const summary = window.merchantStockValidator.getStockSummary();
            const container = document.getElementById('stock-summary-container');
            if (!container) return;

            container.innerHTML = `
                <div class="text-sm">
                    <div class="font-semibold text-gray-700 mb-2">Stock Allocation Summary</div>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div>General Stock: <span class="font-medium">${summary.generalStock}</span></div>
                        <div>Total Color Stock: <span class="font-medium">${summary.totalColorStock}</span></div>
                        <div>Remaining: <span class="font-medium ${summary.remainingGeneralStock >= 0 ? 'text-green-600' : 'text-red-600'}">${summary.remainingGeneralStock}</span></div>
                        <div>Colors: <span class="font-medium">${summary.colorBreakdown.length}</span></div>
                    </div>
                </div>
            `;
        }

        setupStockValidationIntegration();
    });
</script>
@endsection
