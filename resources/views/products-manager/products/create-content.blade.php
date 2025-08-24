{{-- Products Manager Products Create Content - For AJAX Loading --}}
<style>
    /* Orange theme override for Products Manager - Ensure consistent styling */
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

<div class="products-manager-theme">
    @if(isset($needsBranch) && $needsBranch)
        <!-- Need Branch Message -->
        <div class="text-center py-12">
            <div class="mx-auto h-16 w-16 rounded-full bg-orange-100 dark:bg-orange-900/20 flex items-center justify-center mb-4">
                <i class="fas fa-store text-orange-500 text-2xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Create a Branch First</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-6">{{ $message ?? 'You need to create a branch before adding products.' }}</p>
            <a href="{{ route('vendor.branches.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-md transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Create Branch
            </a>
        </div>
    @elseif(isset($needsCategories) && $needsCategories)
        <!-- Need Categories Message -->
        <div class="text-center py-12">
            <div class="mx-auto h-16 w-16 rounded-full bg-orange-100 dark:bg-orange-900/20 flex items-center justify-center mb-4">
                <i class="fas fa-tags text-orange-500 text-2xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No Categories Available</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-6">{{ $message ?? 'No product categories found. Please contact the administrator.' }}</p>
            <a href="{{ route('products-manager.products.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-md transition-colors ajax-nav">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Products
            </a>
        </div>
    @else
        <!-- Page Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Create Product</h1>
                    <p class="text-gray-600 dark:text-gray-400">Add a new product to your inventory</p>
                </div>
                <a href="{{ route('products-manager.products.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md transition-colors ajax-nav">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Products
                </a>
            </div>
        </div>

        <!-- Loading Indicator -->
        <div id="vue-loading-indicator" class="flex items-center justify-center py-12">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 flex items-center space-x-3 shadow-lg">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-orange-500"></div>
                <span class="text-gray-700 dark:text-gray-300">Loading product creation form...</span>
            </div>
        </div>

        <!-- Translation Setup - Comprehensive Setup for AJAX Loading -->
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
            'enter_product_name_english' => __('vendor.enter_product_name_english'),
            'enter_product_name_arabic' => __('vendor.enter_product_name_arabic'),
            'enter_product_description_english' => __('vendor.enter_product_description_english'),
            'enter_product_description_arabic' => __('vendor.enter_product_description_arabic'),

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
            'vendor.size_management' => __('vendor.size_management'),
            'vendor.manage_sizes_stock_allocation' => __('vendor.manage_sizes_stock_allocation'),
            'vendor.stock_allocation_for_color' => __('vendor.stock_allocation_for_color'),
            'vendor.allocated' => __('vendor.allocated'),
            'vendor.units' => __('vendor.units'),
            'vendor.unit' => __('vendor.unit'),
            'vendor.available' => __('vendor.available'),
            'vendor.allocated_stock' => __('vendor.allocated_stock'),
            'vendor.add_size' => __('vendor.add_size'),
            'size_name' => __('vendor.size_name'),
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
            'vendor.remaining_stock' => __('vendor.remaining_stock'),
            'vendor.stock_allocation_progress' => __('vendor.stock_allocation_progress'),
            'vendor.size_name' => __('vendor.size_name'),
            'vendor.all_stock_allocated' => __('vendor.all_stock_allocated'),
              'vendor.product_updated_successfully' => __('vendor.product_updated_successfully'),
            'vendor.continue' => __('vendor.continue'),
            'vendor.exceeds_available' => __('vendor.exceeds_available'),
            'vendor.stock_allocation_exceeds_limit' => __('vendor.stock_allocation_exceeds_limit'),
            


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
            'vendor.order' => __('vendor.order'),
            'vendor.specification_key' => __('vendor.specification_key'),
             'vendor.specification_value_placeholder' => __('vendor.specification_value_placeholder'),
             'vendor.specification_key_placeholder' => __('vendor.specification_key_placeholder'),


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


            // Legacy keys for backward compatibility
            'select_category' => __('vendor.select_category'),
            'select_branch' => __('vendor.select_branch'),
            'product_name' => __('vendor.product_name'),
            'product_description' => __('vendor.description'),
            'price' => __('vendor.price'),
            'save_product' => __('vendor.save_product'),
            'cancel' => __('vendor.cancel'),
            'enter_product_name_english' => __('vendor.enter_product_name_english'),
            'enter_product_name_arabic' => __('vendor.enter_product_name_arabic'),
            'enter_product_description_english' => __('vendor.enter_product_description_english'),
            'enter_product_description_arabic' => __('vendor.enter_product_description_arabic'),
        ]) !!};

        // Set up translations immediately and make them persistent
        window.Laravel.translations = translations;
        window.appTranslations = translations;

        // Ensure translations are available for Vue app initialization
        console.log('🌐 Translations set up for Products Manager create-content page (AJAX):', Object.keys(translations).length, 'keys');

        // Make translations globally accessible
        if (typeof window.getTranslation === 'undefined') {
            window.getTranslation = function(key, fallback = key) {
                return window.Laravel.translations[key] || fallback;
            };
        }
        </script>

        <!-- Vue App Container -->
        <div id="vendor-product-create-app"
             class="vue-app-container"
             data-back-url="{{ route('products-manager.products.index') }}"
             data-create-data-url="{{ route('products-manager.products.create.data') }}"
             data-store-url="{{ route('products-manager.products.store') }}"
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
                    <p class="text-gray-500 dark:text-gray-400 mb-6">The product creation form failed to load. Please try refreshing the page.</p>
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

