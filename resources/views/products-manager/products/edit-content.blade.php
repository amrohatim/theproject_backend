{{-- Products Manager Products Edit Content - For AJAX Loading --}}
<style>
    /* Mobile adjustments for Products Manager edit */
    @media (max-width: 768px) {
        .products-manager-theme {
            padding-left: 0;
            padding-right: 0;
        }
    }
</style>

<div class="products-manager-theme">
    @if(isset($error))
        <!-- Error Message -->
        <div class="text-center py-12">
            <div class="mx-auto h-16 w-16 rounded-full bg-red-100 dark:bg-red-900/20 flex items-center justify-center mb-4">
                <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Access Denied</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-6">{{ $error }}</p>
            <a href="{{ route('products-manager.products.index') }}" 
               class="inline-flex w-full items-center justify-center px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-md transition-colors ajax-nav sm:w-auto">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Products
            </a>
        </div>
    @else
        <!-- Page Header -->
        {{-- <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Edit Product</h1>
                    <p class="text-gray-600 dark:text-gray-400">Update product information and settings</p>
                    @if(isset($product))
                        <div class="mt-2">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Editing:</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $product->name }}</span>
                            @if($product->product_name_arabic)
                                <span class="text-sm text-gray-500 dark:text-gray-400" dir="rtl">({{ $product->product_name_arabic }})</span>
                            @endif
                        </div>
                    @endif
                </div>
                <a href="{{ route('products-manager.products.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md transition-colors ajax-nav">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Products
                </a>
            </div>
        </div> --}}

        <!-- Loading Indicator -->
        <div id="vue-loading-indicator" class="flex items-center justify-center py-12">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 flex items-center space-x-3 shadow-lg">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-orange-500"></div>
                <span class="text-gray-700 dark:text-gray-300">Loading product edit form...</span>
            </div>
        </div>

        <!-- Translation Setup -->
        <script>
        window.Laravel = window.Laravel || {};
        window.Laravel.translations = {!! json_encode([
            // Page headers and main sections
            'vendor.edit_product' => __('vendor.edit_product'),
            'vendor.update_product_information' => __('vendor.update_product_information'),
            'vendor.product_details' => __('vendor.product_details'),
            'vendor.colors_and_images' => __('vendor.colors_and_images'),
            'vendor.specifications' => __('vendor.specifications'),
            'vendor.save_changes' => __('vendor.save_changes'),
            'vendor.back_to_products' => __('vendor.back_to_products'),
            'vendor.loading' => __('vendor.loading'),
            'vendor.pricing_and_inventory' => __('vendor.pricing_and_inventory'),
            'vendor.current_price' => __('vendor.current_price'),
            'vendor.available_for_purchase' => __('vendor.available_for_purchase'),
            'vendor.sale' => __('vendor.sale'),
            'vendor.off' => __('vendor.off'),

            // Stock management
            'vendor.stock_allocation_progress' => __('vendor.stock_allocation_progress'),
            'vendor.units_allocated' => __('vendor.units_allocated'),
            'vendor.total_inventory_allocation_note' => __('vendor.total_inventory_allocation_note'),
            'vendor.display_order_note' => __('vendor.display_order_note'),
            'vendor.unit' => __('vendor.unit'),

            // Basic product information
            'vendor.product_name' => __('vendor.product_name'),
            'vendor.enter_product_name' => __('vendor.enter_product_name'),
            'vendor.enter_product_name_english' => __('vendor.enter_product_name_english'),
            'vendor.enter_product_name_arabic' => __('vendor.enter_product_name_arabic'),
            'vendor.category' => __('vendor.category'),
            'vendor.select_category' => __('vendor.select_category'),
            'vendor.branch' => __('vendor.branch'),
            'vendor.select_branch' => __('vendor.select_branch'),
            'vendor.description' => __('vendor.description'),
            'vendor.enter_description' => __('vendor.enter_description'),
            'vendor.enter_product_description_english' => __('vendor.enter_product_description_english'),
            'vendor.enter_product_description_arabic' => __('vendor.enter_product_description_arabic'),
            'vendor.price' => __('vendor.price'),
            'vendor.enter_price' => __('vendor.enter_price'),
            'vendor.original_price' => __('vendor.original_price'),
            'vendor.enter_original_price' => __('vendor.enter_original_price'),
            'vendor.stock' => __('vendor.stock'),
            'vendor.total_stock' => __('vendor.total_stock'),
            'vendor.enter_stock' => __('vendor.enter_stock'),
            'vendor.display_order' => __('vendor.display_order'),
            'vendor.enter_display_order' => __('vendor.enter_display_order'),
            'vendor.is_available' => __('vendor.is_available'),
            'vendor.product_available' => __('vendor.product_available'),
    

            // Color and variant information
            'vendor.product_colors' => __('vendor.product_colors'),
            'vendor.add_color_variants_with_images' => __('vendor.add_color_variants_with_images'),
            'vendor.add_color' => __('vendor.add_color'),
            'vendor.color_variant' => __('vendor.color_variant'),
            'vendor.default' => __('vendor.default'),
            'vendor.set_as_default' => __('vendor.set_as_default'),
            'vendor.color_name' => __('vendor.color_name'),
            'vendor.select_color' => __('vendor.select_color'),
            'vendor.color_code' => __('vendor.color_code'),
            'vendor.price_adjustment' => __('vendor.price_adjustment'),
            'vendor.color_stock' => __('vendor.color_stock'),
            'vendor.stock' => __('vendor.stock'),
            'vendor.available_for_this_color' => __('vendor.available_for_this_color'),
            'vendor.currently_allocated' => __('vendor.currently_allocated'),
            'vendor.color_image' => __('vendor.color_image'),
            'vendor.image_format_info' => __('vendor.image_format_info'),
            'vendor.default_color' => __('vendor.default_color'),
            'vendor.main_product_image_info' => __('vendor.main_product_image_info'),
            'vendor.upload_image' => __('vendor.upload_image'),
            'vendor.change_image' => __('vendor.change_image'),
            'vendor.remove_image' => __('vendor.remove_image'),
            'vendor.drag_drop_image' => __('vendor.drag_drop_image'),
            'vendor.click_to_upload' => __('vendor.click_to_upload'),
            'vendor.supported_formats' => __('vendor.supported_formats'),
            'vendor.max_file_size' => __('vendor.max_file_size'),

            // Size management
            'vendor.size_management' => __('vendor.size_management'),
            'vendor.manage_sizes_stock_allocation' => __('vendor.manage_sizes_stock_allocation'),
            'vendor.stock_allocation_for_color' => __('vendor.stock_allocation_for_color'),
            'add_new_size' => __('vendor.add_new_size'),
            'vendor.size_category' => __('vendor.size_category'),
            'vendor.allocated' => __('vendor.allocated'),
            'vendor.units' => __('vendor.units'),
            'vendor.unit' => __('vendor.unit'),
            'vendor.available' => __('vendor.available'),
            'select_category' => __('vendor.select_category'),
            'vendor.add_size' => __('vendor.add_size'),
            'size_name' => __('vendor.size_name'),
            'select_size_name' => __('vendor.select_size_name'),
            'vendor.size_value' => __('vendor.size_value'),
            'vendor.size_stock' => __('vendor.size_stock'),
            'vendor.size_price_adjustment' => __('vendor.size_price_adjustment'),
            'vendor.no_sizes_added_yet' => __('vendor.no_sizes_added_yet'),
            'vendor.click_add_size_to_start_managing' => __('vendor.click_add_size_to_start_managing'),
            'units' => __('vendor.units'),
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
            'vendor.product_updated_successfully' => __('vendor.product_updated_successfully'),
            'vendor.continue' => __('vendor.continue'),
            'vendor.exceeds_available' => __('vendor.exceeds_available'),
            'vendor.stock_allocation_exceeds_limit' => __('vendor.stock_allocation_exceeds_limit'),
            'vendor.try_again' => __('vendor.try_again'),
            


            // Specifications
            'vendor.product_specifications' => __('vendor.product_specifications'),
            'vendor.add_detailed_specifications' => __('vendor.add_detailed_specifications'),
            'vendor.add_specifications_detailed_info' => __('vendor.add_specifications_detailed_info'),
            'vendor.add_specification' => __('vendor.add_specification'),
            'vendor.specification_name' => __('vendor.specification_name'),
            'vendor.specification_value' => __('vendor.specification_value'),
            'vendor.no_specifications_added_yet' => __('vendor.no_specifications_added_yet'),
            'vendor.order' => __('vendor.order'),
            'vendor.specification_key' => __('vendor.specification_key'),
            'vendor.specification_value_placeholder' => __('vendor.specification_value_placeholder'),
            'vendor.specification_key_placeholder' => __('vendor.specification_key_placeholder'),
            


            // Actions and buttons
            'vendor.save_product' => __('vendor.save_product'),
            'vendor.cancel' => __('vendor.cancel'),
            'vendor.delete' => __('vendor.delete'),
            'vendor.remove' => __('vendor.remove'),
            'vendor.edit' => __('vendor.edit'),
            'vendor.view' => __('vendor.view'),
            'vendor.duplicate' => __('vendor.duplicate'),
            'vendor.next' => __('vendor.next'),
            'vendor.previous' => __('vendor.previous'),

            // Status and messages
            'vendor.saving' => __('vendor.saving'),
            'vendor.saved' => __('vendor.saved'),
            'vendor.error' => __('vendor.error'),
            'vendor.success' => __('vendor.success'),
            'vendor.warning' => __('vendor.warning'),
            'vendor.confirm' => __('vendor.confirm'),
            'vendor.yes' => __('vendor.yes'),
            'vendor.no' => __('vendor.no'),
            'vendor.ok' => __('vendor.ok'),
             'vendor.product_name_english_required' => __('vendor.product_name_english_required'),
            'vendor.product_name_arabic_required' => __('vendor.product_name_arabic_required'),
            'vendor.stock_must_be_zero_or_greater' => __('vendor.stock_must_be_zero_or_greater'),
            'vendor.color_image_required' => __('vendor.color_image_required'),
            'vendor.color_variants_required' => __('vendor.color_variants_required'),

            // Validation messages
            'vendor.required' => __('vendor.required'),
            'vendor.field_required' => __('vendor.field_required'),
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
        'vendor.product_updated_successfully'=>__('vendor.product_updated_successfully'),
        'vendor.need_create_branch_first'=>__('vendor.need_create_branch_first'),
        'vendor.no_branches_available'=>__('vendor.no_branches_available'),
         'vendor.error_updating_product' => __('vendor.error_updating_product'),
         'vendor.error_creating_product' => __('vendor.error_creating_product'),
         'vendor.branch_selection_required' => __('vendor.branch_selection_required'),
         'vendor.price_must_be_greater_than_zero' => __('vendor.price_must_be_greater_than_zero'),



            // Legacy keys for backward compatibility
            'enter_product_name_english' => __('vendor.enter_product_name_english'),
            'enter_product_name_arabic' => __('vendor.enter_product_name_arabic'),
            'enter_product_description_english' => __('vendor.enter_product_description_english'),
            'enter_product_description_arabic' => __('vendor.enter_product_description_arabic'),
        ]) !!};

        // Also set up appTranslations for backward compatibility
        window.appTranslations = window.Laravel.translations;
        </script>

        <!-- Vue App Container -->
        <div id="vendor-product-edit-app"
             class="vue-app-container hidden"
             data-back-url="{{ route('products-manager.products.index') }}"
             data-product-id="{{ $product->id ?? '' }}"
             data-edit-data-url="{{ isset($product) ? route('products-manager.products.edit.data', $product) : '' }}"
             data-update-url="{{ isset($product) ? route('products-manager.products.update', $product) : '' }}"
             data-session-store-url="{{ route('vendor.products.session.store') }}"
             data-session-get-url="{{ route('vendor.products.session.get') }}"
             data-session-clear-url="{{ route('vendor.products.session.clear') }}">
        </div>

        <!-- Fallback Content (if Vue fails to load) -->
        <div id="fallback-content" class="hidden">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6">
                <div class="text-center py-8">
                    <div class="mx-auto h-16 w-16 rounded-full bg-red-100 dark:bg-red-900/20 flex items-center justify-center mb-4">
                        <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Failed to Load Form</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6">The product edit form failed to load. Please try refreshing the page.</p>
                    <button onclick="window.location.reload()" 
                            class="inline-flex items-center px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-md transition-colors">
                        <i class="fas fa-refresh mr-2"></i>
                        Refresh Page
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

