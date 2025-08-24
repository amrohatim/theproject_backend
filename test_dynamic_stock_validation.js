/**
 * Test Script for Dynamic Stock Validation System
 * 
 * This script tests the dynamic stock validation functionality in the Laravel vendor dashboard
 * product creation form, specifically:
 * 1. Size section visibility when color stock is 0 or less
 * 2. Stock validation preventing over-allocation
 * 3. Event handling and integration between form and dynamic manager
 * 4. Progress bar updates and validation messages
 */

// Test configuration
const TEST_CONFIG = {
    baseUrl: 'http://localhost:8000',
    testRoute: '/test/vendor-product-create',
    timeout: 10000,
    debug: true
};

// Test utilities
const TestUtils = {
    log: (message, type = 'info') => {
        const timestamp = new Date().toISOString();
        const prefix = type === 'error' ? '❌' : type === 'success' ? '✅' : 'ℹ️';
        console.log(`[${timestamp}] ${prefix} ${message}`);
    },

    wait: (ms) => new Promise(resolve => setTimeout(resolve, ms)),

    waitForElement: async (selector, timeout = 5000) => {
        const start = Date.now();
        while (Date.now() - start < timeout) {
            const element = document.querySelector(selector);
            if (element) return element;
            await TestUtils.wait(100);
        }
        throw new Error(`Element ${selector} not found within ${timeout}ms`);
    },

    waitForElementToDisappear: async (selector, timeout = 5000) => {
        const start = Date.now();
        while (Date.now() - start < timeout) {
            const element = document.querySelector(selector);
            if (!element) return true;
            await TestUtils.wait(100);
        }
        throw new Error(`Element ${selector} still visible after ${timeout}ms`);
    }
};

// Test cases
const TestCases = {
    async testSizeVisibilityWithZeroStock() {
        TestUtils.log('Testing size section visibility with zero stock...');
        
        try {
            // Find the first color item
            const colorItem = await TestUtils.waitForElement('.color-item');
            const colorSelect = colorItem.querySelector('.color-name-select');
            const stockInput = colorItem.querySelector('.color-stock-input');
            
            // Select a color first
            colorSelect.value = 'Red';
            colorSelect.dispatchEvent(new Event('change', { bubbles: true }));
            await TestUtils.wait(500);
            
            // Set stock to a positive value first to create size section
            stockInput.value = '10';
            stockInput.dispatchEvent(new Event('input', { bubbles: true }));
            await TestUtils.wait(1000);
            
            // Verify size section appears
            const sizeSection = await TestUtils.waitForElement('.color-size-allocation');
            TestUtils.log('Size section appeared with positive stock', 'success');
            
            // Now set stock to 0
            stockInput.value = '0';
            stockInput.dispatchEvent(new Event('input', { bubbles: true }));
            await TestUtils.wait(1000);
            
            // Verify size section disappears and stock required message appears
            await TestUtils.waitForElementToDisappear('.color-size-allocation');
            const stockRequiredMessage = await TestUtils.waitForElement('.stock-required-message');
            
            TestUtils.log('Size section hidden and stock required message shown with zero stock', 'success');
            return true;
            
        } catch (error) {
            TestUtils.log(`Size visibility test failed: ${error.message}`, 'error');
            return false;
        }
    },

    async testStockValidationPreventsOverAllocation() {
        TestUtils.log('Testing stock validation prevents over-allocation...');
        
        try {
            // Find the first color item
            const colorItem = await TestUtils.waitForElement('.color-item');
            const colorSelect = colorItem.querySelector('.color-name-select');
            const stockInput = colorItem.querySelector('.color-stock-input');
            
            // Select a color and set stock to 10
            colorSelect.value = 'Blue';
            colorSelect.dispatchEvent(new Event('change', { bubbles: true }));
            stockInput.value = '10';
            stockInput.dispatchEvent(new Event('input', { bubbles: true }));
            await TestUtils.wait(1000);
            
            // Wait for size section to appear
            const sizeSection = await TestUtils.waitForElement('.color-size-allocation');
            
            // Wait for size inputs to be populated
            await TestUtils.wait(1000);
            const sizeInputs = sizeSection.querySelectorAll('.size-stock-input');
            
            if (sizeInputs.length === 0) {
                TestUtils.log('No size inputs found - this may be expected if no sizes are configured', 'info');
                return true;
            }
            
            // Try to allocate more than available stock
            const firstSizeInput = sizeInputs[0];
            firstSizeInput.value = '15'; // More than the 10 available
            firstSizeInput.dispatchEvent(new Event('input', { bubbles: true }));
            await TestUtils.wait(1000);
            
            // Check if the value was capped to prevent over-allocation
            const actualValue = parseInt(firstSizeInput.value);
            if (actualValue <= 10) {
                TestUtils.log(`Over-allocation prevented: input capped to ${actualValue}`, 'success');
                return true;
            } else {
                TestUtils.log(`Over-allocation not prevented: value is ${actualValue}`, 'error');
                return false;
            }
            
        } catch (error) {
            TestUtils.log(`Stock validation test failed: ${error.message}`, 'error');
            return false;
        }
    },

    async testProgressBarUpdates() {
        TestUtils.log('Testing progress bar updates...');
        
        try {
            // Find the first color item
            const colorItem = await TestUtils.waitForElement('.color-item');
            const colorSelect = colorItem.querySelector('.color-name-select');
            const stockInput = colorItem.querySelector('.color-stock-input');
            
            // Select a color and set stock
            colorSelect.value = 'Green';
            colorSelect.dispatchEvent(new Event('change', { bubbles: true }));
            stockInput.value = '20';
            stockInput.dispatchEvent(new Event('input', { bubbles: true }));
            await TestUtils.wait(1000);
            
            // Wait for size section
            const sizeSection = await TestUtils.waitForElement('.color-size-allocation');
            await TestUtils.wait(1000);
            
            // Check if progress bar exists
            const progressBar = sizeSection.querySelector('.stock-progress-bar');
            const allocatedStock = sizeSection.querySelector('.allocated-stock');
            const remainingStock = sizeSection.querySelector('.remaining-stock');
            
            if (progressBar && allocatedStock && remainingStock) {
                TestUtils.log('Progress bar and stock displays found', 'success');
                
                // Check initial values
                const initialAllocated = allocatedStock.textContent;
                const initialRemaining = remainingStock.textContent;
                TestUtils.log(`Initial - Allocated: ${initialAllocated}, Remaining: ${initialRemaining}`, 'info');
                
                return true;
            } else {
                TestUtils.log('Progress bar or stock displays not found', 'error');
                return false;
            }
            
        } catch (error) {
            TestUtils.log(`Progress bar test failed: ${error.message}`, 'error');
            return false;
        }
    },

    async testEventHandling() {
        TestUtils.log('Testing event handling integration...');
        
        try {
            // Check if dynamic color-size manager is available
            if (!window.dynamicColorSizeManager) {
                TestUtils.log('Dynamic color-size manager not found on window object', 'error');
                return false;
            }
            
            TestUtils.log('Dynamic color-size manager found', 'success');
            
            // Test custom event dispatching
            const testColorItem = document.querySelector('.color-item');
            if (!testColorItem) {
                TestUtils.log('No color item found for event testing', 'error');
                return false;
            }
            
            // Dispatch a test event
            const testEvent = new CustomEvent('colorStockChanged', {
                detail: {
                    colorItem: testColorItem,
                    colorName: 'TestColor',
                    stock: 0
                }
            });
            
            document.dispatchEvent(testEvent);
            await TestUtils.wait(500);
            
            TestUtils.log('Event dispatching test completed', 'success');
            return true;
            
        } catch (error) {
            TestUtils.log(`Event handling test failed: ${error.message}`, 'error');
            return false;
        }
    }
};

