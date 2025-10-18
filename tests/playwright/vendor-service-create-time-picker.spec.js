const path = require('path');
const { test, expect } = require('@playwright/test');

test.describe('Vendor Service Creation - Time Picker', () => {
  test('creates a new service using time picker interactions', async ({ page }) => {
    const timestamp = Date.now();
    const serviceNameEn = `Automation Service ${timestamp}`;
    const serviceNameAr = `خدمة آلية ${timestamp}`;
    const descriptionEn = `Automated service description ${timestamp}`;
    const descriptionAr = `وصف خدمة آلي ${timestamp}`;
    const priceValue = '150.75';
    const durationValue = '45';
    const imagePath = path.resolve('test_image.png');

    // Login
    await page.goto('/vendor/login');
    await page.fill('input[name="email"]', 'gogoh3296@gmail.com');
    await page.fill('input[name="password"]', 'Fifa2021');
    await page.click('button[type="submit"]');
    await page.waitForURL('**/vendor/dashboard', { timeout: 60000 });

    // Navigate to create service page
    await page.goto('/vendor/services/create');
    await page.waitForSelector('form', { timeout: 30000 });

    // Fill English service name
    await page.fill('#name', serviceNameEn);

    // Fill Arabic service name via switcher
    await page.click('[data-field="service_name"] .language-tab[data-language="ar"]');
    await page.fill('#service_name_arabic', serviceNameAr);
    await page.click('[data-field="service_name"] .language-tab[data-language="en"]');

    // Select category (first available child)
    const categoryOptions = page.locator('#category_id option:not([disabled])').filter({ hasNotText: 'Select' });
    const categoryCount = await categoryOptions.count();
    if (categoryCount === 0) {
      throw new Error('No categories available to select.');
    }
    const categoryValue = await categoryOptions.first().getAttribute('value');
    await page.selectOption('#category_id', categoryValue);

    // Select branch
    const branchOptions = page.locator('#branch_id option').filter({ hasNotText: 'Select' });
    const branchCount = await branchOptions.count();
    if (branchCount === 0) {
      throw new Error('No branches available to select.');
    }
    const branchValue = await branchOptions.first().getAttribute('value');
    await page.selectOption('#branch_id', branchValue);

    // Fill descriptions in both languages
    await page.fill('#description', descriptionEn);
    await page.click('[data-field="service_description"] .language-tab[data-language="ar"]');
    await page.fill('#service_description_arabic', descriptionAr);
    await page.click('[data-field="service_description"] .language-tab[data-language="en"]');

    // Pricing & duration
    await page.fill('#price', priceValue);
    await page.fill('#duration', durationValue);

    // Availability days - Monday and Thursday
    await page.click('label[for="day_1"]');
    await page.click('label[for="day_4"]');

    // Time picker interactions
    const startTimeInput = page.locator('#start_time');
    await startTimeInput.click();
    const startOverlay = page.locator('#start_time-time-picker-overlay');
    await startOverlay.waitFor({ state: 'visible' });
    await startOverlay.locator('.time-picker-hour').selectOption('09');
    await startOverlay.locator('.time-picker-minute').selectOption('30');
    await startOverlay.locator('.time-picker-apply').click();
    await startOverlay.waitFor({ state: 'hidden' });
    const startTimeValue = await startTimeInput.inputValue();
    expect(startTimeValue).toBe('09:30');

    const endTimeInput = page.locator('#end_time');
    await endTimeInput.click();
    const endOverlay = page.locator('#end_time-time-picker-overlay');
    await endOverlay.waitFor({ state: 'visible' });
    await endOverlay.locator('.time-picker-hour').selectOption('11');
    await endOverlay.locator('.time-picker-minute').selectOption('00');
    await endOverlay.locator('.time-picker-apply').click();
    await endOverlay.waitFor({ state: 'hidden' });
    const endTimeValue = await endTimeInput.inputValue();
    expect(endTimeValue).toBe('11:00');

    // Upload image (required)
    await page.setInputFiles('#image', imagePath);

    // Submit
    await page.click('button[type="submit"]');
    await page.waitForURL('**/vendor/services', { timeout: 60000 });

    // Verify success
    await expect(page.locator('text=Service created successfully.')).toBeVisible({ timeout: 15000 });

    const createdRow = page.locator('table tbody tr', { hasText: serviceNameEn });
    await expect(createdRow).toBeVisible({ timeout: 15000 });
    await expect(createdRow.locator('td').nth(4)).toContainText(durationValue);
  });
});
