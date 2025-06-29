@extends('layouts.dashboard')

@section('title', 'Create Deal')
@section('page-title', 'Create Deal')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .form-section {
        margin-bottom: 2rem;
    }
    .form-section-title {
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #e2e8f0;
    }
    .selection-container {
        max-height: 300px;
        overflow-y: auto;
        border: 1px solid #e2e8f0;
        border-radius: 0.375rem;
        padding: 0.5rem;
    }
</style>
@endsection

@section('content')
<div class="container mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <form action="{{ route('vendor.deals.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Basic Information -->
            <div class="form-section">
                <h3 class="form-section-title text-xl font-bold text-gray-800 dark:text-white">Deal Information</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" class="form-input w-full" required>
                        @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Discount Percentage -->
                    <div>
                        <label for="discount_percentage" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Discount Percentage <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="number" name="discount_percentage" id="discount_percentage" value="{{ old('discount_percentage') }}" min="1" max="100" class="form-input w-full" required>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <span class="text-gray-500">%</span>
                            </div>
                        </div>
                        @error('discount_percentage')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Description -->
                <div class="mt-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                    <textarea name="description" id="description" rows="3" class="form-textarea w-full">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Promotional Message -->
                <div class="mt-4">
                    <label for="promotional_message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Promotional Message
                        <span class="ml-1 text-gray-500" title="A short, eye-catching message to display on the deal. This will appear as a button in the mobile app.">
                            <i class="fas fa-info-circle"></i>
                        </span>
                    </label>
                    <div class="relative">
                        <input type="text" name="promotional_message" id="promotional_message" value="{{ old('promotional_message') }}"
                               class="form-input w-full" maxlength="50" placeholder="e.g., 'Limited Time Offer!' or 'Buy Now!'">
                        <div class="absolute right-2 bottom-2 text-xs text-gray-500">
                            <span id="char-count">0</span>/50
                        </div>
                    </div>
                    @error('promotional_message')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date Range -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <!-- Start Date -->
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date <span class="text-red-500">*</span></label>
                        <input type="text" name="start_date" id="start_date" value="{{ old('start_date', date('Y-m-d')) }}" class="form-input w-full datepicker" required>
                        @error('start_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- End Date -->
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date <span class="text-red-500">*</span></label>
                        <input type="text" name="end_date" id="end_date" value="{{ old('end_date', date('Y-m-d', strtotime('+30 days'))) }}" class="form-input w-full datepicker" required>
                        @error('end_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Image -->
                <div class="mt-4">
                    <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deal Image</label>
                    <input type="file" name="image" id="image" class="form-input w-full">
                    <p class="text-gray-500 text-sm mt-1">Recommended size: 1200x600px. Max size: 2MB.</p>
                    @error('image')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status <span class="text-red-500">*</span></label>
                    <div class="flex space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="status" value="active" class="form-radio" {{ old('status', 'active') == 'active' ? 'checked' : '' }}>
                            <span class="ml-2">Active</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="status" value="inactive" class="form-radio" {{ old('status') == 'inactive' ? 'checked' : '' }}>
                            <span class="ml-2">Inactive</span>
                        </label>
                    </div>
                    @error('status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Application Scope -->
            <div class="form-section">
                <h3 class="form-section-title text-xl font-bold text-gray-800 dark:text-white">Deal Application</h3>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deal Type <span class="text-red-500">*</span></label>
                    <div class="space-y-2">
                        <label class="inline-flex items-center">
                            <input type="radio" name="applies_to" value="products" class="form-radio" {{ old('applies_to', 'products') == 'products' ? 'checked' : '' }} onchange="toggleSelectionContainers()">
                            <span class="ml-2">Product Deal - Apply to selected products only</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="applies_to" value="services" class="form-radio" {{ old('applies_to') == 'services' ? 'checked' : '' }} onchange="toggleSelectionContainers()">
                            <span class="ml-2">Service Deal - Apply to selected services only</span>
                        </label>
                    </div>
                    @error('applies_to')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Product Selection -->
                <div id="products-container" class="mt-4" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Select Products</label>
                    <div class="selection-container">
                        @foreach($products as $product)
                            <div class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded">
                                <label class="inline-flex items-center w-full">
                                    <input type="checkbox" name="product_ids[]" value="{{ $product->id }}" class="form-checkbox" {{ in_array($product->id, old('product_ids', [])) ? 'checked' : '' }}>
                                    <span class="ml-2">{{ $product->name }} - ${{ number_format($product->price, 2) }}</span>
                                    <span class="ml-auto text-sm text-gray-500">{{ $product->branch->name }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                    @error('product_ids')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Service Selection -->
                <div id="services-container" class="mt-4" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Select Services</label>
                    <div class="selection-container">
                        @foreach($services as $service)
                            <div class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded">
                                <label class="inline-flex items-center w-full">
                                    <input type="checkbox" name="service_ids[]" value="{{ $service->id }}" class="form-checkbox" {{ in_array($service->id, old('service_ids', [])) ? 'checked' : '' }}>
                                    <span class="ml-2">{{ $service->name }} - ${{ number_format($service->price, 2) }} ({{ $service->duration }}min)</span>
                                    <span class="ml-auto text-sm text-gray-500">{{ $service->branch->name }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                    @error('service_ids')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>


            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-4 mt-6">
                <a href="{{ route('vendor.deals.index') }}" class="btn-outline">Cancel</a>
                <button type="submit" class="btn-primary">Create Deal</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize date pickers
        flatpickr(".datepicker", {
            dateFormat: "Y-m-d",
            minDate: "today"
        });

        // Initialize selection containers
        toggleSelectionContainers();

        // Initialize character counter for promotional message
        const promotionalMessage = document.getElementById('promotional_message');
        const charCount = document.getElementById('char-count');

        if (promotionalMessage && charCount) {
            // Update initial count
            charCount.textContent = promotionalMessage.value.length;

            // Update count on input
            promotionalMessage.addEventListener('input', function() {
                charCount.textContent = this.value.length;

                // Change color when approaching limit
                if (this.value.length > 40) {
                    charCount.classList.add('text-orange-500');
                } else {
                    charCount.classList.remove('text-orange-500');
                }

                // Change color when at limit
                if (this.value.length >= 50) {
                    charCount.classList.add('text-red-500');
                } else {
                    charCount.classList.remove('text-red-500');
                }
            });
        }
    });

    function toggleSelectionContainers() {
        const appliesTo = document.querySelector('input[name="applies_to"]:checked').value;
        const productsContainer = document.getElementById('products-container');
        const servicesContainer = document.getElementById('services-container');

        // Hide all containers first
        productsContainer.style.display = 'none';
        servicesContainer.style.display = 'none';

        // Show relevant containers based on selection
        if (appliesTo === 'products') {
            productsContainer.style.display = 'block';
        } else if (appliesTo === 'services') {
            servicesContainer.style.display = 'block';
        }
    }
</script>
@endsection
