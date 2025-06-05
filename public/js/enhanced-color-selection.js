/**
 * Enhanced Color Selection Implementation
 * Provides automatic hex value population when color names are selected
 * Follows the same pattern as enhanced-size-selection.js
 */

// Color data mapping - exact color names to hex codes
const COLOR_DATA = {
    'Red': '#FF0000',
    'Crimson': '#DC143C',
    'FireBrick': '#B22222',
    'DarkRed': '#8B0000',
    'IndianRed': '#CD5C5C',
    'LightCoral': '#F08080',
    'Salmon': '#FA8072',
    'DarkSalmon': '#E9967A',
    'LightSalmon': '#FFA07A',
    'Orange': '#FFA500',
    'DarkOrange': '#FF8C00',
    'Coral': '#FF7F50',
    'Tomato': '#FF6347',
    'Gold': '#FFD700',
    'Yellow': '#FFFF00',
    'LightYellow': '#FFFFE0',
    'LemonChiffon': '#FFFACD',
    'Khaki': '#F0E68C',
    'DarkKhaki': '#BDB76B',
    'Green': '#008000',
    'Lime': '#00FF00',
    'ForestGreen': '#228B22',
    'DarkGreen': '#006400',
    'SeaGreen': '#2E8B57',
    'MediumSeaGreen': '#3CB371',
    'LightGreen': '#90EE90',
    'PaleGreen': '#98FB98',
    'SpringGreen': '#00FF7F',
    'MediumSpringGreen': '#00FA9A',
    'YellowGreen': '#9ACD32',
    'Olive': '#808000',
    'DarkOliveGreen': '#556B2F',
    'Blue': '#0000FF',
    'MediumBlue': '#0000CD',
    'DarkBlue': '#00008B',
    'Navy': '#000080',
    'SkyBlue': '#87CEEB',
    'LightSkyBlue': '#87CEFA',
    'DeepSkyBlue': '#00BFFF',
    'DodgerBlue': '#1E90FF',
    'SteelBlue': '#4682B4',
    'CornflowerBlue': '#6495ED',
    'RoyalBlue': '#4169E1',
    'LightBlue': '#ADD8E6',
    'PowderBlue': '#B0E0E6',
    'Purple': '#800080',
    'MediumPurple': '#9370DB',
    'BlueViolet': '#8A2BE2',
    'Violet': '#EE82EE',
    'Orchid': '#DA70D6',
    'Magenta': '#FF00FF',
    'Fuchsia': '#FF00FF',
    'DeepPink': '#FF1493',
    'HotPink': '#FF69B4',
    'LightPink': '#FFB6C1',
    'PaleVioletRed': '#DB7093',
    'Brown': '#A52A2A',
    'SaddleBrown': '#8B4513',
    'Sienna': '#A0522D',
    'Chocolate': '#D2691E',
    'Peru': '#CD853F',
    'Tan': '#D2B48C',
    'RosyBrown': '#BC8F8F',
    'SandyBrown': '#F4A460',
    'BurlyWood': '#DEB887',
    'Wheat': '#F5DEB3',
    'NavajoWhite': '#FFDEAD',
    'Black': '#000000',
    'DimGray': '#696969',
    'Gray': '#808080',
    'DarkGray': '#A9A9A9',
    'Silver': '#C0C0C0',
    'LightGray': '#D3D3D3',
    'Gainsboro': '#DCDCDC',
    'WhiteSmoke': '#F5F5F5',
    'White': '#FFFFFF'
};

class EnhancedColorSelection {
    constructor() {
        this.initializeEventListeners();
        this.initializeColorDropdownStyles();
    }

    initializeEventListeners() {
        // Listen for color name selection changes
        document.addEventListener('change', (e) => {
            if (e.target.classList.contains('color-name-select')) {
                this.handleColorSelection(e.target);
            }
        });
    }

