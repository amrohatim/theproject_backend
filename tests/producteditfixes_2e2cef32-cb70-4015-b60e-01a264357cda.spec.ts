
import { test } from '@playwright/test';
import { expect } from '@playwright/test';

test('ProductEditFixes_2025-07-05', async ({ page, context }) => {
  
    // Navigate to URL
    await page.goto('https://dala3chic.com:443');

    // Navigate to URL
    await page.goto('https://dala3chic.com:443');

    // Take screenshot
    await page.screenshot({ path: 'homepage.png' });

    // Click element
    await page.click('a[href*="login"]');

    // Navigate to URL
    await page.goto('https://dala3chic.com:443/merchant/login');

    // Navigate to URL
    await page.goto('https://dala3chic.com:443/login');
});