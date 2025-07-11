const { chromium } = require('playwright');

async function testAddColorFunctionality() {
    console.log('🚀 Starting Add Color Button Test...');
    
    const browser = await chromium.launch({ 
        headless: false,
        slowMo: 1000 // Slow down for better visibility
    });
    
    const context = await browser.newContext({
        viewport: { width: 1920, height: 1080 }
    });
    
    const page = await context.newPage();
    
    try {
        // Navigate to the product edit page
        console.log('📍 Navigating to product edit page...');
        await page.goto('https://dala3chic.com/merchant/products/11/edit', { 
            waitUntil: 'networkidle',
            timeout: 30000 
        });
        
        // Wait for page to fully load
        await page.waitForTimeout(3000);
        
        // Check if we need to login first
        const currentUrl = page.url();
        if (currentUrl.includes('/login')) {
            console.log('🔐 Login required - please login manually and run the test again');
            await page.waitForTimeout(10000); // Give time to login manually
        }
        
        // Navigate to Colors & Images tab
        console.log('🎨 Switching to Colors & Images tab...');
        await page.click('button[data-tab="colors"]');
        await page.waitForTimeout(1000);
        
        // Take screenshot before clicking
        await page.screenshot({ path: 'before-add-color.png', fullPage: true });
        console.log('📸 Screenshot taken: before-add-color.png');
        
        // Count existing color items
        const initialColorCount = await page.locator('.color-item').count();
        console.log(`📊 Initial color count: ${initialColorCount}`);
        
        // Check if Add Color button exists and is visible
        const addColorButton = page.locator('#add-color');
        const isVisible = await addColorButton.isVisible();
        console.log(`👁️ Add Color button visible: ${isVisible}`);
        
        if (!isVisible) {
            // Try the "Add First Color" button if main button is not visible
            const addFirstColorButton = page.locator('#add-first-color');
            const isFirstVisible = await addFirstColorButton.isVisible();
            console.log(`👁️ Add First Color button visible: ${isFirstVisible}`);
            
            if (isFirstVisible) {
                console.log('🖱️ Clicking Add First Color button...');
                await addFirstColorButton.click();
            } else {
                throw new Error('Neither Add Color nor Add First Color button is visible');
            }
        } else {
            console.log('🖱️ Clicking Add Color button...');
            await addColorButton.click();
        }
        
        // Wait for new color form to be added
        await page.waitForTimeout(2000);
        
        // Count color items after clicking
        const finalColorCount = await page.locator('.color-item').count();
        console.log(`📊 Final color count: ${finalColorCount}`);
        
        // Take screenshot after clicking
        await page.screenshot({ path: 'after-add-color.png', fullPage: true });
        console.log('📸 Screenshot taken: after-add-color.png');
        
        // Verify that a new color form was added
        if (finalColorCount > initialColorCount) {
            console.log('✅ SUCCESS: New color form was added!');
            console.log(`📈 Color count increased from ${initialColorCount} to ${finalColorCount}`);
            
            // Test the new color form functionality
            const newColorItem = page.locator('.color-item').last();
            
            // Test color name selection
            console.log('🧪 Testing color name selection...');
            await newColorItem.locator('select[name*="[name]"]').selectOption('Red');
            await page.waitForTimeout(500);
            
            // Test color code input
            console.log('🧪 Testing color code input...');
            await newColorItem.locator('input[name*="[color_code]"]').fill('#FF0000');
            await page.waitForTimeout(500);
            
            // Test stock input
            console.log('🧪 Testing stock input...');
            await newColorItem.locator('input[name*="[stock]"]').fill('10');
            await page.waitForTimeout(500);
            
            // Take final screenshot
            await page.screenshot({ path: 'final-test-result.png', fullPage: true });
            console.log('📸 Final screenshot taken: final-test-result.png');
            
            console.log('✅ All tests passed! Add Color functionality is working correctly.');
            
        } else {
            console.log('❌ FAILURE: No new color form was added');
            console.log(`📊 Color count remained at ${initialColorCount}`);
            
            // Check for JavaScript errors
            const errors = await page.evaluate(() => {
                return window.console.errors || [];
            });
            
            if (errors.length > 0) {
                console.log('🐛 JavaScript errors found:', errors);
            }
            
            throw new Error('Add Color button did not add a new color form');
        }
        
    } catch (error) {
        console.error('❌ Test failed:', error.message);
        await page.screenshot({ path: 'error-screenshot.png', fullPage: true });
        console.log('📸 Error screenshot taken: error-screenshot.png');
        throw error;
    } finally {
        await browser.close();
    }
}

// Run the test
testAddColorFunctionality()
    .then(() => {
        console.log('🎉 Test completed successfully!');
        process.exit(0);
    })
    .catch((error) => {
        console.error('💥 Test failed:', error);
        process.exit(1);
    });
