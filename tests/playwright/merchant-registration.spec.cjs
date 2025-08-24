const { test, expect } = require('@playwright/test');

test.describe('Merchant Registration with Google Maps', () => {
  test.beforeEach(async ({ page }) => {
    // Navigate to the registration page
    await page.goto('/register');
  });

  test('Complete merchant registration flow with Google Maps integration', async ({ page }) => {
    // Step 1: Choose merchant registration
    await page.click('[data-testid="merchant-registration-link"]');
    await expect(page).toHaveURL(/.*\/register\/merchant/);
    await expect(page.locator('h1')).toContainText('Join as Merchant');

    // Step 2: Fill basic information
    await page.fill('#name', 'Test Merchant Store');
    await page.fill('#email', 'testmerchant@example.com');
    await page.fill('#phone', '+971501234567');
    await page.fill('#password', 'TestPassword123');
    await page.fill('#password_confirmation', 'TestPassword123');

    // Step 3: Test Google Maps integration
    await page.click('#location-search');
    
    // Wait for map container to appear
    await page.waitForSelector('#map-container', { state: 'visible' });
    await expect(page.locator('#google-map')).toBeVisible();

    // Test location search
    await page.fill('#location-search', 'Dubai Mall, Dubai');
    await page.waitForTimeout(2000); // Wait for autocomplete

    // Simulate location selection by directly setting values
    await page.evaluate(() => {
      document.getElementById('store_location_lat').value = '25.1972';
      document.getElementById('store_location_lng').value = '55.2796';
      document.getElementById('store_location_address').value = 'Dubai Mall, Financial Centre Road, Dubai';
      document.getElementById('selected-location-container').style.display = 'block';
    });

    // Verify location is selected
    await expect(page.locator('#selected-location-container')).toBeVisible();
    await expect(page.locator('#store_location_address')).toHaveValue('Dubai Mall, Financial Centre Road, Dubai');

    // Step 4: Upload required files
    const testImagePath = './tests/fixtures/test-image.jpg';
    await page.setInputFiles('#uae_id_front', testImagePath);
    await page.setInputFiles('#uae_id_back', testImagePath);

    // Step 5: Configure delivery options
    await page.check('#delivery_capability');
    await page.waitForSelector('#delivery-fees.show', { state: 'visible' });
    await page.fill('input[name="delivery_fees[within_city]"]', '15');
    await page.fill('input[name="delivery_fees[outside_city]"]', '25');

    // Step 6: Submit form
    await page.click('#submitBtn');
    
    // Wait for redirect to email verification
    await page.waitForURL(/.*\/merchant\/email\/verify\/temp\/.*/);
    await expect(page.locator('body')).toContainText('Email Verification Required');
  });

  test('Google Maps location selection and clearing functionality', async ({ page }) => {
    await page.click('[data-testid="merchant-registration-link"]');
    
    // Test location search activation
    await page.click('#location-search');
    await page.waitForSelector('#map-container', { state: 'visible' });
    await expect(page.locator('#google-map')).toBeVisible();

    // Simulate location selection
    await page.evaluate(() => {
      document.getElementById('store_location_lat').value = '25.1972';
      document.getElementById('store_location_lng').value = '55.2796';
      document.getElementById('store_location_address').value = 'Burj Khalifa, Dubai';
      document.getElementById('selected-location-container').style.display = 'block';
    });

    // Verify location data
    await expect(page.locator('#store_location_lat')).toHaveValue('25.1972');
    await expect(page.locator('#store_location_lng')).toHaveValue('55.2796');
    await expect(page.locator('#store_location_address')).toHaveValue('Burj Khalifa, Dubai');

    // Test clearing location
    await page.click('.clear-location-btn');
    
    // Verify location is cleared
    await expect(page.locator('#store_location_lat')).toHaveValue('');
    await expect(page.locator('#store_location_lng')).toHaveValue('');
    await expect(page.locator('#store_location_address')).toHaveValue('');
    await expect(page.locator('#location-search')).toHaveValue('');
    await expect(page.locator('#selected-location-container')).not.toBeVisible();
  });

  test('Form validation with missing required fields', async ({ page }) => {
    await page.click('[data-testid="merchant-registration-link"]');
    
    // Try to submit empty form
    await page.click('#submitBtn');
    
    // Check for validation errors
    await page.waitForSelector('.error-message', { state: 'visible' });
    await expect(page.locator('.error-message')).toContainText('This field is required');

    // Test email validation
    await page.fill('#email', 'invalid-email');
    await page.click('#submitBtn');
    await expect(page.locator('.error-message')).toContainText('Please enter a valid email address');

    // Test password confirmation
    await page.fill('#email', 'test@example.com');
    await page.fill('#password', 'password123');
    await page.fill('#password_confirmation', 'different');
    await page.click('#submitBtn');
    await expect(page.locator('.error-message')).toContainText('Password confirmation does not match');
  });

  test('Google Maps fallback when API fails', async ({ page }) => {
    await page.click('[data-testid="merchant-registration-link"]');
    
    // Simulate Google Maps API failure
    await page.evaluate(() => {
      window.gm_authFailure();
    });

    await page.waitForSelector('#map-container', { state: 'visible' });
    await expect(page.locator('#map-container')).toContainText('Google Maps is currently unavailable');
    await expect(page.locator('#map-container')).toContainText('You can still enter your address manually');

    // Test manual address entry
    await page.fill('#location-search', 'Manual Address Entry Test');
    await expect(page.locator('#store_location_address')).toHaveValue('Manual Address Entry Test');
  });

  test('Responsive design on mobile viewport', async ({ page }) => {
    // Set mobile viewport
    await page.setViewportSize({ width: 375, height: 667 });
    
    await page.click('[data-testid="merchant-registration-link"]');
    
    // Test that form is responsive
    await expect(page.locator('.registration-container')).toBeVisible();
    
    // Test Google Maps on mobile
    await page.click('#location-search');
    await page.waitForSelector('#map-container', { state: 'visible' });
    
    // Check that map height is adjusted for mobile
    const mapHeight = await page.locator('#google-map').evaluate(el => 
      window.getComputedStyle(el).height
    );
    expect(mapHeight).toBe('250px');
  });

  test('Verify other registration flows remain unaffected', async ({ page }) => {
    // Test vendor registration
    await page.click('[data-testid="vendor-registration-link"]');
    await expect(page).toHaveURL(/.*\/register\/vendor/);
    await expect(page.locator('body')).toContainText('Vendor Registration');

    // Go back and test provider registration
    await page.goto('/register');
    await page.click('[data-testid="provider-registration-link"]');
    await expect(page).toHaveURL(/.*\/register\/provider/);
    await expect(page.locator('body')).toContainText('Provider Registration');

    // Verify registration choice page
    await page.goto('/register');
    await expect(page.locator('body')).toContainText('Choose Your Registration Type');
    await expect(page.locator('[data-testid="vendor-registration-link"]')).toBeVisible();
    await expect(page.locator('[data-testid="provider-registration-link"]')).toBeVisible();
    await expect(page.locator('[data-testid="merchant-registration-link"]')).toBeVisible();
  });

  test('Location data persistence on form errors', async ({ page }) => {
    await page.click('[data-testid="merchant-registration-link"]');
    
    // Set location data
    await page.click('#location-search');
    await page.evaluate(() => {
      document.getElementById('store_location_lat').value = '25.1972';
      document.getElementById('store_location_lng').value = '55.2796';
      document.getElementById('store_location_address').value = 'Test Location';
      document.getElementById('selected-location-container').style.display = 'block';
    });

    // Submit form with missing required fields to trigger validation error
    await page.click('#submitBtn');
    
    // Verify location data is preserved
    await expect(page.locator('#store_location_lat')).toHaveValue('25.1972');
    await expect(page.locator('#store_location_lng')).toHaveValue('55.2796');
    await expect(page.locator('#store_location_address')).toHaveValue('Test Location');
  });
});
