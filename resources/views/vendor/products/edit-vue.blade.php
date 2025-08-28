@extends('layouts.dashboard')

@section('title', 'Edit Product')
@section('page-title', 'Edit Product')

@section('styles')
<style>
    /* Vue.js specific styles */
    .vue-app-container {
        min-height: 100vh;
    }

    /* Loading spinner styles */
    .spinner-border {
        width: 3rem;
        height: 3rem;
        border: 0.25em solid currentColor;
        border-right-color: transparent;
        border-radius: 50%;
        animation: spinner-border 0.75s linear infinite;
    }

    @keyframes spinner-border {
        to {
            transform: rotate(360deg);
        }
    }

    /* Vue component base styles */
    .vue-text-lg {
        font-size: 1.125rem;
        font-weight: 600;
        color: #1f2937;
    }

    .vue-text-sm {
        font-size: 0.875rem;
        font-weight: 500;
        color: #374151;
    }

    .vue-form-control {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        background-color: #ffffff;
        color: #1f2937;
        font-size: 0.875rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .vue-form-control:focus {
        outline: none;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    .vue-btn {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.15s ease-in-out;
        cursor: pointer;
        border: 1px solid transparent;
    }

    .vue-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .vue-btn-primary {
        background-color: #6366f1;
        color: #ffffff;
        border-color: #6366f1;
    }

    .vue-btn-primary:hover:not(:disabled) {
        background-color: #5b21b6;
        border-color: #5b21b6;
    }

    .vue-btn-secondary {
        background-color: #6b7280;
        color: #ffffff;
        border-color: #6b7280;
    }

    .vue-btn-secondary:hover:not(:disabled) {
        background-color: #4b5563;
        border-color: #4b5563;
    }

    .vue-btn-success {
        background-color: #10b981;
        color: #ffffff;
        border-color: #10b981;
    }

    .vue-btn-success:hover:not(:disabled) {
        background-color: #059669;
        border-color: #059669;
    }

    .vue-card {
        background-color: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    }

    /* Tab styles */
    .vue-tab-content {
        min-height: 400px;
    }

    /* Dark mode support */
    @media (prefers-color-scheme: dark) {
        .vue-text-lg {
            color: #f9fafb;
        }

        .vue-text-sm {
            color: #d1d5db;
        }

        .vue-form-control {
            background-color: #374151;
            border-color: #4b5563;
            color: #f9fafb;
        }

        .vue-form-control:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .vue-card {
            background-color: #1f2937;
            border-color: #374151;
        }
    }

    /* Animation classes */
    .fade-enter-active, .fade-leave-active {
        transition: opacity 0.3s;
    }

    .fade-enter-from, .fade-leave-to {
        opacity: 0;
    }

    /* Tab transition */
    .tab-transition-enter-active, .tab-transition-leave-active {
        transition: all 0.3s ease;
    }

    .tab-transition-enter-from {
        opacity: 0;
        transform: translateX(10px);
    }

    .tab-transition-leave-to {
        opacity: 0;
        transform: translateX(-10px);
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .vue-app-container {
            padding: 1rem;
        }
    }
</style>
@endsection

@section('content')
<!-- Pass translations to Vue -->
<script>
    // Set up Laravel translations for Vue components
    window.Laravel = window.Laravel || {};
    window.Laravel.translations = {
        // Page headers and main sections
        'vendor.edit_product': '{{ __('vendor.edit_product') }}',
        'vendor.update_product_information': '{{ __('vendor.update_product_information') }}',
        'vendor.product_details': '{{ __('vendor.product_details') }}',
        'vendor.colors_and_images': '{{ __('vendor.colors_and_images') }}',
        'vendor.specifications': '{{ __('vendor.specifications') }}',
        'vendor.save_changes': '{{ __('vendor.save_changes') }}',
        'vendor.back_to_products': '{{ __('vendor.back_to_products') }}',
        'vendor.loading': '{{ __('vendor.loading') }}',
        'vendor.pricing_and_inventory': '{{ __('vendor.pricing_and_inventory') }}',
        'vendor.current_price': '{{ __('vendor.current_price') }}',
        'vendor.available_for_purchase': '{{ __('vendor.available_for_purchase') }}',
        'vendor.sale': '{{ __('vendor.sale') }}',
        'vendor.off': '{{ __('vendor.off') }}',

        // Stock management
        'vendor.stock_allocation_progress': '{{ __('vendor.stock_allocation_progress') }}',
        'vendor.units_allocated': '{{ __('vendor.units_allocated') }}',
        'vendor.total_inventory_allocation_note': '{{ __('vendor.total_inventory_allocation_note') }}',
        'vendor.display_order_note': '{{ __('vendor.display_order_note') }}',
        'vendor.unit': '{{ __('vendor.unit') }}',
        'vendor.stock_allocation_exceeds_limit': '{{ __('vendor.stock_allocation_exceeds_limit') }}',
        // Basic product information
        'vendor.product_name': '{{ __('vendor.product_name') }}',
        'vendor.enter_product_name': '{{ __('vendor.enter_product_name') }}',
        'vendor.category': '{{ __('vendor.category') }}',
        'vendor.select_category': '{{ __('vendor.select_category') }}',
        'vendor.branch': '{{ __('vendor.branch') }}',
        'vendor.select_branch': '{{ __('vendor.select_branch') }}',
        'vendor.description': '{{ __('vendor.description') }}',
        'vendor.enter_description': '{{ __('vendor.enter_description') }}',
        'vendor.price': '{{ __('vendor.price') }}',
        'vendor.enter_price': '{{ __('vendor.enter_price') }}',
        'vendor.original_price': '{{ __('vendor.original_price') }}',
        'vendor.enter_original_price': '{{ __('vendor.enter_original_price') }}',
        'vendor.stock': '{{ __('vendor.stock') }}',
        'vendor.enter_stock': '{{ __('vendor.enter_stock') }}',
        'vendor.exceeds_available': '{{ __('vendor.exceeds_available') }}',
        'vendor.available_for_this_color': '{{ __('vendor.available_for_this_color') }}',
        'vendor.currently_allocated': '{{ __('vendor.currently_allocated') }}',
        'vendor.display_order': '{{ __('vendor.display_order') }}',
        'vendor.enter_display_order': '{{ __('vendor.enter_display_order') }}',
        'vendor.is_available': '{{ __('vendor.is_available') }}',
        'vendor.product_available': '{{ __('vendor.product_available') }}',
        'vendor.continue': '{{ __('vendor.continue') }}',
        'vendor.branch_selection_required ': '{{ __('vendor.branch_selection_required') }}',
        'vendor.price_must_be_greater_than_zero ': '{{ __('vendor.price_must_be_greater_than_zero') }}',
        'vendor.color_variants_required': '{{ __('vendor.color_variants_required') }}',

        // Color and variant information
        'vendor.color_variant': '{{ __('vendor.color_variant') }}',
        'vendor.default': '{{ __('vendor.default') }}',
        'vendor.set_as_default': '{{ __('vendor.set_as_default') }}',
        'vendor.color_name': '{{ __('vendor.color_name') }}',
        'vendor.select_color': '{{ __('vendor.select_color') }}',
        'vendor.color_code': '{{ __('vendor.color_code') }}',
        'vendor.price_adjustment': '{{ __('vendor.price_adjustment') }}',
        'vendor.color_stock': '{{ __('vendor.color_stock') }}',
        'vendor.upload_image': '{{ __('vendor.upload_image') }}',
        'vendor.change_image': '{{ __('vendor.change_image') }}',
        'vendor.remove_image': '{{ __('vendor.remove_image') }}',
        'vendor.drag_drop_image': '{{ __('vendor.drag_drop_image') }}',
        'vendor.click_to_upload': '{{ __('vendor.click_to_upload') }}',
        'vendor.supported_formats': '{{ __('vendor.supported_formats') }}',
        'vendor.max_file_size': '{{ __('vendor.max_file_size') }}',

        // Size management
        'vendor.size_management': '{{ __('vendor.size_management') }}',
        'vendor.add_size': '{{ __('vendor.add_size') }}',
        'vendor.size_name': '{{ __('vendor.size_name') }}',
        'vendor.size_value': '{{ __('vendor.size_value') }}',
        'vendor.size_stock': '{{ __('vendor.size_stock') }}',
        'vendor.size_price_adjustment': '{{ __('vendor.size_price_adjustment') }}',
        'vendor.manage_sizes_stock_allocation': '{{ __('vendor.manage_sizes_stock_allocation') }}',
        'vendor.stock_allocation_for_color': '{{ __('vendor.stock_allocation_for_color') }}',
        'vendor.allocated': '{{ __('vendor.allocated') }}',
        'vendor.units': '{{ __('vendor.units') }}',
        'vendor.available': '{{ __('vendor.available') }}',
        'vendor.stock_quantity': '{{ __('vendor.stock_quantity') }}',
        'vendor.no_sizes_added_yet': '{{ __('vendor.no_sizes_added_yet') }}',
        'vendor.click_add_size_to_start_managing': '{{ __('vendor.click_add_size_to_start_managing') }}',
        'vendor.allocated_stock': '{{ __('vendor.allocated_stock') }}',
        'vendor.all_stock_allocated': '{{ __('vendor.all_stock_allocated') }}',
        'click_add_size_to_start_managing': '{{ __('vendor.click_add_size_to_start_managing') }}',
        'vendor.remaining_stock': '{{ __('vendor.remaining_stock') }}',

        // Size Management Modal/Form
        'vendor.size_category': '{{ __('vendor.size_category') }}',
        'select_size_category': '{{ __('vendor.select_size_category') }}',
        'select_size_name': '{{ __('vendor.select_size_name') }}',
        'auto_filled_based_on_size_name': '{{ __('vendor.auto_filled_based_on_size_name') }}',
        'adding': '{{ __('vendor.adding') }}',
        'add_size': '{{ __('vendor.add_size') }}',
        'cancel': '{{ __('vendor.cancel') }}',
        'save': '{{ __('vendor.save') }}',
        'saving': '{{ __('vendor.saving') }}',
        'edit': '{{ __('vendor.edit') }}',
        'delete': '{{ __('vendor.delete') }}',

        // Size Categories
        'clothes': '{{ __('vendor.clothes') }}',
        'shoes': '{{ __('vendor.shoes') }}',
        'accessories': '{{ __('vendor.accessories') }}',
        'electronics': '{{ __('vendor.electronics') }}',
        'other': '{{ __('vendor.other') }}',

        // Validation Messages
        'vendor.size_category_required': '{{ __('vendor.size_category_required') }}',
        'vendor.size_name_required': '{{ __('vendor.size_name_required') }}',
        'vendor.size_value_required': '{{ __('vendor.size_value_required') }}',
        'vendor.stock_cannot_be_negative': '{{ __('vendor.stock_cannot_be_negative') }}',
        'vendor.stock_cannot_exceed_color_stock': '{{ __('vendor.stock_cannot_exceed_color_stock') }}',
        'vendor.failed_load_sizes': '{{ __('vendor.failed_load_sizes') }}',
        'vendor.failed_save_size': '{{ __('vendor.failed_save_size') }}',
        'vendor.failed_remove_size': '{{ __('vendor.failed_remove_size') }}',
        'vendor.failed_add_size': '{{ __('vendor.failed_add_size') }}',

        // Keys without vendor prefix (for Vue component compatibility)
        'size_name': '{{ __('vendor.size_name') }}',
        'stock_quantity': '{{ __('vendor.stock_quantity') }}',
        'units': '{{ __('vendor.units') }}',

        // Additional Size Management Modal Keys
        'add_new_size': '{{ __('vendor.add_new_size') }}',
        'select_category': '{{ __('vendor.select_category') }}',
        'to_allocate': '{{ __('vendor.to_allocate') }}',
        'available': '{{ __('vendor.available') }}',

        // Keys without vendor prefix for Vue component compatibility
        'size_category': '{{ __('vendor.size_category') }}',
        'vendor.aed': '{{ __('vendor.currency_aed') }}',
        'to_allocate': '{{ __('vendor.to_allocate') }}',

        // Specifications
        'vendor.specifications': '{{ __('vendor.specifications') }}',
        'vendor.product_specifications': '{{ __('vendor.product_specifications') }}',
        'vendor.add_detailed_specifications': '{{ __('vendor.add_detailed_specifications') }}',
        'vendor.no_specifications_added_yet': '{{ __('vendor.no_specifications_added_yet') }}',
        'vendor.add_technical_specifications': '{{ __('vendor.add_technical_specifications') }}',
        'vendor.add_first_specification': '{{ __('vendor.add_first_specification') }}',
        'vendor.add_specification': '{{ __('vendor.add_specification') }}',
        'vendor.specification_key': '{{ __('vendor.specification_key') }}',
        'vendor.specification_value': '{{ __('vendor.specification_value') }}',
        'vendor.enter_specification_key': '{{ __('vendor.enter_specification_key') }}',
        'vendor.enter_specification_value': '{{ __('vendor.enter_specification_value') }}',

        // Actions and buttons
        'vendor.save_product': '{{ __('vendor.save_product') }}',
        'vendor.update_product': '{{ __('vendor.update_product') }}',
        'vendor.cancel': '{{ __('vendor.cancel') }}',
        'vendor.back_to_products': '{{ __('vendor.back_to_products') }}',
        'vendor.add_color': '{{ __('vendor.add_color') }}',
        'vendor.remove_color': '{{ __('vendor.remove_color') }}',
        'vendor.preview_product': '{{ __('vendor.preview_product') }}',
        'vendor.product_colors': '{{ __('vendor.product_colors') }}',
        'vendor.add_color_variants_with_images': '{{ __('vendor.add_color_variants_with_images') }}',
        'vendor.color_name': '{{ __('vendor.color_name') }}',
        'vendor.select_color': '{{ __('vendor.select_color') }}',
        'vendor.color_code': '{{ __('vendor.color_code') }}',
        'vendor.price_adjustment': '{{ __('vendor.price_adjustment') }}',
        'vendor.color_image': '{{ __('vendor.color_image') }}',
        'vendor.default_color': '{{ __('vendor.default_color') }}',
        'vendor.image_format_info': '{{ __('vendor.image_format_info') }}',
        'vendor.main_product_image_info': '{{ __('vendor.main_product_image_info') }}',
        'vendor.available_for_this_color': '{{ __('vendor.available_for_this_color') }}',
        'vendor.currently_allocated': '{{ __('vendor.currently_allocated') }}',

        // Tabs
        'vendor.basic_info': '{{ __('vendor.basic_info') }}',
        'vendor.colors_images': '{{ __('vendor.colors_images') }}',
        'vendor.specifications_tab': '{{ __('vendor.specifications_tab') }}',

        // Messages and validation
        'vendor.product_updated_successfully': '{{ __('vendor.product_updated_successfully') }}',
        'vendor.error_updating_product': '{{ __('vendor.error_updating_product') }}',
        'vendor.please_fill_required_fields': '{{ __('vendor.please_fill_required_fields') }}',
        'vendor.at_least_one_color_required': '{{ __('vendor.at_least_one_color_required') }}',
        'vendor.at_least_one_image_required': '{{ __('vendor.at_least_one_image_required') }}',
        'vendor.loading': '{{ __('vendor.loading') }}',
        'vendor.saving': '{{ __('vendor.saving') }}',
        'vendor.success': '{{ __('vendor.success') }}',
        'vendor.error': '{{ __('vendor.error') }}',
        'vendor.try_again': '{{ __('vendor.try_again') }}',
        'vendor.close': '{{ __('vendor.close') }}',
           'vendor.please_fix_validation_errors' : '{{ __('vendor.please_fix_validation_errors') }}',
        'vendor.product_name_both_languages_required' : '{{ __('vendor.product_name_both_languages_required') }}',
        'vendor.category_selection_required' : '{{ __('vendor.category_selection_required') }}',

        // Stock management
        'vendor.total_stock': '{{ __('vendor.total_stock') }}',
        'vendor.allocated_stock': '{{ __('vendor.allocated_stock') }}',
        'vendor.available_stock': '{{ __('vendor.available_stock') }}',
        'vendor.stock_allocation': '{{ __('vendor.stock_allocation') }}',
        'vendor.over_allocated': '{{ __('vendor.over_allocated') }}',
        'vendor.stock_corrected': '{{ __('vendor.stock_corrected') }}',

        // General
        'vendor.edit_product': '{{ __('vendor.edit_product') }}',
        'vendor.update_product_inventory': '{{ __('vendor.update_product_inventory') }}',
        'vendor.required_field': '{{ __('vendor.required_field') }}',
        'vendor.optional_field': '{{ __('vendor.optional_field') }}',
        'vendor.currency_aed': '{{ __('vendor.currency_aed') }}',
        'vendor.pieces': '{{ __('vendor.pieces') }}',
        'vendor.sale_badge': '{{ __('vendor.sale_badge') }}',
        'vendor.off': '{{ __('vendor.off') }}',
        'enter_product_name_english': '{{ __('vendor.enter_product_name_english') }}',
        'enter_product_name_arabic': '{{ __('vendor.enter_product_name_arabic') }}',
        'enter_product_description_english': '{{ __('vendor.enter_product_description_english') }}',
        'enter_product_description_arabic': '{{ __('vendor.enter_product_description_arabic') }}',
    };

    // Also set up appTranslations for backward compatibility
    window.appTranslations = window.Laravel.translations;
</script>

<div id="vendor-product-edit-app"
     class="vue-app-container"
     data-product-id="{{ $product->id }}"
     data-back-url="{{ route('vendor.products.index') }}">
</div>
@endsection

@push('scripts')
<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- Vue App Script -->
@vite(['resources/js/vendor-product-edit.js'])
@endpush

@section('scripts')
<!-- Additional scripts if needed -->
<script>
    // Global error handler for Vue.js
    window.addEventListener('unhandledrejection', function(event) {
        console.error('Unhandled promise rejection:', event.reason);
    });

    // CSRF token setup for AJAX requests
    window.axios = window.axios || {};
    if (window.axios.defaults) {
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    }

    // Set up global fetch defaults
    const originalFetch = window.fetch;
    window.fetch = function(url, options = {}) {
        options.headers = options.headers || {};
        if (!options.headers['X-CSRF-TOKEN']) {
            options.headers['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        }
        return originalFetch(url, options);
    };

    // Add any global configuration needed for the Vue app
    window.vendorProductEditConfig = {
        apiBaseUrl: '{{ url("/") }}',
        vendorProductsUrl: '{{ route("vendor.products.index") }}',
        csrfToken: '{{ csrf_token() }}'
    };
</script>
@endsection
