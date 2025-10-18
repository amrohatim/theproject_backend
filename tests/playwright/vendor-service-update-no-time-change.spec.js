const { test, expect } = require('@playwright/test');

test.describe('Vendor Service Update Without Time Change', () => {
  test('updates the most recent service while keeping existing hours', async ({ page }) => {
    const timestamp = Date.now();
    const newServiceName = `Automation Service Update ${timestamp}`;
    const newDescription = `Automated description update ${timestamp}`;
    const newPrice = '123.45';
    const newDuration = '55';

    // Login as vendor using provided credentials
    await page.goto('/vendor/login');
    await page.fill('input[name="email"]', 'gogoh3296@gmail.com');
    await page.fill('input[name="password"]', 'Fifa2021');
    await page.click('button[type="submit"]');
    await page.waitForURL('**/vendor/dashboard', { timeout: 60000 });

    // Navigate to services list and open the last service for editing
    await page.goto('/vendor/services');
    const serviceRows = page.locator('table tbody tr');
    await expect(serviceRows.first()).toBeVisible({ timeout: 20000 });

    const lastServiceRow = serviceRows.last();
    await lastServiceRow.scrollIntoViewIfNeeded();
    await lastServiceRow.locator('a:has(i.fa-edit)').click();
    await page.waitForURL(/\/vendor\/services\/\d+\/edit/, { timeout: 30000 });

    const serviceUrlMatch = page.url().match(/services\/(\d+)\/edit/);
    expect(serviceUrlMatch).not.toBeNull();
    const serviceId = serviceUrlMatch[1];

    // Capture existing times to verify they remain unchanged later
    const initialStartTime = await page.inputValue('#start_time');
    const initialEndTime = await page.inputValue('#end_time');

    // Helper to explicitly control available day selections
    const setDaySelection = async (dayIndex, shouldBeChecked) => {
      const input = page.locator(`#day_${dayIndex}`);
      const label = page.locator(`label[for="day_${dayIndex}"]`);
      const currentlyChecked = await input.isChecked();

      if (shouldBeChecked && !currentlyChecked) {
        await label.click();
      } else if (!shouldBeChecked && currentlyChecked) {
        await label.click();
      }
    };

    // Update required fields without touching the time inputs
    await page.fill('#name', newServiceName);
    await page.fill('#description', newDescription);
    await page.fill('#price', newPrice);
    await page.fill('#duration', newDuration);

    // Set available days to Monday and Thursday only (indices 1 and 4)
    for (const dayIndex of [0, 1, 2, 3, 4, 5, 6]) {
      await setDaySelection(dayIndex, [1, 4].includes(dayIndex));
    }

    // Submit the form
    await page.click('button[type="submit"]');
    await page.waitForURL('**/vendor/services', { timeout: 60000 });

    // Confirm success message and updated values in the listing
    await expect(page.locator('text=Service updated successfully.')).toBeVisible({ timeout: 15000 });

    const updatedRow = page.locator('table tbody tr', { hasText: newServiceName });
    await expect(updatedRow).toBeVisible({ timeout: 15000 });
    await expect(updatedRow.locator('td').nth(3)).toContainText('$123.45');
    await expect(updatedRow.locator('td').nth(4)).toContainText('55');

    // Reopen the same service edit page to verify times (and day selections) remain intact
    await page.goto(`/vendor/services/${serviceId}/edit`);
    await page.waitForSelector('#start_time', { timeout: 30000 });

    await expect(page.locator('#start_time')).toHaveValue(initialStartTime);
    await expect(page.locator('#end_time')).toHaveValue(initialEndTime);

    // Ensure available days reflect the new selections
    for (const dayIndex of [1, 4]) {
      await expect(page.locator(`#day_${dayIndex}`)).toBeChecked();
    }
    for (const dayIndex of [0, 2, 3, 5, 6]) {
      await expect(page.locator(`#day_${dayIndex}`)).not.toBeChecked();
    }
  });
});
