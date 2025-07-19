const { test, expect } = require('@playwright/test');

/**
 * Test for the Size Management UI refresh fix
 * This test specifically addresses the issue where adding the first size 
 * to a newly created color variant fails to refresh the UI properly.
 */

let productId;

test.describe('Size Management UI Refresh Fix', () => {
    test.beforeAll(async ({ browser }) => {
        const page = await browser.newPage();
        
        // Login as merchant
        await page.goto('/merchant/login');
        await page.fill('#email', 'merchant@example.com');
        await page.fill('#password', 'password');
        await page.click('button[type="submit"]');
        await page.waitForURL('**/merchant/dashboard');
        
        // Create a test product
        await page.goto('/merchant/products/create');
        await page.waitForLoadState('networkidle');
        
        // Fill basic product information
        await page.fill('#name', 'Test Product - New Color First Size Fix');
        await page.selectOption('#category_id', { index: 1 });
        await page.fill('#price', '99.99');
        await page.fill('#stock', '100');
        await page.fill('#description', 'Test product for new color first size fix');
        
        // Submit the product
        await page.click('button[type="submit"]');
        await page.waitForURL('**/merchant/products');
        
        // Extract product ID from the URL or response
        const url = page.url();
        const match = url.match(/\/merchant\/products\/(\d+)/);
        if (match) {
            productId = match[1];
        } else {
            // Alternative: get the latest product ID from the products list
            await page.goto('/merchant/products');
            const firstProductLink = page.locator('a[href*="/merchant/products/"]').first();
            const href = await firstProductLink.getAttribute('href');
            productId = href.match(/\/(\d+)$/)[1];
        }
        
        await page.close();
    });

    test('should successfully add first size to new color variant and refresh UI', async ({ page }) => {
        console.log('🧪 Testing new color first size addition and UI refresh...');
        
        // Navigate to product edit page
        await page.goto(`/merchant/products/${productId}/edit`);
        await page.waitForLoadState('networkidle');
        
        // Switch to Colors & Images tab
        await page.click('button:has-text("Colors & Images")');
        await page.waitForTimeout(1000);
        
        // Add a new color variant
        console.log('📝 Adding new color variant...');
        await page.click('button:has-text("Add Color")');
        await page.waitForTimeout(1000);
        
        // Fill in the new color details
        const newColorCard = page.locator('.color-item').last();
        
        // Select color name
        const colorNameSelect = newColorCard.locator('select[name*="[name]"]');
        await colorNameSelect.selectOption('Blue');
        
        // Set color code
        const colorCodeInput = newColorCard.locator('input[name*="[color_code]"]');
        await colorCodeInput.fill('#0000FF');
        
        // Set stock
        const stockInput = newColorCard.locator('input[name*="[stock]"]');
        await stockInput.fill('25');
        
        // Wait for the color to be ready for size management
        await page.waitForTimeout(1000);
        
        // Verify Size Management section is visible
        const sizeManagementSection = newColorCard.locator('.size-management-container');
        await expect(sizeManagementSection).toBeVisible();
        
        // Check for "No sizes added" state
        const noSizesMessage = sizeManagementSection.locator('text=No sizes added');
        await expect(noSizesMessage).toBeVisible();
        
        // Click "Add First Size" button
        console.log('➕ Adding first size to new color variant...');
        const addFirstSizeButton = sizeManagementSection.locator('button:has-text("Add First Size")');
        await addFirstSizeButton.click();
        await page.waitForTimeout(500);
        
        // Fill in size details in the modal
        const modal = page.locator('.fixed.inset-0');
        await expect(modal).toBeVisible();
        
        // Fill size name
        const sizeNameInput = modal.locator('input[placeholder*="S, M, L, XL"]');
        await sizeNameInput.fill('Large');
        
        // Fill size value
        const sizeValueInput = modal.locator('input[placeholder*="32, Medium"]');
        await sizeValueInput.fill('L');
        
        // Fill stock
        const sizeStockInput = modal.locator('input[placeholder="0"]').first();
        await sizeStockInput.fill('10');
        
        // Fill price adjustment
        const priceAdjustmentInput = modal.locator('input[placeholder="0.00"]');
        await priceAdjustmentInput.fill('2.50');
        
        // Submit the size
        console.log('💾 Submitting new size...');
        const addSizeButton = modal.locator('button:has-text("Add Size")');
        await addSizeButton.click();
        
        // Wait for the modal to close and UI to update
        await page.waitForTimeout(2000);
        
        // Verify the modal is closed
        await expect(modal).not.toBeVisible();
        
        // Verify the size was added and UI refreshed properly
        console.log('✅ Verifying UI refresh and size display...');
        
        // Check that "No sizes added" message is gone
        await expect(noSizesMessage).not.toBeVisible();
        
        // Check that the size is now displayed in the list
        const sizeItem = sizeManagementSection.locator('.size-item');
        await expect(sizeItem).toBeVisible();
        
        // Verify size details are displayed correctly
        await expect(sizeItem.locator('text=Large')).toBeVisible();
        await expect(sizeItem.locator('text=L')).toBeVisible();
        await expect(sizeItem.locator('text=10')).toBeVisible(); // Stock
        await expect(sizeItem.locator('text=+$2.50')).toBeVisible(); // Price adjustment
        
        // Verify no error messages are displayed
        const errorMessage = page.locator('text=Failed to load sizes');
        await expect(errorMessage).not.toBeVisible();
        
        // Test the refresh button functionality
        console.log('🔄 Testing refresh button...');
        const refreshButton = sizeManagementSection.locator('button:has-text("Refresh")');
        await expect(refreshButton).toBeVisible();
        await refreshButton.click();
        await page.waitForTimeout(1000);
        
        // Verify size is still displayed after manual refresh
        await expect(sizeItem).toBeVisible();
        await expect(sizeItem.locator('text=Large')).toBeVisible();
        
        console.log('✅ Test completed successfully!');
    });

    test('should handle errors gracefully and show error state', async ({ page }) => {
        console.log('🧪 Testing error handling and recovery...');
        
        // Navigate to product edit page
        await page.goto(`/merchant/products/${productId}/edit`);
        await page.waitForLoadState('networkidle');
        
        // Switch to Colors & Images tab
        await page.click('button:has-text("Colors & Images")');
        await page.waitForTimeout(1000);
        
        // Find an existing color with sizes (from previous test)
        const colorCard = page.locator('.color-item').first();
        const sizeManagementSection = colorCard.locator('.size-management-container');
        
        // Intercept the API call to simulate an error
        await page.route('**/api/color-sizes/get-sizes-for-color', route => {
            route.fulfill({
                status: 500,
                contentType: 'application/json',
                body: JSON.stringify({
                    success: false,
                    message: 'Simulated server error for testing'
                })
            });
        });
        
        // Click refresh to trigger the error
        const refreshButton = sizeManagementSection.locator('button:has-text("Refresh")');
        await refreshButton.click();
        await page.waitForTimeout(1000);
        
        // Verify error state is displayed
        const errorState = sizeManagementSection.locator('.bg-red-50');
        await expect(errorState).toBeVisible();
        
        // Verify error message is shown
        const errorMessage = errorState.locator('text=Error Loading Sizes');
        await expect(errorMessage).toBeVisible();
        
        // Verify "Try Again" button is available
        const tryAgainButton = errorState.locator('button:has-text("Try Again")');
        await expect(tryAgainButton).toBeVisible();
        
        // Test error dismissal
        const dismissButton = errorState.locator('.fa-times').locator('..');
        await dismissButton.click();
        await page.waitForTimeout(500);
        
        // Verify error state is dismissed
        await expect(errorState).not.toBeVisible();
        
        console.log('✅ Error handling test completed successfully!');
    });

    test.afterAll(async ({ browser }) => {
        // Clean up: delete the test product
        const page = await browser.newPage();
        
        try {
            await page.goto(`/merchant/products/${productId}/edit`);
            await page.waitForLoadState('networkidle');
            
            // Delete the product if delete functionality exists
            const deleteButton = page.locator('button:has-text("Delete")');
            if (await deleteButton.count() > 0) {
                await deleteButton.click();
                await page.waitForTimeout(1000);
                
                // Confirm deletion if confirmation dialog appears
                const confirmButton = page.locator('button:has-text("Confirm"), button:has-text("Yes")');
                if (await confirmButton.count() > 0) {
                    await confirmButton.click();
                }
            }
        } catch (error) {
            console.log('Note: Could not clean up test product:', error.message);
        }
        
        await page.close();
    });
});
