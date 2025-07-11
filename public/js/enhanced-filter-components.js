/**
 * Enhanced Filter Components
 * Modern UI/UX with animations, range sliders, and multi-select dropdowns
 */

class EnhancedFilterComponents {
    constructor(container, options = {}) {
        this.container = container;
        this.options = {
            enableAnimations: true,
            debounceDelay: 300,
            ...options
        };

        this.filterToggle = null;
        this.filterPanel = null;
        this.activeFilters = {};
        this.rangeSliders = [];
        this.multiSelects = [];
        
        this.debounceTimer = null;

        this.init();
    }

    init() {
        this.findElements();
        this.bindEvents();
        this.initializeRangeSliders();
        this.initializeMultiSelects();
        this.loadCategories();
    }

    findElements() {
        this.filterToggle = this.container.querySelector('.enhanced-filter-toggle');
        this.filterPanel = this.container.querySelector('.enhanced-filter-panel');
        this.filterCountElement = this.container.querySelector('.filter-count');
        this.activeFilterTags = this.container.querySelector('#activeFilterTags');
        this.clearAllButton = this.container.querySelector('#clearAllFilters');
        this.applyButton = this.container.querySelector('#applyFilters');
    }

    bindEvents() {
        // Filter toggle
        if (this.filterToggle) {
            this.filterToggle.addEventListener('click', () => {
                this.toggleFilterPanel();
            });
        }

        // Quick filter tags
        const quickFilterTags = this.container.querySelectorAll('.enhanced-filter-tag');
        quickFilterTags.forEach(tag => {
            tag.addEventListener('click', (e) => {
                this.toggleQuickFilter(e.target);
            });
        });

        // Clear all filters
        if (this.clearAllButton) {
            this.clearAllButton.addEventListener('click', () => {
                this.clearAllFilters();
            });
        }

        // Apply filters
        if (this.applyButton) {
            this.applyButton.addEventListener('click', () => {
                this.applyFilters();
            });
        }

        // Click outside to close
        document.addEventListener('click', (e) => {
            if (!this.container.contains(e.target)) {
                this.hideFilterPanel();
            }
        });
    }

    toggleFilterPanel() {
        if (this.filterPanel.classList.contains('visible')) {
            this.hideFilterPanel();
        } else {
            this.showFilterPanel();
        }
    }

    showFilterPanel() {
        this.filterPanel.classList.add('visible');
        this.filterToggle.classList.add('active');
        
        if (this.options.enableAnimations) {
            // Add entrance animation
            this.filterPanel.style.transform = 'translateY(-10px)';
            this.filterPanel.style.opacity = '0';
            
            requestAnimationFrame(() => {
                this.filterPanel.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
                this.filterPanel.style.transform = 'translateY(0)';
                this.filterPanel.style.opacity = '1';
            });
        }
    }

    hideFilterPanel() {
        this.filterPanel.classList.remove('visible');
        this.filterToggle.classList.remove('active');
    }

    toggleQuickFilter(button) {
        const filter = button.dataset.filter;
        const value = button.dataset.value;
        const isActive = button.classList.contains('active');

        if (isActive) {
            // Remove filter
            delete this.activeFilters[filter];
            button.classList.remove('active');
        } else {
            // Add filter
            this.activeFilters[filter] = value;
            button.classList.add('active');
        }

        this.updateFilterDisplay();
        this.debouncedApplyFilters();
    }

    initializeRangeSliders() {
        const rangeSliders = this.container.querySelectorAll('.enhanced-range-slider');
        
        rangeSliders.forEach(slider => {
            const rangeSliderInstance = new EnhancedRangeSlider(slider, {
                min: 0,
                max: 1000,
                step: 10,
                onChange: (values) => {
                    const filter = slider.dataset.filter;
                    this.activeFilters[`${filter}_min`] = values.min;
                    this.activeFilters[`${filter}_max`] = values.max;
                    this.updateFilterDisplay();
                    this.debouncedApplyFilters();
                }
            });
            
            this.rangeSliders.push(rangeSliderInstance);
        });
    }

