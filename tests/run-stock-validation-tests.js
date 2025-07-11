#!/usr/bin/env node

/**
 * Stock Validation Test Runner
 * Simplified test runner for merchant stock validation functionality
 */

const { chromium } = require('playwright');

// Configuration
const BASE_URL = 'https://dala3chic.com';
const PRODUCT_CREATE_URL = `${BASE_URL}/merchant/products/create`;
const PRODUCT_EDIT_URL = `${BASE_URL}/merchant/products/9/edit`;

// Test credentials
const MERCHANT_CREDENTIALS = {
    email: process.env.MERCHANT_EMAIL || 'test@merchant.com',
    password: process.env.MERCHANT_PASSWORD || 'password123'
};

class StockValidationTester {
    constructor() {
        this.browser = null;
        this.page = null;
        this.testResults = [];
    }

    async init() {
        console.log('🚀 Initializing Stock Validation Tests...');
        this.browser = await chromium.launch({ 
            headless: false, // Set to true for CI/CD
            slowMo: 500 // Slow down for better visibility
        });
        this.page = await this.browser.newPage();
        
        // Set viewport for consistent testing
        await this.page.setViewportSize({ width: 1280, height: 720 });
        
        console.log('✅ Browser initialized');
    }

    async login() {
        console.log('🔐 Logging in as merchant...');
        await this.page.goto(`${BASE_URL}/merchant/login`);
        
        await this.page.fill('input[name="email"]', MERCHANT_CREDENTIALS.email);
        await this.page.fill('input[name="password"]', MERCHANT_CREDENTIALS.password);
        await this.page.click('button[type="submit"]');
        
        // Wait for redirect to dashboard
        await this.page.waitForURL('**/merchant/dashboard', { timeout: 10000 });
        console.log('✅ Login successful');
    }

    async runTest(testName, testFunction) {
        console.log(`\n🧪 Running test: ${testName}`);
        try {
            await testFunction();
            console.log(`✅ PASSED: ${testName}`);
            this.testResults.push({ name: testName, status: 'PASSED' });
        } catch (error) {
            console.log(`❌ FAILED: ${testName}`);
            console.log(`   Error: ${error.message}`);
            this.testResults.push({ name: testName, status: 'FAILED', error: error.message });
        }
    }

    async testGeneralStockValidation() {
        await this.page.goto(PRODUCT_CREATE_URL);
        await this.page.waitForLoadState('networkidle');

        // Set general stock to 100
        await this.page.fill('#stock', '100');
        
        // Set color stock to 60
        await this.page.fill('.color-stock-input', '60');
        
        // Add another color
        await this.page.click('#add-color');
        await this.page.waitForTimeout(1000);
        
        // Set second color stock to 50 (total would be 110)
        const colorInputs = await this.page.locator('.color-stock-input').all();
        await colorInputs[1].fill('50');
        
        // Wait for validation
        await this.page.waitForTimeout(1000);
        
        // Check if second color was auto-corrected to 40
        const correctedValue = await colorInputs[1].inputValue();
        if (correctedValue !== '40') {
            throw new Error(`Expected color stock to be corrected to 40, got ${correctedValue}`);
        }
        
        // Check for validation alert
        const alert = this.page.locator('.stock-validation-alert').last();
        const isVisible = await alert.isVisible();
        if (!isVisible) {
            throw new Error('Validation alert should be visible');
        }
    }

    async testNegativeStockPrevention() {
        await this.page.goto(PRODUCT_CREATE_URL);
        await this.page.waitForLoadState('networkidle');

        // Try to set negative stock
        await this.page.fill('#stock', '-10');
        await this.page.waitForTimeout(500);
        
        // Should be corrected to 0
        const value = await this.page.locator('#stock').inputValue();
        if (value !== '0') {
            throw new Error(`Expected negative stock to be corrected to 0, got ${value}`);
        }
        
        // Check for error alert
        const alert = this.page.locator('.stock-validation-alert');
        const alertText = await alert.textContent();
        if (!alertText || !alertText.includes('cannot be negative')) {
            throw new Error('Expected negative stock error message');
        }
    }

    async testVisualFeedback() {
        await this.page.goto(PRODUCT_CREATE_URL);
        await this.page.waitForLoadState('networkidle');

        // Set general stock
        await this.page.fill('#stock', '50');
        
        // Set color stock that exceeds general stock
        const colorInput = this.page.locator('.color-stock-input').first();
        await colorInput.fill('60');
        
        // Wait for visual feedback
        await this.page.waitForTimeout(500);
        
        // Check for border color change (yellow during correction)
        const borderColor = await colorInput.evaluate(el => getComputedStyle(el).borderColor);
        
        // The exact color might vary, but it should not be the default
        if (borderColor === 'rgb(209, 213, 219)') { // Default gray border
            throw new Error('Expected visual feedback (border color change) but got default styling');
        }
    }