@if(!isset($error))
<!-- Load Vite Assets for Vue App -->
@vite(['resources/js/vendor-product-edit.js'])

<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- Vue App Initialization Script -->
<script>
// Enhanced initialization for AJAX context with DOM stability
(function() {
    const loadingIndicator = document.getElementById('vue-loading-indicator');
    const vueContainer = document.getElementById('vendor-product-edit-app');
    const fallbackContent = document.getElementById('fallback-content');

    // Function to safely prepare container
    function prepareVueContainer() {
        if (!vueContainer) return false;

        // Ensure container is properly positioned in DOM
        const rect = vueContainer.getBoundingClientRect();
        if (rect.width === 0 || rect.height === 0) {
            console.warn('⚠️ Vue container has zero dimensions, adjusting...');
            vueContainer.style.minHeight = '100px';
            vueContainer.style.display = 'block';
        }

        // Stop any ongoing DOM mutations that might interfere
        const observer = new MutationObserver(() => {});
        observer.observe(vueContainer, { childList: true, subtree: true });

        // Clear any pending DOM operations
        setTimeout(() => {
            observer.disconnect();
        }, 100);

        return true;
    }

    // Show the Vue container with proper preparation
    if (loadingIndicator) loadingIndicator.classList.add('hidden');
    if (vueContainer) {
        vueContainer.classList.remove('hidden');
        prepareVueContainer();
    }

    console.log('Vue app container ready for vendor product edit');

    // Fallback timeout in case Vue fails to mount
    setTimeout(function() {
        // Check if Vue app has mounted by looking for Vue-specific attributes
        const vueElements = vueContainer ? vueContainer.querySelectorAll('[data-v-]') : [];
        const hasVueContent = vueContainer && (vueContainer.children.length > 0 || vueElements.length > 0);

        if (!hasVueContent) {
            console.warn('Vue app may not have loaded properly, showing fallback');
            if (vueContainer) vueContainer.classList.add('hidden');
            if (fallbackContent) fallbackContent.classList.remove('hidden');
        }
    }, 10000); // 10 second timeout
})();

