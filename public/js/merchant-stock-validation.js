/**
 * Merchant Stock Validation System
 * Implements real-time stock validation for merchant product forms
 * Hierarchy: General Stock -> Color Stock -> Size Stock
 */

class MerchantStockValidator {
    constructor() {
        this.generalStockInput = null;
        this.colorStockInputs = [];
        this.sizeStockInputs = [];
        this.alertTimeout = null;
        this.addColorButton = null;
        this.addColorTooltip = null;
        this.init();
    }

    init() {
        this.setupElements();
        this.setupEventListeners();
        this.setupMutationObserver();

        // Initial button state update
        setTimeout(() => {
            this.updateAddColorButtonState();
        }, 100);

        console.log('Merchant Stock Validation System initialized');
    }

    setupElements() {
        this.generalStockInput = document.getElementById('stock');
        this.addColorButton = document.getElementById('add-color');
        this.addColorTooltip = document.getElementById('add-color-tooltip');
        this.updateColorStockInputs();
        this.updateSizeStockInputs();
    }

    updateColorStockInputs() {
        this.colorStockInputs = Array.from(document.querySelectorAll('.color-stock-input'));
    }

    updateSizeStockInputs() {
        this.sizeStockInputs = Array.from(document.querySelectorAll('.size-stock-input'));
    }

    setupEventListeners() {
        // General stock validation
        if (this.generalStockInput) {
            this.generalStockInput.addEventListener('input', (e) => this.handleGeneralStockChange(e));
            this.generalStockInput.addEventListener('blur', (e) => this.handleGeneralStockChange(e));
        }

        // Color stock validation
        document.addEventListener('input', (e) => {
            if (e.target.classList.contains('color-stock-input')) {
                this.handleColorStockChange(e);
            }
        });

        document.addEventListener('blur', (e) => {
            if (e.target.classList.contains('color-stock-input')) {
                this.handleColorStockChange(e);
            }
        }, true);

        // Size stock validation
        document.addEventListener('input', (e) => {
            if (e.target.classList.contains('size-stock-input')) {
                this.handleSizeStockChange(e);
            }
        });

        document.addEventListener('blur', (e) => {
            if (e.target.classList.contains('size-stock-input')) {
                this.handleSizeStockChange(e);
            }
        }, true);
    }

    setupMutationObserver() {
        // Watch for dynamically added color/size inputs
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === 'childList') {
                    this.updateColorStockInputs();
                    this.updateSizeStockInputs();
                }
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    handleGeneralStockChange(e) {
        const generalStock = parseInt(e.target.value) || 0;
        
        // Ensure non-negative
        if (generalStock < 0) {
            e.target.value = 0;
            this.showAlert(e.target, 'Stock cannot be negative', 'error');
            return;
        }

        // Validate all color stocks against new general stock
        this.validateAllColorStocks(generalStock);

        // Update Add Color button state
        this.updateAddColorButtonState();
    }

    handleColorStockChange(e) {
        const colorStock = parseInt(e.target.value) || 0;
        const generalStock = parseInt(this.generalStockInput?.value) || 0;

        // Ensure non-negative
        if (colorStock < 0) {
            e.target.value = 0;
            this.showAlert(e.target, 'Stock cannot be negative', 'error');
            return;
        }

        // Validate against general stock
        const totalColorStock = this.calculateTotalColorStock();
        const currentColorStock = parseInt(e.target.value) || 0;
        const otherColorsStock = totalColorStock - currentColorStock;

        if (otherColorsStock + colorStock > generalStock) {
            const maxAllowed = Math.max(0, generalStock - otherColorsStock);
            e.target.value = maxAllowed;
            this.showAlert(e.target, 
                `Exceeds general stock limit! Auto-adjusted to ${maxAllowed}. General stock: ${generalStock}`, 
                'warning');
            this.addVisualFeedback(e.target, 'corrected');
        }

        // Validate all size stocks for this color
        this.validateSizeStocksForColor(e.target);

        // Update Add Color button state
        this.updateAddColorButtonState();
    }

    handleSizeStockChange(e) {
        const sizeStock = parseInt(e.target.value) || 0;
        
        // Ensure non-negative
        if (sizeStock < 0) {
            e.target.value = 0;
            this.showAlert(e.target, 'Stock cannot be negative', 'error');
            return;
        }

        // Find the parent color item and its stock
        const colorItem = e.target.closest('.color-item');
        if (!colorItem) return;

        const colorStockInput = colorItem.querySelector('.color-stock-input');
        if (!colorStockInput) return;

        const colorStock = parseInt(colorStockInput.value) || 0;
        
        // Calculate total size stock for this color
        const sizeInputs = colorItem.querySelectorAll('.size-stock-input');
        let totalSizeStock = 0;
        sizeInputs.forEach(input => {
            totalSizeStock += parseInt(input.value) || 0;
        });

        // Check if total size stock exceeds color stock
        if (totalSizeStock > colorStock) {
            const overAllocation = totalSizeStock - colorStock;
            const maxAllowed = Math.max(0, sizeStock - overAllocation);
            e.target.value = maxAllowed;
            this.showAlert(e.target, 
                `Exceeds color stock limit! Auto-adjusted to ${maxAllowed}. Color stock: ${colorStock}`, 
                'warning');
            this.addVisualFeedback(e.target, 'corrected');
        }
    }

