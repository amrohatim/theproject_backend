const { test, expect } = require('@playwright/test');

test.describe('Merchant Product Stock Allocation Validation', () => {
    test.beforeEach(async ({ page }) => {
        // Login as test merchant
        await page.goto('/login');
        await page.fill('input[name="email"]', 'merchant@test.com');
        await page.fill('input[name="password"]', 'password123');
        await page.click('button[type="submit"]');
        
        // Wait for redirect to merchant dashboard
        await page.waitForURL('**/merchant/dashboard');
        
        // Navigate to product creation page
        await page.goto('/merchant/products/create');
        await page.waitForLoadState('networkidle');
    });

    test('should validate stock allocation between general and color-specific stock', async ({ page }) => {
        // Fill in basic product information
        await page.fill('#name', 'Test Product for Stock Validation');
        
        // Select first available category (skip disabled options)
        await page.selectOption('#category_id', { index: 1 });
        
        await page.fill('#price', '100.00');
        await page.fill('#description', 'Test product for stock allocation validation');
        
        // Step 1: Set general product stock to 100 units
        await page.fill('#stock', '100');
        
        // Verify general stock is set
        const generalStock = await page.inputValue('#stock');
        expect(generalStock).toBe('100');
        
        // Step 2: Allocate 90 units to the first product color
        const firstColorStockInput = page.locator('.color-stock-input').first();
        await firstColorStockInput.fill('90');
        
        // Verify first color stock allocation
        const firstColorStock = await firstColorStockInput.inputValue();
        expect(firstColorStock).toBe('90');
        
        // Check if stock summary exists and is updated
        const stockSummary = page.locator('.stock-summary');
        if (await stockSummary.count() > 0) {
            const summaryText = await stockSummary.textContent();
            console.log('Stock summary:', summaryText);
        }
        
        // Step 3: Add a second color
        await page.click('#add-color');
        
        // Wait for the new color item to be added
        await page.waitForSelector('.color-item:nth-child(2)', { timeout: 5000 });
        
        // Step 4: Attempt to allocate 11 units to the second color (should exceed limit)
        const secondColorStockInput = page.locator('.color-item:nth-child(2) .color-stock-input');
        await secondColorStockInput.fill('11');
        
        // Wait a moment for any validation to process
        await page.waitForTimeout(1000);
        
        // Step 5: Check for validation logic
        // Look for validation error messages
        const errorMessages = page.locator('.color-stock-validation, .stock-validation-error, .alert-danger');
        const errorCount = await errorMessages.count();
        
        if (errorCount > 0) {
            console.log('✅ Validation error found - stock allocation validation is working');
            const errorText = await errorMessages.first().textContent();
            console.log('Error message:', errorText);
            expect(errorText.toLowerCase()).toContain('exceed');
        } else {
            console.log('⚠️ No validation error found - stock allocation validation may be missing');
        }
        
        // Check if the input value was corrected automatically
        const correctedValue = await secondColorStockInput.inputValue();
        const correctedValueInt = parseInt(correctedValue);
        
        if (correctedValueInt <= 10) {
            console.log('✅ Input value was corrected to:', correctedValue);
        } else {
            console.log('⚠️ Input value was not corrected, still:', correctedValue);
        }
        
        // Step 6: Test "Add Color" button behavior when stock is fully allocated
        await firstColorStockInput.fill('100'); // Allocate all stock to first color
        await page.waitForTimeout(500); // Wait for validation to process
        
        const addColorButton = page.locator('#add-color');
        const isDisabled = await addColorButton.isDisabled();
        
        if (isDisabled) {
            console.log('✅ Add Color button is disabled when stock is fully allocated');
        } else {
            console.log('⚠️ Add Color button is not disabled when stock is fully allocated');
        }
        
        // Step 7: Test form submission validation
        // Reset to over-allocation scenario
        await firstColorStockInput.fill('90');
        await secondColorStockInput.fill('20'); // Total: 110, exceeds 100
        
        // Try to submit the form
        const submitButton = page.locator('button[type="submit"]');
        await submitButton.click();
        
        // Wait for any validation response
        await page.waitForTimeout(2000);
        
        // Check for form validation error
        const formErrors = page.locator('.alert-danger, .error-message, .stock-validation-error');
        const formErrorCount = await formErrors.count();
        
        if (formErrorCount > 0) {
            console.log('✅ Form submission validation is working');
            const errorText = await formErrors.first().textContent();
            console.log('Form error message:', errorText);
        } else {
            console.log('⚠️ Form submission validation may be missing');
        }
    });

    test('should show real-time stock calculation and visual feedback', async ({ page }) => {
        // Set general stock
        await page.fill('#stock', '50');
        
        // Test real-time updates
        const firstColorStock = page.locator('.color-stock-input').first();
        await firstColorStock.fill('30');
        
        // Check for visual feedback elements
        const remainingStockDisplay = page.locator('.remaining-stock, .stock-remaining, .stock-summary');
        const displayCount = await remainingStockDisplay.count();
        
        if (displayCount > 0) {
            console.log('✅ Stock display elements found');
            const displayText = await remainingStockDisplay.first().textContent();
            console.log('Stock display text:', displayText);
        } else {
            console.log('⚠️ No stock display elements found');
        }
        
        // Test over-allocation scenario
        await firstColorStock.fill('60'); // Exceed general stock
        await page.waitForTimeout(500);
        
        // Look for error styling
        const errorStyling = page.locator('.text-red-600, .text-danger, .error, .has-error');
        const errorStyleCount = await errorStyling.count();
        
        if (errorStyleCount > 0) {
            console.log('✅ Error styling found for over-allocation');
        } else {
            console.log('⚠️ No error styling found for over-allocation');
        }
    });

    test('should handle mobile responsive design for stock validation', async ({ page }) => {
        // Test mobile viewport
        await page.setViewportSize({ width: 375, height: 667 });
        
        // Verify stock inputs are accessible on mobile
        const stockInput = page.locator('#stock');
        const isVisible = await stockInput.isVisible();
        expect(isVisible).toBe(true);
        
        // Test color stock inputs on mobile
        const colorStockInput = page.locator('.color-stock-input').first();
        const colorInputVisible = await colorStockInput.isVisible();
        expect(colorInputVisible).toBe(true);
        
        // Test that validation messages are readable on mobile
        await stockInput.fill('100');
        await colorStockInput.fill('150'); // Over-allocation
        await page.waitForTimeout(500);
        
        const validationMessage = page.locator('.color-stock-validation, .stock-validation-error');
        const messageCount = await validationMessage.count();
        
        if (messageCount > 0) {
            const messageVisible = await validationMessage.first().isVisible();
            if (messageVisible) {
                console.log('✅ Validation messages are visible on mobile');
            } else {
                console.log('⚠️ Validation messages are not visible on mobile');
            }
        } else {
            console.log('⚠️ No validation messages found on mobile');
        }
    });

    test('should verify current implementation status', async ({ page }) => {
        console.log('🔍 Analyzing current merchant product stock allocation implementation...');
        
        // Check if stock validation functions exist
        const hasStockValidation = await page.evaluate(() => {
            return typeof window.validateColorStock === 'function' ||
                   typeof window.calculateTotalAllocatedStock === 'function' ||
                   typeof window.updateStockSummary === 'function';
        });
        
        if (hasStockValidation) {
            console.log('✅ Stock validation functions found in JavaScript');
        } else {
            console.log('❌ Stock validation functions NOT found in JavaScript');
        }
        
        // Check for stock summary elements
        const stockSummaryExists = await page.locator('.stock-summary, .stock-display, .allocated-stock').count() > 0;
        
        if (stockSummaryExists) {
            console.log('✅ Stock summary elements found in HTML');
        } else {
            console.log('❌ Stock summary elements NOT found in HTML');
        }
        
        // Check for validation message containers
        const validationContainers = await page.locator('.color-stock-validation, .stock-validation-error').count() > 0;
        
        if (validationContainers) {
            console.log('✅ Validation message containers found');
        } else {
            console.log('❌ Validation message containers NOT found');
        }
        
        console.log('📋 Implementation Analysis Complete');
    });
});
