const { test, expect } = require('@playwright/test');

test.describe('Merchant Product Images', () => {
  let page;

  test.beforeEach(async ({ browser }) => {
    page = await browser.newPage();
    
    // Login as test merchant
    await page.goto('https://dala3chic.com/login');
    await page.fill('input[name="email"]', 'merchant@test.com');
    await page.fill('input[name="password"]', 'password123');
    await page.click('button[type="submit"]');
    
    // Wait for redirect after login
    await page.waitForURL(/dashboard|products/);
  });

  test.afterEach(async () => {
    await page.close();
  });

  test.describe('Desktop Viewport', () => {
    test.beforeEach(async () => {
      await page.setViewportSize({ width: 1280, height: 720 });
    });

    test('should display product images correctly on listing page', async () => {
      await page.goto('https://dala3chic.com/merchant/products');
      
      // Wait for page to load
      await page.waitForSelector('table', { timeout: 10000 });
      
      // Get all product images
      const images = await page.locator('img[alt*="Handmade Silver Earrings"], img[alt*="Ceramic Vase Set"], img[alt*="Beaded Bracelet Set"]').all();
      
      // Verify at least some images are present
      expect(images.length).toBeGreaterThan(0);
      
      // Check each image
      for (const img of images) {
        const src = await img.getAttribute('src');
        const naturalWidth = await img.evaluate(el => el.naturalWidth);
        const naturalHeight = await img.evaluate(el => el.naturalHeight);
        
        // Verify image has valid src
        expect(src).toBeTruthy();
        expect(src).not.toContain('undefined');
        expect(src).not.toContain('null');
        
        // Verify image loads (has dimensions)
        expect(naturalWidth).toBeGreaterThan(0);
        expect(naturalHeight).toBeGreaterThan(0);
        
        // Verify URL format is correct (no double prefixes)
        expect(src).not.toMatch(/storage.*storage/);
        expect(src).not.toMatch(/https.*https/);
      }
    });

    test('should display product image correctly on detail page', async () => {
      await page.goto('https://dala3chic.com/merchant/products/16');
      
      // Wait for page to load
      await page.waitForSelector('.discord-card', { timeout: 10000 });
      
      // Find the product image
      const productImage = page.locator('img[alt="Handmade Silver Earrings"]').first();
      
      // Verify image exists
      await expect(productImage).toBeVisible();
      
      // Check image properties
      const src = await productImage.getAttribute('src');
      const naturalWidth = await productImage.evaluate(el => el.naturalWidth);
      const naturalHeight = await productImage.evaluate(el => el.naturalHeight);
      
      // Verify image has valid src and loads
      expect(src).toBeTruthy();
      expect(src).toMatch(/storage\/products\/.*\.png/);
      expect(naturalWidth).toBeGreaterThan(0);
      expect(naturalHeight).toBeGreaterThan(0);
    });

    test('should display current image in edit form', async () => {
      await page.goto('https://dala3chic.com/merchant/products/16/edit');
      
      // Wait for page to load
      await page.waitForSelector('.image-upload-container', { timeout: 10000 });
      
      // Find the preview image
      const previewImage = page.locator('.preview-img');
      
      // Verify preview image exists and is visible
      await expect(previewImage).toBeVisible();
      
      // Check image properties
      const src = await previewImage.getAttribute('src');
      const naturalWidth = await previewImage.evaluate(el => el.naturalWidth);
      const naturalHeight = await previewImage.evaluate(el => el.naturalHeight);
      
      // Verify image has valid src and loads
      expect(src).toBeTruthy();
      expect(src).toMatch(/storage\/products\/.*\.png/);
      expect(naturalWidth).toBeGreaterThan(0);
      expect(naturalHeight).toBeGreaterThan(0);
    });

    test('should show placeholder for products without images', async () => {
      await page.goto('https://dala3chic.com/merchant/products');
      
      // Wait for page to load
      await page.waitForSelector('table', { timeout: 10000 });
      
      // Find placeholder images
      const placeholderImages = await page.locator('img[src*="placeholder.png"]').all();
      
      // Verify placeholders exist
      expect(placeholderImages.length).toBeGreaterThan(0);
      
      // Check placeholder properties
      for (const img of placeholderImages) {
        const src = await img.getAttribute('src');
        const naturalWidth = await img.evaluate(el => el.naturalWidth);
        const naturalHeight = await img.evaluate(el => el.naturalHeight);
        
        // Verify placeholder loads correctly
        expect(src).toContain('placeholder.png');
        expect(naturalWidth).toBeGreaterThan(0);
        expect(naturalHeight).toBeGreaterThan(0);
      }
    });

    test('should handle image upload form on create page', async () => {
      await page.goto('https://dala3chic.com/merchant/products/create');
      
      // Wait for page to load
      await page.waitForSelector('.image-upload-container', { timeout: 10000 });
      
      // Verify upload container exists
      const uploadContainer = page.locator('.image-upload-container');
      await expect(uploadContainer).toBeVisible();
      
      // Verify upload placeholder is shown
      const uploadPlaceholder = page.locator('.upload-placeholder');
      await expect(uploadPlaceholder).toBeVisible();
      
      // Verify file input exists
      const fileInput = page.locator('input[type="file"][name="image"]');
      await expect(fileInput).toBeAttached();
    });
  });

  test.describe('Mobile Viewport', () => {
    test.beforeEach(async () => {
      await page.setViewportSize({ width: 375, height: 667 });
    });

    test('should display product images correctly on mobile listing page', async () => {
      await page.goto('https://dala3chic.com/merchant/products');
      
      // Wait for page to load
      await page.waitForSelector('table', { timeout: 10000 });
      
      // Get all product images
      const images = await page.locator('img[style*="width: 60px"]').all();
      
      // Verify images are present
      expect(images.length).toBeGreaterThan(0);
      
      // Check that images maintain their aspect ratio on mobile
      for (const img of images) {
        const src = await img.getAttribute('src');
        const naturalWidth = await img.evaluate(el => el.naturalWidth);
        const naturalHeight = await img.evaluate(el => el.naturalHeight);
        
        // Verify image loads
        expect(src).toBeTruthy();
        expect(naturalWidth).toBeGreaterThan(0);
        expect(naturalHeight).toBeGreaterThan(0);
      }
    });

    test('should display product image correctly on mobile detail page', async () => {
      await page.goto('https://dala3chic.com/merchant/products/16');
      
      // Wait for page to load
      await page.waitForSelector('.discord-card', { timeout: 10000 });
      
      // Find the product image
      const productImage = page.locator('img[alt="Handmade Silver Earrings"]').first();
      
      // Verify image exists and is responsive
      await expect(productImage).toBeVisible();
      
      const src = await productImage.getAttribute('src');
      const naturalWidth = await productImage.evaluate(el => el.naturalWidth);
      
      expect(src).toBeTruthy();
      expect(naturalWidth).toBeGreaterThan(0);
    });

    test('should handle image upload form on mobile create page', async () => {
      await page.goto('https://dala3chic.com/merchant/products/create');
      
      // Wait for page to load
      await page.waitForSelector('.image-upload-container', { timeout: 10000 });
      
      // Verify upload container is responsive
      const uploadContainer = page.locator('.image-upload-container');
      await expect(uploadContainer).toBeVisible();
      
      // Check that upload area is accessible on mobile
      const uploadPlaceholder = page.locator('.upload-placeholder');
      await expect(uploadPlaceholder).toBeVisible();
    });
  });

  test.describe('Image URL Validation', () => {
    test('should not have malformed URLs with double prefixes', async () => {
      await page.goto('https://dala3chic.com/merchant/products');
      
      // Wait for page to load
      await page.waitForSelector('table', { timeout: 10000 });
      
      // Get all images on the page
      const images = await page.locator('img').all();
      
      for (const img of images) {
        const src = await img.getAttribute('src');
        
        if (src) {
          // Check for double storage prefixes
          expect(src).not.toMatch(/storage.*storage/);
          
          // Check for double protocol prefixes
          expect(src).not.toMatch(/https.*https/);
          
          // Check for malformed URLs
          expect(src).not.toContain('undefined');
          expect(src).not.toContain('null');
          
          // Verify URL format
          expect(src).toMatch(/^https:\/\/[^\/]+\/(storage\/|images\/)/);
        }
      }
    });
  });
});
