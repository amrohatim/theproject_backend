const { test, expect } = require('@playwright/test');

test.describe('Merchant Service Time Picker', () => {
  test('time picker should appear when clicking time inputs', async ({ page }) => {
    // Navigate to login page
    await page.goto('/login');
    
    // Login with provided credentials
    await page.fill('input[name="email"]', 'amroqr69@gmail.com');
    await page.fill('input[name="password"]', 'Fifa2021');
    await page.click('button[type="submit"]');
    
    // Wait for navigation after login
    await page.waitForURL('**/dashboard');
    
    // Navigate to the merchant services create page
    await page.goto('/merchant/services/create');
    
    // Wait for the page to load
    await page.waitForSelector('#start_time');
    
    // Set up console error listener
    const consoleErrors = [];
    page.on('console', msg => {
      if (msg.type() === 'error') {
        consoleErrors.push(msg.text());
        console.log(`Console error: ${msg.text()}`);
      }
    });
    
    // Click on start_time input
    await page.click('#start_time');
    
    // Wait a moment to see if time picker appears
    await page.waitForTimeout(1000);
    
    // Check if time picker overlay is visible
    const startTimeOverlay = page.locator('#start_time-time-picker-overlay:not(.hidden)');
    const isVisible = await startTimeOverlay.isVisible();
    
    // Take a screenshot for debugging
    await page.screenshot({ path: 'test-results/time-picker-test.png' });
    
    // Log the result
    if (isVisible) {
      console.log('Time picker overlay is visible for start_time');
    } else {
      console.log('Time picker overlay is NOT visible for start_time');
      
      // Check if the overlay exists but is hidden
      const hiddenOverlay = page.locator('#start_time-time-picker-overlay.hidden');
      const exists = await hiddenOverlay.count() > 0;
      if (exists) {
        console.log('Overlay exists but is hidden');
      } else {
        console.log('Overlay does not exist at all');
      }
    }
    
    // Check for any console errors
    console.log(`Found ${consoleErrors.length} console errors`);
    
    // Assert that the time picker should be visible
    await expect(startTimeOverlay).toBeVisible({ timeout: 2000 });
  });
});