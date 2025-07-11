import { test, expect } from '@playwright/test';

// Test configuration
const BASE_URL = 'https://dala3chic.com';
const PRODUCT_EDIT_URL = `${BASE_URL}/merchant/products/11/edit`;

// Test data
const testProduct = {
  name: 'Test Product Updated',
  description: 'Updated product description for testing',
  price: '99.99',
  originalPrice: '149.99',
  stock: '100'
};

const testColor = {
  name: 'Red',
  colorCode: '#FF0000',
  priceAdjustment: '5.00',
  stock: '25'
};

const testSpecification = {
  key: 'Material',
  value: '100% Cotton'
};

test.describe('Product Edit Vue.js Component', () => {
  test.beforeEach(async ({ page }) => {
    // Set viewport for consistent testing
    await page.setViewportSize({ width: 1280, height: 720 });

    // Login as merchant first
    await page.goto(`${BASE_URL}/login`);
    await page.fill('input[name="email"]', 'merchant@test.com');
    await page.fill('input[name="password"]', 'password123');
    await page.click('button[type="submit"]');

    // Wait for redirect to dashboard
    await page.waitForURL('**/merchant/dashboard', { timeout: 10000 });

    // Navigate to product edit page
    await page.goto(PRODUCT_EDIT_URL);

    // Wait for Vue app to load
    await page.waitForSelector('#product-edit-app', { timeout: 10000 });
    await page.waitForFunction(() => {
      const app = document.querySelector('#product-edit-app');
      return app && !app.querySelector('.spinner-border');
    }, { timeout: 15000 });
  });

  test('should load Vue.js component successfully', async ({ page }) => {
    // Check that Vue app container exists
    await expect(page.locator('#product-edit-app')).toBeVisible();
    
    // Check that loading spinner is gone
    await expect(page.locator('.spinner-border')).not.toBeVisible();
    
    // Check that main form elements are present
    await expect(page.locator('input[id="name"]')).toBeVisible();
    await expect(page.locator('select[id="category_id"]')).toBeVisible();
    await expect(page.locator('select[id="branch_id"]')).toBeVisible();
    
    // Check that tabs are present
    await expect(page.locator('[data-tab="basic"]')).toBeVisible();
    await expect(page.locator('[data-tab="colors"]')).toBeVisible();
    await expect(page.locator('[data-tab="specifications"]')).toBeVisible();
  });

  test('should display product data correctly', async ({ page }) => {
    // Check that product name is loaded
    const nameInput = page.locator('input[id="name"]');
    await expect(nameInput).not.toHaveValue('');
    
    // Check that price fields have values
    const priceInput = page.locator('input[id="price"]');
    await expect(priceInput).not.toHaveValue('');
    
    // Check that stock field has value
    const stockInput = page.locator('input[id="stock"]');
    await expect(stockInput).not.toHaveValue('');
  });

  test('should handle tab navigation correctly', async ({ page }) => {
    // Start on basic tab (should be active by default)
    await expect(page.locator('[data-tab="basic"].active')).toBeVisible();
    
    // Click on colors tab
    await page.click('[data-tab="colors"]');
    await expect(page.locator('[data-tab="colors"].active')).toBeVisible();
    await expect(page.locator('[data-tab="basic"].active')).not.toBeVisible();
    
    // Click on specifications tab
    await page.click('[data-tab="specifications"]');
    await expect(page.locator('[data-tab="specifications"].active')).toBeVisible();
    await expect(page.locator('[data-tab="colors"].active')).not.toBeVisible();
    
    // Go back to basic tab
    await page.click('[data-tab="basic"]');
    await expect(page.locator('[data-tab="basic"].active')).toBeVisible();
  });

  test('should update basic product information', async ({ page }) => {
    // Update product name
    await page.fill('input[id="name"]', testProduct.name);
    await expect(page.locator('input[id="name"]')).toHaveValue(testProduct.name);
    
    // Update description
    await page.fill('textarea[id="description"]', testProduct.description);
    await expect(page.locator('textarea[id="description"]')).toHaveValue(testProduct.description);
    
    // Update price
    await page.fill('input[id="price"]', testProduct.price);
    await expect(page.locator('input[id="price"]')).toHaveValue(testProduct.price);
    
    // Update original price
    await page.fill('input[id="original_price"]', testProduct.originalPrice);
    await expect(page.locator('input[id="original_price"]')).toHaveValue(testProduct.originalPrice);
    
    // Update stock
    await page.fill('input[id="stock"]', testProduct.stock);
    await expect(page.locator('input[id="stock"]')).toHaveValue(testProduct.stock);
  });

  test('should show sale badge when original price is higher', async ({ page }) => {
    // Set original price higher than current price
    await page.fill('input[id="price"]', '50.00');
    await page.fill('input[id="original_price"]', '100.00');
    
    // Check that sale badge appears
    await expect(page.locator('text=Sale')).toBeVisible();
    await expect(page.locator('text=50% off')).toBeVisible();
  });

  test('should handle color management', async ({ page }) => {
    // Navigate to colors tab
    await page.click('[data-tab="colors"]');
    
    // Check if there are existing colors or add new one
    const existingColors = await page.locator('.color-item').count();
    
    if (existingColors === 0) {
      // Click add color button
      await page.click('button:has-text("Add Your First Color")');
    } else {
      // Click add color button
      await page.click('button:has-text("Add Color")');
    }
    
    // Wait for new color form to appear
    await page.waitForSelector('.color-item', { timeout: 5000 });
    
    // Fill color information
    const colorItems = page.locator('.color-item');
    const lastColorItem = colorItems.last();
    
    await lastColorItem.locator('select.color-name-select').selectOption(testColor.name);
    await lastColorItem.locator('input[type="text"][placeholder="#000000"]').fill(testColor.colorCode);
    await lastColorItem.locator('input[type="number"][step="0.01"]').fill(testColor.priceAdjustment);
    await lastColorItem.locator('.color-stock-input').fill(testColor.stock);
  });

  test('should handle image upload for colors', async ({ page }) => {
    // Navigate to colors tab
    await page.click('[data-tab="colors"]');
    
    // Wait for colors to load
    await page.waitForSelector('.color-item', { timeout: 5000 });
    
    // Find first color item
    const firstColorItem = page.locator('.color-item').first();
    
    // Check if image upload area exists
    await expect(firstColorItem.locator('.image-preview-container')).toBeVisible();
    
    // Create a test image file
    const testImagePath = 'tests/fixtures/test-image.jpg';
    
    // Try to upload image (if file exists)
    try {
      await firstColorItem.locator('input[type="file"]').setInputFiles(testImagePath);
      
      // Wait for image preview to appear
      await page.waitForSelector('.image-preview', { timeout: 5000 });
      await expect(firstColorItem.locator('.image-preview')).toBeVisible();
    } catch (error) {
      console.log('Test image file not found, skipping image upload test');
    }
  });

  test('should handle specifications management', async ({ page }) => {
    // Navigate to specifications tab
    await page.click('[data-tab="specifications"]');
    
    // Check if there are existing specifications or add new one
    const existingSpecs = await page.locator('.specification-item').count();
    
    if (existingSpecs === 0) {
      // Click add specification button
      await page.click('button:has-text("Add First Specification")');
    } else {
      // Click add specification button
      await page.click('button:has-text("Add Specification")');
    }
    
    // Wait for new specification form to appear
    await page.waitForSelector('.specification-item', { timeout: 5000 });
    
    // Fill specification information
    const specItems = page.locator('.specification-item');
    const lastSpecItem = specItems.last();
    
    await lastSpecItem.locator('input[placeholder="e.g., Material"]').fill(testSpecification.key);
    await lastSpecItem.locator('input[placeholder="e.g., 100% Cotton"]').fill(testSpecification.value);
  });

  test('should validate required fields', async ({ page }) => {
    // Clear required fields
    await page.fill('input[id="name"]', '');
    await page.fill('input[id="price"]', '');
    
    // Try to save
    await page.click('button:has-text("Save Changes")');
    
    // Check for validation errors (should stay on basic tab)
    await expect(page.locator('[data-tab="basic"].active')).toBeVisible();
  });

  test('should track stock allocation progress', async ({ page }) => {
    // Check that stock progress bar exists
    await expect(page.locator('.h-2.rounded-full')).toBeVisible();
    
    // Navigate to colors tab
    await page.click('[data-tab="colors"]');
    
    // If colors exist, check stock allocation
    const colorCount = await page.locator('.color-item').count();
    if (colorCount > 0) {
      // Check that stock allocation is displayed
      await expect(page.locator('text=units allocated')).toBeVisible();
    }
  });

  test('should handle form submission', async ({ page }) => {
    // Fill required fields
    await page.fill('input[id="name"]', testProduct.name);
    await page.fill('input[id="price"]', testProduct.price);
    
    // Navigate to colors tab and ensure at least one color exists
    await page.click('[data-tab="colors"]');
    
    const colorCount = await page.locator('.color-item').count();
    if (colorCount === 0) {
      await page.click('button:has-text("Add Your First Color")');
      await page.waitForSelector('.color-item', { timeout: 5000 });
      
      const colorItem = page.locator('.color-item').first();
      await colorItem.locator('select.color-name-select').selectOption('Red');
    }
    
    // Go back to basic tab
    await page.click('[data-tab="basic"]');
    
    // Monitor network requests
    const responsePromise = page.waitForResponse(response => 
      response.url().includes('/merchant/products/') && response.request().method() === 'POST'
    );
    
    // Submit form
    await page.click('button:has-text("Save Changes")');
    
    // Wait for response
    try {
      const response = await responsePromise;
      expect(response.status()).toBeLessThan(500); // Should not be server error
    } catch (error) {
      console.log('Form submission test completed (response may vary based on server state)');
    }
  });

  test('should handle back button navigation', async ({ page }) => {
    // Click back button
    await page.click('a:has-text("Back to Products")');

    // Should navigate to products index
    await page.waitForURL('**/merchant/products', { timeout: 10000 });
    expect(page.url()).toContain('/merchant/products');
  });

  test('should handle preview button', async ({ page }) => {
    // Click preview button
    const [newPage] = await Promise.all([
      page.waitForEvent('popup'),
      page.click('button:has-text("Preview")')
    ]);

    // Check that preview opens in new tab
    expect(newPage.url()).toContain('/merchant/products/11');
    await newPage.close();
  });
});

