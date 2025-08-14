{{-- Products Manager Products Edit Content - For AJAX Loading --}}
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
        </div>

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
            // Basic form labels
            'vendor.product_name' => __('vendor.product_name'),
            'vendor.product_name_arabic' => __('vendor.product_name_arabic'),
            'vendor.category' => __('vendor.category'),
            'vendor.branch' => __('vendor.branch'),
            'vendor.price' => __('vendor.price'),
            'vendor.original_price' => __('vendor.original_price'),
            'vendor.stock_quantity' => __('vendor.stock_quantity'),
            'vendor.description' => __('vendor.description'),
            'vendor.product_description_arabic' => __('vendor.product_description_arabic'),
            'vendor.is_available' => __('vendor.is_available'),
            'vendor.display_order' => __('vendor.display_order'),

            // Buttons and actions
            'vendor.update_product' => __('vendor.update_product'),
            'vendor.save_changes' => __('vendor.save_changes'),
            'vendor.cancel' => __('vendor.cancel'),
            'vendor.back_to_products' => __('vendor.back_to_products'),
            'vendor.preview_product' => __('vendor.preview_product'),

            // Messages
            'vendor.product_updated_successfully' => __('vendor.product_updated_successfully'),
            'vendor.error_updating_product' => __('vendor.error_updating_product'),
            'vendor.loading' => __('vendor.loading'),
            'vendor.saving' => __('vendor.saving'),

            // Validation
            'vendor.field_required' => __('vendor.field_required'),
            'vendor.select_category' => __('vendor.select_category'),
            'vendor.select_branch' => __('vendor.select_branch'),

            // Placeholders
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
// Simple initialization for AJAX context
(function() {
    const loadingIndicator = document.getElementById('vue-loading-indicator');
    const vueContainer = document.getElementById('vendor-product-edit-app');
    const fallbackContent = document.getElementById('fallback-content');

    // Show the Vue container immediately since Vite will handle mounting
    if (loadingIndicator) loadingIndicator.classList.add('hidden');
    if (vueContainer) vueContainer.classList.remove('hidden');

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
