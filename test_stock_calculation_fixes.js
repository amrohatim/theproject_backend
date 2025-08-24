/**
 * Test Script for Size Allocation Calculation Fixes
 * 
 * This script tests the fixed calculation logic in the dynamic stock validation system.
 * It verifies that:
 * 1. Color stock calculations are correct
 * 2. Size allocation math is accurate
 * 3. Real-time updates work properly
 * 4. Progress bars show correct percentages
 * 5. Over-allocation prevention works
 */

class StockCalculationTester {
    constructor() {
        this.testResults = [];
        this.manager = null;
    }

    log(message, type = 'info') {
        const timestamp = new Date().toLocaleTimeString();
        const logEntry = `[${timestamp}] ${type.toUpperCase()}: ${message}`;
        console.log(logEntry);
        this.testResults.push({ timestamp, type, message });
    }

    async wait(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    async waitForElement(selector, timeout = 5000) {
        const startTime = Date.now();
        while (Date.now() - startTime < timeout) {
            const element = document.querySelector(selector);
            if (element) return element;
            await this.wait(100);
        }
        throw new Error(`Element ${selector} not found within ${timeout}ms`);
    }

    async runAllTests() {
        this.log('Starting Stock Calculation Tests...');
        
        try {
            // Wait for the manager to be available
            await this.waitForManager();
            
            // Run individual tests
            await this.testBasicCalculations();
            await this.testProgressBarUpdates();
            await this.testOverAllocationPrevention();
            await this.testEvenDistribution();
            await this.testClearAllocation();
            await this.testEdgeCases();
            
            this.log('All tests completed successfully!', 'success');
            return this.generateReport();
            
        } catch (error) {
            this.log(`Test suite failed: ${error.message}`, 'error');
            throw error;
        }
    }

    async waitForManager() {
        let attempts = 0;
        while (attempts < 50) {
            if (window.dynamicColorSizeManager) {
                this.manager = window.dynamicColorSizeManager;
                this.log('Dynamic Color Size Manager found');
                return;
            }
            await this.wait(100);
            attempts++;
        }
        throw new Error('Dynamic Color Size Manager not found');
    }

    async testBasicCalculations() {
        this.log('Testing basic stock calculations...');
        
        // Find first color item
        const colorItem = document.querySelector('.color-item');
        if (!colorItem) {
            throw new Error('No color item found');
        }

        // Set color and stock
        const colorSelect = colorItem.querySelector('.color-name-select');
        const stockInput = colorItem.querySelector('.color-stock-input');
        
        if (!colorSelect || !stockInput) {
            throw new Error('Color controls not found');
        }

        // Set test values
        colorSelect.value = 'Red';
        colorSelect.dispatchEvent(new Event('change', { bubbles: true }));
        
        stockInput.value = '20';
        stockInput.dispatchEvent(new Event('input', { bubbles: true }));
        
        await this.wait(1000);

        // Verify size section appears
        const sizeSection = colorItem.querySelector('.color-size-allocation');
        if (!sizeSection) {
            throw new Error('Size allocation section not created');
        }

        // Check initial display
        const allocatedSpan = sizeSection.querySelector('.allocated-stock');
        const remainingSpan = sizeSection.querySelector('.remaining-stock');
        const totalSpan = sizeSection.querySelector('.total-stock');

        if (allocatedSpan.textContent !== '0') {
            throw new Error(`Expected allocated to be 0, got ${allocatedSpan.textContent}`);
        }
        if (remainingSpan.textContent !== '20') {
            throw new Error(`Expected remaining to be 20, got ${remainingSpan.textContent}`);
        }
        if (totalSpan.textContent !== '20') {
            throw new Error(`Expected total to be 20, got ${totalSpan.textContent}`);
        }

        this.log('Basic calculations test passed', 'success');
    }

    async testProgressBarUpdates() {
        this.log('Testing progress bar updates...');
        
        const sizeSection = document.querySelector('.color-size-allocation');
        if (!sizeSection) {
            throw new Error('Size section not found');
        }

        const sizeInputs = sizeSection.querySelectorAll('.size-stock-input');
        if (sizeInputs.length === 0) {
            this.log('No size inputs found - skipping progress bar test', 'warning');
            return;
        }

        // Allocate 10 units to first size
        const firstInput = sizeInputs[0];
        firstInput.value = '10';
        firstInput.dispatchEvent(new Event('input', { bubbles: true }));
        
        await this.wait(500);

        // Check progress bar
        const progressBar = sizeSection.querySelector('.stock-progress-bar');
        const allocationPercentage = sizeSection.querySelector('.allocation-percentage');
        const allocatedText = sizeSection.querySelector('.allocated-text');
        
        if (!progressBar || !allocationPercentage || !allocatedText) {
            throw new Error('Progress bar elements not found');
        }

        // Should be 50% (10 out of 20)
        const expectedPercentage = 50;
        const actualPercentage = parseInt(allocationPercentage.textContent);
        
        if (actualPercentage !== expectedPercentage) {
            throw new Error(`Expected ${expectedPercentage}%, got ${actualPercentage}%`);
        }

        // Check allocated text
        if (!allocatedText.textContent.includes('10')) {
            throw new Error(`Expected allocated text to show 10, got ${allocatedText.textContent}`);
        }

        this.log('Progress bar updates test passed', 'success');
    }

    async testOverAllocationPrevention() {
        this.log('Testing over-allocation prevention...');
        
        const sizeSection = document.querySelector('.color-size-allocation');
        const sizeInputs = sizeSection.querySelectorAll('.size-stock-input');
        
        if (sizeInputs.length < 2) {
            this.log('Need at least 2 size inputs for over-allocation test', 'warning');
            return;
        }

        // Try to allocate more than available (20 total, already have 10 allocated)
        const secondInput = sizeInputs[1];
        secondInput.value = '15'; // This would make total 25, exceeding 20
        secondInput.dispatchEvent(new Event('input', { bubbles: true }));
        
        await this.wait(500);

        // Check if value was capped
        const actualValue = parseInt(secondInput.value);
        const maxExpected = 10; // Should be capped to 10 (20 total - 10 already allocated)
        
        if (actualValue > maxExpected) {
            throw new Error(`Over-allocation not prevented: got ${actualValue}, max should be ${maxExpected}`);
        }

        this.log(`Over-allocation prevented: input capped to ${actualValue}`, 'success');
    }

    async testEvenDistribution() {
        this.log('Testing even distribution functionality...');
        
        const sizeSection = document.querySelector('.color-size-allocation');
        const distributeButton = sizeSection.querySelector('.distribute-evenly');
        
        if (!distributeButton) {
            this.log('Distribute evenly button not found - skipping test', 'warning');
            return;
        }

        // Click distribute evenly
        distributeButton.click();
        await this.wait(500);

        // Check if stock was distributed
        const sizeInputs = sizeSection.querySelectorAll('.size-stock-input');
        const totalAllocated = Array.from(sizeInputs).reduce((sum, input) => {
            return sum + (parseInt(input.value) || 0);
        }, 0);

        if (totalAllocated !== 20) {
            throw new Error(`Even distribution failed: total ${totalAllocated}, expected 20`);
        }

        this.log('Even distribution test passed', 'success');
    }

    async testClearAllocation() {
        this.log('Testing clear allocation functionality...');
        
        const sizeSection = document.querySelector('.color-size-allocation');
        const clearButton = sizeSection.querySelector('.clear-allocation');
        
        if (!clearButton) {
            this.log('Clear allocation button not found - skipping test', 'warning');
            return;
        }

        // Click clear allocation
        clearButton.click();
        await this.wait(500);

        // Check if all allocations were cleared
        const sizeInputs = sizeSection.querySelectorAll('.size-stock-input');
        const totalAllocated = Array.from(sizeInputs).reduce((sum, input) => {
            return sum + (parseInt(input.value) || 0);
        }, 0);

        if (totalAllocated !== 0) {
            throw new Error(`Clear allocation failed: total ${totalAllocated}, expected 0`);
        }

        // Check remaining stock display
        const remainingSpan = sizeSection.querySelector('.remaining-stock');
        if (remainingSpan.textContent !== '20') {
            throw new Error(`Expected remaining to be 20 after clear, got ${remainingSpan.textContent}`);
        }

        this.log('Clear allocation test passed', 'success');
    }

    async testEdgeCases() {
        this.log('Testing edge cases...');
        
        const sizeSection = document.querySelector('.color-size-allocation');
        const sizeInputs = sizeSection.querySelectorAll('.size-stock-input');
        
        if (sizeInputs.length === 0) {
            this.log('No size inputs for edge case testing', 'warning');
            return;
        }

        // Test negative values
        const firstInput = sizeInputs[0];
        firstInput.value = '-5';
        firstInput.dispatchEvent(new Event('input', { bubbles: true }));
        await this.wait(200);

        if (parseInt(firstInput.value) !== 0) {
            throw new Error(`Negative value not handled: got ${firstInput.value}, expected 0`);
        }

        // Test non-numeric values
        firstInput.value = 'abc';
        firstInput.dispatchEvent(new Event('input', { bubbles: true }));
        await this.wait(200);

        if (parseInt(firstInput.value) !== 0) {
            throw new Error(`Non-numeric value not handled: got ${firstInput.value}, expected 0`);
        }

        this.log('Edge cases test passed', 'success');
    }

    generateReport() {
        const successCount = this.testResults.filter(r => r.type === 'success').length;
        const errorCount = this.testResults.filter(r => r.type === 'error').length;
        const warningCount = this.testResults.filter(r => r.type === 'warning').length;

        return {
            summary: {
                total: this.testResults.length,
                success: successCount,
                errors: errorCount,
                warnings: warningCount
            },
            results: this.testResults
        };
    }
}

// Make tester globally available
window.StockCalculationTester = StockCalculationTester;

// Auto-run if in test mode
if (window.location.search.includes('test=stock')) {
    document.addEventListener('DOMContentLoaded', async () => {
        await new Promise(resolve => setTimeout(resolve, 2000)); // Wait for page to load
        const tester = new StockCalculationTester();
        try {
            const report = await tester.runAllTests();
            console.log('Test Report:', report);
        } catch (error) {
            console.error('Test failed:', error);
        }
    });
}
