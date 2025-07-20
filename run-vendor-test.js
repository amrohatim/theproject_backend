#!/usr/bin/env node

/**
 * Vendor Product Creation Test Runner
 * 
 * This script runs the comprehensive vendor product creation test
 * with proper setup and configuration.
 */

const { execSync } = require('child_process');
const path = require('path');

console.log('🚀 Starting Vendor Product Creation Test Suite...\n');

// Ensure screenshots directory exists
const fs = require('fs');
const screenshotsDir = path.join(__dirname, 'tests', 'screenshots');
if (!fs.existsSync(screenshotsDir)) {
    fs.mkdirSync(screenshotsDir, { recursive: true });
    console.log('📁 Created screenshots directory');
}

try {
    // Run the specific vendor test
    console.log('🧪 Running vendor product creation comprehensive test...\n');
    
    execSync('npx playwright test tests/playwright/vendor-product-creation-comprehensive.spec.js --headed', {
        stdio: 'inherit',
        cwd: __dirname
    });
    
    console.log('\n✅ Vendor product creation test completed successfully!');
    console.log('📸 Screenshots saved to: tests/screenshots/');
    
} catch (error) {
    console.error('\n❌ Test failed with error:', error.message);
    console.log('📸 Check screenshots in tests/screenshots/ for debugging');
    process.exit(1);
}
