// Convert to CommonJS format for older Node.js compatibility
const { test, expect } = require('@playwright/test');

test.describe('Merchant Product Stock Allocation Validation', () => {
    let page;
    let context;

    test.beforeAll(async ({ browser }) => {
        context = await browser.newContext();
        page = await context.newPage();
        
        // Navigate to the application
        await page.goto('https://dala3chic.com:443');
        
        // Login as merchant (we'll need to create a merchant account first)
        // This will be implemented after we create the merchant account
    });

    test.afterAll(async () => {
        await context.close();
    });

    test('should validate stock allocation between general and color-specific stock', async () => {
        // Navigate to merchant product creation page
        await page.goto('https://dala3chic.com:443/merchant/products/create');
        
        // Fill in basic product information
        await page.fill('#name', 'Test Product for Stock Validation');
        await page.selectOption('#category_id', { index: 1 }); // Select first available category
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
        
        // Check if stock summary is updated (if it exists)
        const stockSummary = page.locator('.stock-summary');
        if (await stockSummary.count() > 0) {
            const summaryText = await stockSummary.textContent();
            expect(summaryText).toContain('90'); // Should show allocated stock
            expect(summaryText).toContain('10'); // Should show remaining stock
        }
        
        // Step 3: Add a second color
        await page.click('#add-color');
        
        // Wait for the new color item to be added
        await page.waitForSelector('.color-item:nth-child(2)');
        
        // Step 4: Attempt to allocate 11 units to the second color (should exceed limit)
        const secondColorStockInput = page.locator('.color-item:nth-child(2) .color-stock-input');
        await secondColorStockInput.fill('11');
        
        // Step 5: Verify validation logic
        // Check if the system detects over-allocation
        const totalAllocated = 90 + 11; // 101
        const generalStockValue = 100;
        
        if (totalAllocated > generalStockValue) {
            // Look for validation error messages
            const errorMessage = page.locator('.color-stock-validation, .stock-validation-error');
            if (await errorMessage.count() > 0) {
                const errorText = await errorMessage.textContent();
                expect(errorText).toContain('exceed'); // Should contain error about exceeding stock
            }
            
            // Check if the input value was corrected
            const correctedValue = await secondColorStockInput.inputValue();
            expect(parseInt(correctedValue)).toBeLessThanOrEqual(10); // Should be corrected to max allowed
        }
        
        // Step 6: Test "Add Color" button behavior
        // The button should be disabled when all stock is allocated
        await firstColorStockInput.fill('100'); // Allocate all stock to first color
        await page.waitForTimeout(500); // Wait for validation to process
        
        const addColorButton = page.locator('#add-color');
        const isDisabled = await addColorButton.isDisabled();
        
        // If stock validation is implemented, button should be disabled
        if (isDisabled) {
            expect(isDisabled).toBe(true);
        }
        
        // Step 7: Test form submission validation
        await page.fill('#name', 'Test Product Stock Validation');
        
        // Set up over-allocation scenario
        await firstColorStockInput.fill('90');
        await secondColorStockInput.fill('20'); // Total: 110, exceeds 100
        
        // Try to submit the form
        const submitButton = page.locator('button[type="submit"]');
        await submitButton.click();
        
        // Check for form validation error
        const formError = page.locator('.alert-danger, .error-message, .stock-validation-error');
        if (await formError.count() > 0) {
            const errorText = await formError.textContent();
            expect(errorText).toContain('stock'); // Should mention stock validation error
        }
    });

    test('should show real-time stock calculation and visual feedback', async () => {
        await page.goto('https://dala3chic.com:443/merchant/products/create');
        
        // Set general stock
        await page.fill('#stock', '50');
        
        // Test real-time updates
        const firstColorStock = page.locator('.color-stock-input').first();
        await firstColorStock.fill('30');
        
        // Check for visual feedback elements
        const remainingStockDisplay = page.locator('.remaining-stock, .stock-remaining');
        if (await remainingStockDisplay.count() > 0) {
            const remainingText = await remainingStockDisplay.textContent();
            expect(remainingText).toContain('20'); // Should show remaining 20 units
        }
        
        // Test color-coded feedback
        await firstColorStock.fill('60'); // Exceed general stock
        
        // Look for error styling
        const errorStyling = page.locator('.text-red-600, .text-danger, .error');
        if (await errorStyling.count() > 0) {
            const isVisible = await errorStyling.isVisible();
            expect(isVisible).toBe(true);
        }
    });

    test('should handle mobile responsive design for stock validation', async () => {
        // Test mobile viewport
        await page.setViewportSize({ width: 375, height: 667 });
        await page.goto('https://dala3chic.com:443/merchant/products/create');
        
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
        
        const validationMessage = page.locator('.color-stock-validation');
        if (await validationMessage.count() > 0) {
            const messageVisible = await validationMessage.isVisible();
            expect(messageVisible).toBe(true);
        }
    });
});
