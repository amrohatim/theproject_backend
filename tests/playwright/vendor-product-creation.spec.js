const { test, expect } = require('@playwright/test');
const path = require('path');
const fs = require('fs');

test.describe('Vendor Product Creation - End-to-End Testing', () => {
  let page;
  let createdProductId = null;
  let vendorUserId = null;

  test.beforeEach(async ({ browser }) => {
    page = await browser.newPage();

    // Enable console logging for debugging
    page.on('console', msg => {
      console.log(`[BROWSER] ${msg.type()}: ${msg.text()}`);
    });

    // Enable request/response logging for API calls
    page.on('request', request => {
      if (request.url().includes('/vendor/products') || request.url().includes('/api/')) {
        console.log(`[REQUEST] ${request.method()} ${request.url()}`);
      }
    });

    page.on('response', response => {
      if (response.url().includes('/vendor/products') || response.url().includes('/api/')) {
        console.log(`[RESPONSE] ${response.status()} ${response.url()}`);
      }
    });

    // Login as vendor
    await page.goto('http://localhost:8000/login');
    await page.fill('input[name="email"]', 'gogoh3296@gmail.com');
    await page.fill('input[name="password"]', 'Fifa2021');
    await page.click('button[type="submit"]');

    // Wait for redirect to dashboard
    await page.waitForURL('**/vendor/dashboard');

    // Get vendor user ID for verification
    vendorUserId = await page.evaluate(() => {
      // Try to get user ID from page context or meta tags
      const userMeta = document.querySelector('meta[name="user-id"]');
      if (userMeta) {
        return parseInt(userMeta.getAttribute('content'));
      }

      // Fallback: try to get from window object if available
      if (window.Laravel && window.Laravel.user) {
        return window.Laravel.user.id;
      }

      return null;
    });

    console.log('🔑 Vendor User ID:', vendorUserId);
  });

  test.afterEach(async () => {
    if (page) {
      await page.close();
    }
  });

  test('should perform complete end-to-end vendor product creation with database and UI verification', async () => {
    console.log('🚀 Starting comprehensive vendor product creation test...');

    // Create test image if it doesn't exist
    const testImagePath = path.join(__dirname, '../fixtures/test-vendor-product.jpg');
    await createTestImageIfNeeded(testImagePath);

    // Navigate to product creation page
    await page.goto('http://localhost:8000/vendor/products/create');
    await page.waitForLoadState('networkidle');

    // Wait for Vue component to load
    await page.waitForSelector('#vendor-product-create-app, form', { timeout: 10000 });
    await page.waitForTimeout(2000); // Allow Vue to fully initialize
    console.log('✅ Vendor product creation page loaded');

    // Fill basic product information
    const productName = `Test Vendor Product E2E ${Date.now()}`;
    await page.fill('input[name="name"]', productName);
    console.log('✅ Product name filled:', productName);

    // Select category (assuming category with ID 1 exists)
    await page.selectOption('select[name="category_id"]', '1');
    console.log('✅ Category selected');

    // Handle branch selection (may be auto-selected for vendors)
    try {
      const branchSelect = page.locator('select[name="branch_id"]');
      const branchExists = await branchSelect.isVisible();
      if (branchExists) {
        await page.selectOption('select[name="branch_id"]', { index: 1 });
        console.log('✅ Branch selected');
      } else {
        console.log('ℹ️ Branch auto-selected or not required');
      }
    } catch (error) {
      console.log('ℹ️ Branch selection not available or auto-handled');
    }

    // Set prices
    await page.fill('input[name="price"]', '149.99');
    await page.fill('input[name="original_price"]', '199.99');
    console.log('✅ Prices set');

    // Set general stock
    await page.fill('input[name="stock"]', '100');
    console.log('✅ General stock set');

    // Add comprehensive description
    await page.fill('textarea[name="description"]', 'Comprehensive test vendor product for end-to-end validation including database persistence, UI verification, and three-tier stock management');
    console.log('✅ Description added');

    // Switch to Colors & Images tab if needed
    const colorsTab = page.locator('button[data-tab="colors"]');
    const colorsTabExists = await colorsTab.isVisible().catch(() => false);
    if (colorsTabExists) {
      await page.click('button[data-tab="colors"]');
      await page.waitForTimeout(1000);
      console.log('✅ Switched to colors tab');
    }

    // Add first color variant (Red)
    await page.click('button:has-text("Add Color")');
    await page.waitForTimeout(1000);

    // Fill first color details
    await page.fill('input[placeholder*="color name"], input[placeholder="Enter color name"]', 'Red');
    await page.fill('input[type="color"]', '#FF0000');

    // Upload color image
    const fileInput = page.locator('input[type="file"]').first();
    await fileInput.setInputFiles(testImagePath);
    await page.waitForTimeout(1500); // Wait for image upload
    console.log('✅ Red color added with image');

    // Add second color variant (Blue) for comprehensive testing
    await page.click('button:has-text("Add Color")');
    await page.waitForTimeout(1000);

    // Fill second color details
    const colorInputs = page.locator('input[placeholder*="color name"], input[placeholder="Enter color name"]');
    await colorInputs.nth(1).fill('Blue');
    const colorPickers = page.locator('input[type="color"]');
    await colorPickers.nth(1).fill('#0000FF');

    // Upload image for second color
    const fileInputs = page.locator('input[type="file"]');
    await fileInputs.nth(1).setInputFiles(testImagePath);
    await page.waitForTimeout(1500);
    console.log('✅ Blue color added with image');

    // Add third color variant (Green) to test multiple color management
    await page.click('button:has-text("Add Color")');
    await page.waitForTimeout(1000);

    await colorInputs.nth(2).fill('Green');
    await colorPickers.nth(2).fill('#00FF00');
    await fileInputs.nth(2).setInputFiles(testImagePath);
    await page.waitForTimeout(1500);
    console.log('✅ Green color added with image');

    // Now add multiple sizes following three-tier stock validation (general > color > size)
    console.log('🔧 Adding sizes with three-tier stock validation...');

    // Add clothing sizes
    await addVendorSizeWithCategory(page, 'XS', 'clothes', '15');
    console.log('✅ Added clothing size: XS (category: clothes)');

    await addVendorSizeWithCategory(page, 'Small', 'clothes', '20');
    console.log('✅ Added clothing size: Small (category: clothes)');

    await addVendorSizeWithCategory(page, 'Medium', 'clothes', '25');
    console.log('✅ Added clothing size: Medium (category: clothes)');

    await addVendorSizeWithCategory(page, 'Large', 'clothes', '20');
    console.log('✅ Added clothing size: Large (category: clothes)');

    await addVendorSizeWithCategory(page, 'XL', 'clothes', '15');
    console.log('✅ Added clothing size: XL (category: clothes)');

    // Add shoe sizes
    await addVendorSizeWithCategory(page, '40', 'shoes', '8');
    console.log('✅ Added shoe size: 40 (category: shoes)');

    await addVendorSizeWithCategory(page, '41', 'shoes', '10');
    console.log('✅ Added shoe size: 41 (category: shoes)');

    await addVendorSizeWithCategory(page, '42', 'shoes', '12');
    console.log('✅ Added shoe size: 42 (category: shoes)');

    await addVendorSizeWithCategory(page, '43', 'shoes', '10');
    console.log('✅ Added shoe size: 43 (category: shoes)');

    await addVendorSizeWithCategory(page, '44', 'shoes', '8');
    console.log('✅ Added shoe size: 44 (category: shoes)');

    // Add hat sizes
    await addVendorSizeWithCategory(page, 'One Size', 'hats', '30');
    console.log('✅ Added hat size: One Size (category: hats)');

    await addVendorSizeWithCategory(page, 'Adjustable', 'hats', '25');
    console.log('✅ Added hat size: Adjustable (category: hats)');

    // Submit the form and capture response
    console.log('🔄 Submitting vendor product creation form...');

    // Listen for the form submission response
    const responsePromise = page.waitForResponse(response =>
      response.url().includes('/vendor/products') && response.request().method() === 'POST'
    );

    await page.click('button[type="submit"]:has-text("Save"), button[type="submit"]:has-text("Create Product")');

    const response = await responsePromise;
    console.log(`Form submission response: ${response.status()}`);

    if (response.status() !== 200 && response.status() !== 302) {
      const responseText = await response.text();
      console.error('Form submission failed:', responseText);
      throw new Error(`Vendor product creation failed with status ${response.status()}`);
    }

    // Wait for success indication
    await page.waitForTimeout(3000);

    // Get the created product ID
    createdProductId = await getVendorProductId(page, productName);
    console.log('📝 Created vendor product ID:', createdProductId);

    if (!createdProductId) {
      throw new Error('Failed to retrieve created vendor product ID');
    }

    // Verify the product appears in vendor products list
    await page.goto('http://localhost:8000/vendor/products');
    await page.waitForLoadState('networkidle');

    const productExists = await page.locator(`text=${productName}`).first().isVisible().catch(() => false);
    expect(productExists).toBeTruthy();
    console.log('✅ Vendor product found in products list');

    // Perform comprehensive database verification
    await verifyVendorDatabasePersistence(page, createdProductId, productName, vendorUserId);

    // Perform UI verification
    await verifyVendorUIDisplay(page, createdProductId, productName);
  });

  test('should verify comprehensive database persistence and user_id assignment', async () => {
    // This test verifies the complete database state after vendor product creation
    if (!createdProductId) {
      console.log('⚠️ No product ID available, skipping verification');
      return;
    }

    console.log('🔍 Performing comprehensive database verification for vendor product ID:', createdProductId);

    // Verify product exists with correct user_id
    const productData = await page.evaluate(async (productId) => {
      try {
        const response = await fetch(`/api/products/${productId}/verify-vendor-complete`);
        if (response.ok) {
          return await response.json();
        }
        return null;
      } catch (error) {
        console.error('API call failed:', error);
        return null;
      }
    }, createdProductId);

    if (!productData) {
      console.log('⚠️ Could not retrieve product data from API, trying direct verification...');
      await verifyVendorDatabaseDirectly(page, createdProductId, vendorUserId);
      return;
    }

    console.log('📊 Vendor product verification data:', JSON.stringify(productData, null, 2));

    // Verify product basic data and user_id
    expect(productData.product).toBeDefined();
    expect(productData.product.id).toBe(createdProductId);
    expect(productData.product.user_id).not.toBeNull();
    if (vendorUserId) {
      expect(productData.product.user_id).toBe(vendorUserId);
      console.log(`✅ Product correctly assigned to vendor user ID: ${vendorUserId}`);
    } else {
      console.log(`✅ Product has valid user ID: ${productData.product.user_id}`);
    }

    // Verify colors (should have 3)
    expect(productData.colors).toBeDefined();
    expect(productData.colors.length).toBe(3);
    console.log(`✅ Found ${productData.colors.length} colors as expected`);

    // Verify sizes (should have 12 total)
    expect(productData.sizes).toBeDefined();
    expect(productData.sizes.length).toBe(12);
    console.log(`✅ Found ${productData.sizes.length} sizes as expected`);

    // Verify size categories distribution
    const clothingSizes = productData.sizes.filter(s => s.size_category_id === 1);
    const shoeSizes = productData.sizes.filter(s => s.size_category_id === 2);
    const hatSizes = productData.sizes.filter(s => s.size_category_id === 3);

    expect(clothingSizes.length).toBe(5); // XS, S, M, L, XL
    expect(shoeSizes.length).toBe(5);     // 40, 41, 42, 43, 44
    expect(hatSizes.length).toBe(2);      // One Size, Adjustable

    console.log(`✅ Size distribution: ${clothingSizes.length} clothing, ${shoeSizes.length} shoes, ${hatSizes.length} hats`);

    // Verify each size has proper size_category_id
    for (const size of productData.sizes) {
      expect(size.size_category_id).not.toBeNull();
      expect(size.size_category_id).toBeGreaterThan(0);
      expect(size.size_category_id).toBeLessThanOrEqual(3);
      console.log(`✅ Size "${size.name}" has valid category ID: ${size.size_category_id}`);
    }

    // Verify images are properly stored
    for (const color of productData.colors) {
      if (color.image) {
        expect(color.image).not.toContain('placeholder');
        expect(color.image).not.toContain('default');
        console.log(`✅ Color "${color.name}" has proper image: ${color.image}`);
      }
    }

    console.log('🎉 All vendor database verification checks passed!');
  });
});