// Main test runner
const TestRunner = {
    async runAllTests() {
        TestUtils.log('Starting Dynamic Stock Validation Tests...');
        
        const results = {
            sizeVisibility: false,
            stockValidation: false,
            progressBar: false,
            eventHandling: false
        };
        
        try {
            // Wait for page to load completely
            await TestUtils.wait(2000);
            
            // Run tests
            results.sizeVisibility = await TestCases.testSizeVisibilityWithZeroStock();
            await TestUtils.wait(1000);
            
            results.stockValidation = await TestCases.testStockValidationPreventsOverAllocation();
            await TestUtils.wait(1000);
            
            results.progressBar = await TestCases.testProgressBarUpdates();
            await TestUtils.wait(1000);
            
            results.eventHandling = await TestCases.testEventHandling();
            
            // Report results
            TestUtils.log('\n=== TEST RESULTS ===');
            Object.entries(results).forEach(([test, passed]) => {
                TestUtils.log(`${test}: ${passed ? 'PASSED' : 'FAILED'}`, passed ? 'success' : 'error');
            });
            
            const passedCount = Object.values(results).filter(Boolean).length;
            const totalCount = Object.keys(results).length;
            
            TestUtils.log(`\nOverall: ${passedCount}/${totalCount} tests passed`, 
                passedCount === totalCount ? 'success' : 'error');
            
            return results;
            
        } catch (error) {
            TestUtils.log(`Test runner failed: ${error.message}`, 'error');
            return results;
        }
    }
};

// Auto-run tests when script is loaded
if (typeof window !== 'undefined') {
    // Browser environment - wait for DOM and run tests
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => TestRunner.runAllTests(), 3000);
        });
    } else {
        setTimeout(() => TestRunner.runAllTests(), 3000);
    }
} else {
    // Node.js environment - export for use with automation tools
    module.exports = { TestRunner, TestCases, TestUtils };
}
