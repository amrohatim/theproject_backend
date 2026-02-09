@extends('layouts.dashboard')

@section('title', 'Add Product')
@section('page-title', 'Add Product')

@php
    $businessTypeCategoryMap = $businessTypeCategoryMap
        ?? \App\Models\BusinessType::query()
            ->pluck('product_categories', 'business_name')
            ->map(function ($categories) {
                $ids = is_array($categories) ? $categories : [];
                return array_values(array_filter($ids, static fn ($id) => is_numeric($id)));
            })
            ->toArray();
@endphp

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/mdbassit/Coloris@latest/dist/coloris.min.css"/>
<link rel="stylesheet" href="{{ asset('css/enhanced-color-management.css') }}"/>
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
        border-color: #10b981 !important;
        background-color: #f0fdf4 !important;
        box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.1), 0 2px 4px -1px rgba(16, 185, 129, 0.06);
    }

    .image-preview {
        transition: opacity 0.3s ease;
    }

    .image-placeholder {
        transition: opacity 0.3s ease;
    }

    /* Dark mode adjustments */
    @media (prefers-color-scheme: dark) {
        .image-preview-container {
            border-color: #374151;
            background-color: #1f2937;
        }

        .image-preview-container.has-image {
            border-color: #10b981 !important;
            background-color: #064e3b !important;
        }
    }

    .section-card {
        transition: all 0.3s ease;
        margin-bottom: 2rem;
    }

    .section-card:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    /* Smooth scrolling */
    html {
        scroll-behavior: smooth;
    }

    /* Section dividers */
    .section-divider {
        height: 1px;
        background: linear-gradient(to right, transparent, rgba(156, 163, 175, 0.5), transparent);
        margin: 2rem 0;
    }

    /* Color swatch styling */
    .color-swatch {
        cursor: pointer;
        transition: transform 0.2s;
    }

    .color-swatch:hover {
        transform: scale(1.1) translateY(-50%) !important;
    }

    /* Coloris customization */
    .clr-field button {
        width: 28px;
        height: 28px;
        left: auto;
        right: 8px;
        border-radius: 5px;
    }
</style>
@endsection