    initializeColorDropdownStyles() {
        // Add custom CSS for enhanced color dropdown styling
        if (!document.getElementById('enhanced-color-dropdown-styles')) {
            const style = document.createElement('style');
            style.id = 'enhanced-color-dropdown-styles';
            style.textContent = `
                /* Custom Color Dropdown Styles */
                .custom-color-dropdown {
                    position: relative;
                    width: 100%;
                }

                .custom-color-dropdown-trigger {
                    width: 100%;
                    padding: 8px 40px 8px 12px;
                    border: 1px solid #d1d5db;
                    border-radius: 6px;
                    background: white;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    font-size: 14px;
                    transition: all 0.2s ease;
                }

                .custom-color-dropdown-trigger:hover {
                    border-color: #6366f1;
                }

                .custom-color-dropdown-trigger:focus {
                    outline: none;
                    border-color: #6366f1;
                    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
                }

                .custom-color-dropdown-arrow {
                    position: absolute;
                    right: 12px;
                    top: 50%;
                    transform: translateY(-50%);
                    width: 0;
                    height: 0;
                    border-left: 4px solid transparent;
                    border-right: 4px solid transparent;
                    border-top: 4px solid #6b7280;
                    transition: transform 0.2s ease;
                }

                .custom-color-dropdown.open .custom-color-dropdown-arrow {
                    transform: translateY(-50%) rotate(180deg);
                }

                .custom-color-dropdown-menu {
                    position: absolute;
                    top: 100%;
                    left: 0;
                    right: 0;
                    min-width: 420px;
                    background: white;
                    border: 1px solid #d1d5db;
                    border-radius: 6px;
                    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
                    z-index: 1000;
                    max-height: 450px;
                    display: none;
                    overflow: hidden;
                }

                .color-search-container {
                    padding: 12px 16px;
                    border-bottom: 1px solid #e5e7eb;
                    background: #f9fafb;
                }

                .color-search-input {
                    width: 100%;
                    padding: 8px 12px;
                    border: 1px solid #d1d5db;
                    border-radius: 6px;
                    font-size: 14px;
                    outline: none;
                    transition: border-color 0.2s ease;
                }

                .color-search-input:focus {
                    border-color: #6366f1;
                    box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.1);
                }

                .color-options-container {
                    max-height: 360px;
                    overflow-y: auto;
                    padding: 12px;
                }

                .color-options-grid {
                    display: grid;
                    grid-template-columns: repeat(3, 1fr);
                    gap: 12px;
                }

                .no-results-message {
                    padding: 16px 12px;
                    text-align: center;
                    color: #6b7280;
                    font-size: 14px;
                    font-style: italic;
                }

                .custom-color-dropdown.open .custom-color-dropdown-menu {
                    display: block;
                }

                /* Responsive behavior for smaller screens */
                @media (max-width: 768px) {
                    .custom-color-dropdown-menu {
                        min-width: 320px;
                        max-height: 400px;
                        left: 50%;
                        right: auto;
                        transform: translateX(-50%);
                        width: 90vw;
                        max-width: 420px;
                    }

                    .color-options-container {
                        max-height: 320px;
                        padding: 10px;
                    }

                    .color-options-grid {
                        gap: 10px;
                    }
                }

                @media (max-width: 480px) {
                    .custom-color-dropdown-menu {
                        min-width: 280px;
                        max-height: 350px;
                        width: 95vw;
                        max-width: 350px;
                    }

                    .color-options-container {
                        max-height: 280px;
                        padding: 8px;
                    }

                    .color-options-grid {
                        gap: 8px;
                    }
                }

                .custom-color-dropdown-option {
                    padding: 14px 10px;
                    cursor: pointer;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    gap: 8px;
                    transition: all 0.15s ease;
                    border: 1px solid #e5e7eb;
                    border-radius: 8px;
                    background: #ffffff;
                    min-height: 90px;
                    text-align: center;
                }

                .custom-color-dropdown-option:hover {
                    background-color: #f9fafb;
                    border-color: #6366f1;
                    transform: translateY(-1px);
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                }

                .custom-color-dropdown-option.selected {
                    background-color: #eef2ff;
                    border-color: #6366f1;
                    box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
                }

                .color-swatch {
                    width: 24px;
                    height: 24px;
                    border-radius: 4px;
                    border: 1px solid #d1d5db;
                    flex-shrink: 0;
                    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
                    transition: transform 0.15s ease;
                }

                .custom-color-dropdown-option:hover .color-swatch {
                    transform: scale(1.1);
                }

                .color-name {
                    font-weight: 500;
                    color: #374151;
                    font-size: 12px;
                    line-height: 1.2;
                    margin: 0;
                    word-break: break-word;
                    max-width: 100%;
                }

                .color-hex {
                    font-size: 10px;
                    color: #6b7280;
                    font-family: 'Courier New', monospace;
                    font-weight: 500;
                    background: #f3f4f6;
                    padding: 2px 4px;
                    border-radius: 3px;
                    border: 1px solid #e5e7eb;
                    margin: 0;
                    white-space: nowrap;
                }

                .selected-color-display {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    flex: 1;
                }

                .selected-color-swatch {
                    width: 16px;
                    height: 16px;
                    border-radius: 2px;
                    border: 1px solid #d1d5db;
                    flex-shrink: 0;
                }

                .selected-color-text {
                    color: #374151;
                    font-weight: 500;
                }

                .placeholder-text {
                    color: #9ca3af;
                }

                /* Dark mode styles */
                .dark .custom-color-dropdown-trigger {
                    background: #374151;
                    border-color: #4b5563;
                    color: #f3f4f6;
                }

                .dark .custom-color-dropdown-menu {
                    background: #374151;
                    border-color: #4b5563;
                }

                .dark .color-search-container {
                    background: #4b5563;
                    border-color: #6b7280;
                }

                .dark .color-search-input {
                    background: #374151;
                    border-color: #6b7280;
                    color: #f3f4f6;
                }

                .dark .color-search-input:focus {
                    border-color: #6366f1;
                    box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
                }

                .dark .custom-color-dropdown-option {
                    border-color: #4b5563;
                    background: #374151;
                }

                .dark .custom-color-dropdown-option:hover {
                    background-color: #4b5563;
                    border-color: #6366f1;
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
                }

                .dark .custom-color-dropdown-option.selected {
                    background-color: #4338ca;
                    border-color: #6366f1;
                    box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.3);
                }

                .dark .color-name {
                    color: #f3f4f6;
                }

                .dark .color-hex {
                    background: #4b5563;
                    border-color: #6b7280;
                    color: #d1d5db;
                }

                .dark .no-results-message {
                    color: #9ca3af;
                }

                .dark .selected-color-text {
                    color: #f3f4f6;
                }

                .dark .color-swatch,
                .dark .selected-color-swatch {
                    border-color: #4b5563;
                }

                /* Hidden select for form submission */
                .hidden-color-select {
                    position: absolute;
                    opacity: 0;
                    pointer-events: none;
                    z-index: -1;
                }
            `;
            document.head.appendChild(style);
        }
    }

