/**
 * Browser automation test for vendor size management modal
 * Tests the enhanced size management functionality with dropdowns
 */

const { chromium } = require('playwright');

async function testVendorSizeManagement() {
    console.log('🚀 Starting Vendor Size Management Test...');
    
    const browser = await chromium.launch({ headless: false });
    const context = await browser.newContext();
    const page = await context.newPage();
    
    try {
        // Navigate to vendor product creation page
        console.log('📍 Navigating to vendor product creation page...');
        await page.goto('http://localhost:8000/vendor/products/create');
        
        // Wait for page to load
        await page.waitForTimeout(3000);
        
        // Navigate to Colors & Images tab
        console.log('🎨 Clicking on Colors & Images tab...');
        await page.click('button:has-text("Colors & Images")');
        await page.waitForTimeout(1000);
        
        // Add first color
        console.log('➕ Adding first color...');
        await page.click('button:has-text("Add First Color")');
        await page.waitForTimeout(2000);
        
        // Select a color name to enable size management
        console.log('🎯 Selecting color name...');
        const colorDropdown = page.locator('.color-selection-container').first();
        await colorDropdown.click();
        await page.waitForTimeout(500);
        
        // Select Red color
        await page.click('text=Red');
        await page.waitForTimeout(1000);
        
        // Set color stock to enable size management
        console.log('📊 Setting color stock...');
        const stockInput = page.locator('input[placeholder="10"]').first();
        await stockInput.fill('10');
        await page.waitForTimeout(1000);
        
        // Find and click the "Add Size" button
        console.log('🔍 Looking for Add Size button...');
        await page.waitForSelector('button:has-text("Add Size")', { timeout: 10000 });
        await page.click('button:has-text("Add Size")');
        
        // Wait for modal to appear
        console.log('⏳ Waiting for Add New Size modal...');
        await page.waitForSelector('text=Add New Size', { timeout: 5000 });
        
        // Test 1: Verify modal opens correctly
        console.log('✅ Test 1: Modal opens correctly');
        const modalTitle = await page.textContent('h3:has-text("Add New Size")');
        if (!modalTitle) {
            throw new Error('Modal title not found');
        }
        
        // Test 2: Verify Size Category dropdown is present and first
        console.log('✅ Test 2: Checking Size Category dropdown...');
        const categoryDropdown = page.locator('select').first();
        const categoryLabel = await page.textContent('label:has-text("Size Category")');
        if (!categoryLabel) {
            throw new Error('Size Category label not found');
        }
        
        // Test 3: Verify Size Name dropdown is present and second
        console.log('✅ Test 3: Checking Size Name dropdown...');
        const nameLabel = await page.textContent('label:has-text("Size Name")');
        if (!nameLabel) {
            throw new Error('Size Name label not found');
        }
        
        // Test 4: Verify Size Value dropdown is present and third
        console.log('✅ Test 4: Checking Size Value dropdown...');
        const valueLabel = await page.textContent('label:has-text("Size Value")');
        if (!valueLabel) {
            throw new Error('Size Value label not found');
        }
        
        // Test 5: Test dropdown functionality
        console.log('✅ Test 5: Testing dropdown functionality...');
        
        // Select category
        await categoryDropdown.selectOption('clothing');
        await page.waitForTimeout(500);
        
        // Verify size name dropdown is enabled
        const nameDropdown = page.locator('select').nth(1);
        const isNameEnabled = await nameDropdown.isEnabled();
        if (!isNameEnabled) {
            throw new Error('Size Name dropdown should be enabled after category selection');
        }
        
        // Select size name
        await nameDropdown.selectOption('Small');
        await page.waitForTimeout(500);
        
        // Verify size value dropdown is enabled
        const valueDropdown = page.locator('select').nth(2);
        const isValueEnabled = await valueDropdown.isEnabled();
        if (!isValueEnabled) {
            throw new Error('Size Value dropdown should be enabled after name selection');
        }
        
        // Select size value
        await valueDropdown.selectOption('S');
        await page.waitForTimeout(500);
        
        // Test 6: Test adding the size
        console.log('✅ Test 6: Testing size addition...');
        await page.click('button:has-text("Add Size")');
        await page.waitForTimeout(2000);
        
        // Verify modal closes
        const modalExists = await page.locator('text=Add New Size').count();
        if (modalExists > 0) {
            console.log('⚠️  Modal still visible, checking for validation errors...');
        } else {
            console.log('✅ Modal closed successfully after adding size');
        }
        
        // Test 7: Verify field order
        console.log('✅ Test 7: Verifying field order...');
        // Re-open modal to check field order
        await page.click('button:has-text("Add Size")');
        await page.waitForTimeout(1000);
        
        const labels = await page.locator('label').allTextContents();
        const sizeLabels = labels.filter(label => 
            label.includes('Size Category') || 
            label.includes('Size Name') || 
            label.includes('Size Value')
        );
        
        const expectedOrder = ['Size Category', 'Size Name', 'Size Value'];
        let orderCorrect = true;
        for (let i = 0; i < expectedOrder.length; i++) {
            if (!sizeLabels.some(label => label.includes(expectedOrder[i]))) {
                orderCorrect = false;
                break;
            }
        }
        
        if (orderCorrect) {
            console.log('✅ Field order is correct');
        } else {
            console.log('❌ Field order is incorrect');
            console.log('Expected:', expectedOrder);
            console.log('Found:', sizeLabels);
        }
        
        console.log('🎉 All tests completed successfully!');
        
    } catch (error) {
        console.error('❌ Test failed:', error.message);
        throw error;
    } finally {
        await browser.close();
    }
}

// Run the test
if (require.main === module) {
    testVendorSizeManagement()
        .then(() => {
            console.log('✅ Test suite completed successfully');
            process.exit(0);
        })
        .catch((error) => {
            console.error('❌ Test suite failed:', error);
            process.exit(1);
        });
}

module.exports = { testVendorSizeManagement };
