// Global teardown for Playwright tests
const { chromium } = require('@playwright/test');

async function globalTeardown(config) {
  console.log('🧹 Starting global teardown for License Management tests...');
  
  const browser = await chromium.launch();
  const page = await browser.newPage();
  
  try {
    // Clean up test data if needed
    await cleanupTestData(page);
    
    console.log('✅ Global teardown completed successfully');
  } catch (error) {
    console.error('❌ Global teardown failed:', error);
    // Don't throw error in teardown to avoid masking test failures
  } finally {
    await browser.close();
  }
}

async function cleanupTestData(page) {
  console.log('🧹 Cleaning up test data...');
  
  // Here you would typically:
  // 1. Remove test license files
  // 2. Reset test merchant license states
  // 3. Clean up any test data created during tests
  // 4. Reset database to clean state
  
  try {
    // Example: Reset test merchant license status
    // This would typically be done through API calls or database operations
    
    console.log('🧹 Test data cleanup completed');
  } catch (error) {
    console.warn('⚠️ Some test data may not have been cleaned up:', error.message);
  }
}

module.exports = globalTeardown;