@if(!isset($needsBranch) && !isset($needsCategories))
<!-- Load Vite Assets for Vue App -->
@vite(['resources/js/vendor-product-create.js'])

<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- Vue App Initialization Script -->
<script>
// Enhanced initialization for AJAX context
(function() {
    const loadingIndicator = document.getElementById('vue-loading-indicator');
    const vueContainer = document.getElementById('vendor-product-create-app');
    const fallbackContent = document.getElementById('fallback-content');

    console.log('🚀 Starting Vue app initialization for Products Manager');
    console.log('Vue container found:', !!vueContainer);
    console.log('Vue container classes before:', vueContainer?.className);

    // Show the Vue container immediately and hide loading
    if (loadingIndicator) {
        loadingIndicator.classList.add('hidden');
        console.log('✅ Loading indicator hidden');
    }

    if (vueContainer) {
        // Ensure container is visible and properly styled
        vueContainer.setAttribute('aria-busy', 'true');
        console.log('✅ Vue container prepared');
        console.log('Vue container classes:', vueContainer.className);
    }

    // Global function to manually trigger Vue initialization (for debugging)
    window.debugVueInit = function() {
        console.log('🔧 Manual Vue initialization triggered');
        console.log('Vue container innerHTML:', vueContainer?.innerHTML.substring(0, 200));
        console.log('Vue container children:', vueContainer?.children.length);

        // Try to trigger Vue mounting manually
        if (window.Vue && window.Vue.createApp) {
            console.log('Vue.createApp available, attempting manual mount...');
        } else {
            console.log('Vue.createApp not available globally');
        }
    };

    // Force load function removed - relying on @vite directive for proper asset loading

    // Auto-trigger removed - relying on @vite directive for proper asset loading

    // Fallback timeout with more detailed checking
    setTimeout(function() {
        console.log('🕐 Checking Vue app mount status after 5 seconds...');

        if (!vueContainer) {
            console.error('❌ Vue container not found');
            return;
        }

        const vueElements = vueContainer.querySelectorAll('[data-v-]');
        const hasVueContent = vueContainer.children.length > 0 || vueElements.length > 0;
        const hasVueText = vueContainer.textContent.trim().length > 10;

        console.log('Vue container children:', vueContainer.children.length);
        console.log('Vue elements with data-v-:', vueElements.length);
        console.log('Vue container text length:', vueContainer.textContent.trim().length);
        console.log('Has Vue content:', hasVueContent);
        console.log('Has Vue text:', hasVueText);

        if (!hasVueContent && !hasVueText) {
            console.warn('⚠️ Vue app may not have loaded properly, showing fallback');
            if (vueContainer) vueContainer.classList.add('hidden');
            if (fallbackContent) fallbackContent.classList.remove('hidden');
        } else {
            console.log('✅ Vue app appears to be working');
        }
    }, 5000); // 5 second timeout for initial check

    // Extended fallback timeout
    setTimeout(function() {
        console.log('🕐 Final Vue app check after 15 seconds...');
        const vueElements = vueContainer ? vueContainer.querySelectorAll('[data-v-]') : [];
        const hasVueContent = vueContainer && (vueContainer.children.length > 0 || vueElements.length > 0);

        if (!hasVueContent) {
            console.error('❌ Vue app failed to load after 15 seconds, showing fallback');
            if (vueContainer) vueContainer.classList.add('hidden');
            if (fallbackContent) fallbackContent.classList.remove('hidden');
        }
    }, 15000); // 15 second final timeout
})();

// Override form submission to handle Products Manager context
document.addEventListener('submit', function(e) {
    const form = e.target;
    if (form && form.action && form.action.includes('/vendor/products')) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const method = form.method || 'POST';
        
        fetch(form.action, {
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
            alert('Failed to create product. Please try again.');
        });
    }
});
</script>
@endif