    validateAllColorStocks(generalStock) {
        this.updateColorStockInputs();
        
        this.colorStockInputs.forEach(input => {
            const colorStock = parseInt(input.value) || 0;
            const totalColorStock = this.calculateTotalColorStock();
            const otherColorsStock = totalColorStock - colorStock;

            if (otherColorsStock + colorStock > generalStock) {
                const maxAllowed = Math.max(0, generalStock - otherColorsStock);
                input.value = maxAllowed;
                this.showAlert(input, 
                    `Auto-adjusted to ${maxAllowed} due to general stock limit`, 
                    'warning');
                this.addVisualFeedback(input, 'corrected');
            }

            // Also validate size stocks for this color
            this.validateSizeStocksForColor(input);
        });
    }

    validateSizeStocksForColor(colorStockInput) {
        const colorItem = colorStockInput.closest('.color-item');
        if (!colorItem) return;

        const colorStock = parseInt(colorStockInput.value) || 0;
        const sizeInputs = colorItem.querySelectorAll('.size-stock-input');
        
        let totalSizeStock = 0;
        sizeInputs.forEach(input => {
            totalSizeStock += parseInt(input.value) || 0;
        });

        // If total size stock exceeds color stock, adjust proportionally
        if (totalSizeStock > colorStock) {
            const ratio = colorStock / totalSizeStock;
            sizeInputs.forEach(input => {
                const currentValue = parseInt(input.value) || 0;
                const adjustedValue = Math.floor(currentValue * ratio);
                input.value = adjustedValue;
                this.addVisualFeedback(input, 'corrected');
            });
            
            if (sizeInputs.length > 0) {
                this.showAlert(sizeInputs[0], 
                    `Size stocks auto-adjusted proportionally to fit color stock limit of ${colorStock}`, 
                    'warning');
            }
        }
    }

    calculateTotalColorStock() {
        this.updateColorStockInputs();
        return this.colorStockInputs.reduce((total, input) => {
            return total + (parseInt(input.value) || 0);
        }, 0);
    }



    getAlertClasses(type) {
        const classes = {
            'error': 'text-red-700 bg-red-50 border-red-200 shadow-sm',
            'warning': 'text-yellow-700 bg-yellow-50 border-yellow-200 shadow-sm',
            'success': 'text-green-700 bg-green-50 border-green-200 shadow-sm',
            'info': 'text-blue-700 bg-blue-50 border-blue-200 shadow-sm'
        };
        return classes[type] || classes.info;
    }

    getAlertIcon(type) {
        const icons = {
            'error': 'fas fa-exclamation-triangle',
            'warning': 'fas fa-exclamation-circle',
            'success': 'fas fa-check-circle',
            'info': 'fas fa-info-circle'
        };
        return icons[type] || icons.info;
    }