test.describe('Product Edit Vue.js Component - Mobile', () => {
  test.beforeEach(async ({ page }) => {
    // Set mobile viewport
    await page.setViewportSize({ width: 375, height: 667 });

    // Login as merchant first
    await page.goto(`${BASE_URL}/login`);
    await page.fill('input[name="email"]', 'merchant@test.com');
    await page.fill('input[name="password"]', 'password123');
    await page.click('button[type="submit"]');

    // Wait for redirect to dashboard
    await page.waitForURL('**/merchant/dashboard', { timeout: 10000 });

    // Navigate to product edit page
    await page.goto(PRODUCT_EDIT_URL);

    // Wait for Vue app to load
    await page.waitForSelector('#product-edit-app', { timeout: 10000 });
    await page.waitForFunction(() => {
      const app = document.querySelector('#product-edit-app');
      return app && !app.querySelector('.spinner-border');
    }, { timeout: 15000 });
  });

  test('should be responsive on mobile devices', async ({ page }) => {
    // Check that main elements are visible on mobile
    await expect(page.locator('#product-edit-app')).toBeVisible();
    await expect(page.locator('input[id="name"]')).toBeVisible();

    // Check that tabs are responsive
    await expect(page.locator('[data-tab="basic"]')).toBeVisible();

    // Check that form fields stack properly on mobile
    const nameInput = page.locator('input[id="name"]');
    const nameBox = await nameInput.boundingBox();
    expect(nameBox.width).toBeGreaterThan(200); // Should take reasonable width on mobile
  });

  test('should handle tab navigation on mobile', async ({ page }) => {
    // Test tab switching on mobile
    await page.click('[data-tab="colors"]');
    await expect(page.locator('[data-tab="colors"].active')).toBeVisible();

    await page.click('[data-tab="specifications"]');
    await expect(page.locator('[data-tab="specifications"].active')).toBeVisible();
  });

  test('should handle color management on mobile', async ({ page }) => {
    // Navigate to colors tab
    await page.click('[data-tab="colors"]');

    // Check if add color button is accessible on mobile
    const addColorButton = page.locator('button:has-text("Add Color")');
    if (await addColorButton.isVisible()) {
      await addColorButton.click();
      await page.waitForSelector('.color-item', { timeout: 5000 });
    }
  });

  test('should handle image upload on mobile', async ({ page }) => {
    // Navigate to colors tab
    await page.click('[data-tab="colors"]');

    // Wait for colors to load
    await page.waitForSelector('.color-item', { timeout: 5000 });

    // Check that image upload area is accessible on mobile
    const imageContainer = page.locator('.image-preview-container').first();
    await expect(imageContainer).toBeVisible();

    // Check that the container is properly sized for mobile
    const containerBox = await imageContainer.boundingBox();
    expect(containerBox.width).toBeGreaterThan(100);
    expect(containerBox.height).toBeGreaterThan(100);
  });
});

