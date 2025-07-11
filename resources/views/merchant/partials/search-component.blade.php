{{-- Enhanced Merchant Search Component --}}
<div class="enhanced-search-container">
    <form class="search-form" role="search">
        <div class="position-relative">
            <input
                type="text"
                class="enhanced-search-input"
                placeholder="{{ $placeholder ?? 'Search products, services, categories...' }}"
                autocomplete="off"
                spellcheck="false"
                aria-label="Search"
                value="{{ request('search') }}"
                data-search-type="{{ $type ?? 'global' }}"
            >
            <i class="fas fa-search enhanced-search-icon"></i>
            <div class="enhanced-search-loading">
                <div class="spinner"></div>
            </div>
            <button type="button" class="enhanced-search-clear" aria-label="Clear search">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- Enhanced Search Suggestions Dropdown --}}
        <div class="enhanced-search-suggestions" role="listbox" aria-label="Search suggestions">
            {{-- Recent Searches Section --}}
            <div class="recent-searches" style="display: none;">
                <div class="recent-searches-title">
                    <i class="fas fa-history"></i>
                    Recent Searches
                </div>
                <div class="recent-searches-list">
                    {{-- Recent searches will be populated by JavaScript --}}
                </div>
            </div>

            {{-- Search Suggestions Section --}}
            <div class="suggestions-list">
                {{-- Suggestions will be populated by JavaScript --}}
            </div>

            {{-- Search Stats --}}
            <div class="search-stats" style="display: none;">
                <span class="search-stats-text"></span>
            </div>
        </div>
    </form>
</div>

{{-- Search Results Container (optional) --}}
@if(isset($showResults) && $showResults)
<div class="search-results" id="searchResults">
    {{-- Search results will be populated by JavaScript --}}
</div>
@endif

{{-- Enhanced Filter Toggle Button (optional) --}}
@if(isset($showFilters) && $showFilters)
<div class="enhanced-filter-container">
    <button type="button" class="enhanced-filter-toggle" id="enhancedFilterToggle">
        <i class="fas fa-filter"></i>
        <span>Filters</span>
        <span class="filter-count" style="display: none;">0</span>
    </button>

    {{-- Enhanced Filter Panel --}}
    <div class="enhanced-filter-panel" id="enhancedFilterPanel">
        {{-- Quick Filters Section --}}
        <div class="filter-section">
            <div class="filter-section-title">
                <i class="fas fa-bolt"></i>
                Quick Filters
            </div>
            <div class="quick-filters-grid" style="display: flex; flex-wrap: wrap; gap: 8px;">
                @if(isset($type) && $type === 'products')
                    <button type="button" class="enhanced-filter-tag" data-filter="status" data-value="active">
                        <i class="fas fa-check-circle"></i>
                        Active
                    </button>
                    <button type="button" class="enhanced-filter-tag" data-filter="featured" data-value="1">
                        <i class="fas fa-star"></i>
                        Featured
                    </button>
                    <button type="button" class="enhanced-filter-tag" data-filter="stock_status" data-value="low_stock">
                        <i class="fas fa-exclamation-triangle"></i>
                        Low Stock
                    </button>
                    <button type="button" class="enhanced-filter-tag" data-filter="stock_status" data-value="in_stock">
                        <i class="fas fa-boxes"></i>
                        In Stock
                    </button>
                @elseif(isset($type) && $type === 'services')
                    <button type="button" class="enhanced-filter-tag" data-filter="status" data-value="active">
                        <i class="fas fa-check-circle"></i>
                        Active
                    </button>
                    <button type="button" class="enhanced-filter-tag" data-filter="featured" data-value="1">
                        <i class="fas fa-star"></i>
                        Featured
                    </button>
                    <button type="button" class="enhanced-filter-tag" data-filter="service_type" data-value="home_service">
                        <i class="fas fa-home"></i>
                        Home Service
                    </button>
                    <button type="button" class="enhanced-filter-tag" data-filter="available" data-value="1">
                        <i class="fas fa-calendar-check"></i>
                        Available
                    </button>
                @else
                    <button type="button" class="enhanced-filter-tag" data-filter="type" data-value="products">
                        <i class="fas fa-box"></i>
                        Products
                    </button>
                    <button type="button" class="enhanced-filter-tag" data-filter="type" data-value="services">
                        <i class="fas fa-concierge-bell"></i>
                        Services
                    </button>
                @endif
            </div>
        </div>

        {{-- Price Range Filter --}}
        @if(isset($type) && $type === 'products')
        <div class="filter-section">
            <div class="filter-section-title">
                <i class="fas fa-dollar-sign"></i>
                Price Range
            </div>
            <div class="enhanced-range-slider" data-filter="price">
                <div class="range-slider-track">
                    <div class="range-slider-fill"></div>
                    <div class="range-slider-thumb" data-thumb="min"></div>
                    <div class="range-slider-thumb" data-thumb="max"></div>
                </div>
                <div class="range-values">
                    <span class="range-value" data-value="min">$0</span>
                    <span class="range-value" data-value="max">$1000</span>
                </div>
            </div>
        </div>
        @endif

        {{-- Category Multi-Select --}}
        <div class="filter-section">
            <div class="filter-section-title">
                <i class="fas fa-tags"></i>
                Categories
            </div>
            <div class="enhanced-multiselect" data-filter="categories">
                <div class="multiselect-trigger">
                    <div class="multiselect-selected">
                        <span class="multiselect-placeholder">Select categories...</span>
                    </div>
                    <i class="fas fa-chevron-down multiselect-arrow"></i>
                </div>
                <div class="multiselect-dropdown">
                    {{-- Categories will be populated by JavaScript --}}
                </div>
            </div>
        </div>

        {{-- Active Filters Display --}}
        <div class="enhanced-filter-tags" id="activeFilterTags">
            {{-- Active filter tags will be populated by JavaScript --}}
        </div>

        {{-- Filter Actions --}}
        <div class="filter-actions">
            <button type="button" class="filter-clear-all" id="clearAllFilters">
                <i class="fas fa-times"></i>
                Clear All
            </button>
            <button type="button" class="filter-apply" id="applyFilters">
                <i class="fas fa-check"></i>
                Apply Filters
            </button>
        </div>
    </div>
