{{-- Products Pagination Partial for Products Manager --}}
@if($products->hasPages())
<div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
    <div class="flex items-center justify-between">
        <div class="text-sm text-gray-700 dark:text-gray-300">
            {{ __('products_manager.showing') }} <span class="font-medium">{{ $products->firstItem() }}</span> {{ __('products_manager.to') }} <span class="font-medium">{{ $products->lastItem() }}</span> {{ __('products_manager.of') }} <span class="font-medium">{{ $products->total() }}</span> {{ __('products_manager.results') }}
        </div>
        <div class="flex items-center space-x-2">
            {{-- Previous Page Link --}}
            @if ($products->onFirstPage())
                <button class="px-3 py-2 text-sm font-medium text-gray-500 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    {{ __('products_manager.previous') }}
                </button>
            @else
                <button class="px-3 py-2 text-sm font-medium text-gray-500 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700" data-page="{{ $products->currentPage() - 1 }}">
                    {{ __('products_manager.previous') }}
                </button>
            @endif

            {{-- Current Page --}}
            <button class="px-3 py-2 text-sm font-medium text-white bg-orange-600 border border-orange-600 rounded-lg">
                {{ $products->currentPage() }}
            </button>

            {{-- Next Page Link --}}
            @if ($products->hasMorePages())
                <button class="px-3 py-2 text-sm font-medium text-gray-500 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700" data-page="{{ $products->currentPage() + 1 }}">
                    {{ __('products_manager.next') }}
                </button>
            @else
                <button class="px-3 py-2 text-sm font-medium text-gray-500 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    {{ __('products_manager.next') }}
                </button>
            @endif
        </div>
    </div>
</div>

<script>
// Handle pagination clicks
document.querySelectorAll('button[data-page]').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        const page = this.getAttribute('data-page');

        // Get current search and filter parameters
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('page', page);

        // Update URL without page reload
        const newUrl = window.location.pathname + '?' + urlParams.toString();
        window.history.pushState({}, '', newUrl);

        // Trigger search/filter update
        if (window.productsManagerAjaxNav) {
            window.productsManagerAjaxNav.loadContent(newUrl, true);
        } else {
            window.location.href = newUrl;
        }
    });
});
</script>
@endif
