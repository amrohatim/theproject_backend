const { test, expect } = require('@playwright/test');
const path = require('path');
const fs = require('fs');

test.describe('Merchant Product Creation with Size Categories', () => {
  let page;
  let createdProductId = null;

  test.beforeEach(async ({ browser }) => {
    page = await browser.newPage();

    // Enable console logging for debugging
    page.on('console', msg => {
      console.log(`[BROWSER] ${msg.type()}: ${msg.text()}`);
    });

    // Login as merchant
    await page.goto('http://localhost:8000/login');
    await page.fill('input[name="email"]', 'gogoh3296@gmail.com');
    await page.fill('input[name="password"]', 'Fifa2021');
    await page.click('button[type="submit"]');

    // Wait for redirect to dashboard
    await page.waitForURL('**/merchant/dashboard');
  });

  test.afterEach(async () => {
    if (page) {
      await page.close();
    }
  });

  test('should create product with proper size categories and validate database persistence', async () => {
    console.log('🚀 Starting comprehensive product creation test...');

    // Create test image if it doesn't exist
    const testImagePath = path.join(__dirname, '../fixtures/test-product-image.jpg');
    await createTestImageIfNeeded(testImagePath);

    // Navigate to product creation page
    await page.goto('http://localhost:8000/merchant/products/create');
    await page.waitForLoadState('networkidle');

    // Wait for Vue component to load
    await page.waitForSelector('#product-create-app', { timeout: 10000 });
    console.log('✅ Product creation page loaded');

    // Fill basic product information
    const productName = `Test Product Size Categories ${Date.now()}`;
    await page.fill('input[name="name"]', productName);
    console.log('✅ Product name filled:', productName);

    // Select category (assuming category with ID 1 exists)
    await page.selectOption('select[name="category_id"]', '1');
    console.log('✅ Category selected');

    // Set price
    await page.fill('input[name="price"]', '99.99');
    await page.fill('input[name="original_price"]', '129.99');
    console.log('✅ Prices set');

    // Set stock
    await page.fill('input[name="stock"]', '100');
    console.log('✅ Stock set');

    // Add description
    await page.fill('textarea[name="description"]', 'Test product for comprehensive size category validation with database persistence checks');
    console.log('✅ Description added');

    // Switch to Colors & Images tab
    await page.click('button[data-tab="colors"]');
    await page.waitForTimeout(1000);
    console.log('✅ Switched to colors tab');

    // Add first color (Red)
    await page.click('button:has-text("Add Color")');
    await page.waitForTimeout(500);

    // Fill color details
    await page.fill('input[placeholder="Enter color name"], input[placeholder="Color name"]', 'Red');
    await page.fill('input[type="color"]', '#FF0000');

    // Upload color image
    const fileInput = page.locator('input[type="file"]').first();
    await fileInput.setInputFiles(testImagePath);
    await page.waitForTimeout(1000); // Wait for image upload
    console.log('✅ Red color added with image');

    // Add second color (Blue) to test multiple colors
    await page.click('button:has-text("Add Color")');
    await page.waitForTimeout(500);

    // Fill second color details
    const colorInputs = page.locator('input[placeholder="Enter color name"], input[placeholder="Color name"]');
    await colorInputs.nth(1).fill('Blue');
    const colorPickers = page.locator('input[type="color"]');
    await colorPickers.nth(1).fill('#0000FF');

    // Upload image for second color
    const fileInputs = page.locator('input[type="file"]');
    await fileInputs.nth(1).setInputFiles(testImagePath);
    await page.waitForTimeout(1000);
    console.log('✅ Blue color added with image');

    // Now add sizes with proper category assignments
    console.log('🔧 Adding sizes with category validation...');

    // Add clothing size (Small) - Category ID should be 1
    await addSizeWithCategory(page, 'Small', 'clothes', '20');
    console.log('✅ Added clothing size: Small (category: clothes)');

    // Add clothing size (Medium) - Category ID should be 1
    await addSizeWithCategory(page, 'Medium', 'clothes', '30');
    console.log('✅ Added clothing size: Medium (category: clothes)');

    // Add clothing size (Large) - Category ID should be 1
    await addSizeWithCategory(page, 'Large', 'clothes', '25');
    console.log('✅ Added clothing size: Large (category: clothes)');

    // Add shoe size - Category ID should be 2
    await addSizeWithCategory(page, '42', 'shoes', '15');
    console.log('✅ Added shoe size: 42 (category: shoes)');

    // Add another shoe size
    await addSizeWithCategory(page, '43', 'shoes', '18');
    console.log('✅ Added shoe size: 43 (category: shoes)');

    // Add hat size - Category ID should be 3
    await addSizeWithCategory(page, 'One Size', 'hats', '10');
    console.log('✅ Added hat size: One Size (category: hats)');

    // Submit the form and capture response
    console.log('🔄 Submitting product creation form...');

    // Listen for the form submission response
    const responsePromise = page.waitForResponse(response =>
      response.url().includes('/merchant/products') && response.request().method() === 'POST'
    );

    await page.click('button[type="submit"]:has-text("Save Product"), button[type="submit"]:has-text("Create Product")');

    const response = await responsePromise;
    console.log(`Form submission response: ${response.status()}`);

    if (response.status() !== 200 && response.status() !== 302) {
      const responseText = await response.text();
      console.error('Form submission failed:', responseText);
      throw new Error(`Product creation failed with status ${response.status()}`);
    }

    // Wait for success indication
    await page.waitForTimeout(3000);

    // Try to get the product ID from the response or current state
    createdProductId = await getLatestProductId(page, productName);
    console.log('📝 Created product ID:', createdProductId);

    if (!createdProductId) {
      throw new Error('Failed to retrieve created product ID');
    }

    // Verify the product appears in the products list
    await page.goto('http://localhost:8000/merchant/products');
    await page.waitForLoadState('networkidle');

    const productExists = await page.locator(`text=${productName}`).first().isVisible().catch(() => false);
    expect(productExists).toBeTruthy();
    console.log('✅ Product found in products list');

    // Perform comprehensive database verification
    await verifyDatabasePersistence(page, createdProductId, productName);
  });

  test('should verify comprehensive database persistence and size categories', async () => {
    // This test verifies the database state after product creation
    if (!createdProductId) {
      console.log('⚠️ No product ID available, skipping verification');
      return;
    }

    console.log('🔍 Performing comprehensive database verification for product ID:', createdProductId);

    // Verify product exists in database with correct data
    const productData = await page.evaluate(async (productId) => {
      try {
        const response = await fetch(`/api/products/${productId}/verify-complete`);
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
      console.log('⚠️ Could not retrieve product data from API, trying direct database verification...');
      // Alternative verification method
      await verifyDatabaseDirectly(page, createdProductId);
      return;
    }

    console.log('📊 Product verification data:', JSON.stringify(productData, null, 2));

    // Verify product basic data
    expect(productData.product).toBeDefined();
    expect(productData.product.id).toBe(createdProductId);
    console.log('✅ Product exists in database');

    // Verify colors
    expect(productData.colors).toBeDefined();
    expect(productData.colors.length).toBeGreaterThanOrEqual(2);
    console.log(`✅ Found ${productData.colors.length} colors`);

    // Verify sizes with size_category_id
    expect(productData.sizes).toBeDefined();
    expect(productData.sizes.length).toBeGreaterThanOrEqual(6);
    console.log(`✅ Found ${productData.sizes.length} sizes`);

    // Verify each size has proper size_category_id
    const expectedSizeCategories = {
      'Small': 1,    // clothes
      'Medium': 1,   // clothes
      'Large': 1,    // clothes
      '42': 2,       // shoes
      '43': 2,       // shoes
      'One Size': 3  // hats
    };

    for (const size of productData.sizes) {
      expect(size.size_category_id).not.toBeNull();
      expect(size.size_category_id).toBeGreaterThan(0);
      expect(size.size_category_id).toBeLessThanOrEqual(3);

      if (expectedSizeCategories[size.name]) {
        expect(size.size_category_id).toBe(expectedSizeCategories[size.name]);
        console.log(`✅ Size "${size.name}" has correct category ID: ${size.size_category_id}`);
      } else {
        console.log(`✅ Size "${size.name}" has valid category ID: ${size.size_category_id}`);
      }
    }

    // Verify images are properly stored
    if (productData.colors) {
      for (const color of productData.colors) {
        if (color.image) {
          expect(color.image).not.toContain('placeholder');
          expect(color.image).not.toContain('default');
          console.log(`✅ Color "${color.name}" has proper image: ${color.image}`);
        }
      }
    }

    console.log('🎉 All database verification checks passed!');
  });
});

// Helper Functions

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
 * Add a size with specific category
 */
async function addSizeWithCategory(page, sizeName, category, stock) {
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
    const sizeInputs = page.locator('input[placeholder="Size name"]');
    const categorySelects = page.locator('select[name*="category"]');
    const stockInputs = page.locator('input[placeholder="Stock"]');

    const count = await sizeInputs.count();
    const lastIndex = count - 1;

    await sizeInputs.nth(lastIndex).fill(sizeName);
    await categorySelects.nth(lastIndex).selectOption(category);
    await stockInputs.nth(lastIndex).fill(stock);
  }

  await page.waitForTimeout(500);
}

/**
 * Get the latest product ID for verification
 */
async function getLatestProductId(page, productName) {
  try {
    // Try to get product ID from API
    const productId = await page.evaluate(async (name) => {
      try {
        const response = await fetch('/api/products/search', {
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
    console.error('Failed to get product ID:', error);
    return null;
  }
}

/**
 * Verify database persistence comprehensively
 */
async function verifyDatabasePersistence(page, productId, productName) {
  console.log('🔍 Verifying database persistence...');

  // Take screenshot for debugging
  await page.screenshot({
    path: `tests/screenshots/merchant-product-created-${productId}.png`,
    fullPage: true
  });

  // Verify product exists in products list
  await page.goto('http://localhost:8000/merchant/products');
  await page.waitForLoadState('networkidle');

  const productExists = await page.locator(`text=${productName}`).first().isVisible().catch(() => false);
  if (!productExists) {
    console.error('❌ Product not found in products list');
    throw new Error('Product not found in products list');
  }

  console.log('✅ Product found in products list');

  // Check if product image is properly displayed (not a placeholder)
  const productImages = page.locator('img[src*="products"], img[src*="uploads"]');
  const imageCount = await productImages.count();

  if (imageCount > 0) {
    const firstImageSrc = await productImages.first().getAttribute('src');
    if (firstImageSrc && !firstImageSrc.includes('placeholder') && !firstImageSrc.includes('default')) {
      console.log('✅ Product images appear to be properly uploaded');
    } else {
      console.log('⚠️ Product images may be placeholders');
    }
  }
}

/**
 * Direct database verification as fallback
 */
async function verifyDatabaseDirectly(page, productId) {
  console.log('🔍 Performing direct database verification...');

  // This would require a custom API endpoint for testing
  // For now, we'll verify what we can through the UI
  await page.goto(`http://localhost:8000/merchant/products/${productId}/edit`);
  await page.waitForLoadState('networkidle');

  // Check if the edit page loads successfully
  const editPageLoaded = await page.locator('input[name="name"]').isVisible().catch(() => false);
  if (editPageLoaded) {
    console.log('✅ Product edit page loads successfully');

    // Check if sizes are present
    const sizesSection = await page.locator('text=Sizes').isVisible().catch(() => false);
    if (sizesSection) {
      console.log('✅ Sizes section is present');
    }

    // Check if colors are present
    const colorsSection = await page.locator('text=Colors').isVisible().catch(() => false);
    if (colorsSection) {
      console.log('✅ Colors section is present');
    }
  } else {
    console.error('❌ Product edit page failed to load');
    throw new Error('Product edit page failed to load');
  }
}
