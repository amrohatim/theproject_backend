const { test, expect } = require('@playwright/test');

test.describe('Merchant Product Edit - Add Color Functionality', () => {
    let page;

    test.beforeEach(async ({ browser }) => {
        page = await browser.newPage();
        
        // Enable console logging to catch JavaScript errors
        page.on('console', msg => {
            if (msg.type() === 'error') {
                console.log('❌ Console Error:', msg.text());
            } else if (msg.type() === 'log') {
                console.log('📝 Console Log:', msg.text());
            }
        });

        // Listen for page errors
        page.on('pageerror', error => {
            console.log('❌ Page Error:', error.message);
        });

        // Navigate to the merchant login page first
        await page.goto('https://dala3chic.com/merchant/login');
        await page.waitForLoadState('networkidle');
    });

    test('should investigate and fix Add Color button functionality', async () => {
        // Step 1: Login as merchant
        console.log('🔐 Attempting merchant login...');
        
        // Check if we're already logged in or need to login
        const currentUrl = page.url();
        if (currentUrl.includes('/login')) {
            // Fill login form - we'll need to use test credentials
            await page.fill('input[name="email"]', 'merchant@test.com'); // Replace with actual test merchant email
            await page.fill('input[name="password"]', 'password'); // Replace with actual test password
            await page.click('button[type="submit"]');
            await page.waitForLoadState('networkidle');
        }

        // Step 2: Navigate to product edit page
        console.log('🔗 Navigating to product edit page...');
        await page.goto('https://dala3chic.com/merchant/products/11/edit');
        await page.waitForLoadState('networkidle');

        // Take screenshot of initial state
        await page.screenshot({ 
            path: 'tests/screenshots/product-edit-initial.png',
            fullPage: true 
        });

        // Step 3: Navigate to Colors & Images tab
        console.log('🎨 Switching to Colors & Images tab...');
        await page.click('button[data-tab="colors"]');
        await page.waitForTimeout(1000); // Wait for tab switch animation

        // Take screenshot of colors tab
        await page.screenshot({ 
            path: 'tests/screenshots/colors-tab-initial.png',
            fullPage: true 
        });

        // Step 4: Check if Add Color button exists and is visible
        console.log('🔍 Checking Add Color button...');
        const addColorButton = page.locator('#add-color');
        await expect(addColorButton).toBeVisible();
        
        // Log button properties
        const buttonText = await addColorButton.textContent();
        const buttonClasses = await addColorButton.getAttribute('class');
        console.log('📋 Add Color Button Text:', buttonText);
        console.log('📋 Add Color Button Classes:', buttonClasses);

        // Step 5: Check current color items count
        const initialColorItems = await page.locator('.color-item').count();
        console.log('📊 Initial color items count:', initialColorItems);

        // Step 6: Check for JavaScript errors before clicking
        const jsErrors = [];
        page.on('pageerror', error => jsErrors.push(error.message));

        // Step 7: Click the Add Color button
        console.log('🖱️ Clicking Add Color button...');
        await addColorButton.click();
        await page.waitForTimeout(2000); // Wait for any DOM changes

        // Step 8: Check if new color item was added
        const finalColorItems = await page.locator('.color-item').count();
        console.log('📊 Final color items count:', finalColorItems);

        // Take screenshot after clicking
        await page.screenshot({ 
            path: 'tests/screenshots/after-add-color-click.png',
            fullPage: true 
        });

        // Step 9: Check for JavaScript console logs and errors
        console.log('🐛 JavaScript errors found:', jsErrors);

        // Step 10: Verify the functionality
        if (finalColorItems > initialColorItems) {
            console.log('✅ Add Color button is working correctly!');
            expect(finalColorItems).toBe(initialColorItems + 1);
        } else {
            console.log('❌ Add Color button is not working - investigating...');
            
            // Check if the button has event listeners
            const hasListener = await page.evaluate(() => {
                const button = document.getElementById('add-color');
                return button && button.hasAttribute('data-listener-attached');
            });
            console.log('🔗 Button has listener attached:', hasListener);

            // Check if the colors container exists
            const containerExists = await page.locator('#colors-container').count();
            console.log('📦 Colors container exists:', containerExists > 0);

            // Check if there are any existing color items to clone
            const existingItems = await page.locator('.color-item').count();
            console.log('🎨 Existing color items for cloning:', existingItems);

            // Execute the handleAddColor function manually to test
            const manualResult = await page.evaluate(() => {
                try {
                    // Check if the function exists
                    if (typeof handleAddColor === 'function') {
                        const fakeEvent = { preventDefault: () => {}, stopPropagation: () => {} };
                        handleAddColor(fakeEvent);
                        return 'Function executed successfully';
                    } else {
                        return 'handleAddColor function not found';
                    }
                } catch (error) {
                    return 'Error executing function: ' + error.message;
                }
            });
            console.log('🔧 Manual function execution result:', manualResult);
        }
    });

    test('should test Add Color functionality on mobile viewport', async () => {
        // Set mobile viewport
        await page.setViewportSize({ width: 375, height: 667 });
        
        console.log('📱 Testing on mobile viewport...');
        
        // Login and navigate (similar to desktop test)
        await page.goto('https://dala3chic.com/merchant/login');
        await page.waitForLoadState('networkidle');
        
        // Navigate to product edit page
        await page.goto('https://dala3chic.com/merchant/products/11/edit');
        await page.waitForLoadState('networkidle');

        // Switch to colors tab
        await page.click('button[data-tab="colors"]');
        await page.waitForTimeout(1000);

        // Take mobile screenshot
        await page.screenshot({ 
            path: 'tests/screenshots/mobile-colors-tab.png',
            fullPage: true 
        });

        // Test Add Color button on mobile
        const addColorButton = page.locator('#add-color');
        await expect(addColorButton).toBeVisible();
        
        const initialCount = await page.locator('.color-item').count();
        await addColorButton.click();
        await page.waitForTimeout(2000);
        
        const finalCount = await page.locator('.color-item').count();
        
        // Take screenshot after mobile click
        await page.screenshot({ 
            path: 'tests/screenshots/mobile-after-add-color.png',
            fullPage: true 
        });

        console.log('📱 Mobile test - Initial count:', initialCount, 'Final count:', finalCount);
        expect(finalCount).toBe(initialCount + 1);
    });

    test.afterEach(async () => {
        await page.close();
    });
});
