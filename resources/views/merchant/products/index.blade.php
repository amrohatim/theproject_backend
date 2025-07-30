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
                <h2 class="text-3xl font-bold text-gray-900">{{ __('merchant.my_products') }}</h2>
            </div>
            <p class="text-gray-600">{{ __('merchant.manage_product_inventory') }}</p>
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
                    {{ __('merchant.add_new_product') }}
                </a>
            @else
                <button class="discord-btn" disabled style="opacity: 0.6; cursor: not-allowed;"
                        title="{{ __('merchant.license_required_to_add_products') }}">
                    <i class="fas fa-lock me-1"></i> {{ __('merchant.add_new_product') }}
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
                    {{ __('merchant.license_required') }}
                </h5>
                <p style="margin: 4px 0 0 0; color: var(--discord-light); font-size: 14px;">
                    @if($merchant)
                        @switch($merchant->license_status)
                            @case('checking')
                                {{ __('merchant.license_under_review_products') }}
                                @break
                            @case('expired')
                                {{ __('merchant.license_expired_products') }}
                                @break
                            @case('rejected')
                                {{ __('merchant.license_rejected_products') }}
                                @break
                            @default
                                {{ __('merchant.license_outdated_products') }}
                        @endswitch
                    @else
                        {{ __('merchant.complete_merchant_profile') }}
                    @endif
                </p>
            </div>
            <div class="ms-auto">
                <a href="{{ route('merchant.license.upload') }}" class="discord-btn-secondary p-1 rounded-md">
                    <i class="fas fa-upload me-1"></i> {{ __('merchant.upload_license') }}
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
                <svg class="absolute left-3 top-1/2 mt-2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input
                    type="text"
                    placeholder="{{ __('merchant.search_products_placeholder') }}"
                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    value="{{ request('search') }}"
                    id="productSearch"
                >
            </div>
        </div>
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center space-y-2 sm:space-y-0 sm:space-x-3">
            <button class="inline-flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors" id="filterToggle">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"></path>
                </svg>
                {{ __('merchant.filters') }}
                <span id="activeFiltersCount" class="ml-2 px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full hidden">0</span>
            </button>

        </div>
    </div>

    <!-- Advanced Filters Panel -->
    <div id="filtersPanel" class="mt-4 bg-gray-50 border-t border-gray-200 p-6 hidden">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">{{ __('merchant.advanced_filters') }}</h3>
            <button id="clearAllFilters" class="text-sm text-red-600 hover:text-red-800">
                {{ __('merchant.clear_all_filters') }}
            </button>
        </div>

        <form id="filtersForm" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            <!-- Category Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('merchant.filter_by_category') }}</label>
                <select  name="category" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" style="text-align: left;  direction: ltr;">
                    <option value="">{{ __('merchant.all_categories') }}</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('merchant.filter_by_status') }}</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"style="text-align: left;  direction: ltr;">
                    <option value="">{{ __('merchant.all_statuses') }}</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('merchant.active') }}</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>{{ __('merchant.inactive') }}</option>
                </select>
            </div>

            <!-- Stock Level Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('merchant.stock_level') }}</label>
                <select name="stock_level" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"style="text-align: left;  direction: ltr;">
                    <option value="">{{ __('merchant.all_stock_levels') }}</option>
                    <option value="in_stock" {{ request('stock_level') == 'in_stock' ? 'selected' : '' }}>{{ __('merchant.in_stock') }}</option>
                    <option value="low_stock" {{ request('stock_level') == 'low_stock' ? 'selected' : '' }}>{{ __('merchant.low_stock') }}</option>
                    <option value="out_of_stock" {{ request('stock_level') == 'out_of_stock' ? 'selected' : '' }}>{{ __('merchant.out_of_stock') }}</option>
                </select>
            </div>

            <!-- Sort Options -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('merchant.sort_by') }}</label>
                <div class="flex space-x-2 gap-3">
                    <select name="sort" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"style="text-align: left;  direction: ltr;">
                        <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>{{ __('merchant.sort_by_date') }}</option>
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>{{ __('merchant.sort_by_name') }}</option>
                        <option value="category" {{ request('sort') == 'category' ? 'selected' : '' }}>{{ __('merchant.sort_by_category') }}</option>
                        <option value="price" {{ request('sort') == 'price' ? 'selected' : '' }}>{{ __('merchant.sort_by_price') }}</option>
                        <option value="stock" {{ request('sort') == 'stock' ? 'selected' : '' }}>{{ __('merchant.sort_by_stock') }}</option>
                        <option value="status" {{ request('sort') == 'status' ? 'selected' : '' }}>{{ __('merchant.sort_by_status') }}</option>
                    </select>
                    <select name="direction" class="px-7 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>{{ __('merchant.sort_descending') }}</option>
                        <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>{{ __('merchant.sort_ascending') }}</option>
                    </select>
                </div>
            </div>

            <!-- Price Range -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('merchant.price_range') }}</label>
                <div class="flex space-x-2">
                    <input type="number" name="price_min" placeholder="{{ __('merchant.min_price') }}"
                           value="{{ request('price_min') }}"
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <input type="number" name="price_max" placeholder="{{ __('merchant.max_price') }}"
                           value="{{ request('price_max') }}"
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <!-- Date Range -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('merchant.date_range') }}</label>
                <div class="flex space-x-2">
                    <input type="date" name="date_from"
                           value="{{ request('date_from') }}"
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <input type="date" name="date_to"
                           value="{{ request('date_to') }}"
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
        </form>
    </div>

    <!-- Active Filters Display -->
    <div id="activeFilters" class="mt-4 hidden">
        <div class="flex items-center space-x-2">
            <span class="text-sm font-medium text-gray-700">{{ __('merchant.active_filters') }}:</span>
            <div id="activeFiltersList" class="flex flex-wrap gap-2"></div>
        </div>
    </div>