    handleColorSelection(selectElement) {
        const selectedColorName = selectElement.value;
        const colorItem = selectElement.closest('.color-item');

        if (!selectedColorName || !colorItem) return;

        // Get the hex code for the selected color
        const hexCode = COLOR_DATA[selectedColorName];

        if (hexCode) {
            // Update the color code input
            const colorCodeInput = colorItem.querySelector('input[name*="[color_code]"]');
            if (colorCodeInput) {
                colorCodeInput.value = hexCode;

                // Trigger input event to update color picker and visual feedback
                colorCodeInput.dispatchEvent(new Event('input', { bubbles: true }));

                // Update color swatch if it exists
                this.updateColorSwatch(colorCodeInput, hexCode);
            }
        }
    }

    updateColorSwatch(input, hexCode) {
        // Find or create color swatch
        let colorSwatch = input.parentElement.querySelector('.color-swatch');

        if (!colorSwatch) {
            colorSwatch = document.createElement('div');
            colorSwatch.className = 'color-swatch absolute right-10 top-1/2 transform -translate-y-1/2 w-6 h-6 rounded-full border border-gray-300 dark:border-gray-600';
            input.parentElement.style.position = 'relative';
            input.parentElement.appendChild(colorSwatch);

            // Adjust input padding to make room for the swatch
            input.style.paddingRight = '3rem';
        }

        // Update the color swatch background
        colorSwatch.style.backgroundColor = hexCode;
        colorSwatch.style.display = 'block';
    }

