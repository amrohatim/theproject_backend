/**
 * Advanced Products Filtering and Sorting System
 * Handles real-time filtering, sorting, bulk actions, and URL state management
 */

// Global variables
var currentFilters = {};
var selectedProducts = [];
var isLoading = false;
var debounceTimer = null;

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeProductsFilters();
});

function initializeProductsFilters() {
    bindEvents();
    loadFiltersFromURL();
    updateActiveFiltersDisplay();
    updateSortIcons();
}

function bindEvents() {
    // Filter toggle
    var filterToggle = document.getElementById('filterToggle');
    if (filterToggle) {
        filterToggle.addEventListener('click', function() {
            toggleFiltersPanel();
        });
    }

    // Search input with debounce
    var productSearch = document.getElementById('productSearch');
    if (productSearch) {
        productSearch.addEventListener('input', function(e) {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(function() {
                updateFilter('search', e.target.value);
            }, 300);
        });
    }

    // Filter form changes
    var filtersForm = document.getElementById('filtersForm');
    if (filtersForm) {
        filtersForm.addEventListener('change', function(e) {
            updateFilter(e.target.name, e.target.value);
        });
    }

    // Clear all filters
    var clearAllFiltersBtn = document.getElementById('clearAllFilters');
    if (clearAllFiltersBtn) {
        clearAllFiltersBtn.addEventListener('click', function() {
            clearAllFilters();
        });
    }

    // Sortable headers
    var sortableHeaders = document.querySelectorAll('.sortable-header');
    for (var i = 0; i < sortableHeaders.length; i++) {
        sortableHeaders[i].addEventListener('click', function() {
            var sortBy = this.dataset.sort;
            toggleSort(sortBy);
        });
    }

    // Select all checkbox
    var selectAll = document.getElementById('selectAll');
    if (selectAll) {
        selectAll.addEventListener('change', function(e) {
            toggleSelectAll(e.target.checked);
        });
    }

    // Individual product checkboxes
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('product-checkbox')) {
            toggleProductSelection(e.target.value, e.target.checked);
        }
    });

    // Bulk actions
    var bulkActivate = document.getElementById('bulkActivate');
    if (bulkActivate) {
        bulkActivate.addEventListener('click', function() {
            performBulkAction('activate');
        });
    }

    var bulkDeactivate = document.getElementById('bulkDeactivate');
    if (bulkDeactivate) {
        bulkDeactivate.addEventListener('click', function() {
            performBulkAction('deactivate');
        });
    }

    var bulkDelete = document.getElementById('bulkDelete');
    if (bulkDelete) {
        bulkDelete.addEventListener('click', function() {
            performBulkAction('delete');
        });
    }

    var clearSelection = document.getElementById('clearSelection');
    if (clearSelection) {
        clearSelection.addEventListener('click', function() {
            clearProductSelection();
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

function toggleSort(sortBy) {
    var currentSort = currentFilters.sort || 'created_at';
    var currentDirection = currentFilters.direction || 'desc';

    if (currentSort === sortBy) {
        // Toggle direction
        currentFilters.direction = currentDirection === 'asc' ? 'desc' : 'asc';
    } else {
        // New sort field
        currentFilters.sort = sortBy;
        currentFilters.direction = 'asc';
    }

    applyFilters();
    updateURL();
    updateSortIcons();
}

function applyFilters() {
    if (isLoading) return;

    isLoading = true;
    showLoadingState();

    var params = new URLSearchParams();
    for (var key in currentFilters) {
        if (currentFilters.hasOwnProperty(key)) {
            params.set(key, currentFilters[key]);
        }
    }

    fetch(window.location.pathname + '?' + params.toString(), {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(function(response) {
        return response.json();
    })
    .then(function(data) {
        if (data.success) {
            updateProductsTable(data.html);
            updatePagination(data.pagination);
            updateResultsCount(data.total);
        }
    })
    .catch(function(error) {
        console.error('Filter error:', error);
        showError('Failed to apply filters');
    })
    .finally(function() {
        isLoading = false;
        hideLoadingState();
    });
}

function updateURL() {
    var params = new URLSearchParams();
    for (var key in currentFilters) {
        if (currentFilters.hasOwnProperty(key)) {
            params.set(key, currentFilters[key]);
        }
    }
    var newURL = window.location.pathname + '?' + params.toString();
    window.history.replaceState({}, '', newURL);
}

function loadFiltersFromURL() {
    var params = new URLSearchParams(window.location.search);

    params.forEach(function(value, key) {
        currentFilters[key] = value;

        // Update form fields
        var field = document.querySelector('[name="' + key + '"]');
        if (field) {
            field.value = value;
        }

        // Update search field
        if (key === 'search') {
            var searchField = document.getElementById('productSearch');
            if (searchField) {
                searchField.value = value;
            }
        }
    });
}

function updateActiveFiltersDisplay() {
    // Simple implementation - just show count
    var filterCount = Object.keys(currentFilters).length;
    var activeFiltersCount = document.getElementById('activeFiltersCount');

    if (activeFiltersCount) {
        if (filterCount > 0) {
            activeFiltersCount.textContent = filterCount;
            activeFiltersCount.classList.remove('hidden');
        } else {
            activeFiltersCount.classList.add('hidden');
        }
    }
}

function updateSortIcons() {
    var currentSort = currentFilters.sort || 'created_at';
    var currentDirection = currentFilters.direction || 'desc';

    // Reset all icons
    var sortIcons = document.querySelectorAll('.sort-icon');
    for (var i = 0; i < sortIcons.length; i++) {
        sortIcons[i].innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>';
        sortIcons[i].classList.remove('text-blue-600');
        sortIcons[i].classList.add('text-gray-400');
    }

    // Update active sort icon
    var activeHeader = document.querySelector('[data-sort="' + currentSort + '"] .sort-icon');
    if (activeHeader) {
        activeHeader.classList.remove('text-gray-400');
        activeHeader.classList.add('text-blue-600');

        if (currentDirection === 'asc') {
            activeHeader.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>';
        } else {
            activeHeader.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>';
        }
    }
}

function applyFilters() {
    if (isLoading) return;

    isLoading = true;
    showLoadingState();

        var params = new URLSearchParams(currentFilters);
        
        fetch(window.location.pathname + '?' + params.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success) {
                updateProductsTable(data.html);
                updatePagination(data.pagination);
                updateResultsCount(data.total);
            }
        })
        .catch(function(error) {
            console.error('Filter error:', error);
            showError('Failed to apply filters');
        })
        .finally(function() {
            isLoading = false;
            hideLoadingState();
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
    var categorySelect = document.querySelector('select[name="category"]');
    if (categorySelect) {
        categorySelect.value = '';
    }

    // Status dropdown: Reset to "All Statuses" (empty value)
    var statusSelect = document.querySelector('select[name="status"]');
    if (statusSelect) {
        statusSelect.value = '';
    }

    // Stock Level dropdown: Reset to "All Stock Levels" (empty value)
    var stockLevelSelect = document.querySelector('select[name="stock_level"]');
    if (stockLevelSelect) {
        stockLevelSelect.value = '';
    }

    // Sort By dropdown: Reset to "Date Created" (created_at)
    var sortSelect = document.querySelector('select[name="sort"]');
    if (sortSelect) {
        sortSelect.value = 'created_at';
    }

    // Sort Direction dropdown: Reset to "Descending" (desc)
    var directionSelect = document.querySelector('select[name="direction"]');
    if (directionSelect) {
        directionSelect.value = 'desc';
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

    // Date Range inputs: Clear both from and to date fields
    var dateFromInput = document.querySelector('input[name="date_from"]');
    if (dateFromInput) {
        dateFromInput.value = '';
    }
    var dateToInput = document.querySelector('input[name="date_to"]');
    if (dateToInput) {
        dateToInput.value = '';
    }

    // Search input: Clear the search text
    var productSearch = document.getElementById('productSearch');
    if (productSearch) {
        productSearch.value = '';
    }

    applyFilters();
    updateURL();
    updateActiveFiltersDisplay();
    updateSortIcons();
}

// Helper functions
function updateProductsTable(html) {
    var tableContainer = document.getElementById('productsTableContainer');
    if (tableContainer) {
        tableContainer.innerHTML = html;
        bindCheckboxEvents();
    }
}

function updatePagination(html) {
    var paginationContainer = document.getElementById('paginationContainer');
    if (paginationContainer) {
        paginationContainer.innerHTML = html;
    }
}

function updateResultsCount(total) {
    var countElements = document.querySelectorAll('.text-sm.text-gray-500');
    for (var i = 0; i < countElements.length; i++) {
        if (countElements[i].textContent.includes('products found')) {
            countElements[i].textContent = total + ' products found';
            break;
        }
    }
}

function bindCheckboxEvents() {
    var checkboxes = document.querySelectorAll('.product-checkbox');
    for (var i = 0; i < checkboxes.length; i++) {
        checkboxes[i].addEventListener('change', function(e) {
            toggleProductSelection(e.target.value, e.target.checked);
        });
    }
}

function showLoadingState() {
    var tableContainer = document.getElementById('productsTableContainer');
    if (tableContainer) {
        tableContainer.style.opacity = '0.6';
        tableContainer.style.pointerEvents = 'none';
    }
}

function hideLoadingState() {
    var tableContainer = document.getElementById('productsTableContainer');
    if (tableContainer) {
        tableContainer.style.opacity = '1';
        tableContainer.style.pointerEvents = 'auto';
    }
}

function showError(message) {
    console.error(message);
    // Simple alert for now - can be enhanced with better UI
    alert(message);
}

// Bulk actions and selection functions
function toggleSelectAll(checked) {
    var checkboxes = document.querySelectorAll('.product-checkbox');
    for (var i = 0; i < checkboxes.length; i++) {
        checkboxes[i].checked = checked;
        toggleProductSelection(checkboxes[i].value, checked);
    }
}

function toggleProductSelection(productId, selected) {
    var index = selectedProducts.indexOf(productId);

    if (selected && index === -1) {
        selectedProducts.push(productId);
    } else if (!selected && index > -1) {
        selectedProducts.splice(index, 1);
    }

    updateBulkActionsDisplay();
    updateSelectAllState();
}

function updateBulkActionsDisplay() {
    var bulkActions = document.getElementById('bulkActions');
    var selectedCount = document.getElementById('selectedCount');

    if (bulkActions && selectedCount) {
        if (selectedProducts.length > 0) {
            bulkActions.classList.remove('hidden');
            selectedCount.textContent = selectedProducts.length;
        } else {
            bulkActions.classList.add('hidden');
        }
    }
}

function updateSelectAllState() {
    var selectAll = document.getElementById('selectAll');
    var checkboxes = document.querySelectorAll('.product-checkbox');
    var checkedBoxes = document.querySelectorAll('.product-checkbox:checked');

    if (selectAll) {
        if (checkedBoxes.length === 0) {
            selectAll.indeterminate = false;
            selectAll.checked = false;
        } else if (checkedBoxes.length === checkboxes.length) {
            selectAll.indeterminate = false;
            selectAll.checked = true;
        } else {
            selectAll.indeterminate = true;
            selectAll.checked = false;
        }
    }
}

function clearProductSelection() {
    selectedProducts = [];
    var checkboxes = document.querySelectorAll('.product-checkbox');
    for (var i = 0; i < checkboxes.length; i++) {
        checkboxes[i].checked = false;
    }
    updateBulkActionsDisplay();
    updateSelectAllState();
}

function performBulkAction(action) {
    if (selectedProducts.length === 0) {
        showError('No items selected');
        return;
    }

    var confirmMessages = {
        activate: 'Are you sure you want to activate the selected products?',
        deactivate: 'Are you sure you want to deactivate the selected products?',
        delete: 'Are you sure you want to delete the selected products? This action cannot be undone.'
    };

    if (!confirm(confirmMessages[action])) {
        return;
    }

    var csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        showError('CSRF token not found');
        return;
    }

    fetch('/merchant/products/bulk-action', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken.content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            action: action,
            product_ids: selectedProducts
        })
    })
    .then(function(response) {
        return response.json();
    })
    .then(function(data) {
        if (data.success) {
            alert(data.message || 'Bulk action completed successfully');
            clearProductSelection();
            applyFilters(); // Refresh the table
        } else {
            showError(data.message || 'Failed to perform bulk action');
        }
    })
    .catch(function(error) {
        console.error('Bulk action error:', error);
        showError('Failed to perform bulk action');
    });
}


