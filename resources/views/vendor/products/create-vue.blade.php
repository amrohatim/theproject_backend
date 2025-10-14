@extends('layouts.dashboard')

@section('title', 'Add Product')
@section('page-title', 'Add Product')

@section('styles')
<style>
    /* Vue.js specific styles with modern UI improvements */
    .vue-app-container {
        min-height: 100vh;
        background: linear-gradient(135deg, #f5f7fa 0%, #f0f2f5 100%);
        padding: 2rem 0;
    }

    /* Loading spinner styles - Enhanced */
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

    /* Modern Typography */
    .vue-text-lg {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1f2937;
        letter-spacing: -0.025em;
    }

    .vue-text-sm {
        font-size: 0.875rem;
        font-weight: 600;
        color: #374151;
        letter-spacing: -0.01em;
    }

    /* Enhanced Form Controls with Modern Design - Vendor (Blue Theme) */
    .vue-form-control-vendor {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 0.75rem;
        background-color: #ffffff;
        color: #1f2937;
        font-size: 0.9375rem;
        font-weight: 500;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }

    .vue-form-control-vendor:hover {
        border-color: #d1d5db;
        box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.06);
    }

    .vue-form-control-vendor:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1), 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        transform: translateY(-1px);
    }

    .vue-form-control-vendor::placeholder {
        color: #9ca3af;
        font-weight: 400;
    }

    /* Enhanced Form Controls with Modern Design - Products Manager (Orange Theme) */
    .vue-form-control-pm {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 0.75rem;
        background-color: #ffffff;
        color: #1f2937;
        font-size: 0.9375rem;
        font-weight: 500;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }

    .vue-form-control-pm:hover {
        border-color: #d1d5db;
        box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.06);
    }

    .vue-form-control-pm:focus {
        outline: none;
        border-color: #f59e0b;
        box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1), 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        transform: translateY(-1px);
    }

    .vue-form-control-pm::placeholder {
        color: #9ca3af;
        font-weight: 400;
    }

    /* Modern Button Styles */
    .vue-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.75rem 1.5rem;
        border-radius: 0.75rem;
        font-size: 0.9375rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        border: 2px solid transparent;
        box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.06);
        letter-spacing: -0.01em;
    }

    .vue-btn:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px 0 rgba(0, 0, 0, 0.15);
    }

    .vue-btn:active:not(:disabled) {
        transform: translateY(0);
        box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.06);
    }

    .vue-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none !important;
    }

    /* Vendor Primary Button (Blue Theme) */
    .vue-btn-primary-vendor {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: #ffffff;
        border-color: transparent;
    }

    .vue-btn-primary-vendor:hover:not(:disabled) {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        box-shadow: 0 6px 16px 0 rgba(59, 130, 246, 0.4);
    }

    /* Products Manager Primary Button (Orange Theme) */
    .vue-btn-primary-pm {
        background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
        color: #ffffff;
        border-color: transparent;
    }

    .vue-btn-primary-pm:hover:not(:disabled) {
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        box-shadow: 0 6px 16px 0 rgba(245, 158, 11, 0.4);
    }

    .vue-btn-secondary {
        background-color: #ffffff;
        color: #374151;
        border-color: #e5e7eb;
    }

    .vue-btn-secondary:hover:not(:disabled) {
        background-color: #f9fafb;
        border-color: #d1d5db;
    }

    .vue-btn-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff;
        border-color: transparent;
    }

    .vue-btn-success:hover:not(:disabled) {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        box-shadow: 0 6px 16px 0 rgba(16, 185, 129, 0.4);
    }

    /* Enhanced Card Design */
    .vue-card {
        background-color: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }

    .vue-card:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        transform: translateY(-2px);
    }

    /* Tab styles - Modern Design */
    .vue-tab-content {
        min-height: 400px;
        animation: fadeInUp 0.4s ease-out;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Enhanced Animation classes */
    .fade-enter-active, .fade-leave-active {
        transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .fade-enter-from, .fade-leave-to {
        opacity: 0;
    }

    /* Smooth Tab transition */
    .tab-transition-enter-active, .tab-transition-leave-active {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .tab-transition-enter-from {
        opacity: 0;
        transform: translateX(20px);
    }

    .tab-transition-leave-to {
        opacity: 0;
        transform: translateX(-20px);
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .vue-app-container {
            padding: 1rem;
        }

        .vue-btn {
            padding: 0.625rem 1.25rem;
            font-size: 0.875rem;
        }

        .vue-form-control {
            padding: 0.625rem 0.875rem;
            font-size: 0.875rem;
        }
    }
</style>
@endsection

@section('content')
<!-- Pass translations to Vue -->
<script>
    // Set up Laravel translations for Vue components
    window.Laravel = window.Laravel || {};
    window.Laravel.translations = {!! json_encode([
        // Page headers and main sections
        'vendor.create_new_product' => __('vendor.create_new_product'),
        'vendor.add_new_product_inventory' => __('vendor.add_new_product_inventory'),
        'vendor.product_details' => __('vendor.product_details'),
        'vendor.colors_and_images' => __('vendor.colors_and_images'),
        'vendor.specifications' => __('vendor.specifications'),
        'vendor.enter_basic_details' => __('vendor.enter_basic_details'),
        'vendor.next' => __('vendor.next'),
        'vendor.previous' => __('vendor.previous'),
        'vendor.back_to_products' => __('vendor.back_to_products'),
        'vendor.create_another' => __('vendor.create_another'),

        // Basic product information
        'vendor.product_name' => __('vendor.product_name'),
        'vendor.enter_product_name' => __('vendor.enter_product_name'),
        'vendor.category' => __('vendor.category'),
        'vendor.select_category' => __('vendor.select_category'),
        'vendor.branch' => __('vendor.branch'),
        'vendor.select_branch' => __('vendor.select_branch'),
        'vendor.description' => __('vendor.description'),
        'vendor.enter_description' => __('vendor.enter_description'),
        'vendor.price' => __('vendor.price'),
        'vendor.enter_price' => __('vendor.enter_price'),
        'vendor.original_price' => __('vendor.original_price'),
        'vendor.enter_original_price' => __('vendor.enter_original_price'),
        'vendor.stock' => __('vendor.stock'),
        'vendor.enter_stock' => __('vendor.enter_stock'),
        'vendor.exceeds_available' => __('vendor.exceeds_available'),
        'vendor.available_for_this_color' => __('vendor.available_for_this_color'),
        'vendor.currently_allocated' => __('vendor.currently_allocated'),
        'vendor.sku' => __('vendor.sku'),
        'vendor.enter_sku' => __('vendor.enter_sku'),

        // Colors and variants
        'vendor.colors' => __('vendor.colors'),
        'vendor.add_color' => __('vendor.add_color'),
        'vendor.add_first_color' => __('vendor.add_first_color'),
        'vendor.no_colors_added_yet' => __('vendor.no_colors_added_yet'),
        'vendor.color_name' => __('vendor.color_name'),
        'vendor.select_color' => __('vendor.select_color'),
        'vendor.search_colors' => __('vendor.search_colors'),
        'vendor.color_code' => __('vendor.color_code'),
        'vendor.price_adjustment' => __('vendor.price_adjustment'),
        'vendor.total_stock' => __('vendor.total_stock'),
        'vendor.display_order' => __('vendor.display_order'),
        'vendor.color_image' => __('vendor.color_image'),
        'vendor.default_color' => __('vendor.default_color'),
        'vendor.no_image_selected' => __('vendor.no_image_selected'),
        'vendor.image_preview_size' => __('vendor.image_preview_size'),
        'vendor.image_format_info' => __('vendor.image_format_info'),
        'vendor.main_product_image_info' => __('vendor.main_product_image_info'),
        'vendor.available_for_this_color' => __('vendor.available_for_this_color'),
        'vendor.currently_allocated' => __('vendor.currently_allocated'),
        'vendor.set_color_name_stock_for_sizes' => __('vendor.set_color_name_stock_for_sizes'),
        'vendor.add_color_variants_images' => __('vendor.add_color_variants_images'),
        'vendor.add_color_variants_appealing' => __('vendor.add_color_variants_appealing'),
        'vendor.color_variant' => __('vendor.color_variant'),

        // Sizes
        'vendor.sizes' => __('vendor.sizes'),
        'vendor.size_management' => __('vendor.size_management'),
        'vendor.add_size' => __('vendor.add_size'),
        'vendor.size_name' => __('vendor.size_name'),
        'vendor.size_value' => __('vendor.size_value'),
        'vendor.size_stock' => __('vendor.size_stock'),
        'vendor.manage_sizes_stock_allocation' => __('vendor.manage_sizes_stock_allocation'),
        'vendor.stock_allocation_for_color' => __('vendor.stock_allocation_for_color'),
        'vendor.allocated' => __('vendor.allocated'),
        'vendor.units' => __('vendor.units'),
        'vendor.unit' => __('vendor.unit'),
        'vendor.available' => __('vendor.available'),
        'vendor.stock_quantity' => __('vendor.stock_quantity'),
        'vendor.no_sizes_added_yet' => __('vendor.no_sizes_added_yet'),
        'vendor.click_add_size_to_start_managing' => __('vendor.click_add_size_to_start_managing'),
        'vendor.allocated_stock' => __('vendor.allocated_stock'),
        'vendor.all_stock_allocated' => __('vendor.all_stock_allocated'),
        'click_add_size_to_start_managing' => __('vendor.click_add_size_to_start_managing'),
        'vendor.remaining_stock' => __('vendor.remaining_stock'),

        // Size Management Modal/Form
        'vendor.size_category' => __('vendor.size_category'),
        'select_size_category' => __('vendor.select_size_category'),
        'select_size_name' => __('vendor.select_size_name'),
        'auto_filled_based_on_size_name' => __('vendor.auto_filled_based_on_size_name'),
        'adding' => __('vendor.adding'),
        'add_size' => __('vendor.add_size'),
        'cancel' => __('vendor.cancel'),
        'save' => __('vendor.save'),
        'saving' => __('vendor.saving'),
        'edit' => __('vendor.edit'),
        'delete' => __('vendor.delete'),

        // Size Categories
        'clothes' => __('vendor.clothes'),
        'shoes' => __('vendor.shoes'),
        'hats' => __('vendor.hats'),
        'accessories' => __('vendor.accessories'),
        'electronics' => __('vendor.electronics'),
        'other' => __('vendor.other'),

        // Validation Messages
        'vendor.size_category_required' => __('vendor.size_category_required'),
        'vendor.size_name_required' => __('vendor.size_name_required'),
        'vendor.size_value_required' => __('vendor.size_value_required'),
        'vendor.stock_cannot_be_negative' => __('vendor.stock_cannot_be_negative'),
        'vendor.stock_cannot_exceed_color_stock' => __('vendor.stock_cannot_exceed_color_stock'),
        'vendor.failed_load_sizes' => __('vendor.failed_load_sizes'),
        'vendor.failed_save_size' => __('vendor.failed_save_size'),
        'vendor.failed_remove_size' => __('vendor.failed_remove_size'),
        'vendor.failed_add_size' => __('vendor.failed_add_size'),

        // Keys without vendor prefix (for Vue component compatibility)
        'size_name' => __('vendor.size_name'),
        'size_value' => __('vendor.size_value'),
        'stock_quantity' => __('vendor.stock_quantity'),
        'price_adjustment' => __('vendor.price_adjustment'),
        'units' => __('vendor.units'),
        'unit' => __('vendor.unit'),
        'aed' => __('vendor.aed'),
        'vendor.aed'=> __('vendor.aed'),
        'available' => __('vendor.available'),
        'to_allocate' => __('vendor.to_allocate'),

        // Additional Size Management Modal Keys
        'add_new_size' => __('vendor.add_new_size'),
        'select_category' => __('vendor.select_category'),
        'size_category' => __('vendor.size_category'),

        // Specifications
        'vendor.specifications' => __('vendor.specifications'),
        'vendor.add_specification' => __('vendor.add_specification'),
        'vendor.specification_key' => __('vendor.specification_key'),
        'vendor.specification_value' => __('vendor.specification_value'),
        'vendor.specification_key_placeholder' => __('vendor.specification_key_placeholder'),
        'vendor.specification_value_placeholder' => __('vendor.specification_value_placeholder'),
        'vendor.add_first_specification' => __('vendor.add_first_specification'),
        'vendor.order' => __('vendor.order'),
        'vendor.stock_allocation_exceeds_limit' => __('vendor.stock_allocation_exceeds_limit'),
        'vendor.exceeds_available' => __('vendor.exceeds_available'),

        // Images
        'vendor.product_images' => __('vendor.product_images'),
        'vendor.main_image' => __('vendor.main_image'),
        'vendor.additional_images' => __('vendor.additional_images'),
        'vendor.upload_image' => __('vendor.upload_image'),
        'vendor.drag_drop_image' => __('vendor.drag_drop_image'),


        // Actions
        'vendor.save_product' => __('vendor.save_product'),
        'vendor.save_draft' => __('vendor.save_draft'),
        'vendor.cancel' => __('vendor.cancel'),
        'vendor.back_to_products' => __('vendor.back_to_products'),
        'vendor.loading' => __('vendor.loading'),
        'vendor.saving' => __('vendor.saving'),

        // Validation messages
        'vendor.required_field' => __('vendor.required_field'),
        'vendor.invalid_price' => __('vendor.invalid_price'),
        'vendor.invalid_stock' => __('vendor.invalid_stock'),
        'vendor.image_too_large' => __('vendor.image_too_large'),
        'vendor.invalid_image_format' => __('vendor.invalid_image_format'),
        'vendor.please_fix_validation_errors' => __('vendor.please_fix_validation_errors'),
        'vendor.product_name_both_languages_required' => __('vendor.product_name_both_languages_required'),
        'vendor.category_selection_required' => __('vendor.category_selection_required'),
         'vendor.set_as_default' => __('vendor.set_as_default'),
        // Success messages
        'vendor.product_created_successfully' => __('vendor.product_created_successfully'),
        'vendor.draft_saved_successfully' => __('vendor.draft_saved_successfully'),

        // Error messages
        'vendor.error_creating_product' => __('vendor.error_creating_product'),
        'vendor.error_saving_draft' => __('vendor.error_saving_draft'),
        'vendor.network_error' => __('vendor.network_error'),
        'vendor.continue' => __('vendor.continue'),

        // Section titles
        'vendor.basic_information' => __('vendor.basic_information'),
        'vendor.pricing_inventory' => __('vendor.pricing_inventory'),
        'vendor.product_variants' => __('vendor.product_variants'),
        'vendor.product_specifications' => __('vendor.product_specifications'),
        'vendor.product_images_section' => __('vendor.product_images_section'),

        // Additional missing keys
        'vendor.branch_automatically_selected' => __('vendor.branch_automatically_selected'),
        'vendor.total_stock_quantity_available' => __('vendor.total_stock_quantity_available'),
        'vendor.enter_product_description' => __('vendor.enter_product_description'),
        'vendor.product_available_sale' => __('vendor.product_available_sale'),
        'vendor.uncheck_if_not_available' => __('vendor.uncheck_if_not_available'),
        'vendor.loading_product_creation_form' => __('vendor.loading_product_creation_form'),
        'vendor.need_create_branch_first' => __('vendor.need_create_branch_first'),
        'vendor.no_branches_available' => __('vendor.no_branches_available'),
        'vendor.branch_selection_required' => __('vendor.branch_selection_required'),
        'vendor.price_must_be_greater_than_zero' => __('vendor.price_must_be_greater_than_zero'),
        'vendor.color_variants_required' => __('vendor.color_variants_required'),

        // Common words
        'vendor.yes' => __('vendor.yes'),
        'vendor.no' => __('vendor.no'),
        'vendor.optional' => __('vendor.optional'),
        'vendor.required' => __('vendor.required'),
        'vendor.remove' => __('vendor.remove'),
        'vendor.add' => __('vendor.add'),
        'vendor.edit' => __('vendor.edit'),
        'vendor.delete' => __('vendor.delete'),
        'vendor.confirm' => __('vendor.confirm'),
        'vendor.close' => __('vendor.close'),
        'vendor.default' => __('vendor.default'),
        'vendor.stock_allocation_progress' => __('vendor.stock_allocation_progress'),
        'vendor.stock_auto_corrected' => __('vendor.stock_auto_corrected'),
        'vendor.add_detailed_specifications' => __('vendor.add_detailed_specifications'),
        'vendor.no_specifications_added_yet' => __('vendor.no_specifications_added_yet'),
        'vendor.add_specifications_detailed_info' => __('vendor.add_specifications_detailed_info'),
        'vendor.try_again' => __('vendor.try_again'),
        'vendor.view_products' => __('vendor.view_products'),
        'vendor.product_created_available_inventory' => __('vendor.product_created_available_inventory'),
        'enter_product_name_english' => __('vendor.enter_product_name_english'),
        'enter_product_name_arabic' => __('vendor.enter_product_name_arabic'),
        'enter_product_description_english' => __('vendor.enter_product_description_english'),
        'enter_product_description_arabic' => __('vendor.enter_product_description_arabic'),
        
    ]) !!};

    // Also set up appTranslations for backward compatibility
    window.appTranslations = window.Laravel.translations;
</script>

<div id="vendor-product-create-app"
     class="vue-app-container"
     data-back-url="{{ route('vendor.products.index') }}"
     data-create-data-url="{{ route('vendor.products.create.data') }}"
     data-store-url="{{ route('vendor.products.store') }}"
     data-session-store-url="{{ route('vendor.products.session.store') }}"
     data-session-get-url="{{ route('vendor.products.session.get') }}"
     data-session-clear-url="{{ route('vendor.products.session.clear') }}"
     data-user-role="{{ auth()->user()->role ?? 'vendor' }}">
</div>
@endsection

@push('scripts')
<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- Vue App Script -->
@vite(['resources/js/vendor-product-create.js'])
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
</script>
@endsection
