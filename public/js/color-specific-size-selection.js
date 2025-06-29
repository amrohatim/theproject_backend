/**
 * Color-Specific Size Selection Management
 * Handles dynamic size category selection and size options for each color
 */

class ColorSpecificSizeSelection {
    constructor() {
        this.sizeCategories = {
            clothes: {
                sizes: [
                    { name: 'XXS', value: 'XXS', symbol: '👶' },
                    { name: 'XS', value: 'XS', symbol: '🧒' },
                    { name: 'S', value: 'S', symbol: '👦' },
                    { name: 'M', value: 'M', symbol: '👨' },
                    { name: 'L', value: 'L', symbol: '👨‍🦱' },
                    { name: 'XL', value: 'XL', symbol: '👨‍🦲' },
                    { name: 'XXL', value: 'XXL', symbol: '🧔' },
                    { name: '3XL', value: '3XL', symbol: '👨‍🦳' },
                    { name: '4XL', value: '4XL', symbol: '🧙‍♂️' },
                    { name: '5XL', value: '5XL', symbol: '🎅' }
                ]
            },
            shoes: {
                sizes: [
                    { name: 'EU 16', value: 'EU 16', footLength: '9.7 cm' },
                    { name: 'EU 17', value: 'EU 17', footLength: '10.4 cm' },
                    { name: 'EU 18', value: 'EU 18', footLength: '11.0 cm' },
                    { name: 'EU 19', value: 'EU 19', footLength: '11.7 cm' },
                    { name: 'EU 20', value: 'EU 20', footLength: '12.3 cm' },
                    { name: 'EU 21', value: 'EU 21', footLength: '13.0 cm' },
                    { name: 'EU 22', value: 'EU 22', footLength: '13.7 cm' },
                    { name: 'EU 23', value: 'EU 23', footLength: '14.3 cm' },
                    { name: 'EU 24', value: 'EU 24', footLength: '15.0 cm' },
                    { name: 'EU 25', value: 'EU 25', footLength: '15.7 cm' },
                    { name: 'EU 26', value: 'EU 26', footLength: '16.3 cm' },
                    { name: 'EU 27', value: 'EU 27', footLength: '17.0 cm' },
                    { name: 'EU 28', value: 'EU 28', footLength: '17.7 cm' },
                    { name: 'EU 29', value: 'EU 29', footLength: '18.3 cm' },
                    { name: 'EU 30', value: 'EU 30', footLength: '19.0 cm' },
                    { name: 'EU 31', value: 'EU 31', footLength: '19.7 cm' },
                    { name: 'EU 32', value: 'EU 32', footLength: '20.3 cm' },
                    { name: 'EU 33', value: 'EU 33', footLength: '21.0 cm' },
                    { name: 'EU 34', value: 'EU 34', footLength: '21.7 cm' },
                    { name: 'EU 35', value: 'EU 35', footLength: '22.3 cm' },
                    { name: 'EU 36', value: 'EU 36', footLength: '23.0 cm' },
                    { name: 'EU 37', value: 'EU 37', footLength: '23.7 cm' },
                    { name: 'EU 38', value: 'EU 38', footLength: '24.3 cm' },
                    { name: 'EU 39', value: 'EU 39', footLength: '25.0 cm' },
                    { name: 'EU 40', value: 'EU 40', footLength: '25.7 cm' },
                    { name: 'EU 41', value: 'EU 41', footLength: '26.3 cm' },
                    { name: 'EU 42', value: 'EU 42', footLength: '27.0 cm' },
                    { name: 'EU 43', value: 'EU 43', footLength: '27.7 cm' },
                    { name: 'EU 44', value: 'EU 44', footLength: '28.3 cm' },
                    { name: 'EU 45', value: 'EU 45', footLength: '29.0 cm' },
                    { name: 'EU 46', value: 'EU 46', footLength: '29.7 cm' },
                    { name: 'EU 47', value: 'EU 47', footLength: '30.3 cm' },
                    { name: 'EU 48', value: 'EU 48', footLength: '31.0 cm' }
                ]
            },
            hats: {
                sizes: [
                    { name: 'EU 40', value: 'EU 40', ageGroup: 'Newborn (0-3 months)' },
                    { name: 'EU 42', value: 'EU 42', ageGroup: 'Baby (3-6 months)' },
                    { name: 'EU 44', value: 'EU 44', ageGroup: 'Baby (6-12 months)' },
                    { name: 'EU 46', value: 'EU 46', ageGroup: 'Toddler (1-2 years)' },
                    { name: 'EU 48', value: 'EU 48', ageGroup: 'Toddler (2-3 years)' },
                    { name: 'EU 50', value: 'EU 50', ageGroup: 'Child (3-5 years)' },
                    { name: 'EU 52', value: 'EU 52', ageGroup: 'Child (5-8 years)' },
                    { name: 'EU 54', value: 'EU 54', ageGroup: 'Child (8-12 years)' },
                    { name: 'EU 56', value: 'EU 56', ageGroup: 'Teen (12-16 years)' },
                    { name: 'EU 58', value: 'EU 58', ageGroup: 'Adult Small' },
                    { name: 'EU 60', value: 'EU 60', ageGroup: 'Adult Medium' },
                    { name: 'EU 62', value: 'EU 62', ageGroup: 'Adult Large' },
                    { name: 'EU 64', value: 'EU 64', ageGroup: 'Adult Extra Large' }
                ]
            }
        };

        this.init();
    }

