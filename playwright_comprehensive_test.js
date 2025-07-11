// Comprehensive Playwright test for product color image fix
const { test, expect } = require('@playwright/test');

// Test data
const testData = {
  merchantEmail: 'merchant@test.com',
  merchantPassword: 'password',
  productIds: [7, 9],
  problematicImages: [
    '1751640943_uBysKFaIUZ.png',
    '1751640333_gR36eTVlBM.png',
    '1751674751_KM0JQfIugP.png',
    '1751674928_dVL8IcWFx3.png'
  ]
};

// Helper function to login as merchant
async function loginAsMerchant(page) {
  await page.goto('https://dala3chic.com/login');
  await page.fill('input[name="email"]', testData.merchantEmail);
  await page.fill('input[name="password"]', testData.merchantPassword);
  await page.click('button[type="submit"]');
  await page.waitForURL('https://dala3chic.com/');
}

// Helper function to check image loading
async function checkImageLoading(page, imageSelector) {
  const images = await page.locator(imageSelector).all();
  const results = [];
  
  for (const img of images) {
    const src = await img.getAttribute('src');
    const complete = await img.evaluate(el => el.complete);
    const naturalWidth = await img.evaluate(el => el.naturalWidth);
    const naturalHeight = await img.evaluate(el => el.naturalHeight);
    
    results.push({
      src,
      complete,
      naturalWidth,
      naturalHeight,
      isLoaded: complete && naturalWidth > 0 && naturalHeight > 0
    });
  }
  
  return results;
}

// Test 1: Desktop viewport - Product edit pages
test('Desktop: Product color images load without 403 errors', async ({ page }) => {
  // Set desktop viewport
  await page.setViewportSize({ width: 1920, height: 1080 });
  
  await loginAsMerchant(page);
  
  for (const productId of testData.productIds) {
    await page.goto(`https://dala3chic.com/merchant/products/${productId}/edit`);
    
    // Wait for page to load
    await page.waitForSelector('.color-item', { timeout: 10000 });
    
    // Check for 403 errors in console
    const logs = [];
    page.on('console', msg => {
      if (msg.type() === 'error' && msg.text().includes('403')) {
        logs.push(msg.text());
      }
    });
    
    // Check image loading
    const imageResults = await checkImageLoading(page, 'img.image-preview');
    
    // Assertions
    expect(logs.length).toBe(0); // No 403 errors
    
    for (const result of imageResults) {
      expect(result.isLoaded).toBe(true);
      expect(result.src).toContain('https://dala3chic.com/images/products/colors/');
    }
    
    console.log(`✅ Product ${productId} - Desktop: ${imageResults.length} images loaded successfully`);
  }
});

// Test 2: Mobile viewport - Product edit pages
test('Mobile: Product color images load without 403 errors', async ({ page }) => {
  // Set mobile viewport
  await page.setViewportSize({ width: 375, height: 667 });
  
  await loginAsMerchant(page);
  
  for (const productId of testData.productIds) {
    await page.goto(`https://dala3chic.com/merchant/products/${productId}/edit`);
    
    // Wait for page to load
    await page.waitForSelector('.color-item', { timeout: 10000 });
    
    // Check for 403 errors in console
    const logs = [];
    page.on('console', msg => {
      if (msg.type() === 'error' && msg.text().includes('403')) {
        logs.push(msg.text());
      }
    });
    
    // Check image loading
    const imageResults = await checkImageLoading(page, 'img.image-preview');
    
    // Assertions
    expect(logs.length).toBe(0); // No 403 errors
    
    for (const result of imageResults) {
      expect(result.isLoaded).toBe(true);
      expect(result.src).toContain('https://dala3chic.com/images/products/colors/');
    }
    
    console.log(`✅ Product ${productId} - Mobile: ${imageResults.length} images loaded successfully`);
  }
});

// Test 3: Direct image URL access
test('Direct image URLs are accessible', async ({ page }) => {
  for (const filename of testData.problematicImages) {
    const url = `https://dala3chic.com/images/products/colors/${filename}`;
    
    const response = await page.goto(url);
    
    // Check response status
    expect(response.status()).toBe(200);
    
    // Check content type
    const contentType = response.headers()['content-type'];
    expect(contentType).toContain('image');
    
    console.log(`✅ Direct access: ${filename} - Status: ${response.status()}, Type: ${contentType}`);
  }
});

// Test 4: Network monitoring for 403 errors
test('Network monitoring: No 403 errors during page load', async ({ page }) => {
  const failedRequests = [];
  
  // Monitor network requests
  page.on('response', response => {
    if (response.status() === 403) {
      failedRequests.push({
        url: response.url(),
        status: response.status()
      });
    }
  });
  
  await loginAsMerchant(page);
  
  for (const productId of testData.productIds) {
    await page.goto(`https://dala3chic.com/merchant/products/${productId}/edit`);
    await page.waitForSelector('.color-item', { timeout: 10000 });
    
    // Wait a bit for all resources to load
    await page.waitForTimeout(2000);
  }
  
  // Check for any 403 errors
  const imageRelated403s = failedRequests.filter(req => 
    req.url.includes('image') || 
    req.url.includes('.png') || 
    req.url.includes('.jpg') || 
    req.url.includes('products')
  );
  
  expect(imageRelated403s.length).toBe(0);
  
  if (failedRequests.length > 0) {
    console.log('Non-image 403 errors found:', failedRequests);
  }
  
  console.log(`✅ Network monitoring: No image-related 403 errors found`);
});

// Test 5: Image loading performance
test('Image loading performance test', async ({ page }) => {
  await loginAsMerchant(page);
  
  for (const productId of testData.productIds) {
    const startTime = Date.now();
    
    await page.goto(`https://dala3chic.com/merchant/products/${productId}/edit`);
    await page.waitForSelector('.color-item', { timeout: 10000 });
    
    // Wait for all images to load
    await page.waitForFunction(() => {
      const images = document.querySelectorAll('img.image-preview');
      return Array.from(images).every(img => img.complete && img.naturalWidth > 0);
    }, { timeout: 15000 });
    
    const loadTime = Date.now() - startTime;
    
    // Performance assertion (should load within 15 seconds)
    expect(loadTime).toBeLessThan(15000);
    
    console.log(`✅ Product ${productId} - Load time: ${loadTime}ms`);
  }
});