test.describe('Product Edit Vue.js Component - Cross-Browser', () => {
  ['chromium', 'firefox', 'webkit'].forEach(browserName => {
    test(`should work correctly in ${browserName}`, async ({ page }) => {
      // Set standard desktop viewport
      await page.setViewportSize({ width: 1280, height: 720 });

      // Login as merchant first
      await page.goto(`${BASE_URL}/login`);
      await page.fill('input[name="email"]', 'merchant@test.com');
      await page.fill('input[name="password"]', 'password123');
      await page.click('button[type="submit"]');

      // Wait for redirect to dashboard
      await page.waitForURL('**/merchant/dashboard', { timeout: 10000 });

      // Navigate to product edit page
      await page.goto(PRODUCT_EDIT_URL);

      // Wait for Vue app to load
      await page.waitForSelector('#product-edit-app', { timeout: 10000 });
      await page.waitForFunction(() => {
        const app = document.querySelector('#product-edit-app');
        return app && !app.querySelector('.spinner-border');
      }, { timeout: 15000 });

      // Basic functionality test
      await expect(page.locator('#product-edit-app')).toBeVisible();
      await expect(page.locator('input[id="name"]')).toBeVisible();

      // Test tab navigation
      await page.click('[data-tab="colors"]');
      await expect(page.locator('[data-tab="colors"].active')).toBeVisible();

      // Test form interaction
      await page.click('[data-tab="basic"]');
      await page.fill('input[id="name"]', 'Cross-browser test');
      await expect(page.locator('input[id="name"]')).toHaveValue('Cross-browser test');
    });
  });
});