@section('content')
<div class="container mx-auto">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Add Product</h2>
                <p class="mt-1 text-gray-600 dark:text-gray-400">Create a new product for your store</p>
            </div>
            <div>
                <a href="{{ route('vendor.products.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Products
                </a>
            </div>
        </div>
    </div>

    <!-- Product form -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
        <form action="{{ route('vendor.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Section Navigation -->
            <div class="mb-6">
                <div class="border-b border-gray-200 dark:border-gray-700 pb-2">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Product Information Sections</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">All sections are displayed below for easy editing</p>
                </div>
            </div>

            <!-- Basic Information Section -->
            <div id="basic-panel" class="mb-8 bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-6">
                    <h3 class="text-xl font-medium text-gray-900 dark:text-white">1. Basic Information</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Basic Information -->
                    <div class="space-y-4">

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Product Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category <span class="text-red-500">*</span></label>
                        <select id="category_id" name="category_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                            <option value="">Select Category</option>
                            @foreach($parentCategories ?? [] as $parentCategory)
                                <optgroup label="{{ $parentCategory->name }}">
                                    <!-- Parent category as disabled option -->
                                    {{-- <option value="{{ $parentCategory->id }}" disabled style="color: #9ca3af; font-weight: bold;">{{ $parentCategory->name }}</option> --}}

                                    <!-- Child categories -->
                                    @foreach($parentCategory->children as $childCategory)
                                        <option value="{{ $childCategory->id }}" data-category-id="{{ $childCategory->id }}" {{ old('category_id') == $childCategory->id ? 'selected' : '' }}>&nbsp;&nbsp;{{ $childCategory->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Branch -->
                    <div>
                        <label for="branch_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Branch <span class="text-red-500">*</span></label>
                        <select id="branch_id" name="branch_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                            <option value="">Select Branch</option>
                            @foreach($branches ?? [] as $branch)
                                <option value="{{ $branch->id }}" data-business-type="{{ $branch->business_type }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                        @error('branch_id')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                        <textarea id="description" name="description" rows="4" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Pricing and Inventory -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Pricing & Inventory</h3>

                    <!-- Price -->
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Price <span class="text-red-500">*</span></label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input type="number" name="price" id="price" min="0" step="0.01" value="{{ old('price') }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" placeholder="0.00" required>
                        </div>
                        @error('price')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Original Price -->
                    <div>
                        <label for="original_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Original Price (if on sale)</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input type="number" name="original_price" id="original_price" min="0" step="0.01" value="{{ old('original_price') }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" placeholder="0.00">
                        </div>
                        @error('original_price')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Stock -->
                    <div>
                        <label for="stock" class="block text-sm font-medium text-gray-700 dark:text-gray-300">General Stock <span class="text-red-500">*</span></label>
                        <input type="number" name="stock" id="stock" min="0" value="{{ old('stock', 0) }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" required>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Total stock quantity available for all color variants</p>
                        @error('stock')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror

                        <!-- Stock Allocation Summary -->
                        <div id="stock-summary" class="mt-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-md border hidden">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Stock Allocation</span>
                                <span id="stock-status-badge" class="px-2 py-1 text-xs font-medium rounded-full"></span>
                            </div>
                            <div class="space-y-1 text-xs text-gray-600 dark:text-gray-400">
                                <div class="flex justify-between">
                                    <span>Total Available:</span>
                                    <span id="total-available-stock" class="font-medium">0</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Allocated to Colors:</span>
                                    <span id="total-allocated-stock" class="font-medium">0</span>
                                </div>
                                <div class="flex justify-between border-t border-gray-200 dark:border-gray-600 pt-1">
                                    <span>Remaining:</span>
                                    <span id="remaining-stock" class="font-medium"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Availability -->
                    <div>
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="is_available" name="is_available" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 dark:border-gray-600 rounded" {{ old('is_available', '1') == '1' ? 'checked' : '' }} value="1">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="is_available" class="font-medium text-gray-700 dark:text-gray-300">Available for purchase</label>
                                <p class="text-gray-500 dark:text-gray-400">Uncheck if this product is not available for purchase.</p>
                            </div>
                        </div>
                        @error('is_available')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Note about product image -->
                    <div>
                        <div class="bg-blue-50 dark:bg-blue-900 border-l-4 border-blue-400 p-4 mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700 dark:text-blue-300">
                                        Product images must be associated with colors. Please add at least one color with an image in the Colors section below.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colors Section (moved up to be second section) -->
            <div id="colors-panel" class="mb-8 bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-6">
                    <h3 class="text-xl font-medium text-gray-900 dark:text-white">2. Product Colors, Images, and Sizes</h3>
                </div>
                <div class="mb-4">
                    <div class="bg-yellow-50 dark:bg-yellow-900 border-l-4 border-yellow-400 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700 dark:text-yellow-300">
                                    <strong>Required:</strong> Each product must have at least one color with an associated image. The color marked as default will have its image used as the main product image.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-4 flex justify-between items-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Add color options with images, sizes, and stock allocation. Each color can have its own size variants.</p>
                    <div class="relative">
                        <button type="button" id="add-color" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-indigo-600">
                            <i class="fas fa-plus mr-2"></i> Add Color
                        </button>
                        <!-- Tooltip for disabled state -->
                        <div id="add-color-tooltip" class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs text-white bg-gray-900 rounded-md opacity-0 pointer-events-none transition-opacity duration-200 whitespace-nowrap hidden">
                            All available stock has been allocated to color variants
                            <div class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
                        </div>
                    </div>
                </div>

                <div id="colors-container" class="space-y-6">
                    <div class="color-item border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <div class="grid grid-cols-12 gap-4">
                            <div class="col-span-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Color Name <span class="text-red-500">*</span></label>
                                <select name="colors[0][name]" class="color-name-select focus:ring-amber-500 focus:border-amber-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" required>
                                    <option value="">Select Color</option>
                                    <option value="Red">Red</option>
                                    <option value="Crimson">Crimson</option>
                                    <option value="FireBrick">FireBrick</option>
                                    <option value="DarkRed">DarkRed</option>
                                    <option value="IndianRed">IndianRed</option>
                                    <option value="LightCoral">LightCoral</option>
                                    <option value="Salmon">Salmon</option>
                                    <option value="DarkSalmon">DarkSalmon</option>
                                    <option value="LightSalmon">LightSalmon</option>
                                    <option value="Orange">Orange</option>
                                    <option value="DarkOrange">DarkOrange</option>
                                    <option value="Coral">Coral</option>
                                    <option value="Tomato">Tomato</option>
                                    <option value="Gold">Gold</option>
                                    <option value="Yellow">Yellow</option>
                                    <option value="LightYellow">LightYellow</option>
                                    <option value="LemonChiffon">LemonChiffon</option>
                                    <option value="Khaki">Khaki</option>
                                    <option value="DarkKhaki">DarkKhaki</option>
                                    <option value="Green">Green</option>
                                    <option value="Lime">Lime</option>
                                    <option value="ForestGreen">ForestGreen</option>
                                    <option value="DarkGreen">DarkGreen</option>
                                    <option value="SeaGreen">SeaGreen</option>
                                    <option value="MediumSeaGreen">MediumSeaGreen</option>
                                    <option value="LightGreen">LightGreen</option>
                                    <option value="PaleGreen">PaleGreen</option>
                                    <option value="SpringGreen">SpringGreen</option>
                                    <option value="MediumSpringGreen">MediumSpringGreen</option>
                                    <option value="YellowGreen">YellowGreen</option>
                                    <option value="Olive">Olive</option>
                                    <option value="DarkOliveGreen">DarkOliveGreen</option>
                                    <option value="Blue">Blue</option>
                                    <option value="MediumBlue">MediumBlue</option>
                                    <option value="DarkBlue">DarkBlue</option>
                                    <option value="Navy">Navy</option>
                                    <option value="SkyBlue">SkyBlue</option>
                                    <option value="LightSkyBlue">LightSkyBlue</option>
                                    <option value="DeepSkyBlue">DeepSkyBlue</option>
                                    <option value="DodgerBlue">DodgerBlue</option>
                                    <option value="SteelBlue">SteelBlue</option>
                                    <option value="CornflowerBlue">CornflowerBlue</option>
                                    <option value="RoyalBlue">RoyalBlue</option>
                                    <option value="LightBlue">LightBlue</option>
                                    <option value="PowderBlue">PowderBlue</option>
                                    <option value="Purple">Purple</option>
                                    <option value="MediumPurple">MediumPurple</option>
                                    <option value="BlueViolet">BlueViolet</option>
                                    <option value="Violet">Violet</option>
                                    <option value="Orchid">Orchid</option>
                                    <option value="Magenta">Magenta</option>
                                    <option value="Fuchsia">Fuchsia</option>
                                    <option value="DeepPink">DeepPink</option>
                                    <option value="HotPink">HotPink</option>
                                    <option value="LightPink">LightPink</option>
                                    <option value="PaleVioletRed">PaleVioletRed</option>
                                    <option value="Brown">Brown</option>
                                    <option value="SaddleBrown">SaddleBrown</option>
                                    <option value="Sienna">Sienna</option>
                                    <option value="Chocolate">Chocolate</option>
                                    <option value="Peru">Peru</option>
                                    <option value="Tan">Tan</option>
                                    <option value="RosyBrown">RosyBrown</option>
                                    <option value="SandyBrown">SandyBrown</option>
                                    <option value="BurlyWood">BurlyWood</option>
                                    <option value="Wheat">Wheat</option>
                                    <option value="NavajoWhite">NavajoWhite</option>
                                    <option value="Black">Black</option>
                                    <option value="DimGray">DimGray</option>
                                    <option value="Gray">Gray</option>
                                    <option value="DarkGray">DarkGray</option>
                                    <option value="Silver">Silver</option>
                                    <option value="LightGray">LightGray</option>
                                    <option value="Gainsboro">Gainsboro</option>
                                    <option value="WhiteSmoke">WhiteSmoke</option>
                                    <option value="White">White</option>
                                </select>
                            </div>
                            <div class="col-span-3">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Color Code</label>
                                <div class="relative">
                                    <input type="text" name="colors[0][color_code]" placeholder="#FF0000" class="color-code-input focus:ring-amber-500 focus:border-amber-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md pr-12" data-coloris>
                                    <div class="color-preview absolute right-2 top-1/2 transform -translate-y-1/2 w-8 h-8 rounded border border-gray-300 dark:border-gray-600 cursor-pointer" style="background-color: #ffffff;"></div>
                                </div>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Price Adjustment</label>
                                <input type="number" step="0.01" name="colors[0][price_adjustment]" placeholder="0.00" value="0" class="focus:ring-amber-500 focus:border-amber-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Total Stock</label>
                                <input type="number" name="colors[0][stock]" placeholder="10" value="0" min="0" class="color-stock-input focus:ring-amber-500 focus:border-amber-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md transition-colors duration-200">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Total stock to allocate across sizes</p>
                            </div>
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Display Order</label>
                                <input type="number" name="colors[0][display_order]" placeholder="0" value="0" class="focus:ring-amber-500 focus:border-amber-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                            </div>
                            <div class="col-span-1 flex items-end justify-center">
                                <button type="button" class="remove-item text-red-500 hover:text-red-700">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex flex-col">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Color Image <span class="text-red-500">*</span></label>

                                <!-- Image Preview Container -->
                                <div class="image-preview-container mb-3" style="width: 300px; height: 400px; border: 2px dashed #d1d5db; border-radius: 8px; background-color: #f9fafb; display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative;">
                                    <img class="image-preview hidden" style="width: 100%; height: 100%; object-fit: cover; border-radius: 6px;" alt="Image Preview">
                                    <div class="image-placeholder text-center">
                                        <i class="fas fa-image text-gray-400 text-4xl mb-2"></i>
                                        <p class="text-gray-500 text-sm">No image selected</p>
                                        <p class="text-gray-400 text-xs">300x400px preview</p>
                                    </div>
                                </div>

                                <input type="file" name="color_images[0]" class="color-image-input block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-gray-700 dark:file:text-gray-200 mb-2" required accept="image/*" style="max-width: 300px;">
                                <input type="hidden" name="colors[0][image]" value="">

                                <!-- Error message container for image validation -->
                                <div class="image-error-message hidden mt-2 p-3 bg-red-50 border border-red-200 rounded-md" style="max-width: 300px;">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-red-700 error-text"></p>
                                        </div>
                                    </div>
                                </div>

                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 mb-4" style="max-width: 300px;">
                                    PNG, JPG, GIF up to 20MB
                                </p>

                                <!-- Default Color Checkbox - positioned below image -->
                                <div class="mt-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Default Color</label>
                                    <div class="flex items-start">
                                        <input type="checkbox" name="colors[0][is_default]" value="1" class="default-color-checkbox focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded mt-1" checked>
                                        <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">This image will be the main product image</span>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>





            <!-- Specifications Section -->
            <div id="specifications-panel" class="mb-8 bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-6">
                    <h3 class="text-xl font-medium text-gray-900 dark:text-white">3. Product Specifications</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Add key-value specifications for your product</p>
                </div>
                <div class="mb-4 flex justify-between items-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Add technical specifications, features, or other product details</p>
                    <button type="button" id="add-specification" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-plus mr-2"></i> Add Specification
                    </button>
                </div>

                <div id="specifications-container" class="space-y-4">
                    <div class="specification-item grid grid-cols-12 gap-4 items-center">
                        <div class="col-span-5">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Key</label>
                            <input type="text" name="specifications[0][key]" placeholder="Material" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                        </div>
                        <div class="col-span-5">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Value</label>
                            <input type="text" name="specifications[0][value]" placeholder="Cotton" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                        </div>
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Order</label>
                            <input type="number" name="specifications[0][display_order]" placeholder="0" value="0" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                        </div>
                        <div class="col-span-1 flex items-end justify-center">
                            <button type="button" class="remove-item text-red-500 hover:text-red-700">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Submit Button -->
            <div class="mt-8 flex justify-end">
                <button type="submit" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-save mr-2"></i> Save Product
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
                preview.classList.remove('hidden');
                preview.querySelector('img').src = e.target.result;

                // Hide placeholder
                placeholder.classList.add('hidden');

                // Update file name
                fileNameElement.textContent = input.files[0].name;
            }

            reader.readAsDataURL(input.files[0]);
        } else {
            // Hide preview
            preview.classList.add('hidden');

            // Show placeholder
            placeholder.classList.remove('hidden');

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
            errorContainer.classList.add('hidden');

            if (input.files && input.files[0]) {
                const file = input.files[0];

                // Validate file type
                if (!file.type.startsWith('image/')) {
                    showImageError(errorContainer, errorText, 'Please select a valid image file.');
                    input.value = '';
                    return;
                }

                // Enhanced file size validation (20MB limit) with immediate feedback
                if (file.size > 20 * 1024 * 1024) {
                    const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
                    showImageError(errorContainer, errorText, `File size (${fileSizeMB}MB) exceeds the 20MB limit. Please choose a smaller image.`);
                    input.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewImg.classList.remove('hidden');
                    placeholder.style.display = 'none';

                    // Add CSS class for selected state
                    previewContainer.classList.add('has-image');
                };
                reader.readAsDataURL(file);
            } else {
                // Reset to placeholder state
                previewImg.classList.add('hidden');
                previewImg.src = '';
                placeholder.style.display = 'block';

                // Remove CSS class for selected state
                previewContainer.classList.remove('has-image');
            }
        }

        function showImageError(errorContainer, errorText, message) {
            errorText.textContent = message;
            errorContainer.classList.remove('hidden');

            // Auto-hide error after 5 seconds
            setTimeout(() => {
                errorContainer.classList.add('hidden');
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

        function setupCategoryBusinessTypeFilter() {
            const branchSelect = document.getElementById('branch_id');
            const categorySelect = document.getElementById('category_id');
            if (!branchSelect || !categorySelect) {
                return;
            }

            const businessTypeCategoryMap = @json($businessTypeCategoryMap ?? []);

            function applyFilter() {
                const selectedOption = branchSelect.options[branchSelect.selectedIndex];
                const businessType = selectedOption ? selectedOption.getAttribute('data-business-type') : '';
                const allowedIds = businessTypeCategoryMap[businessType] || null;
                const hasFilter = Array.isArray(allowedIds) && allowedIds.length > 0;

                if (!branchSelect.value) {
                    categorySelect.value = '';
                    categorySelect.disabled = true;
                    categorySelect.querySelectorAll('option[data-category-id]').forEach(function(option) {
                        option.hidden = true;
                    });
                    categorySelect.querySelectorAll('optgroup').forEach(function(group) {
                        group.hidden = true;
                    });
                    return;
                }

                categorySelect.disabled = false;

                categorySelect.querySelectorAll('option[data-category-id]').forEach(function(option) {
                    const categoryId = parseInt(option.getAttribute('data-category-id'), 10);
                    const show = hasFilter && Number.isFinite(categoryId) && allowedIds.includes(categoryId);
                    option.hidden = !show;
                });

                categorySelect.querySelectorAll('optgroup').forEach(function(group) {
                    const hasVisible = Array.from(group.querySelectorAll('option[data-category-id]')).some(function(option) {
                        return !option.hidden;
                    });
                    group.hidden = !hasVisible;
                });

                const selectedCategory = categorySelect.options[categorySelect.selectedIndex];
                if (selectedCategory && selectedCategory.getAttribute('data-category-id') && selectedCategory.hidden) {
                    categorySelect.value = '';
                }
            }

            branchSelect.addEventListener('change', applyFilter);
            applyFilter();
        }

        // Setup stock validation for general stock field
        function setupStockValidation() {
            const generalStockInput = document.getElementById('stock');
            if (generalStockInput) {
                generalStockInput.addEventListener('input', function() {
                    updateStockSummary();

                    // Validate all existing color stocks against new general stock
                    document.querySelectorAll('.color-stock-input').forEach(function(colorInput) {
                        validateColorStock(colorInput);
                    });
                });

                // Initial update
                updateStockSummary();
            }
        }

        // Enhanced color stock change listeners for dynamic size allocation
        function setupColorStockListeners() {
            document.querySelectorAll('.color-stock-input').forEach(function(input) {
                input.addEventListener('input', function() {
                    const colorItem = this.closest('.color-item');
                    const colorSelect = colorItem.querySelector('.color-name-select');
                    const stockValue = parseInt(this.value) || 0;

                    // Add real-time visual feedback to the stock input
                    addStockInputFeedback(this, stockValue);

                    // Validate stock against general stock limit
                    validateColorStock(this);

                    // Always trigger the dynamic color-size manager for proper handling
                    if (colorSelect && colorSelect.value) {
                        if (stockValue > 0) {
                            // Dispatch event for positive stock
                            const event = new CustomEvent('colorStockChanged', {
                                detail: {
                                    colorItem: colorItem,
                                    colorName: colorSelect.value,
                                    stock: stockValue
                                }
                            });
                            document.dispatchEvent(event);
                        } else {
                            // Dispatch event for zero/negative stock to trigger proper hiding
                            const event = new CustomEvent('colorStockChanged', {
                                detail: {
                                    colorItem: colorItem,
                                    colorName: colorSelect.value,
                                    stock: 0 // Ensure it's 0 for proper handling
                                }
                            });
                            document.dispatchEvent(event);

                            // Also directly call the dynamic manager if available
                            if (window.dynamicColorSizeManager) {
                                window.dynamicColorSizeManager.hideSizesForColor(colorItem);
                            }
                        }
                    } else {
                        // No color selected - hide any existing size sections
                        if (window.dynamicColorSizeManager) {
                            window.dynamicColorSizeManager.hideSizesForColor(colorItem);
                        } else {
                            // Fallback: remove size allocation section manually
                            const sizeSection = colorItem.querySelector('.color-size-allocation');
                            if (sizeSection) {
                                sizeSection.remove();
                            }
                        }
                    }
                });

                // Add blur event for validation
                input.addEventListener('blur', function() {
                    validateColorStock(this);
                });
            });
        }

        // Add visual feedback for stock input changes
        function addStockInputFeedback(input, value) {
            // Remove existing feedback classes
            input.classList.remove('border-green-500', 'border-yellow-500', 'border-red-500', 'bg-green-50', 'bg-yellow-50', 'bg-red-50');

            if (value > 0) {
                input.classList.add('border-green-500', 'bg-green-50');
            } else if (value === 0) {
                input.classList.add('border-yellow-500', 'bg-yellow-50');
            } else {
                input.classList.add('border-red-500', 'bg-red-50');
            }

            // Remove feedback after 2 seconds
            setTimeout(() => {
                input.classList.remove('border-green-500', 'border-yellow-500', 'border-red-500', 'bg-green-50', 'bg-yellow-50', 'bg-red-50');
            }, 2000);
        }

        // Enhanced stock validation with general stock limit checking
        function validateColorStock(input) {
            const value = parseInt(input.value) || 0;
            const colorItem = input.closest('.color-item');
            const generalStock = parseInt(document.getElementById('stock').value) || 0;

            // Remove any existing validation message
            const existingMessage = colorItem.querySelector('.color-stock-validation');
            if (existingMessage) {
                existingMessage.remove();
            }

            if (value < 0) {
                // Show error for negative values
                showColorStockError(input, 'Stock cannot be negative');
                input.value = 0;
                addStockInputFeedback(input, 0);
                updateStockSummary();
                return;
            }

            // Check against general stock limit
            const totalAllocated = calculateTotalAllocatedStock();
            const currentColorStock = parseInt(input.value) || 0;
            const otherColorsStock = totalAllocated - currentColorStock;

            if (otherColorsStock + value > generalStock) {
                const maxAllowed = generalStock - otherColorsStock;
                showColorStockError(input, `Exceeds available stock. Maximum allowed: ${maxAllowed}`);
                input.value = Math.max(0, maxAllowed);
                addStockInputFeedback(input, Math.max(0, maxAllowed));
            }

            updateStockSummary();
        }

        // Show color stock validation error
        function showColorStockError(input, message) {
            const colorItem = input.closest('.color-item');
            const errorMessage = document.createElement('div');
            errorMessage.className = 'color-stock-validation mt-1 text-xs text-red-600 dark:text-red-400';
            errorMessage.innerHTML = `<i class="fas fa-exclamation-triangle mr-1"></i>${message}`;
            input.parentNode.appendChild(errorMessage);

            // Auto-remove error after 5 seconds
            setTimeout(() => {
                if (errorMessage.parentNode) {
                    errorMessage.remove();
                }
            }, 5000);
        }

        // Calculate total allocated stock across all color variants
        function calculateTotalAllocatedStock() {
            let total = 0;
            document.querySelectorAll('.color-stock-input').forEach(function(input) {
                total += parseInt(input.value) || 0;
            });
            return total;
        }

        // Update stock summary display
        function updateStockSummary() {
            const generalStock = parseInt(document.getElementById('stock').value) || 0;
            const totalAllocated = calculateTotalAllocatedStock();
            const remaining = generalStock - totalAllocated;

            const summaryDiv = document.getElementById('stock-summary');
            const totalAvailableSpan = document.getElementById('total-available-stock');
            const totalAllocatedSpan = document.getElementById('total-allocated-stock');
            const remainingSpan = document.getElementById('remaining-stock');
            const statusBadge = document.getElementById('stock-status-badge');

            // Show/hide summary based on general stock value
            if (generalStock > 0) {
                summaryDiv.classList.remove('hidden');

                totalAvailableSpan.textContent = generalStock;
                totalAllocatedSpan.textContent = totalAllocated;
                remainingSpan.textContent = remaining;

                // Update status badge
                if (remaining < 0) {
                    statusBadge.textContent = 'Over Allocated';
                    statusBadge.className = 'px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
                    remainingSpan.className = 'font-medium text-red-600 dark:text-red-400';
                } else if (remaining === 0) {
                    statusBadge.textContent = 'Fully Allocated';
                    statusBadge.className = 'px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
                    remainingSpan.className = 'font-medium text-yellow-600 dark:text-yellow-400';
                } else {
                    statusBadge.textContent = 'Available';
                    statusBadge.className = 'px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
                    remainingSpan.className = 'font-medium text-green-600 dark:text-green-400';
                }
            } else {
                summaryDiv.classList.add('hidden');
            }

            // Update Add Color button state
            updateAddColorButtonState();
        }

        // Update Add Color button state based on stock availability
        function updateAddColorButtonState() {
            const generalStock = parseInt(document.getElementById('stock').value) || 0;
            const totalAllocated = calculateTotalAllocatedStock();
            const remaining = generalStock - totalAllocated;

            const addColorBtn = document.getElementById('add-color');
            const tooltip = document.getElementById('add-color-tooltip');

            if (remaining <= 0 && generalStock > 0) {
                addColorBtn.disabled = true;
                addColorBtn.classList.add('opacity-50', 'cursor-not-allowed');

                // Show tooltip on hover
                addColorBtn.addEventListener('mouseenter', showAddColorTooltip);
                addColorBtn.addEventListener('mouseleave', hideAddColorTooltip);
            } else {
                addColorBtn.disabled = false;
                addColorBtn.classList.remove('opacity-50', 'cursor-not-allowed');

                // Remove tooltip listeners
                addColorBtn.removeEventListener('mouseenter', showAddColorTooltip);
                addColorBtn.removeEventListener('mouseleave', hideAddColorTooltip);
                hideAddColorTooltip();
            }
        }

        // Show tooltip for disabled Add Color button
        function showAddColorTooltip() {
            const tooltip = document.getElementById('add-color-tooltip');
            tooltip.classList.remove('hidden');
            setTimeout(() => {
                tooltip.classList.remove('opacity-0');
            }, 10);
        }

        // Hide tooltip for Add Color button
        function hideAddColorTooltip() {
            const tooltip = document.getElementById('add-color-tooltip');
            tooltip.classList.add('opacity-0');
            setTimeout(() => {
                tooltip.classList.add('hidden');
            }, 200);
        }

        // Initialize all validation functions
        setupDefaultColorSelection();
        setupCategoryValidation();
        setupCategoryBusinessTypeFilter();
        setupStockValidation();

        // Setup color stock change listeners for dynamic size allocation
        setupColorStockListeners();

        // Add color
        const addColorBtn = document.getElementById('add-color');
        const colorsContainer = document.getElementById('colors-container');

        if (addColorBtn && colorsContainer) {
            addColorBtn.addEventListener('click', function() {
                const items = colorsContainer.querySelectorAll('.color-item');
                const newIndex = items.length;

                const newItem = document.createElement('div');
                newItem.className = 'color-item border border-gray-200 dark:border-gray-700 rounded-lg p-4';
                newItem.innerHTML = `
                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Color Name</label>
                            <select name="colors[${newIndex}][name]" class="color-name-select focus:ring-amber-500 focus:border-amber-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" required>
                                <option value="">Select Color</option>
                                <option value="Red">Red</option>
                                <option value="Crimson">Crimson</option>
                                <option value="FireBrick">FireBrick</option>
                                <option value="DarkRed">DarkRed</option>
                                <option value="IndianRed">IndianRed</option>
                                <option value="LightCoral">LightCoral</option>
                                <option value="Salmon">Salmon</option>
                                <option value="DarkSalmon">DarkSalmon</option>
                                <option value="LightSalmon">LightSalmon</option>
                                <option value="Orange">Orange</option>
                                <option value="DarkOrange">DarkOrange</option>
                                <option value="Coral">Coral</option>
                                <option value="Tomato">Tomato</option>
                                <option value="Gold">Gold</option>
                                <option value="Yellow">Yellow</option>
                                <option value="LightYellow">LightYellow</option>
                                <option value="LemonChiffon">LemonChiffon</option>
                                <option value="Khaki">Khaki</option>
                                <option value="DarkKhaki">DarkKhaki</option>
                                <option value="Green">Green</option>
                                <option value="Lime">Lime</option>
                                <option value="ForestGreen">ForestGreen</option>
                                <option value="DarkGreen">DarkGreen</option>
                                <option value="SeaGreen">SeaGreen</option>
                                <option value="MediumSeaGreen">MediumSeaGreen</option>
                                <option value="LightGreen">LightGreen</option>
                                <option value="PaleGreen">PaleGreen</option>
                                <option value="SpringGreen">SpringGreen</option>
                                <option value="MediumSpringGreen">MediumSpringGreen</option>
                                <option value="YellowGreen">YellowGreen</option>
                                <option value="Olive">Olive</option>
                                <option value="DarkOliveGreen">DarkOliveGreen</option>
                                <option value="Blue">Blue</option>
                                <option value="MediumBlue">MediumBlue</option>
                                <option value="DarkBlue">DarkBlue</option>
                                <option value="Navy">Navy</option>
                                <option value="SkyBlue">SkyBlue</option>
                                <option value="LightSkyBlue">LightSkyBlue</option>
                                <option value="DeepSkyBlue">DeepSkyBlue</option>
                                <option value="DodgerBlue">DodgerBlue</option>
                                <option value="SteelBlue">SteelBlue</option>
                                <option value="CornflowerBlue">CornflowerBlue</option>
                                <option value="RoyalBlue">RoyalBlue</option>
                                <option value="LightBlue">LightBlue</option>
                                <option value="PowderBlue">PowderBlue</option>
                                <option value="Purple">Purple</option>
                                <option value="MediumPurple">MediumPurple</option>
                                <option value="BlueViolet">BlueViolet</option>
                                <option value="Violet">Violet</option>
                                <option value="Orchid">Orchid</option>
                                <option value="Magenta">Magenta</option>
                                <option value="Fuchsia">Fuchsia</option>
                                <option value="DeepPink">DeepPink</option>
                                <option value="HotPink">HotPink</option>
                                <option value="LightPink">LightPink</option>
                                <option value="PaleVioletRed">PaleVioletRed</option>
                                <option value="Brown">Brown</option>
                                <option value="SaddleBrown">SaddleBrown</option>
                                <option value="Sienna">Sienna</option>
                                <option value="Chocolate">Chocolate</option>
                                <option value="Peru">Peru</option>
                                <option value="Tan">Tan</option>
                                <option value="RosyBrown">RosyBrown</option>
                                <option value="SandyBrown">SandyBrown</option>
                                <option value="BurlyWood">BurlyWood</option>
                                <option value="Wheat">Wheat</option>
                                <option value="NavajoWhite">NavajoWhite</option>
                                <option value="Black">Black</option>
                                <option value="DimGray">DimGray</option>
                                <option value="Gray">Gray</option>
                                <option value="DarkGray">DarkGray</option>
                                <option value="Silver">Silver</option>
                                <option value="LightGray">LightGray</option>
                                <option value="Gainsboro">Gainsboro</option>
                                <option value="WhiteSmoke">WhiteSmoke</option>
                                <option value="White">White</option>
                            </select>
                        </div>
                        <div class="col-span-3">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Color Code</label>
                            <div class="relative">
                                <input type="text" name="colors[${newIndex}][color_code]" placeholder="#FF0000" class="color-code-input focus:ring-amber-500 focus:border-amber-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md pr-12" data-coloris>
                                <div class="color-preview absolute right-2 top-1/2 transform -translate-y-1/2 w-8 h-8 rounded border border-gray-300 dark:border-gray-600 cursor-pointer" style="background-color: #ffffff;"></div>
                            </div>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Price Adjustment</label>
                            <input type="number" step="0.01" name="colors[${newIndex}][price_adjustment]" placeholder="0.00" value="0" class="focus:ring-amber-500 focus:border-amber-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Total Stock</label>
                            <input type="number" name="colors[${newIndex}][stock]" placeholder="10" value="0" min="0" class="color-stock-input focus:ring-amber-500 focus:border-amber-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md transition-colors duration-200">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Total stock to allocate across sizes</p>
                        </div>
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Display Order</label>
                            <input type="number" name="colors[${newIndex}][display_order]" placeholder="0" value="${newIndex}" class="focus:ring-amber-500 focus:border-amber-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                        </div>
                        <div class="col-span-1 flex items-end justify-center">
                            <button type="button" class="remove-item text-red-500 hover:text-red-700">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex flex-col">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Color Image <span class="text-red-500">*</span></label>

                            <!-- Image Preview Container -->
                            <div class="image-preview-container mb-3" style="width: 300px; height: 400px; border: 2px dashed #d1d5db; border-radius: 8px; background-color: #f9fafb; display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative;">
                                <img class="image-preview hidden" style="width: 100%; height: 100%; object-fit: cover; border-radius: 6px;" alt="Image Preview">
                                <div class="image-placeholder text-center">
                                    <i class="fas fa-image text-gray-400 text-4xl mb-2"></i>
                                    <p class="text-gray-500 text-sm">No image selected</p>
                                    <p class="text-gray-400 text-xs">300x400px preview</p>
                                </div>
                            </div>

                            <input type="file" name="color_images[${newIndex}]" class="color-image-input block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-gray-700 dark:file:text-gray-200 mb-2" required accept="image/*" style="max-width: 300px;">
                            <input type="hidden" name="colors[${newIndex}][image]" value="">

                            <!-- Error message container for image validation -->
                            <div class="image-error-message hidden mt-2 p-3 bg-red-50 border border-red-200 rounded-md" style="max-width: 300px;">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-red-700 error-text"></p>
                                    </div>
                                </div>
                            </div>

                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 mb-4" style="max-width: 300px;">
                                PNG, JPG, GIF up to 20MB
                            </p>

                            <!-- Default Color Checkbox - positioned below image -->
                            <div class="mt-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Default Color</label>
                                <div class="flex items-start">
                                    <input type="checkbox" name="colors[${newIndex}][is_default]" value="1" class="default-color-checkbox focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded mt-1">
                                    <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">This image will be the main product image</span>
                                </div>
                            </div>
                        </div>
                    </div>


                `;

                colorsContainer.appendChild(newItem);
                setupRemoveButtons();
                setupImagePreview(); // Re-setup image preview for new items
                setupDefaultColorSelection(); // Re-setup default color selection for new items
                setupColorStockListeners(); // Re-setup color stock listeners for new items

                // Enhance the new color dropdown with visual styling
                if (window.enhancedColorSelection) {
                    window.enhancedColorSelection.handleDynamicColorItem(newItem);
                }
            });
        }

        // Add specification
        const addSpecificationBtn = document.getElementById('add-specification');
        const specificationsContainer = document.getElementById('specifications-container');

        if (addSpecificationBtn && specificationsContainer) {
            addSpecificationBtn.addEventListener('click', function() {
                const items = specificationsContainer.querySelectorAll('.specification-item');
                const newIndex = items.length;

                const newItem = document.createElement('div');
                newItem.className = 'specification-item grid grid-cols-12 gap-4 items-center';
                newItem.innerHTML = `
                    <div class="col-span-5">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Key</label>
                        <input type="text" name="specifications[${newIndex}][key]" placeholder="Material" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                    </div>
                    <div class="col-span-5">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Value</label>
                        <input type="text" name="specifications[${newIndex}][value]" placeholder="Cotton" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Order</label>
                        <input type="number" name="specifications[${newIndex}][display_order]" placeholder="0" value="${newIndex}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                    </div>
                    <div class="col-span-1 flex items-end justify-center">
                        <button type="button" class="remove-item text-red-500 hover:text-red-700">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `;

                specificationsContainer.appendChild(newItem);
                setupRemoveButtons();
            });
        }



        // Remove item functionality
        function setupRemoveButtons() {
            document.querySelectorAll('.remove-item').forEach(button => {
                button.addEventListener('click', function() {
                    const item = this.closest('.color-item, .specification-item');
                    if (item) {
                        const isColorItem = item.classList.contains('color-item');
                        item.remove();

                        // Update stock summary if a color item was removed
                        if (isColorItem) {
                            updateStockSummary();
                        }
                    }
                });
            });
        }

        // Form submission validation
        const productForm = document.querySelector('form');
        if (productForm) {
            productForm.addEventListener('submit', function(e) {
                const generalStock = parseInt(document.getElementById('stock').value) || 0;
                const totalAllocated = calculateTotalAllocatedStock();

                if (totalAllocated > generalStock) {
                    e.preventDefault();

                    // Show error message
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow-lg z-50';
                    errorDiv.innerHTML = `
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <div>
                                <strong>Stock Validation Error</strong><br>
                                Total allocated stock (${totalAllocated}) exceeds general stock (${generalStock}).
                                Please adjust color stock quantities before saving.
                            </div>
                            <button type="button" class="ml-4 text-red-700 hover:text-red-900" onclick="this.parentElement.parentElement.remove()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                    document.body.appendChild(errorDiv);

                    // Auto-remove error after 8 seconds
                    setTimeout(() => {
                        if (errorDiv.parentNode) {
                            errorDiv.remove();
                        }
                    }, 8000);

                    // Scroll to stock summary
                    const stockSummary = document.getElementById('stock-summary');
                    if (stockSummary) {
                        stockSummary.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }

                    return false;
                }
            });
        }

        // Initialize all functionality
        setupRemoveButtons();
    });
</script>
<script src="{{ asset('js/enhanced-size-selection.js') }}"></script>
<script src="{{ asset('js/enhanced-color-selection.js') }}"></script>
<script src="{{ asset('js/color-specific-size-selection.js') }}"></script>
<script src="{{ asset('js/dynamic-color-size-management.js') }}"></script>
@endsection