    initializeMultiSelects() {
        const multiSelects = this.container.querySelectorAll('.enhanced-multiselect');
        
        multiSelects.forEach(multiSelect => {
            const multiSelectInstance = new EnhancedMultiSelect(multiSelect, {
                onChange: (selectedValues) => {
                    const filter = multiSelect.dataset.filter;
                    if (selectedValues.length > 0) {
                        this.activeFilters[filter] = selectedValues;
                    } else {
                        delete this.activeFilters[filter];
                    }
                    this.updateFilterDisplay();
                    this.debouncedApplyFilters();
                }
            });
            
            this.multiSelects.push(multiSelectInstance);
        });
    }

    async loadCategories() {
        try {
            const response = await fetch('/merchant/categories', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.populateCategories(data.categories || []);
            }
        } catch (error) {
            console.error('Error loading categories:', error);
        }
    }

    populateCategories(categories) {
        const categoryMultiSelect = this.container.querySelector('[data-filter="categories"]');
        if (!categoryMultiSelect) return;

        const dropdown = categoryMultiSelect.querySelector('.multiselect-dropdown');
        if (!dropdown) return;

        const html = categories.map(category => `
            <div class="multiselect-option" data-value="${category.id}">
                <div class="multiselect-checkbox"></div>
                <span>${category.name}</span>
            </div>
        `).join('');

        dropdown.innerHTML = html;

        // Re-bind events for the new options
        const multiSelectInstance = this.multiSelects.find(ms => ms.container === categoryMultiSelect);
        if (multiSelectInstance) {
            multiSelectInstance.bindOptionEvents();
        }
    }

    updateFilterDisplay() {
        this.updateFilterCount();
        this.updateActiveFilterTags();
    }

    updateFilterCount() {
        const count = Object.keys(this.activeFilters).length;
        
        if (this.filterCountElement) {
            if (count > 0) {
                this.filterCountElement.textContent = count;
                this.filterCountElement.style.display = 'inline-block';
                
                if (this.options.enableAnimations) {
                    this.filterCountElement.style.animation = 'filterCountPulse 0.3s ease-out';
                }
            } else {
                this.filterCountElement.style.display = 'none';
            }
        }
    }

