@extends('layouts.merchant')

@section('title', 'Create Product')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/vue-styles.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/mdbassit/Coloris@latest/dist/coloris.min.css">
<style>
    :root {
        --primary-blue: #3b82f6;
        --primary-blue-hover: #2563eb;
        --primary-blue-light: #eff6ff;
        --gray-50: #f9fafb;
        --gray-100: #f3f4f6;
        --gray-200: #e5e7eb;
        --gray-300: #d1d5db;
        --gray-400: #9ca3af;
        --gray-500: #6b7280;
        --gray-600: #4b5563;
        --gray-700: #374151;
        --gray-800: #1f2937;
        --gray-900: #111827;
        --green-50: #f0fdf4;
        --green-200: #bbf7d0;
        --green-500: #22c55e;
        --green-600: #16a34a;
        --green-700: #15803d;
        --red-500: #ef4444;
        --red-600: #dc2626;
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
        'create_new_product' => __('merchant.create_new_product'),
        'add_new_product_inventory' => __('merchant.add_new_product_inventory'),
        'back_to_products' => __('merchant.back_to_products'),
        'loading' => __('merchant.loading'),
        'loading_product_creation_form' => __('merchant.loading_product_creation_form'),

        // Product Details Section
        'product_details' => __('merchant.product_details'),
        'product_name' => __('merchant.product_name'),
        'category' => __('merchant.category'),
        'select_category' => __('merchant.select_category'),
        'description' => __('merchant.description'),
        'enter_product_description' => __('merchant.enter_product_description'),
        'product_available_sale' => __('merchant.product_available_sale'),

        // Pricing & Stock Section
        'pricing_stock' => __('merchant.pricing_stock'),
        'price' => __('merchant.price'),
        'original_price' => __('merchant.original_price'),
        'total_stock' => __('merchant.total_stock'),

        // Tab Labels
        'basic_info' => __('merchant.basic_info'),
        'colors_images' => __('merchant.colors_images'),
        'specifications' => __('merchant.specifications'),

        // Colors Section
        'product_colors' => __('merchant.product_colors'),
        'add_color_variants_images' => __('merchant.add_color_variants_images'),
        'add_color' => __('merchant.add_color'),
        'no_colors_added_yet' => __('merchant.no_colors_added_yet'),
        'add_color_variants_appealing' => __('merchant.add_color_variants_appealing'),
        'add_first_color' => __('merchant.add_first_color'),
        'stock_allocation_progress' => __('merchant.stock_allocation_progress'),
        'units_allocated' => __('merchant.units_allocated'),
        'stock_over_allocated_adjust' => __('merchant.stock_over_allocated_adjust'),
        'stock_allocation_summary' => __('merchant.stock_allocation_summary'),
        'allocated_stock' => __('merchant.allocated_stock'),
        'remaining_stock' => __('merchant.remaining_stock'),

        // Specifications Section
        'product_specifications' => __('merchant.product_specifications'),
        'add_detailed_specifications' => __('merchant.add_detailed_specifications'),
        'add_specification' => __('merchant.add_specification'),
        'no_specifications_added_yet' => __('merchant.no_specifications_added_yet'),
        'add_specifications_detailed_info' => __('merchant.add_specifications_detailed_info'),
        'add_first_specification' => __('merchant.add_first_specification'),

        // Form Actions
        'cancel' => __('merchant.cancel'),
        'creating' => __('merchant.creating'),
        'create_product' => __('merchant.create_product'),
        'product_created_successfully' => __('merchant.product_created_successfully'),
        'product_created_available_inventory' => __('merchant.product_created_available_inventory'),
        'view_products' => __('merchant.view_products'),
        'create_another' => __('merchant.create_another'),
        'error_creating_product' => __('merchant.error_creating_product'),
        'try_again' => __('merchant.try_again'),

        // Color Variant Card
        'color_variant' => __('merchant.color_variant'),
        'default' => __('merchant.default'),
        'set_as_default' => __('merchant.set_as_default'),
        'color_name' => __('merchant.color_name'),
        'color_code' => __('merchant.color_code'),
        'set_color_name_stock_manage_sizes' => __('merchant.set_color_name_stock_manage_sizes'),

        // Specification Item
        'key' => __('merchant.key'),
        'value' => __('merchant.value'),
        'eg_material' => __('merchant.eg_material'),
        'eg_cotton' => __('merchant.eg_cotton'),
        'order' => __('merchant.order'),

        // Additional Color Variant Card translations
        'select_color' => __('merchant.select_color'),
        'search_colors' => __('merchant.search_colors'),
        'additional_cost_color_variant' => __('merchant.additional_cost_color_variant'),
        'stock' => __('merchant.stock'),
        'units' => __('merchant.units'),
        'in_stock' => __('merchant.in_stock'),
        'out_of_stock' => __('merchant.out_of_stock'),
        'stock_auto_corrected' => __('merchant.stock_auto_corrected'),
        'display_order' => __('merchant.display_order'),
        'lower_numbers_appear_first' => __('merchant.lower_numbers_appear_first'),
        'product_image' => __('merchant.product_image'),
        'change_image' => __('merchant.change_image'),
        'main_image' => __('merchant.main_image'),
        'upload_image' => __('merchant.upload_image'),
        'png_jpg_up_to_2mb' => __('merchant.png_jpg_up_to_2mb'),
        'click_drag_upload' => __('merchant.click_drag_upload'),
        'save_color_variant_first_manage_sizes' => __('merchant.save_color_variant_first_manage_sizes'),

        // Error messages
        'stock_auto_adjusted' => __('merchant.stock_auto_adjusted'),
        'select_valid_image_file' => __('merchant.select_valid_image_file'),
        'file_size_exceeds_limit' => __('merchant.file_size_exceeds_limit'),
        'failed_load_image' => __('merchant.failed_load_image'),
        'color_must_have_name_stock_before_sizes' => __('merchant.color_must_have_name_stock_before_sizes'),
        'failed_save_color' => __('merchant.failed_save_color'),

        // Size Management translations
        'size_management' => __('merchant.size_management'),
        'manage_sizes_stock_allocation' => __('merchant.manage_sizes_stock_allocation'),
        'refresh' => __('merchant.refresh'),
        'add_size' => __('merchant.add_size'),
        'no_sizes_added' => __('merchant.no_sizes_added'),
        'add_sizes_inventory_management' => __('merchant.add_sizes_inventory_management'),
        'add_sizes_stock_allocation' => __('merchant.add_sizes_stock_allocation'),
        'add_first_size' => __('merchant.add_first_size'),
        'refresh_sizes_list' => __('merchant.refresh_sizes_list'),
        'error_loading_sizes' => __('merchant.error_loading_sizes'),
        'loading_sizes' => __('merchant.loading_sizes'),
        'edit_size' => __('merchant.edit_size'),
        'remove_size' => __('merchant.remove_size'),
        'size_name' => __('merchant.size_name'),
        'select_size' => __('merchant.select_size'),
        'size_value' => __('merchant.size_value'),
        'auto_filled_selection' => __('merchant.auto_filled_selection'),
        'stock_quantity' => __('merchant.stock_quantity'),
        'price_adjustment' => __('merchant.price_adjustment'),
        'additional_cost_size_variant' => __('merchant.additional_cost_size_variant'),
        'available_for_purchase' => __('merchant.available_for_purchase'),
        'saving' => __('merchant.saving'),
        'save' => __('merchant.save'),
        'add_new_size' => __('merchant.add_new_size'),
        'size_category' => __('merchant.size_category'),
        'clothing_sizes' => __('merchant.clothing_sizes'),
        'shoe_sizes' => __('merchant.shoe_sizes'),
        'hat_sizes' => __('merchant.hat_sizes'),
        'available' => __('merchant.available'),
        'allocated' => __('merchant.allocated'),
        'select_size_first' => __('merchant.select_size_first'),
        'please_click_refresh' => __('merchant.please_click_refresh'),
        'aed' => __('merchant.aed'),
        'product_name_arabic' => __('merchant.product_name_arabic'),
        'product_name_english' => __('merchant.product_name_english'),
        'enter_product_description_english' => __('merchant.enter_product_description_english'),
        'enter_product_description_arabic' => __('merchant.enter_product_description_arabic'),
        'prodcut_created_avaliable_inventory' => __('merchant.prodcut_created_avaliable_inventory'),
        'description_required_when_arabic_provided' => __('merchant.description_required_when_arabic_provided'),
        'description_required_when_english_provided' => __('merchant.description_required_when_english_provided'),

        // Stock Allocation Summary translations
        'stock_allocation_summary' => __('merchant.stock_allocation_summary'),
        'allocated_stock' => __('merchant.allocated_stock'),
        'remaining_stock' => __('merchant.remaining_stock'),
    ]) !!};
</script>

<div id="product-create-app"
     data-back-url="{{ route('merchant.products.index') }}">
    <!-- Loading state while Vue app initializes -->
    <div class="d-flex justify-content-center align-items-center" style="min-height: 50vh;">
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">{{ __('merchant.loading') }}</span>
            </div>
            <p class="mt-3 text-muted">{{ __('merchant.loading_product_creation_form') }}</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@vite(['resources/js/product-create.js'])
<script src="https://cdn.jsdelivr.net/gh/mdbassit/Coloris@latest/dist/coloris.min.js"></script>
<script src="{{ asset('js/color-picker.js') }}"></script>
<script src="{{ asset('js/enhanced-size-selection.js') }}"></script>
<script src="{{ asset('js/enhanced-color-selection.js') }}"></script>
<script src="{{ asset('js/color-specific-size-selection.js') }}"></script>
<script src="{{ asset('js/dynamic-color-size-management.js') }}"></script>
<script src="{{ asset('js/merchant-stock-validation.js') }}"></script>
@endsection