    addVisualFeedback(element, type) {
        // Store original border style
        const originalBorder = element.style.border;
        const originalBackground = element.style.backgroundColor;

        // Remove existing feedback classes
        element.classList.remove('border-red-500', 'border-yellow-500', 'border-green-500',
                                 'bg-red-50', 'bg-yellow-50', 'bg-green-50');

        if (type === 'corrected') {
            // Show correction feedback with Discord theme colors
            element.style.transition = 'all 0.3s ease';
            element.style.border = '2px solid #f59e0b'; // Yellow border
            element.style.backgroundColor = '#fef3c7'; // Light yellow background

            // Add a subtle glow effect
            element.style.boxShadow = '0 0 0 3px rgba(245, 158, 11, 0.1)';

            setTimeout(() => {
                element.style.border = '2px solid #10b981'; // Green border
                element.style.backgroundColor = '#d1fae5'; // Light green background
                element.style.boxShadow = '0 0 0 3px rgba(16, 185, 129, 0.1)';

                setTimeout(() => {
                    // Restore original styling
                    element.style.border = originalBorder;
                    element.style.backgroundColor = originalBackground;
                    element.style.boxShadow = '';
                }, 2000);
            }, 1000);
        } else if (type === 'error') {
            // Error feedback
            element.style.transition = 'all 0.3s ease';
            element.style.border = '2px solid #ef4444';
            element.style.backgroundColor = '#fef2f2';
            element.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.1)';

            setTimeout(() => {
                element.style.border = originalBorder;
                element.style.backgroundColor = originalBackground;
                element.style.boxShadow = '';
            }, 3000);
        } else if (type === 'success') {
            // Success feedback
            element.style.transition = 'all 0.3s ease';
            element.style.border = '2px solid #10b981';
            element.style.backgroundColor = '#d1fae5';
            element.style.boxShadow = '0 0 0 3px rgba(16, 185, 129, 0.1)';

            setTimeout(() => {
                element.style.border = originalBorder;
                element.style.backgroundColor = originalBackground;
                element.style.boxShadow = '';
            }, 2000);
        }
    }

    // Enhanced alert with animation
    showAlert(element, message, type = 'info') {
        // Clear any existing alert timeout
        if (this.alertTimeout) {
            clearTimeout(this.alertTimeout);
        }

        // Remove existing alerts
        const existingAlert = element.parentNode.querySelector('.stock-validation-alert');
        if (existingAlert) {
            existingAlert.style.opacity = '0';
            existingAlert.style.transform = 'translateY(-10px)';
            setTimeout(() => existingAlert.remove(), 200);
        }

        // Create alert element with animation
        const alert = document.createElement('div');
        alert.className = `stock-validation-alert mt-2 text-xs p-3 rounded-md border ${this.getAlertClasses(type)}`;
        alert.style.opacity = '0';
        alert.style.transform = 'translateY(-10px)';
        alert.style.transition = 'all 0.3s ease';
        alert.innerHTML = `<i class="${this.getAlertIcon(type)} mr-2"></i>${message}`;

        // Insert after the input
        element.parentNode.insertBefore(alert, element.nextSibling);

        // Animate in
        setTimeout(() => {
            alert.style.opacity = '1';
            alert.style.transform = 'translateY(0)';
        }, 10);

        // Auto-remove after 4 seconds with animation
        this.alertTimeout = setTimeout(() => {
            if (alert.parentNode) {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.remove();
                    }
                }, 300);
            }
        }, 4000);
    }

    // Public method to refresh validation
    refreshValidation() {
        this.setupElements();
        if (this.generalStockInput) {
            this.handleGeneralStockChange({ target: this.generalStockInput });
        }
        // Update Add Color button state
        this.updateAddColorButtonState();
    }

    // Enhanced validation for form submission
    validateFormSubmission() {
        let hasErrors = false;
        const errors = [];

        // Check general stock vs total color stock
        const generalStock = parseInt(this.generalStockInput?.value) || 0;
        const totalColorStock = this.calculateTotalColorStock();

        if (totalColorStock > generalStock) {
            hasErrors = true;
            errors.push(`Total color stock (${totalColorStock}) exceeds general stock (${generalStock})`);
        }

        // Check each color's stock vs its size stocks
        this.colorStockInputs.forEach((colorInput, index) => {
            const colorItem = colorInput.closest('.color-item');
            if (!colorItem) return;

            const colorStock = parseInt(colorInput.value) || 0;
            const sizeInputs = colorItem.querySelectorAll('.size-stock-input');
            let totalSizeStock = 0;

            sizeInputs.forEach(sizeInput => {
                totalSizeStock += parseInt(sizeInput.value) || 0;
            });

            if (totalSizeStock > colorStock) {
                hasErrors = true;
                const colorName = colorItem.querySelector('.color-name-select')?.value || `Color ${index + 1}`;
                errors.push(`${colorName}: Size stock total (${totalSizeStock}) exceeds color stock (${colorStock})`);
            }
        });

        return { hasErrors, errors };
    }

    // Show comprehensive validation summary
    showValidationSummary() {
        const validation = this.validateFormSubmission();

        // Remove existing summary
        const existingSummary = document.querySelector('.stock-validation-summary');
        if (existingSummary) {
            existingSummary.remove();
        }

        if (validation.hasErrors) {
            const summary = document.createElement('div');
            summary.className = 'stock-validation-summary mt-4 p-4 bg-red-50 border border-red-200 rounded-md';
            summary.innerHTML = `
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-red-500 mt-1 mr-2"></i>
                    <div>
                        <h4 class="text-red-800 font-semibold mb-2">Stock Validation Errors:</h4>
                        <ul class="text-red-700 text-sm space-y-1">
                            ${validation.errors.map(error => `<li>• ${error}</li>`).join('')}
                        </ul>
                    </div>
                </div>
            `;

            // Insert before submit button
            const submitButton = document.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.parentNode.insertBefore(summary, submitButton);
            }
        }

        return validation;
    }

    // Auto-correct all stock levels to maintain hierarchy
    autoCorrectAllStocks() {
        const generalStock = parseInt(this.generalStockInput?.value) || 0;

        // First, correct color stocks
        const totalColorStock = this.calculateTotalColorStock();
        if (totalColorStock > generalStock) {
            // Proportionally reduce color stocks
            const ratio = generalStock / totalColorStock;
            this.colorStockInputs.forEach(input => {
                const currentValue = parseInt(input.value) || 0;
                const adjustedValue = Math.floor(currentValue * ratio);
                input.value = adjustedValue;
                this.addVisualFeedback(input, 'corrected');
            });
        }

        // Then, correct size stocks for each color
        this.colorStockInputs.forEach(colorInput => {
            this.validateSizeStocksForColor(colorInput);
        });

        this.showAlert(this.generalStockInput, 'All stock levels auto-corrected to maintain hierarchy', 'success');
    }

    // Get current stock allocation summary
    getStockSummary() {
        const generalStock = parseInt(this.generalStockInput?.value) || 0;
        const totalColorStock = this.calculateTotalColorStock();
        const remainingGeneralStock = generalStock - totalColorStock;

        const colorBreakdown = this.colorStockInputs.map((input, index) => {
            const colorItem = input.closest('.color-item');
            const colorName = colorItem?.querySelector('.color-name-select')?.value || `Color ${index + 1}`;
            const colorStock = parseInt(input.value) || 0;

            const sizeInputs = colorItem?.querySelectorAll('.size-stock-input') || [];
            const totalSizeStock = Array.from(sizeInputs).reduce((sum, sizeInput) => {
                return sum + (parseInt(sizeInput.value) || 0);
            }, 0);

            return {
                name: colorName,
                stock: colorStock,
                sizeStock: totalSizeStock,
                remaining: colorStock - totalSizeStock
            };
        });

        return {
            generalStock,
            totalColorStock,
            remainingGeneralStock,
            colorBreakdown
        };
    }

    // Add Color Button Management
    updateAddColorButtonState() {
        if (!this.addColorButton) return;

        const generalStock = parseInt(this.generalStockInput?.value) || 0;
        const totalColorStock = this.calculateTotalColorStock();
        const shouldDisable = totalColorStock >= generalStock && generalStock > 0;

        if (shouldDisable) {
            this.disableAddColorButton();
        } else {
            this.enableAddColorButton();
        }
    }

    disableAddColorButton() {
        if (!this.addColorButton) return;

        // Disable the button
        this.addColorButton.disabled = true;
        this.addColorButton.style.opacity = '0.5';
        this.addColorButton.style.cursor = 'not-allowed';
        this.addColorButton.style.pointerEvents = 'none';

        // Show tooltip on hover
        if (this.addColorTooltip) {
            this.addColorTooltip.style.display = 'block';

            // Add hover listeners to show/hide tooltip
            this.addColorButton.parentElement.addEventListener('mouseenter', this.showTooltip.bind(this));
            this.addColorButton.parentElement.addEventListener('mouseleave', this.hideTooltip.bind(this));
        }
    }

    enableAddColorButton() {
        if (!this.addColorButton) return;

        // Enable the button
        this.addColorButton.disabled = false;
        this.addColorButton.style.opacity = '1';
        this.addColorButton.style.cursor = 'pointer';
        this.addColorButton.style.pointerEvents = 'auto';

        // Hide tooltip
        if (this.addColorTooltip) {
            this.addColorTooltip.style.display = 'none';
            this.addColorTooltip.style.opacity = '0';

            // Remove hover listeners
            this.addColorButton.parentElement.removeEventListener('mouseenter', this.showTooltip.bind(this));
            this.addColorButton.parentElement.removeEventListener('mouseleave', this.hideTooltip.bind(this));
        }
    }

    showTooltip() {
        if (this.addColorTooltip && this.addColorButton.disabled) {
            this.addColorTooltip.style.opacity = '1';
        }
    }

    hideTooltip() {
        if (this.addColorTooltip) {
            this.addColorTooltip.style.opacity = '0';
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    window.merchantStockValidator = new MerchantStockValidator();

    // Add form submission validation
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const validation = window.merchantStockValidator.validateFormSubmission();
            if (validation.hasErrors) {
                e.preventDefault();
                window.merchantStockValidator.showValidationSummary();

                // Scroll to validation summary
                const summary = document.querySelector('.stock-validation-summary');
                if (summary) {
                    summary.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    }
});

// Also initialize if script is loaded after DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        if (!window.merchantStockValidator) {
            window.merchantStockValidator = new MerchantStockValidator();
        }
    });
} else {
    if (!window.merchantStockValidator) {
        window.merchantStockValidator = new MerchantStockValidator();
    }
}
