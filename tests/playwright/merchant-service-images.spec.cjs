const { test, expect } = require('@playwright/test');

test.describe('Merchant Service Images', () => {
  let page;

  test.beforeEach(async ({ browser }) => {
    page = await browser.newPage();
    
    // Login as test merchant
    await page.goto('https://dala3chic.com/login');
    await page.fill('input[name="email"]', 'merchant@test.com');
    await page.fill('input[name="password"]', 'password123');
    await page.click('button[type="submit"]');
    
    // Wait for redirect after login
    await page.waitForURL(/dashboard|services/);
  });

  test.afterEach(async () => {
    await page.close();
  });

  test('should display service images correctly on merchant services page - desktop', async () => {
    // Set desktop viewport
    await page.setViewportSize({ width: 1280, height: 720 });
    
    // Navigate to merchant services page
    await page.goto('https://dala3chic.com/merchant/services');
    
    // Wait for the page to load
    await page.waitForSelector('table', { timeout: 10000 });
    
    // Check if there are any services with images
    const serviceImages = await page.locator('table img').count();
    
    if (serviceImages > 0) {
      // Get the first service image
      const firstImage = page.locator('table img').first();
      
      // Verify image has proper src attribute
      const imageSrc = await firstImage.getAttribute('src');
      expect(imageSrc).toBeTruthy();
      expect(imageSrc).toMatch(/^https?:\/\//); // Should be a valid URL
      expect(imageSrc).not.toContain('storage/storage/'); // Should not have duplicate paths
      expect(imageSrc).not.toContain('https://https://'); // Should not have duplicate protocols
      
      // Verify image is visible
      await expect(firstImage).toBeVisible();
      
      // Verify image loads successfully (not broken)
      const imageLoadStatus = await page.evaluate((imgSrc) => {
        return new Promise((resolve) => {
          const img = new Image();
          img.onload = () => resolve('loaded');
          img.onerror = () => resolve('error');
          img.src = imgSrc;
        });
      }, imageSrc);
      
      expect(imageLoadStatus).toBe('loaded');
      
      // Verify image has proper styling
      const imageStyle = await firstImage.getAttribute('style');
      expect(imageStyle).toContain('width: 60px');
      expect(imageStyle).toContain('height: 60px');
      expect(imageStyle).toContain('object-fit: cover');
    }
  });

  test('should display service images correctly on merchant services page - mobile', async () => {
    // Set mobile viewport (iPhone SE)
    await page.setViewportSize({ width: 375, height: 667 });
    
    // Navigate to merchant services page
    await page.goto('https://dala3chic.com/merchant/services');
    
    // Wait for the page to load
    await page.waitForSelector('table', { timeout: 10000 });
    
    // Check if there are any services with images
    const serviceImages = await page.locator('table img').count();
    
    if (serviceImages > 0) {
      // Get the first service image
      const firstImage = page.locator('table img').first();
      
      // Verify image has proper src attribute
      const imageSrc = await firstImage.getAttribute('src');
      expect(imageSrc).toBeTruthy();
      expect(imageSrc).toMatch(/^https?:\/\//); // Should be a valid URL
      
      // Verify image is visible on mobile
      await expect(firstImage).toBeVisible();
      
      // Verify image loads successfully
      const imageLoadStatus = await page.evaluate((imgSrc) => {
        return new Promise((resolve) => {
          const img = new Image();
          img.onload = () => resolve('loaded');
          img.onerror = () => resolve('error');
          img.src = imgSrc;
        });
      }, imageSrc);
      
      expect(imageLoadStatus).toBe('loaded');
    }
  });

  test('should display service image correctly on service detail page', async () => {
    // Navigate to merchant services page first
    await page.goto('https://dala3chic.com/merchant/services');
    await page.waitForSelector('table', { timeout: 10000 });
    
    // Check if there are any services
    const serviceRows = await page.locator('table tbody tr').count();
    
    if (serviceRows > 0) {
      // Click on the first service to view details
      const firstServiceLink = page.locator('table tbody tr').first().locator('a').first();
      if (await firstServiceLink.count() > 0) {
        await firstServiceLink.click();
        
        // Wait for service detail page to load
        await page.waitForSelector('.discord-card', { timeout: 10000 });
        
        // Check if there's a service image on the detail page
        const detailImage = page.locator('.discord-card img').first();
        
        if (await detailImage.count() > 0) {
          // Verify image has proper src attribute
          const imageSrc = await detailImage.getAttribute('src');
          expect(imageSrc).toBeTruthy();
          expect(imageSrc).toMatch(/^https?:\/\//);
          expect(imageSrc).not.toContain('storage/storage/');
          
          // Verify image is visible
          await expect(detailImage).toBeVisible();
          
          // Verify image loads successfully
          const imageLoadStatus = await page.evaluate((imgSrc) => {
            return new Promise((resolve) => {
              const img = new Image();
              img.onload = () => resolve('loaded');
              img.onerror = () => resolve('error');
              img.src = imgSrc;
            });
          }, imageSrc);
          
          expect(imageLoadStatus).toBe('loaded');
        }
      }
    }
  });

  test('should show placeholder for services without images', async () => {
    // Navigate to merchant services page
    await page.goto('https://dala3chic.com/merchant/services');
    await page.waitForSelector('table', { timeout: 10000 });
    
    // Look for placeholder divs (services without images)
    const placeholders = page.locator('div[style*="background-color: var(--discord-darkest)"]');
    const placeholderCount = await placeholders.count();
    
    if (placeholderCount > 0) {
      // Verify placeholder is visible
      await expect(placeholders.first()).toBeVisible();
      
      // Verify placeholder contains the expected icon
      const icon = placeholders.first().locator('i.fas.fa-concierge-bell');
      await expect(icon).toBeVisible();
    }
  });

  test('should not have broken image placeholders', async () => {
    // Navigate to merchant services page
    await page.goto('https://dala3chic.com/merchant/services');
    await page.waitForSelector('table', { timeout: 10000 });
    
    // Get all images on the page
    const images = page.locator('table img');
    const imageCount = await images.count();
    
    // Check each image to ensure none are broken
    for (let i = 0; i < imageCount; i++) {
      const image = images.nth(i);
      const imageSrc = await image.getAttribute('src');
      
      if (imageSrc) {
        // Check if image loads successfully
        const imageLoadStatus = await page.evaluate((imgSrc) => {
          return new Promise((resolve) => {
            const img = new Image();
            img.onload = () => resolve('loaded');
            img.onerror = () => resolve('error');
            img.src = imgSrc;
          });
        }, imageSrc);
        
        expect(imageLoadStatus).toBe('loaded');
      }
    }
  });

  test('should have properly formatted image URLs', async () => {
    // Navigate to merchant services page
    await page.goto('https://dala3chic.com/merchant/services');
    await page.waitForSelector('table', { timeout: 10000 });
    
    // Get all images on the page
    const images = page.locator('table img');
    const imageCount = await images.count();
    
    // Check each image URL format
    for (let i = 0; i < imageCount; i++) {
      const image = images.nth(i);
      const imageSrc = await image.getAttribute('src');
      
      if (imageSrc) {
        // Verify URL format
        expect(imageSrc).toMatch(/^https?:\/\//); // Should start with http or https
        expect(imageSrc).not.toContain('storage/storage/'); // No duplicate storage paths
        expect(imageSrc).not.toContain('https://https://'); // No duplicate protocols
        expect(imageSrc).not.toContain('storage/https://'); // No mixed paths
        
        // Verify it's a valid URL
        expect(() => new URL(imageSrc)).not.toThrow();
      }
    }
  });
});