</div>

<!-- Bulk Actions -->
<div id="bulkActions" class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4 hidden">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <span class="text-sm font-medium text-blue-900">
                <span id="selectedCount">0</span> {{ __('merchant.items_selected') }}
            </span>
            <div class="flex items-center space-x-2">
                <button id="bulkActivate" class="px-3 py-1 text-xs font-medium text-green-700 bg-green-100 rounded-full hover:bg-green-200">
                    {{ __('merchant.activate_selected') }}
                </button>
                <button id="bulkDeactivate" class="px-3 py-1 text-xs font-medium text-yellow-700 bg-yellow-100 rounded-full hover:bg-yellow-200">
                    {{ __('merchant.deactivate_selected') }}
                </button>
                <button id="bulkDelete" class="px-3 py-1 text-xs font-medium text-red-700 bg-red-100 rounded-full hover:bg-red-200">
                    {{ __('merchant.delete_selected') }}
                </button>
            </div>
        </div>
        <button id="clearSelection" class="text-sm text-gray-500 hover:text-gray-700">
            {{ __('merchant.clear_filters') }}
        </button>
    </div>
</div>

<!-- Products List -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">{{ __('merchant.products_list') }}</h3>
            <span class="text-sm text-gray-500">{{ $products->total() }} {{ __('merchant.products_found') }}</span>
        </div>
    </div>

    {{-- Loading indicator --}}
    <div class="products-loading" style="display: none; text-align: center; padding: 20px;">
        <i class="fas fa-spinner fa-spin" style="color: var(--primary-blue); font-size: 24px;"></i>
        <p style="color: var(--gray-500); margin-top: 8px;">{{ __('merchant.loading_products') }}</p>
    </div>

    {{-- Products Table Container --}}
    <div id="productsTableContainer" class="products-table-container">
        @include('merchant.products.partials.products-table', ['products' => $products])
    </div>

    {{-- Pagination Container --}}
    <div id="paginationContainer" class="products-pagination-container">
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
                <div style="color: var(--discord-light); font-size: 14px;">{{ __('merchant.active_products') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="discord-card">
            <div class="discord-card-body" style="text-align: center;">
                <div style="font-size: 24px; font-weight: 700; color: var(--discord-yellow); margin-bottom: 8px;">
                    {{ $products->where('is_available', false)->count() }}
                </div>
                <div style="color: var(--discord-light); font-size: 14px;">{{ __('merchant.inactive_products') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="discord-card">
            <div class="discord-card-body" style="text-align: center;">
                <div style="font-size: 24px; font-weight: 700; color: var(--discord-green); margin-bottom: 8px;">
                    ${{ number_format($products->avg('price'), 2) }}
                </div>
                <div style="color: var(--discord-light); font-size: 14px;">{{ __('merchant.average_price') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="discord-card">
            <div class="discord-card-body" style="text-align: center;">
                <div style="font-size: 24px; font-weight: 700; color: var(--discord-lightest); margin-bottom: 8px;">
                    {{ $products->whereNotNull('stock')->sum('stock') }}
                </div>
                <div style="color: var(--discord-light); font-size: 14px;">{{ __('merchant.total_stock') }}</div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script src="{{ asset('js/merchant/products-advanced-filters.js') }}"></script>
@endsection