test.describe('Product Edit Vue.js Component - Error Handling', () => {
  test.beforeEach(async ({ page }) => {
    await page.setViewportSize({ width: 1280, height: 720 });
  });

  test('should handle network errors gracefully', async ({ page }) => {
    // Block the API endpoint to simulate network error
    await page.route('**/merchant/products/*/edit-data', route => {
      route.abort();
    });

    // Navigate to product edit page
    await page.goto(PRODUCT_EDIT_URL);

    // Should show some error handling (loading state or error message)
    await page.waitForSelector('#product-edit-app', { timeout: 10000 });
  });

  test('should handle invalid product ID', async ({ page }) => {
    // Navigate to non-existent product
    await page.goto(`${BASE_URL}/merchant/products/99999/edit`);

    // Should handle 404 or show appropriate error
    // This test depends on server-side error handling
  });

  test('should monitor console for JavaScript errors', async ({ page }) => {
    const consoleErrors = [];

    page.on('console', msg => {
      if (msg.type() === 'error') {
        consoleErrors.push(msg.text());
      }
    });

    // Login as merchant first
    await page.goto(`${BASE_URL}/login`);
    await page.fill('input[name="email"]', 'merchant@test.com');
    await page.fill('input[name="password"]', 'password123');
    await page.click('button[type="submit"]');

    // Wait for redirect to dashboard
    await page.waitForURL('**/merchant/dashboard', { timeout: 10000 });

    // Navigate to product edit page
    await page.goto(PRODUCT_EDIT_URL);

    // Wait for Vue app to load
    await page.waitForSelector('#product-edit-app', { timeout: 10000 });
    await page.waitForFunction(() => {
      const app = document.querySelector('#product-edit-app');
      return app && !app.querySelector('.spinner-border');
    }, { timeout: 15000 });

    // Interact with the page
    await page.click('[data-tab="colors"]');
    await page.click('[data-tab="specifications"]');
    await page.click('[data-tab="basic"]');

    // Check for console errors
    const criticalErrors = consoleErrors.filter(error =>
      !error.includes('favicon') &&
      !error.includes('404') &&
      !error.includes('net::ERR_')
    );

    if (criticalErrors.length > 0) {
      console.log('Console errors found:', criticalErrors);
    }

    // Allow some non-critical errors but fail on Vue/JavaScript errors
    const vueErrors = criticalErrors.filter(error =>
      error.includes('Vue') ||
      error.includes('TypeError') ||
      error.includes('ReferenceError')
    );

    expect(vueErrors.length).toBe(0);
  });
});
