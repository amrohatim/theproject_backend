/**
 * Comprehensive test to reproduce merchant product creation issues:
 * 1. Products with sizes not being saved to database
 * 2. Product images not saving to correct paths
 */

const { test, expect } = require('@playwright/test');
const path = require('path');

test.describe('Merchant Product Creation Debug', () => {
  let page;
  let context;

  test.beforeAll(async ({ browser }) => {
    context = await browser.newContext();
    page = await context.newPage();
    
    // Enable console logging
    page.on('console', msg => {
      console.log(`[BROWSER] ${msg.type()}: ${msg.text()}`);
    });
    
    // Enable request/response logging
    page.on('request', request => {
      if (request.url().includes('/merchant/products') || request.url().includes('/api/')) {
        console.log(`[REQUEST] ${request.method()} ${request.url()}`);
      }
    });
    
    page.on('response', response => {
      if (response.url().includes('/merchant/products') || response.url().includes('/api/')) {
        console.log(`[RESPONSE] ${response.status()} ${response.url()}`);
      }
    });
  });

  test.afterAll(async () => {
    await context.close();
  });

  test('should reproduce product creation issues with sizes and images', async () => {
    // Step 1: Login as merchant
    console.log('Step 1: Logging in as merchant...');
    await page.goto('http://localhost:8000/login');
    
    // Fill login form
    await page.fill('input[name="email"]', 'amrozr26@gmail.com');
    await page.fill('input[name="password"]', 'Fifa2021');
    await page.click('button[type="submit"]');
    
    // Wait for redirect to dashboard
    await page.waitForURL('**/merchant/dashboard**', { timeout: 10000 });
    console.log('✓ Successfully logged in');

    // Step 2: Navigate to product creation page
    console.log('Step 2: Navigating to product creation...');
    await page.goto('http://localhost:8000/merchant/products/create');
    await page.waitForLoadState('networkidle');
    console.log('✓ Product creation page loaded');

    // Step 3: Fill basic product information
    console.log('Step 3: Filling basic product information...');
    await page.fill('input[id="name"]', 'Test Product with Sizes');
    
    // Select category
    await page.click('select[name="category_id"]');
    await page.selectOption('select[name="category_id"]', { index: 1 });
    
    // Fill price and stock
    await page.fill('input[name="price"]', '99.99');
    await page.fill('input[name="original_price"]', '129.99');
    await page.fill('input[name="stock"]', '100');
    await page.fill('textarea[name="description"]', 'Test product with color variants and sizes');
    
    console.log('✓ Basic information filled');

    // Step 4: Switch to Colors & Images tab
    console.log('Step 4: Switching to Colors & Images tab...');
    await page.click('button[data-tab="colors"]');
    await page.waitForTimeout(1000);

    // Step 5: Add first color variant
    console.log('Step 5: Adding first color variant...');
    await page.click('button:has-text("Add Color")');
    await page.waitForTimeout(1000);

    // Fill color information
    await page.fill('input[placeholder="Enter color name"]', 'Red');
    
    // Upload color image
    const testImagePath = path.join(__dirname, '../fixtures/test-product-image.jpg');
    console.log(`Uploading image from: ${testImagePath}`);
    
    // Create a test image if it doesn't exist
    const fs = require('fs');
    if (!fs.existsSync(testImagePath)) {
      const testImageDir = path.dirname(testImagePath);
      if (!fs.existsSync(testImageDir)) {
        fs.mkdirSync(testImageDir, { recursive: true });
      }
      // Create a simple 1x1 pixel image for testing
      const imageBuffer = Buffer.from([
        0xFF, 0xD8, 0xFF, 0xE0, 0x00, 0x10, 0x4A, 0x46, 0x49, 0x46, 0x00, 0x01,
        0x01, 0x01, 0x00, 0x48, 0x00, 0x48, 0x00, 0x00, 0xFF, 0xDB, 0x00, 0x43,
        0x00, 0x08, 0x06, 0x06, 0x07, 0x06, 0x05, 0x08, 0x07, 0x07, 0x07, 0x09,
        0x09, 0x08, 0x0A, 0x0C, 0x14, 0x0D, 0x0C, 0x0B, 0x0B, 0x0C, 0x19, 0x12,
        0x13, 0x0F, 0x14, 0x1D, 0x1A, 0x1F, 0x1E, 0x1D, 0x1A, 0x1C, 0x1C, 0x20,
        0x24, 0x2E, 0x27, 0x20, 0x22, 0x2C, 0x23, 0x1C, 0x1C, 0x28, 0x37, 0x29,
        0x2C, 0x30, 0x31, 0x34, 0x34, 0x34, 0x1F, 0x27, 0x39, 0x3D, 0x38, 0x32,
        0x3C, 0x2E, 0x33, 0x34, 0x32, 0xFF, 0xC0, 0x00, 0x11, 0x08, 0x00, 0x01,
        0x00, 0x01, 0x01, 0x01, 0x11, 0x00, 0x02, 0x11, 0x01, 0x03, 0x11, 0x01,
        0xFF, 0xC4, 0x00, 0x14, 0x00, 0x01, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00,
        0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x08, 0xFF, 0xC4,
        0x00, 0x14, 0x10, 0x01, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00,
        0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0xFF, 0xDA, 0x00, 0x0C,
        0x03, 0x01, 0x00, 0x02, 0x11, 0x03, 0x11, 0x00, 0x3F, 0x00, 0xB2, 0xC0,
        0x07, 0xFF, 0xD9
      ]);
      fs.writeFileSync(testImagePath, imageBuffer);
    }
    
    await page.setInputFiles('input[type="file"]', testImagePath);
    await page.waitForTimeout(2000);
    console.log('✓ Color image uploaded');

    // Step 6: Add sizes to the color variant
    console.log('Step 6: Adding sizes to color variant...');
    
    // Look for the "Add Size" button in the size management section
    await page.click('button:has-text("Add Size")');
    await page.waitForTimeout(1000);

    // Fill size information in the modal
    await page.selectOption('select[name="category"]', 'clothes');
    await page.selectOption('select[name="size_name"]', 'Small');
    await page.fill('input[name="stock"]', '25');
    
    // Save the size
    await page.click('button:has-text("Add Size"):not(:disabled)');
    await page.waitForTimeout(1000);
    console.log('✓ Small size added');

    // Add Medium size
    await page.click('button:has-text("Add Size")');
    await page.waitForTimeout(1000);
    await page.selectOption('select[name="category"]', 'clothes');
    await page.selectOption('select[name="size_name"]', 'Medium');
    await page.fill('input[name="stock"]', '35');
    await page.click('button:has-text("Add Size"):not(:disabled)');
    await page.waitForTimeout(1000);
    console.log('✓ Medium size added');

    // Add Large size
    await page.click('button:has-text("Add Size")');
    await page.waitForTimeout(1000);
    await page.selectOption('select[name="category"]', 'clothes');
    await page.selectOption('select[name="size_name"]', 'Large');
    await page.fill('input[name="stock"]', '40');
    await page.click('button:has-text("Add Size"):not(:disabled)');
    await page.waitForTimeout(1000);
    console.log('✓ Large size added');

    // Step 7: Submit the form and capture the response
    console.log('Step 7: Submitting product creation form...');
    
    // Listen for the form submission
    const responsePromise = page.waitForResponse(response => 
      response.url().includes('/merchant/products') && response.request().method() === 'POST'
    );
    
    await page.click('button[type="submit"]:has-text("Save Product")');
    
    const response = await responsePromise;
    console.log(`Form submission response: ${response.status()}`);
    
    if (response.status() !== 200 && response.status() !== 302) {
      const responseText = await response.text();
      console.error('Form submission failed:', responseText);
    }

    // Step 8: Wait for success message or error
    await page.waitForTimeout(3000);
    
    // Check for success message
    const successMessage = await page.locator('text=Product Created Successfully').first();
    const isSuccessVisible = await successMessage.isVisible().catch(() => false);
    
    if (isSuccessVisible) {
      console.log('✓ Success message displayed');
    } else {
      console.log('⚠ No success message found');
    }

    // Step 9: Navigate to products list to verify creation
    console.log('Step 8: Checking products list...');
    await page.goto('http://localhost:8000/merchant/products');
    await page.waitForLoadState('networkidle');
    
    // Look for the created product
    const productExists = await page.locator('text=Test Product with Sizes').first().isVisible().catch(() => false);
    
    if (productExists) {
      console.log('✓ Product found in products list');
      
      // Check if image is displayed correctly (not a placeholder)
      const productImage = page.locator('img').first();
      const imageSrc = await productImage.getAttribute('src');
      console.log(`Product image src: ${imageSrc}`);
      
      if (imageSrc && !imageSrc.includes('placeholder') && !imageSrc.includes('default')) {
        console.log('✓ Product image appears to be correctly uploaded');
      } else {
        console.log('⚠ Product image appears to be a placeholder');
      }
    } else {
      console.log('❌ Product NOT found in products list - Issue confirmed!');
    }

    // Step 10: Take screenshot for debugging
    await page.screenshot({ 
      path: 'tests/screenshots/product-creation-debug.png',
      fullPage: true 
    });
    console.log('✓ Screenshot saved for debugging');
  });
});
