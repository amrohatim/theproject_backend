const { test, expect } = require('@playwright/test');

test.describe('Merchant New Color Size Management Refresh Fix', () => {
    let productId;

    test.beforeEach(async ({ page }) => {
        // Login as test merchant
        await page.goto('/login');
        await page.fill('input[name="email"]', 'merchant@test.com');
        await page.fill('input[name="password"]', 'password123');
        await page.click('button[type="submit"]');
        
        // Wait for redirect to merchant dashboard
        await page.waitForURL('**/merchant/dashboard');
        
        // Create a test product first
        await page.goto('/merchant/products/create');
        await page.waitForLoadState('networkidle');
        
        // Fill basic product information
        await page.fill('#name', 'Test Product for New Color Size Refresh');
        await page.selectOption('#category_id', { index: 1 });
        await page.fill('#price', '75.00');
        await page.fill('#stock', '200');
        await page.fill('#description', 'Test product for new color size refresh functionality');

        // Add a basic color variant first (required for product creation)
        await page.click('button:has-text("Add Color")');
        await page.waitForTimeout(1000);

        // Fill color information
        const colorNameSelect = page.locator('.color-name-select').first();
        await colorNameSelect.selectOption('Black');

        const colorStockInput = page.locator('.color-stock-input').first();
        await colorStockInput.fill('100');

        // Submit the product
        await page.click('button[type="submit"]');
        await page.waitForURL('**/merchant/products');
        
        // Get the product ID from the URL or find the created product
        await page.click('a:has-text("Test Product for New Color Size Refresh")');
        await page.waitForURL('**/merchant/products/*/edit');
        
        // Extract product ID from URL
        const url = page.url();
        productId = url.match(/\/products\/(\d+)\/edit/)[1];
    });

    test('should immediately show newly added size for a brand new color variant', async ({ page }) => {
        // Navigate to the product edit page
        await page.goto(`/merchant/products/${productId}/edit`);
        await page.waitForLoadState('networkidle');
        
        // Switch to Colors & Images tab
        await page.click('button:has-text("Colors & Images")');
        await page.waitForTimeout(1000);
        
        // Add a new color variant
        await page.click('button:has-text("Add Color")');
        await page.waitForTimeout(1000);
        
        // Fill in the new color details
        const colorNameSelect = page.locator('.color-name-select').last();
        await colorNameSelect.selectOption('Blue');
        
        const colorStockInput = page.locator('.color-stock-input').last();
        await colorStockInput.fill('50');
        
        // Wait for the size management section to appear
        await page.waitForTimeout(1000);
        
        // Verify that the size management section shows empty state
        const emptyStateText = page.locator('text=No sizes added').last();
        await expect(emptyStateText).toBeVisible();
        
        // Click "Add Size" button for this new color
        const addSizeButton = page.locator('button:has-text("Add Size")').last();
        await addSizeButton.click();
        await page.waitForTimeout(500);
        
        // Fill in size information
        await page.fill('input[placeholder="e.g., S, M, L, XL"]', 'Large');
        await page.fill('input[placeholder="e.g., 32, Medium, etc."]', 'L');
        await page.fill('input[placeholder="0"]:nth-of-type(1)', '25');
        await page.fill('input[placeholder="0.00"]', '3.00');
        
        // Check the "Available for purchase" checkbox
        await page.check('#new-size-available');
        
        // Submit the form
        await page.click('button:has-text("Add Size")');
        
        // Wait for the API call to complete and UI to update
        await page.waitForTimeout(2000);
        
        // CRITICAL TEST: Check if the newly added size appears immediately in the UI
        // This should NOT show "No sizes added" anymore
        const noSizesText = page.locator('text=No sizes added').last();
        await expect(noSizesText).not.toBeVisible();
        
        // The size should now be visible in the size management section
        const sizeItem = page.locator('.size-item').last();
        await expect(sizeItem).toBeVisible();
        
        // Check if the size details are displayed correctly
        const sizeName = page.locator('h6:has-text("Large")').last();
        await expect(sizeName).toBeVisible();
        
        const sizeValue = page.locator('text=Value: L').last();
        await expect(sizeValue).toBeVisible();
        
        const sizeStock = page.locator('text=Stock: 25').last();
        await expect(sizeStock).toBeVisible();
        
        // Verify that the modal is closed
        const modal = page.locator('.fixed.inset-0');
        await expect(modal).not.toBeVisible();
    });

    test('should handle multiple new colors with sizes correctly', async ({ page }) => {
        // Navigate to the product edit page
        await page.goto(`/merchant/products/${productId}/edit`);
        await page.waitForLoadState('networkidle');
        
        // Switch to Colors & Images tab
        await page.click('button:has-text("Colors & Images")');
        await page.waitForTimeout(1000);
        
        // Add first new color variant
        await page.click('button:has-text("Add Color")');
        await page.waitForTimeout(1000);
        
        // Fill in the first color details
        const firstColorSelect = page.locator('.color-name-select').last();
        await firstColorSelect.selectOption('Red');
        
        const firstColorStock = page.locator('.color-stock-input').last();
        await firstColorStock.fill('30');
        await page.waitForTimeout(1000);
        
        // Add a size to the first color
        const firstAddSizeButton = page.locator('button:has-text("Add Size")').last();
        await firstAddSizeButton.click();
        await page.waitForTimeout(500);
        
        await page.fill('input[placeholder="e.g., S, M, L, XL"]', 'Medium');
        await page.fill('input[placeholder="0"]:nth-of-type(1)', '15');
        await page.click('button:has-text("Add Size")');
        await page.waitForTimeout(2000);
        
        // Verify first size is visible
        const firstSizeName = page.locator('h6:has-text("Medium")').last();
        await expect(firstSizeName).toBeVisible();
        
        // Add second new color variant
        await page.click('button:has-text("Add Color")');
        await page.waitForTimeout(1000);
        
        // Fill in the second color details
        const secondColorSelect = page.locator('.color-name-select').last();
        await secondColorSelect.selectOption('Green');
        
        const secondColorStock = page.locator('.color-stock-input').last();
        await secondColorStock.fill('40');
        await page.waitForTimeout(1000);
        
        // Add a size to the second color
        const secondAddSizeButton = page.locator('button:has-text("Add Size")').last();
        await secondAddSizeButton.click();
        await page.waitForTimeout(500);
        
        await page.fill('input[placeholder="e.g., S, M, L, XL"]', 'Small');
        await page.fill('input[placeholder="0"]:nth-of-type(1)', '20');
        await page.click('button:has-text("Add Size")');
        await page.waitForTimeout(2000);
        
        // Verify second size is visible
        const secondSizeName = page.locator('h6:has-text("Small")').last();
        await expect(secondSizeName).toBeVisible();
        
        // Verify both sizes are still visible
        await expect(firstSizeName).toBeVisible();
        await expect(secondSizeName).toBeVisible();
    });

    test.afterEach(async ({ page }) => {
        // Clean up: delete the test product
        if (productId) {
            await page.goto('/merchant/products');
            await page.waitForLoadState('networkidle');
            
            // Find and delete the test product
            const deleteButton = page.locator(`tr:has-text("Test Product for New Color Size Refresh") .fa-trash`);
            if (await deleteButton.count() > 0) {
                page.on('dialog', async dialog => {
                    await dialog.accept();
                });
                await deleteButton.click();
                await page.waitForTimeout(1000);
            }
        }
    });
});
