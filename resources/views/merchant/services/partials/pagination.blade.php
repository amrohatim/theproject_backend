{{-- Services Pagination Partial for AJAX responses --}}
@if($services->hasPages())
<div class="discord-card-body">
    <div class="d-flex justify-content-between align-items-center">
        <div style="color: var(--discord-light); font-size: 14px;">
            {{ __('merchant.showing') }} {{ $services->firstItem() }} {{ __('merchant.to') }} {{ $services->lastItem() }} {{ __('merchant.of') }} {{ $services->total() }} {{ __('merchant.results') }}
        </div>
        <div>
            {{-- Custom pagination for AJAX --}}
            <nav aria-label="Services pagination">
                <ul class="pagination pagination-sm mb-0">
                    {{-- Previous Page Link --}}
                    @if ($services->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link" style="background-color: var(--discord-darkest); border-color: var(--discord-darkest); color: var(--discord-light);">
                                <i class="fas fa-chevron-left"></i>
                            </span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="#" data-page="{{ $services->currentPage() - 1 }}" style="background-color: var(--discord-darker); border-color: var(--discord-darkest); color: var(--discord-lightest);">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($services->getUrlRange(1, $services->lastPage()) as $page => $url)
                        @if ($page == $services->currentPage())
                            <li class="page-item active">
                                <span class="page-link" style="background-color: var(--discord-primary); border-color: var(--discord-primary); color: white;">
                                    {{ $page }}
                                </span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="#" data-page="{{ $page }}" style="background-color: var(--discord-darker); border-color: var(--discord-darkest); color: var(--discord-lightest);">
                                    {{ $page }}
                                </a>
                            </li>
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($services->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="#" data-page="{{ $services->currentPage() + 1 }}" style="background-color: var(--discord-darker); border-color: var(--discord-darkest); color: var(--discord-lightest);">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link" style="background-color: var(--discord-darkest); border-color: var(--discord-darkest); color: var(--discord-light);">
                                <i class="fas fa-chevron-right"></i>
                            </span>
                        </li>
                    @endif
                </ul>
            </nav>
        </div>
    </div>
</div>

<script>
// Handle pagination clicks
document.querySelectorAll('.page-link[data-page]').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const page = this.getAttribute('data-page');
        
        // Get current search and filter parameters
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('page', page);
        
        // Update URL without page reload
        const newUrl = window.location.pathname + '?' + urlParams.toString();
        window.history.pushState({}, '', newUrl);
        
        // Trigger search/filter update
        if (typeof updateServicesList === 'function') {
            updateServicesList();
        }
    });
});
</script>
@endif
