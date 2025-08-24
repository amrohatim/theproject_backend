/**
 * Merchant Stock Validation Browser Tests
 * Tests the real-time stock validation system for merchant product forms
 */

const { test, expect } = require('@playwright/test');

// Test configuration
const BASE_URL = 'https://dala3chic.com';
const MERCHANT_LOGIN_URL = `${BASE_URL}/merchant/login`;
const PRODUCT_CREATE_URL = `${BASE_URL}/merchant/products/create`;
const PRODUCT_EDIT_URL = `${BASE_URL}/merchant/products/9/edit`;

// Test credentials (should be configured in environment)
const MERCHANT_CREDENTIALS = {
    email: process.env.MERCHANT_EMAIL || 'test@merchant.com',
    password: process.env.MERCHANT_PASSWORD || 'password123'
};

test.describe('Merchant Stock Validation System', () => {
    test.beforeEach(async ({ page }) => {
        // Login as merchant before each test
        await page.goto(MERCHANT_LOGIN_URL);
        await page.fill('input[name="email"]', MERCHANT_CREDENTIALS.email);
        await page.fill('input[name="password"]', MERCHANT_CREDENTIALS.password);
        await page.click('button[type="submit"]');
        
        // Wait for successful login
        await page.waitForURL('**/merchant/dashboard');
    });

    test.describe('Product Creation Form', () => {
        test.beforeEach(async ({ page }) => {
            await page.goto(PRODUCT_CREATE_URL);
            await page.waitForLoadState('networkidle');
        });

        test('should validate general stock against color stock totals', async ({ page }) => {
            // Set general stock to 100
            await page.fill('#stock', '100');
            
            // Set first color stock to 60
            await page.fill('.color-stock-input', '60');
            
            // Add another color and set stock to 50 (total would be 110, exceeding general stock)
            await page.click('#add-color');
            await page.waitForTimeout(500);
            
            const colorInputs = await page.locator('.color-stock-input').all();
            await colorInputs[1].fill('50');
            
            // The second color stock should be auto-corrected to 40
            await expect(colorInputs[1]).toHaveValue('40');
            
            // Check for validation alert
            const alert = page.locator('.stock-validation-alert').last();
            await expect(alert).toBeVisible();
            await expect(alert).toContainText('Auto-adjusted to 40');
        });

        test('should show visual feedback when stock is corrected', async ({ page }) => {
            // Set general stock
            await page.fill('#stock', '50');
            
            // Set color stock that exceeds general stock
            const colorInput = page.locator('.color-stock-input').first();
            await colorInput.fill('60');
            
            // Check that input gets visual feedback (border color change)
            await expect(colorInput).toHaveCSS('border-color', 'rgb(245, 158, 11)'); // Yellow border
            
            // Wait for transition to green
            await page.waitForTimeout(1200);
            await expect(colorInput).toHaveCSS('border-color', 'rgb(16, 185, 129)'); // Green border
        });

        test('should prevent negative stock values', async ({ page }) => {
            // Try to set negative general stock
            await page.fill('#stock', '-10');
            
            // Should be auto-corrected to 0
            await expect(page.locator('#stock')).toHaveValue('0');
            
            // Check for error alert
            const alert = page.locator('.stock-validation-alert');
            await expect(alert).toBeVisible();
            await expect(alert).toContainText('Stock cannot be negative');
        });

        test('should validate size stock against color stock', async ({ page }) => {
            // Set up basic product info
            await page.fill('#stock', '100');
            await page.fill('.color-stock-input', '50');
            
            // Select a color to enable size management
            await page.selectOption('.color-name-select', 'Red');
            await page.waitForTimeout(500);
            
            // Add size stock that exceeds color stock
            const sizeStockInput = page.locator('.size-stock-input').first();
            if (await sizeStockInput.isVisible()) {
                await sizeStockInput.fill('60');
                
                // Should be auto-corrected to not exceed color stock
                await expect(sizeStockInput).toHaveValue('50');
                
                // Check for validation alert
                const alert = page.locator('.stock-validation-alert').last();
                await expect(alert).toBeVisible();
                await expect(alert).toContainText('color stock limit');
            }
        });

        test('should show stock summary on general stock focus', async ({ page }) => {
            const stockInput = page.locator('#stock');
            await stockInput.click();
            
            // Stock summary should appear
            const summary = page.locator('#stock-summary-container');
            await expect(summary).toBeVisible();
            await expect(summary).toContainText('Stock Allocation Summary');
            
            // Click outside to hide summary
            await page.click('body');
            await page.waitForTimeout(300);
            await expect(summary).toBeHidden();
        });

        test('should prevent form submission with validation errors', async ({ page }) => {
            // Set up invalid stock configuration
            await page.fill('#stock', '50');
            await page.fill('.color-stock-input', '60'); // Exceeds general stock
            
            // Fill required fields
            await page.fill('#name', 'Test Product');
            await page.fill('#description', 'Test Description');
            await page.selectOption('#category_id', { index: 1 });
            await page.fill('#price', '100');
            
            // Try to submit form
            await page.click('button[type="submit"]');
            
            // Should show validation summary
            const validationSummary = page.locator('.stock-validation-summary');
            await expect(validationSummary).toBeVisible();
            await expect(validationSummary).toContainText('Stock Validation Errors');
            
            // Form should not be submitted (still on same page)
            await expect(page).toHaveURL(PRODUCT_CREATE_URL);
        });
    });

    test.describe('Product Edit Form', () => {
        test.beforeEach(async ({ page }) => {
            await page.goto(PRODUCT_EDIT_URL);
            await page.waitForLoadState('networkidle');
        });

        test('should validate existing product stock levels', async ({ page }) => {
            // Get current general stock value
            const generalStock = await page.locator('#stock').inputValue();
            const generalStockValue = parseInt(generalStock) || 0;
            
            // Try to set color stock higher than general stock
            const colorInput = page.locator('.color-stock-input').first();
            await colorInput.fill((generalStockValue + 10).toString());
            
            // Should be auto-corrected
            const correctedValue = await colorInput.inputValue();
            expect(parseInt(correctedValue)).toBeLessThanOrEqual(generalStockValue);
        });

        test('should maintain validation when adding new colors', async ({ page }) => {
            // Set general stock
            await page.fill('#stock', '100');
            
            // Add new color
            await page.click('#add-color');
            await page.waitForTimeout(500);
            
            // Set stock for new color that would exceed limit
            const colorInputs = await page.locator('.color-stock-input').all();
            const lastColorInput = colorInputs[colorInputs.length - 1];
            await lastColorInput.fill('150');
            
            // Should be auto-corrected
            const correctedValue = await lastColorInput.inputValue();
            expect(parseInt(correctedValue)).toBeLessThanOrEqual(100);
        });
    });

    test.describe('Mobile Responsiveness', () => {
        test.beforeEach(async ({ page }) => {
            // Set mobile viewport
            await page.setViewportSize({ width: 375, height: 667 });
        });

        test('should work correctly on mobile devices', async ({ page }) => {
            await page.goto(PRODUCT_CREATE_URL);
            await page.waitForLoadState('networkidle');
            
            // Test basic validation on mobile
            await page.fill('#stock', '50');
            await page.fill('.color-stock-input', '60');
            
            // Should still auto-correct on mobile
            await expect(page.locator('.color-stock-input')).toHaveValue('50');
            
            // Alert should be visible and properly sized
            const alert = page.locator('.stock-validation-alert');
            await expect(alert).toBeVisible();
            
            // Check that alert doesn't overflow on mobile
            const alertBox = await alert.boundingBox();
            const viewportWidth = 375;
            expect(alertBox.width).toBeLessThanOrEqual(viewportWidth - 20); // Account for margins
        });
    });

    test.describe('Performance and Edge Cases', () => {
        test('should handle rapid input changes', async ({ page }) => {
            await page.goto(PRODUCT_CREATE_URL);
            await page.waitForLoadState('networkidle');
            
            const stockInput = page.locator('#stock');
            const colorInput = page.locator('.color-stock-input');
            
            // Rapidly change values
            for (let i = 0; i < 5; i++) {
                await stockInput.fill((50 + i * 10).toString());
                await colorInput.fill((60 + i * 5).toString());
                await page.waitForTimeout(100);
            }
            
            // Final values should be valid
            const finalGeneralStock = parseInt(await stockInput.inputValue());
            const finalColorStock = parseInt(await colorInput.inputValue());
            expect(finalColorStock).toBeLessThanOrEqual(finalGeneralStock);
        });

        test('should handle zero and empty values correctly', async ({ page }) => {
            await page.goto(PRODUCT_CREATE_URL);
            await page.waitForLoadState('networkidle');
            
            // Test zero values
            await page.fill('#stock', '0');
            await page.fill('.color-stock-input', '0');
            
            // Should accept zero values
            await expect(page.locator('#stock')).toHaveValue('0');
            await expect(page.locator('.color-stock-input')).toHaveValue('0');
            
            // Test empty values
            await page.fill('#stock', '');
            await page.fill('.color-stock-input', '10');
            
            // Color stock should be corrected to 0 when general stock is empty
            await expect(page.locator('.color-stock-input')).toHaveValue('0');
        });
    });
});

// Helper function to wait for validation to complete
async function waitForValidation(page, timeout = 1000) {
    await page.waitForTimeout(timeout);
    await page.waitForFunction(() => {
        return !document.querySelector('.stock-validation-alert[style*="opacity: 0"]');
    });
}
