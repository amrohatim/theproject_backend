const { test, expect } = require('@playwright/test');

test.describe('Merchant Product Size Management', () => {
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
        await page.fill('#name', 'Test Product for Size Management');
        await page.selectOption('#category_id', { index: 1 });
        await page.fill('#price', '50.00');
        await page.fill('#stock', '100');
        await page.fill('#description', 'Test product for size management functionality');
        
        // Add a color variant
        await page.click('button:has-text("Add Color")');
        await page.waitForTimeout(1000);
        
        // Fill color information
        const colorNameSelect = page.locator('.color-name-select').first();
        await colorNameSelect.selectOption('Red');
        
        const colorStockInput = page.locator('.color-stock-input').first();
        await colorStockInput.fill('50');
        
        // Submit the product
        await page.click('button[type="submit"]');
        await page.waitForURL('**/merchant/products');
        
        // Get the product ID from the URL or find the created product
        await page.click('a:has-text("Test Product for Size Management")');
        await page.waitForURL('**/merchant/products/*/edit');
        
        // Extract product ID from URL
        const url = page.url();
        productId = url.match(/\/products\/(\d+)\/edit/)[1];
    });

    test('should display size management section for each color variant', async ({ page }) => {
        // Navigate to the product edit page
        await page.goto(`/merchant/products/${productId}/edit`);
        await page.waitForLoadState('networkidle');
        
        // Switch to Colors & Images tab
        await page.click('button:has-text("Colors & Images")');
        await page.waitForTimeout(1000);
        
        // Check if size management section is visible
        const sizeManagementSection = page.locator('.size-management-container');
        await expect(sizeManagementSection).toBeVisible();
        
        // Check if the header is correct
        const sizeManagementHeader = page.locator('h5:has-text("Size Management")');
        await expect(sizeManagementHeader).toBeVisible();
        
        // Check if "Add Size" button is present
        const addSizeButton = page.locator('button:has-text("Add Size")');
        await expect(addSizeButton).toBeVisible();
    });

    test('should show empty state when no sizes are added', async ({ page }) => {
        await page.goto(`/merchant/products/${productId}/edit`);
        await page.waitForLoadState('networkidle');
        
        // Switch to Colors & Images tab
        await page.click('button:has-text("Colors & Images")');
        await page.waitForTimeout(1000);
        
        // Check for empty state
        const emptyStateIcon = page.locator('.fas.fa-ruler');
        await expect(emptyStateIcon).toBeVisible();
        
        const emptyStateText = page.locator('text=No sizes added');
        await expect(emptyStateText).toBeVisible();
        
        const addFirstSizeButton = page.locator('button:has-text("Add First Size")');
        await expect(addFirstSizeButton).toBeVisible();
    });

    test('should open add size modal when clicking Add Size button', async ({ page }) => {
        await page.goto(`/merchant/products/${productId}/edit`);
        await page.waitForLoadState('networkidle');
        
        // Switch to Colors & Images tab
        await page.click('button:has-text("Colors & Images")');
        await page.waitForTimeout(1000);
        
        // Click Add Size button
        await page.click('button:has-text("Add Size")');
        await page.waitForTimeout(500);
        
        // Check if modal is visible
        const modal = page.locator('.fixed.inset-0');
        await expect(modal).toBeVisible();
        
        // Check modal title
        const modalTitle = page.locator('h3:has-text("Add New Size")');
        await expect(modalTitle).toBeVisible();
        
        // Check form fields
        await expect(page.locator('input[placeholder="e.g., S, M, L, XL"]')).toBeVisible();
        await expect(page.locator('input[placeholder="e.g., 32, Medium, etc."]')).toBeVisible();
        await expect(page.locator('input[placeholder="0"]')).toBeVisible();
        await expect(page.locator('input[placeholder="0.00"]')).toBeVisible();
    });

    test('should add a new size successfully', async ({ page }) => {
        await page.goto(`/merchant/products/${productId}/edit`);
        await page.waitForLoadState('networkidle');
        
        // Switch to Colors & Images tab
        await page.click('button:has-text("Colors & Images")');
        await page.waitForTimeout(1000);
        
        // Click Add Size button
        await page.click('button:has-text("Add Size")');
        await page.waitForTimeout(500);
        
        // Fill in size information
        await page.fill('input[placeholder="e.g., S, M, L, XL"]', 'Medium');
        await page.fill('input[placeholder="e.g., 32, Medium, etc."]', 'M');
        await page.fill('input[placeholder="0"]:nth-of-type(1)', '20');
        await page.fill('input[placeholder="0.00"]', '5.00');
        
        // Check the "Available for purchase" checkbox
        await page.check('#new-size-available');
        
        // Submit the form
        await page.click('button:has-text("Add Size")');
        await page.waitForTimeout(1000);
        
        // Check if modal is closed
        const modal = page.locator('.fixed.inset-0');
        await expect(modal).not.toBeVisible();
        
        // Check if size appears in the list
        const sizeItem = page.locator('.size-item');
        await expect(sizeItem).toBeVisible();
        
        const sizeName = page.locator('h6:has-text("Medium")');
        await expect(sizeName).toBeVisible();
        
        const sizeValue = page.locator('text=Value: M');
        await expect(sizeValue).toBeVisible();
        
        const sizeStock = page.locator('text=Stock: 20');
        await expect(sizeStock).toBeVisible();
    });

    test('should validate required fields when adding a size', async ({ page }) => {
        await page.goto(`/merchant/products/${productId}/edit`);
        await page.waitForLoadState('networkidle');
        
        // Switch to Colors & Images tab
        await page.click('button:has-text("Colors & Images")');
        await page.waitForTimeout(1000);
        
        // Click Add Size button
        await page.click('button:has-text("Add Size")');
        await page.waitForTimeout(500);
        
        // Try to submit without filling required fields
        await page.click('button:has-text("Add Size")');
        await page.waitForTimeout(500);
        
        // Check for validation error
        const errorMessage = page.locator('.text-red-500:has-text("Size name is required")');
        await expect(errorMessage).toBeVisible();
        
        // Check that modal is still open
        const modal = page.locator('.fixed.inset-0');
        await expect(modal).toBeVisible();
    });

    test('should edit an existing size', async ({ page }) => {
        // First add a size
        await page.goto(`/merchant/products/${productId}/edit`);
        await page.waitForLoadState('networkidle');
        
        // Switch to Colors & Images tab
        await page.click('button:has-text("Colors & Images")');
        await page.waitForTimeout(1000);
        
        // Add a size first
        await page.click('button:has-text("Add Size")');
        await page.waitForTimeout(500);
        await page.fill('input[placeholder="e.g., S, M, L, XL"]', 'Large');
        await page.fill('input[placeholder="0"]:nth-of-type(1)', '15');
        await page.click('button:has-text("Add Size")');
        await page.waitForTimeout(1000);
        
        // Click edit button
        await page.click('.fa-edit');
        await page.waitForTimeout(500);
        
        // Check if edit mode is active
        const nameInput = page.locator('input[value="Large"]');
        await expect(nameInput).toBeVisible();
        
        // Modify the size
        await nameInput.fill('Extra Large');
        await page.fill('input[type="number"]:nth-of-type(1)', '25');
        
        // Save changes
        await page.click('button:has-text("Save")');
        await page.waitForTimeout(1000);
        
        // Check if changes are saved
        const updatedSizeName = page.locator('h6:has-text("Extra Large")');
        await expect(updatedSizeName).toBeVisible();
        
        const updatedStock = page.locator('text=Stock: 25');
        await expect(updatedStock).toBeVisible();
    });

    test('should cancel edit mode without saving changes', async ({ page }) => {
        // First add a size
        await page.goto(`/merchant/products/${productId}/edit`);
        await page.waitForLoadState('networkidle');
        
        // Switch to Colors & Images tab
        await page.click('button:has-text("Colors & Images")');
        await page.waitForTimeout(1000);
        
        // Add a size first
        await page.click('button:has-text("Add Size")');
        await page.waitForTimeout(500);
        await page.fill('input[placeholder="e.g., S, M, L, XL"]', 'Small');
        await page.fill('input[placeholder="0"]:nth-of-type(1)', '10');
        await page.click('button:has-text("Add Size")');
        await page.waitForTimeout(1000);
        
        // Click edit button
        await page.click('.fa-edit');
        await page.waitForTimeout(500);
        
        // Modify the size
        const nameInput = page.locator('input[value="Small"]');
        await nameInput.fill('Modified Size');
        
        // Cancel changes
        await page.click('button:has-text("Cancel")');
        await page.waitForTimeout(500);
        
        // Check if original values are restored
        const originalSizeName = page.locator('h6:has-text("Small")');
        await expect(originalSizeName).toBeVisible();
        
        // Check that edit mode is exited
        const editInput = page.locator('input[value="Modified Size"]');
        await expect(editInput).not.toBeVisible();
    });

    test('should delete a size with confirmation', async ({ page }) => {
        // First add a size
        await page.goto(`/merchant/products/${productId}/edit`);
        await page.waitForLoadState('networkidle');
        
        // Switch to Colors & Images tab
        await page.click('button:has-text("Colors & Images")');
        await page.waitForTimeout(1000);
        
        // Add a size first
        await page.click('button:has-text("Add Size")');
        await page.waitForTimeout(500);
        await page.fill('input[placeholder="e.g., S, M, L, XL"]', 'Test Size');
        await page.click('button:has-text("Add Size")');
        await page.waitForTimeout(1000);
        
        // Set up dialog handler for confirmation
        page.on('dialog', async dialog => {
            expect(dialog.message()).toContain('Are you sure you want to remove this size?');
            await dialog.accept();
        });
        
        // Click delete button
        await page.click('.fa-trash');
        await page.waitForTimeout(1000);
        
        // Check if size is removed
        const deletedSize = page.locator('h6:has-text("Test Size")');
        await expect(deletedSize).not.toBeVisible();
        
        // Check if empty state is shown again
        const emptyStateText = page.locator('text=No sizes added');
        await expect(emptyStateText).toBeVisible();
    });

    test.afterEach(async ({ page }) => {
        // Clean up: delete the test product
        if (productId) {
            await page.goto('/merchant/products');
            await page.waitForLoadState('networkidle');
            
            // Find and delete the test product
            const deleteButton = page.locator(`tr:has-text("Test Product for Size Management") .fa-trash`);
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
