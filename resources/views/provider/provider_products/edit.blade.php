@extends('layouts.provider')

@section('title', __('provider.edit_product'))

@section('header', __('provider.edit_product_title'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center">
        <div style="width: 40px; height: 40px; background-color: var(--discord-primary); border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
            <i class="fas fa-edit text-white"></i>
        </div>
        <div>
            <h4 class="mb-0">{{ __('provider.edit_product') }}</h4>
            <p class="text-muted mb-0" style="font-size: 14px; color: var(--discord-light);">Edit product information</p>
        </div>
    </div>
    <a href="{{ route('provider.provider-products.index') }}" class="discord-btn discord-btn-secondary">
        <i class="fas fa-arrow-left me-2"></i> Back to Inventory
    </a>
</div>

<div class="discord-card mb-4">
    <div class="discord-card-header">
            <i class="fas fa-box me-2" style="color: var(--discord-primary);"></i>
            {{ __('provider.product_information') }}
        </div>
        <div class="p-4">
                <form action="{{ route('provider.provider-products.update', $providerProduct->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <!-- Left Column - Basic Information -->
                        <div class="col-lg-8">
                            <div class="mb-4" style="border-radius: 8px; padding: 16px; border: 1px solid #e0e1e5;">
                                <h5 class="mb-3" style="font-weight: 600; font-size: 16px; color: var(--discord-lightest);">{{ __('provider.basic_information') }}</h5>
                            
                            <div class="mb-3">
                                <label style="display: block; margin-bottom: 8px; color: var(--discord-lightest); font-weight: 500;">
                                    {{ __('provider.product_name') }} <span style="color: var(--discord-red);">*</span>
                                </label>
                                
                                <!-- Language Switch for Product Name -->
                                <x-form-language-switch field-name="product_name" />
                                
                                <!-- English Product Name -->
                                <div data-lang-field="product_name" data-lang="en" style="display: block;">
                                    <input type="text" class="form-control" id="product_name" name="product_name" value="{{ old('product_name', $providerProduct->product_name) }}" required 
                                        placeholder="{{ __('provider.enter_product_name_english') }}"
                                        style="background-color: var(--discord-dark); border: none; color: var(--discord-lightest); padding: 10px 12px; border-radius: 4px; width: 100%;">
                                    @error('product_name')
                                        <div style="color: var(--discord-red); font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Arabic Product Name -->
                                <div data-lang-field="product_name" data-lang="ar" style="display: none;" dir="rtl">
                                    <input type="text" class="form-control" id="product_name_arabic" name="product_name_arabic" value="{{ old('product_name_arabic', $providerProduct->product_name_arabic) }}" required 
                                        placeholder="{{ __('provider.enter_product_name_arabic') }}"
                                        style="background-color: var(--discord-dark); border: none; color: var(--discord-lightest); padding: 10px 12px; border-radius: 4px; width: 100%;">
                                    @error('product_name_arabic')
                                        <div style="color: var(--discord-red); font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label style="display: block; margin-bottom: 8px; color: var(--discord-lightest); font-weight: 500;">
                                    {{ __('provider.description') }}
                                </label>
                                
                                <!-- Language Switch for Product Description -->
                                <x-form-language-switch field-name="product_description" />
                                
                                <!-- English Product Description -->
                                <div data-lang-field="product_description" data-lang="en" style="display: block;">
                                    <textarea class="form-control" id="description" name="description" rows="5" 
                                        placeholder="{{ __('provider.enter_product_description_english') }}"
                                        style="background-color: var(--discord-dark); border: none; color: var(--discord-lightest); padding: 10px 12px; border-radius: 4px; width: 100%; resize: vertical;">{{ old('description', $providerProduct->description) }}</textarea>
                                    @error('description')
                                        <div style="color: var(--discord-red); font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Arabic Product Description -->
                                <div data-lang-field="product_description" data-lang="ar" style="display: none;" dir="rtl">
                                    <textarea class="form-control" id="product_description_arabic" name="product_description_arabic" rows="5" 
                                        placeholder="{{ __('provider.enter_product_description_arabic') }}"
                                        style="background-color: var(--discord-dark); border: none; color: var(--discord-lightest); padding: 10px 12px; border-radius: 4px; width: 100%; resize: vertical;">{{ old('product_description_arabic', $providerProduct->product_description_arabic) }}</textarea>
                                    @error('product_description_arabic')
                                        <div style="color: var(--discord-red); font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Pricing -->
                        <div class="mb-4" style="border-radius: 8px; padding: 16px; border: 1px solid #e0e1e5;">
                            <h5 class="mb-3" style="font-weight: 600; font-size: 16px; color: var(--discord-lightest);">{{ __('provider.pricing') }}</h5>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="price" style="display: block; margin-bottom: 8px; color: var(--discord-lightest); font-weight: 500;">
                                            {{ __('provider.price') }} <span style="color: var(--discord-red);">*</span>
                                        </label>
                                        <div style="position: relative;">
                                            <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--discord-light); font-weight: 500;">$</span>
                                            <input type="number" step="0.01" min="0" class="form-control" id="price" name="price" value="{{ old('price', $providerProduct->price) }}" required 
                                                style="background-color: var(--discord-dark); border: none; color: var(--discord-lightest); padding: 10px 12px 10px 30px; border-radius: 4px; width: 100%;">
                                        </div>
                                        @error('price')
                                            <div style="color: var(--discord-red); font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="original_price" style="display: block; margin-bottom: 8px; color: var(--discord-lightest); font-weight: 500;">
                                            {{ __('provider.original_price') }}
                                        </label>
                                        <div style="position: relative;">
                                            <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--discord-light); font-weight: 500;">$</span>
                                            <input type="number" step="0.01" min="0" class="form-control" id="original_price" name="original_price" value="{{ old('original_price', $providerProduct->original_price) }}" 
                                                style="background-color: var(--discord-dark); border: none; color: var(--discord-lightest); padding: 10px 12px 10px 30px; border-radius: 4px; width: 100%;">
                                        </div>
                                        <small style="color: var(--discord-light); font-size: 12px;">{{ __('provider.original_price_description') }}</small>
                                        @error('original_price')
                                            <div style="color: var(--discord-red); font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Inventory -->
                        <div class="mb-4" style="border-radius: 8px; padding: 16px; border: 1px solid #e0e1e5;">
                            <h5 class="mb-3" style="font-weight: 600; font-size: 16px; color: var(--discord-lightest);">{{ __('provider.inventory') }}</h5>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="sku" style="display: block; margin-bottom: 8px; color: var(--discord-lightest); font-weight: 500;">
                                            {{ __('provider.sku') }}
                                        </label>
                                        <input type="text" class="form-control" id="sku" name="sku" value="{{ old('sku', $providerProduct->sku) }}" 
                                            style="background-color: var(--discord-dark); border: none; color: var(--discord-lightest); padding: 10px 12px; border-radius: 4px; width: 100%;">
                                        <small style="color: var(--discord-light); font-size: 12px;">{{ __('provider.sku_help') }}</small>
                                        @error('sku')
                                            <div style="color: var(--discord-red); font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="stock" style="display: block; margin-bottom: 8px; color: var(--discord-lightest); font-weight: 500;">
                                            {{ __('provider.stock_quantity') }} <span style="color: var(--discord-red);">*</span>
                                        </label>
                                        <input type="number" min="0" class="form-control" id="stock" name="stock" value="{{ old('stock', $providerProduct->stock) }}" required 
                                            style="background-color: var(--discord-dark); border: none; color: var(--discord-lightest); padding: 10px 12px; border-radius: 4px; width: 100%;">
                                        @error('stock')
                                            <div style="color: var(--discord-red); font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column - Image and Category -->
                    <div class="col-lg-4">
                        <div class="mb-4" style="border-radius: 8px; padding: 16px; border: 1px solid #e0e1e5;">
                            <h5 class="mb-3" style="font-weight: 600; font-size: 16px; color: var(--discord-lightest);">{{ __('provider.product_image') }}</h5>
                            
                            <div class="text-center mb-3">
                                @if($providerProduct->image)
                                    <img id="image-preview" src="{{ asset($providerProduct->image) }}" alt="Current Image" style="max-width: 100%; max-height: 200px; border-radius: 8px; margin-bottom: 15px;">
                                @else
                                    <img id="image-preview" src="#" alt="Preview" style="max-width: 100%; max-height: 200px; display: none; border-radius: 8px; margin-bottom: 15px;">
                                    <div id="image-placeholder" style="background-color: var(--discord-dark); border-radius: 8px; padding: 30px; margin-bottom: 15px;">
                                        <i class="fas fa-image fa-3x" style="color: var(--discord-light); margin-bottom: 10px;"></i>
                                        <p style="color: var(--discord-light); margin: 0;">{{ __('provider.no_image_selected') }}</p>
                                    </div>
                                @endif
                            </div>
                            
                            <input type="file" id="image" name="image" accept="image/*" style="display: none;">
                            <button type="button" onclick="document.getElementById('image').click()" 
                                style="background-color: var(--discord-primary); color: white; border: none; padding: 10px 20px; border-radius: 4px; width: 100%; font-weight: 500; cursor: pointer;">
                                <i class="fas fa-upload me-2"></i> {{ __('provider.select_image') }}
                            </button>
                            @error('image')
                                <div style="color: var(--discord-red); font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="category_id" style="display: block; margin-bottom: 8px; color: var(--discord-lightest); font-weight: 500;">
                                {{ __('provider.select_category') }}
                            </label>
                            <select class="form-select" id="category_id" name="category_id" required
                                style="background-color: var(--discord-dark); border: none; color: var(--discord-lightest); padding: 10px 12px; border-radius: 4px; width: 100%;">
                                <option value="" style="background-color: var(--discord-dark);">{{ __('provider.select_category') }}</option>
                                @foreach($parentCategories as $parentCategory)
                                    <optgroup label="{{ $parentCategory->name }}" style="background-color: var(--discord-dark); color: var(--discord-primary);">
                                        <option value="{{ $parentCategory->id }}"
                                            {{ old('category_id', $providerProduct->category_id) == $parentCategory->id ? 'selected' : '' }}
                                            style="background-color: var(--discord-dark); color: var(--discord-light); font-style: italic;"
                                            disabled data-is-parent="true">
                                            {{ $parentCategory->name }} ({{ __('provider.select_subcategory') }})
                                        </option>
                                        @foreach($parentCategory->children as $childCategory)
                                            <option value="{{ $childCategory->id }}"
                                                {{ old('category_id', $providerProduct->category_id) == $childCategory->id ? 'selected' : '' }}
                                                style="background-color: var(--discord-dark); color: var(--discord-lightest);" data-is-parent="false">
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
                    </div>
                </div>
                
                <div class="d-flex justify-content-end mt-4 gap-2">
                    <button type="submit" class="discord-btn">
                        <i class="fas fa-save me-2"></i> {{ __('provider.update_product') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

<!-- Validation Error Modal -->
<div id="validationErrorModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background-color: var(--discord-darker); border-radius: 8px; padding: 24px; max-width: 500px; width: 90%; margin: 20px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);">
        <div style="text-align: center; margin-bottom: 20px;">
            <div style="width: 60px; height: 60px; background-color: var(--discord-red); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                <i class="fas fa-exclamation-triangle" style="color: white; font-size: 24px;"></i>
            </div>
            <h3 style="color: var(--discord-lightest); margin: 0 0 8px; font-size: 20px; font-weight: 600;">{{ __('provider.validation_error') }}</h3>
            <p style="color: var(--discord-light); margin: 0; font-size: 14px;">{{ __('provider.please_correct_following_errors') }}</p>
        </div>
        <ul id="validationErrorList" style="list-style: none; padding: 0; margin: 0 0 20px; max-height: 300px; overflow-y: auto;">
            <!-- Errors will be populated here -->
        </ul>
        <div style="text-align: center;">
            <button onclick="closeValidationErrorModal()" style="background-color: var(--discord-red); color: white; border: none; padding: 10px 24px; border-radius: 4px; font-weight: 500; cursor: pointer;">
                {{ __('provider.ok') }}
            </button>
        </div>
    </div>
</div>

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

    // Form validation
    $('form').on('submit', function(e) {
        var errors = [];

        // Category validation
        var categoryId = $('#category_id').val();
        if (!categoryId || categoryId === '') {
            errors.push({
                field: 'category_id',
                message: '{{ __('provider.category_selection_required') }}'
            });
        } else {
            // Check if selected category is a parent category
            var selectedOption = $('#category_id option:selected');
            var isParentCategory = selectedOption.data('is-parent') === true;
            if (isParentCategory) {
                errors.push({
                    field: 'category_id',
                    message: '{{ __('provider.select_specific_subcategory_not_parent') }}'
                });
            }
        }

        // Bilingual description validation
        var englishDesc = $('#description').val().trim();
        var arabicDesc = $('#product_description_arabic').val().trim();

        if (englishDesc && !arabicDesc) {
            errors.push({
                field: 'product_description_arabic',
                message: '{{ __('provider.arabic_description_required_when_english_provided') }}'
            });
        }

        if (arabicDesc && !englishDesc) {
            errors.push({
                field: 'description',
                message: '{{ __('provider.english_description_required_when_arabic_provided') }}'
            });
        }

        // If there are errors, show modal and prevent submission
        if (errors.length > 0) {
            e.preventDefault();
            showValidationErrorModal(errors);
            return false;
        }
    });
});

