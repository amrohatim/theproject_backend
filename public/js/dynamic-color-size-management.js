/**
 * Dynamic Color-Size Relationship Management System
 *
 * This system provides:
 * 1. Dynamic size display based on color selection
 * 2. Stock allocation constraints and validation
 * 3. Real-time stock feedback and calculations
 * 4. Integration with existing enhanced color selection
 */

class DynamicColorSizeManager {
    constructor() {
        this.currentProductId = null;
        this.currentColorId = null;
        this.colorStockData = {};
        this.sizeAllocations = {};
        this.isInitialized = false;

        // CSRF token for API requests
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        this.init();
    }

    init() {
        if (this.isInitialized) return;

        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setupEventListeners());
        } else {
            this.setupEventListeners();
        }

        this.isInitialized = true;
    }

    setupEventListeners() {
        // Listen for color selection changes
        document.addEventListener('change', (e) => {
            if (e.target.classList.contains('color-name-select') ||
                e.target.closest('.enhanced-color-dropdown')) {
                this.handleColorSelection(e);
            }
        });

        // Listen for enhanced color dropdown selections
        document.addEventListener('colorSelected', (e) => {
            this.handleEnhancedColorSelection(e.detail);
        });

        // Listen for custom color stock change events from the form
        document.addEventListener('colorStockChanged', (e) => {
            this.handleColorStockChanged(e.detail);
        });

        // Listen for color stock input changes (direct input)
        document.addEventListener('input', (e) => {
            if (e.target.classList.contains('color-stock-input')) {
                this.handleColorStockInput(e);
            }
        });

        // Listen for size stock input changes in allocation sections
        document.addEventListener('input', (e) => {
            if (e.target.classList.contains('size-stock-input')) {
                this.handleSizeStockChange(e);
            }
        });

        // Listen for form submissions to validate stock allocations
        document.addEventListener('submit', (e) => {
            if (e.target.closest('form')) {
                this.validateFormSubmission(e);
            }
        });

        // Listen for size changes to refresh allocations
        document.addEventListener('change', (e) => {
            if (e.target.classList.contains('size-category-select') ||
                e.target.classList.contains('size-selection-dropdown')) {
                // Refresh all color allocations when sizes change
                setTimeout(() => this.refreshAllocations(), 100);
            }
        });

        // Listen for allocation size category changes
        document.addEventListener('change', (e) => {
            if (e.target.classList.contains('allocation-size-category-select')) {
                this.handleAllocationCategoryChange(e.target);
            }
        });

        // Listen for allocation size selection changes
        document.addEventListener('change', (e) => {
            if (e.target.classList.contains('allocation-size-option')) {
                this.handleAllocationSizeSelection(e.target);
            }
        });

        // Listen for default size checkbox changes
        document.addEventListener('change', (e) => {
            if (e.target.classList.contains('allocation-default-size-checkbox')) {
                this.handleDefaultSizeSelection(e.target);
            }
        });

        // Listen for remove allocation size button clicks
        document.addEventListener('click', (e) => {
            if (e.target.closest('.remove-allocation-size')) {
                this.handleRemoveAllocationSize(e.target.closest('.remove-allocation-size'));
            }
        });

        // Listen for add allocation size button clicks
        document.addEventListener('click', (e) => {
            if (e.target.closest('.add-allocation-size')) {
                this.handleAddAllocationSize(e.target.closest('.add-allocation-size'));
            }
        });
    }

    handleColorSelection(e) {
        const colorSelect = e.target.classList.contains('color-name-select') ?
            e.target : e.target.closest('.color-item')?.querySelector('.color-name-select');

        if (!colorSelect) return;

        const colorItem = colorSelect.closest('.color-item');
        const colorName = colorSelect.value;
        const stockInput = colorItem?.querySelector('.color-stock-input');
        const colorStock = stockInput ? parseInt(stockInput.value) || 0 : 0;

        if (colorName && colorStock > 0) {
            this.updateColorStockData(colorItem, colorName, colorStock);
            this.showSizesForColor(colorItem, colorName);
        } else {
            this.hideSizesForColor(colorItem);
        }
    }

    handleEnhancedColorSelection(detail) {
        if (detail && detail.colorName && detail.colorItem) {
            const stockInput = detail.colorItem.querySelector('.color-stock-input');
            const colorStock = stockInput ? parseInt(stockInput.value) || 0 : 0;

            if (colorStock > 0) {
                this.updateColorStockData(detail.colorItem, detail.colorName, colorStock);
                this.showSizesForColor(detail.colorItem, detail.colorName);
            }
        }
    }

    handleColorStockChanged(detail) {
        if (detail && detail.colorItem && detail.colorName) {
            if (detail.stock > 0) {
                this.updateColorStockData(detail.colorItem, detail.colorName, detail.stock);
                this.showSizesForColor(detail.colorItem, detail.colorName);
            } else {
                // Stock is 0 or less - hide the size section and show required message
                this.hideSizesForColor(detail.colorItem);

                // Clear any existing allocations for this color
                const colorIndex = this.getColorIndex(detail.colorItem);
                if (this.sizeAllocations[colorIndex]) {
                    this.sizeAllocations[colorIndex] = {};
                }
                if (this.colorStockData[colorIndex]) {
                    this.colorStockData[colorIndex].allocatedStock = 0;
                    this.colorStockData[colorIndex].remainingStock = 0;
                }
            }
        }
    }

    handleColorStockInput(e) {
        const stockInput = e.target;
        const colorItem = stockInput.closest('.color-item');
        const colorSelect = colorItem?.querySelector('.color-name-select');
        const stockValue = parseInt(stockInput.value) || 0;

        if (colorSelect && colorSelect.value) {
            if (stockValue > 0) {
                this.updateColorStockData(colorItem, colorSelect.value, stockValue);
                this.showSizesForColor(colorItem, colorSelect.value);
            } else {
                // Stock is 0 or less - hide the size section and show required message
                this.hideSizesForColor(colorItem);
            }
        } else {
            // No color selected - hide any existing size sections
            this.hideSizesForColor(colorItem);
        }
    }

    updateColorStockData(colorItem, colorName, totalStock) {
        const colorIndex = this.getColorIndex(colorItem);
        this.colorStockData[colorIndex] = {
            name: colorName,
            totalStock: totalStock,
            allocatedStock: 0,
            remainingStock: totalStock
        };
    }

    showSizesForColor(colorItem, colorName) {
        const colorIndex = this.getColorIndex(colorItem);

        // Remove any existing stock required message
        const existingMessage = colorItem.querySelector('.stock-required-message');
        if (existingMessage) {
            existingMessage.style.opacity = '0';
            existingMessage.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                if (existingMessage.parentNode) {
                    existingMessage.remove();
                }
            }, 200);
        }

        // Create or update the size allocation section
        let sizeSection = colorItem.querySelector('.color-size-allocation');
        if (!sizeSection) {
            sizeSection = this.createSizeAllocationSection(colorIndex, colorName);
            colorItem.appendChild(sizeSection);

            // Add fade-in animation
            setTimeout(() => {
                sizeSection.style.opacity = '1';
                sizeSection.style.transform = 'translateY(0)';
            }, 50);
        }

        this.populateSizesForColor(sizeSection, colorIndex);
    }

    hideSizesForColor(colorItem) {
        const sizeSection = colorItem.querySelector('.color-size-allocation');
        if (sizeSection) {
            // Add fade-out animation before removal
            sizeSection.style.opacity = '0';
            sizeSection.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                if (sizeSection.parentNode) {
                    sizeSection.remove();
                }
                // Show informational message when size section is hidden
                this.showStockRequiredMessage(colorItem);
            }, 200);
        } else {
            // Show informational message if no size section exists
            this.showStockRequiredMessage(colorItem);
        }
    }

    showStockRequiredMessage(colorItem) {
        // Remove any existing message
        const existingMessage = colorItem.querySelector('.stock-required-message');
        if (existingMessage) {
            existingMessage.remove();
        }

        // Create informational message
        const message = document.createElement('div');
        message.className = 'stock-required-message mt-4 p-4 bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-yellow-900/20 dark:to-orange-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800 transition-all duration-300';
        message.innerHTML = `
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-yellow-500 text-lg"></i>
                </div>
                <div class="ml-3">
                    <h4 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                        Size Management Unavailable
                    </h4>
                    <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                        Add stock to this color (greater than 0) to enable size management and allocation options.
                    </p>
                    <div class="mt-2 flex items-center text-xs text-yellow-600 dark:text-yellow-400">
                        <i class="fas fa-lightbulb mr-1"></i>
                        <span>Tip: Enter a stock quantity above, then select sizes and allocate stock across them.</span>
                    </div>
                </div>
            </div>
        `;

        // Insert the message where the size section would be
        colorItem.appendChild(message);

        // Add fade-in animation
        setTimeout(() => {
            message.style.opacity = '1';
            message.style.transform = 'translateY(0)';
        }, 50);
    }

    createSizeAllocationSection(colorIndex, colorName) {
        const section = document.createElement('div');
        section.className = 'color-size-allocation mt-4 p-4 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 shadow-sm transition-all duration-300';
        section.innerHTML = `
            <div class="flex justify-between items-center mb-3">
                <div class="flex items-center">
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        <i class="fas fa-layer-group mr-2 text-indigo-500"></i>
                        Size Allocation for ${colorName}
                    </h4>
                    <div class="ml-2 relative group">
                        <i class="fas fa-info-circle text-gray-400 hover:text-gray-600 cursor-help"></i>
                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-10">
                            Allocate stock quantities for each size. Total cannot exceed color stock.
                        </div>
                    </div>
                </div>
                <div class="stock-info-container">
                    <div class="stock-info text-sm bg-white dark:bg-gray-800 px-3 py-1 rounded-full border shadow-sm">
                        <span class="text-gray-600 dark:text-gray-400">Allocated: </span>
                        <span class="allocated-stock font-semibold text-blue-600">0</span>
                        <span class="text-gray-600 dark:text-gray-400"> / Remaining: </span>
                        <span class="remaining-stock font-semibold text-green-600">0</span>
                        <span class="text-gray-600 dark:text-gray-400"> / Total: </span>
                        <span class="total-stock font-semibold text-indigo-600">0</span>
                    </div>
                    <!-- Enhanced Progress Bar -->
                    <div class="stock-progress-container mt-2">
                        <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mb-1">
                            <span>Stock Allocation Progress</span>
                            <span class="allocation-percentage">0%</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                            <div class="stock-progress-bar h-2 rounded-full transition-all duration-300 ease-in-out bg-gradient-to-r from-blue-500 to-indigo-500" style="width: 0%"></div>
                        </div>
                        <div class="flex justify-between text-xs mt-1">
                            <span class="allocated-text text-gray-500 dark:text-gray-400">0 allocated</span>
                            <span class="remaining-stock-text text-green-600 dark:text-green-400 font-medium">0 remaining</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stock Status Indicator -->
            <div class="stock-status-indicator mb-3 p-2 rounded-lg border transition-all duration-200 hidden">
                <div class="flex items-center">
                    <div class="status-icon mr-2"></div>
                    <div class="status-message text-sm"></div>
                </div>
            </div>

            <!-- Add Size Button -->
            <div class="mb-3">
                <button type="button" class="add-allocation-size inline-flex items-center px-3 py-2 border border-transparent text-xs leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200" data-color-index="${colorIndex}">
                    <i class="fas fa-plus mr-1"></i> Add Size
                </button>
            </div>

            <div class="size-allocation-grid space-y-3">
                <div class="loading-message text-center text-gray-500 dark:text-gray-400 py-4">
                    <i class="fas fa-spinner fa-spin mr-2"></i>
                    Loading available sizes...
                </div>
            </div>

            <!-- Enhanced Validation Message -->
            <div class="validation-message mt-3 text-sm hidden"></div>

            <!-- Stock Distribution Suggestions -->
            <div class="stock-suggestions mt-3 hidden">
                <div class="flex items-center justify-between p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-lightbulb text-blue-500 mr-2"></i>
                        <span class="text-sm text-blue-700 dark:text-blue-300">Quick Actions:</span>
                    </div>
                    <div class="flex space-x-2">
                        <button type="button" class="distribute-evenly px-2 py-1 text-xs bg-blue-100 dark:bg-blue-800 text-blue-700 dark:text-blue-200 rounded hover:bg-blue-200 dark:hover:bg-blue-700 transition-colors">
                            Distribute Evenly
                        </button>
                        <button type="button" class="clear-allocation px-2 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                            Clear All
                        </button>
                    </div>
                </div>
            </div>
        `;
        return section;
    }

    populateSizesForColor(sizeSection, colorIndex) {
        const grid = sizeSection.querySelector('.size-allocation-grid');
        const loadingMessage = grid.querySelector('.loading-message');

        // Clear loading message
        if (loadingMessage) {
            loadingMessage.remove();
        }

        // Clear existing size inputs
        grid.innerHTML = '';

        // Get all available sizes from multiple sources
        let sizeItems = [];

        // First, try to get from global sizes container
        const globalSizesContainer = document.getElementById('sizes-container');
        if (globalSizesContainer) {
            sizeItems = globalSizesContainer.querySelectorAll('.size-item');
        }

        // If no global sizes, get from color-specific sizes
        if (sizeItems.length === 0) {
            const colorItem = sizeSection.closest('.color-item');
            const colorSizesContainer = colorItem?.querySelector('.color-sizes-container');
            if (colorSizesContainer) {
                sizeItems = colorSizesContainer.querySelectorAll('.color-size-item');
            }
        }

        // If still no sizes, create comprehensive default size options based on categories
        if (sizeItems.length === 0) {
            this.createDefaultCategorizedSizes(grid, colorIndex, sizeSection);
            return;
        }

        // Create size allocation inputs for each available size
        Array.from(sizeItems).forEach((sizeItem, index) => {
            const { sizeName, sizeData } = this.extractSizeNameFromItem(sizeItem, index);
            const allocationItem = this.createSizeAllocationItem(colorIndex, index, sizeName, sizeData);
            grid.appendChild(allocationItem);
        });

        this.updateStockDisplay(sizeSection, colorIndex);
    }

    createDefaultCategorizedSizes(grid, colorIndex, sizeSection) {
        // Create only ONE default size allocation item
        // Vendors can add more sizes manually using the "Add Size" button
        const defaultSizeName = 'Medium'; // Simple default size

        const allocationItem = this.createSizeAllocationItem(colorIndex, 0, defaultSizeName);
        grid.appendChild(allocationItem);

        this.updateStockDisplay(sizeSection, colorIndex);
    }

    extractSizeNameFromItem(sizeItem, index) {
        let sizeName = '';
        let sizeData = null;

        // Try to get size name from various inputs in order of preference
        const sizeNameInput = sizeItem.querySelector('input[name*="[name]"], .size-name-input, .allocation-size-name-input');
        const sizeSelectionDropdown = sizeItem.querySelector('.size-selection-dropdown');
        const sizeCategorySelect = sizeItem.querySelector('.size-category-select, .color-size-category-select, .allocation-size-category-select');
        const colorSizeOption = sizeItem.querySelector('.color-size-option, .allocation-size-option');
        const additionalInfoInput = sizeItem.querySelector('.allocation-additional-info-input, input[name*="[additional_info]"]');
        const priceAdjustmentInput = sizeItem.querySelector('input[name*="[price_adjustment]"]');
        const displayOrderInput = sizeItem.querySelector('input[name*="[display_order]"]');
        const defaultCheckbox = sizeItem.querySelector('.allocation-default-size-checkbox, input[name*="[is_default]"]');

        if (sizeNameInput && sizeNameInput.value) {
            // Use the actual size name from the input
            sizeName = sizeNameInput.value;
        } else if (sizeSelectionDropdown && sizeSelectionDropdown.value) {
            // Extract the display text from the selected option
            const selectedOption = sizeSelectionDropdown.options[sizeSelectionDropdown.selectedIndex];
            sizeName = selectedOption?.text || sizeSelectionDropdown.value;
        } else if (colorSizeOption && colorSizeOption.value) {
            // For color-specific size options
            const selectedOption = colorSizeOption.options[colorSizeOption.selectedIndex];
            sizeName = selectedOption?.text || colorSizeOption.value;
        } else if (sizeCategorySelect && sizeCategorySelect.value) {
            // Generate a name based on category
            const category = sizeCategorySelect.value;
            sizeName = `${category.charAt(0).toUpperCase() + category.slice(1)} Size ${index + 1}`;
        } else {
            // Final fallback
            sizeName = `Size ${index + 1}`;
        }

        // Extract additional data if available
        if (sizeCategorySelect || additionalInfoInput || priceAdjustmentInput || displayOrderInput || defaultCheckbox) {
            sizeData = {
                category: sizeCategorySelect?.value || '',
                additionalInfo: additionalInfoInput?.value || '',
                priceAdjustment: parseFloat(priceAdjustmentInput?.value) || 0,
                displayOrder: parseInt(displayOrderInput?.value) || index,
                isDefault: defaultCheckbox?.checked || false
            };
        }

        return { sizeName, sizeData };
    }

    createSizeAllocationItem(colorIndex, sizeIndex, sizeName, sizeData = null) {
        const item = document.createElement('div');
        item.className = 'size-allocation-item p-4 bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 shadow-sm hover:shadow-md transition-shadow duration-200 mb-4';

        // Extract size information if available
        const category = sizeData?.category || '';
        const sizeValue = sizeData?.value || sizeName;
        const additionalInfo = sizeData?.additionalInfo || '';
        const priceAdjustment = sizeData?.priceAdjustment || 0;
        const displayOrder = sizeData?.displayOrder || sizeIndex;
        const isDefault = sizeData?.isDefault || false;

        item.innerHTML = `
            <div class="grid grid-cols-12 gap-3">
                <!-- Size Category Dropdown -->
                <div class="col-span-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Size Category</label>
                    <select name="color_size_allocations[${colorIndex}][${sizeIndex}][category]"
                            class="p-2 allocation-size-category-select focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md"
                            data-color-index="${colorIndex}" data-size-index="${sizeIndex}">
                        <option value="" class>Select Category</option>
                        <option value="clothes" ${category === 'clothes' ? 'selected' : ''}>Clothes</option>
                        <option value="shoes" ${category === 'shoes' ? 'selected' : ''}>Shoes</option>
                        <option value="hats" ${category === 'hats' ? 'selected' : ''}>Hats</option>
                    </select>
                </div>

                <!-- Dynamic Size Selection -->
                <div class="col-span-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Size</label>
                    <div class="allocation-size-selection-container">
                        ${category ? this.generateAllocationSizeSelection(category, sizeName, colorIndex, sizeIndex) : `
                            <div class="flex items-center justify-center text-gray-500 dark:text-gray-400 text-sm h-10 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-800">
                                <p>Select category first</p>
                            </div>
                        `}
                    </div>
                    <input type="hidden" name="color_size_allocations[${colorIndex}][${sizeIndex}][size_name]" value="${sizeName}" class="allocation-size-name-input">
                    <input type="hidden" name="color_size_allocations[${colorIndex}][${sizeIndex}][size_value]" value="${sizeValue}" class="allocation-size-value-input">
                </div>

                <!-- Stock Input -->
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Stock</label>
                    <input type="number"
                           name="color_size_allocations[${colorIndex}][${sizeIndex}][stock]"
                           class="size-stock-input focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md"
                           min="0"
                           value="0"
                           data-color-index="${colorIndex}"
                           data-size-index="${sizeIndex}"
                           data-size-name="${sizeName}"
                           placeholder="0">
                </div>

                <!-- Price Adjustment -->
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Price +/-</label>
                    <input type="number"
                           step="0.01"
                           name="color_size_allocations[${colorIndex}][${sizeIndex}][price_adjustment]"
                           class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md"
                           placeholder="0.00"
                           value="${priceAdjustment}">
                </div>

                <!-- Remove Button -->
                <div class="col-span-1 flex items-end justify-center">
                    <button type="button" class="remove-allocation-size text-red-500 hover:text-red-700 text-sm" data-color-index="${colorIndex}" data-size-index="${sizeIndex}">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>

            <!-- Second Row: Additional Info, Display Order, Default Checkbox -->
            <div class="grid grid-cols-12 gap-3 mt-3">
                <!-- Additional Information -->
                <div class="col-span-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Additional Info</label>
                    <input type="text"
                           name="color_size_allocations[${colorIndex}][${sizeIndex}][additional_info]"
                           class="allocation-additional-info-input focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md"
                           placeholder="Foot length, age group, etc."
                           value="${additionalInfo}">
                </div>

                <!-- Display Order -->
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Order</label>
                    <input type="number"
                           name="color_size_allocations[${colorIndex}][${sizeIndex}][display_order]"
                           class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md"
                           placeholder="0"
                           value="${displayOrder}">
                </div>

                <!-- Default Size Checkbox -->
                <div class="col-span-6">
                    <div class="flex items-start mt-1">
                        <div class="flex items-center h-5">
                            <input type="checkbox"
                                   name="color_size_allocations[${colorIndex}][${sizeIndex}][is_default]"
                                   value="1"
                                   class="allocation-default-size-checkbox focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                   data-color-index="${colorIndex}"
                                   ${isDefault ? 'checked' : ''}>
                        </div>
                        <div class="ml-2 text-sm">
                            <label class="font-medium text-gray-700 dark:text-gray-300">Default Size for This Color</label>
                            <p class="text-gray-500 dark:text-gray-400 text-xs">Set as the default size option</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
        return item;
    }

    generateAllocationSizeSelection(category, selectedSize, colorIndex, sizeIndex) {
        // Access SIZE_DATA from enhanced-size-selection.js
        const sizeData = window.SIZE_DATA || {
            clothes: { 'S': 'Small', 'M': 'Medium', 'L': 'Large', 'XL': 'Extra Large' },
            shoes: { 38: '24.0cm', 39: '24.5cm', 40: '25.0cm', 41: '25.5cm', 42: '26.0cm' },
            hats: { 56: 'Youth/Adult XS', 57: 'Adult S', 58: 'Adult M', 59: 'Adult M/L', 60: 'Adult L' }
        };

        if (!sizeData[category]) {
            return `
                <div class="flex items-center justify-center text-gray-500 dark:text-gray-400 text-sm h-10 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-800">
                    <p>Invalid category</p>
                </div>
            `;
        }

        let options = '';
        Object.keys(sizeData[category]).forEach(size => {
            const displayText = category === 'clothes'
                ? `${size} (${sizeData[category][size]})`
                : `EU ${size} (${sizeData[category][size]})`;
            const isSelected = selectedSize.includes(size) ? 'selected' : '';
            options += `<option value="${size}" ${isSelected}>${displayText}</option>`;
        });

        return `
            <select class="p-2 allocation-size-option focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md"
                    data-color-index="${colorIndex}" data-size-index="${sizeIndex}">
                <option value="">Select Size</option>
                ${options}
            </select>
        `;
    }

    handleAllocationCategoryChange(selectElement) {
        const category = selectElement.value;
        const colorIndex = selectElement.dataset.colorIndex;
        const sizeIndex = selectElement.dataset.sizeIndex;
        const allocationItem = selectElement.closest('.size-allocation-item');
        const sizeSelectionContainer = allocationItem.querySelector('.allocation-size-selection-container');
        const sizeNameInput = allocationItem.querySelector('.allocation-size-name-input');
        const sizeValueInput = allocationItem.querySelector('.allocation-size-value-input');
        const additionalInfoInput = allocationItem.querySelector('.allocation-additional-info-input');

        if (!category) {
            // Clear the container if no category is selected
            sizeSelectionContainer.innerHTML = `
                <div class="flex items-center justify-center text-gray-500 dark:text-gray-400 text-sm h-10 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-800">
                    <p>Select category first</p>
                </div>
            `;
            sizeNameInput.value = '';
            sizeValueInput.value = '';
            if (additionalInfoInput) additionalInfoInput.value = '';
            return;
        }

        // Generate size selection dropdown for the category
        sizeSelectionContainer.innerHTML = this.generateAllocationSizeSelection(category, '', colorIndex, sizeIndex);
    }

    handleAllocationSizeSelection(selectElement) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const allocationItem = selectElement.closest('.size-allocation-item');
        const categorySelect = allocationItem.querySelector('.allocation-size-category-select');
        const sizeNameInput = allocationItem.querySelector('.allocation-size-name-input');
        const sizeValueInput = allocationItem.querySelector('.allocation-size-value-input');
        const additionalInfoInput = allocationItem.querySelector('.allocation-additional-info-input');
        const stockInput = allocationItem.querySelector('.size-stock-input');

        const category = categorySelect.value;
        const selectedValue = selectElement.value;

        if (!selectedValue) {
            sizeNameInput.value = '';
            sizeValueInput.value = '';
            if (additionalInfoInput) additionalInfoInput.value = '';
            return;
        }

        // Access SIZE_DATA for additional information
        const sizeData = window.SIZE_DATA || {};

        // Update hidden inputs based on category
        switch (category) {
            case 'clothes':
                sizeNameInput.value = sizeData.clothes?.[selectedValue] || selectedValue;
                sizeValueInput.value = selectedValue;
                if (additionalInfoInput) additionalInfoInput.value = '';
                break;
            case 'shoes':
                sizeNameInput.value = 'EU';
                sizeValueInput.value = selectedValue;
                if (additionalInfoInput) additionalInfoInput.value = sizeData.shoes?.[selectedValue] || '';
                break;
            case 'hats':
                sizeNameInput.value = 'EU';
                sizeValueInput.value = selectedValue;
                if (additionalInfoInput) additionalInfoInput.value = sizeData.hats?.[selectedValue] || '';
                break;
        }

        // Update the stock input data-size-name attribute
        if (stockInput) {
            const displayText = selectedOption.text;
            stockInput.setAttribute('data-size-name', displayText);
        }
    }

    handleDefaultSizeSelection(checkbox) {
        const colorIndex = checkbox.dataset.colorIndex;
        const allocationItem = checkbox.closest('.size-allocation-item');
        const colorSizeAllocation = allocationItem.closest('.color-size-allocation');

        if (checkbox.checked) {
            // Uncheck all other default checkboxes for this color
            const otherCheckboxes = colorSizeAllocation.querySelectorAll('.allocation-default-size-checkbox');
            otherCheckboxes.forEach(otherCheckbox => {
                if (otherCheckbox !== checkbox) {
                    otherCheckbox.checked = false;
                }
            });

            // Show success message
            this.showTemporaryMessage(colorSizeAllocation, 'Default size updated for this color!', 'success');
        }
    }

    handleRemoveAllocationSize(button) {
        const colorIndex = button.dataset.colorIndex;
        const sizeIndex = button.dataset.sizeIndex;
        const allocationItem = button.closest('.size-allocation-item');
        const colorSizeAllocation = allocationItem.closest('.color-size-allocation');

        // Remove from size allocations tracking
        if (this.sizeAllocations[colorIndex] && this.sizeAllocations[colorIndex][sizeIndex]) {
            delete this.sizeAllocations[colorIndex][sizeIndex];
        }

        // Remove the item with animation
        allocationItem.style.opacity = '0';
        allocationItem.style.transform = 'translateY(-10px)';
        setTimeout(() => {
            if (allocationItem.parentNode) {
                allocationItem.remove();
            }

            // Update stock display after removal
            this.recalculateColorStockData(colorIndex);
            this.updateStockDisplay(colorSizeAllocation, colorIndex);

            // Show message if no sizes left
            const remainingSizes = colorSizeAllocation.querySelectorAll('.size-allocation-item');
            if (remainingSizes.length === 0) {
                const grid = colorSizeAllocation.querySelector('.size-allocation-grid');
                grid.innerHTML = `
                    <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                        <i class="fas fa-plus-circle text-3xl mb-3 opacity-50"></i>
                        <p class="text-sm">No size allocations yet</p>
                        <p class="text-xs mt-1">Sizes will appear here based on your global size settings</p>
                    </div>
                `;
            }
        }, 200);
    }

    handleAddAllocationSize(button) {
        const colorIndex = button.dataset.colorIndex;
        const colorSizeAllocation = button.closest('.color-size-allocation');
        const grid = colorSizeAllocation.querySelector('.size-allocation-grid');

        // Get the next size index
        const existingSizes = grid.querySelectorAll('.size-allocation-item');
        const sizeIndex = existingSizes.length;

        // Remove any placeholder messages
        const placeholder = grid.querySelector('.text-center');
        if (placeholder) {
            placeholder.remove();
        }

        // Create new allocation item
        const newAllocationItem = this.createSizeAllocationItem(colorIndex, sizeIndex, `Size ${sizeIndex + 1}`);

        // Add with animation
        newAllocationItem.style.opacity = '0';
        newAllocationItem.style.transform = 'translateY(-10px)';
        grid.appendChild(newAllocationItem);

        // Trigger animation
        setTimeout(() => {
            newAllocationItem.style.opacity = '1';
            newAllocationItem.style.transform = 'translateY(0)';
        }, 50);

        // Show success message
        this.showTemporaryMessage(colorSizeAllocation, 'New size allocation added! Configure the size details.', 'success');
    }

    handleSizeStockChange(e) {
        const input = e.target;
        const colorIndex = input.dataset.colorIndex;
        const sizeIndex = input.dataset.sizeIndex;
        let newValue = parseInt(input.value) || 0;
        const originalInputValue = parseInt(e.target.value) || 0;

        if (!this.colorStockData[colorIndex]) return;

        // Ensure non-negative values with immediate feedback
        if (newValue < 0) {
            newValue = 0;
            input.value = 0;
            this.showInstantAlert(input, 'Stock cannot be negative', 'error');
        }

        // Update allocation tracking
        if (!this.sizeAllocations[colorIndex]) {
            this.sizeAllocations[colorIndex] = {};
        }

        // Calculate what the total would be with this new value
        const currentAllocations = { ...this.sizeAllocations[colorIndex] };
        currentAllocations[sizeIndex] = newValue;
        const totalAllocated = Object.values(currentAllocations).reduce((sum, val) => {
            const numVal = parseInt(val) || 0;
            return sum + numVal;
        }, 0);
        const colorData = this.colorStockData[colorIndex];

        // Real-time validation with immediate feedback
        if (totalAllocated > colorData.totalStock) {
            const overAllocation = totalAllocated - colorData.totalStock;
            const maxAllowedForThisSize = Math.max(0, newValue - overAllocation);

            // Show immediate alert for over-allocation
            this.showInstantAlert(input,
                `Exceeds color stock limit! Maximum allowed: ${maxAllowedForThisSize}. Total stock: ${colorData.totalStock}`,
                'error');

            // Auto-correct the value
            newValue = maxAllowedForThisSize;
            input.value = newValue;

            // Add visual feedback for correction
            input.classList.add('border-red-500', 'bg-red-50', 'dark:bg-red-900/20');
            setTimeout(() => {
                input.classList.remove('border-red-500', 'bg-red-50', 'dark:bg-red-900/20');
                input.classList.add('border-green-500', 'bg-green-50', 'dark:bg-green-900/20');
                setTimeout(() => {
                    input.classList.remove('border-green-500', 'bg-green-50', 'dark:bg-green-900/20');
                }, 2000);
            }, 1000);
        } else if (totalAllocated === colorData.totalStock) {
            // Perfect allocation feedback
            this.showInstantAlert(input, 'Perfect! All stock allocated.', 'success');
        } else if (newValue > 0) {
            // Valid allocation feedback
            const remaining = colorData.totalStock - totalAllocated;
            this.showInstantAlert(input, `Valid allocation. ${remaining} remaining.`, 'info');
        }

        // Update the allocation with the validated value
        this.sizeAllocations[colorIndex][sizeIndex] = newValue;

        // Recalculate total allocated stock for this color using the updated allocations
        const finalTotalAllocated = Object.values(this.sizeAllocations[colorIndex]).reduce((sum, val) => {
            const numVal = parseInt(val) || 0;
            return sum + numVal;
        }, 0);
        const remainingStock = colorData.totalStock - finalTotalAllocated;

        // Update color data
        colorData.allocatedStock = finalTotalAllocated;
        colorData.remainingStock = Math.max(0, remainingStock);

        // Update display
        const sizeSection = input.closest('.color-size-allocation');
        this.updateStockDisplay(sizeSection, colorIndex);

        // Validate allocation (should always be valid now, but keep for safety)
        const isOverAllocated = finalTotalAllocated > colorData.totalStock;
        this.validateStockAllocation(sizeSection, colorIndex, isOverAllocated);

        // Add visual feedback to the input
        this.addInputFeedback(input, !isOverAllocated);

        // Show temporary message if we had to adjust the value
        if (originalInputValue !== newValue && originalInputValue > 0) {
            this.showTemporaryMessage(sizeSection,
                `Allocation auto-corrected from ${originalInputValue} to ${newValue} to prevent over-allocation.`,
                'warning');
        }
    }

    updateStockDisplay(sizeSection, colorIndex) {
        const colorData = this.colorStockData[colorIndex];
        if (!colorData) return;

        // Update basic stock display
        const allocatedSpan = sizeSection.querySelector('.allocated-stock');
        const remainingSpan = sizeSection.querySelector('.remaining-stock');
        const totalSpan = sizeSection.querySelector('.total-stock');

        if (allocatedSpan) {
            allocatedSpan.textContent = colorData.allocatedStock;
        }

        if (remainingSpan) {
            remainingSpan.textContent = colorData.remainingStock;
            remainingSpan.className = `remaining-stock font-semibold ${
                colorData.remainingStock >= 0 ? 'text-green-600' : 'text-red-600'
            }`;
        }

        if (totalSpan) {
            totalSpan.textContent = colorData.totalStock;
        }

        // Update enhanced progress bar and indicators
        this.updateProgressBar(sizeSection, colorData);
        this.updateStockStatusIndicator(sizeSection, colorData);
        this.updateStockSuggestions(sizeSection, colorData, colorIndex);
    }

    updateProgressBar(sizeSection, colorData) {
        const progressBar = sizeSection.querySelector('.stock-progress-bar');
        const allocationPercentage = sizeSection.querySelector('.allocation-percentage');
        const remainingStockText = sizeSection.querySelector('.remaining-stock-text');
        const allocatedText = sizeSection.querySelector('.allocated-text');

        if (!progressBar || !allocationPercentage || !remainingStockText) return;

        const percentage = colorData.totalStock > 0 ?
            Math.min((colorData.allocatedStock / colorData.totalStock) * 100, 100) : 0;

        // Update progress bar
        progressBar.style.width = `${percentage}%`;

        // Change color based on allocation status
        if (colorData.allocatedStock > colorData.totalStock) {
            progressBar.className = 'stock-progress-bar h-2 rounded-full transition-all duration-300 ease-in-out bg-gradient-to-r from-red-500 to-red-600';
        } else if (percentage >= 90) {
            progressBar.className = 'stock-progress-bar h-2 rounded-full transition-all duration-300 ease-in-out bg-gradient-to-r from-yellow-500 to-orange-500';
        } else {
            progressBar.className = 'stock-progress-bar h-2 rounded-full transition-all duration-300 ease-in-out bg-gradient-to-r from-blue-500 to-indigo-500';
        }

        // Update percentage text
        allocationPercentage.textContent = `${Math.round(percentage)}%`;

        // Update allocated text
        if (allocatedText) {
            allocatedText.textContent = `${colorData.allocatedStock} allocated`;
        }

        // Update remaining stock text
        remainingStockText.textContent = `${colorData.remainingStock} remaining`;
        remainingStockText.className = `remaining-stock-text font-medium ${
            colorData.remainingStock >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'
        }`;
    }

    updateStockStatusIndicator(sizeSection, colorData) {
        const statusIndicator = sizeSection.querySelector('.stock-status-indicator');
        const statusIcon = sizeSection.querySelector('.status-icon');
        const statusMessage = sizeSection.querySelector('.status-message');

        if (!statusIndicator || !statusIcon || !statusMessage) return;

        let showIndicator = false;
        let iconClass = '';
        let message = '';
        let containerClass = 'stock-status-indicator mb-3 p-2 rounded-lg border transition-all duration-200';

        if (colorData.allocatedStock > colorData.totalStock) {
            showIndicator = true;
            iconClass = 'fas fa-exclamation-triangle text-red-500';
            message = `Over-allocated by ${colorData.allocatedStock - colorData.totalStock} units. Please reduce allocation.`;
            containerClass += ' bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800';
        } else if (colorData.allocatedStock === colorData.totalStock && colorData.totalStock > 0) {
            showIndicator = true;
            iconClass = 'fas fa-check-circle text-green-500';
            message = 'Perfect! All stock has been allocated across sizes.';
            containerClass += ' bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800';
        } else if (colorData.allocatedStock > 0 && colorData.allocatedStock < colorData.totalStock) {
            const remaining = colorData.totalStock - colorData.allocatedStock;
            showIndicator = true;
            iconClass = 'fas fa-info-circle text-blue-500';
            message = `${remaining} units remaining to allocate. Consider distributing evenly or adding to specific sizes.`;
            containerClass += ' bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800';
        }

        if (showIndicator) {
            statusIcon.className = iconClass;
            statusMessage.textContent = message;
            statusIndicator.className = containerClass;
            statusIndicator.classList.remove('hidden');
        } else {
            statusIndicator.classList.add('hidden');
        }
    }

    updateStockSuggestions(sizeSection, colorData, colorIndex) {
        const suggestions = sizeSection.querySelector('.stock-suggestions');
        if (!suggestions) return;

        // Show suggestions if there's unallocated stock and sizes are available
        const sizeInputs = sizeSection.querySelectorAll('.size-stock-input');
        const hasUnallocatedStock = colorData.remainingStock > 0;
        const hasSizes = sizeInputs.length > 0;

        if (hasUnallocatedStock && hasSizes) {
            suggestions.classList.remove('hidden');
            this.setupSuggestionButtons(sizeSection, colorIndex);
        } else {
            suggestions.classList.add('hidden');
        }
    }

    validateStockAllocation(sizeSection, colorIndex, isOverAllocated) {
        const validationMessage = sizeSection.querySelector('.validation-message');
        if (!validationMessage) return;

        if (isOverAllocated) {
            const colorData = this.colorStockData[colorIndex];
            validationMessage.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2 text-red-500"></i>
                    <span>Stock allocation exceeds available stock! Maximum allowed: <strong>${colorData.totalStock}</strong></span>
                </div>
            `;
            validationMessage.className = 'validation-message mt-3 text-sm text-red-700 bg-red-50 dark:bg-red-900/20 p-3 rounded-lg border border-red-200 dark:border-red-800 shadow-sm';
            validationMessage.classList.remove('hidden');
        } else {
            validationMessage.classList.add('hidden');
        }
    }

    addInputFeedback(input, isValid) {
        // Remove any existing feedback classes
        input.classList.remove('border-red-500', 'bg-red-50', 'border-green-500', 'bg-green-50', 'dark:bg-red-900/20', 'dark:bg-green-900/20');

        if (isValid) {
            input.classList.add('border-green-500', 'bg-green-50', 'dark:bg-green-900/20');
        } else {
            input.classList.add('border-red-500', 'bg-red-50', 'dark:bg-red-900/20');
        }

        // Remove feedback after 3 seconds
        setTimeout(() => {
            input.classList.remove('border-green-500', 'bg-green-50', 'border-red-500', 'bg-red-50', 'dark:bg-red-900/20', 'dark:bg-green-900/20');
        }, 3000);
    }

    validateFormSubmission(e) {
        let hasErrors = false;
        const errorMessages = [];

        // Check all color allocations
        Object.keys(this.colorStockData).forEach(colorIndex => {
            const colorData = this.colorStockData[colorIndex];
            if (colorData.allocatedStock > colorData.totalStock) {
                hasErrors = true;
                errorMessages.push(`• ${colorData.name}: Allocated ${colorData.allocatedStock} exceeds available ${colorData.totalStock}`);
            }
        });

        if (hasErrors) {
            e.preventDefault();

            // Create a more user-friendly error dialog
            const errorDialog = document.createElement('div');
            errorDialog.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
            errorDialog.innerHTML = `
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md mx-4 shadow-xl">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-exclamation-triangle text-red-500 text-xl mr-3"></i>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Stock Allocation Errors</h3>
                    </div>
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                            The following colors have stock allocation issues that need to be resolved:
                        </p>
                        <ul class="text-sm text-red-600 dark:text-red-400 space-y-1">
                            ${errorMessages.join('')}
                        </ul>
                    </div>
                    <div class="flex justify-end">
                        <button class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500" onclick="this.closest('.fixed').remove()">
                            OK, I'll fix this
                        </button>
                    </div>
                </div>
            `;

            document.body.appendChild(errorDialog);

            // Auto-remove after 10 seconds
            setTimeout(() => {
                if (errorDialog.parentNode) {
                    errorDialog.remove();
                }
            }, 10000);

            return false;
        }

        return true;
    }

    setupSuggestionButtons(sizeSection, colorIndex) {
        const distributeBtn = sizeSection.querySelector('.distribute-evenly');
        const clearBtn = sizeSection.querySelector('.clear-allocation');

        if (distributeBtn) {
            // Remove existing event listeners
            distributeBtn.replaceWith(distributeBtn.cloneNode(true));
            const newDistributeBtn = sizeSection.querySelector('.distribute-evenly');

            newDistributeBtn.addEventListener('click', () => {
                this.distributeStockEvenly(sizeSection, colorIndex);
            });
        }

        if (clearBtn) {
            // Remove existing event listeners
            clearBtn.replaceWith(clearBtn.cloneNode(true));
            const newClearBtn = sizeSection.querySelector('.clear-allocation');

            newClearBtn.addEventListener('click', () => {
                this.clearAllocation(sizeSection, colorIndex);
            });
        }
    }

    distributeStockEvenly(sizeSection, colorIndex) {
        const colorData = this.colorStockData[colorIndex];
        if (!colorData || colorData.totalStock <= 0) return;

        const sizeInputs = sizeSection.querySelectorAll('.size-stock-input');
        if (sizeInputs.length === 0) return;

        const stockPerSize = Math.floor(colorData.totalStock / sizeInputs.length);
        const remainder = colorData.totalStock % sizeInputs.length;

        // Ensure allocations object exists
        if (!this.sizeAllocations[colorIndex]) {
            this.sizeAllocations[colorIndex] = {};
        }

        // Clear current allocations
        this.sizeAllocations[colorIndex] = {};

        // Distribute stock evenly
        sizeInputs.forEach((input, index) => {
            const allocation = stockPerSize + (index < remainder ? 1 : 0);
            input.value = allocation;

            // Update tracking
            const sizeIndex = input.dataset.sizeIndex;
            this.sizeAllocations[colorIndex][sizeIndex] = allocation;

            // Add visual feedback
            this.addInputFeedback(input, true);
        });

        // Update color data and displays
        this.recalculateColorStockData(colorIndex);
        this.updateStockDisplay(sizeSection, colorIndex);

        // Show success message
        this.showTemporaryMessage(sizeSection, 'Stock distributed evenly across all sizes!', 'success');
    }

    clearAllocation(sizeSection, colorIndex) {
        const sizeInputs = sizeSection.querySelectorAll('.size-stock-input');

        // Clear all inputs
        sizeInputs.forEach(input => {
            input.value = '0';
            this.addInputFeedback(input, true);
        });

        // Clear tracking
        this.sizeAllocations[colorIndex] = {};

        // Update displays
        this.recalculateColorStockData(colorIndex);
        this.updateStockDisplay(sizeSection, colorIndex);

        // Show success message
        this.showTemporaryMessage(sizeSection, 'All size allocations cleared!', 'info');
    }

    recalculateColorStockData(colorIndex) {
        const colorData = this.colorStockData[colorIndex];
        if (!colorData) return;

        // Ensure allocations object exists
        if (!this.sizeAllocations[colorIndex]) {
            this.sizeAllocations[colorIndex] = {};
        }

        // Recalculate allocated stock
        const allocations = this.sizeAllocations[colorIndex];
        const totalAllocated = Object.values(allocations).reduce((sum, val) => {
            const numVal = parseInt(val) || 0;
            return sum + numVal;
        }, 0);

        colorData.allocatedStock = totalAllocated;
        colorData.remainingStock = Math.max(0, colorData.totalStock - totalAllocated);
    }

    showTemporaryMessage(sizeSection, message, type = 'info') {
        const messageContainer = document.createElement('div');
        const typeClasses = {
            success: 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800 text-green-700 dark:text-green-300',
            info: 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800 text-blue-700 dark:text-blue-300',
            warning: 'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200 dark:border-yellow-800 text-yellow-700 dark:text-yellow-300'
        };

        messageContainer.className = `temporary-message mt-2 p-2 rounded border text-sm ${typeClasses[type] || typeClasses.info} transition-all duration-200`;
        messageContainer.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span>${message}</span>
            </div>
        `;

        // Insert after suggestions
        const suggestions = sizeSection.querySelector('.stock-suggestions');
        if (suggestions) {
            suggestions.parentNode.insertBefore(messageContainer, suggestions.nextSibling);
        } else {
            sizeSection.appendChild(messageContainer);
        }

        // Auto-remove after 3 seconds
        setTimeout(() => {
            if (messageContainer.parentNode) {
                messageContainer.style.opacity = '0';
                setTimeout(() => {
                    if (messageContainer.parentNode) {
                        messageContainer.remove();
                    }
                }, 200);
            }
        }, 3000);
    }

    showInstantAlert(input, message, type = 'info') {
        // Remove any existing instant alerts for this input
        const existingAlert = input.parentNode.querySelector('.instant-alert');
        if (existingAlert) {
            existingAlert.remove();
        }

        const alertContainer = document.createElement('div');
        const typeClasses = {
            success: 'bg-green-100 dark:bg-green-900/30 border-green-300 dark:border-green-700 text-green-800 dark:text-green-200',
            info: 'bg-blue-100 dark:bg-blue-900/30 border-blue-300 dark:border-blue-700 text-blue-800 dark:text-blue-200',
            warning: 'bg-yellow-100 dark:bg-yellow-900/30 border-yellow-300 dark:border-yellow-700 text-yellow-800 dark:text-yellow-200',
            error: 'bg-red-100 dark:bg-red-900/30 border-red-300 dark:border-red-700 text-red-800 dark:text-red-200'
        };

        const icons = {
            success: 'fa-check-circle',
            info: 'fa-info-circle',
            warning: 'fa-exclamation-triangle',
            error: 'fa-times-circle'
        };

        alertContainer.className = `instant-alert absolute z-10 mt-1 p-2 rounded-md border text-xs shadow-lg ${typeClasses[type] || typeClasses.info} transition-all duration-200 transform scale-95 opacity-0`;
        alertContainer.style.minWidth = '200px';
        alertContainer.innerHTML = `
            <div class="flex items-center">
                <i class="fas ${icons[type] || icons.info} mr-2 flex-shrink-0"></i>
                <span class="flex-1">${message}</span>
            </div>
        `;

        // Position the alert relative to the input
        input.parentNode.style.position = 'relative';
        input.parentNode.appendChild(alertContainer);

        // Animate in
        setTimeout(() => {
            alertContainer.classList.remove('scale-95', 'opacity-0');
            alertContainer.classList.add('scale-100', 'opacity-100');
        }, 10);

        // Auto-remove after 3 seconds
        setTimeout(() => {
            if (alertContainer.parentNode) {
                alertContainer.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    if (alertContainer.parentNode) {
                        alertContainer.remove();
                    }
                }, 200);
            }
        }, 3000);
    }

    getColorIndex(colorItem) {
        const nameInput = colorItem.querySelector('input[name*="[name]"]') ||
                         colorItem.querySelector('select[name*="[name]"]');
        if (!nameInput) return 0;

        const match = nameInput.name.match(/\[(\d+)\]/);
        return match ? parseInt(match[1]) : 0;
    }

    // Public method to refresh size allocations when colors or sizes change
    refreshAllocations() {
        document.querySelectorAll('.color-item').forEach(colorItem => {
            const colorSelect = colorItem.querySelector('.color-name-select');
            const stockInput = colorItem.querySelector('.color-stock-input');

            if (colorSelect && colorSelect.value && stockInput && stockInput.value) {
                this.updateColorStockData(colorItem, colorSelect.value, parseInt(stockInput.value) || 0);
                this.showSizesForColor(colorItem, colorSelect.value);
            }
        });
    }

    // Public method to get current allocation data
    getAllocationData() {
        return {
            colorStockData: this.colorStockData,
            sizeAllocations: this.sizeAllocations
        };
    }

    // Debug method to verify calculations
    debugCalculations(colorIndex) {
        const colorData = this.colorStockData[colorIndex];
        const allocations = this.sizeAllocations[colorIndex] || {};

        console.log(`Debug for Color Index ${colorIndex}:`);
        console.log('Color Data:', colorData);
        console.log('Size Allocations:', allocations);

        const calculatedTotal = Object.values(allocations).reduce((sum, val) => {
            const numVal = parseInt(val) || 0;
            return sum + numVal;
        }, 0);

        console.log('Calculated Total:', calculatedTotal);
        console.log('Stored Allocated:', colorData?.allocatedStock);
        console.log('Remaining:', colorData?.remainingStock);
        console.log('Total Stock:', colorData?.totalStock);

        return {
            colorData,
            allocations,
            calculatedTotal,
            isConsistent: calculatedTotal === colorData?.allocatedStock
        };
    }
}

// Initialize the system when the script loads
let dynamicColorSizeManager;

document.addEventListener('DOMContentLoaded', function() {
    dynamicColorSizeManager = new DynamicColorSizeManager();

    // Make it globally available
    window.dynamicColorSizeManager = dynamicColorSizeManager;
});

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = DynamicColorSizeManager;
}