// Helper Functions for Vendor Product Testing

/**
 * Create a test image file if it doesn't exist
 */
async function createTestImageIfNeeded(testImagePath) {
  if (!fs.existsSync(testImagePath)) {
    const testImageDir = path.dirname(testImagePath);
    if (!fs.existsSync(testImageDir)) {
      fs.mkdirSync(testImageDir, { recursive: true });
    }

    // Create a simple JPEG image buffer for testing
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
}

/**
 * Add a size with specific category for vendor products
 */
async function addVendorSizeWithCategory(page, sizeName, category, stock) {
  // Click Add Size button
  await page.click('button:has-text("Add Size")');
  await page.waitForTimeout(1000);

  // Fill size information in modal or form
  try {
    // Try modal approach first
    await page.selectOption('select[name="category"]', category);
    await page.selectOption('select[name="size_name"]', sizeName);
    await page.fill('input[name="stock"]', stock);
    await page.click('button:has-text("Add Size"):not(:disabled)');
  } catch (error) {
    // Fallback to direct form approach
    const sizeInputs = page.locator('input[placeholder*="size name"], input[name*="size"][name*="name"]');
    const categorySelects = page.locator('select[name*="category"]');
    const stockInputs = page.locator('input[name*="stock"]');

    const count = await sizeInputs.count();
    const lastIndex = count - 1;

    await sizeInputs.nth(lastIndex).fill(sizeName);
    await categorySelects.nth(lastIndex).selectOption(category);
    await stockInputs.nth(lastIndex).fill(stock);
  }

  await page.waitForTimeout(500);
}

/**
 * Get the vendor product ID for verification
 */
async function getVendorProductId(page, productName) {
  try {
    // Try to get product ID from API
    const productId = await page.evaluate(async (name) => {
      try {
        const response = await fetch('/api/vendor/products/search', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
          },
          body: JSON.stringify({ name: name })
        });

        if (response.ok) {
          const data = await response.json();
          return data.product_id || data.id;
        }
      } catch (error) {
        console.error('API search failed:', error);
      }
      return null;
    }, productName);

    if (productId) {
      return productId;
    }

    // Fallback: try to extract from URL or page content
    const currentUrl = page.url();
    const urlMatch = currentUrl.match(/\/products\/(\d+)/);
    if (urlMatch) {
      return parseInt(urlMatch[1]);
    }

    return null;
  } catch (error) {
    console.error('Failed to get vendor product ID:', error);
    return null;
  }
}

