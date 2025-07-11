const { test, expect } = require('@playwright/test');

test.describe('Merchant Product Size Management - Advanced Scenarios', () => {
    let productId;

    test.beforeEach(async ({ page }) => {
        // Login as test merchant
        await page.goto('/login');
        await page.fill('input[name="email"]', 'merchant@test.com');
        await page.fill('input[name="password"]', 'password123');
        await page.click('button[type="submit"]');
        
        // Wait for redirect to merchant dashboard
        await page.waitForURL('**/merchant/dashboard');
        
        // Create a test product with multiple colors
        await page.goto('/merchant/products/create');
        await page.waitForLoadState('networkidle');
        
        // Fill basic product information
        await page.fill('#name', 'Multi-Color Product for Size Testing');
        await page.selectOption('#category_id', { index: 1 });
        await page.fill('#price', '75.00');
        await page.fill('#stock', '200');
        await page.fill('#description', 'Test product with multiple colors for size management');
        
        // Add first color variant
        await page.click('button:has-text("Add Color")');
        await page.waitForTimeout(1000);
        
        const firstColorSelect = page.locator('.color-name-select').first();
        await firstColorSelect.selectOption('Red');
        
        const firstColorStock = page.locator('.color-stock-input').first();
        await firstColorStock.fill('100');
        
        // Add second color variant
        await page.click('button:has-text("Add Color")');
        await page.waitForTimeout(1000);
        
        const secondColorSelect = page.locator('.color-name-select').nth(1);
        await secondColorSelect.selectOption('Blue');
        
        const secondColorStock = page.locator('.color-stock-input').nth(1);
        await secondColorStock.fill('100');
        
        // Submit the product
        await page.click('button[type="submit"]');
        await page.waitForURL('**/merchant/products');
        
        // Get the product ID
        await page.click('a:has-text("Multi-Color Product for Size Testing")');
        await page.waitForURL('**/merchant/products/*/edit');
        
        const url = page.url();
        productId = url.match(/\/products\/(\d+)\/edit/)[1];
    });

    test('should manage sizes independently for different color variants', async ({ page }) => {
        await page.goto(`/merchant/products/${productId}/edit`);
        await page.waitForLoadState('networkidle');
        
        // Switch to Colors & Images tab
        await page.click('button:has-text("Colors & Images")');
        await page.waitForTimeout(1000);
        
        // Add size to first color (Red)
        const firstColorSizeSection = page.locator('.size-management-container').first();
        await firstColorSizeSection.locator('button:has-text("Add Size")').click();
        await page.waitForTimeout(500);
        
        await page.fill('input[placeholder="e.g., S, M, L, XL"]', 'Small');
        await page.fill('input[placeholder="0"]:nth-of-type(1)', '30');
        await page.click('button:has-text("Add Size")');
        await page.waitForTimeout(1000);
        
        // Add size to second color (Blue)
        const secondColorSizeSection = page.locator('.size-management-container').nth(1);
        await secondColorSizeSection.locator('button:has-text("Add Size")').click();
        await page.waitForTimeout(500);
        
        await page.fill('input[placeholder="e.g., S, M, L, XL"]', 'Large');
        await page.fill('input[placeholder="0"]:nth-of-type(1)', '40');
        await page.click('button:has-text("Add Size")');
        await page.waitForTimeout(1000);
        
        // Verify sizes are added to correct colors
        const firstColorSize = firstColorSizeSection.locator('h6:has-text("Small")');
        await expect(firstColorSize).toBeVisible();
        
        const secondColorSize = secondColorSizeSection.locator('h6:has-text("Large")');
        await expect(secondColorSize).toBeVisible();
        
        // Verify sizes don't appear in wrong color sections
        const firstColorLarge = firstColorSizeSection.locator('h6:has-text("Large")');
        await expect(firstColorLarge).not.toBeVisible();
        
        const secondColorSmall = secondColorSizeSection.locator('h6:has-text("Small")');
        await expect(secondColorSmall).not.toBeVisible();
    });

    test('should validate duplicate size names within the same color', async ({ page }) => {
        await page.goto(`/merchant/products/${productId}/edit`);
        await page.waitForLoadState('networkidle');
        
        // Switch to Colors & Images tab
        await page.click('button:has-text("Colors & Images")');
        await page.waitForTimeout(1000);
        
        // Add first size
        const firstColorSizeSection = page.locator('.size-management-container').first();
        await firstColorSizeSection.locator('button:has-text("Add Size")').click();
        await page.waitForTimeout(500);
        
        await page.fill('input[placeholder="e.g., S, M, L, XL"]', 'Medium');
        await page.click('button:has-text("Add Size")');
        await page.waitForTimeout(1000);
        
        // Try to add another size with the same name
        await firstColorSizeSection.locator('button:has-text("Add Size")').click();
        await page.waitForTimeout(500);
        
        await page.fill('input[placeholder="e.g., S, M, L, XL"]', 'Medium');
        await page.click('button:has-text("Add Size")');
        await page.waitForTimeout(500);
        
        // Check for validation error
        const errorMessage = page.locator('.text-red-500:has-text("A size with this name already exists")');
        await expect(errorMessage).toBeVisible();
    });

    test('should allow same size names across different colors', async ({ page }) => {
        await page.goto(`/merchant/products/${productId}/edit`);
        await page.waitForLoadState('networkidle');
        
        // Switch to Colors & Images tab
        await page.click('button:has-text("Colors & Images")');
        await page.waitForTimeout(1000);
        
        // Add "Medium" size to first color
        const firstColorSizeSection = page.locator('.size-management-container').first();
        await firstColorSizeSection.locator('button:has-text("Add Size")').click();
        await page.waitForTimeout(500);
        
        await page.fill('input[placeholder="e.g., S, M, L, XL"]', 'Medium');
        await page.click('button:has-text("Add Size")');
        await page.waitForTimeout(1000);
        
        // Add "Medium" size to second color
        const secondColorSizeSection = page.locator('.size-management-container').nth(1);
        await secondColorSizeSection.locator('button:has-text("Add Size")').click();
        await page.waitForTimeout(500);
        
        await page.fill('input[placeholder="e.g., S, M, L, XL"]', 'Medium');
        await page.click('button:has-text("Add Size")');
        await page.waitForTimeout(1000);
        
        // Verify both sizes are created successfully
        const firstColorMedium = firstColorSizeSection.locator('h6:has-text("Medium")');
        await expect(firstColorMedium).toBeVisible();
        
        const secondColorMedium = secondColorSizeSection.locator('h6:has-text("Medium")');
        await expect(secondColorMedium).toBeVisible();
    });

    test('should handle price adjustments correctly', async ({ page }) => {
        await page.goto(`/merchant/products/${productId}/edit`);
        await page.waitForLoadState('networkidle');
        
        // Switch to Colors & Images tab
        await page.click('button:has-text("Colors & Images")');
        await page.waitForTimeout(1000);
        
        // Add size with positive price adjustment
        const sizeSection = page.locator('.size-management-container').first();
        await sizeSection.locator('button:has-text("Add Size")').click();
        await page.waitForTimeout(500);
        
        await page.fill('input[placeholder="e.g., S, M, L, XL"]', 'Premium');
        await page.fill('input[placeholder="0.00"]', '10.50');
        await page.click('button:has-text("Add Size")');
        await page.waitForTimeout(1000);
        
        // Verify positive price adjustment is displayed
        const positivePriceAdjustment = page.locator('text=+$10.5');
        await expect(positivePriceAdjustment).toBeVisible();
        
        // Add size with negative price adjustment
        await sizeSection.locator('button:has-text("Add Size")').click();
        await page.waitForTimeout(500);
        
        await page.fill('input[placeholder="e.g., S, M, L, XL"]', 'Discount');
        await page.fill('input[placeholder="0.00"]', '-5.25');
        await page.click('button:has-text("Add Size")');
        await page.waitForTimeout(1000);
        
        // Verify negative price adjustment is displayed
        const negativePriceAdjustment = page.locator('text=-$5.25');
        await expect(negativePriceAdjustment).toBeVisible();
    });

    test('should validate numeric inputs for stock and price adjustment', async ({ page }) => {
        await page.goto(`/merchant/products/${productId}/edit`);
        await page.waitForLoadState('networkidle');
        
        // Switch to Colors & Images tab
        await page.click('button:has-text("Colors & Images")');
        await page.waitForTimeout(1000);
        
        // Try to add size with invalid stock
        const sizeSection = page.locator('.size-management-container').first();
        await sizeSection.locator('button:has-text("Add Size")').click();
        await page.waitForTimeout(500);
        
        await page.fill('input[placeholder="e.g., S, M, L, XL"]', 'Test Size');
        await page.fill('input[placeholder="0"]:nth-of-type(1)', '-10');
        await page.click('button:has-text("Add Size")');
        await page.waitForTimeout(500);
        
        // Check for stock validation error
        const stockError = page.locator('.text-red-500:has-text("Stock cannot be negative")');
        await expect(stockError).toBeVisible();
    });

    test('should persist size data after page reload', async ({ page }) => {
        await page.goto(`/merchant/products/${productId}/edit`);
        await page.waitForLoadState('networkidle');
        
        // Switch to Colors & Images tab
        await page.click('button:has-text("Colors & Images")');
        await page.waitForTimeout(1000);
        
        // Add a size
        const sizeSection = page.locator('.size-management-container').first();
        await sizeSection.locator('button:has-text("Add Size")').click();
        await page.waitForTimeout(500);
        
        await page.fill('input[placeholder="e.g., S, M, L, XL"]', 'Persistent Size');
        await page.fill('input[placeholder="e.g., 32, Medium, etc."]', 'PS');
        await page.fill('input[placeholder="0"]:nth-of-type(1)', '25');
        await page.fill('input[placeholder="0.00"]', '2.50');
        await page.click('button:has-text("Add Size")');
        await page.waitForTimeout(1000);
        
        // Reload the page
        await page.reload();
        await page.waitForLoadState('networkidle');
        
        // Switch to Colors & Images tab again
        await page.click('button:has-text("Colors & Images")');
        await page.waitForTimeout(1000);
        
        // Verify the size is still there
        const persistentSize = page.locator('h6:has-text("Persistent Size")');
        await expect(persistentSize).toBeVisible();
        
        const sizeValue = page.locator('text=Value: PS');
        await expect(sizeValue).toBeVisible();
        
        const sizeStock = page.locator('text=Stock: 25');
        await expect(sizeStock).toBeVisible();
        
        const priceAdjustment = page.locator('text=+$2.5');
        await expect(priceAdjustment).toBeVisible();
    });

    test('should handle loading states correctly', async ({ page }) => {
        await page.goto(`/merchant/products/${productId}/edit`);
        await page.waitForLoadState('networkidle');
        
        // Switch to Colors & Images tab
        await page.click('button:has-text("Colors & Images")');
        await page.waitForTimeout(1000);
        
        // Check for loading state when sizes are being fetched
        const loadingIndicator = page.locator('.fas.fa-spinner.fa-spin');
        
        // The loading state might be very brief, so we'll check if it appears or if content loads
        const sizeSection = page.locator('.size-management-container');
        await expect(sizeSection).toBeVisible();
        
        // Verify that either loading indicator appears briefly or content is loaded
        const hasContent = await sizeSection.locator('text=No sizes added').isVisible() || 
                          await sizeSection.locator('.size-item').count() > 0;
        expect(hasContent).toBe(true);
    });

    test.afterEach(async ({ page }) => {
        // Clean up: delete the test product
        if (productId) {
            await page.goto('/merchant/products');
            await page.waitForLoadState('networkidle');
            
            // Find and delete the test product
            const deleteButton = page.locator(`tr:has-text("Multi-Color Product for Size Testing") .fa-trash`);
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
