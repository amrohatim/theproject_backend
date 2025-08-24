const { chromium } = require('playwright');

async function testEmailVerificationFlow() {
  console.log('🚀 Starting Email Verification Flow Test...');
  
  const browser = await chromium.launch({
    headless: true,
    slowMo: 500 // Slow down for better stability
  });
  
  const context = await browser.newContext({
    viewport: { width: 1280, height: 720 }
  });
  
  const page = await context.newPage();
  
  // Monitor console logs for errors
  page.on('console', msg => {
    if (msg.type() === 'error') {
      console.error('❌ Browser Console Error:', msg.text());
    } else if (msg.type() === 'log') {
      console.log('📝 Browser Console Log:', msg.text());
    }
  });
  
  // Monitor network requests
  page.on('response', response => {
    if (response.url().includes('/api/merchant-registration/')) {
      console.log(`🌐 API Response: ${response.status()} ${response.url()}`);
    }
  });
  
  try {
    // Step 1: Navigate to merchant registration
    console.log('📍 Step 1: Navigating to merchant registration page...');
    await page.goto('https://dala3chic.com/register/merchant');
    await page.waitForLoadState('networkidle');
    
    // Take screenshot of initial page
    await page.screenshot({ path: 'test-screenshots/01-initial-page.png' });
    
    // Step 2: Fill out merchant information form
    console.log('📍 Step 2: Filling out merchant information...');
    
    // Generate unique test data
    const timestamp = Date.now();
    const testEmail = `test.merchant.${timestamp}@example.com`;
    const testPhone = `+971501234${timestamp.toString().slice(-3)}`;
    
    console.log(`📧 Using test email: ${testEmail}`);
    console.log(`📱 Using test phone: ${testPhone}`);
    
    // Fill form fields
    await page.fill('input[name="name"]', `Test Merchant ${timestamp}`);
    await page.fill('input[name="email"]', testEmail);
    await page.fill('input[name="phone"]', testPhone);
    await page.fill('input[name="password"]', 'TestPassword123!');
    await page.fill('input[name="password_confirmation"]', 'TestPassword123!');
    
    // Fill business information
    await page.fill('input[name="business_name"]', `Test Business ${timestamp}`);
    await page.fill('input[name="business_license"]', `BL${timestamp}`);
    await page.fill('textarea[name="business_description"]', 'Test business description for automated testing');
    
    // Fill address information
    await page.fill('input[name="address"]', 'Test Address 123');
    await page.fill('input[name="city"]', 'Dubai');
    await page.fill('input[name="state"]', 'Dubai');
    await page.fill('input[name="postal_code"]', '12345');
    await page.selectOption('select[name="country"]', 'AE');
    
    // Take screenshot after filling form
    await page.screenshot({ path: 'test-screenshots/02-form-filled.png' });
    
    // Submit the form
    console.log('📍 Step 3: Submitting merchant information...');
    await page.click('button[type="submit"]');
    
    // Wait for email verification step
    await page.waitForSelector('h2:has-text("Email Verification")', { timeout: 10000 });
    console.log('✅ Successfully reached email verification step');
    
    // Take screenshot of email verification page
    await page.screenshot({ path: 'test-screenshots/03-email-verification-page.png' });
    
    // Step 4: Check for verification code in logs
    console.log('📍 Step 4: Looking for verification code in application logs...');
    
    // Wait a moment for the email to be sent
    await page.waitForTimeout(3000);
    
    // Step 5: Simulate entering verification code
    console.log('📍 Step 5: Testing verification code input...');
    
    // Test with invalid code first
    console.log('🧪 Testing with invalid verification code...');
    const invalidCode = '123456';
    
    // Fill verification code inputs
    for (let i = 0; i < 6; i++) {
      await page.fill(`input[data-index="${i}"]`, invalidCode[i]);
    }
    
    // Take screenshot with invalid code
    await page.screenshot({ path: 'test-screenshots/04-invalid-code-entered.png' });
    
    // Wait for error response
    await page.waitForTimeout(2000);
    
    // Check for error message
    const errorMessage = await page.locator('.text-red-600, .error-message, [class*="error"]').first();
    if (await errorMessage.isVisible()) {
      console.log('✅ Error handling working - invalid code rejected');
      await page.screenshot({ path: 'test-screenshots/05-error-message-shown.png' });
    }
    
    // Step 6: Test with a valid-looking code (we'll need to get this from logs)
    console.log('📍 Step 6: Testing API endpoint directly...');
    
    // Get registration token from the page
    const registrationToken = await page.evaluate(() => {
      // Try to find the registration token in the Vue app data
      const app = document.querySelector('[data-registration-token]');
      if (app) {
        return app.getAttribute('data-registration-token');
      }
      
      // Alternative: check if it's in window object
      if (window.registrationToken) {
        return window.registrationToken;
      }
      
      return null;
    });
    
    console.log('🔑 Registration token:', registrationToken);
    
    // Test API endpoint directly
    const apiResponse = await page.evaluate(async (token) => {
      try {
        const response = await fetch('/api/merchant-registration/verify-email', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
          },
          body: JSON.stringify({
            registration_token: token,
            verification_code: '999999' // Test code
          })
        });
        
        const data = await response.json();
        return {
          status: response.status,
          data: data
        };
      } catch (error) {
        return {
          error: error.message
        };
      }
    }, registrationToken);
    
    console.log('🔍 API Response:', JSON.stringify(apiResponse, null, 2));
    
    // Step 7: Check application logs for verification codes
    console.log('📍 Step 7: Test completed. Check Laravel logs for verification codes.');
    console.log('💡 To find the verification code, run: tail -f storage/logs/laravel.log | grep "verification code"');
    
    // Final screenshot
    await page.screenshot({ path: 'test-screenshots/06-test-completed.png' });
    
    console.log('✅ Email verification flow test completed successfully!');
    console.log('📸 Screenshots saved in test-screenshots/ directory');
    
  } catch (error) {
    console.error('❌ Test failed:', error);
    await page.screenshot({ path: 'test-screenshots/error-screenshot.png' });
    throw error;
  } finally {
    await browser.close();
  }
}

// Run the test
if (require.main === module) {
  testEmailVerificationFlow()
    .then(() => {
      console.log('🎉 Test execution completed');
      process.exit(0);
    })
    .catch((error) => {
      console.error('💥 Test execution failed:', error);
      process.exit(1);
    });
}

module.exports = { testEmailVerificationFlow };