// Modal functions
function showValidationErrorModal(errors) {
    const modal = document.getElementById('validationErrorModal');
    const errorList = document.getElementById('validationErrorList');

    // Clear previous errors
    errorList.innerHTML = '';

    // Add each error to the list
    errors.forEach(error => {
        const li = document.createElement('li');
        li.style.cssText = 'padding: 12px; margin-bottom: 8px; background-color: rgba(220, 38, 38, 0.1); border: 1px solid rgba(220, 38, 38, 0.3); border-radius: 4px; display: flex; align-items: flex-start;';
        li.innerHTML = `
            <i class="fas fa-exclamation-circle" style="color: var(--discord-red); margin-right: 12px; margin-top: 2px; flex-shrink: 0;"></i>
            <div style="color: var(--discord-lightest); font-size: 14px; line-height: 1.4;">
                <strong>${getFieldDisplayName(error.field)}:</strong> ${error.message}
            </div>
        `;
        errorList.appendChild(li);
    });

    // Show modal
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';

    // Focus first error field
    if (errors.length > 0) {
        const firstErrorField = document.getElementById(errors[0].field) ||
                              document.querySelector(`[name="${errors[0].field}"]`);
        if (firstErrorField) {
            setTimeout(() => {
                firstErrorField.focus();
                firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 300);
        }
    }
}

function closeValidationErrorModal() {
    const modal = document.getElementById('validationErrorModal');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
}

function getFieldDisplayName(fieldName) {
    const fieldNames = {
        'category_id': '{{ __('provider.category') }}',
        'description': '{{ __('provider.description') }} ({{ __('provider.english') }})',
        'product_description_arabic': '{{ __('provider.description') }} ({{ __('provider.arabic') }})',
        'product_name': '{{ __('provider.product_name') }}',
        'product_name_arabic': '{{ __('provider.product_name_arabic') }}',
        'price': '{{ __('provider.price') }}',
        'stock': '{{ __('provider.stock_quantity') }}'
    };
    return fieldNames[fieldName] || fieldName;
}
</script>
@endsection
