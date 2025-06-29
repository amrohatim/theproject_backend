@extends('layouts.provider')

@section('title', 'Add Product')

@section('header', 'Create New Product')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center">
        <div style="width: 40px; height: 40px; background-color: var(--discord-primary); border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
            <i class="fas fa-plus text-white"></i>
        </div>
        <div>
            <h4 class="mb-0">New Product</h4>
            <p class="text-muted mb-0" style="font-size: 14px; color: var(--discord-light);">Add a new product to your inventory</p>
        </div>
    </div>
    <a href="{{ route('provider.provider-products.index') }}" class="discord-btn discord-btn-secondary">
        <i class="fas fa-arrow-left me-2"></i> Back to Inventory
    </a>
</div>

<div class="discord-card mb-4">
    <div class="discord-card-header">
        <i class="fas fa-box me-2" style="color: var(--discord-primary);"></i>
        Product Information
    </div>
    <div class="p-4">
            <form action="{{ route('provider.provider-products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row">
                    <!-- Left Column - Basic Information -->
                    <div class="col-lg-8">
                        <div class="mb-4" style="border-radius: 8px; padding: 16px; border: 1px solid #e0e1e5;">
                            <h5 class="mb-3" style="font-weight: 600; font-size: 16px; color: var(--discord-lightest);">Basic Information</h5>
                            
                            <div class="mb-3">
                                <label for="product_name" style="display: block; margin-bottom: 8px; color: var(--discord-lightest); font-weight: 500;">
                                    Product Name <span style="color: var(--discord-red);">*</span>
                                </label>
                                <input type="text" class="form-control" id="product_name" name="product_name" value="{{ old('product_name') }}" required 
                                    style="background-color: var(--discord-dark); border: none; color: var(--discord-lightest); padding: 10px 12px; border-radius: 4px; width: 100%;">
                                @error('product_name')
                                    <div style="color: var(--discord-red); font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" style="display: block; margin-bottom: 8px; color: var(--discord-lightest); font-weight: 500;">
                                    Description
                                </label>
                                <textarea class="form-control" id="description" name="description" rows="5" style="background-color: var(--discord-dark); border: none; color: var(--discord-lightest); padding: 10px 12px; border-radius: 4px; width: 100%; resize: vertical;">{{ old('description') }}</textarea>
                                @error('description')
                                    <div style="color: var(--discord-red); font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Pricing Information -->
                        <div class="mb-4" style="border-radius: 8px; padding: 16px; border: 1px solid #e0e1e5;">
                            <h5 class="mb-3" style="font-weight: 600; font-size: 16px; color: var(--discord-lightest);">Pricing</h5>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="price" style="display: block; margin-bottom: 8px; color: var(--discord-lightest); font-weight: 500;">
                                        Price ($) <span style="color: var(--discord-red);">*</span>
                                    </label>
                                    <div style="position: relative;">
                                        <div style="position: absolute; left: 10px; top: 10px; color: var(--discord-light);">$</div>
                                        <input type="number" step="0.01" min="0" class="form-control" id="price" name="price" value="{{ old('price') }}" required 
                                            style="background-color: var(--discord-dark); border: none; color: var(--discord-lightest); padding: 10px 12px 10px 24px; border-radius: 4px; width: 100%;">
                                    </div>
                                    @error('price')
                                        <div style="color: var(--discord-red); font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="original_price" style="display: block; margin-bottom: 8px; color: var(--discord-lightest); font-weight: 500;">
                                        Original Price ($)
                                    </label>
                                    <div style="position: relative;">
                                        <div style="position: absolute; left: 10px; top: 10px; color: var(--discord-light);">$</div>
                                        <input type="number" step="0.01" min="0" class="form-control" id="original_price" name="original_price" value="{{ old('original_price') }}" 
                                            style="background-color: var(--discord-dark); border: none; color: var(--discord-lightest); padding: 10px 12px 10px 24px; border-radius: 4px; width: 100%;">
                                    </div>
                                    <small style="color: var(--discord-light); font-size: 12px;">Original price (if on sale)</small>
                                    @error('original_price')
                                        <div style="color: var(--discord-red); font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Inventory Information -->
                        <div class="mb-4" style="border-radius: 8px; padding: 16px; border: 1px solid #e0e1e5;">
                            <h5 class="mb-3" style="font-weight: 600; font-size: 16px; color: var(--discord-lightest);">Inventory</h5>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="sku" style="display: block; margin-bottom: 8px; color: var(--discord-lightest); font-weight: 500;">
                                        SKU (Stock Keeping Unit)
                                    </label>
                                    <input type="text" class="form-control" id="sku" name="sku" value="{{ old('sku') }}" 
                                        style="background-color: var(--discord-dark); border: none; color: var(--discord-lightest); padding: 10px 12px; border-radius: 4px; width: 100%;">
                                    <small style="color: var(--discord-light); font-size: 12px;">Will be auto-generated if left blank</small>
                                    @error('sku')
                                        <div style="color: var(--discord-red); font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="stock" style="display: block; margin-bottom: 8px; color: var(--discord-lightest); font-weight: 500;">
                                        Stock Quantity <span style="color: var(--discord-red);">*</span>
                                    </label>
                                    <input type="number" min="0" class="form-control" id="stock" name="stock" value="{{ old('stock', 1) }}" required 
                                        style="background-color: var(--discord-dark); border: none; color: var(--discord-lightest); padding: 10px 12px; border-radius: 4px; width: 100%;">
                                    @error('stock')
                                        <div style="color: var(--discord-red); font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column - Image and Categories -->
                    <div class="col-lg-4">
                        <!-- Product Image -->
                        <div class="mb-4" style="border-radius: 8px; padding: 16px; border: 1px solid #e0e1e5;">
                            <h5 class="mb-3" style="font-weight: 600; font-size: 16px; color: var(--discord-lightest);">Product Image</h5>
                            
                            <div class="text-center mb-3">
                                <img id="image-preview" src="#" alt="Preview" style="max-width: 100%; max-height: 200px; display: none; border-radius: 8px; margin-bottom: 15px;">
                                <div id="image-placeholder" style="background-color: var(--discord-dark); border-radius: 8px; padding: 30px; margin-bottom: 15px;">
                                    <i class="fas fa-image fa-3x" style="color: var(--discord-light); margin-bottom: 10px;"></i>
                                    <p style="color: var(--discord-light); margin: 0;">No image selected</p>
                                </div>
                                
                                <div class="input-group">
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*" style="display: none;">
                                    <label for="image" class="discord-btn" style="width: 100%; cursor: pointer;">
                                        <i class="fas fa-upload me-2"></i> Select Image
                                    </label>
                                </div>
                                
                                @error('image')
                                    <div style="color: var(--discord-red); font-size: 14px; margin-top: 10px;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Category -->
                        <div class="mb-4" style="border-radius: 8px; padding: 16px; border: 1px solid #e0e1e5;">
                            <h5 class="mb-3" style="font-weight: 600; font-size: 16px; color: var(--discord-lightest);">Category</h5>
                            
                            <div class="mb-3">
                                <label for="category_id" style="display: block; margin-bottom: 8px; color: var(--discord-lightest); font-weight: 500;">
                                    Select Category
                                </label>
                                <select class="form-select" id="category_id" name="category_id" 
                                    style="background-color: var(--discord-dark); border: none; color: var(--discord-lightest); padding: 10px 12px; border-radius: 4px; width: 100%;">
                                    <option value="" style="background-color: var(--discord-dark);">Select Category</option>
                                    @foreach($parentCategories as $parentCategory)
                                        <optgroup label="{{ $parentCategory->name }}" style="background-color: var(--discord-dark); color: var(--discord-primary);">
                                            <option value="{{ $parentCategory->id }}" {{ old('category_id') == $parentCategory->id ? 'selected' : '' }} style="background-color: var(--discord-dark);">
                                                {{ $parentCategory->name }}
                                            </option>
                                            @foreach($parentCategory->children as $childCategory)
                                                <option value="{{ $childCategory->id }}" {{ old('category_id') == $childCategory->id ? 'selected' : '' }} style="background-color: var(--discord-dark); padding-left: 20px;">
                                                    — {{ $childCategory->name }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div style="color: var(--discord-red); font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Hidden field to always set product as active -->
                            <input type="hidden" name="is_active" value="1">
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end mt-4 gap-2">
                    <button type="reset" class="discord-btn discord-btn-secondary me-2">
                        <i class="fas fa-undo me-2"></i> Reset
                    </button>
                    <button type="submit" class="discord-btn">
                        <i class="fas fa-save me-2"></i> Add to Inventory
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Simple image preview functionality
    $('#image').on('change', function() {
        var file = this.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#image-preview').attr('src', e.target.result).show();
                $('#image-placeholder').hide();
            }
            reader.readAsDataURL(file);
        }
    });
    
    // Generate SKU if empty when product name changes
    $('#product_name').on('blur', function() {
        if (!$('#sku').val()) {
            // Create a basic SKU from product name (first letters of each word + timestamp)
            var productName = $(this).val();
            if (productName) {
                var acronym = productName.split(/\s+/).map(function(word) {
                    return word[0] || '';
                }).join('').toUpperCase();
                var timestamp = new Date().getTime().toString().substr(-6);
                $('#sku').val(acronym + '-' + timestamp);
            }
        }
    });
});
</script>
@endsection
