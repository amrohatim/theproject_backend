/**
 * Color Picker Implementation
 * Enhances color input fields with an interactive color picker
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Coloris with our preferred settings
    Coloris({
        themeMode: 'auto',
        alpha: true,
        format: 'hex',
        focusInput: true,
        clearButton: true,
        clearLabel: 'Clear',
        swatches: [
            '#FF0000', // Red
            '#FF4500', // Orange Red
            '#FFA500', // Orange
            '#FFD700', // Gold
            '#FFFF00', // Yellow
            '#9ACD32', // Yellow Green
            '#008000', // Green
            '#00FFFF', // Cyan
            '#0000FF', // Blue
            '#4B0082', // Indigo
            '#800080', // Purple
            '#FF00FF', // Magenta
            '#FFC0CB', // Pink
            '#A52A2A', // Brown
            '#808080', // Gray
            '#FFFFFF', // White
            '#000000'  // Black
        ]
    });

    // Apply color picker to all color code inputs
    initializeColorPickers();

    // Set up a mutation observer to watch for dynamically added color inputs
    setupColorPickerObserver();
});

/**
 * Initialize color pickers for all color code inputs
 */
function initializeColorPickers() {
    // Find all color code inputs in the colors section
    const colorInputs = document.querySelectorAll('input[name$="[color_code]"], .color-code-input');

    colorInputs.forEach(input => {
        // Add data-coloris attribute to enable the color picker
        input.setAttribute('data-coloris', '');

        // Add event listener to update visual feedback when color changes
        input.addEventListener('input', updateColorVisualFeedback);

        // Initialize visual feedback for existing values
        updateColorVisualFeedback.call(input);

        // Add click listener to color preview to open color picker
        const colorPreview = input.parentElement.querySelector('.color-preview');
        if (colorPreview) {
            colorPreview.addEventListener('click', () => {
                input.click();
            });
        }
    });
}

/**
 * Update the visual feedback for a color input
 */
function updateColorVisualFeedback() {
    // 'this' refers to the input element
    const colorValue = this.value;

    // Check if we have the new layout with color-preview
    let colorPreview = this.parentElement.querySelector('.color-preview');

    if (colorPreview) {
        // New layout with dedicated color preview
        if (colorValue) {
            colorPreview.style.backgroundColor = colorValue;
            colorPreview.style.display = 'block';
        } else {
            colorPreview.style.backgroundColor = '#ffffff';
        }
    } else {
        // Fallback to old layout - create color swatch if needed
        let colorSwatch = this.parentElement.querySelector('.color-swatch');

        if (!colorSwatch) {
            colorSwatch = document.createElement('div');
            colorSwatch.className = 'color-swatch absolute right-10 top-1/2 transform -translate-y-1/2 w-6 h-6 rounded-full border border-gray-300 dark:border-gray-600';
            this.parentElement.style.position = 'relative';
            this.parentElement.appendChild(colorSwatch);

            // Adjust input padding to make room for the swatch
            this.style.paddingRight = '3rem';
        }

        // Update the color swatch background
        if (colorValue) {
            colorSwatch.style.backgroundColor = colorValue;
            colorSwatch.style.display = 'block';
        } else {
            colorSwatch.style.display = 'none';
        }
    }
}

/**
 * Set up a mutation observer to watch for dynamically added color inputs
 */
function setupColorPickerObserver() {
    // Get the container where new color items will be added
    const colorsContainer = document.getElementById('colors-container');
    
    if (!colorsContainer) return;
    
    // Create a new observer
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                // New nodes were added, check if they contain color inputs
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) { // Element node
                        const newColorInputs = node.querySelectorAll('input[name$="[color_code]"], .color-code-input');

                        newColorInputs.forEach(input => {
                            // Add data-coloris attribute to enable the color picker
                            input.setAttribute('data-coloris', '');

                            // Add event listener to update visual feedback when color changes
                            input.addEventListener('input', updateColorVisualFeedback);

                            // Initialize visual feedback
                            updateColorVisualFeedback.call(input);

                            // Add click listener to color preview to open color picker
                            const colorPreview = input.parentElement.querySelector('.color-preview');
                            if (colorPreview) {
                                colorPreview.addEventListener('click', () => {
                                    input.click();
                                });
                            }
                        });
                    }
                });
            }
        });
    });
    
    // Start observing the container for added nodes
    observer.observe(colorsContainer, { childList: true });
}

// Listen for color picker events
document.addEventListener('coloris:pick', event => {
    // Update the input field with the selected color
    const input = event.target;
    input.value = event.detail.color;
    
    // Trigger the input event to update the visual feedback
    input.dispatchEvent(new Event('input', { bubbles: true }));
});
