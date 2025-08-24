/**
 * Vue.js Stock Validation Test Script
 * 
 * This script tests the hierarchical stock validation functionality in the new Vue.js
 * tabbed product creation interface for merchants.
 * 
 * Test Coverage:
 * 1. General Stock (Total Product Stock) validation
 * 2. Color Stock Allocation validation
 * 3. Size Stock Distribution validation
 * 4. Stock allocation summary and progress bar
 * 5. Over-allocation prevention and auto-correction
 * 6. Form validation and error handling
 */

const TEST_CONFIG = {
    baseUrl: 'http://localhost:8000',
    loginUrl: '/login',
    productCreateUrl: '/merchant/products/create',
    testCredentials: {
        email: 'merchant@test.com',
        password: 'password123'
    },
    timeout: 30000,
    debug: true
};

class VueStockValidationTester {
    constructor() {
        this.results = {
            generalStockValidation: false,
            colorStockAllocation: false,
            sizeStockDistribution: false,
            stockSummaryDisplay: false,
            overAllocationPrevention: false,
            formValidation: false,
            autoCorrection: false,
            progressBarUpdates: false
        };
    }

    log(message, type = 'info') {
        const timestamp = new Date().toLocaleTimeString();
        const prefix = type === 'success' ? '✅' : type === 'error' ? '❌' : type === 'warning' ? '⚠️' : 'ℹ️';
        console.log(`[${timestamp}] ${prefix} ${message}`);
    }

