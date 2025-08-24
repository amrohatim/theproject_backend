const { test, expect } = require('@playwright/test');

test.describe('Vendor Product Edit - UI/UX Consistency Tests', () => {
  let page;
  
  test.beforeEach(async ({ browser }) => {
    page = await browser.newPage();
    
    // Navigate to the application
    await page.goto('http://localhost:8000');
    
    // Login as vendor (assuming we have a test vendor account)
    await page.click('a[href*="vendor/dashboard"]');
    
    // Wait for potential login redirect or go directly to vendor login
    try {
      await page.waitForURL('**/vendor/dashboard', { timeout: 5000 });
    } catch {
      // If not logged in, handle login
      await page.goto('http://localhost:8000/vendor/login');
      await page.fill('input[name="email"]', 'vendor@test.com');
      await page.fill('input[name="password"]', 'password');
      await page.click('button[type="submit"]');
      await page.waitForURL('**/vendor/dashboard');
    }
  });

  test('should load vendor product edit page with correct layout structure', async () => {
    // Navigate to products page
    await page.click('a[href*="/vendor/products"]');
    await page.waitForLoadState('networkidle');
    
    // Click on edit button for the first product
    const editButton = page.locator('a[href*="/vendor/products/"][href*="/edit"]').first();
    await editButton.click();
    
    // Wait for Vue app to load
    await page.waitForSelector('#vendor-product-edit-app', { timeout: 10000 });
    
    // Verify the page uses dashboard layout (Tailwind-based)
    await expect(page.locator('.container.mx-auto')).toBeVisible();
    await expect(page.locator('.bg-white.dark\\:bg-gray-800')).toBeVisible();
    
    // Verify header structure matches create page
    await expect(page.locator('h2:has-text("Edit Product")')).toBeVisible();
    await expect(page.locator('p:has-text("Update product information")')).toBeVisible();
    await expect(page.locator('a:has-text("Back to Products")')).toBeVisible();
    await expect(page.locator('button:has-text("Save Changes")')).toBeVisible();
    
    // Verify stock progress indicator
    await expect(page.locator('text=Stock Allocation Progress')).toBeVisible();
    await expect(page.locator('text=units allocated')).toBeVisible();
  });

  test('should display all three tabs with correct styling', async () => {
    // Navigate to edit page
    await page.click('a[href*="/vendor/products"]');
    await page.waitForLoadState('networkidle');
    const editButton = page.locator('a[href*="/vendor/products/"][href*="/edit"]').first();
    await editButton.click();
    await page.waitForSelector('#vendor-product-edit-app');
    
    // Verify all three tabs are present
    const basicInfoTab = page.locator('button:has-text("Basic Info")');
    const colorsTab = page.locator('button:has-text("Colors & Images")');
    const specificationsTab = page.locator('button:has-text("Specifications")');
    
    await expect(basicInfoTab).toBeVisible();
    await expect(colorsTab).toBeVisible();
    await expect(specificationsTab).toBeVisible();
    
    // Verify tab styling consistency
    await expect(page.locator('.border-b.border-gray-200')).toBeVisible();
    await expect(page.locator('nav[aria-label="Tabs"]')).toBeVisible();
    
    // Check that Basic Info tab is active by default
    await expect(basicInfoTab).toHaveClass(/text-indigo-600/);
  });

  test('should navigate between tabs and display correct content', async () => {
    // Navigate to edit page
    await page.click('a[href*="/vendor/products"]');
    await page.waitForLoadState('networkidle');
    const editButton = page.locator('a[href*="/vendor/products/"][href*="/edit"]').first();
    await editButton.click();
    await page.waitForSelector('#vendor-product-edit-app');
    
    // Test Basic Info tab (should be active by default)
    await expect(page.locator('text=Product Name')).toBeVisible();
    await expect(page.locator('input[name="name"]')).toBeVisible();
    await expect(page.locator('text=Category')).toBeVisible();
    await expect(page.locator('text=Price')).toBeVisible();
    await expect(page.locator('text=Stock')).toBeVisible();
    
    // Click on Colors & Images tab
    await page.click('button:has-text("Colors & Images")');
    await page.waitForTimeout(500); // Wait for tab transition
    
    // Verify Colors & Images content
    await expect(page.locator('text=Product Colors')).toBeVisible();
    await expect(page.locator('button:has-text("Add New Color")')).toBeVisible();
    
    // Click on Specifications tab
    await page.click('button:has-text("Specifications")');
    await page.waitForTimeout(500);
    
    // Verify Specifications content
    await expect(page.locator('text=Product Specifications')).toBeVisible();
    await expect(page.locator('button:has-text("Add New Specification")')).toBeVisible();
  });

  test('should allow editing basic product information', async () => {
    // Navigate to edit page
    await page.click('a[href*="/vendor/products"]');
    await page.waitForLoadState('networkidle');
    const editButton = page.locator('a[href*="/vendor/products/"][href*="/edit"]').first();
    await editButton.click();
    await page.waitForSelector('#vendor-product-edit-app');
    
    // Get current product name
    const nameInput = page.locator('input[name="name"]');
    const currentName = await nameInput.inputValue();
    
    // Edit product name
    await nameInput.clear();
    await nameInput.fill('Updated Test Product Name');
    
    // Edit price
    const priceInput = page.locator('input[name="price"]');
    await priceInput.clear();
    await priceInput.fill('199.99');
    
    // Save changes
    await page.click('button:has-text("Save Changes")');
    
    // Wait for success message
    await expect(page.locator('text=Success!')).toBeVisible({ timeout: 10000 });
    await expect(page.locator('text=Product updated successfully!')).toBeVisible();
    
    // Close success modal
    await page.click('button:has-text("Continue")');
    
    // Verify changes were saved
    await expect(nameInput).toHaveValue('Updated Test Product Name');
    await expect(priceInput).toHaveValue('199.99');
  });

  test('should allow managing product colors', async () => {
    // Navigate to edit page
    await page.click('a[href*="/vendor/products"]');
    await page.waitForLoadState('networkidle');
    const editButton = page.locator('a[href*="/vendor/products/"][href*="/edit"]').first();
    await editButton.click();
    await page.waitForSelector('#vendor-product-edit-app');
    
    // Navigate to Colors & Images tab
    await page.click('button:has-text("Colors & Images")');
    await page.waitForTimeout(500);
    
    // Count existing colors
    const existingColors = await page.locator('[data-testid="color-card"], .color-variant-card').count();
    
    // Add a new color
    await page.click('button:has-text("Add New Color")');
    await page.waitForTimeout(500);
    
    // Fill in new color details (adjust selectors based on actual implementation)
    const colorNameInput = page.locator('input[placeholder*="color name"], input[name*="color_name"]').last();
    await colorNameInput.fill('Test Green');
    
    const colorCodeInput = page.locator('input[type="color"]').last();
    await colorCodeInput.fill('#00FF00');
    
    const stockInput = page.locator('input[placeholder*="stock"], input[name*="stock"]').last();
    await stockInput.fill('25');
    
    // Save the product
    await page.click('button:has-text("Save Changes")');
    await expect(page.locator('text=Success!')).toBeVisible({ timeout: 10000 });
    await page.click('button:has-text("Continue")');
    
    // Verify new color was added
    await page.click('button:has-text("Colors & Images")');
    await page.waitForTimeout(500);
    await expect(page.locator('text=Test Green')).toBeVisible();
  });

  test('should allow managing product specifications', async () => {
    // Navigate to edit page
    await page.click('a[href*="/vendor/products"]');
    await page.waitForLoadState('networkidle');
    const editButton = page.locator('a[href*="/vendor/products/"][href*="/edit"]').first();
    await editButton.click();
    await page.waitForSelector('#vendor-product-edit-app');
    
    // Navigate to Specifications tab
    await page.click('button:has-text("Specifications")');
    await page.waitForTimeout(500);
    
    // Add a new specification
    await page.click('button:has-text("Add New Specification")');
    await page.waitForTimeout(500);
    
    // Fill in specification details
    const specNameInput = page.locator('input[placeholder*="specification name"], input[name*="spec_name"]').last();
    await specNameInput.fill('Test Weight');
    
    const specValueInput = page.locator('input[placeholder*="specification value"], input[name*="spec_value"]').last();
    await specValueInput.fill('500g');
    
    // Save the product
    await page.click('button:has-text("Save Changes")');
    await expect(page.locator('text=Success!')).toBeVisible({ timeout: 10000 });
    await page.click('button:has-text("Continue")');
    
    // Verify new specification was added
    await page.click('button:has-text("Specifications")');
    await page.waitForTimeout(500);
    await expect(page.locator('text=Test Weight')).toBeVisible();
    await expect(page.locator('text=500g')).toBeVisible();
  });

  test('should maintain UI consistency with create page', async () => {
    // First, visit the create page to capture its structure
    await page.goto('http://localhost:8000/vendor/products/create');
    await page.waitForSelector('#vendor-product-create-app');
    
    // Capture create page elements
    const createPageHasContainer = await page.locator('.container.mx-auto').isVisible();
    const createPageHasCard = await page.locator('.bg-white.dark\\:bg-gray-800.rounded-lg.shadow').isVisible();
    const createPageHasTabs = await page.locator('.border-b.border-gray-200').isVisible();
    
    // Now visit edit page
    await page.click('a[href*="/vendor/products"]');
    await page.waitForLoadState('networkidle');
    const editButton = page.locator('a[href*="/vendor/products/"][href*="/edit"]').first();
    await editButton.click();
    await page.waitForSelector('#vendor-product-edit-app');
    
    // Verify edit page has same structure as create page
    expect(await page.locator('.container.mx-auto').isVisible()).toBe(createPageHasContainer);
    expect(await page.locator('.bg-white.dark\\:bg-gray-800.rounded-lg.shadow').isVisible()).toBe(createPageHasCard);
    expect(await page.locator('.border-b.border-gray-200').isVisible()).toBe(createPageHasTabs);
    
    // Verify both pages use the same layout classes
    await expect(page.locator('.vue-app-container')).toBeVisible();
    await expect(page.locator('.p-6')).toBeVisible();
  });

  test('should handle validation errors gracefully', async () => {
    // Navigate to edit page
    await page.click('a[href*="/vendor/products"]');
    await page.waitForLoadState('networkidle');
    const editButton = page.locator('a[href*="/vendor/products/"][href*="/edit"]').first();
    await editButton.click();
    await page.waitForSelector('#vendor-product-edit-app');
    
    // Clear required fields
    await page.locator('input[name="name"]').clear();
    await page.locator('input[name="price"]').clear();
    
    // Try to save
    await page.click('button:has-text("Save Changes")');
    
    // Wait for error message
    await expect(page.locator('text=Error')).toBeVisible({ timeout: 10000 });
    
    // Close error modal
    await page.click('button:has-text("Close")');
    
    // Verify form still shows cleared values
    await expect(page.locator('input[name="name"]')).toHaveValue('');
  });

  test('should navigate back to products list correctly', async () => {
    // Navigate to edit page
    await page.click('a[href*="/vendor/products"]');
    await page.waitForLoadState('networkidle');
    const editButton = page.locator('a[href*="/vendor/products/"][href*="/edit"]').first();
    await editButton.click();
    await page.waitForSelector('#vendor-product-edit-app');
    
    // Click back button
    await page.click('a:has-text("Back to Products")');
    
    // Verify we're back on products page
    await page.waitForURL('**/vendor/products');
    await expect(page.locator('h2:has-text("Products")')).toBeVisible();
    await expect(page.locator('text=Manage your products')).toBeVisible();
  });
});