    async testStockSummaryDisplay() {
        await this.page.goto(PRODUCT_CREATE_URL);
        await this.page.waitForLoadState('networkidle');

        // Click on general stock input
        await this.page.click('#stock');
        await this.page.waitForTimeout(500);
        
        // Check if summary container appears
        const summary = this.page.locator('#stock-summary-container');
        const isVisible = await summary.isVisible();
        if (!isVisible) {
            throw new Error('Stock summary should be visible when general stock input is focused');
        }
        
        // Check summary content
        const summaryText = await summary.textContent();
        if (!summaryText.includes('Stock Allocation Summary')) {
            throw new Error('Summary should contain allocation information');
        }
    }

    async testFormSubmissionValidation() {
        await this.page.goto(PRODUCT_CREATE_URL);
        await this.page.waitForLoadState('networkidle');

        // Set up invalid stock configuration
        await this.page.fill('#stock', '50');
        await this.page.fill('.color-stock-input', '60');
        
        // Fill required fields
        await this.page.fill('#name', 'Test Product');
        await this.page.fill('#description', 'Test Description');
        await this.page.selectOption('#category_id', { index: 1 });
        await this.page.fill('#price', '100');
        
        // Try to submit
        await this.page.click('button[type="submit"]');
        await this.page.waitForTimeout(1000);
        
        // Should show validation summary
        const validationSummary = this.page.locator('.stock-validation-summary');
        const isVisible = await validationSummary.isVisible();
        if (!isVisible) {
            throw new Error('Validation summary should prevent form submission');
        }
        
        // Should still be on create page
        const currentUrl = this.page.url();
        if (!currentUrl.includes('/create')) {
            throw new Error('Form should not submit with validation errors');
        }
    }

    async testMobileResponsiveness() {
        // Set mobile viewport
        await this.page.setViewportSize({ width: 375, height: 667 });
        
        await this.page.goto(PRODUCT_CREATE_URL);
        await this.page.waitForLoadState('networkidle');
        
        // Test validation on mobile
        await this.page.fill('#stock', '50');
        await this.page.fill('.color-stock-input', '60');
        await this.page.waitForTimeout(500);
        
        // Should still auto-correct
        const value = await this.page.locator('.color-stock-input').inputValue();
        if (value !== '50') {
            throw new Error(`Mobile validation failed: expected 50, got ${value}`);
        }
        
        // Reset viewport
        await this.page.setViewportSize({ width: 1280, height: 720 });
    }

    async runAllTests() {
        await this.init();
        await this.login();

        // Run all tests
        await this.runTest('General Stock Validation', () => this.testGeneralStockValidation());
        await this.runTest('Negative Stock Prevention', () => this.testNegativeStockPrevention());
        await this.runTest('Visual Feedback', () => this.testVisualFeedback());
        await this.runTest('Stock Summary Display', () => this.testStockSummaryDisplay());
        await this.runTest('Form Submission Validation', () => this.testFormSubmissionValidation());
        await this.runTest('Mobile Responsiveness', () => this.testMobileResponsiveness());

        // Print results
        this.printResults();
        
        await this.cleanup();
    }

    printResults() {
        console.log('\n📊 Test Results Summary:');
        console.log('========================');
        
        const passed = this.testResults.filter(r => r.status === 'PASSED').length;
        const failed = this.testResults.filter(r => r.status === 'FAILED').length;
        
        this.testResults.forEach(result => {
            const icon = result.status === 'PASSED' ? '✅' : '❌';
            console.log(`${icon} ${result.name}`);
            if (result.error) {
                console.log(`   ${result.error}`);
            }
        });
        
        console.log(`\nTotal: ${this.testResults.length} | Passed: ${passed} | Failed: ${failed}`);
        
        if (failed === 0) {
            console.log('🎉 All tests passed!');
        } else {
            console.log('⚠️  Some tests failed. Please review the implementation.');
        }
    }

    async cleanup() {
        if (this.browser) {
            await this.browser.close();
            console.log('🧹 Browser closed');
        }
    }
}

// Run tests if this file is executed directly
if (require.main === module) {
    const tester = new StockValidationTester();
    tester.runAllTests().catch(error => {
        console.error('❌ Test runner failed:', error);
        process.exit(1);
    });
}

module.exports = StockValidationTester;
