@extends('layouts.products-manager')

@section('title', 'Add Product')
@section('page-title', 'Add Product')

@section('styles')
<style>
    /* Orange theme override for Products Manager */
    :root {
        --pm-orange: #F46C3F;
        --pm-orange-hover: #e55a2b;
        --pm-orange-light: #fef3f0;
        --pm-orange-dark: #d14d26;
    }

    /* Override blue theme with orange for Products Manager context */
    .products-manager-theme {
        --primary-blue: var(--pm-orange);
        --primary-blue-hover: var(--pm-orange-hover);
        --primary-blue-light: var(--pm-orange-light);
    }

    /* Vue.js specific styles with orange theme */
    .vue-app-container {
        min-height: 100vh;
    }

    .vue-btn-blue-solid {
        background-color: var(--pm-orange) !important;
        border-color: var(--pm-orange) !important;
        color: #ffffff !important;
    }

    .vue-btn-blue-solid:hover {
        background-color: var(--pm-orange-hover) !important;
        border-color: var(--pm-orange-hover) !important;
    }

    .vue-form-control:focus {
        border-color: var(--pm-orange) !important;
        box-shadow: 0 0 0 3px rgba(244, 108, 63, 0.1) !important;
    }

    /* Override primary blue variables in Vue components */
    .vue-card {
        --tw-ring-color: var(--pm-orange) !important;
    }

    .vue-card.ring-2 {
        border-color: var(--pm-orange-light) !important;
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

    /* Vue component base styles with orange theme */
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
        background-color: var(--pm-orange);
        color: #ffffff;
        border-color: var(--pm-orange);
    }

    .vue-btn-primary:hover {
        background-color: var(--pm-orange-hover);
        border-color: var(--pm-orange-hover);
    }

    /* Tab styles with orange theme */
    .vue-tab-active {
        background-color: var(--pm-orange) !important;
        color: #ffffff !important;
        border-color: var(--pm-orange) !important;
    }

    .vue-tab:hover {
        background-color: var(--pm-orange-light) !important;
        color: var(--pm-orange-dark) !important;
    }

    /* Progress bar with orange theme */
    .vue-progress-bar {
        background-color: var(--pm-orange) !important;
    }

    /* Success/error states with orange accents */
    .vue-success {
        border-color: #10b981;
        background-color: #f0fdf4;
    }

    .vue-error {
        border-color: #ef4444;
        background-color: #fef2f2;
    }

    /* Orange theme for color variant cards */
    .color-item .vue-btn-blue-solid {
        background-color: var(--pm-orange) !important;
        border-color: var(--pm-orange) !important;
    }

    .color-item .vue-btn-blue-solid:hover {
        background-color: var(--pm-orange-hover) !important;
        border-color: var(--pm-orange-hover) !important;
    }

    /* Default color badge with orange theme */
    .color-item span[style*="--primary-blue-hover"] {
        color: var(--pm-orange-dark) !important;
    }

    /* Ring colors for default items */
    .color-item[style*="--tw-ring-color"] {
        --tw-ring-color: var(--pm-orange) !important;
    }
</style>
@endsection

