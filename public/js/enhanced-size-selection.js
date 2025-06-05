/**
 * Enhanced Size Selection JavaScript
 * Provides category-based size selection for products
 */

// Size data mappings
const SIZE_DATA = {
    clothes: {
        'XXS': 'Extra Extra Small',
        'XS': 'Extra Small',
        'S': 'Small',
        'M': 'Medium',
        'L': 'Large',
        'XL': 'Extra Large',
        'XXL': 'Extra Extra Large',
        '3XL': 'Triple Extra Large',
        '4XL': 'Quadruple Extra Large',
        '5XL': 'Quintuple Extra Large'
    },
    shoes: {
        16: '9.7cm', 17: '10.4cm', 18: '11.0cm', 19: '11.7cm', 20: '12.3cm',
        21: '13.0cm', 22: '13.7cm', 23: '14.3cm', 24: '15.0cm', 25: '15.7cm',
        26: '16.3cm', 27: '17.0cm', 28: '17.7cm', 29: '18.3cm', 30: '19.0cm',
        31: '19.7cm', 32: '20.3cm', 33: '21.0cm', 34: '21.7cm', 35: '22.5cm',
        36: '23.0cm', 37: '23.5cm', 38: '24.0cm', 39: '24.5cm', 40: '25.0cm',
        41: '25.5cm', 42: '26.0cm', 43: '26.5cm', 44: '27.0cm', 45: '27.5cm',
        46: '28.0cm', 47: '28.5cm', 48: '29.0cm'
    },
    hats: {
        40: 'Infant (0–6 months)', 42: 'Infant (0–6 months)', 44: 'Infant (6–12 months)',
        46: 'Toddler (6–12 months)', 48: 'Toddler (1–2 years)', 50: 'Child (2–4 years)',
        52: 'Child (4–6 years)', 54: 'Child (6–8 years)', 55: 'Youth (8–10 years)',
        56: 'Youth/Adult XS', 57: 'Adult S', 58: 'Adult M', 59: 'Adult M/L',
        60: 'Adult L', 61: 'Adult XL', 62: 'Adult XL', 63: 'Adult XXL', 64: 'Adult XXL'
    }
};

class EnhancedSizeSelection {
    constructor() {
        this.initializeEventListeners();
    }

    initializeEventListeners() {
        // Listen for size category changes
        document.addEventListener('change', (e) => {
            if (e.target.classList.contains('size-category-select')) {
                this.handleCategoryChange(e.target);
            }
        });

        // Listen for size selection changes
        document.addEventListener('change', (e) => {
            if (e.target.classList.contains('size-selection-dropdown')) {
                this.handleSizeSelection(e.target);
            }
        });
    }

    handleCategoryChange(selectElement) {
        const category = selectElement.value;
        const sizeItem = selectElement.closest('.size-item');
        const sizeSelectionContainer = sizeItem.querySelector('.size-selection-container');

        if (!category) {
            // Clear the container if no category is selected
            sizeSelectionContainer.innerHTML = `
                <div class="col-span-6 flex items-center justify-center text-gray-500 dark:text-gray-400">
                    <p>Please select a size category</p>
                </div>
            `;
            return;
        }

        sizeSelectionContainer.innerHTML = this.generateSizeSelection(category, sizeItem);
    }

    handleSizeSelection(selectElement) {
        const category = selectElement.closest('.size-item').querySelector('.size-category-select').value;
        const selectedValue = selectElement.value;
        const sizeItem = selectElement.closest('.size-item');

        if (!selectedValue) return;

        // Update the hidden inputs based on category
        this.updateSizeFields(category, selectedValue, sizeItem);
    }

    updateSizeFields(category, selectedValue, sizeItem) {
        const nameInput = sizeItem.querySelector('input[name*="[name]"]');
        const valueInput = sizeItem.querySelector('input[name*="[value]"]');
        const additionalInfoInput = sizeItem.querySelector('input[name*="[additional_info]"]');

        switch (category) {
            case 'clothes':
                nameInput.value = SIZE_DATA.clothes[selectedValue];
                valueInput.value = selectedValue;
                additionalInfoInput.value = '';
                break;
            case 'shoes':
                nameInput.value = 'EU';
                valueInput.value = selectedValue;
                additionalInfoInput.value = SIZE_DATA.shoes[selectedValue];
                break;
            case 'hats':
                nameInput.value = 'EU';
                valueInput.value = selectedValue;
                additionalInfoInput.value = SIZE_DATA.hats[selectedValue];
                break;
        }
    }