</div>
@endif

{{-- Advanced Filter Modal (optional) --}}
@if(isset($showAdvancedFilters) && $showAdvancedFilters)
<div class="modal fade" id="advancedFiltersModal" tabindex="-1" aria-labelledby="advancedFiltersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background-color: var(--discord-darker); border: 1px solid var(--discord-darkest);">
            <div class="modal-header" style="border-bottom: 1px solid var(--discord-darkest);">
                <h5 class="modal-title" id="advancedFiltersModalLabel" style="color: var(--discord-lightest);">
                    <i class="fas fa-filter me-2"></i>Advanced Filters
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1);"></button>
            </div>
            <div class="modal-body">
                <form id="advancedFiltersForm">
                    <div class="row">
                        {{-- Category Filter --}}
                        <div class="col-md-6 mb-3">
                            <label for="categoryFilter" class="form-label" style="color: var(--discord-lightest);">Category</label>
                            <select class="form-select" id="categoryFilter" name="category_id" style="background-color: var(--discord-darkest); border: 1px solid var(--discord-darkest); color: var(--discord-lightest);">
                                <option value="">All Categories</option>
                                {{-- Categories will be populated by JavaScript --}}
                            </select>
                        </div>
                        
                        {{-- Status Filter --}}
                        <div class="col-md-6 mb-3">
                            <label for="statusFilter" class="form-label" style="color: var(--discord-lightest);">Status</label>
                            <select class="form-select" id="statusFilter" name="status" style="background-color: var(--discord-darkest); border: 1px solid var(--discord-darkest); color: var(--discord-lightest);">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        
                        {{-- Price Range --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="color: var(--discord-lightest);">Price Range</label>
                            <div class="row">
                                <div class="col-6">
                                    <input type="number" class="form-control" id="priceMin" name="price_min" placeholder="Min" min="0" step="0.01" style="background-color: var(--discord-darkest); border: 1px solid var(--discord-darkest); color: var(--discord-lightest);">
                                </div>
                                <div class="col-6">
                                    <input type="number" class="form-control" id="priceMax" name="price_max" placeholder="Max" min="0" step="0.01" style="background-color: var(--discord-darkest); border: 1px solid var(--discord-darkest); color: var(--discord-lightest);">
                                </div>
                            </div>
                        </div>
                        
                        {{-- Date Range --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="color: var(--discord-lightest);">Date Range</label>
                            <div class="row">
                                <div class="col-6">
                                    <input type="date" class="form-control" id="dateFrom" name="date_from" style="background-color: var(--discord-darkest); border: 1px solid var(--discord-darkest); color: var(--discord-lightest);">
                                </div>
                                <div class="col-6">
                                    <input type="date" class="form-control" id="dateTo" name="date_to" style="background-color: var(--discord-darkest); border: 1px solid var(--discord-darkest); color: var(--discord-lightest);">
                                </div>
                            </div>
                        </div>
                        
                        @if(isset($type) && $type === 'products')
                        {{-- Stock Status Filter --}}
                        <div class="col-md-6 mb-3">
                            <label for="stockStatusFilter" class="form-label" style="color: var(--discord-lightest);">Stock Status</label>
                            <select class="form-select" id="stockStatusFilter" name="stock_status" style="background-color: var(--discord-darkest); border: 1px solid var(--discord-darkest); color: var(--discord-lightest);">
                                <option value="">All Stock Levels</option>
                                <option value="in_stock">In Stock (>10)</option>
                                <option value="low_stock">Low Stock (1-10)</option>
                                <option value="out_of_stock">Out of Stock (0)</option>
                            </select>
                        </div>
                        
                        {{-- Featured Filter --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="color: var(--discord-lightest);">Featured</label>
                            <div class="form-check" style="margin-top: 8px;">
                                <input class="form-check-input" type="checkbox" id="featuredFilter" name="featured" value="1">
                                <label class="form-check-label" for="featuredFilter" style="color: var(--discord-light);">
                                    Show only featured products
                                </label>
                            </div>
                        </div>
                        @endif
                        
                        @if(isset($type) && $type === 'services')
                        {{-- Service Type Filter --}}
                        <div class="col-md-6 mb-3">
                            <label for="serviceTypeFilter" class="form-label" style="color: var(--discord-lightest);">Service Type</label>
                            <select class="form-select" id="serviceTypeFilter" name="service_type" style="background-color: var(--discord-darkest); border: 1px solid var(--discord-darkest); color: var(--discord-lightest);">
                                <option value="">All Service Types</option>
                                <option value="home_service">Home Service</option>
                                <option value="in_store">In-Store Service</option>
                            </select>
                        </div>
                        
                        {{-- Duration Range --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="color: var(--discord-lightest);">Duration (minutes)</label>
                            <div class="row">
                                <div class="col-6">
                                    <input type="number" class="form-control" id="durationMin" name="duration_min" placeholder="Min" min="0" style="background-color: var(--discord-darkest); border: 1px solid var(--discord-darkest); color: var(--discord-lightest);">
                                </div>
                                <div class="col-6">
                                    <input type="number" class="form-control" id="durationMax" name="duration_max" placeholder="Max" min="0" style="background-color: var(--discord-darkest); border: 1px solid var(--discord-darkest); color: var(--discord-lightest);">
                                </div>
                            </div>
                        </div>
                        
                        {{-- Featured Filter --}}
                        <div class="col-md-12 mb-3">
                            <label class="form-label" style="color: var(--discord-lightest);">Featured</label>
                            <div class="form-check" style="margin-top: 8px;">
                                <input class="form-check-input" type="checkbox" id="featuredServiceFilter" name="featured" value="1">
                                <label class="form-check-label" for="featuredServiceFilter" style="color: var(--discord-light);">
                                    Show only featured services
                                </label>
                            </div>
                        </div>
                        @endif
                        
                        {{-- Sort Options --}}
                        <div class="col-md-6 mb-3">
                            <label for="sortBy" class="form-label" style="color: var(--discord-lightest);">Sort By</label>
                            <select class="form-select" id="sortBy" name="sort_by" style="background-color: var(--discord-darkest); border: 1px solid var(--discord-darkest); color: var(--discord-lightest);">
                                <option value="created_at">Date Created</option>
                                <option value="updated_at">Date Updated</option>
                                <option value="name">Name</option>
                                <option value="price">Price</option>
                                @if(isset($type) && $type === 'products')
                                <option value="stock">Stock</option>
                                @elseif(isset($type) && $type === 'services')
                                <option value="duration">Duration</option>
                                @endif
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="sortOrder" class="form-label" style="color: var(--discord-lightest);">Sort Order</label>
                            <select class="form-select" id="sortOrder" name="sort_order" style="background-color: var(--discord-darkest); border: 1px solid var(--discord-darkest); color: var(--discord-lightest);">
                                <option value="desc">Descending</option>
                                <option value="asc">Ascending</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer" style="border-top: 1px solid var(--discord-darkest);">
                <button type="button" class="discord-btn-secondary" id="clearFilters">
                    <i class="fas fa-times me-1"></i> Clear All
                </button>
                <button type="button" class="discord-btn" id="applyFilters">
                    <i class="fas fa-check me-1"></i> Apply Filters
                </button>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Include required CSS and JS --}}
@push('styles')
<link rel="stylesheet" href="{{ asset('css/merchant-search.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/merchant-search.js') }}"></script>
@endpush
