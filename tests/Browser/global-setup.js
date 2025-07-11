// Global setup for Playwright tests
const { chromium } = require('@playwright/test');

async function globalSetup(config) {
  console.log('🚀 Starting global setup for License Management tests...');
  
  const browser = await chromium.launch();
  const page = await browser.newPage();
  
  try {
    // Verify the application is accessible
    await page.goto('https://dala3chic.com');
    await page.waitForLoadState('networkidle');
    
    console.log('✅ Application is accessible');
    
    // Setup test data if needed
    await setupTestData(page);
    
    console.log('✅ Global setup completed successfully');
  } catch (error) {
    console.error('❌ Global setup failed:', error);
    throw error;
  } finally {
    await browser.close();
  }
}

async function setupTestData(page) {
  console.log('📝 Setting up test data...');
  
  // Here you would typically:
  // 1. Create test merchants with different license states
  // 2. Create test admin users
  // 3. Set up test license files
  // 4. Configure test database state
  
  // For now, we'll just verify that our test users exist
  try {
    // Check if test merchant exists by trying to login
    await page.goto('https://dala3chic.com/login');
    await page.fill('input[name="email"]', 'merchant@test.com');
    await page.fill('input[name="password"]', 'password123');
    await page.click('button[type="submit"]');
    
    // If login successful, merchant exists
    const currentUrl = page.url();
    if (currentUrl.includes('/merchant/dashboard')) {
      console.log('✅ Test merchant account verified');
      await page.goto('https://dala3chic.com/logout');
    } else {
      console.warn('⚠️ Test merchant account may not exist or credentials are incorrect');
    }
    
    // Check admin account
    await page.goto('https://dala3chic.com/login');
    await page.fill('input[name="email"]', 'admin@example.com');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    
    const adminUrl = page.url();
    if (adminUrl.includes('/admin/dashboard')) {
      console.log('✅ Test admin account verified');
      await page.goto('https://dala3chic.com/logout');
    } else {
      console.warn('⚠️ Test admin account may not exist or credentials are incorrect');
    }
    
  } catch (error) {
    console.warn('⚠️ Could not verify test accounts:', error.message);
  }
  
  console.log('📝 Test data setup completed');
}

module.exports = globalSetup;
