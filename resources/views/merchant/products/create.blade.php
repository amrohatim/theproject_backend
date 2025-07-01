@extends('layouts.merchant')

@section('title', 'Add New Product')
@section('header', 'Add New Product')

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
                    Create a new product for your merchant store
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

<!-- Product Form -->
<div class="discord-card">
    <div class="discord-card-header">
        <i class="fas fa-edit me-2" style="color: var(--discord-primary);"></i>
        Product Information
    </div>
    <div class="discord-card-body">
        <form action="{{ route('merchant.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                <!-- Product Image -->
                <div class="col-md-4 mb-4">
                    <x-image-upload
                        name="image"
                        label="Product Image"
                        :error="$errors->first('image')"
                        container-class="mb-0" />
                </div>

                <!-- Product Details -->
                <div class="col-md-8">
                    <div class="row">
                        <!-- Product Name -->
                        <div class="col-md-12 mb-3">
                            <label for="name" class="form-label" style="color: var(--discord-lightest); font-weight: 600;">
                                Product Name *
                            </label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   required
                                   style="background-color: var(--discord-darkest); border: 1px solid var(--discord-darkest); color: var(--discord-lightest);">
                            @error('name')
                                <div style="color: var(--discord-red); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Category and Price -->
                        <div class="col-md-6 mb-3">
                            <label for="category_id" class="form-label" style="color: var(--discord-lightest); font-weight: 600;">
                                Category *
                            </label>
                            <select class="form-select @error('category_id') is-invalid @enderror" 
                                    id="category_id" 
                                    name="category_id" 
                                    required
                                    style="background-color: var(--discord-darkest); border: 1px solid var(--discord-darkest); color: var(--discord-lightest);">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div style="color: var(--discord-red); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label" style="color: var(--discord-lightest); font-weight: 600;">
                                Price ($) *
                            </label>
                            <input type="number" 
                                   class="form-control @error('price') is-invalid @enderror" 
                                   id="price" 
                                   name="price" 
                                   value="{{ old('price') }}" 
                                   step="0.01" 
                                   min="0" 
                                   required
                                   style="background-color: var(--discord-darkest); border: 1px solid var(--discord-darkest); color: var(--discord-lightest);">
                            @error('price')
                                <div style="color: var(--discord-red); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- SKU and Stock -->
                        <div class="col-md-6 mb-3">
                            <label for="sku" class="form-label" style="color: var(--discord-lightest); font-weight: 600;">
                                SKU (Optional)
                            </label>
                            <input type="text" 
                                   class="form-control @error('sku') is-invalid @enderror" 
                                   id="sku" 
                                   name="sku" 
                                   value="{{ old('sku') }}"
                                   style="background-color: var(--discord-darkest); border: 1px solid var(--discord-darkest); color: var(--discord-lightest);">
                            @error('sku')
                                <div style="color: var(--discord-red); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="stock_quantity" class="form-label" style="color: var(--discord-lightest); font-weight: 600;">
                                Stock Quantity (Optional)
                            </label>
                            <input type="number" 
                                   class="form-control @error('stock_quantity') is-invalid @enderror" 
                                   id="stock_quantity" 
                                   name="stock_quantity" 
                                   value="{{ old('stock_quantity') }}" 
                                   min="0"
                                   style="background-color: var(--discord-darkest); border: 1px solid var(--discord-darkest); color: var(--discord-lightest);">
                            @error('stock_quantity')
                                <div style="color: var(--discord-red); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="col-md-12 mb-3">
                            <label for="status" class="form-label" style="color: var(--discord-lightest); font-weight: 600;">
                                Status
                            </label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" 
                                    name="status"
                                    style="background-color: var(--discord-darkest); border: 1px solid var(--discord-darkest); color: var(--discord-lightest);">
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <div style="color: var(--discord-red); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="mb-4">
                <label for="description" class="form-label" style="color: var(--discord-lightest); font-weight: 600;">
                    Product Description *
                </label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" 
                          name="description" 
                          rows="4" 
                          required
                          style="background-color: var(--discord-darkest); border: 1px solid var(--discord-darkest); color: var(--discord-lightest);">{{ old('description') }}</textarea>
                @error('description')
                    <div style="color: var(--discord-red); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('merchant.products.index') }}" class="discord-btn discord-btn-secondary">
                    <i class="fas fa-times me-1"></i> Cancel
                </a>
                <button type="submit" class="discord-btn">
                    <i class="fas fa-save me-1"></i> Create Product
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate SKU based on product name (optional)
    const nameInput = document.getElementById('name');
    const skuInput = document.getElementById('sku');

    if (nameInput && skuInput) {
        nameInput.addEventListener('blur', function() {
            const name = this.value;
            const sku = skuInput.value;

            if (name && !sku) {
                const generatedSku = name.toUpperCase()
                    .replace(/[^A-Z0-9]/g, '')
                    .substring(0, 8) +
                    Math.random().toString(36).substring(2, 5).toUpperCase();
                skuInput.value = generatedSku;
            }
        });
    }
});
</script>
@endsection
