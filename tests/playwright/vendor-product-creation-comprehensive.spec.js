const { test, expect } = require('@playwright/test');

test.describe('Vendor Product Creation - Comprehensive Test Suite', () => {
  let page;
  let context;

  test.beforeAll(async ({ browser }) => {
    // Create a new browser context
    context = await browser.newContext({
      viewport: { width: 1920, height: 1080 },
      ignoreHTTPSErrors: true
    });
    page = await context.newPage();

    // Enable console logging for debugging
    page.on('console', msg => {
      if (msg.type() === 'error') {
        console.log('❌ Browser Console Error:', msg.text());
      } else if (msg.type() === 'warn') {
        console.log('⚠️ Browser Console Warning:', msg.text());
      }
    });

    // Handle uncaught exceptions
    page.on('pageerror', error => {
      console.log('💥 Page Error:', error.message);
    });
  });

  test.afterAll(async () => {
    await context.close();
  });

  test('should complete full vendor product creation with stock validation and size management', async () => {
    console.log('🚀 Starting comprehensive vendor product creation test...');

    // Step 1: Login as vendor
    console.log('Step 1: Logging in as vendor...');
    await page.goto('http://localhost:8000/login');
    await page.waitForLoadState('networkidle');
    
    // Fill login form
    await page.fill('input[name="email"]', 'gogoh3296@gmail.com');
    await page.fill('input[name="password"]', 'Fifa2021');
    await page.click('button[type="submit"]');
    
    // Wait for redirect to dashboard
    await page.waitForURL('**/vendor/dashboard**', { timeout: 15000 });
    console.log('✅ Successfully logged in as vendor');

    // Step 2: Navigate to product creation page
    console.log('Step 2: Navigating to product creation...');
    await page.goto('http://localhost:8000/vendor/products/create');
    await page.waitForLoadState('networkidle');
    
    // Wait for Vue app to initialize
    await page.waitForSelector('#vendor-product-create-app', { timeout: 10000 });
    await page.waitForTimeout(2000); // Allow Vue to fully initialize
    console.log('✅ Product creation page loaded');

    // Take initial screenshot
    await page.screenshot({ 
      path: 'tests/screenshots/vendor-product-create-initial.png',
      fullPage: true 
    });

    // Step 3: Fill Basic Info Tab
    console.log('Step 3: Filling basic product information...');
    
    // Verify we're on the basic tab
    await expect(page.locator('[data-tab="basic"]')).toHaveClass(/active/);
    
    // Fill product name
    await page.fill('input[name="name"]', 'Test Vendor Product - Enhanced');
    
    // Select category (wait for options to load)
    await page.waitForSelector('select[name="category_id"] option:not([value=""])', { timeout: 5000 });
    await page.selectOption('select[name="category_id"]', { index: 1 });
    
    // Select branch (wait for options to load)
    await page.waitForSelector('select[name="branch_id"] option:not([value=""])', { timeout: 5000 });
    await page.selectOption('select[name="branch_id"]', { index: 1 });
    
    // Set price and stock
    await page.fill('input[name="price"]', '150.00');
    await page.fill('input[name="original_price"]', '200.00');
    await page.fill('input[name="stock"]', '100'); // General stock for validation testing
    
    // Fill description
    await page.fill('textarea[name="description"]', 'This is a comprehensive test product with enhanced stock validation and size management features.');
    
    console.log('✅ Basic info filled successfully');

    // Step 4: Navigate to Colors & Images Tab
    console.log('Step 4: Testing Colors & Images tab...');
    await page.click('[data-tab="colors"]');
    await page.waitForTimeout(1000);
    
    // Verify tab switch
    await expect(page.locator('[data-tab="colors"]')).toHaveClass(/active/);
    
    // Take screenshot of colors tab
    await page.screenshot({ 
      path: 'tests/screenshots/vendor-colors-tab-initial.png',
      fullPage: true 
    });

    // Step 5: Add First Color with Stock Validation Testing
    console.log('Step 5: Adding first color and testing stock validation...');
    await page.click('button:has-text("Add Color")');
    await page.waitForTimeout(1000);
    
    // Verify color card appeared
    await expect(page.locator('.color-item')).toHaveCount(1);
    
    // Test color selection dropdown
    await page.click('.selected-color-display');
    await page.waitForSelector('.color-dropdown', { timeout: 3000 });
    
    // Search for a color
    await page.fill('.color-search-input', 'blue');
    await page.waitForTimeout(500);
    
    // Select blue color
    await page.click('.color-option:has-text("Blue")');
    await page.waitForTimeout(500);
    
    // Verify color swatch updated
    const colorSwatch = page.locator('.color-item .w-6.h-6').first();
    const swatchStyle = await colorSwatch.getAttribute('style');
    expect(swatchStyle).toContain('#0000FF'); // Blue color code
    
    console.log('✅ Color swatch display working correctly');

    // Test stock validation - try to exceed general stock
    console.log('Step 6: Testing stock validation...');
    await page.fill('.color-item input[placeholder="0"]:last-of-type', '150'); // Exceeds general stock of 100
    await page.waitForTimeout(1000);
    
    // Verify stock was corrected
    const stockInput = page.locator('.color-item input[placeholder="0"]:last-of-type');
    const correctedValue = await stockInput.inputValue();
    expect(parseInt(correctedValue)).toBeLessThanOrEqual(100);
    
    console.log('✅ Stock validation working - value corrected to:', correctedValue);

    // Set valid stock amount
    await page.fill('.color-item input[placeholder="0"]:last-of-type', '50');
    await page.waitForTimeout(1000);

    // Step 7: Test Size Management
    console.log('Step 7: Testing size management functionality...');
    
    // Verify size management section appears after setting color name and stock
    await expect(page.locator('.size-management-container')).toBeVisible();
    
    // Add a size
    await page.click('button:has-text("Add Size")');
    await page.waitForSelector('.fixed.inset-0', { timeout: 3000 }); // Modal
    
    // Fill size details
    await page.fill('input[placeholder="e.g., Small, Medium, Large"]', 'Medium');
    await page.fill('input[placeholder="e.g., S, M, L, XL"]', 'M');
    await page.fill('.enhanced-input-group input[placeholder="0"]', '20'); // Size stock
    await page.fill('.enhanced-input-group input[placeholder="0.00"]', '5.00'); // Price adjustment
    
    // Save size
    await page.click('button:has-text("Add Size")');
    await page.waitForTimeout(2000);
    
    // Verify size was added
    await expect(page.locator('.size-item')).toHaveCount(1);
    console.log('✅ Size management working correctly');

    // Step 8: Upload Image
    console.log('Step 8: Testing image upload...');
    
    // Create a test image file (1x1 pixel PNG)
    const testImageBuffer = Buffer.from('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==', 'base64');
    
    // Upload image
    const fileInput = page.locator('.color-image-input');
    await fileInput.setInputFiles({
      name: 'test-image.png',
      mimeType: 'image/png',
      buffer: testImageBuffer
    });
    
    await page.waitForTimeout(2000);
    
    // Verify image preview appears
    await expect(page.locator('.image-preview')).toBeVisible();
    console.log('✅ Image upload working correctly');

    // Step 9: Add Second Color to Test Multi-Color Stock Allocation
    console.log('Step 9: Testing multi-color stock allocation...');
    await page.click('button:has-text("Add Color")');
    await page.waitForTimeout(1000);
    
    // Configure second color
    const secondColorCard = page.locator('.color-item').nth(1);
    await secondColorCard.locator('.selected-color-display').click();
    await page.click('.color-option:has-text("Red")');
    await secondColorCard.locator('input[placeholder="0"]:last-of-type').fill('60'); // This should exceed remaining stock
    await page.waitForTimeout(1000);
    
    // Verify stock allocation summary appears
    await expect(page.locator('.bg-blue-50:has-text("Overall Stock Allocation")')).toBeVisible();
    
    // Check if over-allocation warning appears
    const overAllocationWarning = page.locator('text=⚠️ Total color stock allocation exceeds general stock limit');
    if (await overAllocationWarning.isVisible()) {
      console.log('✅ Over-allocation warning displayed correctly');
    }

    // Step 10: Navigate to Specifications Tab
    console.log('Step 10: Testing Specifications tab...');
    await page.click('[data-tab="specifications"]');
    await page.waitForTimeout(1000);
    
    // Add specification
    await page.click('button:has-text("Add Specification")');
    await page.waitForTimeout(500);
    
    // Fill specification
    const specCard = page.locator('.specification-item').first();
    await specCard.locator('input[placeholder="e.g., Material, Size, Weight"]').fill('Material');
    await specCard.locator('input[placeholder="e.g., Cotton, Large, 2kg"]').fill('Premium Cotton Blend');
    
    console.log('✅ Specifications tab working correctly');

    // Step 11: Final Screenshots and Validation
    console.log('Step 11: Taking final screenshots...');
    
    // Screenshot of completed form
    await page.screenshot({ 
      path: 'tests/screenshots/vendor-product-complete.png',
      fullPage: true 
    });

    // Go back to colors tab to verify everything is still there
    await page.click('[data-tab="colors"]');
    await page.waitForTimeout(1000);
    
    await page.screenshot({ 
      path: 'tests/screenshots/vendor-colors-final.png',
      fullPage: true 
    });

    // Step 12: Submit Form (Optional - comment out if you don't want to actually create the product)
    /*
    console.log('Step 12: Submitting product...');
    await page.click('button:has-text("Save Product")');
    await page.waitForTimeout(5000);
    
    // Check for success or error
    const successModal = page.locator('.modal:has-text("Success")');
    const errorModal = page.locator('.modal:has-text("Error")');
    
    if (await successModal.isVisible()) {
      console.log('✅ Product created successfully');
      await page.screenshot({ 
        path: 'tests/screenshots/vendor-product-success.png',
        fullPage: true 
      });
    } else if (await errorModal.isVisible()) {
      console.log('❌ Product creation failed');
      await page.screenshot({ 
        path: 'tests/screenshots/vendor-product-error.png',
        fullPage: true 
      });
    }
    */

    console.log('🎉 Comprehensive vendor product creation test completed successfully!');
    
    // Final verification - ensure all key elements are present
    await expect(page.locator('.color-item')).toHaveCount(2); // Two colors
    await expect(page.locator('.size-item')).toHaveCount(1); // One size
    await expect(page.locator('.image-preview')).toHaveCount(1); // One image
    
    console.log('✅ All functionality verified successfully');
  });

  test('should test stock validation edge cases', async () => {
    console.log('🧪 Testing stock validation edge cases...');
    
    // Login and navigate to product creation
    await page.goto('http://localhost:8000/login');
    await page.fill('input[name="email"]', 'gogoh3296@gmail.com');
    await page.fill('input[name="password"]', 'Fifa2021');
    await page.click('button[type="submit"]');
    await page.waitForURL('**/vendor/dashboard**', { timeout: 15000 });
    
    await page.goto('http://localhost:8000/vendor/products/create');
    await page.waitForSelector('#vendor-product-create-app', { timeout: 10000 });
    await page.waitForTimeout(2000);
    
    // Set up basic info with low stock for testing
    await page.fill('input[name="name"]', 'Stock Validation Test Product');
    await page.selectOption('select[name="category_id"]', { index: 1 });
    await page.selectOption('select[name="branch_id"]', { index: 1 });
    await page.fill('input[name="price"]', '100.00');
    await page.fill('input[name="stock"]', '10'); // Low stock for testing
    
    // Go to colors tab
    await page.click('[data-tab="colors"]');
    await page.waitForTimeout(1000);
    
    // Add color and test various stock scenarios
    await page.click('button:has-text("Add Color")');
    await page.click('.selected-color-display');
    await page.click('.color-option:has-text("Blue")');
    
    // Test 1: Exact stock allocation
    await page.fill('.color-item input[placeholder="0"]:last-of-type', '10');
    await page.waitForTimeout(1000);
    
    // Test 2: Add second color - should not allow any stock
    await page.click('button:has-text("Add Color")');
    const secondColor = page.locator('.color-item').nth(1);
    await secondColor.locator('.selected-color-display').click();
    await page.click('.color-option:has-text("Red")');
    await secondColor.locator('input[placeholder="0"]:last-of-type').fill('5');
    await page.waitForTimeout(1000);
    
    // Verify second color stock was corrected to 0
    const secondColorStock = await secondColor.locator('input[placeholder="0"]:last-of-type').inputValue();
    expect(parseInt(secondColorStock)).toBe(0);
    
    console.log('✅ Stock validation edge cases working correctly');
  });
});
