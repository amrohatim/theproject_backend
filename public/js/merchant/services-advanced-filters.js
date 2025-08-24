/**
 * Advanced Services Filtering and Sorting System
 * Handles real-time filtering, sorting, and URL state management for services
 */

// Global variables
var currentFilters = {};
var isLoading = false;
var debounceTimer = null;

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeServicesFilters();
});

function initializeServicesFilters() {
    bindEvents();
    loadFiltersFromURL();
    updateActiveFiltersDisplay();
}

function bindEvents() {
    // Search input
    var searchInput = document.getElementById('serviceSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(function() {
                updateFilter('search', searchInput.value);
            }, 300);
        });
    }

    // Filters toggle
    var filtersToggle = document.getElementById('filtersToggle');
    if (filtersToggle) {
        filtersToggle.addEventListener('click', function() {
            toggleFiltersPanel();
        });
    }

    // Clear all filters
    var clearAllFiltersBtn = document.getElementById('clearAllFilters');
    if (clearAllFiltersBtn) {
        clearAllFiltersBtn.addEventListener('click', function() {
            clearAllFilters();
        });
    }

    // Form inputs
    var formInputs = document.querySelectorAll('#filtersForm select, #filtersForm input');
    for (var i = 0; i < formInputs.length; i++) {
        formInputs[i].addEventListener('change', function() {
            var name = this.name;
            var value = this.value;
            updateFilter(name, value);
        });
    }
}

function toggleFiltersPanel() {
    var panel = document.getElementById('filtersPanel');
    if (!panel) return;

    var isHidden = panel.classList.contains('hidden');

    if (isHidden) {
        panel.classList.remove('hidden');
        panel.style.maxHeight = panel.scrollHeight + 'px';
    } else {
        panel.style.maxHeight = '0px';
        setTimeout(function() {
            panel.classList.add('hidden');
        }, 300);
    }
}

function updateFilter(name, value) {
    if (value === '' || value === null) {
        delete currentFilters[name];
    } else {
        currentFilters[name] = value;
    }

    applyFilters();
    updateURL();
    updateActiveFiltersDisplay();
}

function applyFilters() {
    if (isLoading) return;

    isLoading = true;
    showLoading();

    // Build query parameters
    var params = new URLSearchParams();
    for (var key in currentFilters) {
        if (currentFilters[key] !== '') {
            params.set(key, currentFilters[key]);
        }
    }

    // Make AJAX request
    fetch('/merchant/services?' + params.toString(), {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        }
    })
    .then(function(response) {
        return response.json();
    })
    .then(function(data) {
        if (data.success) {
            updateServicesTable(data.html);
            updatePagination(data.pagination);
            updateResultsCount(data.total);
        }
    })
    .catch(function(error) {
        console.error('Error applying filters:', error);
    })
    .finally(function() {
        isLoading = false;
        hideLoading();
    });
}

function clearAllFilters() {
    currentFilters = {};

    // Reset form
    var filtersForm = document.getElementById('filtersForm');
    if (filtersForm) {
        filtersForm.reset();
    }

    // Reset specific form elements to their default values
    // Category dropdown: Reset to "All Categories" (empty value)
    var categorySelect = document.querySelector('select[name="category_id"]');
    if (categorySelect) {
        categorySelect.value = '';
    }

    // Status dropdown: Reset to "All Statuses" (empty value)
    var statusSelect = document.querySelector('select[name="status"]');
    if (statusSelect) {
        statusSelect.value = '';
    }

    // Service Type dropdown: Reset to "All Service Types" (empty value)
    var serviceTypeSelect = document.querySelector('select[name="service_type"]');
    if (serviceTypeSelect) {
        serviceTypeSelect.value = '';
    }

    // Sort By dropdown: Reset to "Date Created" (created_at)
    var sortBySelect = document.querySelector('select[name="sort_by"]');
    if (sortBySelect) {
        sortBySelect.value = 'created_at';
    }

    // Sort Order dropdown: Reset to "Descending" (desc)
    var sortOrderSelect = document.querySelector('select[name="sort_order"]');
    if (sortOrderSelect) {
        sortOrderSelect.value = 'desc';
    }

    // Price Range inputs: Clear min and max price fields
    var priceMinInput = document.querySelector('input[name="price_min"]');
    if (priceMinInput) {
        priceMinInput.value = '';
    }
    var priceMaxInput = document.querySelector('input[name="price_max"]');
    if (priceMaxInput) {
        priceMaxInput.value = '';
    }

    // Duration Range inputs: Clear min and max duration fields
    var durationMinInput = document.querySelector('input[name="duration_min"]');
    if (durationMinInput) {
        durationMinInput.value = '';
    }
    var durationMaxInput = document.querySelector('input[name="duration_max"]');
    if (durationMaxInput) {
        durationMaxInput.value = '';
    }

    // Date Range inputs: Clear both from and to date fields
    var dateFromInput = document.querySelector('input[name="date_from"]');
    if (dateFromInput) {
        dateFromInput.value = '';
    }
    var dateToInput = document.querySelector('input[name="date_to"]');
    if (dateToInput) {
        dateToInput.value = '';
    }

    // Featured dropdown: Reset to "All Services" (empty value)
    var featuredSelect = document.querySelector('select[name="featured"]');
    if (featuredSelect) {
        featuredSelect.value = '';
    }

    // Search input: Clear the search text
    var serviceSearch = document.getElementById('serviceSearch');
    if (serviceSearch) {
        serviceSearch.value = '';
    }

    applyFilters();
    updateURL();
    updateActiveFiltersDisplay();
}

// Helper functions
function updateServicesTable(html) {
    var tableContainer = document.querySelector('.services-table-container');
    if (tableContainer) {
        tableContainer.innerHTML = html;
    }
}

function updatePagination(html) {
    var paginationContainer = document.querySelector('.services-pagination-container');
    if (paginationContainer) {
        paginationContainer.innerHTML = html;
    }
}

function updateResultsCount(total) {
    var countElements = document.querySelectorAll('.services-count');
    for (var i = 0; i < countElements.length; i++) {
        countElements[i].textContent = total + ' services found';
    }
}

function showLoading() {
    var loadingElement = document.querySelector('.services-loading');
    if (loadingElement) {
        loadingElement.style.display = 'block';
    }
}

function hideLoading() {
    var loadingElement = document.querySelector('.services-loading');
    if (loadingElement) {
        loadingElement.style.display = 'none';
    }
}

function loadFiltersFromURL() {
    var urlParams = new URLSearchParams(window.location.search);
    currentFilters = {};
    
    urlParams.forEach(function(value, key) {
        currentFilters[key] = value;
    });
}

function updateURL() {
    var params = new URLSearchParams();
    for (var key in currentFilters) {
        if (currentFilters[key] !== '') {
            params.set(key, currentFilters[key]);
        }
    }
    
    var newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
    window.history.replaceState({}, '', newUrl);
}

function updateActiveFiltersDisplay() {
    var activeCount = Object.keys(currentFilters).filter(function(key) {
        return currentFilters[key] !== '' && currentFilters[key] !== null;
    }).length;

    var countBadge = document.getElementById('activeFiltersCount');
    if (countBadge) {
        if (activeCount > 0) {
            countBadge.textContent = activeCount;
            countBadge.classList.remove('hidden');
        } else {
            countBadge.classList.add('hidden');
        }
    }
}

// Global function for clearing search and filters (used in empty state)
function clearServiceSearchAndFilters() {
    clearAllFilters();
}
