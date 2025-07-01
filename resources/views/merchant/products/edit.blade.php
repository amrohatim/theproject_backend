@extends('layouts.merchant')

@section('title', 'Edit Product')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 style="color: var(--discord-lightest); font-size: 28px; font-weight: 700; margin: 0;">
                <i class="fas fa-edit me-3" style="color: var(--discord-primary);"></i>
                Edit Product
            </h1>
            <p style="color: var(--discord-light); margin: 8px 0 0 0; font-size: 14px;">
                Update your product information and settings
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('merchant.products.index') }}" class="discord-btn-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back to Products
            </a>
        </div>
    </div>

    <!-- Edit Product Form -->
    <div class="discord-card">
        <div class="discord-card-header">
            <i class="fas fa-box me-2" style="color: var(--discord-primary);"></i>
            Product Information
        </div>
        
        <form action="{{ route('merchant.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row">
                <!-- Product Image -->
                <div class="col-md-4 mb-4">
                    <x-image-upload
                        name="image"
                        label="Product Image"
                        :current-image="$product->image"
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
                                   value="{{ old('name', $product->name) }}" 
                                   required
                                   style="background-color: var(--discord-darkest); border: 1px solid var(--discord-darkest); color: var(--discord-lightest);">
                            @error('name')
                                <div style="color: var(--discord-red); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Category -->
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
                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
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
                                   value="{{ old('price', $product->price) }}" 
                                   step="0.01" 
                                   min="0" 
                                   required
                                   style="background-color: var(--discord-darkest); border: 1px solid var(--discord-darkest); color: var(--discord-lightest);">
                            @error('price')
                                <div style="color: var(--discord-red); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Original Price -->
                        <div class="col-md-6 mb-3">
                            <label for="original_price" class="form-label" style="color: var(--discord-lightest); font-weight: 600;">
                                Original Price ($)
                            </label>
                            <input type="number" 
                                   class="form-control @error('original_price') is-invalid @enderror" 
                                   id="original_price" 
                                   name="original_price" 
                                   value="{{ old('original_price', $product->original_price) }}" 
                                   step="0.01" 
                                   min="0"
                                   style="background-color: var(--discord-darkest); border: 1px solid var(--discord-darkest); color: var(--discord-lightest);">
                            @error('original_price')
                                <div style="color: var(--discord-red); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Stock -->
                        <div class="col-md-6 mb-3">
                            <label for="stock" class="form-label" style="color: var(--discord-lightest); font-weight: 600;">
                                Stock Quantity
                            </label>
                            <input type="number" 
                                   class="form-control @error('stock') is-invalid @enderror" 
                                   id="stock" 
                                   name="stock" 
                                   value="{{ old('stock', $product->stock) }}" 
                                   min="0"
                                   style="background-color: var(--discord-darkest); border: 1px solid var(--discord-darkest); color: var(--discord-lightest);">
                            @error('stock')
                                <div style="color: var(--discord-red); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- SKU -->
                        <div class="col-md-6 mb-3">
                            <label for="sku" class="form-label" style="color: var(--discord-lightest); font-weight: 600;">
                                SKU
                            </label>
                            <input type="text" 
                                   class="form-control @error('sku') is-invalid @enderror" 
                                   id="sku" 
                                   name="sku" 
                                   value="{{ old('sku', $product->sku) }}"
                                   style="background-color: var(--discord-darkest); border: 1px solid var(--discord-darkest); color: var(--discord-lightest);">
                            @error('sku')
                                <div style="color: var(--discord-red); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="col-md-6 mb-3">
                            <label for="is_available" class="form-label" style="color: var(--discord-lightest); font-weight: 600;">
                                Status
                            </label>
                            <select class="form-select @error('is_available') is-invalid @enderror" 
                                    id="is_available" 
                                    name="is_available"
                                    style="background-color: var(--discord-darkest); border: 1px solid var(--discord-darkest); color: var(--discord-lightest);">
                                <option value="1" {{ old('is_available', $product->is_available) == 1 ? 'selected' : '' }}>Available</option>
                                <option value="0" {{ old('is_available', $product->is_available) == 0 ? 'selected' : '' }}>Unavailable</option>
                            </select>
                            @error('is_available')
                                <div style="color: var(--discord-red); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="row">
                <div class="col-12 mb-4">
                    <label for="description" class="form-label" style="color: var(--discord-lightest); font-weight: 600;">
                        Description *
                    </label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" 
                              name="description" 
                              rows="4" 
                              required
                              style="background-color: var(--discord-darkest); border: 1px solid var(--discord-darkest); color: var(--discord-lightest);">{{ old('description', $product->description) }}</textarea>
                    @error('description')
                        <div style="color: var(--discord-red); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="d-flex justify-content-end gap-3 mt-4">
                <a href="{{ route('merchant.products.index') }}" class="discord-btn-secondary">
                    <i class="fas fa-times me-2"></i> Cancel
                </a>
                <button type="submit" class="discord-btn">
                    <i class="fas fa-save me-2"></i> Update Product
                </button>
            </div>
        </form>
    </div>
</div>
@endsection