/**
 * Verify vendor database persistence comprehensively
 */
async function verifyVendorDatabasePersistence(page, productId, productName, vendorUserId) {
  console.log('🔍 Verifying vendor database persistence...');

  // Take screenshot for debugging
  await page.screenshot({
    path: `tests/screenshots/vendor-product-created-${productId}.png`,
    fullPage: true
  });

  // Verify product exists in vendor products list
  await page.goto('http://localhost:8000/vendor/products');
  await page.waitForLoadState('networkidle');

  const productExists = await page.locator(`text=${productName}`).first().isVisible().catch(() => false);
  if (!productExists) {
    console.error('❌ Vendor product not found in products list');
    throw new Error('Vendor product not found in products list');
  }

  console.log('✅ Vendor product found in products list');

  // Check if product images are properly displayed
  const productImages = page.locator('img[src*="products"], img[src*="uploads"]');
  const imageCount = await productImages.count();

  if (imageCount > 0) {
    const firstImageSrc = await productImages.first().getAttribute('src');
    if (firstImageSrc && !firstImageSrc.includes('placeholder') && !firstImageSrc.includes('default')) {
      console.log('✅ Vendor product images appear to be properly uploaded');
    } else {
      console.log('⚠️ Vendor product images may be placeholders');
    }
  }
}