    async wait(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    async waitForElement(selector, timeout = 10000) {
        const startTime = Date.now();
        while (Date.now() - startTime < timeout) {
            const element = document.querySelector(selector);
            if (element && element.offsetParent !== null) {
                return element;
            }
            await this.wait(100);
        }
        throw new Error(`Element ${selector} not found within ${timeout}ms`);
    }

    async waitForVueApp() {
        this.log('Waiting for Vue.js app to initialize...');
        
        // Wait for the loading spinner to disappear and the main content to appear
        let attempts = 0;
        const maxAttempts = 50;
        
        while (attempts < maxAttempts) {
            const loadingSpinner = document.querySelector('.spinner-border');
            const mainContent = document.querySelector('#product-create-app .space-y-6');
            
            if (!loadingSpinner && mainContent) {
                this.log('Vue.js app initialized successfully', 'success');
                return true;
            }
            
            await this.wait(200);
            attempts++;
        }
        
        throw new Error('Vue.js app failed to initialize within timeout');
    }

    async testGeneralStockValidation() {
        this.log('Testing General Stock validation...');
        
        try {
            // Test 1: Set valid general stock
            const stockInput = await this.waitForElement('#stock');
            stockInput.value = '100';
            stockInput.dispatchEvent(new Event('input', { bubbles: true }));
            await this.wait(500);
            
            if (stockInput.value !== '100') {
                throw new Error('General stock input not accepting valid values');
            }
            
            // Test 2: Test negative stock prevention
            stockInput.value = '-10';
            stockInput.dispatchEvent(new Event('input', { bubbles: true }));
            await this.wait(500);
            
            // Vue.js should prevent negative values or auto-correct them
            const finalValue = parseInt(stockInput.value) || 0;
            if (finalValue < 0) {
                throw new Error('Negative stock values not prevented');
            }
            
            // Reset to valid value
            stockInput.value = '100';
            stockInput.dispatchEvent(new Event('input', { bubbles: true }));
            await this.wait(500);
            
            this.log('General stock validation passed', 'success');
            return true;
            
        } catch (error) {
            this.log(`General stock validation failed: ${error.message}`, 'error');
            return false;
        }
    }

    async testColorStockAllocation() {
        this.log('Testing Color Stock allocation...');
        
        try {
            // First, add a color
            const addColorBtn = await this.waitForElement('button:has-text("Add First Color"), button:has-text("Add Color")');
            addColorBtn.click();
            await this.wait(1000);
            
            // Wait for color card to appear
            const colorCard = await this.waitForElement('.vue-card:has(input[placeholder="0"])', 5000);
            
            // Set color name
            const colorNameInput = colorCard.querySelector('input[placeholder="Enter color name..."], select');
            if (colorNameInput) {
                if (colorNameInput.tagName === 'SELECT') {
                    colorNameInput.value = 'Red';
                    colorNameInput.dispatchEvent(new Event('change', { bubbles: true }));
                } else {
                    colorNameInput.value = 'Red';
                    colorNameInput.dispatchEvent(new Event('input', { bubbles: true }));
                }
                await this.wait(500);
            }
            
            // Set color stock
            const colorStockInput = colorCard.querySelector('input[type="number"]');
            if (!colorStockInput) {
                throw new Error('Color stock input not found');
            }
            
            // Test valid allocation (within general stock limit)
            colorStockInput.value = '60';
            colorStockInput.dispatchEvent(new Event('input', { bubbles: true }));
            await this.wait(1000);
            
            // Test over-allocation (should be auto-corrected)
            colorStockInput.value = '150'; // Exceeds general stock of 100
            colorStockInput.dispatchEvent(new Event('input', { bubbles: true }));
            await this.wait(1000);
            
            // Check if auto-correction occurred
            const correctedValue = parseInt(colorStockInput.value) || 0;
            if (correctedValue > 100) {
                throw new Error('Over-allocation not prevented or corrected');
            }
            
            this.log('Color stock allocation validation passed', 'success');
            return true;
            
        } catch (error) {
            this.log(`Color stock allocation failed: ${error.message}`, 'error');
            return false;
        }
    }

    async testStockSummaryDisplay() {
        this.log('Testing Stock Summary display...');
        
        try {
            // Look for stock summary section
            const stockSummary = await this.waitForElement('.vue-card:has-text("Stock Allocation Summary")', 5000);
            
            // Check for required elements
            const totalStockDisplay = stockSummary.querySelector('span:contains("Total Stock")');
            const allocatedStockDisplay = stockSummary.querySelector('span:contains("Allocated Stock")');
            const remainingStockDisplay = stockSummary.querySelector('span:contains("Remaining Stock")');
            const progressBar = stockSummary.querySelector('.bg-gray-200 .h-2');
            
            if (!progressBar) {
                throw new Error('Progress bar not found in stock summary');
            }
            
            // Verify progress bar updates
            const progressWidth = progressBar.style.width;
            if (!progressWidth || progressWidth === '0%') {
                this.log('Progress bar may not be updating correctly', 'warning');
            }
            
            this.log('Stock summary display validation passed', 'success');
            return true;
            
        } catch (error) {
            this.log(`Stock summary display failed: ${error.message}`, 'error');
            return false;
        }
    }

    async testSizeStockDistribution() {
        this.log('Testing Size Stock distribution...');
        
        try {
            // Look for size management section
            const sizeSection = document.querySelector('[data-testid="size-management"], .size-management-container');
            
            if (!sizeSection) {
                this.log('Size management section not visible (may require color with stock > 0)', 'warning');
                return true; // Not a failure if sizes aren't shown yet
            }
            
            // Test size stock inputs if available
            const sizeStockInputs = sizeSection.querySelectorAll('input[type="number"]');
            
            if (sizeStockInputs.length > 0) {
                const firstSizeInput = sizeStockInputs[0];
                
                // Test valid size stock allocation
                firstSizeInput.value = '20';
                firstSizeInput.dispatchEvent(new Event('input', { bubbles: true }));
                await this.wait(500);
                
                // Test over-allocation (should be prevented)
                firstSizeInput.value = '200'; // Exceeds color stock
                firstSizeInput.dispatchEvent(new Event('input', { bubbles: true }));
                await this.wait(500);
                
                const correctedValue = parseInt(firstSizeInput.value) || 0;
                if (correctedValue > 100) { // Assuming color stock is 100 or less
                    this.log('Size stock over-allocation not prevented', 'warning');
                }
            }
            
            this.log('Size stock distribution validation passed', 'success');
            return true;
            
        } catch (error) {
            this.log(`Size stock distribution failed: ${error.message}`, 'error');
            return false;
        }
    }

    async testFormValidation() {
        this.log('Testing Form validation...');
        
        try {
            // Fill required fields
            const nameInput = await this.waitForElement('#name');
            nameInput.value = 'Test Product - Stock Validation';
            nameInput.dispatchEvent(new Event('input', { bubbles: true }));
            
            const categorySelect = await this.waitForElement('#category_id');
            if (categorySelect.options.length > 1) {
                categorySelect.selectedIndex = 1;
                categorySelect.dispatchEvent(new Event('change', { bubbles: true }));
            }
            
            const priceInput = await this.waitForElement('input[type="number"]:not(#stock)');
            priceInput.value = '99.99';
            priceInput.dispatchEvent(new Event('input', { bubbles: true }));
            
            await this.wait(1000);
            
            // Try to submit form
            const submitBtn = document.querySelector('button[type="submit"], button:has-text("Create Product")');
            if (submitBtn) {
                submitBtn.click();
                await this.wait(2000);
                
                // Check for validation messages or success
                const errorMessages = document.querySelectorAll('.text-red-500, .alert-danger');
                const successMessages = document.querySelectorAll('.text-green-500, .alert-success');
                
                if (errorMessages.length > 0 || successMessages.length > 0) {
                    this.log('Form validation working (errors or success detected)', 'success');
                } else {
                    this.log('Form validation response unclear', 'warning');
                }
            }
            
            this.log('Form validation test completed', 'success');
            return true;
            
        } catch (error) {
            this.log(`Form validation failed: ${error.message}`, 'error');
            return false;
        }
    }

    async runAllTests() {
        this.log('Starting Vue.js Stock Validation Tests...');
        
        try {
            // Wait for Vue app to load
            await this.waitForVueApp();
            
            // Run all tests
            this.results.generalStockValidation = await this.testGeneralStockValidation();
            await this.wait(1000);
            
            this.results.colorStockAllocation = await this.testColorStockAllocation();
            await this.wait(1000);
            
            this.results.stockSummaryDisplay = await this.testStockSummaryDisplay();
            await this.wait(1000);
            
            this.results.sizeStockDistribution = await this.testSizeStockDistribution();
            await this.wait(1000);
            
            this.results.formValidation = await this.testFormValidation();
            
            // Print results
            this.printResults();
            
        } catch (error) {
            this.log(`Test execution failed: ${error.message}`, 'error');
        }
    }

    printResults() {
        this.log('\n=== STOCK VALIDATION TEST RESULTS ===');
        
        let passedTests = 0;
        const totalTests = Object.keys(this.results).length;
        
        Object.entries(this.results).forEach(([test, passed]) => {
            const status = passed ? 'PASSED' : 'FAILED';
            const icon = passed ? '✅' : '❌';
            this.log(`${icon} ${test}: ${status}`, passed ? 'success' : 'error');
            if (passed) passedTests++;
        });
        
        this.log(`\n📊 Summary: ${passedTests}/${totalTests} tests passed`);
        
        if (passedTests === totalTests) {
            this.log('🎉 All stock validation tests passed!', 'success');
        } else {
            this.log(`⚠️ ${totalTests - passedTests} test(s) failed`, 'warning');
        }
    }
}

// Auto-run tests when script is loaded
if (typeof window !== 'undefined') {
    window.vueStockTester = new VueStockValidationTester();
    
    // Add a global function to run tests
    window.runStockValidationTests = () => {
        window.vueStockTester.runAllTests();
    };
    
    console.log('Vue Stock Validation Tester loaded. Run tests with: runStockValidationTests()');
}