@section('content')
<div class="products-manager-theme">
    {{-- Set up translations for Vue app - ensure they're available immediately --}}
    <script>
    // Ensure Laravel object exists
    window.Laravel = window.Laravel || {};

    // Set up translations immediately and make them persistent
    const translations = {!! json_encode([
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
            'vendor.enter_product_name_english' => __('vendor.enter_product_name_english'),
            'vendor.enter_product_name_arabic' => __('vendor.enter_product_name_arabic'),
            'vendor.category' => __('vendor.category'),
            'vendor.select_category' => __('vendor.select_category'),
            'vendor.branch' => __('vendor.branch'),
            'vendor.select_branch' => __('vendor.select_branch'),
            'vendor.branch_automatically_selected' => __('vendor.branch_automatically_selected'),
            'vendor.description' => __('vendor.description'),
            'vendor.enter_description' => __('vendor.enter_description'),
            'vendor.enter_product_description_english' => __('vendor.enter_product_description_english'),
            'vendor.enter_product_description_arabic' => __('vendor.enter_product_description_arabic'),
            'vendor.price' => __('vendor.price'),
            'vendor.enter_price' => __('vendor.enter_price'),
            'vendor.original_price' => __('vendor.original_price'),
            'vendor.enter_original_price' => __('vendor.enter_original_price'),
            'vendor.total_stock' => __('vendor.total_stock'),
            'vendor.enter_stock' => __('vendor.enter_stock'),
            'vendor.total_stock_quantity_available' => __('vendor.total_stock_quantity_available'),
            'vendor.product_available_sale' => __('vendor.product_available_sale'),
            'vendor.uncheck_if_not_available' => __('vendor.uncheck_if_not_available'),
            'vendor.sku' => __('vendor.sku'),
            'vendor.enter_sku' => __('vendor.enter_sku'),

            // Colors and variants
            'vendor.colors' => __('vendor.colors'),
            'vendor.product_colors' => __('vendor.product_colors'),
            'vendor.add_color' => __('vendor.add_color'),
            'vendor.add_first_color' => __('vendor.add_first_color'),
            'vendor.no_colors_added_yet' => __('vendor.no_colors_added_yet'),
            'vendor.add_color_variants_with_images' => __('vendor.add_color_variants_with_images'),
            'vendor.add_color_variants_images' => __('vendor.add_color_variants_images'),
            'vendor.add_color_variants_appealing' => __('vendor.add_color_variants_appealing'),
            'vendor.color_name' => __('vendor.color_name'),
            'vendor.select_color' => __('vendor.select_color'),
            'vendor.search_colors' => __('vendor.search_colors'),
            'vendor.color_code' => __('vendor.color_code'),
            'vendor.price_adjustment' => __('vendor.price_adjustment'),
            'vendor.color_stock' => __('vendor.color_stock'),
            'vendor.stock' => __('vendor.stock'),
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
            'vendor.color_variant' => __('vendor.color_variant'),
            'vendor.default' => __('vendor.default'),
            'vendor.set_as_default' => __('vendor.set_as_default'),
            'vendor.upload_image' => __('vendor.upload_image'),
            'vendor.change_image' => __('vendor.change_image'),
            'vendor.remove_image' => __('vendor.remove_image'),
            'vendor.drag_drop_image' => __('vendor.drag_drop_image'),
            'vendor.click_to_upload' => __('vendor.click_to_upload'),
            'vendor.supported_formats' => __('vendor.supported_formats'),
            'vendor.max_file_size' => __('vendor.max_file_size'),

            // Sizes
            'vendor.sizes' => __('vendor.sizes'),
            'add_new_size' => __('vendor.add_new_size'),
            'vendor.size_category' => __('vendor.size_category'),
            'vendor.size_management' => __('vendor.size_management'),
            'vendor.manage_sizes_stock_allocation' => __('vendor.manage_sizes_stock_allocation'),
            'vendor.stock_allocation_for_color' => __('vendor.stock_allocation_for_color'),
            'vendor.allocated' => __('vendor.allocated'),
            'vendor.units' => __('vendor.units'),
            'units' => __('vendor.units'),
            'vendor.unit' => __('vendor.unit'),
            'vendor.available' => __('vendor.available'),
            'vendor.add_size' => __('vendor.add_size'),
            'size_name' => __('vendor.size_name'),
            'select_size_name' => __('vendor.select_size_name'),
            'vendor.size_value' => __('vendor.size_value'),
            'vendor.size_stock' => __('vendor.size_stock'),
            'vendor.size_price_adjustment' => __('vendor.size_price_adjustment'),
            'vendor.no_sizes_added_yet' => __('vendor.no_sizes_added_yet'),
            'vendor.add_first_size' => __('vendor.add_first_size'),
            'vendor.size_chart' => __('vendor.size_chart'),
            'vendor.standard_sizes' => __('vendor.standard_sizes'),
            'vendor.custom_size' => __('vendor.custom_size'),
            'vendor.enter_custom_size' => __('vendor.enter_custom_size'),
            'vendor.click_add_size_to_start_managing' => __('vendor.click_add_size_to_start_managing'),
            'auto_filled_based_on_size_name' => __('vendor.auto_filled_based_on_size_name'),
            'vendor.aed' => __('vendor.aed'),
            'to_allocate' => __('vendor.to_allocate'),
            'available' => __('vendor.available'),
            'stock_quantity' => __('vendor.stock_quantity'),
            'clothes' => __('vendor.clothes'),
            'shoes' => __('vendor.shoes'),
            'hats' => __('vendor.hats'),
            'vendor.allocated_stock' => __('vendor.allocated_stock'),
            'vendor.remaining_stock' => __('vendor.remaining_stock'),
            'vendor.stock_allocation_progress' => __('vendor.stock_allocation_progress'),
            'vendor.size_name' => __('vendor.size_name'),
            'vendor.all_stock_allocated' => __('vendor.all_stock_allocated'),

            // Specifications
            'vendor.specifications' => __('vendor.specifications'),
            'vendor.product_specifications' => __('vendor.product_specifications'),
            'vendor.add_detailed_specifications' => __('vendor.add_detailed_specifications'),
            'vendor.add_specifications_detailed_info' => __('vendor.add_specifications_detailed_info'),
            'vendor.add_specification' => __('vendor.add_specification'),
            'vendor.specification_name' => __('vendor.specification_name'),
            'vendor.specification_value' => __('vendor.specification_value'),
            'vendor.no_specifications_added_yet' => __('vendor.no_specifications_added_yet'),
            'vendor.add_first_specification' => __('vendor.add_first_specification'),
            'vendor.enter_specification_name' => __('vendor.enter_specification_name'),
            'vendor.enter_specification_value' => __('vendor.enter_specification_value'),
            'vendor.product_updated_successfully' => __('vendor.product_updated_successfully'),
            'vendor.continue' => __('vendor.continue'),
            'vendor.exceeds_available' => __('vendor.exceeds_available'),
            'vendor.stock_allocation_exceeds_limit' => __('vendor.stock_allocation_exceeds_limit'),
            'vendor.order' => __('vendor.order'),
            'vendor.specification_key' => __('vendor.specification_key'),
            'vendor.specification_value_placeholder' => __('vendor.specification_value_placeholder'),
            'vendor.specification_key_placeholder' => __('vendor.specification_key_placeholder'),
            'enter_product_name_english' => __('vendor.enter_product_name_english'),
            'enter_product_name_arabic' => __('vendor.enter_product_name_arabic'),
            'enter_product_description_english' => __('vendor.enter_product_description_english'),
            'enter_product_description_arabic' => __('vendor.enter_product_description_arabic'),


            // Actions and buttons
            'vendor.save_product' => __('vendor.save_product'),
            'vendor.save_changes' => __('vendor.save_changes'),
            'vendor.cancel' => __('vendor.cancel'),
            'vendor.delete' => __('vendor.delete'),
            'vendor.remove' => __('vendor.remove'),
            'vendor.edit' => __('vendor.edit'),
            'vendor.view' => __('vendor.view'),
            'vendor.duplicate' => __('vendor.duplicate'),

            // Status and messages
            'vendor.loading' => __('vendor.loading'),
            'vendor.saving' => __('vendor.saving'),
            'vendor.saved' => __('vendor.saved'),
            'vendor.error' => __('vendor.error'),
            'vendor.success' => __('vendor.success'),
            'vendor.warning' => __('vendor.warning'),
            'vendor.confirm' => __('vendor.confirm'),
            'vendor.yes' => __('vendor.yes'),
            'vendor.no' => __('vendor.no'),
            'vendor.ok' => __('vendor.ok'),

            // Validation messages
            'vendor.required' => __('vendor.required'),
            'vendor.invalid_email' => __('vendor.invalid_email'),
            'vendor.invalid_phone' => __('vendor.invalid_phone'),
            'vendor.min_length' => __('vendor.min_length'),
            'vendor.max_length' => __('vendor.max_length'),
            'vendor.numeric_only' => __('vendor.numeric_only'),
            'vendor.positive_number' => __('vendor.positive_number'),
             'vendor.please_fix_validation_errors' => __('vendor.please_fix_validation_errors'),
        'vendor.product_name_both_languages_required' => __('vendor.product_name_both_languages_required'),
        'vendor.category_selection_required' => __('vendor.category_selection_required'),
        'vendor.product_created_successfully'=>__('vendor.product_created_successfully'),
        'vendor.continue'=>__('vendor.continue'),
        'vendor.view_products'=>__('vendor.view_products'),
        'vendor.product_created_available_inventory'=>__('vendor.product_created_available_inventory'),
        'vendor.need_create_branch_first'=>__('vendor.need_create_branch_first'),
        'vendor.no_branches_available'=>__('vendor.no_branches_available'),


            // Legacy keys for backward compatibility
            'select_category' => __('vendor.select_category'),
            'select_branch' => __('vendor.select_branch'),
            'product_name' => __('vendor.product_name'),
            'product_description' => __('vendor.description'),
            'price' => __('vendor.price'),
            'save_product' => __('vendor.save_product'),
            'cancel' => __('vendor.cancel'),
    ]) !!};

    // Set up translations immediately and make them persistent
    window.Laravel.translations = translations;
    window.appTranslations = translations;

    // Ensure translations are available for Vue app initialization
    console.log('🌐 Translations set up for Products Manager create page:', Object.keys(translations).length, 'keys');

    // Make translations globally accessible
    if (typeof window.getTranslation === 'undefined') {
        window.getTranslation = function(key, fallback = key) {
            return window.Laravel.translations[key] || fallback;
        };
    }
    </script>

    {{-- Vue App Container --}}
    <div id="vendor-product-create-app"
         class="vue-app-container"
         data-back-url="{{ route('products-manager.products.index') }}"
         data-create-data-url="{{ route('products-manager.products.create.data') }}"
         data-store-url="{{ route('products-manager.products.store') }}"
         data-session-store-url="{{ route('vendor.products.session.store') }}"
         data-session-get-url="{{ route('vendor.products.session.get') }}"
         data-session-clear-url="{{ route('vendor.products.session.clear') }}">
    </div>
</div>
@endsection

@section('scripts')
<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- Auto-refresh functionality for Products Manager create page -->
<script>
(function() {
    'use strict';

    // Check if this is the specific URL that needs auto-refresh
    const currentUrl = window.location.href;
    const targetPath = '/products-manager/products/create';

    // Only proceed if we're on the exact target URL
    if (!currentUrl.includes(targetPath)) {
        console.log('🔄 Auto-refresh: Not on target URL, skipping');
        return;
    }

    // Check if page has already been refreshed to prevent infinite loops
    const sessionRefreshKey = 'pm_create_session_refreshed';

    // Use both sessionStorage and a URL parameter to track refresh state
    const urlParams = new URLSearchParams(window.location.search);
    const hasRefreshParam = urlParams.has('auto_refreshed');
    const hasSessionFlag = sessionStorage.getItem(sessionRefreshKey) === 'true';

    if (hasRefreshParam || hasSessionFlag) {
        console.log('🔄 Auto-refresh: Page already refreshed, skipping to prevent loop');
        // Clean up the URL parameter if it exists
        if (hasRefreshParam) {
            urlParams.delete('auto_refreshed');
            const newUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() : '');
            window.history.replaceState({}, '', newUrl);
        }
        return;
    }

    // Check if we came from the products listing page (normal navigation)
    const referrer = document.referrer;
    const isFromProductsListing = referrer.includes('/products-manager/products') && !referrer.includes('/products-manager/products/create');

    // Check navigation type - we want to refresh for normal navigation, not direct access
    const navigationType = performance.getEntriesByType('navigation')[0]?.type;
    const isDirectAccess = navigationType === 'navigate' && !referrer;

    console.log('🔄 Auto-refresh: Navigation analysis', {
        referrer: referrer,
        isFromProductsListing: isFromProductsListing,
        navigationType: navigationType,
        isDirectAccess: isDirectAccess
    });

    // Only refresh if we came from the products listing page (normal navigation)
    // Do NOT refresh for direct URL access
    if (!isFromProductsListing || isDirectAccess) {
        console.log('🔄 Auto-refresh: Skipping - not from products listing page or is direct access');
        return;
    }

    // Set flags to prevent future refreshes
    sessionStorage.setItem(sessionRefreshKey, 'true');

    // Wait for the page to fully load before refreshing
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', performAutoRefresh);
    } else {
        // Page is already loaded
        performAutoRefresh();
    }

    function performAutoRefresh() {
        console.log('🔄 Auto-refresh: Performing automatic page refresh for Products Manager create page (from products listing)');

        // Add a parameter to track that we've refreshed
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.set('auto_refreshed', '1');

        // Perform the refresh
        window.location.href = currentUrl.toString();
    }

    // Clean up session storage when navigating away from the page
    window.addEventListener('beforeunload', function() {
        // Only clear if we're actually navigating away (not refreshing)
        if (!performance.getEntriesByType('navigation')[0] ||
            performance.getEntriesByType('navigation')[0].type !== 'reload') {
            sessionStorage.removeItem(sessionRefreshKey);
        }
    });

    // Also clean up when the page becomes hidden (user switches tabs, etc.)
    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'hidden') {
            // Set a timeout to clear the flag after a reasonable time
            setTimeout(function() {
                if (document.visibilityState === 'hidden') {
                    sessionStorage.removeItem(sessionRefreshKey);
                }
            }, 30000); // 30 seconds
        }
    });
})();
</script>

<!-- Vue App Script -->
@vite(['resources/js/vendor-product-create.js'])
@endsection