    generateSizeSelection(category, sizeItem) {
        const index = this.getSizeIndex(sizeItem);

        switch (category) {
            case 'clothes':
                return this.generateClothesSelection(index);
            case 'shoes':
                return this.generateShoesSelection(index);
            case 'hats':
                return this.generateHatsSelection(index);
            default:
                return `
                    <div class="col-span-6 flex items-center justify-center text-gray-500 dark:text-gray-400">
                        <p>Please select a valid size category</p>
                    </div>
                `;
        }
    }

    generateClothesSelection(index) {
        const options = Object.keys(SIZE_DATA.clothes).map(size =>
            `<option value="${size}">${size} (${SIZE_DATA.clothes[size]})</option>`
        ).join('');

        return `
            <div class="col-span-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Clothing Size</label>
                <select class="size-selection-dropdown focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                    <option value="">Select Size</option>
                    ${options}
                </select>
            </div>
        `;
    }

    generateShoesSelection(index) {
        const options = Object.keys(SIZE_DATA.shoes).map(size =>
            `<option value="${size}">EU ${size} (${SIZE_DATA.shoes[size]})</option>`
        ).join('');

        return `
            <div class="col-span-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">EU Shoe Size</label>
                <select class="size-selection-dropdown focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                    <option value="">Select Size</option>
                    ${options}
                </select>
            </div>
        `;
    }

    generateHatsSelection(index) {
        const options = Object.keys(SIZE_DATA.hats).map(size =>
            `<option value="${size}">EU ${size} (${SIZE_DATA.hats[size]})</option>`
        ).join('');

        return `
            <div class="col-span-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">EU Hat Size</label>
                <select class="size-selection-dropdown focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                    <option value="">Select Size</option>
                    ${options}
                </select>
            </div>
        `;
    }

    getSizeIndex(sizeItem) {
        const nameInput = sizeItem.querySelector('input[name*="[name]"]');
        if (nameInput) {
            const match = nameInput.name.match(/\[(\d+)\]/);
            return match ? match[1] : '0';
        }
        return '0';
    }

    // Method to create enhanced size item HTML
    static createEnhancedSizeItem(index) {
        return `
            <div class="size-item border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-3">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Size Category</label>
                        <select class="size-category-select focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" required>
                            <option value="">Select Category</option>
                            <option value="clothes">Clothes</option>
                            <option value="shoes">Shoes</option>
                            <option value="hats">Hats</option>
                        </select>
                    </div>
                    <div class="size-selection-container col-span-6 grid grid-cols-6 gap-4">
                        <div class="col-span-6 flex items-center justify-center text-gray-500 dark:text-gray-400">
                            <p>Please select a size category</p>
                        </div>
                        <input type="hidden" name="sizes[${index}][name]" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" required>
                        <input type="hidden" name="sizes[${index}][value]" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Price Adjustment</label>
                        <input type="number" step="0.01" name="sizes[${index}][price_adjustment]" placeholder="0.00" value="0" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                    </div>
                    <div class="col-span-1 flex items-end justify-center">
                        <button type="button" class="remove-item text-red-500 hover:text-red-700">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="grid grid-cols-12 gap-4 mt-4">
                    <div class="col-span-3">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Additional Info</label>
                        <input type="text" name="sizes[${index}][additional_info]" placeholder="Foot length, age group, etc." class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Stock</label>
                        <input type="number" name="sizes[${index}][stock]" placeholder="10" value="0" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Order</label>
                        <input type="number" name="sizes[${index}][display_order]" placeholder="0" value="${index}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                    </div>
                    <div class="col-span-5">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="sizes[${index}][is_default]" name="sizes[${index}][is_default]" type="checkbox" class="default-size-checkbox focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded" value="1">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="sizes[${index}][is_default]" class="font-medium text-gray-700 dark:text-gray-300">Default Size</label>
                                <p class="text-gray-500 dark:text-gray-400">Set as the default size option</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
}

// Make SIZE_DATA globally available for other scripts
window.SIZE_DATA = SIZE_DATA;

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    new EnhancedSizeSelection();
});