    createCustomColorDropdown(name, selectedValue = '', isRequired = true) {
        const dropdownId = `custom-color-dropdown-${Math.random().toString(36).substring(2, 11)}`;
        const selectedColor = selectedValue ? COLOR_DATA[selectedValue] : null;

        const optionsHtml = Object.keys(COLOR_DATA).map(colorName => {
            const hexCode = COLOR_DATA[colorName];
            const isSelected = selectedValue === colorName ? 'selected' : '';
            return `
                <div class="custom-color-dropdown-option ${isSelected}" data-value="${colorName}" data-hex="${hexCode}" data-search-text="${colorName.toLowerCase()}">
                    <div class="color-swatch" style="background-color: ${hexCode};"></div>
                    <div class="color-name">${colorName}</div>
                    <div class="color-hex">${hexCode}</div>
                </div>
            `;
        }).join('');

        const triggerContent = selectedColor ? `
            <div class="selected-color-display">
                <div class="selected-color-swatch" style="background-color: ${selectedColor};"></div>
                <span class="selected-color-text">${selectedValue}</span>
            </div>
        ` : '<span class="placeholder-text">Select Color</span>';

        return `
            <div class="custom-color-dropdown" id="${dropdownId}">
                <div class="custom-color-dropdown-trigger" tabindex="0">
                    ${triggerContent}
                    <div class="custom-color-dropdown-arrow"></div>
                </div>
                <div class="custom-color-dropdown-menu">
                    <div class="color-search-container">
                        <input type="text" class="color-search-input" placeholder="Search colors..." autocomplete="off">
                    </div>
                    <div class="color-options-container">
                        <div class="color-options-grid">
                            ${optionsHtml}
                        </div>
                        <div class="no-results-message" style="display: none;">No colors found matching your search.</div>
                    </div>
                </div>
                <select name="${name}" class="hidden-color-select color-name-select" ${isRequired ? 'required' : ''}>
                    <option value="">Select Color</option>
                    ${Object.keys(COLOR_DATA).map(colorName =>
                        `<option value="${colorName}" ${selectedValue === colorName ? 'selected' : ''}>${colorName}</option>`
                    ).join('')}
                </select>
            </div>
        `;
    }

