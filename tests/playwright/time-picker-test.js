// @ts-check
const { test, expect } = require('@playwright/test');

test('Time picker should appear when clicking time inputs', async ({ page }) => {
  // Navigate to login page
  await page.goto('http://127.0.0.1:8000/login');
  
  // Login with provided credentials
  await page.fill('input[name="email"]', 'amroqr69@gmail.com');
  await page.fill('input[name="password"]', 'Fifa2021');
  await page.click('button[type="submit"]');
  
  // Wait for navigation after login
  await page.waitForURL('**/dashboard');
  
  // Navigate to the merchant services create page
  await page.goto('http://127.0.0.1:8000/merchant/services/create');
  
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
  const startTimeOverlay = await page.$('#start_time-time-picker-overlay:not(.hidden)');
  
  // Log the result
  if (startTimeOverlay) {
    console.log('Time picker overlay is visible for start_time');
  } else {
    console.log('Time picker overlay is NOT visible for start_time');
    
    // Check if the overlay exists but is hidden
    const hiddenOverlay = await page.$('#start_time-time-picker-overlay.hidden');
    if (hiddenOverlay) {
      console.log('Overlay exists but is hidden');
    } else {
      console.log('Overlay does not exist at all');
    }
  }
  
  // Check for any console errors
  console.log(`Found ${consoleErrors.length} console errors`);
  
  // Take a screenshot for debugging
  await page.screenshot({ path: 'time-picker-test.png' });
});