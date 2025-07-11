{{-- Products Pagination Partial for AJAX responses --}}
@if($products->hasPages())
<div class="px-6 py-4 border-t border-gray-200">
    <div class="flex items-center justify-between">
        <div class="text-sm text-gray-700">
            Showing <span class="font-medium">{{ $products->firstItem() }}</span> to <span class="font-medium">{{ $products->lastItem() }}</span> of <span class="font-medium">{{ $products->total() }}</span> results
        </div>
        <div class="flex items-center space-x-2">
            {{-- Previous Page Link --}}
            @if ($products->onFirstPage())
                <button class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    Previous
                </button>
            @else
                <button class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50" data-page="{{ $products->currentPage() - 1 }}">
                    Previous
                </button>
            @endif

            {{-- Current Page --}}
            <button class="px-3 py-2 text-sm font-medium text-white bg-blue-600 border border-blue-600 rounded-lg">
                {{ $products->currentPage() }}
            </button>

            {{-- Next Page Link --}}
            @if ($products->hasMorePages())
                <button class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50" data-page="{{ $products->currentPage() + 1 }}">
                    Next
                </button>
            @else
                <button class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    Next
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
        if (typeof updateProductsList === 'function') {
            updateProductsList();
        }
    });
});
</script>
@endif
