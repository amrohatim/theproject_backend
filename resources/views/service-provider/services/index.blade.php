@extends('layouts.service-provider')

@section('title', __('service_provider.services'))
@section('page-title', __('service_provider.services'))

@section('content')
<div class="container mx-auto">
    <!-- Filters Section -->
    <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
        <!-- Filter Header -->
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <div class="flex items-center">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('service_provider.filters') }}</h3>
                @if($activeFilters > 0)
                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-[#53D2DC] text-white">
                        {{ __('service_provider.filters_active', ['count' => $activeFilters]) }}
                    </span>
                @endif
            </div>
            <button id="toggleFilters" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-chevron-down transition-transform" id="filterChevron"></i>
            </button>
        </div>

        <!-- Filter Content -->
        <div id="filterContent" class="p-4">
            <form id="filterForm" class="space-y-4">
                <!-- Search Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-search mr-1"></i>
                            {{ __('service_provider.search_services') }}
                        </label>
                        <input type="text"
                               name="search"
                               id="searchInput"
                               value="{{ request('search') }}"
                               placeholder="{{ __('service_provider.search_placeholder') }}"
                               class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-[#53D2DC] focus:border-[#53D2DC] transition-colors" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-sort mr-1"></i>
                            {{ __('service_provider.sort_by') }}
                        </label>
                        <select name="sort_by"
                                id="sortBy"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-[#53D2DC] focus:border-[#53D2DC]">
                            <option value="name" @selected(request('sort_by') == 'name')>{{ __('service_provider.sort_name_az') }}</option>
                            <option value="price_low" @selected(request('sort_by') == 'price_low')>{{ __('service_provider.sort_price_low_high') }}</option>
                            <option value="price_high" @selected(request('sort_by') == 'price_high')>{{ __('service_provider.sort_price_high_low') }}</option>
                            <option value="duration_low" @selected(request('sort_by') == 'duration_low')>{{ __('service_provider.sort_duration_short_long') }}</option>
                            <option value="duration_high" @selected(request('sort_by') == 'duration_high')>{{ __('service_provider.sort_duration_long_short') }}</option>
                            <option value="newest" @selected(request('sort_by') == 'newest')>{{ __('service_provider.sort_newest_first') }}</option>
                        </select>
                    </div>
                </div>

                <!-- Filter Row -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-building mr-1"></i>
                            {{ __('service_provider.branch') }}
                        </label>
                        <select name="branch_id"
                                id="branchFilter"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-[#53D2DC] focus:border-[#53D2DC]">
                            <option value="">{{ __('service_provider.all_branches') }}</option>
                            @foreach(($branches ?? []) as $branch)
                                <option value="{{ $branch->id }}" @selected(request('branch_id') == $branch->id)>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-tags mr-1"></i>
                            {{ __('service_provider.category') }}
                        </label>
                        <select name="category_id"
                                id="categoryFilter"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-[#53D2DC] focus:border-[#53D2DC]">
                            <option value="">{{ __('service_provider.all_categories') }}</option>
                            @foreach(($categories ?? []) as $category)
                                <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-toggle-on mr-1"></i>
                            {{ __('service_provider.status') }}
                        </label>
                        <select name="status"
                                id="statusFilter"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-[#53D2DC] focus:border-[#53D2DC]">
                            <option value="">{{ __('service_provider.all_status') }}</option>
                            <option value="available" @selected(request('status') == 'available')>{{ __('service_provider.available') }}</option>
                            <option value="unavailable" @selected(request('status') == 'unavailable')>{{ __('service_provider.unavailable') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-dollar-sign mr-1"></i>
                            {{ __('service_provider.price_range') }}
                        </label>
                        <div class="flex space-x-2">
                            <input type="number"
                                   name="min_price"
                                   id="minPrice"
                                   value="{{ request('min_price') }}"
                                   placeholder="{{ __('service_provider.min') }}"
                                   min="0"
                                   step="0.01"
                                   class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-[#53D2DC] focus:border-[#53D2DC] text-sm" />
                            <input type="number"
                                   name="max_price"
                                   id="maxPrice"
                                   value="{{ request('max_price') }}"
                                   placeholder="{{ __('service_provider.max') }}"
                                   min="0"
                                   step="0.01"
                                   class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-[#53D2DC] focus:border-[#53D2DC] text-sm" />
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex items-center space-x-3">
                        <button type="button"
                                id="clearFilters"
                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                            <i class="fas fa-times mr-2"></i>
                            {{ __('service_provider.clear_all_filters') }}
                        </button>
                        <div id="resultsCount" class="text-sm text-gray-500 dark:text-gray-400">
                            <!-- Results count will be updated via AJAX -->
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-[#53D2DC] text-white rounded-md hover:bg-[#53D2DC]/90 focus:ring-2 focus:ring-[#53D2DC] focus:ring-offset-2 transition-colors">
                            <i class="fas fa-filter mr-2"></i>
                            {{ __('service_provider.apply_filters') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Services List -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <div class="flex items-center">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('service_provider.your_services') }}</h3>
                <div id="loadingSpinner" class="ml-3 hidden">
                    <i class="fas fa-spinner fa-spin text-[#53D2DC]"></i>
                </div>
            </div>
            <a href="{{ route('service-provider.services.create') }}"
               class="inline-flex items-center px-3 py-2 bg-[#53D2DC] text-white rounded-md hover:bg-[#53D2DC]/90 focus:ring-2 focus:ring-[#53D2DC] focus:ring-offset-2 transition-colors text-sm">
                <i class="fas fa-plus mr-2"></i>
                {{ __('service_provider.add_service') }}
            </a>
        </div>

        <!-- Services Table Container -->
        <div id="servicesContainer">
            @include('service-provider.services.partials.services-table', ['services' => $services])
        </div>

        <!-- Pagination Container -->
        <div id="paginationContainer" class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
            {{ $services->links() }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const filterForm = document.getElementById('filterForm');
    const searchInput = document.getElementById('searchInput');
    const toggleFiltersBtn = document.getElementById('toggleFilters');
    const filterContent = document.getElementById('filterContent');
    const filterChevron = document.getElementById('filterChevron');
    const clearFiltersBtn = document.getElementById('clearFilters');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const servicesContainer = document.getElementById('servicesContainer');
    const paginationContainer = document.getElementById('paginationContainer');
    const resultsCount = document.getElementById('resultsCount');

    // Filter toggle functionality
    let filtersExpanded = true;
    toggleFiltersBtn.addEventListener('click', function() {
        filtersExpanded = !filtersExpanded;
        if (filtersExpanded) {
            filterContent.style.display = 'block';
            filterChevron.style.transform = 'rotate(0deg)';
        } else {
            filterContent.style.display = 'none';
            filterChevron.style.transform = 'rotate(-90deg)';
        }
    });

    // Real-time search with debouncing
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            performFilter();
        }, 500); // 500ms delay
    });

    // Filter change handlers
    const filterElements = ['sortBy', 'branchFilter', 'categoryFilter', 'statusFilter', 'minPrice', 'maxPrice'];
    filterElements.forEach(elementId => {
        const element = document.getElementById(elementId);
        if (element) {
            element.addEventListener('change', performFilter);
        }
    });

    // Form submission handler
    filterForm.addEventListener('submit', function(e) {
        e.preventDefault();
        performFilter();
    });

    // Clear filters functionality
    clearFiltersBtn.addEventListener('click', function() {
        clearAllFilters();
    });

    // Perform AJAX filtering
    function performFilter() {
        const formData = new FormData(filterForm);
        const params = new URLSearchParams(formData);

        // Show loading state
        showLoading(true);

        // Update URL without page reload
        const newUrl = `${window.location.pathname}?${params.toString()}`;
        window.history.pushState({}, '', newUrl);

        // Make AJAX request
        fetch(`{{ route('service-provider.services.filter') }}?${params.toString()}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update services container
                servicesContainer.innerHTML = data.html;

                // Update pagination
                paginationContainer.innerHTML = data.pagination;

                // Update results count
                updateResultsCount(data.count);

                // Re-attach pagination click handlers
                attachPaginationHandlers();
            } else {
                console.error('Filter request failed:', data);
            }
        })
        .catch(error => {
            console.error('Filter request error:', error);
            // Show error message to user
            showErrorMessage('An error occurred while filtering services. Please try again.');
        })
        .finally(() => {
            showLoading(false);
        });
    }

    // Show/hide loading state
    function showLoading(show) {
        if (show) {
            loadingSpinner.classList.remove('hidden');
            servicesContainer.style.opacity = '0.6';
        } else {
            loadingSpinner.classList.add('hidden');
            servicesContainer.style.opacity = '1';
        }
    }

    // Update results count
    function updateResultsCount(count) {
        if (count === 0) {
            resultsCount.textContent = 'No services found';
        } else if (count === 1) {
            resultsCount.textContent = '1 service found';
        } else {
            resultsCount.textContent = `${count} services found`;
        }
    }

    // Clear all filters
    function clearAllFilters() {
        // Reset form
        filterForm.reset();

        // Clear URL parameters
        window.history.pushState({}, '', window.location.pathname);

        // Perform filter to refresh results
        performFilter();
    }

    // Global function for clear filters button in no results state
    window.clearAllFilters = clearAllFilters;

    // Attach pagination click handlers
    function attachPaginationHandlers() {
        const paginationLinks = paginationContainer.querySelectorAll('a[href*="page="]');
        paginationLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const url = new URL(this.href);
                const page = url.searchParams.get('page');

                // Add current filters to pagination
                const formData = new FormData(filterForm);
                formData.append('page', page);
                const params = new URLSearchParams(formData);

                // Update URL
                const newUrl = `${window.location.pathname}?${params.toString()}`;
                window.history.pushState({}, '', newUrl);

                // Perform filter with pagination
                performFilterWithPage(page);
            });
        });
    }

    // Perform filter with specific page
    function performFilterWithPage(page) {
        const formData = new FormData(filterForm);
        formData.append('page', page);
        const params = new URLSearchParams(formData);

        showLoading(true);

        fetch(`{{ route('service-provider.services.filter') }}?${params.toString()}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                servicesContainer.innerHTML = data.html;
                paginationContainer.innerHTML = data.pagination;
                updateResultsCount(data.count);
                attachPaginationHandlers();

                // Scroll to top of services list
                servicesContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        })
        .catch(error => {
            console.error('Pagination request error:', error);
        })
        .finally(() => {
            showLoading(false);
        });
    }

    // Show error message
    function showErrorMessage(message) {
        // Create and show a temporary error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded z-50';
        errorDiv.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-red-500 hover:text-red-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        document.body.appendChild(errorDiv);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (errorDiv.parentElement) {
                errorDiv.remove();
            }
        }, 5000);
    }

    // Initialize results count on page load
    updateResultsCount({{ $services->total() }});

    // Attach initial pagination handlers
    attachPaginationHandlers();
});

// Handle service deletion
function deleteService(serviceId, serviceName) {
    if (confirm(`{{ __('service_provider.confirm_delete_service') }}`.replace(':name', serviceName))) {
        // Create a form and submit it
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/service-provider/services/${serviceId}`;

        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken;
        form.appendChild(csrfInput);

        // Add method override for DELETE
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);

        // Submit the form
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