// Override form submission to handle Products Manager context
document.addEventListener('submit', function(e) {
    const form = e.target;
    if (form && form.action && (form.action.includes('/vendor/products') || form.action.includes('/products-manager/products'))) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const method = form.method || 'POST';
        
        // Convert products-manager URLs to vendor URLs for the actual request
        let actionUrl = form.action;
        if (actionUrl.includes('/products-manager/products')) {
            actionUrl = actionUrl.replace('/products-manager/products', '/vendor/products');
        }
        
        fetch(actionUrl, {
            method: method,
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Products-Manager-Context': 'true'
            }
        })
        .then(response => {
            if (response.redirected) {
                // Handle redirect - convert vendor URL to products-manager URL
                let redirectUrl = response.url;
                if (redirectUrl.includes('/vendor/products')) {
                    redirectUrl = redirectUrl.replace('/vendor/products', '/products-manager/products');
                }
                
                // Use global AJAX navigation if available
                if (window.productsManagerAjaxNav) {
                    window.productsManagerAjaxNav.loadContent(redirectUrl, true);
                } else {
                    window.location.href = redirectUrl;
                }
            } else if (response.ok) {
                return response.json();
            } else {
                throw new Error('Network response was not ok');
            }
        })
        .then(data => {
            if (data && data.success) {
                // Success - redirect to products list
                const redirectUrl = data.redirect ? data.redirect.replace('/vendor/products', '/products-manager/products') : '{{ route("products-manager.products.index") }}';
                
                if (window.productsManagerAjaxNav) {
                    window.productsManagerAjaxNav.loadContent(redirectUrl, true);
                } else {
                    window.location.href = redirectUrl;
                }
            } else if (data && data.message) {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Form submission error:', error);
            alert('Failed to update product. Please try again.');
        });
    }
});
</script>
@endif
