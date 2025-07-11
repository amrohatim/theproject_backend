@extends('layouts.merchant')

@section('title', 'Products')
@section('header', 'Products')

@section('content')
<!-- Page Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center space-x-3 mb-2">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"></path>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-900">My Products</h2>
            </div>
            <p class="text-gray-600">Manage your product inventory and listings</p>
        </div>
        <div class="mt-4 sm:mt-0">
            @php
                $merchant = Auth::user()->merchantRecord;
                $canAddProducts = $merchant && $merchant->hasValidLicense();
            @endphp

            @if($canAddProducts)
                <a href="{{ route('merchant.products.create') }}" class="discord-btn">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add New Product
                </a>
            @else
                <button class="discord-btn" disabled style="opacity: 0.6; cursor: not-allowed;"
                        title="License required to add products">
                    <i class="fas fa-lock me-1"></i> Add New Product
                </button>
            @endif
        </div>
    </div>
</div>

@if(!$canAddProducts)
<!-- License Warning Banner -->
<div class="discord-card mt-3" style="border-left: 4px solid var(--discord-yellow);">
    <div class="discord-card-body">
        <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-triangle me-3" style="color: var(--discord-yellow); font-size: 24px;"></i>
            <div>
                <h5 style="margin: 0; color: var(--discord-lightest); font-weight: 600;">
                    License Required
                </h5>
                <p style="margin: 4px 0 0 0; color: var(--discord-light); font-size: 14px;">
                    @if($merchant)
                        @switch($merchant->license_status)
                            @case('checking')
                                Your license is currently under review. You'll be able to add products once it's approved.
                                @break
                            @case('expired')
                                Your license has expired. Please upload a new license to continue adding products.
                                @break
                            @case('rejected')
                                Your license was rejected. Please upload a new license to continue adding products.
                                @break
                            @default
                                Your license is outdated. Please upgrade your license to add products or services.
                        @endswitch
                    @else
                        Please complete your merchant profile setup.
                    @endif
                </p>
            </div>
            <div class="ms-auto">
                <a href="{{ route('merchant.settings.global') }}" class="discord-btn-secondary p-1 rounded-md">
                    <i class="fas fa-upload me-1"></i> Upload License
                </a>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Search and Filters -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0 sm:space-x-4">
        <div class="flex-1 max-w-lg">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input
                    type="text"
                    placeholder="Search products by name, SKU, description..."
                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    value="{{ request('search') }}"
                    id="productSearch"
                >
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <button class="inline-flex items-center px-4 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors" id="filterToggle">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"></path>
                </svg>
                Filters
            </button>
            <select class="px-4 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" id="sortSelect">
                <option>Sort by: Name</option>
                <option>Sort by: Price</option>
                <option>Sort by: Stock</option>
                <option>Sort by: Status</option>
            </select>
        </div>
    </div>
</div>

<!-- Products List -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Products List</h3>
            <span class="text-sm text-gray-500">{{ $products->total() }} products found</span>
        </div>
    </div>

    {{-- Loading indicator --}}
    <div class="products-loading" style="display: none; text-align: center; padding: 20px;">
        <i class="fas fa-spinner fa-spin" style="color: var(--primary-blue); font-size: 24px;"></i>
        <p style="color: var(--gray-500); margin-top: 8px;">Loading products...</p>
    </div>

    {{-- Products Table Container --}}
    <div class="products-table-container">
        @include('merchant.products.partials.products-table', ['products' => $products])
    </div>

    {{-- Pagination Container --}}
    <div class="products-pagination-container">
        @include('merchant.products.partials.pagination', ['products' => $products])
    </div>
</div>

<!-- Quick Stats -->
@if($products->count() > 0)
<div class="row mt-4">
    <div class="col-md-3">
        <div class="discord-card">
            <div class="discord-card-body" style="text-align: center;">
                <div style="font-size: 24px; font-weight: 700; color: var(--discord-primary); margin-bottom: 8px;">
                    {{ $products->where('is_available', true)->count() }}
                </div>
                <div style="color: var(--discord-light); font-size: 14px;">Active Products</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="discord-card">
            <div class="discord-card-body" style="text-align: center;">
                <div style="font-size: 24px; font-weight: 700; color: var(--discord-yellow); margin-bottom: 8px;">
                    {{ $products->where('is_available', false)->count() }}
                </div>
                <div style="color: var(--discord-light); font-size: 14px;">Inactive Products</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="discord-card">
            <div class="discord-card-body" style="text-align: center;">
                <div style="font-size: 24px; font-weight: 700; color: var(--discord-green); margin-bottom: 8px;">
                    ${{ number_format($products->avg('price'), 2) }}
                </div>
                <div style="color: var(--discord-light); font-size: 14px;">Average Price</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="discord-card">
            <div class="discord-card-body" style="text-align: center;">
                <div style="font-size: 24px; font-weight: 700; color: var(--discord-lightest); margin-bottom: 8px;">
                    {{ $products->whereNotNull('stock')->sum('stock') }}
                </div>
                <div style="color: var(--discord-light); font-size: 14px;">Total Stock</div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script src="{{ asset('js/merchant-product-filters.js') }}"></script>
<script>
// Modern search and filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('productSearch');
    const filterToggle = document.getElementById('filterToggle');
    const sortSelect = document.getElementById('sortSelect');

    // Search functionality
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                performSearch();
            }, 300);
        });
    }

    // Sort functionality
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            performSearch();
        });
    }

    // Filter toggle (placeholder for future filter panel)
    if (filterToggle) {
        filterToggle.addEventListener('click', function() {
            // TODO: Implement filter panel toggle
            console.log('Filter toggle clicked');
        });
    }

    function performSearch() {
        const searchQuery = searchInput ? searchInput.value : '';
        const sortBy = sortSelect ? sortSelect.value : '';

        // Build URL parameters
        const params = new URLSearchParams();
        if (searchQuery) params.set('search', searchQuery);
        if (sortBy && sortBy !== 'Sort by: Name') {
            const sortValue = sortBy.replace('Sort by: ', '').toLowerCase();
            params.set('sort', sortValue);
        }

        // Update URL and reload page (for now)
        const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
        window.location.href = newUrl;
    }
});

// Clear search and filters function
function clearSearchAndFilters() {
    window.location.href = window.location.pathname;
}
</script>
@endsection