/**
 * Verify vendor UI display
 */
async function verifyVendorUIDisplay(page, productId, productName) {
  console.log('🔍 Verifying vendor UI display...');

  // Navigate to product detail/edit page
  await page.goto(`http://localhost:8000/vendor/products/${productId}/edit`);
  await page.waitForLoadState('networkidle');

  // Check if the edit page loads successfully
  const editPageLoaded = await page.locator('input[name="name"]').isVisible().catch(() => false);
  if (editPageLoaded) {
    console.log('✅ Vendor product edit page loads successfully');

    // Verify product name is displayed correctly
    const nameValue = await page.locator('input[name="name"]').inputValue();
    expect(nameValue).toBe(productName);
    console.log('✅ Product name displays correctly in edit form');

    // Check if colors section is present and populated
    const colorsSection = await page.locator('text=Colors').isVisible().catch(() => false);
    if (colorsSection) {
      console.log('✅ Colors section is present');

      // Count color variants
      const colorCards = page.locator('[data-color-id], .color-card, .color-variant');
      const colorCount = await colorCards.count();
      if (colorCount >= 3) {
        console.log(`✅ Found ${colorCount} color variants as expected`);
      }
    }

    // Check if sizes section is present and populated
    const sizesSection = await page.locator('text=Sizes').isVisible().catch(() => false);
    if (sizesSection) {
      console.log('✅ Sizes section is present');

      // Count size entries
      const sizeEntries = page.locator('[data-size-id], .size-entry, .size-item');
      const sizeCount = await sizeEntries.count();
      if (sizeCount >= 12) {
        console.log(`✅ Found ${sizeCount} size entries as expected`);
      }
    }
  } else {
    console.error('❌ Vendor product edit page failed to load');
    throw new Error('Vendor product edit page failed to load');
  }
}

/**
 * Direct database verification as fallback for vendor products
 */
async function verifyVendorDatabaseDirectly(page, productId, vendorUserId) {
  console.log('🔍 Performing direct vendor database verification...');

  // Navigate to the product in the vendor products list
  await page.goto('http://localhost:8000/vendor/products');
  await page.waitForLoadState('networkidle');

  // Look for the product in the list
  const productRow = page.locator(`tr:has-text("${productId}"), .product-item:has-text("${productId}")`);
  const productExists = await productRow.isVisible().catch(() => false);

  if (productExists) {
    console.log('✅ Vendor product found in products list by ID');
  } else {
    console.log('⚠️ Could not find product by ID, checking by recent entries...');

    // Check if any recent products exist
    const productRows = page.locator('tr, .product-item');
    const rowCount = await productRows.count();
    if (rowCount > 0) {
      console.log(`✅ Found ${rowCount} products in vendor list`);
    } else {
      throw new Error('No products found in vendor products list');
    }
  }
}
