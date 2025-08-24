@extends('layouts.merchant')

@section('title', __('merchant.edit_product'))
@section('header', __('merchant.edit_product'))

@section('styles')
<style>
    /* Ensure Vue app container takes full height */
    #product-edit-app {
        min-height: 100vh;
    }
    
    /* Hide any flash messages that might interfere with Vue */
    .alert {
        display: none;
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
        'edit_product' => __('merchant.edit_product'),
        'update_product_info_colors_specs' => __('merchant.update_product_info_colors_specs'),
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
        'pricing_inventory' => __('merchant.pricing_inventory'),
        'price' => __('merchant.price'),
        'original_price' => __('merchant.original_price'),
        'current_price' => __('merchant.current_price'),
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
        'stock_over_allocated_adjust_quantities' => __('merchant.stock_over_allocated_adjust_quantities'),
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
        'saving' => __('merchant.saving'),
        'save_changes' => __('merchant.save_changes'),
        'preview' => __('merchant.preview'),
        'success' => __('merchant.success'),
        'product_updated_successfully' => __('merchant.product_updated_successfully'),
        'continue' => __('merchant.continue'),
        'error' => __('merchant.error'),
        'close' => __('merchant.close'),

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

        // Validation messages for edit
        'product_name_required' => __('merchant.product_name_required'),
        'category_required' => __('merchant.category_required'),
        'price_must_be_greater_than_zero' => __('merchant.price_must_be_greater_than_zero'),
        'stock_must_be_zero_or_greater' => __('merchant.stock_must_be_zero_or_greater'),
        'at_least_one_color_variant_required' => __('merchant.at_least_one_color_variant_required'),
        'at_least_one_color_must_have_image' => __('merchant.at_least_one_color_must_have_image'),

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

<div id="product-edit-app"
     data-product-id="{{ $product->id }}"
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
@vite(['resources/js/product-edit.js'])

<script>
// Ensure axios is available before configuring it
document.addEventListener('DOMContentLoaded', function() {
    // Wait for axios to be available
    const waitForAxios = () => {
        if (window.axios && window.axios.defaults) {
            // Add CSRF token to axios defaults for Vue app
            const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
            if (csrfTokenElement) {
                window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfTokenElement.getAttribute('content');
            } else {
                console.warn('CSRF token meta tag not found');
            }
        } else {
            // Retry after a short delay
            setTimeout(waitForAxios, 50);
        }
    };

    waitForAxios();
});

// Add any global configuration needed for the Vue app
window.productEditConfig = {
    apiBaseUrl: '{{ url("/") }}',
    merchantProductsUrl: '{{ route("merchant.products.index") }}',
    csrfToken: '{{ csrf_token() }}'
};
</script>
@endsection
