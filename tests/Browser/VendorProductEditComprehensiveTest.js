const { test, expect } = require('@playwright/test');

test.describe('Vendor Product Edit - Comprehensive End-to-End Test', () => {
  let page;
  
  test.beforeAll(async ({ browser }) => {
    page = await browser.newPage();
  });

  test.afterAll(async () => {
    await page.close();
  });

  test('Complete vendor product edit workflow', async () => {
    console.log('🚀 Starting comprehensive vendor product edit test...');

    // Step 1: Login as vendor
    console.log('📝 Step 1: Logging in as vendor...');
    await page.goto('http://localhost:8000/vendor/login');
    
    // Fill login form
    await page.fill('input[name="email"]', 'gogoh3296@gmail.com');
    await page.fill('input[name="password"]', 'password123');
    await page.click('button[type="submit"]');
    
    // Wait for redirect to dashboard
    await page.waitForURL('**/vendor/dashboard', { timeout: 10000 });
    console.log('✅ Successfully logged in as vendor');

    // Step 2: Navigate to product edit page
    console.log('📝 Step 2: Navigating to product edit page...');
    await page.goto('http://localhost:8000/vendor/products/60/edit');
    
    // Wait for Vue component to load
    await page.waitForSelector('[data-product-id="60"]', { timeout: 10000 });
    
    // Wait for product data to load (check if product name field is populated)
    await page.waitForFunction(() => {
      const nameInput = document.querySelector('input[placeholder*="Product Name"]');
      return nameInput && nameInput.value && nameInput.value.trim() !== '';
    }, { timeout: 15000 });
    
    console.log('✅ Product edit page loaded successfully');

    // Step 3: Verify product data loaded correctly
    console.log('📝 Step 3: Verifying product data loaded correctly...');
    
    const productName = await page.inputValue('input[placeholder*="Product Name"]');
    expect(productName).toBe('asfa');
    
    const currentPrice = await page.inputValue('input[placeholder*="Current Price"]');
    expect(currentPrice).toBe('444');
    
    const totalStock = await page.inputValue('input[placeholder*="Total Stock"]');
    expect(totalStock).toBe('22');
    
    console.log('✅ Product data verification passed');

    // Step 4: Test Basic Info tab modifications
    console.log('📝 Step 4: Testing Basic Info modifications...');
    
    // Update product name
    await page.fill('input[placeholder*="Product Name"]', 'Updated Product Name');
    
    // Update description
    await page.fill('textarea[placeholder*="Description"]', 'Updated product description with comprehensive details');
    
    // Update price
    await page.fill('input[placeholder*="Current Price"]', '499');
    await page.fill('input[placeholder*="Original Price"]', '599');
    
    console.log('✅ Basic Info modifications completed');

    // Step 5: Test Colors & Images tab
    console.log('📝 Step 5: Testing Colors & Images tab...');
    
    await page.click('button:has-text("Colors & Images")');
    await page.waitForSelector('h3:has-text("Product Colors")', { timeout: 5000 });
    
    // Verify existing color is displayed
    const colorName = await page.textContent('.color-variant h4');
    expect(colorName).toContain('Color Variant 1');
    
    // Test adding a new color
    await page.click('button:has-text("Add Color")');
    await page.waitForSelector('.color-variant:nth-child(2)', { timeout: 5000 });
    
    console.log('✅ Colors & Images tab testing completed');

    // Step 6: Test Specifications tab
    console.log('📝 Step 6: Testing Specifications tab...');
    
    await page.click('button:has-text("Specifications")');
    await page.waitForSelector('h3:has-text("Product Specifications")', { timeout: 5000 });
    
    // Add a specification
    await page.click('button:has-text("Add Specification")');
    
    // Wait for specification form to appear
    await page.waitForSelector('input[placeholder*="Specification"]', { timeout: 5000 });
    
    // Fill specification details
    await page.fill('input[placeholder*="Specification"]:first-of-type', 'Material');
    await page.fill('input[placeholder*="Value"]:first-of-type', 'Premium Cotton');
    
    console.log('✅ Specifications tab testing completed');

    // Step 7: Save changes
    console.log('📝 Step 7: Saving changes...');
    
    await page.click('button:has-text("Save Changes")');
    
    // Wait for success modal or message
    try {
      await page.waitForSelector('.modal:has-text("Success")', { timeout: 10000 });
      console.log('✅ Success modal appeared');
      
      // Close success modal
      await page.click('button:has-text("Close")');
    } catch (error) {
      // Check for other success indicators
      const successMessage = await page.textContent('body');
      if (successMessage.includes('success') || successMessage.includes('updated')) {
        console.log('✅ Success message detected');
      } else {
        throw new Error('No success indication found after saving');
      }
    }

    // Step 8: Verify persistence by refreshing page
    console.log('📝 Step 8: Verifying data persistence...');
    
    await page.reload();
    await page.waitForSelector('[data-product-id="60"]', { timeout: 10000 });
    
    // Wait for product data to load again
    await page.waitForFunction(() => {
      const nameInput = document.querySelector('input[placeholder*="Product Name"]');
      return nameInput && nameInput.value && nameInput.value.trim() !== '';
    }, { timeout: 15000 });
    
    // Verify changes persisted
    const updatedProductName = await page.inputValue('input[placeholder*="Product Name"]');
    expect(updatedProductName).toBe('Updated Product Name');
    
    const updatedPrice = await page.inputValue('input[placeholder*="Current Price"]');
    expect(updatedPrice).toBe('499');
    
    console.log('✅ Data persistence verification passed');

    // Step 9: Navigate back to products list
    console.log('📝 Step 9: Testing navigation back to products list...');
    
    await page.click('a:has-text("Back to Products")');
    await page.waitForURL('**/vendor/products', { timeout: 10000 });
    
    // Verify we're on the products list page
    const pageTitle = await page.textContent('h1');
    expect(pageTitle).toContain('Products');
    
    console.log('✅ Navigation back to products list successful');

    // Step 10: Verify product appears in list with updates
    console.log('📝 Step 10: Verifying product appears in list with updates...');
    
    // Look for the updated product in the list
    const productInList = await page.locator('text=Updated Product Name').first();
    await expect(productInList).toBeVisible();
    
    console.log('✅ Product appears in list with updated name');

    console.log('🎉 Comprehensive vendor product edit test completed successfully!');
  });

  test('Error handling verification', async () => {
    console.log('🚀 Starting error handling verification test...');

    // Login first
    await page.goto('http://localhost:8000/vendor/login');
    await page.fill('input[name="email"]', 'gogoh3296@gmail.com');
    await page.fill('input[name="password"]', 'password123');
    await page.click('button[type="submit"]');
    await page.waitForURL('**/vendor/dashboard', { timeout: 10000 });

    // Navigate to product edit
    await page.goto('http://localhost:8000/vendor/products/60/edit');
    await page.waitForSelector('[data-product-id="60"]', { timeout: 10000 });
    
    // Wait for data to load
    await page.waitForFunction(() => {
      const nameInput = document.querySelector('input[placeholder*="Product Name"]');
      return nameInput && nameInput.value && nameInput.value.trim() !== '';
    }, { timeout: 15000 });

    // Test validation errors
    console.log('📝 Testing validation errors...');
    
    // Clear required fields
    await page.fill('input[placeholder*="Product Name"]', '');
    await page.fill('input[placeholder*="Current Price"]', '0');
    
    // Try to save
    await page.click('button:has-text("Save Changes")');
    
    // Check for validation errors (should stay on form or show error modal)
    await page.waitForTimeout(2000);
    
    // Verify we're still on the edit page (validation prevented save)
    const currentUrl = page.url();
    expect(currentUrl).toContain('/vendor/products/60/edit');
    
    console.log('✅ Validation error handling verified');
    console.log('🎉 Error handling verification test completed successfully!');
  });
});
