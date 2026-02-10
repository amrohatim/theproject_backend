@extends('layouts.dashboard')

@section('title', __('provider.add_new_product'))
@section('page-title', __('provider.create_new_product'))

@section('styles')
<style>
    .lang-toggle {
        display: inline-flex;
        gap: 0.5rem;
        padding: 0.25rem;
        border: 1px solid #e5e7eb;
        border-radius: 9999px;
        background: #ffffff;
    }
    .lang-toggle button {
        border: none;
        background: transparent;
        color: #6b7280;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.35rem 0.9rem;
        border-radius: 9999px;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        cursor: pointer;
    }
    .lang-toggle button.active {
        background: #f3f4f6;
        color: #111827;
    }
    [dir="rtl"] .rtl-flip {
        transform: scaleX(-1);
    }
    .dark .lang-toggle {
        border-color: #374151;
        background: #111827;
    }
    .dark .lang-toggle button {
        color: #9ca3af;
    }
    .dark .lang-toggle button.active {
        background: #1f2937;
        color: #f9fafb;
    }
</style>
@endsection

@section('content')
<div class="container mx-auto">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ __('provider.new_product') }}</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('provider.add_new_product_description') }}</p>
        </div>
        <div class="mt-4 md:mt-0 flex flex-wrap items-center gap-3">
            <a href="{{ route('provider.provider-products.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2 rtl-flip"></i> {{ __('provider.back_to_inventory') }}
            </a>
            <button type="reset" form="providerProductForm" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                <i class="fas fa-undo mr-2"></i> {{ __('provider.reset') }}
            </button>
            <button type="submit" form="providerProductForm" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-save mr-2"></i> {{ __('provider.add_to_inventory') }}
            </button>
        </div>
    </div>

    <form id="providerProductForm" action="{{ route('provider.provider-products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wide">{{ __('provider.basic_information') }}</h3>
                    </div>

                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('provider.product_name') }} <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-3">
                                <div class="lang-toggle" data-lang-toggle="product_name">
                                    <button type="button" class="active" data-lang="en">EN</button>
                                    <button type="button" data-lang="ar">AR</button>
                                </div>
                            </div>
                            <div class="mt-4" data-lang-field="product_name" data-lang="en" style="display: block;">
                                <input type="text" id="product_name" name="product_name" value="{{ old('product_name') }}" required
                                    placeholder="{{ __('provider.enter_product_name_english') }}"
                                    class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                                @error('product_name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mt-4" data-lang-field="product_name" data-lang="ar" style="display: none;" dir="rtl">
                                <input type="text" id="product_name_arabic" name="product_name_arabic" value="{{ old('product_name_arabic') }}" required
                                    placeholder="{{ __('provider.enter_product_name_arabic') }}"
                                    class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                                @error('product_name_arabic')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('provider.description') }}</label>
                            <div class="mt-3">
                                <div class="lang-toggle" data-lang-toggle="product_description">
                                    <button type="button" class="active" data-lang="en">EN</button>
                                    <button type="button" data-lang="ar">AR</button>
                                </div>
                            </div>
                            <div class="mt-4" data-lang-field="product_description" data-lang="en" style="display: block;">
                                <textarea id="description" name="description" rows="5"
                                    
                                    class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 resize-y">{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mt-4" data-lang-field="product_description" data-lang="ar" style="display: none;" dir="rtl">
                                <textarea id="product_description_arabic" name="product_description_arabic" rows="5"
                                    
                                    class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 resize-y">{{ old('product_description_arabic') }}</textarea>
                                @error('product_description_arabic')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wide mb-6">{{ __('provider.pricing') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('provider.price') }} ($) <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-2 relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">$</span>
                                <input type="number" step="0.01" min="0" id="price" name="price" value="{{ old('price') }}" required
                                    class="block w-full pl-8 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            @error('price')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="original_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('provider.original_price') }} ($)
                            </label>
                            <div class="mt-2 relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">$</span>
                                <input type="number" step="0.01" min="0" id="original_price" name="original_price" value="{{ old('original_price') }}"
                                    class="block w-full pl-8 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('provider.original_price_description') }}</p>
                            @error('original_price')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wide mb-6">{{ __('provider.inventory') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="sku" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('provider.sku') }}</label>
                            <input type="text" id="sku" name="sku" value="{{ old('sku') }}"
                                class="mt-2 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('provider.sku_auto_generated') }}</p>
                            @error('sku')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="stock" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('provider.stock_quantity') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="number" min="0" id="stock" name="stock" value="{{ old('stock', 1) }}" required
                                class="mt-2 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                            @error('stock')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="min_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('provider.min_order') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="number" min="1" id="min_order" name="min_order" value="{{ old('min_order', 1) }}" required
                                class="mt-2 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('provider.min_order_help') }}</p>
                            @error('min_order')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wide mb-4">{{ __('provider.product_image') }}</h3>
                    <div class="text-center">
                        <img id="image-preview" src="#" alt="Preview" class="mx-auto mb-4 max-h-48 rounded-lg hidden">
                        <div id="image-placeholder" class="border border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-gray-500 dark:text-gray-400">
                            <i class="fas fa-image text-3xl mb-3"></i>
                            <p class="text-sm">{{ __('provider.no_image_selected') }}</p>
                        </div>
                        <input type="file" id="image" name="image" accept="image/*" class="hidden">
                        <label for="image" class="mt-4 inline-flex items-center justify-center w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                            <i class="fas fa-upload mr-2"></i> {{ __('provider.select_image') }}
                        </label>
                        @error('image')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wide mb-4">{{ __('provider.category') }}</h3>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('provider.select_category') }}</label>
                    <select id="category_id" name="category_id" required
                        class="mt-2 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">{{ __('provider.select_category') }}</option>
                        @foreach($parentCategories as $parentCategory)
                            <optgroup label="{{ $parentCategory->name }}">
                                @foreach($parentCategory->children as $childCategory)
                                    <option value="{{ $childCategory->id }}" {{ old('category_id') == $childCategory->id ? 'selected' : '' }}>
                                        — {{ $childCategory->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <input type="hidden" name="is_active" value="1">
                </div>
            </div>
        </div>

    </form>
</div>
@endsection

<!-- Validation Error Modal -->
<div id="validationErrorModal" style="display: none; position: fixed; inset: 0; background-color: rgba(0,0,0,0.45); z-index: 9999; align-items: center; justify-content: center;">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-lg mx-4 shadow-xl">
        <div class="text-center mb-5">
            <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-red-100 text-red-600">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('provider.validation_error') }}</h3>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('provider.please_correct_following_errors') }}</p>
        </div>
        <ul id="validationErrorList" class="space-y-2 max-h-72 overflow-y-auto"></ul>
        <div class="mt-5 text-center">
            <button onclick="closeValidationErrorModal()" class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700">
                {{ __('provider.ok') }}
            </button>
        </div>
    </div>
</div>

@section('scripts')
<script>
$(document).ready(function() {
    initLangToggles();

    $('#image').on('change', function() {
        var file = this.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#image-preview').attr('src', e.target.result).removeClass('hidden');
                $('#image-placeholder').hide();
            }
            reader.readAsDataURL(file);
        }
    });

    $('#product_name').on('blur', function() {
        if (!$('#sku').val()) {
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

    $('form').on('submit', function(e) {
        var errors = [];

        var categoryId = $('#category_id').val();
        if (!categoryId || categoryId === '') {
            errors.push({
                field: 'category_id',
                message: '{{ __('provider.category_selection_required') }}'
            });
        }

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

        var stockValue = parseInt($('#stock').val(), 10);
        var minOrderValue = parseInt($('#min_order').val(), 10);
        if (!isNaN(stockValue) && !isNaN(minOrderValue) && minOrderValue > stockValue) {
            errors.push({
                field: 'min_order',
                message: '{{ __('provider.min_order_cannot_exceed_stock') }}'
            });
        }

        if (errors.length > 0) {
            e.preventDefault();
            showValidationErrorModal(errors);
            return false;
        }
    });
});

function initLangToggles() {
    document.querySelectorAll('.lang-toggle').forEach(toggle => {
        const fieldName = toggle.getAttribute('data-lang-toggle');
        const buttons = toggle.querySelectorAll('button');

        buttons.forEach(button => {
            button.addEventListener('click', () => {
                const lang = button.getAttribute('data-lang');

                buttons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');

                document.querySelectorAll(`[data-lang-field="${fieldName}"]`).forEach(field => {
                    field.style.display = 'none';
                });

                const selectedField = document.querySelector(`[data-lang-field="${fieldName}"][data-lang="${lang}"]`);
                if (selectedField) {
                    selectedField.style.display = 'block';
                }
            });
        });

        const defaultField = document.querySelector(`[data-lang-field="${fieldName}"][data-lang="en"]`);
        if (defaultField) {
            defaultField.style.display = 'block';
        }
        const arabicField = document.querySelector(`[data-lang-field="${fieldName}"][data-lang="ar"]`);
        if (arabicField) {
            arabicField.style.display = 'none';
        }
    });
}

function showValidationErrorModal(errors) {
    const modal = document.getElementById('validationErrorModal');
    const errorList = document.getElementById('validationErrorList');

    errorList.innerHTML = '';

    errors.forEach(error => {
        const li = document.createElement('li');
        li.className = 'flex items-start gap-3 rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-700';
        li.innerHTML = `
            <i class="fas fa-exclamation-circle mt-0.5 text-red-500"></i>
            <div>
                <strong>${getFieldDisplayName(error.field)}:</strong> ${error.message}
            </div>
        `;
        errorList.appendChild(li);
    });

    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';

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
        'stock': '{{ __('provider.stock_quantity') }}',
        'min_order': '{{ __('provider.min_order') }}'
    };
    return fieldNames[fieldName] || fieldName;
}
</script>
@endsection