    init() {
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Listen for size category changes in color-specific size items
        document.addEventListener('change', (e) => {
            if (e.target.classList.contains('color-size-category-select')) {
                this.handleCategorySizeSelection(e.target);
            }
        });

        // Listen for size selection changes
        document.addEventListener('change', (e) => {
            if (e.target.classList.contains('color-size-option')) {
                this.handleSizeSelection(e.target);
            }
        });
    }

    handleCategorySizeSelection(selectElement) {
        const category = selectElement.value;
        const sizeItem = selectElement.closest('.color-size-item');
        const sizeContainer = sizeItem.querySelector('.color-size-selection-container');
        const nameInput = sizeItem.querySelector('.size-name-input');
        const valueInput = sizeItem.querySelector('.size-value-input');
        const additionalInfoInput = sizeItem.querySelector('input[name*="[additional_info]"]');

        if (!category) {
            sizeContainer.innerHTML = `
                <div class="flex items-center justify-center text-gray-500 dark:text-gray-400 text-xs">
                    <p>Select category first</p>
                </div>
            `;
            nameInput.value = '';
            valueInput.value = '';
            if (additionalInfoInput) additionalInfoInput.value = '';
            return;
        }

        const sizes = this.sizeCategories[category]?.sizes || [];
        
        sizeContainer.innerHTML = `
            <select class="color-size-option focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm text-xs border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" required>
                <option value="">Select Size</option>
                ${sizes.map(size => `
                    <option value="${size.name}" data-value="${size.value}" data-additional="${this.getAdditionalInfo(category, size)}">
                        ${size.name} ${this.getSizeDisplayInfo(category, size)}
                    </option>
                `).join('')}
            </select>
        `;
    }

    handleSizeSelection(selectElement) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const sizeItem = selectElement.closest('.color-size-item');
        const nameInput = sizeItem.querySelector('.size-name-input');
        const valueInput = sizeItem.querySelector('.size-value-input');
        const additionalInfoInput = sizeItem.querySelector('input[name*="[additional_info]"]');

        if (selectedOption.value) {
            nameInput.value = selectedOption.value;
            valueInput.value = selectedOption.dataset.value || selectedOption.value;
            
            if (additionalInfoInput && selectedOption.dataset.additional) {
                additionalInfoInput.value = selectedOption.dataset.additional;
            }
        } else {
            nameInput.value = '';
            valueInput.value = '';
            if (additionalInfoInput) additionalInfoInput.value = '';
        }
    }

    getSizeDisplayInfo(category, size) {
        switch (category) {
            case 'clothes':
                return size.symbol ? `${size.symbol}` : '';
            case 'shoes':
                return size.footLength ? `(${size.footLength})` : '';
            case 'hats':
                return size.ageGroup ? `(${size.ageGroup})` : '';
            default:
                return '';
        }
    }

    getAdditionalInfo(category, size) {
        switch (category) {
            case 'shoes':
                return size.footLength || '';
            case 'hats':
                return size.ageGroup || '';
            default:
                return '';
        }
    }

    // Method to handle dynamically added size items
    handleDynamicSizeItem(sizeItem) {
        const categorySelect = sizeItem.querySelector('.color-size-category-select');
        if (categorySelect) {
            // Re-setup event listeners for the new item
            this.setupEventListeners();
        }
    }
}

// Initialize the color-specific size selection system
document.addEventListener('DOMContentLoaded', function() {
    window.colorSpecificSizeSelection = new ColorSpecificSizeSelection();
});