    initializeCustomDropdown(dropdownElement) {
        const trigger = dropdownElement.querySelector('.custom-color-dropdown-trigger');
        const hiddenSelect = dropdownElement.querySelector('.hidden-color-select');
        const searchInput = dropdownElement.querySelector('.color-search-input');
        const noResultsMessage = dropdownElement.querySelector('.no-results-message');
        let allOptions = dropdownElement.querySelectorAll('.custom-color-dropdown-option');

        // Toggle dropdown
        trigger.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            this.closeAllDropdowns();
            dropdownElement.classList.toggle('open');

            // Focus search input when dropdown opens
            if (dropdownElement.classList.contains('open')) {
                setTimeout(() => searchInput.focus(), 100);
            }
        });

        // Handle keyboard navigation
        trigger.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.closeAllDropdowns();
                dropdownElement.classList.toggle('open');

                // Focus search input when dropdown opens
                if (dropdownElement.classList.contains('open')) {
                    setTimeout(() => searchInput.focus(), 100);
                }
            }
        });

        // Search functionality
        searchInput.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase().trim();
            let visibleCount = 0;

            allOptions.forEach(option => {
                const searchText = option.dataset.searchText;
                const colorName = option.querySelector('.color-name').textContent.toLowerCase();
                const colorHex = option.querySelector('.color-hex').textContent.toLowerCase();

                const matches = searchText.includes(searchTerm) ||
                               colorName.includes(searchTerm) ||
                               colorHex.includes(searchTerm);

                if (matches) {
                    option.style.display = 'flex';
                    visibleCount++;
                } else {
                    option.style.display = 'none';
                }
            });

            // Show/hide no results message
            if (visibleCount === 0 && searchTerm !== '') {
                noResultsMessage.style.display = 'block';
            } else {
                noResultsMessage.style.display = 'none';
            }
        });

        // Clear search when dropdown closes
        const clearSearch = () => {
            searchInput.value = '';
            allOptions.forEach(option => {
                option.style.display = 'flex';
            });
            noResultsMessage.style.display = 'none';
        };

        // Prevent search input clicks from closing dropdown
        searchInput.addEventListener('click', (e) => {
            e.stopPropagation();
        });

        // Handle option selection
        const handleOptionClick = (option) => {
            const value = option.dataset.value;
            const hex = option.dataset.hex;

            // Update hidden select
            hiddenSelect.value = value;
            hiddenSelect.dispatchEvent(new Event('change', { bubbles: true }));

            // Update trigger display
            trigger.innerHTML = `
                <div class="selected-color-display">
                    <div class="selected-color-swatch" style="background-color: ${hex};"></div>
                    <span class="selected-color-text">${value}</span>
                </div>
                <div class="custom-color-dropdown-arrow"></div>
            `;

            // Update selected state
            allOptions.forEach(opt => opt.classList.remove('selected'));
            option.classList.add('selected');

            // Close dropdown and clear search
            dropdownElement.classList.remove('open');
            clearSearch();

            // Trigger color selection handler
            this.handleColorSelection(hiddenSelect);
        };

        // Add click handlers to initial options
        allOptions.forEach(option => {
            option.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                handleOptionClick(option);
            });
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!dropdownElement.contains(e.target)) {
                dropdownElement.classList.remove('open');
                clearSearch();
            }
        });

        // Handle keyboard navigation in search
        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                dropdownElement.classList.remove('open');
                clearSearch();
                trigger.focus();
            } else if (e.key === 'Enter') {
                // Select first visible option
                const firstVisible = Array.from(allOptions).find(option =>
                    option.style.display !== 'none' && option.style.display !== ''
                );
                if (firstVisible) {
                    handleOptionClick(firstVisible);
                }
            }
        });
    }

    closeAllDropdowns() {
        document.querySelectorAll('.custom-color-dropdown.open').forEach(dropdown => {
            dropdown.classList.remove('open');
        });
    }

    // Method to enhance existing dropdowns with custom styling
    enhanceExistingDropdowns() {
        document.querySelectorAll('.color-name-select').forEach(select => {
            if (!select.classList.contains('enhanced') && !select.classList.contains('hidden-color-select')) {
                this.replaceWithCustomDropdown(select);
            }
        });
    }

    replaceWithCustomDropdown(selectElement) {
        const name = selectElement.name;
        const selectedValue = selectElement.value;
        const isRequired = selectElement.hasAttribute('required');

        // Create custom dropdown HTML
        const customDropdownHtml = this.createCustomColorDropdown(name, selectedValue, isRequired);

        // Replace the select element
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = customDropdownHtml;
        const customDropdown = tempDiv.firstElementChild;

        selectElement.parentNode.replaceChild(customDropdown, selectElement);

        // Initialize the custom dropdown
        this.initializeCustomDropdown(customDropdown);

        // Mark as enhanced
        customDropdown.classList.add('enhanced');
    }

    // Method to handle dynamically added color items
    handleDynamicColorItem(colorItem) {
        const select = colorItem.querySelector('.color-name-select');
        if (select && !select.classList.contains('enhanced') && !select.classList.contains('hidden-color-select')) {
            this.replaceWithCustomDropdown(select);
        }
    }
}

// Initialize the enhanced color selection when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    const colorSelection = new EnhancedColorSelection();

    // Enhance existing dropdowns after a short delay to ensure DOM is fully loaded
    setTimeout(() => {
        colorSelection.enhanceExistingDropdowns();
    }, 100);

    // Make the color selection instance globally available for dynamic content
    window.enhancedColorSelection = colorSelection;
});

// Export for use in other scripts if needed
window.EnhancedColorSelection = EnhancedColorSelection;
window.COLOR_DATA = COLOR_DATA;