    updateActiveFilterTags() {
        if (!this.activeFilterTags) return;

        const tags = Object.entries(this.activeFilters).map(([key, value]) => {
            const displayValue = Array.isArray(value) ? value.join(', ') : value;
            const displayKey = this.formatFilterKey(key);
            
            return `
                <div class="enhanced-filter-tag" data-filter="${key}">
                    <span>${displayKey}: ${displayValue}</span>
                    <button class="enhanced-filter-tag-remove" data-filter="${key}">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
        }).join('');

        this.activeFilterTags.innerHTML = tags;

        // Bind remove events
        const removeButtons = this.activeFilterTags.querySelectorAll('.enhanced-filter-tag-remove');
        removeButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.stopPropagation();
                const filter = button.dataset.filter;
                this.removeFilter(filter);
            });
        });
    }

    formatFilterKey(key) {
        const keyMap = {
            'status': 'Status',
            'featured': 'Featured',
            'stock_status': 'Stock Status',
            'service_type': 'Service Type',
            'categories': 'Categories',
            'price_min': 'Min Price',
            'price_max': 'Max Price',
            'type': 'Type'
        };

        return keyMap[key] || key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    }

    removeFilter(filterKey) {
        delete this.activeFilters[filterKey];
        
        // Update UI elements
        const quickFilterTag = this.container.querySelector(`[data-filter="${filterKey}"]`);
        if (quickFilterTag && quickFilterTag.classList.contains('enhanced-filter-tag')) {
            quickFilterTag.classList.remove('active');
        }

        this.updateFilterDisplay();
        this.debouncedApplyFilters();
    }

    clearAllFilters() {
        this.activeFilters = {};
        
        // Clear quick filter tags
        const quickFilterTags = this.container.querySelectorAll('.enhanced-filter-tag.active');
        quickFilterTags.forEach(tag => tag.classList.remove('active'));
        
        // Reset range sliders
        this.rangeSliders.forEach(slider => slider.reset());
        
        // Reset multi-selects
        this.multiSelects.forEach(multiSelect => multiSelect.reset());
        
        this.updateFilterDisplay();
        this.applyFilters();
    }

    debouncedApplyFilters() {
        if (this.debounceTimer) {
            clearTimeout(this.debounceTimer);
        }

        this.debounceTimer = setTimeout(() => {
            this.applyFilters();
        }, this.options.debounceDelay);
    }

    applyFilters() {
        // Trigger filter event
        const filterEvent = new CustomEvent('enhancedFiltersApplied', {
            detail: { 
                filters: this.activeFilters,
                filterCount: Object.keys(this.activeFilters).length
            }
        });
        document.dispatchEvent(filterEvent);

        // Hide filter panel on mobile after applying
        if (window.innerWidth <= 768) {
            this.hideFilterPanel();
        }
    }

    getActiveFilters() {
        return { ...this.activeFilters };
    }

    setFilters(filters) {
        this.activeFilters = { ...filters };
        this.updateFilterDisplay();
        
        // Update UI elements to reflect the filters
        this.syncUIWithFilters();
    }

    syncUIWithFilters() {
        // Update quick filter tags
        const quickFilterTags = this.container.querySelectorAll('.enhanced-filter-tag');
        quickFilterTags.forEach(tag => {
            const filter = tag.dataset.filter;
            const value = tag.dataset.value;
            
            if (this.activeFilters[filter] === value) {
                tag.classList.add('active');
            } else {
                tag.classList.remove('active');
            }
        });

        // Update range sliders
        this.rangeSliders.forEach(slider => {
            const filter = slider.container.dataset.filter;
            const minValue = this.activeFilters[`${filter}_min`];
            const maxValue = this.activeFilters[`${filter}_max`];
            
            if (minValue !== undefined || maxValue !== undefined) {
                slider.setValues({
                    min: minValue || slider.options.min,
                    max: maxValue || slider.options.max
                });
            }
        });

        // Update multi-selects
        this.multiSelects.forEach(multiSelect => {
            const filter = multiSelect.container.dataset.filter;
            const selectedValues = this.activeFilters[filter];
            
            if (selectedValues) {
                multiSelect.setSelectedValues(Array.isArray(selectedValues) ? selectedValues : [selectedValues]);
            }
        });
    }

    destroy() {
        if (this.debounceTimer) {
            clearTimeout(this.debounceTimer);
        }

        this.rangeSliders.forEach(slider => slider.destroy());
        this.multiSelects.forEach(multiSelect => multiSelect.destroy());
    }
}

// Auto-initialize enhanced filter components
document.addEventListener('DOMContentLoaded', function() {
    const filterContainers = document.querySelectorAll('.enhanced-filter-container');
    
    filterContainers.forEach(container => {
        new EnhancedFilterComponents(container, {
            enableAnimations: true
        });
    });
});

/**
 * Enhanced Range Slider Component
 */
class EnhancedRangeSlider {
    constructor(container, options = {}) {
        this.container = container;
        this.options = {
            min: 0,
            max: 100,
            step: 1,
            onChange: () => {},
            ...options
        };

        this.track = null;
        this.fill = null;
        this.thumbMin = null;
        this.thumbMax = null;
        this.valueMin = null;
        this.valueMax = null;

        this.values = {
            min: this.options.min,
            max: this.options.max
        };

        this.isDragging = false;
        this.activeThumb = null;

        this.init();
    }

    init() {
        this.findElements();
        this.bindEvents();
        this.updateDisplay();
    }

    findElements() {
        this.track = this.container.querySelector('.range-slider-track');
        this.fill = this.container.querySelector('.range-slider-fill');
        this.thumbMin = this.container.querySelector('[data-thumb="min"]');
        this.thumbMax = this.container.querySelector('[data-thumb="max"]');
        this.valueMin = this.container.querySelector('[data-value="min"]');
        this.valueMax = this.container.querySelector('[data-value="max"]');
    }

    bindEvents() {
        if (this.thumbMin) {
            this.thumbMin.addEventListener('mousedown', (e) => this.startDrag(e, 'min'));
            this.thumbMin.addEventListener('touchstart', (e) => this.startDrag(e, 'min'));
        }

        if (this.thumbMax) {
            this.thumbMax.addEventListener('mousedown', (e) => this.startDrag(e, 'max'));
            this.thumbMax.addEventListener('touchstart', (e) => this.startDrag(e, 'max'));
        }

        document.addEventListener('mousemove', (e) => this.handleDrag(e));
        document.addEventListener('touchmove', (e) => this.handleDrag(e));
        document.addEventListener('mouseup', () => this.stopDrag());
        document.addEventListener('touchend', () => this.stopDrag());

        // Track click to move nearest thumb
        if (this.track) {
            this.track.addEventListener('click', (e) => this.handleTrackClick(e));
        }
    }

    startDrag(e, thumb) {
        e.preventDefault();
        this.isDragging = true;
        this.activeThumb = thumb;

        const thumbElement = thumb === 'min' ? this.thumbMin : this.thumbMax;
        if (thumbElement) {
            thumbElement.classList.add('active');
        }
    }

    handleDrag(e) {
        if (!this.isDragging || !this.activeThumb) return;

        e.preventDefault();
        const rect = this.track.getBoundingClientRect();
        const clientX = e.clientX || (e.touches && e.touches[0].clientX);
        const percentage = Math.max(0, Math.min(1, (clientX - rect.left) / rect.width));
        const value = this.options.min + percentage * (this.options.max - this.options.min);
        const steppedValue = Math.round(value / this.options.step) * this.options.step;

        if (this.activeThumb === 'min') {
            this.values.min = Math.min(steppedValue, this.values.max);
        } else {
            this.values.max = Math.max(steppedValue, this.values.min);
        }

        this.updateDisplay();
        this.options.onChange(this.values);
    }

    stopDrag() {
        if (!this.isDragging) return;

        this.isDragging = false;

        const thumbElement = this.activeThumb === 'min' ? this.thumbMin : this.thumbMax;
        if (thumbElement) {
            thumbElement.classList.remove('active');
        }

        this.activeThumb = null;
    }

    handleTrackClick(e) {
        if (this.isDragging) return;

        const rect = this.track.getBoundingClientRect();
        const percentage = (e.clientX - rect.left) / rect.width;
        const value = this.options.min + percentage * (this.options.max - this.options.min);

        // Move the nearest thumb
        const distanceToMin = Math.abs(value - this.values.min);
        const distanceToMax = Math.abs(value - this.values.max);

        if (distanceToMin < distanceToMax) {
            this.values.min = Math.max(this.options.min, Math.min(value, this.values.max));
        } else {
            this.values.max = Math.min(this.options.max, Math.max(value, this.values.min));
        }

        this.updateDisplay();
        this.options.onChange(this.values);
    }

    updateDisplay() {
        const range = this.options.max - this.options.min;
        const minPercentage = ((this.values.min - this.options.min) / range) * 100;
        const maxPercentage = ((this.values.max - this.options.min) / range) * 100;

        // Update fill
        if (this.fill) {
            this.fill.style.left = `${minPercentage}%`;
            this.fill.style.width = `${maxPercentage - minPercentage}%`;
        }

        // Update thumbs
        if (this.thumbMin) {
            this.thumbMin.style.left = `${minPercentage}%`;
        }

        if (this.thumbMax) {
            this.thumbMax.style.left = `${maxPercentage}%`;
        }

        // Update value displays
        if (this.valueMin) {
            this.valueMin.textContent = this.formatValue(this.values.min);
        }

        if (this.valueMax) {
            this.valueMax.textContent = this.formatValue(this.values.max);
        }
    }

    formatValue(value) {
        // Format based on the filter type
        const filter = this.container.dataset.filter;
        if (filter === 'price') {
            return `$${value}`;
        }
        return value.toString();
    }

    setValues(values) {
        this.values = {
            min: Math.max(this.options.min, Math.min(values.min, this.options.max)),
            max: Math.min(this.options.max, Math.max(values.max, this.options.min))
        };
        this.updateDisplay();
    }

    reset() {
        this.values = {
            min: this.options.min,
            max: this.options.max
        };
        this.updateDisplay();
    }

    destroy() {
        // Remove event listeners if needed
    }
}

/**
 * Enhanced Multi-Select Component
 */
class EnhancedMultiSelect {
    constructor(container, options = {}) {
        this.container = container;
        this.options = {
            onChange: () => {},
            ...options
        };

        this.trigger = null;
        this.dropdown = null;
        this.selectedContainer = null;
        this.placeholder = null;
        this.arrow = null;

        this.selectedValues = [];
        this.isOpen = false;

        this.init();
    }

    init() {
        this.findElements();
        this.bindEvents();
        this.updateDisplay();
    }

    findElements() {
        this.trigger = this.container.querySelector('.multiselect-trigger');
        this.dropdown = this.container.querySelector('.multiselect-dropdown');
        this.selectedContainer = this.container.querySelector('.multiselect-selected');
        this.placeholder = this.container.querySelector('.multiselect-placeholder');
        this.arrow = this.container.querySelector('.multiselect-arrow');
    }

    bindEvents() {
        if (this.trigger) {
            this.trigger.addEventListener('click', () => this.toggle());
        }

        this.bindOptionEvents();
    }

    bindOptionEvents() {
        const options = this.dropdown.querySelectorAll('.multiselect-option');
        options.forEach(option => {
            option.addEventListener('click', (e) => {
                e.stopPropagation();
                this.toggleOption(option);
            });
        });
    }

    toggle() {
        if (this.isOpen) {
            this.close();
        } else {
            this.open();
        }
    }

    open() {
        this.isOpen = true;
        this.dropdown.classList.add('visible');
        this.trigger.classList.add('active');
    }

    close() {
        this.isOpen = false;
        this.dropdown.classList.remove('visible');
        this.trigger.classList.remove('active');
    }

    toggleOption(option) {
        const value = option.dataset.value;
        const isSelected = option.classList.contains('selected');

        if (isSelected) {
            this.selectedValues = this.selectedValues.filter(v => v !== value);
            option.classList.remove('selected');
        } else {
            this.selectedValues.push(value);
            option.classList.add('selected');
        }

        this.updateDisplay();
        this.options.onChange(this.selectedValues);
    }

    updateDisplay() {
        if (!this.selectedContainer) return;

        if (this.selectedValues.length === 0) {
            this.selectedContainer.innerHTML = '<span class="multiselect-placeholder">Select categories...</span>';
        } else {
            const tags = this.selectedValues.map(value => {
                const option = this.dropdown.querySelector(`[data-value="${value}"]`);
                const text = option ? option.textContent.trim() : value;

                return `
                    <div class="multiselect-tag">
                        <span>${text}</span>
                        <button class="multiselect-tag-remove" data-value="${value}">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
            }).join('');

            this.selectedContainer.innerHTML = tags;

            // Bind remove events
            const removeButtons = this.selectedContainer.querySelectorAll('.multiselect-tag-remove');
            removeButtons.forEach(button => {
                button.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const value = button.dataset.value;
                    this.removeValue(value);
                });
            });
        }
    }

    removeValue(value) {
        this.selectedValues = this.selectedValues.filter(v => v !== value);

        const option = this.dropdown.querySelector(`[data-value="${value}"]`);
        if (option) {
            option.classList.remove('selected');
        }

        this.updateDisplay();
        this.options.onChange(this.selectedValues);
    }

    setSelectedValues(values) {
        this.selectedValues = [...values];

        // Update option states
        const options = this.dropdown.querySelectorAll('.multiselect-option');
        options.forEach(option => {
            const value = option.dataset.value;
            if (this.selectedValues.includes(value)) {
                option.classList.add('selected');
            } else {
                option.classList.remove('selected');
            }
        });

        this.updateDisplay();
    }

    reset() {
        this.selectedValues = [];

        const options = this.dropdown.querySelectorAll('.multiselect-option');
        options.forEach(option => option.classList.remove('selected'));

        this.updateDisplay();
    }

    destroy() {
        // Remove event listeners if needed
    }
}

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { EnhancedFilterComponents, EnhancedRangeSlider, EnhancedMultiSelect };
}
