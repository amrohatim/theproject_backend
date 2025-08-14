{{-- Products Manager Products Create Content - For AJAX Loading --}}
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
            'vendor.save_product' => __('vendor.save_product'),
            'vendor.back_to_products' => __('vendor.back_to_products'),
            'vendor.loading' => __('vendor.loading'),
            'vendor.next' => __('vendor.next'),
            'vendor.previous' => __('vendor.previous'),
            'vendor.enter_basic_details' => __('vendor.enter_basic_details'),

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
            'vendor.stock' => __('vendor.stock'),
            'vendor.total_stock' => __('vendor.total_stock'),
            'vendor.total_stock_quantity_available' => __('vendor.total_stock_quantity_available'),
            'vendor.enter_stock' => __('vendor.enter_stock'),
            'vendor.is_available' => __('vendor.is_available'),
            'vendor.product_available' => __('vendor.product_available'),
            'vendor.product_available_sale' => __('vendor.product_available_sale'),
            'vendor.uncheck_if_not_available' => __('vendor.uncheck_if_not_available'),

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
            'vendor.no_colors_added_yet' => __('vendor.no_colors_added_yet'),
            'vendor.add_color_variants_appealing' => __('vendor.add_color_variants_appealing'),
            'vendor.add_first_color' => __('vendor.add_first_color'),

            // Size management
            'vendor.size_management' => __('vendor.size_management'),
            'vendor.manage_sizes_stock_allocation' => __('vendor.manage_sizes_stock_allocation'),
            'vendor.stock_allocation_for_color' => __('vendor.stock_allocation_for_color'),
            'vendor.allocated' => __('vendor.allocated'),
            'vendor.units' => __('vendor.units'),
            'vendor.unit' => __('vendor.unit'),
            'vendor.available' => __('vendor.available'),
            'vendor.add_size' => __('vendor.add_size'),
            'vendor.size_name' => __('vendor.size_name'),
            'vendor.size_value' => __('vendor.size_value'),
            'vendor.size_stock' => __('vendor.size_stock'),
            'vendor.size_price_adjustment' => __('vendor.size_price_adjustment'),
            'vendor.no_sizes_added_yet' => __('vendor.no_sizes_added_yet'),
            'vendor.click_add_size_to_start_managing' => __('vendor.click_add_size_to_start_managing'),

            // Specifications
            'vendor.product_specifications' => __('vendor.product_specifications'),
            'vendor.add_detailed_specifications' => __('vendor.add_detailed_specifications'),
            'vendor.add_specifications_detailed_info' => __('vendor.add_specifications_detailed_info'),
            'vendor.add_specification' => __('vendor.add_specification'),
            'vendor.specification_name' => __('vendor.specification_name'),
            'vendor.specification_value' => __('vendor.specification_value'),
            'vendor.no_specifications_added_yet' => __('vendor.no_specifications_added_yet'),
            'vendor.add_first_specification' => __('vendor.add_first_specification'),

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
             class="vue-app-container hidden"
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
        vueContainer.classList.remove('hidden');
        vueContainer.setAttribute('aria-busy', 'true');
        console.log('✅ Vue container shown');
        console.log('Vue container classes after:', vueContainer.className);

        // Force show the container by removing all hiding classes
        vueContainer.style.display = 'block';
        vueContainer.style.visibility = 'visible';
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
