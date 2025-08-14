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
            'vendor.save_product' => __('vendor.save_product'),
            'vendor.save_draft' => __('vendor.save_draft'),
            'vendor.next' => __('vendor.next'),
            'vendor.previous' => __('vendor.previous'),
            'vendor.cancel' => __('vendor.cancel'),
            'vendor.back_to_products' => __('vendor.back_to_products'),

            // Messages
            'vendor.product_created_successfully' => __('vendor.product_created_successfully'),
            'vendor.error_creating_product' => __('vendor.error_creating_product'),
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

    // Try to manually load and execute the Vite script if it's not working
    window.forceLoadVueScript = function() {
        console.log('🔧 Force loading Vue script...');
        const existingScript = document.querySelector('script[src*="vendor-product-create"]');
        if (existingScript) {
            console.log('Script exists, trying to reload...');
            existingScript.remove();
        }

        const script = document.createElement('script');
        script.type = 'module';
        script.src = '/build/assets/vendor-product-create-Cj13Vbkr.js';
        script.onload = () => {
            console.log('✅ Vue script force loaded successfully');
            setTimeout(() => {
                const container = document.getElementById('vendor-product-create-app');
                if (container && container.__vue_app__) {
                    console.log('✅ Vue app mounted after force load');
                } else {
                    console.log('❌ Vue app still not mounted after force load');
                }
            }, 1000);
        };
        script.onerror = (e) => console.error('❌ Failed to force load Vue script:', e);
        document.head.appendChild(script);
    };

    // Auto-trigger force load after a short delay
    setTimeout(() => {
        const container = document.getElementById('vendor-product-create-app');
        if (container && !container.__vue_app__) {
            console.log('🔧 Vue app not mounted, trying force load...');
            window.forceLoadVueScript();
        }
    }, 2000);

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
