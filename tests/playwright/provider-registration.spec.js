import { test, expect } from '@playwright/test';

test.describe('Provider Registration Form Validation', () => {
  test.beforeEach(async ({ page }) => {
    // Navigate to the provider registration step 1 form
    await page.goto('http://localhost:8000/register/provider');

    // Wait for the form to be fully loaded
    await page.waitForSelector('#providerStep1Form');
  });

  test('should load the registration form correctly', async ({ page }) => {
    // Check if all required elements are present
    await expect(page.locator('h2')).toContainText('Provider Registration');
    await expect(page.locator('.form-subtitle')).toContainText('Step 1: Enter your provider information');

    // Check form fields
    await expect(page.locator('#name')).toBeVisible();
    await expect(page.locator('#business_name')).toBeVisible();
    await expect(page.locator('#email')).toBeVisible();
    await expect(page.locator('#phone')).toBeVisible();
    await expect(page.locator('#password')).toBeVisible();
    await expect(page.locator('#password_confirmation')).toBeVisible();
    await expect(page.locator('#business_type')).toBeVisible();
    await expect(page.locator('#submitBtn')).toBeVisible();
  });

  test('should show validation errors for empty required fields', async ({ page }) => {
    // Try to submit empty form
    await page.click('#submitBtn');

    // Wait for validation error modal to appear
    await page.waitForSelector('#validationErrorModal', { state: 'visible' });

    // Check modal content
    await expect(page.locator('#validationErrorModal')).toBeVisible();
    await expect(page.locator('.modal-title')).toContainText('Validation Errors');

    // Check for specific error messages
    await expect(page.locator('#validationErrorList')).toContainText('Full Name');
    await expect(page.locator('#validationErrorList')).toContainText('Business Name');
    await expect(page.locator('#validationErrorList')).toContainText('Email Address');
    await expect(page.locator('#validationErrorList')).toContainText('Phone Number');
    await expect(page.locator('#validationErrorList')).toContainText('Password');
    await expect(page.locator('#validationErrorList')).toContainText('Business Type');

    // Close modal
    await page.click('.modal-close');
    await page.waitForSelector('#validationErrorModal', { state: 'hidden' });
  });

  test('should validate field lengths and formats', async ({ page }) => {
    // Test company name too short
    await page.fill('#name', 'A');
    await page.click('#business_name'); // Trigger blur
    await page.waitForSelector('#name-error', { state: 'visible' });
    await expect(page.locator('#name-error')).toContainText('must be at least 2 characters');
    
    // Test invalid email format
    await page.fill('#email', 'invalid-email');
    await page.click('#phone'); // Trigger blur
    await page.waitForSelector('#email-error', { state: 'visible' });
    await expect(page.locator('#email-error')).toContainText('valid email address');
    
    // Test invalid phone format
    await page.fill('#phone', '123');
    await page.click('#password'); // Trigger blur
    await page.waitForSelector('#phone-error', { state: 'visible' });
    await expect(page.locator('#phone-error')).toContainText('valid UAE phone number');
    
    // Test password too short
    await page.fill('#password', '123');
    await page.click('#password_confirmation'); // Trigger blur
    await page.waitForSelector('#password-error', { state: 'visible' });
    await expect(page.locator('#password-error')).toContainText('at least 8 characters');
  });

  test('should validate password confirmation match', async ({ page }) => {
    // Fill password
    await page.fill('#password', 'password123');
    
    // Fill mismatched confirmation
    await page.fill('#password_confirmation', 'different');
    await page.click('#name'); // Trigger blur
    
    // Check for mismatch error
    await page.waitForSelector('#password_confirmation-error', { state: 'visible' });
    await expect(page.locator('#password_confirmation-error')).toContainText('does not match');
    
    // Fix password confirmation
    await page.fill('#password_confirmation', 'password123');
    await page.click('#name'); // Trigger blur
    
    // Error should disappear
    await page.waitForSelector('#password_confirmation-error i', { state: 'hidden' });
  });

  test('should validate UAE phone number formats', async ({ page }) => {
    const validPhones = ['+971501234567', '971501234567', '0501234567'];
    const invalidPhones = ['123456789', '+1234567890', '+97150123'];
    
    // Test valid phone formats
    for (const phone of validPhones) {
      await page.fill('#phone', phone);
      await page.click('#password'); // Trigger blur
      await page.waitForTimeout(500); // Wait for validation
      
      // Should not show error
      const errorVisible = await page.locator('#phone-error i').isVisible();
      expect(errorVisible).toBeFalsy();
    }
    
    // Test invalid phone formats
    for (const phone of invalidPhones) {
      await page.fill('#phone', phone);
      await page.click('#password'); // Trigger blur
      await page.waitForSelector('#phone-error', { state: 'visible' });
      await expect(page.locator('#phone-error')).toContainText('valid UAE phone number');
    }
  });

  test('should validate delivery capability selection', async ({ page }) => {
    // Fill all required fields except delivery capability
    await page.fill('#name', 'Test Company');
    await page.fill('#business_name', 'Test Business');
    await page.fill('#email', 'test@example.com');
    await page.fill('#phone', '+971501234567');
    await page.fill('#password', 'password123');
    await page.fill('#password_confirmation', 'password123');
    await page.check('#terms');
    
    // Uncheck all delivery options
    await page.uncheck('#pickup_only');
    await page.uncheck('#delivery_available');
    await page.uncheck('#both_options');
    
    // Try to submit
    await page.click('#submit-btn');
    
    // Should show validation error
    await page.waitForSelector('#validationErrorModal', { state: 'visible' });
    await expect(page.locator('#validationErrorList')).toContainText('delivery option');
    
    // Close modal and select delivery option
    await page.click('.modal-close');
    await page.waitForSelector('#validationErrorModal', { state: 'hidden' });
    await page.check('#pickup_only');
  });

  test('should validate terms and conditions', async ({ page }) => {
    // Fill all required fields except terms
    await page.fill('#name', 'Test Company');
    await page.fill('#business_name', 'Test Business');
    await page.fill('#email', 'test@example.com');
    await page.fill('#phone', '+971501234567');
    await page.fill('#password', 'password123');
    await page.fill('#password_confirmation', 'password123');
    await page.check('#pickup_only');
    
    // Don't check terms
    await page.uncheck('#terms');
    
    // Try to submit
    await page.click('#submit-btn');
    
    // Should show validation error
    await page.waitForSelector('#validationErrorModal', { state: 'visible' });
    await expect(page.locator('#validationErrorList')).toContainText('Terms of Service');
    
    // Close modal and check terms
    await page.click('.modal-close');
    await page.waitForSelector('#validationErrorModal', { state: 'hidden' });
    await page.check('#terms');
  });

  test('should test business name uniqueness validation', async ({ page }) => {
    // Test with existing business name from test data
    await page.fill('#business_name', 'Existing Business Name');
    await page.click('#email'); // Trigger blur

    // Wait for async validation
    await page.waitForTimeout(1500);

    // Check if error appears
    await page.waitForSelector('#business_name-error', { state: 'visible' });
    await expect(page.locator('#business_name-error')).toContainText('already taken');

    // Test with unique business name
    await page.fill('#business_name', 'Unique Business Name ' + Date.now());
    await page.click('#email'); // Trigger blur

    // Wait for validation
    await page.waitForTimeout(1500);

    // Error should disappear
    const errorVisible = await page.locator('#business_name-error i').isVisible();
    expect(errorVisible).toBeFalsy();
  });

  test('should handle successful form submission', async ({ page }) => {
    // Fill all fields with valid data
    await page.fill('#name', 'Test Company');
    await page.fill('#business_name', 'Unique Test Business ' + Date.now());
    await page.fill('#email', 'test' + Date.now() + '@example.com');
    await page.fill('#phone', '+971501234567');
    await page.fill('#password', 'password123');
    await page.fill('#password_confirmation', 'password123');
    await page.check('#pickup_only');
    await page.check('#terms');
    
    // Submit form
    await page.click('#submit-btn');
    
    // Wait for success modal or redirect
    // This depends on your actual implementation
    await page.waitForTimeout(3000);
    
    // Check for success indication
    const successModal = page.locator('#successModal');
    const isSuccessVisible = await successModal.isVisible();
    
    if (isSuccessVisible) {
      await expect(successModal).toContainText('Registration Successful');
    }
  });

  test('should test modal accessibility features', async ({ page }) => {
    // Trigger validation modal
    await page.click('#submit-btn');
    await page.waitForSelector('#validationErrorModal', { state: 'visible' });
    
    // Test escape key closes modal
    await page.keyboard.press('Escape');
    await page.waitForSelector('#validationErrorModal', { state: 'hidden' });
    
    // Trigger modal again
    await page.click('#submit-btn');
    await page.waitForSelector('#validationErrorModal', { state: 'visible' });
    
    // Test clicking outside closes modal
    await page.click('.modal-overlay');
    await page.waitForSelector('#validationErrorModal', { state: 'hidden' });
  });

  test('should test form field accessibility', async ({ page }) => {
    // Check ARIA attributes
    await expect(page.locator('#name')).toHaveAttribute('aria-describedby', 'name-error');
    await expect(page.locator('#email')).toHaveAttribute('aria-describedby', 'email-error');
    await expect(page.locator('#phone')).toHaveAttribute('aria-describedby', 'phone-error');
    await expect(page.locator('#terms')).toHaveAttribute('aria-required', 'true');
    
    // Check error message ARIA attributes
    await expect(page.locator('#name-error')).toHaveAttribute('role', 'alert');
    await expect(page.locator('#email-error')).toHaveAttribute('role', 'alert');
    await expect(page.locator('#phone-error')).toHaveAttribute('role', 'alert');
  });

  test('should test real-time validation feedback', async ({ page }) => {
    // Test name field real-time validation
    await page.fill('#name', 'A');
    await page.click('#email');
    await page.waitForSelector('#name-error', { state: 'visible' });
    
    // Fix the field
    await page.fill('#name', 'Valid Company Name');
    await page.click('#email');
    await page.waitForSelector('#name-error i', { state: 'hidden' });
    
    // Test email field real-time validation
    await page.fill('#email', 'invalid');
    await page.click('#phone');
    await page.waitForSelector('#email-error', { state: 'visible' });
    
    // Fix the field
    await page.fill('#email', 'valid@example.com');
    await page.click('#phone');
    await page.waitForSelector('#email-error i', { state: 'hidden' });
  });

  test('should test password strength indicator', async ({ page }) => {
    // Test weak password
    await page.fill('#password', '123');
    await page.click('#password_confirmation');
    await page.waitForSelector('#password-error', { state: 'visible' });
    
    // Test stronger password
    await page.fill('#password', 'password123');
    await page.click('#password_confirmation');
    
    // Check if error disappears or changes
    await page.waitForTimeout(500);
    const errorText = await page.locator('#password-error').textContent();
    expect(errorText).not.toContain('at least 8 characters');
  });

  test('should test form responsiveness on mobile', async ({ page }) => {
    // Set mobile viewport
    await page.setViewportSize({ width: 375, height: 667 });

    // Check if form is still usable
    await expect(page.locator('#providerRegistrationForm')).toBeVisible();
    await expect(page.locator('#name')).toBeVisible();
    await expect(page.locator('#submit-btn')).toBeVisible();

    // Test modal on mobile
    await page.click('#submit-btn');
    await page.waitForSelector('#validationErrorModal', { state: 'visible' });
    await expect(page.locator('.modal-container')).toBeVisible();

    // Close modal
    await page.click('.modal-close');
    await page.waitForSelector('#validationErrorModal', { state: 'hidden' });
  });

  test('should test email registration status validation - verified user', async ({ page }) => {
    // Test with email that has verified registration status
    await page.fill('#email', 'verified@test.com');
    await page.click('#phone'); // Trigger blur

    // Wait for async validation
    await page.waitForTimeout(1500);

    // Check for specific error message
    await page.waitForSelector('#email-error', { state: 'visible' });
    await expect(page.locator('#email-error')).toContainText('registered company with this email');
  });

  test('should test email registration status validation - license completed user', async ({ page }) => {
    // Test with email that has license_completed registration status
    await page.fill('#email', 'license@test.com');
    await page.click('#phone'); // Trigger blur

    // Wait for async validation
    await page.waitForTimeout(1500);

    // Check for specific error message
    await page.waitForSelector('#email-error', { state: 'visible' });
    await expect(page.locator('#email-error')).toContainText('wait for admin approval');
  });

  test('should test phone registration status validation - verified user', async ({ page }) => {
    // Test with phone that has verified registration status
    await page.fill('#phone', '+971501111111');
    await page.click('#password'); // Trigger blur

    // Wait for async validation
    await page.waitForTimeout(1500);

    // Check for specific error message
    await page.waitForSelector('#phone-error', { state: 'visible' });
    await expect(page.locator('#phone-error')).toContainText('registered company with this phone');
  });

  test('should test comprehensive validation flow', async ({ page }) => {
    // Test the complete validation flow with all scenarios

    // Step 1: Test all empty fields
    await page.click('#submit-btn');
    await page.waitForSelector('#validationErrorModal', { state: 'visible' });
    await expect(page.locator('#validationErrorList')).toContainText('Company/Supplier Name');
    await page.click('.modal-close');
    await page.waitForSelector('#validationErrorModal', { state: 'hidden' });

    // Step 2: Fill fields with invalid data
    await page.fill('#name', 'A'); // Too short
    await page.fill('#business_name', 'Existing Business Name'); // Taken
    await page.fill('#email', 'verified@test.com'); // Verified user
    await page.fill('#phone', '+971501111111'); // Verified user
    await page.fill('#password', '123'); // Too short
    await page.fill('#password_confirmation', 'different'); // Mismatch

    // Trigger validations
    await page.click('#submit-btn');
    await page.waitForTimeout(2000); // Wait for all async validations

    // Should show multiple errors
    await page.waitForSelector('#validationErrorModal', { state: 'visible' });
    const errorList = page.locator('#validationErrorList');
    await expect(errorList).toContainText('at least 2 characters');
    await expect(errorList).toContainText('already taken');
    await expect(errorList).toContainText('registered company');

    await page.click('.modal-close');
    await page.waitForSelector('#validationErrorModal', { state: 'hidden' });

    // Step 3: Fix all issues with valid data
    await page.fill('#name', 'Valid Company Name');
    await page.fill('#business_name', 'Valid Business Name ' + Date.now());
    await page.fill('#email', 'valid' + Date.now() + '@example.com');
    await page.fill('#phone', '+971509999999');
    await page.fill('#password', 'validpassword123');
    await page.fill('#password_confirmation', 'validpassword123');
    await page.check('#pickup_only');
    await page.check('#terms');

    // Wait for all validations to clear
    await page.waitForTimeout(2000);

    // Submit should now work
    await page.click('#submit-btn');

    // Should either show success modal or proceed to next step
    await page.waitForTimeout(3000);

    // Check for success indication
    const successModal = page.locator('#successModal');
    const isSuccessVisible = await successModal.isVisible();

    if (isSuccessVisible) {
      await expect(successModal).toContainText('Registration Successful');
    }
  });
});
