/**
 * Merchant Product Filters Component
 * Handles comprehensive filtering for products with real-time updates
 */
class MerchantProductFilters {
    constructor(options = {}) {
        this.options = {
            containerSelector: '.products-container',
            tableSelector: '.products-table-container',
            paginationSelector: '.products-pagination-container',
            filtersFormSelector: '#productFiltersForm',
            quickFiltersSelector: '.quick-filters',
            activeFiltersSelector: '.active-filters',
            loadingSelector: '.products-loading',
            statsSelector: '.products-stats',
            baseUrl: '/merchant/products',
            ...options
        };

        this.currentFilters = {};
        this.isLoading = false;
        this.filterOptions = {};

        this.init();
    }

    init() {
        this.loadFilterOptions();
        this.bindEvents();
        this.loadFiltersFromUrl();
        this.updateProductsList();
    }

    async loadFilterOptions() {
        try {
            const response = await fetch('/merchant/products/filter/options', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });

            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    this.filterOptions = data.options;
                    this.populateFilterDropdowns();
                }
            }
        } catch (error) {
            console.error('Error loading filter options:', error);
        }
    }

    populateFilterDropdowns() {
        // Populate category dropdown
        const categorySelect = document.getElementById('categoryFilter');
        if (categorySelect && this.filterOptions.categories) {
            categorySelect.innerHTML = '<option value="">All Categories</option>';
            this.filterOptions.categories.forEach(category => {
                const option = document.createElement('option');
                option.value = category.id;
                option.textContent = category.name;
                categorySelect.appendChild(option);
            });
        }

        // Set price range inputs
        if (this.filterOptions.price_range) {
            const priceMin = document.getElementById('priceMin');
            const priceMax = document.getElementById('priceMax');
            if (priceMin) priceMin.setAttribute('max', this.filterOptions.price_range.max);
            if (priceMax) priceMax.setAttribute('max', this.filterOptions.price_range.max);
        }
    }

    bindEvents() {
        // Quick filter buttons
        document.querySelectorAll('.filter-toggle[data-filter]').forEach(button => {
            button.addEventListener('click', (e) => {
                this.toggleQuickFilter(e.target);
            });
        });

        // Advanced filters form
        const filtersForm = document.querySelector(this.options.filtersFormSelector);
        if (filtersForm) {
            // Debounced input handlers
            const inputs = filtersForm.querySelectorAll('input, select');
            inputs.forEach(input => {
                let timeout;
                input.addEventListener('input', () => {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => {
                        this.updateFiltersFromForm();
                        this.updateProductsList();
                    }, 500);
                });

                input.addEventListener('change', () => {
                    this.updateFiltersFromForm();
                    this.updateProductsList();
                });
            });
        }

        // Apply filters button
        const applyFiltersBtn = document.getElementById('applyFilters');
        if (applyFiltersBtn) {
            applyFiltersBtn.addEventListener('click', () => {
                this.updateFiltersFromForm();
                this.updateProductsList();
                this.closeFiltersModal();
            });
        }

        // Clear filters button
        const clearFiltersBtn = document.getElementById('clearFilters');
        if (clearFiltersBtn) {
            clearFiltersBtn.addEventListener('click', () => {
                this.clearAllFilters();
            });
        }

        // Filter toggle button
        const filterToggle = document.getElementById('filterToggle');
        if (filterToggle) {
            filterToggle.addEventListener('click', () => {
                this.openFiltersModal();
            });
        }

        // Search integration
        document.addEventListener('merchantSearch', (e) => {
            this.currentFilters.search = e.detail.query;
            this.updateProductsList();
        });

        // Browser back/forward
        window.addEventListener('popstate', () => {
            this.loadFiltersFromUrl();
            this.updateProductsList();
        });
    }

    toggleQuickFilter(button) {
        const filter = button.getAttribute('data-filter');
        const value = button.getAttribute('data-value');

        if (button.classList.contains('active')) {
            // Remove filter
            delete this.currentFilters[filter];
            button.classList.remove('active');
        } else {
            // Add filter
            this.currentFilters[filter] = value;
            button.classList.add('active');
        }

        this.updateProductsList();
        this.updateActiveFiltersDisplay();
        this.updateUrl();
    }

    updateFiltersFromForm() {
        const form = document.querySelector(this.options.filtersFormSelector);
        if (!form) return;

        const formData = new FormData(form);
        const newFilters = {};

        for (const [key, value] of formData.entries()) {
            if (value && value.trim() !== '') {
                newFilters[key] = value;
            }
        }

        // Handle checkboxes separately
        const checkboxes = form.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                newFilters[checkbox.name] = checkbox.value;
            }
        });

        this.currentFilters = { ...this.currentFilters, ...newFilters };
        this.updateActiveFiltersDisplay();
        this.updateUrl();
    }

    async updateProductsList() {
        if (this.isLoading) return;

        try {
            this.isLoading = true;
            this.showLoading();

            const params = new URLSearchParams(this.currentFilters);
            const url = `${this.options.baseUrl}?${params.toString()}`;

            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const data = await response.json();

            if (data.success) {
                this.updateProductsTable(data.html);
                this.updatePagination(data.pagination);
                this.updateStats(data.total);
            } else {
                this.showError('Failed to load products');
            }
        } catch (error) {
            console.error('Error updating products list:', error);
            this.showError('Error loading products');
        } finally {
            this.isLoading = false;
            this.hideLoading();
        }
    }

    updateProductsTable(html) {
        const container = document.querySelector(this.options.tableSelector);
        if (container) {
            container.innerHTML = html;
        }
    }

    updatePagination(html) {
        const container = document.querySelector(this.options.paginationSelector);
        if (container) {
            container.innerHTML = html;
        }
    }

    updateStats(total) {
        const statsContainer = document.querySelector(this.options.statsSelector);
        if (statsContainer) {
            statsContainer.textContent = `${total} products found`;
        }

        // Update filter count badge
        const filterCount = Object.keys(this.currentFilters).length;
        const filterToggle = document.getElementById('filterToggle');
        const filterCountSpan = filterToggle?.querySelector('.filter-count');
        
        if (filterCountSpan) {
            if (filterCount > 0) {
                filterCountSpan.textContent = ` (${filterCount})`;
                filterCountSpan.style.display = 'inline';
            } else {
                filterCountSpan.style.display = 'none';
            }
        }
    }

    updateActiveFiltersDisplay() {
        const container = document.querySelector(this.options.activeFiltersSelector);
        if (!container) return;

        const activeFilters = Object.entries(this.currentFilters)
            .filter(([key, value]) => key !== 'search' && value)
            .map(([key, value]) => {
                const label = this.getFilterLabel(key, value);
                return `
                    <div class="filter-tag">
                        <span>${label}</span>
                        <i class="fas fa-times remove" data-filter="${key}"></i>
                    </div>
                `;
            }).join('');

        if (activeFilters) {
            container.innerHTML = activeFilters;
            container.style.display = 'flex';

            // Bind remove events
            container.querySelectorAll('.remove').forEach(btn => {
                btn.addEventListener('click', () => {
                    const filter = btn.getAttribute('data-filter');
                    this.removeFilter(filter);
                });
            });
        } else {
            container.style.display = 'none';
        }
    }

    getFilterLabel(key, value) {
        const labels = {
            category_id: () => {
                const category = this.filterOptions.categories?.find(c => c.id == value);
                return `Category: ${category?.name || value}`;
            },
            status: () => `Status: ${value.charAt(0).toUpperCase() + value.slice(1)}`,
            stock_status: () => {
                const statusLabels = {
                    in_stock: 'In Stock',
                    low_stock: 'Low Stock',
                    out_of_stock: 'Out of Stock'
                };
                return `Stock: ${statusLabels[value] || value}`;
            },
            featured: () => 'Featured Only',
            price_min: () => `Min Price: $${value}`,
            price_max: () => `Max Price: $${value}`,
            date_from: () => `From: ${value}`,
            date_to: () => `To: ${value}`,
            sort_by: () => `Sort: ${value.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}`,
            sort_order: () => `Order: ${value === 'asc' ? 'Ascending' : 'Descending'}`
        };

        return labels[key] ? labels[key]() : `${key}: ${value}`;
    }

    removeFilter(key) {
        delete this.currentFilters[key];
        
        // Update form inputs
        const form = document.querySelector(this.options.filtersFormSelector);
        if (form) {
            const input = form.querySelector(`[name="${key}"]`);
            if (input) {
                if (input.type === 'checkbox') {
                    input.checked = false;
                } else {
                    input.value = '';
                }
            }
        }

        // Update quick filter buttons
        const quickFilter = document.querySelector(`[data-filter="${key}"]`);
        if (quickFilter) {
            quickFilter.classList.remove('active');
        }

        this.updateProductsList();
        this.updateActiveFiltersDisplay();
        this.updateUrl();
    }

    clearAllFilters() {
        this.currentFilters = {};
        
        // Clear form
        const form = document.querySelector(this.options.filtersFormSelector);
        if (form) {
            form.reset();
        }

        // Clear quick filters
        document.querySelectorAll('.filter-toggle.active').forEach(btn => {
            btn.classList.remove('active');
        });

        // Clear search
        const searchInput = document.querySelector('.merchant-search-input');
        if (searchInput) {
            searchInput.value = '';
        }

        this.updateProductsList();
        this.updateActiveFiltersDisplay();
        this.updateUrl();
        this.closeFiltersModal();
    }

    loadFiltersFromUrl() {
        const urlParams = new URLSearchParams(window.location.search);
        this.currentFilters = {};

        for (const [key, value] of urlParams.entries()) {
            if (value) {
                this.currentFilters[key] = value;
            }
        }

        this.syncFormWithFilters();
        this.syncQuickFiltersWithFilters();
        this.updateActiveFiltersDisplay();
    }

    syncFormWithFilters() {
        const form = document.querySelector(this.options.filtersFormSelector);
        if (!form) return;

        Object.entries(this.currentFilters).forEach(([key, value]) => {
            const input = form.querySelector(`[name="${key}"]`);
            if (input) {
                if (input.type === 'checkbox') {
                    input.checked = value === '1' || value === 'true';
                } else {
                    input.value = value;
                }
            }
        });
    }

    syncQuickFiltersWithFilters() {
        document.querySelectorAll('.filter-toggle[data-filter]').forEach(btn => {
            const filter = btn.getAttribute('data-filter');
            const value = btn.getAttribute('data-value');
            
            if (this.currentFilters[filter] === value) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
    }

    updateUrl() {
        const params = new URLSearchParams(this.currentFilters);
        const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
        window.history.pushState({}, '', newUrl);
    }

    openFiltersModal() {
        const modal = document.getElementById('advancedFiltersModal');
        if (modal) {
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
        }
    }

    closeFiltersModal() {
        const modal = document.getElementById('advancedFiltersModal');
        if (modal) {
            const bsModal = bootstrap.Modal.getInstance(modal);
            if (bsModal) {
                bsModal.hide();
            }
        }
    }

    showLoading() {
        const container = document.querySelector(this.options.loadingSelector);
        if (container) {
            container.style.display = 'block';
        }

        const tableContainer = document.querySelector(this.options.tableSelector);
        if (tableContainer) {
            tableContainer.style.opacity = '0.5';
        }
    }

    hideLoading() {
        const container = document.querySelector(this.options.loadingSelector);
        if (container) {
            container.style.display = 'none';
        }

        const tableContainer = document.querySelector(this.options.tableSelector);
        if (tableContainer) {
            tableContainer.style.opacity = '1';
        }
    }

    showError(message) {
        // You can implement a toast notification or error display here
        console.error(message);
    }

    destroy() {
        // Clean up event listeners if needed
    }
}

// Global function for clearing search and filters (used in empty state)
function clearSearchAndFilters() {
    if (window.productFilters) {
        window.productFilters.clearAllFilters();
    }
}

// Global function for updating products list (used in pagination)
function updateProductsList() {
    if (window.productFilters) {
        window.productFilters.updateProductsList();
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    if (document.querySelector('.products-container')) {
        window.productFilters = new MerchantProductFilters();
    }
});

/**
 * Merchant Service Filters Component
 * Handles comprehensive filtering for services with real-time updates
 */
class MerchantServiceFilters extends MerchantProductFilters {
    constructor(options = {}) {
        const serviceOptions = {
            containerSelector: '.services-container',
            tableSelector: '.services-table-container',
            paginationSelector: '.services-pagination-container',
            filtersFormSelector: '#serviceFiltersForm',
            baseUrl: '/merchant/services',
            ...options
        };

        super(serviceOptions);
    }

    async loadFilterOptions() {
        try {
            const response = await fetch('/merchant/services/filter/options', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });

            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    this.filterOptions = data.options;
                    this.populateServiceFilterDropdowns();
                }
            }
        } catch (error) {
            console.error('Error loading service filter options:', error);
        }
    }

    populateServiceFilterDropdowns() {
        // Populate category dropdown
        const categorySelect = document.getElementById('categoryFilter');
        if (categorySelect && this.filterOptions.categories) {
            categorySelect.innerHTML = '<option value="">All Categories</option>';
            this.filterOptions.categories.forEach(category => {
                const option = document.createElement('option');
                option.value = category.id;
                option.textContent = category.name;
                categorySelect.appendChild(option);
            });
        }

        // Set price range inputs
        if (this.filterOptions.price_range) {
            const priceMin = document.getElementById('priceMin');
            const priceMax = document.getElementById('priceMax');
            if (priceMin) priceMin.setAttribute('max', this.filterOptions.price_range.max);
            if (priceMax) priceMax.setAttribute('max', this.filterOptions.price_range.max);
        }

        // Set duration range inputs
        if (this.filterOptions.duration_range) {
            const durationMin = document.getElementById('durationMin');
            const durationMax = document.getElementById('durationMax');
            if (durationMin) durationMin.setAttribute('max', this.filterOptions.duration_range.max);
            if (durationMax) durationMax.setAttribute('max', this.filterOptions.duration_range.max);
        }
    }

    updateStats(total) {
        const statsContainer = document.querySelector(this.options.statsSelector);
        if (statsContainer) {
            statsContainer.textContent = `${total} services found`;
        }

        // Update filter count badge
        const filterCount = Object.keys(this.currentFilters).length;
        const filterToggle = document.getElementById('filterToggle');
        const filterCountSpan = filterToggle?.querySelector('.filter-count');

        if (filterCountSpan) {
            if (filterCount > 0) {
                filterCountSpan.textContent = ` (${filterCount})`;
                filterCountSpan.style.display = 'inline';
            } else {
                filterCountSpan.style.display = 'none';
            }
        }
    }

    getFilterLabel(key, value) {
        const labels = {
            category_id: () => {
                const category = this.filterOptions.categories?.find(c => c.id == value);
                return `Category: ${category?.name || value}`;
            },
            status: () => `Status: ${value.charAt(0).toUpperCase() + value.slice(1)}`,
            service_type: () => {
                const typeLabels = {
                    home_service: 'Home Service',
                    in_store: 'In-Store Service'
                };
                return `Type: ${typeLabels[value] || value}`;
            },
            featured: () => 'Featured Only',
            price_min: () => `Min Price: $${value}`,
            price_max: () => `Max Price: $${value}`,
            duration_min: () => `Min Duration: ${value} min`,
            duration_max: () => `Max Duration: ${value} min`,
            date_from: () => `From: ${value}`,
            date_to: () => `To: ${value}`,
            sort_by: () => `Sort: ${value.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}`,
            sort_order: () => `Order: ${value === 'asc' ? 'Ascending' : 'Descending'}`
        };

        return labels[key] ? labels[key]() : `${key}: ${value}`;
    }
}

// Global function for clearing search and filters (used in empty state)
function clearServiceSearchAndFilters() {
    if (window.serviceFilters) {
        window.serviceFilters.clearAllFilters();
    }
}

// Global function for updating services list (used in pagination)
function updateServicesList() {
    if (window.serviceFilters) {
        window.serviceFilters.updateProductsList();
    }
}

// Initialize service filters when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    if (document.querySelector('.services-container')) {
        window.serviceFilters = new MerchantServiceFilters();
    }
});
