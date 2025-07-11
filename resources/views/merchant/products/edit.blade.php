@extends('layouts.merchant')

@section('title', 'Edit Product')
@section('header', 'Edit Product')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/mdbassit/Coloris@latest/dist/coloris.min.css"/>
<style>
    /* Vue.js-inspired modern design system with Discord-themed colors */
    :root {
        /* Discord-themed primary color scheme */
        --primary-50: #eff6ff;
        --primary-100: #dbeafe;
        --primary-200: #bfdbfe;
        --primary-400: #60a5fa;
        --primary-500: #1E5EFF;
        --primary-600: #1a52e6;
        --primary-700: #1e40af;
        --primary-800: #1e3a8a;

        /* Slate color scheme */
        --slate-50: #f8fafc;
        --slate-100: #f1f5f9;
        --slate-200: #e2e8f0;
        --slate-300: #cbd5e1;
        --slate-400: #94a3b8;
        --slate-500: #64748b;
        --slate-600: #475569;
        --slate-700: #334155;
        --slate-800: #1e293b;
        --slate-900: #0f172a;

        /* Red for errors */
        --red-500: #ef4444;
        --red-600: #dc2626;
    }

    /* Main page background with gradient */
    .vue-page-container {
        min-height: 100vh;
        background: linear-gradient(to bottom right, var(--slate-50), var(--slate-100));
        padding: 1rem;
    }

    @media (min-width: 768px) {
        .vue-page-container {
            padding: 1.5rem;
        }
    }

    /* Main content container */
    .vue-content-container {
        max-width: 80rem; /* max-w-7xl */
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 1.5rem; /* space-y-6 */
    }

    /* Modern card styling */
    .vue-card {
        background-color: white;
        border: 1px solid var(--slate-200);
        border-radius: 0.5rem; /* rounded-lg */
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        overflow: hidden;
    }

    .vue-card-body {
        padding: 1.5rem; /* p-6 */
    }

    /* Modern form controls */
    .vue-form-control {
        width: 100%;
        padding: 0.5rem 0.75rem; /* px-3 py-2 */
        border: 1px solid var(--slate-300);
        border-radius: 0.5rem; /* rounded-lg */
        background-color: white;
        color: var(--slate-900);
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    .vue-form-control:focus {
        outline: none;
        border-color: var(--primary-500);
        box-shadow: 0 0 0 2px rgba(30, 94, 255, 0.2); /* focus:ring-2 focus:ring-primary-500 */
    }

    .vue-form-control::placeholder {
        color: var(--slate-400);
    }

    /* Modern buttons */
    .vue-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem; /* px-4 py-2 */
        font-size: 0.875rem;
        font-weight: 500;
        border-radius: 0.5rem; /* rounded-lg */
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .vue-btn-primary {
        background-color: var(--primary-600);
        color: white;
    }

    .vue-btn-primary:hover {
        background-color: var(--primary-700);
        color: white;
    }

    .vue-btn-secondary {
        background-color: white;
        color: var(--slate-700);
        border: 1px solid var(--slate-300);
    }

    .vue-btn-secondary:hover {
        background-color: var(--slate-50);
        color: var(--slate-700);
    }

    /* Image preview enhancements */
    .image-preview-container {
        transition: all 0.3s ease;
        border: 2px dashed var(--slate-300);
        border-radius: 0.5rem;
        background-color: var(--slate-50);
        overflow: hidden;
    }

    .image-preview-container:hover {
        border-color: var(--primary-400);
        background-color: var(--primary-50);
    }

    .image-preview-container.has-image {
        border-color: var(--primary-500);
        background-color: var(--primary-50);
    }

    /* Change image button styling */
    .trigger-image-upload {
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
    }

    .trigger-image-upload:hover {
        transform: scale(1.05);
    }

    /* Ensure button is visible on mobile */
    @media (max-width: 768px) {
        .trigger-image-upload {
            background-color: rgba(255, 255, 255, 0.95) !important;
        }
    }

    /* Typography */
    .vue-text-2xl {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--slate-900);
    }

    @media (min-width: 768px) {
        .vue-text-2xl {
            font-size: 1.875rem; /* text-3xl on md+ */
        }
    }

    .vue-text-lg {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--slate-900);
    }

    .vue-text-sm {
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--slate-700);
    }

    .vue-text-muted {
        color: var(--slate-600);
    }

    /* Legacy compatibility for existing JavaScript */
    .form-control {
        @extend .vue-form-control;
    }

    .form-select {
        @extend .vue-form-control;
    }

    .discord-btn {
        @extend .vue-btn;
        @extend .vue-btn-primary;
    }

    .discord-btn-secondary {
        @extend .vue-btn;
        @extend .vue-btn-secondary;
    }

    .discord-card {
        @extend .vue-card;
    }

    .discord-card-body {
        @extend .vue-card-body;
    }

    /* Tab Navigation Styling */
    .vue-tab-button {
        border: none;
        background: none;
        cursor: pointer;
        outline: none;
    }

    .vue-tab-button.active {
        color: var(--primary-600) !important;
        border-bottom: 2px solid var(--primary-600) !important;
        background-color: var(--primary-50) !important;
    }

    .vue-tab-button:not(.active):hover {
        color: var(--slate-900) !important;
        background-color: var(--slate-50) !important;
    }

    /* Tab Content */
    .vue-tab-content {
        display: none;
    }

    .vue-tab-content.active {
        display: block;
    }

    /* Utility classes for layout */
    .flex { display: flex; }
    .items-center { align-items: center; }
    .justify-between { justify-content: space-between; }
    .gap-2 { gap: 0.5rem; }
    .gap-4 { gap: 1rem; }
    .space-y-6 > * + * { margin-top: 1.5rem; }
    .w-full { width: 100%; }
    .h-2 { height: 0.5rem; }
    .rounded-full { border-radius: 9999px; }
    .rounded-t-lg { border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem; }
    .border-b { border-bottom-width: 1px; }
    .border-slate-200 { border-color: var(--slate-200); }
    .px-6 { padding-left: 1.5rem; padding-right: 1.5rem; }
    .py-3 { padding-top: 0.75rem; padding-bottom: 0.75rem; }
    .mb-2 { margin-bottom: 0.5rem; }
    .mt-1 { margin-top: 0.25rem; }
    .mt-3 { margin-top: 0.75rem; }
    .p-3 { padding: 0.75rem; }
    .w-4 { width: 1rem; }
    .h-4 { height: 1rem; }
    .transition-all { transition: all 0.2s ease; }
    .duration-300 { transition-duration: 300ms; }

    @media (min-width: 640px) {
        .sm\:flex-row { flex-direction: row; }
        .sm\:items-center { align-items: center; }
        .sm\:justify-between { justify-content: space-between; }
        .sm\:inline { display: inline; }
    }

    /* Additional utility classes for Vue.js design */
    .space-y-2 > * + * { margin-top: 0.5rem; }
    .space-y-4 > * + * { margin-top: 1rem; }
    .space-x-2 > * + * { margin-left: 0.5rem; }
    .grid { display: grid; }
    .grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .grid-cols-12 { grid-template-columns: repeat(12, minmax(0, 1fr)); }
    .col-span-1 { grid-column: span 1 / span 1; }
    .col-span-4 { grid-column: span 4 / span 4; }
    .col-span-6 { grid-column: span 6 / span 6; }
    .gap-6 { gap: 1.5rem; }
    .aspect-\[3\/4\] { aspect-ratio: 3 / 4; }
    .bg-slate-50 { background-color: var(--slate-50); }
    .bg-slate-100 { background-color: var(--slate-100); }
    .text-slate-400 { color: var(--slate-400); }
    .text-slate-500 { color: var(--slate-500); }
    .text-slate-600 { color: var(--slate-600); }
    .text-slate-700 { color: var(--slate-700); }
    .text-slate-900 { color: var(--slate-900); }
    .text-red-500 { color: var(--red-500); }
    .text-red-600 { color: var(--red-600); }
    .text-primary-600 { color: var(--primary-600); }
    .text-primary-700 { color: var(--primary-700); }
    .text-primary-800 { color: var(--primary-800); }
    .bg-primary-50 { background-color: var(--primary-50); }
    .bg-primary-100 { background-color: var(--primary-100); }
    .border-slate-200 { border-color: var(--slate-200); }
    .border-slate-300 { border-color: var(--slate-300); }
    .border-primary-200 { border-color: var(--primary-200); }
    .border-primary-400 { border-color: var(--primary-400); }
    .border-primary-500 { border-color: var(--primary-500); }
    .border-red-500 { border-color: var(--red-500); }
    .hover\:border-primary-400:hover { border-color: var(--primary-400); }
    .hover\:bg-slate-50:hover { background-color: var(--slate-50); }
    .hover\:bg-slate-100:hover { background-color: var(--slate-100); }
    .hover\:bg-red-50:hover { background-color: rgba(239, 68, 68, 0.1); }
    .hover\:text-red-700:hover { color: #b91c1c; }
    .focus\:ring-primary-500:focus { box-shadow: 0 0 0 2px rgba(30, 94, 255, 0.2); }
    .ring-2 { box-shadow: 0 0 0 2px rgba(30, 94, 255, 0.5); }
    .ring-primary-500 { --tw-ring-color: var(--primary-500); }
    .transform { transform: translateX(var(--tw-translate-x)) translateY(var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y)); }
    .-translate-y-1\/2 { --tw-translate-y: -50%; }
    .absolute { position: absolute; }
    .relative { position: relative; }
    .left-3 { left: 0.75rem; }
    .top-1\/2 { top: 50%; }
    .top-2 { top: 0.5rem; }
    .right-2 { right: 0.5rem; }
    .pl-10 { padding-left: 2.5rem; }
    .text-xs { font-size: 0.75rem; }
    .font-medium { font-weight: 500; }
    .font-semibold { font-weight: 600; }
    .inline-flex { display: inline-flex; }
    .hidden { display: none; }
    .block { display: block; }
    .w-3 { width: 0.75rem; }
    .h-3 { height: 0.75rem; }
    .w-5 { width: 1.25rem; }
    .h-5 { height: 1.25rem; }
    .w-6 { width: 1.5rem; }
    .h-6 { height: 1.5rem; }
    .w-12 { width: 3rem; }
    .h-12 { height: 3rem; }
    .h-10 { height: 2.5rem; }
    .mr-1 { margin-right: 0.25rem; }
    .mr-2 { margin-right: 0.5rem; }
    .ml-2 { margin-left: 0.5rem; }
    .mb-1 { margin-bottom: 0.25rem; }
    .mb-3 { margin-bottom: 0.75rem; }
    .mb-4 { margin-bottom: 1rem; }
    .mx-auto { margin-left: auto; margin-right: auto; }
    .p-1 { padding: 0.25rem; }
    .p-2 { padding: 0.5rem; }
    .px-2 { padding-left: 0.5rem; padding-right: 0.5rem; }
    .py-1 { padding-top: 0.25rem; padding-bottom: 0.25rem; }
    .px-3 { padding-left: 0.75rem; padding-right: 0.75rem; }
    .py-2 { padding-top: 0.5rem; padding-bottom: 0.5rem; }
    .p-4 { padding: 1rem; }
    .p-6 { padding: 1.5rem; }
    .p-8 { padding: 2rem; }
    .py-8 { padding-top: 2rem; padding-bottom: 2rem; }
    .py-12 { padding-top: 3rem; padding-bottom: 3rem; }
    .rounded { border-radius: 0.25rem; }
    .rounded-lg { border-radius: 0.5rem; }
    .rounded-full { border-radius: 9999px; }
    .border { border-width: 1px; }
    .border-2 { border-width: 2px; }
    .border-dashed { border-style: dashed; }
    .shadow-sm { box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); }
    .overflow-hidden { overflow: hidden; }
    .cursor-pointer { cursor: pointer; }
    .flex-1 { flex: 1 1 0%; }
    .flex-col { flex-direction: column; }
    .justify-center { justify-content: center; }
    .text-center { text-align: center; }

    @media (min-width: 1024px) {
        .lg\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
</style>
@endsection

@section('content')
<div class="vue-page-container">
    <div class="vue-content-container">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('merchant.products.index') }}" class="vue-btn vue-btn-secondary">
                    <i class="fas fa-arrow-left w-4 h-4"></i>
                    Back to Products
                </a>
                <div>
                    <h1 class="vue-text-2xl">Edit Product</h1>
                    <p class="vue-text-muted mt-1">Update product information, colors, and specifications</p>
                </div>
            </div>
            <div class="flex gap-2">
                <button type="button" class="vue-btn vue-btn-secondary">
                    Preview
                </button>
                <button type="submit" form="product-edit-form" class="vue-btn vue-btn-primary">
                    <i class="fas fa-save w-4 h-4"></i>
                    Save Changes
                </button>
            </div>
        </div>

        <!-- Stock Progress Indicator -->
        <div class="vue-card" style="background-color: var(--primary-50); border-color: var(--primary-200);">
            <div class="vue-card-body">
                <div class="flex items-center justify-between mb-2">
                    <span class="vue-text-sm" style="color: var(--primary-800);">Stock Allocation Progress</span>
                    <span class="vue-text-sm" style="color: var(--primary-700);">
                        <span id="total-allocated-stock">{{ $product->colors->sum('stock') }}</span> / {{ $product->stock }} units allocated
                    </span>
                </div>
                <div class="w-full rounded-full h-2" style="background-color: var(--primary-200);">
                    <div id="stock-progress-bar"
                         class="h-2 rounded-full transition-all duration-300"
                         style="background-color: var(--primary-600); width: {{ min(($product->colors->sum('stock') / max($product->stock, 1)) * 100, 100) }}%;">
                    </div>
                </div>
                <div id="stock-warning" class="mt-3 p-3 rounded-lg" style="background-color: rgba(251, 191, 36, 0.1); border: 1px solid #f59e0b; display: {{ ($product->colors->sum('stock') > $product->stock) ? 'block' : 'none' }};">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-exclamation-triangle" style="color: #d97706;"></i>
                        <p style="color: #92400e; font-size: 0.875rem; margin: 0;">
                            You've allocated more stock than available. Please adjust color stock quantities.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="space-y-6">
            <!-- Tab Navigation -->
            <div class="flex border-b border-slate-200 bg-white rounded-t-lg">
                <button type="button"
                        class="vue-tab-button active flex items-center gap-2 px-6 py-3 font-medium text-sm transition-colors text-primary-600 border-b-2 border-primary-600 bg-primary-50"
                        data-tab="basic">
                    <i class="fas fa-box w-4 h-4"></i>
                    <span class="hidden sm:inline">Basic Info</span>
                </button>
                <button type="button"
                        class="vue-tab-button flex items-center gap-2 px-6 py-3 font-medium text-sm transition-colors text-slate-600 hover:text-slate-900 hover:bg-slate-50"
                        data-tab="colors">
                    <i class="fas fa-palette w-4 h-4"></i>
                    <span class="hidden sm:inline">Colors & Images</span>
                </button>
                <button type="button"
                        class="vue-tab-button flex items-center gap-2 px-6 py-3 font-medium text-sm transition-colors text-slate-600 hover:text-slate-900 hover:bg-slate-50"
                        data-tab="specifications">
                    <i class="fas fa-file-text w-4 h-4"></i>
                    <span class="hidden sm:inline">Specifications</span>
                </button>
            </div>

            <!-- Enhanced Product Edit Form -->
            <form id="product-edit-form" action="{{ route('merchant.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Basic Information Tab -->
                <div id="basic-tab" class="vue-tab-content active space-y-6">
                    <div class="grid lg:grid-cols-2 gap-6">
                        <!-- Product Details Card -->
                        <div class="vue-card">
                            <div class="p-6 border-b border-slate-200">
                                <h3 class="flex items-center gap-2 vue-text-lg">
                                    <i class="fas fa-box w-5 h-5 text-slate-600"></i>
                                    Product Details
                                </h3>
                            </div>
                            <div class="p-6 space-y-4">
                                <div class="space-y-2">
                                    <label for="name" class="block vue-text-sm">
                                        Product Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}"
                                           class="vue-form-control @error('name') border-red-500 @enderror" required>
                                    @error('name')
                                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label for="category_id" class="block vue-text-sm">
                                            Category <span class="text-red-500">*</span>
                                        </label>
                                        <select id="category_id" name="category_id" class="vue-form-control @error('category_id') border-red-500 @enderror" required>
                                            <option value="">Select category</option>
                                            @foreach($parentCategories ?? [] as $parentCategory)
                                                <optgroup label="{{ $parentCategory->name }}">
                                                    @foreach($parentCategory->children as $childCategory)
                                                        <option value="{{ $childCategory->id }}" {{ old('category_id', $product->category_id) == $childCategory->id ? 'selected' : '' }}>
                                                            {{ $childCategory->name }}
                                                        </option>
                                                    @endforeach
                                                </optgroup>
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="space-y-2">
                                        <label for="branch_id" class="block vue-text-sm">
                                            Branch <span class="text-red-500">*</span>
                                        </label>
                                        <select id="branch_id" name="branch_id" class="vue-form-control @error('branch_id') border-red-500 @enderror" required>
                                            <option value="">Select branch</option>
                                            @foreach($branches ?? [] as $branch)
                                                <option value="{{ $branch->id }}" {{ old('branch_id', $product->branch_id) == $branch->id ? 'selected' : '' }}>
                                                    {{ $branch->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('branch_id')
                                            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label for="description" class="block vue-text-sm">
                                        Description
                                    </label>
                                    <textarea id="description" name="description" rows="4"
                                              class="vue-form-control @error('description') border-red-500 @enderror">{{ old('description', $product->description) }}</textarea>
                                    @error('description')
                                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Pricing & Inventory Card -->
                        <div class="vue-card">
                            <div class="p-6 border-b border-slate-200">
                                <h3 class="flex items-center gap-2 vue-text-lg">
                                    <i class="fas fa-dollar-sign w-5 h-5 text-slate-600"></i>
                                    Pricing & Inventory
                                </h3>
                            </div>
                            <div class="p-6 space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label for="price" class="block vue-text-sm">
                                            Current Price <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <i class="fas fa-dollar-sign absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                                            <input type="number" name="price" id="price" min="0" step="0.01" value="{{ old('price', $product->price) }}"
                                                   class="vue-form-control pl-10 @error('price') border-red-500 @enderror" required>
                                        </div>
                                        @error('price')
                                            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="space-y-2">
                                        <label for="original_price" class="block vue-text-sm">
                                            Original Price
                                        </label>
                                        <div class="relative">
                                            <i class="fas fa-dollar-sign absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                                            <input type="number" name="original_price" id="original_price" min="0" step="0.01" value="{{ old('original_price', $product->original_price) }}"
                                                   class="vue-form-control pl-10 @error('original_price') border-red-500 @enderror">
                                        </div>
                                        @error('original_price')
                                            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label for="stock" class="block vue-text-sm">
                                        Total Stock <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <i class="fas fa-warehouse absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                                        <input type="number" name="stock" id="stock" min="0" value="{{ old('stock', $product->stock) }}"
                                               class="vue-form-control pl-10 @error('stock') border-red-500 @enderror" required>
                                    </div>
                                    <p class="text-xs text-slate-500">Total inventory to be allocated across color variants</p>
                                    @error('stock')
                                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="flex items-center space-x-2">
                                    <input id="is_available" name="is_available" type="checkbox"
                                           class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500"
                                           {{ old('is_available', $product->is_available) ? 'checked' : '' }} value="1">
                                    <label for="is_available" class="vue-text-sm">
                                        Available for purchase
                                    </label>
                                </div>
                                @error('is_available')
                                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                @enderror

                                @if(old('original_price', $product->original_price) && old('original_price', $product->original_price) > old('price', $product->price))
                                <div class="p-3 rounded-lg" style="background-color: var(--primary-50); border: 1px solid var(--primary-200);">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-1 text-xs font-medium rounded" style="background-color: var(--primary-100); color: var(--primary-800);">
                                            Sale
                                        </span>
                                        <span class="text-sm" style="color: var(--primary-700);">
                                            {{ round(((old('original_price', $product->original_price) - old('price', $product->price)) / old('original_price', $product->original_price)) * 100) }}% off
                                        </span>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Colors & Images Tab -->
                <div id="colors-tab" class="vue-tab-content space-y-6" style="display: none;">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="vue-text-lg">Product Colors</h3>
                            <p class="text-sm text-slate-600">Add color variants with images and size options</p>
                        </div>
                        <button type="button" id="add-color" class="vue-btn vue-btn-primary" onclick="console.log('Direct onclick test'); addNewColorForm();">
                            <i class="fas fa-plus w-4 h-4"></i>
                            Add Color
                        </button>
                    </div>
                    <!-- Empty State for Colors -->
                    @if($product->colors->isEmpty())
                    <div class="vue-card" style="border: 2px dashed var(--slate-300);">
                        <div class="flex flex-col items-center justify-center py-12">
                            <i class="fas fa-palette w-12 h-12 text-slate-400 mb-4"></i>
                            <h3 class="vue-text-lg mb-2">No colors added</h3>
                            <p class="text-slate-600 text-center mb-4">
                                Add at least one color variant with an image to continue
                            </p>
                            <button type="button" id="add-first-color" class="vue-btn vue-btn-primary" onclick="console.log('Direct onclick test first color'); addNewColorForm();">
                                <i class="fas fa-plus w-4 h-4"></i>
                                Add Your First Color
                            </button>
                        </div>
                    </div>
                    @endif

                    <!-- Color Cards Container -->
                    <div id="colors-container" class="grid gap-6">
                        @forelse($product->colors as $index => $color)
                        <div class="vue-card color-item {{ $color->is_default ? 'ring-2 ring-primary-500 border-primary-200' : '' }} transition-all duration-200">
                            <div class="p-6 border-b border-slate-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-6 h-6 rounded-full border-2 border-white shadow-sm"
                                             style="background-color: {{ $color->color_code ?: '#000000' }};"></div>
                                        <h4 class="vue-text-lg">
                                            Color Variant {{ $index + 1 }}
                                            @if($color->is_default)
                                            <span class="ml-2 inline-flex items-center px-2 py-1 text-xs font-medium rounded"
                                                  style="background-color: var(--primary-100); color: var(--primary-800);">
                                                <i class="fas fa-star w-3 h-3 mr-1"></i>
                                                Default
                                            </span>
                                            @endif
                                        </h4>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @if(!$color->is_default)
                                        <button type="button" class="vue-btn vue-btn-secondary text-sm set-default-color" data-color-index="{{ $index }}">
                                            Set as Default
                                        </button>
                                        @endif
                                        <button type="button" class="remove-item p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors">
                                            <i class="fas fa-trash w-4 h-4"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="p-6 space-y-6">
                                <div class="grid lg:grid-cols-2 gap-6">
                                    <!-- Color Details -->
                                    <div class="space-y-4">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="space-y-2">
                                                <label class="block vue-text-sm">
                                                    Color Name <span class="text-red-500">*</span>
                                                </label>
                                                <select name="colors[{{ $index }}][name]" class="vue-form-control color-name-select" required>
                                                    <option value="">Select color</option>
                                                    @foreach(['Red', 'Blue', 'Green', 'Navy Blue', 'Forest Green', 'Black', 'White', 'Gray', 'Yellow', 'Orange', 'Purple', 'Pink', 'Brown', 'Silver', 'Gold', 'Maroon', 'Teal', 'Olive', 'Lime', 'Aqua', 'Fuchsia'] as $colorOption)
                                                        <option value="{{ $colorOption }}" {{ $color->name == $colorOption ? 'selected' : '' }}>{{ $colorOption }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="space-y-2">
                                                <label class="block vue-text-sm">Color Code</label>
                                                <div class="flex gap-2">
                                                    <input type="color" name="colors[{{ $index }}][color_code_picker]" value="{{ $color->color_code ?: '#000000' }}"
                                                           class="w-12 h-10 p-1 border border-slate-300 rounded-lg">
                                                    <input type="text" name="colors[{{ $index }}][color_code]" placeholder="#000000" value="{{ $color->color_code }}"
                                                           class="vue-form-control flex-1">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="space-y-2">
                                                <label class="block vue-text-sm">Price Adjustment</label>
                                                <div class="relative">
                                                    <i class="fas fa-dollar-sign absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                                                    <input type="number" step="0.01" name="colors[{{ $index }}][price_adjustment]" value="{{ $color->price_adjustment }}"
                                                           class="vue-form-control pl-10">
                                                </div>
                                            </div>

                                            <div class="space-y-2">
                                                <label class="block vue-text-sm">Stock Allocation</label>
                                                <input type="number" name="colors[{{ $index }}][stock]" value="{{ $color->stock }}" min="0"
                                                       class="vue-form-control color-stock-input">
                                            </div>
                                        </div>

                                        <input type="hidden" name="colors[{{ $index }}][display_order]" value="{{ $color->display_order }}">
                                    </div>
                                    <!-- Image Upload -->
                                    <div class="space-y-4">
                                        <label class="block vue-text-sm">
                                            Product Image <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <div class="aspect-[3/4] bg-slate-50 border-2 border-dashed border-slate-300 rounded-lg overflow-hidden hover:border-primary-400 transition-colors image-preview-container {{ $color->image ? 'has-image' : '' }}">
                                                @if($color->image)
                                                <div class="relative w-full h-full group">
                                                    <img src="{{ $color->image }}" alt="{{ $color->name }} variant"
                                                         class="w-full h-full object-cover image-preview"
                                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                    <div class="w-full h-full flex flex-col items-center justify-center cursor-pointer hover:bg-slate-100 transition-colors image-placeholder trigger-image-upload" style="display: none;">
                                                        <i class="fas fa-image w-12 h-12 text-slate-400 mb-3"></i>
                                                        <p class="text-sm font-medium text-slate-600 mb-1">Image not found</p>
                                                        <p class="text-xs text-slate-500">Click to upload new image</p>
                                                    </div>
                                                    <!-- Change Image Button - Top Left Corner -->
                                                    <div class="absolute top-2 left-2 z-10">
                                                        <button type="button" class="flex items-center justify-center w-8 h-8 bg-white bg-opacity-90 hover:bg-opacity-100 text-slate-700 rounded-full shadow-lg hover:shadow-xl transition-all duration-200 trigger-image-upload group/btn">
                                                            <i class="fas fa-camera w-4 h-4 group-hover/btn:scale-110 transition-transform"></i>
                                                            <span class="sr-only">Change Image</span>
                                                        </button>
                                                    </div>
                                                    @if($color->is_default)
                                                    <div class="absolute top-2 right-2">
                                                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded"
                                                              style="background-color: var(--primary-600); color: white;">
                                                            <i class="fas fa-star w-3 h-3 mr-1"></i>
                                                            Main Image
                                                        </span>
                                                    </div>
                                                    @endif
                                                </div>
                                                @else
                                                <div class="w-full h-full flex flex-col items-center justify-center cursor-pointer hover:bg-slate-100 transition-colors image-placeholder trigger-image-upload">
                                                    <i class="fas fa-image w-12 h-12 text-slate-400 mb-3"></i>
                                                    <p class="text-sm font-medium text-slate-600 mb-1">Upload Image</p>
                                                    <p class="text-xs text-slate-500">PNG, JPG up to 2MB</p>
                                                </div>
                                                @endif
                                            </div>
                                            <input type="file" name="color_images[{{ $index }}]" class="color-image-input hidden" accept="image/*">
                                            <input type="hidden" name="colors[{{ $index }}][image]" value="{{ $color->getRawImagePath() }}">
                                        </div>


                                    </div>
                                </div>

                                <!-- Size Variants Display (if any exist) -->
                                @if($color->sizes && $color->sizes->count() > 0)
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <h4 class="font-medium text-slate-900">Size Variants</h4>
                                        <span class="px-2 py-1 bg-slate-100 text-slate-700 text-xs rounded">
                                            {{ $color->sizes->count() }} sizes
                                        </span>
                                    </div>
                                    <div class="grid gap-3">
                                        @foreach($color->sizes as $size)
                                        <div class="flex items-center gap-4 p-3 bg-slate-50 rounded-lg">
                                            <div class="flex-1">
                                                <div class="font-medium text-sm">{{ $size->name }}</div>
                                                <div class="text-xs text-slate-500">{{ $size->value }}</div>
                                            </div>
                                            <div class="text-sm">
                                                <span class="font-medium">{{ $size->pivot->stock ?? 0 }}</span> units
                                            </div>
                                            @if($size->pivot->price_adjustment && $size->pivot->price_adjustment != 0)
                                            <div class="text-sm">
                                                <span class="{{ $size->pivot->price_adjustment > 0 ? 'text-primary-600' : 'text-red-600' }}">
                                                    {{ $size->pivot->price_adjustment > 0 ? '+' : '' }}${{ $size->pivot->price_adjustment }}
                                                </span>
                                            </div>
                                            @endif
                                            <div class="flex items-center">
                                                @if($size->pivot->is_available)
                                                <i class="fas fa-check w-4 h-4 text-primary-600"></i>
                                                @else
                                                <div class="w-4 h-4 rounded-full bg-slate-300"></div>
                                                @endif
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        @empty
                        <!-- Default empty color item for when no colors exist -->
                        <div class="vue-card color-item">
                            <div class="p-6 border-b border-slate-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-6 h-6 rounded-full border-2 border-white shadow-sm bg-gray-400"></div>
                                        <h4 class="vue-text-lg">Color Variant 1</h4>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="button" class="remove-item p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors">
                                            <i class="fas fa-trash w-4 h-4"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="p-6 space-y-6">
                                <div class="grid lg:grid-cols-2 gap-6">
                                    <!-- Color Details -->
                                    <div class="space-y-4">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="space-y-2">
                                                <label class="block vue-text-sm">
                                                    Color Name <span class="text-red-500">*</span>
                                                </label>
                                                <select name="colors[0][name]" class="vue-form-control color-name-select" required>
                                                    <option value="">Select color</option>
                                                    @foreach(['Red', 'Blue', 'Green', 'Navy Blue', 'Forest Green', 'Black', 'White', 'Gray', 'Yellow', 'Orange', 'Purple', 'Pink', 'Brown', 'Silver', 'Gold', 'Maroon', 'Teal', 'Olive', 'Lime', 'Aqua', 'Fuchsia'] as $colorOption)
                                                        <option value="{{ $colorOption }}">{{ $colorOption }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="space-y-2">
                                                <label class="block vue-text-sm">Color Code</label>
                                                <div class="flex gap-2">
                                                    <input type="color" name="colors[0][color_code_picker]" value="#000000"
                                                           class="w-12 h-10 p-1 border border-slate-300 rounded-lg">
                                                    <input type="text" name="colors[0][color_code]" placeholder="#000000"
                                                           class="vue-form-control flex-1">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="space-y-2">
                                                <label class="block vue-text-sm">Price Adjustment</label>
                                                <div class="relative">
                                                    <i class="fas fa-dollar-sign absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                                                    <input type="number" step="0.01" name="colors[0][price_adjustment]" value="0"
                                                           class="vue-form-control pl-10">
                                                </div>
                                            </div>

                                            <div class="space-y-2">
                                                <label class="block vue-text-sm">Stock Allocation</label>
                                                <input type="number" name="colors[0][stock]" value="0" min="0"
                                                       class="vue-form-control color-stock-input">
                                            </div>
                                        </div>

                                        <input type="hidden" name="colors[0][display_order]" value="0">
                                    </div>
                                    <!-- Image Upload -->
                                    <div class="space-y-4">
                                        <label class="block vue-text-sm">
                                            Product Image <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <div class="aspect-[3/4] bg-slate-50 border-2 border-dashed border-slate-300 rounded-lg overflow-hidden hover:border-primary-400 transition-colors image-preview-container">
                                                <div class="w-full h-full flex flex-col items-center justify-center cursor-pointer hover:bg-slate-100 transition-colors image-placeholder trigger-image-upload">
                                                    <i class="fas fa-image w-12 h-12 text-slate-400 mb-3"></i>
                                                    <p class="text-sm font-medium text-slate-600 mb-1">Upload Image</p>
                                                    <p class="text-xs text-slate-500">PNG, JPG up to 2MB</p>
                                                </div>
                                            </div>
                                            <input type="file" name="color_images[0]" class="color-image-input hidden" required accept="image/*">
                                            <input type="hidden" name="colors[0][image]" value="">
                                        </div>

                                        <!-- Default Color Checkbox -->

                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Specifications Tab -->
                <div id="specifications-tab" class="vue-tab-content space-y-6" style="display: none;">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="vue-text-lg">Product Specifications</h3>
                            <p class="text-sm text-slate-600">Add technical details and product features</p>
                        </div>
                        <button type="button" id="add-specification" class="vue-btn vue-btn-primary">
                            <i class="fas fa-plus w-4 h-4"></i>
                            Add Specification
                        </button>
                    </div>

                    <div class="vue-card">
                        <div class="p-6">
                            <div class="space-y-4">
                                <div id="specifications-container">
                                    @forelse($product->specifications as $index => $specification)
                                    <div class="specification-item grid grid-cols-12 gap-4 items-center p-4 bg-slate-50 rounded-lg">
                                        <div class="col-span-4">
                                            <label class="block vue-text-sm mb-2">Key</label>
                                            <input type="text" name="specifications[{{ $index }}][key]" placeholder="e.g., Material"
                                                   value="{{ $specification->key }}" class="vue-form-control">
                                        </div>
                                        <div class="col-span-6">
                                            <label class="block vue-text-sm mb-2">Value</label>
                                            <input type="text" name="specifications[{{ $index }}][value]" placeholder="e.g., 100% Cotton"
                                                   value="{{ $specification->value }}" class="vue-form-control">
                                        </div>
                                        <div class="col-span-1">
                                            <label class="block vue-text-sm mb-2">Order</label>
                                            <input type="number" name="specifications[{{ $index }}][display_order]"
                                                   value="{{ $specification->display_order }}" class="vue-form-control">
                                        </div>
                                        <div class="col-span-1 flex justify-center">
                                            <button type="button" class="remove-item p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors">
                                                <i class="fas fa-trash w-4 h-4"></i>
                                            </button>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="specification-item grid grid-cols-12 gap-4 items-center p-4 bg-slate-50 rounded-lg">
                                        <div class="col-span-4">
                                            <label class="block vue-text-sm mb-2">Key</label>
                                            <input type="text" name="specifications[0][key]" placeholder="e.g., Material" class="vue-form-control">
                                        </div>
                                        <div class="col-span-6">
                                            <label class="block vue-text-sm mb-2">Value</label>
                                            <input type="text" name="specifications[0][value]" placeholder="e.g., 100% Cotton" class="vue-form-control">
                                        </div>
                                        <div class="col-span-1">
                                            <label class="block vue-text-sm mb-2">Order</label>
                                            <input type="number" name="specifications[0][display_order]" value="0" class="vue-form-control">
                                        </div>
                                        <div class="col-span-1 flex justify-center">
                                            <button type="button" class="remove-item p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors">
                                                <i class="fas fa-trash w-4 h-4"></i>
                                            </button>
                                        </div>
                                    </div>
                                    @endforelse
                                </div>

                                @if($product->specifications->isEmpty())
                                <div class="text-center py-8">
                                    <i class="fas fa-file-text w-12 h-12 text-slate-400 mx-auto mb-4"></i>
                                    <h3 class="vue-text-lg mb-2">No specifications added</h3>
                                    <p class="text-slate-600 mb-4">
                                        Add product specifications to provide detailed information to customers
                                    </p>
                                    <button type="button" id="add-first-specification" class="vue-btn vue-btn-primary">
                                        <i class="fas fa-plus w-4 h-4"></i>
                                        Add First Specification
                                    </button>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
            
@endsection






@section('scripts')
<!-- Browser Compatibility Polyfills -->
<script src="{{ asset('js/browser-compatibility.js') }}"></script>

<!-- External JavaScript Libraries -->
<script src="https://cdn.jsdelivr.net/gh/mdbassit/Coloris@latest/dist/coloris.min.js"></script>
<script src="{{ asset('js/color-picker.js') }}"></script>
<script src="{{ asset('js/enhanced-size-selection.js') }}"></script>
<script src="{{ asset('js/enhanced-color-selection.js') }}"></script>
<script src="{{ asset('js/color-specific-size-selection.js') }}"></script>
<script src="{{ asset('js/dynamic-color-size-management.js') }}"></script>
<script src="{{ asset('js/merchant-stock-validation.js') }}"></script>

<!-- Tab Navigation Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Tab navigation script loaded');

    // Initialize tab navigation
    function initializeTabNavigation() {
        const tabButtons = document.querySelectorAll('.vue-tab-button');
        const tabContents = document.querySelectorAll('.vue-tab-content');

        if (tabButtons.length === 0 || tabContents.length === 0) {
            console.warn('Tab elements not found');
            return;
        }

        tabButtons.forEach((button) => {
            // Remove any existing event listeners
            if (button._tabClickHandler) {
                button.removeEventListener('click', button._tabClickHandler);
            }

            const clickHandler = function(event) {
                event.preventDefault();
                event.stopPropagation();

                const targetTab = this.getAttribute('data-tab');
                if (!targetTab) return;

                // Remove active class from all buttons
                tabButtons.forEach(btn => {
                    btn.classList.remove('active', 'text-primary-600', 'border-b-2', 'border-primary-600', 'bg-primary-50');
                    btn.classList.add('text-slate-600');
                });

                // Add active class to clicked button
                this.classList.add('active', 'text-primary-600', 'border-b-2', 'border-primary-600', 'bg-primary-50');
                this.classList.remove('text-slate-600');

                // Hide all tab contents
                tabContents.forEach(content => {
                    content.style.display = 'none';
                    content.classList.remove('active');
                });

                // Show target tab content
                const targetContent = document.getElementById(targetTab + '-tab');
                if (targetContent) {
                    targetContent.style.display = 'block';
                    targetContent.classList.add('active');
                    console.log('Switched to tab:', targetTab);
                }
            };

            button._tabClickHandler = clickHandler;
            button.addEventListener('click', clickHandler);
        });

        console.log('Tab navigation initialized with', tabButtons.length, 'buttons');
    }

    // Initialize with retry mechanism
    function safeInitialize() {
        try {
            initializeTabNavigation();

            // Initialize enhanced color selection if available
            if (window.enhancedColorSelection) {
                console.log('Enhanced color selection already initialized');
                // The enhanced color selection is already initialized in its own DOMContentLoaded event
                // We can call enhanceExistingDropdowns to refresh any new elements
                window.enhancedColorSelection.enhanceExistingDropdowns();
            }

            // Dynamic color size management is already initialized in its own DOMContentLoaded event
            if (window.dynamicColorSizeManager) {
                console.log('Dynamic color size management already initialized');
                // The manager is already initialized, we can call refresh methods if needed
                if (typeof window.dynamicColorSizeManager.refreshAllocations === 'function') {
                    window.dynamicColorSizeManager.refreshAllocations();
                }
            }

            // Initialize merchant stock validation if available
            if (window.merchantStockValidator) {
                console.log('Initializing merchant stock validation...');
                // Check if it has an initialize method, otherwise it might be auto-initialized
                if (typeof window.merchantStockValidator.initialize === 'function') {
                    window.merchantStockValidator.initialize();
                } else {
                    console.log('Merchant stock validator already initialized');
                }
            }

            // Initialize color-specific size selection if available
            if (window.colorSpecificSizeSelection) {
                console.log('Initializing color-specific size selection...');
                // Check if it has an initialize method, otherwise it might be auto-initialized
                if (typeof window.colorSpecificSizeSelection.initialize === 'function') {
                    window.colorSpecificSizeSelection.initialize();
                } else {
                    console.log('Color-specific size selection already initialized');
                }
            }
        } catch (error) {
            console.error('Initialization error:', error);
            setTimeout(safeInitialize, 100);
        }
    }

    safeInitialize();
    setTimeout(safeInitialize, 500);
});
</script>

<!-- Main Product Edit Script -->
<script>
// Global functions for Add Color functionality
function addNewColorForm() {
    console.log('Adding new color form...');

    const container = document.getElementById('colors-container');
    if (!container) {
        console.error('Colors container not found');
        return;
    }

    // Get current color count for indexing
    const existingColorItems = container.querySelectorAll('.color-item');
    const newIndex = existingColorItems.length;

    console.log('Current color items:', existingColorItems.length, 'New index:', newIndex);

    // Create the new color form HTML
    const newColorHTML = createNewColorFormHTML(newIndex);

    // Create a temporary container to parse the HTML
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = newColorHTML;
    const newColorItem = tempDiv.firstElementChild;

    // Append to container
    container.appendChild(newColorItem);

    // Initialize image upload for the new item
    initializeImageUploadForItem(newColorItem);

    // Initialize color picker for the new item
    initializeColorPickerForItem(newColorItem);

    // Initialize remove functionality
    initializeRemoveFunctionality(newColorItem);

    // Update stock validation if available
    if (window.merchantStockValidator && typeof window.merchantStockValidator.refreshValidation === 'function') {
        setTimeout(() => {
            window.merchantStockValidator.refreshValidation();
        }, 100);
    }

    console.log('New color form added successfully. Total items:', container.children.length);
}

function createNewColorFormHTML(index) {
    return `
        <div class="vue-card color-item transition-all duration-200">
            <div class="p-6 border-b border-slate-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 rounded-full border-2 border-white shadow-sm bg-gray-400"></div>
                        <h4 class="vue-text-lg">Color Variant ${index + 1}</h4>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" class="remove-item p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors">
                            <i class="fas fa-trash w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-6">
                <div class="grid lg:grid-cols-2 gap-6">
                    <!-- Color Details -->
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="block vue-text-sm">
                                    Color Name <span class="text-red-500">*</span>
                                </label>
                                <select name="colors[${index}][name]" class="vue-form-control color-name-select" required>
                                    <option value="">Select color</option>
                                    <option value="Red">Red</option>
                                    <option value="Blue">Blue</option>
                                    <option value="Green">Green</option>
                                    <option value="Navy Blue">Navy Blue</option>
                                    <option value="Forest Green">Forest Green</option>
                                    <option value="Black">Black</option>
                                    <option value="White">White</option>
                                    <option value="Gray">Gray</option>
                                    <option value="Yellow">Yellow</option>
                                    <option value="Orange">Orange</option>
                                    <option value="Purple">Purple</option>
                                    <option value="Pink">Pink</option>
                                    <option value="Brown">Brown</option>
                                    <option value="Silver">Silver</option>
                                    <option value="Gold">Gold</option>
                                    <option value="Maroon">Maroon</option>
                                    <option value="Teal">Teal</option>
                                    <option value="Olive">Olive</option>
                                    <option value="Lime">Lime</option>
                                    <option value="Aqua">Aqua</option>
                                    <option value="Fuchsia">Fuchsia</option>
                                </select>
                            </div>

                            <div class="space-y-2">
                                <label class="block vue-text-sm">Color Code</label>
                                <div class="flex gap-2">
                                    <input type="color" name="colors[${index}][color_code_picker]" value="#000000"
                                           class="w-12 h-10 p-1 border border-slate-300 rounded-lg">
                                    <input type="text" name="colors[${index}][color_code]" placeholder="#000000"
                                           class="vue-form-control flex-1">
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="block vue-text-sm">Price Adjustment</label>
                                <div class="relative">
                                    <i class="fas fa-dollar-sign absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                                    <input type="number" step="0.01" name="colors[${index}][price_adjustment]" value="0"
                                           class="vue-form-control pl-10">
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="block vue-text-sm">Stock Allocation</label>
                                <input type="number" name="colors[${index}][stock]" value="0" min="0"
                                       class="vue-form-control color-stock-input">
                            </div>
                        </div>

                        <input type="hidden" name="colors[${index}][display_order]" value="${index}">
                    </div>

                    <!-- Image Upload -->
                    <div class="space-y-4">
                        <label class="block vue-text-sm">
                            Product Image <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="aspect-[3/4] bg-slate-50 border-2 border-dashed border-slate-300 rounded-lg overflow-hidden hover:border-primary-400 transition-colors image-preview-container">
                                <div class="w-full h-full flex flex-col items-center justify-center cursor-pointer hover:bg-slate-100 transition-colors image-placeholder trigger-image-upload">
                                    <i class="fas fa-image w-12 h-12 text-slate-400 mb-3"></i>
                                    <p class="text-sm font-medium text-slate-600 mb-1">Upload Image</p>
                                    <p class="text-xs text-slate-500">PNG, JPG up to 2MB</p>
                                </div>
                            </div>
                            <input type="file" name="color_images[${index}]" class="color-image-input hidden" accept="image/*">
                            <input type="hidden" name="colors[${index}][image]" value="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function initializeImageUploadForItem(colorItem) {
    const trigger = colorItem.querySelector('.trigger-image-upload');
    const fileInput = colorItem.querySelector('.color-image-input');
    const previewContainer = colorItem.querySelector('.image-preview-container');

    if (trigger && fileInput) {
        trigger.addEventListener('click', function() {
            fileInput.click();
        });

        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const placeholder = previewContainer.querySelector('.image-placeholder');
                    placeholder.innerHTML = `
                        <img src="${e.target.result}" alt="Preview" class="w-full h-full object-cover image-preview">
                    `;
                    previewContainer.classList.add('has-image');
                };
                reader.readAsDataURL(file);
            }
        });
    }
}

function initializeColorPickerForItem(colorItem) {
    const colorPicker = colorItem.querySelector('input[type="color"]');
    const colorCodeInput = colorItem.querySelector('input[name*="[color_code]"]');

    if (colorPicker && colorCodeInput) {
        colorPicker.addEventListener('change', function() {
            colorCodeInput.value = this.value;
        });

        colorCodeInput.addEventListener('input', function() {
            if (this.value.match(/^#[0-9A-F]{6}$/i)) {
                colorPicker.value = this.value;
            }
        });
    }
}

function initializeRemoveFunctionality(colorItem) {
    const removeButton = colorItem.querySelector('.remove-item');
    if (removeButton) {
        removeButton.addEventListener('click', function() {
            if (confirm('Are you sure you want to remove this color variant?')) {
                colorItem.remove();

                // Update stock validation if available
                if (window.merchantStockValidator && typeof window.merchantStockValidator.refreshValidation === 'function') {
                    setTimeout(() => {
                        window.merchantStockValidator.refreshValidation();
                    }, 100);
                }
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('Product edit page loaded');
    window.existingProductData = { colors: [], sizes: [], colorSizes: [] };

    // Image Upload Functionality
        function initializeImageUpload() {
            document.addEventListener('click', function(e) {
                if (e.target.closest('.trigger-image-upload')) {
                    const colorItem = e.target.closest('.color-item');
                    const fileInput = colorItem.querySelector('.color-image-input');
                    if (fileInput) {
                        fileInput.click();
                    }
                }
            });

            document.addEventListener('change', function(e) {
                if (e.target.classList.contains('color-image-input')) {
                    handleImagePreview(e);
                }
            });
        }

        function handleImagePreview(event) {
            const input = event.target;
            const colorItem = input.closest('.color-item');
            const previewContainer = colorItem.querySelector('.image-preview-container');
            const previewImg = previewContainer.querySelector('.image-preview');
            const placeholder = previewContainer.querySelector('.image-placeholder');

            if (input.files && input.files[0]) {
                const file = input.files[0];

                if (!file.type.startsWith('image/')) {
                    alert('Please select a valid image file.');
                    input.value = '';
                    return;
                }

                if (file.size > 2 * 1024 * 1024) {
                    const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
                    alert(`File size (${fileSizeMB}MB) exceeds the 2MB limit.`);
                    input.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    if (previewImg) {
                        previewImg.src = e.target.result;
                        previewImg.style.display = 'block';
                    }
                    if (placeholder) {
                        placeholder.style.display = 'none';
                    }
                    previewContainer.classList.add('has-image');
                };
                reader.readAsDataURL(file);
            }
        }

        // Initialize all functionality with error handling and retry mechanism
        function safeInitialize() {
            try {
                initializeTabNavigation();
                initializeImageUpload();
                console.log('Tab navigation and image upload initialized successfully');
            } catch (error) {
                console.error('Error initializing functionality:', error);
                // Retry after a short delay
                setTimeout(safeInitialize, 100);
            }
        }

        // Initialize immediately and also after a delay to handle timing issues
        safeInitialize();
        setTimeout(safeInitialize, 500);

        // Stock progress updates
        document.getElementById('stock')?.addEventListener('input', updateStockProgress);
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('color-stock-input')) {
                updateStockProgress();
            }
        });

        // Initial stock progress calculation
        updateStockProgress();





        // Remove Item Functionality
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-item')) {
                const item = e.target.closest('.color-item, .specification-item');
                const container = item.closest('#colors-container, #specifications-container');

                if (container.querySelectorAll('.color-item, .specification-item').length > 1) {
                    item.style.transition = 'all 0.3s ease';
                    item.style.opacity = '0';
                    item.style.transform = 'translateY(-10px)';
                    setTimeout(() => {
                        item.remove();
                        updateStockProgress();
                    }, 300);
                } else {
                    alert('At least one item is required.');
                }
            }
        });

        // Set as Default Color Functionality
        document.addEventListener('click', function(e) {
            if (e.target.closest('.set-default-color')) {
                const button = e.target.closest('.set-default-color');
                const colorIndex = button.getAttribute('data-color-index');
                const currentColorItem = button.closest('.color-item');

                // Remove default status from all other colors
                document.querySelectorAll('.color-item').forEach(function(colorItem, index) {
                    const isCurrentColor = colorItem === currentColorItem;
                    const colorHeader = colorItem.querySelector('.vue-text-lg');
                    const defaultBadge = colorItem.querySelector('.inline-flex.items-center');
                    const setDefaultButton = colorItem.querySelector('.set-default-color');
                    const mainImageBadge = colorItem.querySelector('.absolute.top-2.right-2');
                    const hiddenDefaultInput = colorItem.querySelector('input[name*="[is_default]"]');

                    if (isCurrentColor) {
                        // Mark this color as default
                        if (hiddenDefaultInput) {
                            hiddenDefaultInput.checked = true;
                            hiddenDefaultInput.value = '1';
                        }

                        // Add default badge if it doesn't exist
                        if (!defaultBadge && colorHeader) {
                            const badge = document.createElement('span');
                            badge.className = 'ml-2 inline-flex items-center px-2 py-1 text-xs font-medium rounded';
                            badge.style.backgroundColor = 'var(--primary-100)';
                            badge.style.color = 'var(--primary-800)';
                            badge.innerHTML = '<i class="fas fa-star w-3 h-3 mr-1"></i>Default';
                            colorHeader.appendChild(badge);
                        }

                        // Hide the "Set as Default" button
                        if (setDefaultButton) {
                            setDefaultButton.style.display = 'none';
                        }

                        // Add main image badge if image exists
                        const imageContainer = colorItem.querySelector('.relative.w-full.h-full.group');
                        if (imageContainer && !mainImageBadge) {
                            const mainBadge = document.createElement('div');
                            mainBadge.className = 'absolute top-2 right-2';
                            mainBadge.innerHTML = `
                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded"
                                      style="background-color: var(--primary-600); color: white;">
                                    <i class="fas fa-star w-3 h-3 mr-1"></i>
                                    Main Image
                                </span>
                            `;
                            imageContainer.appendChild(mainBadge);
                        }

                        // Add visual ring to indicate default
                        colorItem.classList.add('ring-2', 'ring-primary-500', 'border-primary-200');

                        // Visual feedback animation
                        colorItem.style.transition = 'all 0.3s ease';
                        colorItem.style.transform = 'scale(1.02)';
                        setTimeout(() => {
                            colorItem.style.transform = 'scale(1)';
                        }, 300);

                    } else {
                        // Remove default status from other colors
                        if (hiddenDefaultInput) {
                            hiddenDefaultInput.checked = false;
                            hiddenDefaultInput.value = '';
                        }

                        // Remove default badge
                        if (defaultBadge) {
                            defaultBadge.remove();
                        }

                        // Show the "Set as Default" button
                        if (setDefaultButton) {
                            setDefaultButton.style.display = 'inline-flex';
                        } else {
                            // Create the button if it doesn't exist
                            const buttonContainer = colorItem.querySelector('.flex.items-center.gap-2');
                            if (buttonContainer) {
                                const newButton = document.createElement('button');
                                newButton.type = 'button';
                                newButton.className = 'vue-btn vue-btn-secondary text-sm set-default-color';
                                newButton.setAttribute('data-color-index', index);
                                newButton.textContent = 'Set as Default';
                                buttonContainer.insertBefore(newButton, buttonContainer.firstChild);
                            }
                        }

                        // Remove main image badge
                        if (mainImageBadge) {
                            mainImageBadge.remove();
                        }

                        // Remove visual ring
                        colorItem.classList.remove('ring-2', 'ring-primary-500', 'border-primary-200');

                        // Visual feedback
                        colorItem.style.transition = 'all 0.3s ease';
                        colorItem.style.opacity = '0.8';
                        setTimeout(() => {
                            colorItem.style.opacity = '1';
                        }, 300);
                    }
                });

                console.log('Set color', colorIndex, 'as default');
            }
        });
        // Initialize image preview for existing inputs
        function setupImagePreview() {
            document.querySelectorAll('.color-image-input').forEach(function(input) {
                input.removeEventListener('change', handleImagePreview);
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
            const errorText = errorContainer ? errorContainer.querySelector('.error-text') : null;

            if (errorContainer) {
                errorContainer.classList.add('d-none');
            }

            if (input.files && input.files[0]) {
                const file = input.files[0];

                if (!file.type.startsWith('image/')) {
                    if (errorContainer && errorText) {
                        showImageError(errorContainer, errorText, 'Please select a valid image file.');
                    }
                    input.value = '';
                    return;
                }

                if (file.size > 2 * 1024 * 1024) {
                    const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
                    if (errorContainer && errorText) {
                        showImageError(errorContainer, errorText, `File size (${fileSizeMB}MB) exceeds the 2MB limit.`);
                    }
                    input.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    if (previewImg) {
                        previewImg.src = e.target.result;
                        previewImg.classList.remove('d-none');
                        previewImg.style.display = 'block';
                    } else {
                        // Create new image element if it doesn't exist
                        const newImg = document.createElement('img');
                        newImg.src = e.target.result;
                        newImg.alt = 'Product color variant';
                        newImg.className = 'w-full h-full object-cover image-preview';
                        newImg.onerror = function() {
                            this.style.display = 'none';
                            if (placeholder) {
                                placeholder.style.display = 'flex';
                            }
                        };

                        // Create image container with overlay
                        const imageContainer = document.createElement('div');
                        imageContainer.className = 'relative w-full h-full group';
                        imageContainer.appendChild(newImg);

                        // Add change button in top-left corner
                        const buttonContainer = document.createElement('div');
                        buttonContainer.className = 'absolute top-2 left-2 z-10';

                        const changeButton = document.createElement('button');
                        changeButton.type = 'button';
                        changeButton.className = 'flex items-center justify-center w-8 h-8 bg-white bg-opacity-90 hover:bg-opacity-100 text-slate-700 rounded-full shadow-lg hover:shadow-xl transition-all duration-200 trigger-image-upload group/btn';
                        changeButton.innerHTML = '<i class="fas fa-camera w-4 h-4 group-hover/btn:scale-110 transition-transform"></i><span class="sr-only">Change Image</span>';

                        buttonContainer.appendChild(changeButton);
                        imageContainer.appendChild(buttonContainer);

                        previewContainer.appendChild(imageContainer);
                    }

                    if (placeholder) {
                        placeholder.style.display = 'none';
                    }
                    previewContainer.classList.add('has-image');
                };
                reader.readAsDataURL(file);
            }
        }

        function showImageError(errorContainer, errorText, message) {
            if (errorText) {
                errorText.textContent = message;
                errorContainer.classList.remove('d-none');
                setTimeout(() => {
                    errorContainer.classList.add('d-none');
                }, 5000);
            }
        }

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

                        // Visual feedback for the selected item
                        this.closest('.color-item').style.transition = 'all 0.3s ease';
                        this.closest('.color-item').style.transform = 'scale(1.02)';
                        setTimeout(() => {
                            this.closest('.color-item').style.transform = 'scale(1)';
                        }, 300);
                    }
                });
            });
        }

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

        // Simplified Add Color functionality - Direct and reliable implementation
        function initializeAddColorFunctionality() {
            console.log('Initializing Add Color functionality...');

            // Remove any existing listeners to prevent duplicates
            const addColorButton = document.getElementById('add-color');
            const addFirstColorButton = document.getElementById('add-first-color');

            if (addColorButton) {
                // Clone the button to remove all existing event listeners
                const newAddColorButton = addColorButton.cloneNode(true);
                addColorButton.parentNode.replaceChild(newAddColorButton, addColorButton);

                // Add fresh event listener
                newAddColorButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Add Color button clicked!');
                    addNewColorForm();
                });
                console.log('Add Color button listener attached');
            }

            if (addFirstColorButton) {
                // Clone the button to remove all existing event listeners
                const newAddFirstColorButton = addFirstColorButton.cloneNode(true);
                addFirstColorButton.parentNode.replaceChild(newAddFirstColorButton, addFirstColorButton);

                // Add fresh event listener
                newAddFirstColorButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Add First Color button clicked!');
                    addNewColorForm();
                });
                console.log('Add First Color button listener attached');
            }
        }


        }



        // Clean up DOM structure on page load
        function cleanupDOMStructure() {
            const colorsContainer = document.getElementById('colors-container');
            if (!colorsContainer) return;

            console.log('Cleaning up DOM structure...');

            // First, collect all legitimate color items that should be direct children
            const legitimateColorItems = [];
            document.querySelectorAll('.color-item').forEach(item => {
                // Check if this color item should be a direct child of colors-container
                if (item.closest('#colors-container') === colorsContainer) {
                    legitimateColorItems.push(item);
                }
            });

            console.log('Found legitimate color items:', legitimateColorItems.length);

            // Clear the colors container and re-add only legitimate color items
            const nonColorChildren = Array.from(colorsContainer.children).filter(child =>
                !child.classList.contains('color-item')
            );

            // Define essential elements that should never be removed
            const essentialElements = [
                'specifications-panel',
                'specifications-container',
                'add-specification',
                'submit'
            ];

            // Handle non-color children (move essential elements, remove others)
            nonColorChildren.forEach(child => {
                const isEssential = essentialElements.some(id =>
                    child.id === id ||
                    child.id === `${id}-panel` ||
                    child.querySelector(`#${id}`) ||
                    child.classList.contains('mt-4') && child.classList.contains('d-flex') && child.classList.contains('justify-content-end')
                );

                if (isEssential) {
                    console.warn('Moving essential element back to proper location:', child.className || child.id);
                    const form = document.querySelector('form');
                    if (form) {
                        const colorsSection = colorsContainer.closest('.section-card') || colorsContainer.parentElement;
                        if (colorsSection && colorsSection.nextSibling) {
                            form.insertBefore(child, colorsSection.nextSibling);
                        } else {
                            form.appendChild(child);
                        }
                    }
                } else {
                    console.warn('Removing non-essential misplaced element from colors container on load:', child.className);
                    child.remove();
                }
            });

            // Ensure all legitimate color items are direct children of colors-container
            legitimateColorItems.forEach(item => {
                if (item.parentElement !== colorsContainer) {
                    console.log('Moving color item to correct position in colors-container');
                    colorsContainer.appendChild(item);
                }
            });

            console.log('DOM cleanup complete. Color items in container:', colorsContainer.children.length);
        }

        // Initialize the add color functionality with simplified approach
        console.log('Initializing simplified Add Color functionality...');

        // Wait a moment for all other scripts to load, then initialize
        setTimeout(() => {
            initializeAddColorFunctionality();

            // Initialize existing color items
            const existingColorItems = document.querySelectorAll('.color-item');
            existingColorItems.forEach(item => {
                initializeImageUploadForItem(item);
                initializeColorPickerForItem(item);
                initializeRemoveFunctionality(item);
            });

            console.log('Add Color functionality initialized successfully');
        }, 500);

        // Add Specification functionality
        document.getElementById('add-specification').addEventListener('click', function() {
            const container = document.getElementById('specifications-container');
            const specItems = container.querySelectorAll('.specification-item');
            const newIndex = specItems.length;

            const firstSpecItem = specItems[0];
            const newSpecItem = firstSpecItem.cloneNode(true);

            newSpecItem.querySelectorAll('[name]').forEach(function(input) {
                const name = input.getAttribute('name');
                const newName = name.replace(/\[\d+\]/, `[${newIndex}]`);
                input.setAttribute('name', newName);
                input.value = '';
            });

            container.appendChild(newSpecItem);
        });

        // Remove functionality
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-item')) {
                const item = e.target.closest('.color-item, .specification-item');
                const container = item.closest('#colors-container, #specifications-container');

                if (container.querySelectorAll('.color-item, .specification-item').length > 1) {
                    // Add fade-out animation before removal
                    item.style.transition = 'all 0.3s ease';
                    item.style.opacity = '0';
                    item.style.transform = 'translateY(-10px)';
                    setTimeout(() => {
                        item.remove();
                    }, 300);
                } else {
                    alert('At least one item is required.');
                }
            }
        });

        // Initialize stock validation integration
        function setupStockValidationIntegration() {
            // Note: Add color button listener is now handled in the main add color functionality
            // to prevent duplicate event listeners and form duplication issues

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

        // Initialize existing color-size data
        function initializeExistingData() {
            if (window.existingProductData && window.existingProductData.colors) {
                // Wait for the dynamic color-size management system to be ready
                setTimeout(() => {
                    window.existingProductData.colors.forEach(function(colorData, colorIndex) {
                        const colorItem = document.querySelector(`[name="colors[${colorIndex}][name]"]`)?.closest('.color-item');
                        if (colorItem) {
                            const colorSelect = colorItem.querySelector('.color-name-select');
                            const stockInput = colorItem.querySelector('.color-stock-input');

                            // Show size allocation if color has stock OR if there are existing size allocations
                            const hasStock = colorData.stock > 0;
                            const hasExistingSizes = (colorData.sizes_with_allocations && colorData.sizes_with_allocations.length > 0) ||
                                                   (colorData.sizes && colorData.sizes.length > 0);

                            if (colorSelect && stockInput && colorSelect.value && (hasStock || hasExistingSizes)) {
                                // Trigger the color selection event to show sizes
                                const event = new Event('change', { bubbles: true });
                                colorSelect.dispatchEvent(event);

                                // Wait for the size allocation section to be created, then populate it
                                setTimeout(() => {
                                    populateExistingSizesForColor(colorItem, colorData, colorIndex);
                                }, 1000);
                            }
                        }
                    });
                }, 1000);
            }
        }

        // Function to populate existing sizes for a color
        function populateExistingSizesForColor(colorItem, colorData, colorIndex) {
            const sizeAllocationSection = colorItem.querySelector('.color-size-allocation');

            // Check for sizes_with_allocations first, then fall back to sizes
            const sizesData = colorData.sizes_with_allocations || colorData.sizes || [];

            if (sizeAllocationSection && sizesData.length > 0) {
                const sizeGrid = sizeAllocationSection.querySelector('.size-allocation-grid');
                if (sizeGrid) {
                    // Clear any existing content
                    sizeGrid.innerHTML = '';

                    // Add existing sizes
                    sizesData.forEach((sizeData, sizeIndex) => {
                        const sizeItem = createExistingSizeAllocationItem(colorIndex, sizeIndex, sizeData);
                        sizeGrid.appendChild(sizeItem);
                    });

                    // Update stock summary
                    updateStockSummaryForColor(colorItem, colorData);
                }
            }
        }

        // Function to create a size allocation item for existing data
        function createExistingSizeAllocationItem(colorIndex, sizeIndex, sizeData) {
            const item = document.createElement('div');
            item.className = 'size-allocation-item p-4 bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 shadow-sm hover:shadow-md transition-shadow duration-200 mb-4';

            item.innerHTML = `
                <div class="grid grid-cols-12 gap-4 items-center">
                    <div class="col-span-3">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Size</label>
                        <div class="p-2 bg-gray-50 dark:bg-gray-800 rounded border">
                            <span class="text-sm font-medium">${sizeData.name || 'Size ' + (sizeIndex + 1)}</span>
                            ${sizeData.value ? `<br><span class="text-xs text-gray-500">${sizeData.value}</span>` : ''}
                        </div>
                        <input type="hidden" name="color_sizes[${colorIndex}][${sizeIndex}][size_name]" value="${sizeData.name || ''}">
                        <input type="hidden" name="color_sizes[${colorIndex}][${sizeIndex}][size_value]" value="${sizeData.value || ''}">
                        <input type="hidden" name="color_sizes[${colorIndex}][${sizeIndex}][size_id]" value="${sizeData.id || ''}">
                    </div>
                    <div class="col-span-3">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Stock</label>
                        <input type="number"
                               name="color_sizes[${colorIndex}][${sizeIndex}][stock]"
                               value="${sizeData.stock || 0}"
                               min="0"
                               class="size-stock-input w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                               data-color-index="${colorIndex}"
                               data-size-index="${sizeIndex}">
                    </div>
                    <div class="col-span-3">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Price +/-</label>
                        <input type="number"
                               name="color_sizes[${colorIndex}][${sizeIndex}][price_adjustment]"
                               value="${sizeData.price_adjustment || 0}"
                               step="0.01"
                               class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Available</label>
                        <input type="checkbox"
                               name="color_sizes[${colorIndex}][${sizeIndex}][is_available]"
                               value="1"
                               ${sizeData.is_available ? 'checked' : ''}
                               class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                    </div>
                    <div class="col-span-1">
                        <button type="button" class="remove-size-allocation text-red-600 hover:text-red-800 p-1">
                            <i class="fas fa-trash text-sm"></i>
                        </button>
                    </div>
                </div>
            `;

            return item;
        }

        // Function to update stock summary for a color
        function updateStockSummaryForColor(colorItem, colorData) {
            const stockSummary = colorItem.querySelector('.stock-allocation-summary');
            if (stockSummary && colorData.sizes) {
                const totalAllocated = colorData.sizes.reduce((sum, size) => sum + (parseInt(size.stock) || 0), 0);
                const totalStock = parseInt(colorData.stock) || 0;
                const remaining = totalStock - totalAllocated;

                const allocatedSpan = stockSummary.querySelector('.allocated-stock');
                const remainingSpan = stockSummary.querySelector('.remaining-stock');
                const totalSpan = stockSummary.querySelector('.total-stock');

                if (allocatedSpan) allocatedSpan.textContent = totalAllocated;
                if (remainingSpan) remainingSpan.textContent = remaining;
                if (totalSpan) totalSpan.textContent = totalStock;

                // Update progress bar if it exists
                const progressBar = stockSummary.querySelector('.progress-bar');
                if (progressBar && totalStock > 0) {
                    const percentage = (totalAllocated / totalStock) * 100;
                    progressBar.style.width = `${percentage}%`;
                }
            }
        }

}); // End DOMContentLoaded

</script>
@endsection


