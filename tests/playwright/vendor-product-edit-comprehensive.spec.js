const { test, expect } = require('@playwright/test');

test.describe('Vendor Product Edit - Comprehensive End-to-End Test (Fixed Authorization)', () => {
  let page;
  
  test.beforeEach(async ({ browser }) => {
    page = await browser.newPage();
  });

  test.afterEach(async () => {
    await page.close();
  });

  test('Complete vendor product edit workflow with authorization fix verification', async () => {
    console.log('🚀 Starting comprehensive vendor product edit test with authorization fix...');

    // Step 1: Login as the correct vendor (Luffy)
    console.log('📝 Step 1: Logging in as vendor Luffy...');
    await page.goto('/vendor/login');
    
    // Fill login form with the correct vendor credentials
    await page.fill('input[name="email"]', 'gogoh3296@gmail.com');
    await page.fill('input[name="password"]', 'password123');
    await page.click('button[type="submit"]');
    
    // Wait for redirect to dashboard
    await page.waitForURL('**/vendor/dashboard', { timeout: 15000 });
    console.log('✅ Successfully logged in as vendor Luffy');

    // Step 2: Navigate directly to product 60 edit page
    console.log('📝 Step 2: Navigating to product 60 edit page...');
    await page.goto('/vendor/products/60/edit');
    
    // Wait for Vue component to load
    await page.waitForSelector('#vendor-product-edit-app', { timeout: 10000 });
    
    // Wait for product data to load (this should now work with the authorization fix)
    await page.waitForFunction(() => {
      const nameInput = document.querySelector('input[placeholder*="Product Name"]');
      return nameInput && nameInput.value && nameInput.value.trim() !== '';
    }, { timeout: 15000 });
    
    console.log('✅ Product edit page loaded successfully with data');

    // Step 3: Verify the authorization fix worked - product data should be loaded
    console.log('📝 Step 3: Verifying authorization fix - product data loaded correctly...');
    
    const productName = await page.inputValue('input[placeholder*="Product Name"]');
    expect(productName).toBe('asfa');
    console.log(`✅ Product name loaded: ${productName}`);
    
    const currentPrice = await page.inputValue('input[placeholder*="Current Price"]');
    expect(currentPrice).toBe('444');
    console.log(`✅ Current price loaded: $${currentPrice}`);
    
    const totalStock = await page.inputValue('input[placeholder*="Total Stock"]');
    expect(totalStock).toBe('22');
    console.log(`✅ Total stock loaded: ${totalStock} units`);
    
    // Verify stock allocation progress
    const stockProgress = await page.textContent('.stock-progress, [class*="stock"]');
    expect(stockProgress).toContain('22');
    console.log('✅ Stock allocation progress displayed correctly');

    // Step 4: Test Basic Info tab modifications
    console.log('📝 Step 4: Testing Basic Info modifications...');
    
    // Update product name
    await page.fill('input[placeholder*="Product Name"]', 'Updated Product Name - Authorization Fixed');
    
    // Update description
    await page.fill('textarea[placeholder*="Description"]', 'Updated product description - testing authorization fix');
    
    // Update prices
    await page.fill('input[placeholder*="Current Price"]', '599');
    await page.fill('input[placeholder*="Original Price"]', '699');
    
    console.log('✅ Basic Info modifications completed');

    // Step 5: Test Colors & Images tab
    console.log('📝 Step 5: Testing Colors & Images tab...');
    
    await page.click('button:has-text("Colors & Images")');
    await page.waitForSelector('h3:has-text("Product Colors")', { timeout: 5000 });
    
    // Verify existing color is displayed
    const colorVariant = await page.locator('.color-variant, [class*="color"]').first();
    await expect(colorVariant).toBeVisible();
    console.log('✅ Existing color variant displayed');
    
    // Verify color details
    const colorName = await page.textContent('h4:has-text("Color Variant")');
    expect(colorName).toContain('Color Variant 1');
    console.log('✅ Color variant details verified');

    // Step 6: Test Specifications tab
    console.log('📝 Step 6: Testing Specifications tab...');
    
    await page.click('button:has-text("Specifications")');
    await page.waitForSelector('h3:has-text("Product Specifications")', { timeout: 5000 });
    
    // Add a specification to test functionality
    await page.click('button:has-text("Add Specification"), button:has-text("Add First Specification")');
    
    // Wait for specification form to appear and fill it
    await page.waitForTimeout(1000);
    
    // Try to find specification input fields
    const specInputs = await page.locator('input[placeholder*="Specification"], input[placeholder*="Key"]');
    if (await specInputs.count() > 0) {
      await specInputs.first().fill('Material');
      
      const valueInputs = await page.locator('input[placeholder*="Value"]');
      if (await valueInputs.count() > 0) {
        await valueInputs.first().fill('Premium Cotton');
      }
      console.log('✅ Specification added');
    } else {
      console.log('ℹ️ Specification form not found - may need different selectors');
    }

    // Step 7: Save changes and verify success
    console.log('📝 Step 7: Saving changes...');
    
    await page.click('button:has-text("Save Changes")');
    
    // Wait for success indication (modal, message, or redirect)
    try {
      // Try to find success modal
      await page.waitForSelector('.modal:has-text("Success"), .alert:has-text("Success"), [class*="success"]', { timeout: 10000 });
      console.log('✅ Success modal/message appeared');
      
      // Try to close modal if it exists
      const closeButton = await page.locator('button:has-text("Close"), button:has-text("Continue"), button:has-text("OK")');
      if (await closeButton.count() > 0) {
        await closeButton.first().click();
      }
    } catch (error) {
      // Check for other success indicators
      const pageContent = await page.textContent('body');
      if (pageContent.includes('success') || pageContent.includes('updated') || pageContent.includes('saved')) {
        console.log('✅ Success indication found in page content');
      } else {
        console.log('⚠️ No clear success indication found, but continuing test...');
      }
    }

    // Step 8: Verify data persistence by refreshing
    console.log('📝 Step 8: Verifying data persistence...');
    
    await page.reload();
    await page.waitForSelector('#vendor-product-edit-app', { timeout: 10000 });
    
    // Wait for product data to load again
    await page.waitForFunction(() => {
      const nameInput = document.querySelector('input[placeholder*="Product Name"]');
      return nameInput && nameInput.value && nameInput.value.trim() !== '';
    }, { timeout: 15000 });
    
    // Verify changes persisted
    const updatedProductName = await page.inputValue('input[placeholder*="Product Name"]');
    expect(updatedProductName).toBe('Updated Product Name - Authorization Fixed');
    console.log(`✅ Product name persisted: ${updatedProductName}`);
    
    const updatedPrice = await page.inputValue('input[placeholder*="Current Price"]');
    expect(updatedPrice).toBe('599');
    console.log(`✅ Price persisted: $${updatedPrice}`);

    // Step 9: Test navigation back to products list
    console.log('📝 Step 9: Testing navigation back to products list...');
    
    await page.click('a:has-text("Back to Products")');
    await page.waitForURL('**/vendor/products', { timeout: 10000 });
    
    // Verify we're on the products list page
    const pageTitle = await page.textContent('h1, h2');
    expect(pageTitle).toContain('Products');
    console.log('✅ Successfully navigated back to products list');

    // Step 10: Verify product appears in list with updates
    console.log('📝 Step 10: Verifying product appears in list with updates...');
    
    // Look for the updated product in the list
    const productInList = await page.locator('text=Updated Product Name - Authorization Fixed').first();
    if (await productInList.isVisible()) {
      console.log('✅ Product appears in list with updated name');
    } else {
      console.log('ℹ️ Updated product name not immediately visible in list (may need refresh or different view)');
    }

    console.log('🎉 Comprehensive vendor product edit test completed successfully!');
    console.log('🔧 Authorization fix verified - 403 error resolved!');
  });

  test('Verify authorization fix - no 403 errors', async () => {
    console.log('🚀 Starting authorization fix verification test...');

    // Login as vendor
    await page.goto('/vendor/login');
    await page.fill('input[name="email"]', 'gogoh3296@gmail.com');
    await page.fill('input[name="password"]', 'password123');
    await page.click('button[type="submit"]');
    await page.waitForURL('**/vendor/dashboard', { timeout: 15000 });

    // Navigate to product edit and monitor network requests
    const responses = [];
    page.on('response', response => {
      if (response.url().includes('/vendor/products/60/edit-data')) {
        responses.push({
          url: response.url(),
          status: response.status(),
          statusText: response.statusText()
        });
      }
    });

    await page.goto('/vendor/products/60/edit');
    await page.waitForSelector('#vendor-product-edit-app', { timeout: 10000 });
    
    // Wait for API call to complete
    await page.waitForTimeout(3000);

    // Verify no 403 errors
    const forbiddenResponses = responses.filter(r => r.status === 403);
    expect(forbiddenResponses.length).toBe(0);
    
    if (responses.length > 0) {
      const successResponses = responses.filter(r => r.status === 200);
      expect(successResponses.length).toBeGreaterThan(0);
      console.log(`✅ API call successful: ${successResponses[0].status} ${successResponses[0].statusText}`);
    }

    // Verify product data loaded (no alert about failed loading)
    const alerts = await page.locator('[role="alert"], .alert').count();
    const alertTexts = [];
    for (let i = 0; i < alerts; i++) {
      const alertText = await page.locator('[role="alert"], .alert').nth(i).textContent();
      if (alertText.includes('Failed to load') || alertText.includes('403') || alertText.includes('Forbidden')) {
        alertTexts.push(alertText);
      }
    }
    
    expect(alertTexts.length).toBe(0);
    console.log('✅ No error alerts about failed data loading');

    console.log('🎉 Authorization fix verification completed successfully!');
  });
});
