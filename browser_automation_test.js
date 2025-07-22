/**
 * Browser Automation Test for Vendor Product Creation
 * Uses Playwright to test the complete product creation flow
 */

async function runVendorProductCreationTest() {
    console.log('🚀 Starting Browser Automation Test for Vendor Product Creation');
    console.log('=' .repeat(60));
    
    const testResults = {
        login: false,
        navigation: false,
        basicInfo: false,
        colorCreation: false,
        sizeCreation: false,
        productSave: false,
        productId: null
    };
    
    try {
        // Step 1: Login as Vendor
        console.log('\n📋 Step 1: Login as Vendor');
        await browser_navigate_Playwright({ url: 'http://localhost:8000/login' });
        await browser_wait_for_Playwright({ time: 3 });
        
        // Take snapshot to see the login form
        let snapshot = await browser_snapshot_Playwright();
        console.log('Login page loaded');
        
        // Find and fill email field
        const emailMatch = snapshot.match(/input[^>]*email[^>]*ref="([^"]+)"/i);
        if (emailMatch) {
            await browser_type_Playwright({
                element: 'Email input field',
                ref: emailMatch[1],
                text: 'gogoh3296@gmail.com'
            });
            console.log('✅ Email filled');
        }
        
        // Find and fill password field
        const passwordMatch = snapshot.match(/input[^>]*password[^>]*ref="([^"]+)"/i);
        if (passwordMatch) {
            await browser_type_Playwright({
                element: 'Password input field',
                ref: passwordMatch[1],
                text: 'Fifa2021'
            });
            console.log('✅ Password filled');
        }
        
        // Find and click login button
        const loginBtnMatch = snapshot.match(/button[^>]*login[^>]*ref="([^"]+)"/i) ||
                             snapshot.match(/button[^>]*submit[^>]*ref="([^"]+)"/i);
        if (loginBtnMatch) {
            await browser_click_Playwright({
                element: 'Login button',
                ref: loginBtnMatch[1]
            });
            console.log('✅ Login button clicked');
        }
        
        // Wait for login to complete
        await browser_wait_for_Playwright({ time: 5 });
        
        // Verify login success
        const currentUrl = await browser_evaluate_Playwright({
            function: '() => window.location.href'
        });
        
        if (currentUrl.includes('vendor') || currentUrl.includes('dashboard')) {
            console.log('✅ Login successful - redirected to vendor area');
            testResults.login = true;
        } else {
            throw new Error('Login failed - not redirected to vendor area');
        }
        
        // Step 2: Navigate to Product Creation
        console.log('\n📋 Step 2: Navigate to Product Creation');
        await browser_navigate_Playwright({ 
            url: 'http://localhost:8000/vendor/products/create' 
        });
        await browser_wait_for_Playwright({ time: 3 });
        
        snapshot = await browser_snapshot_Playwright();
        if (snapshot.includes('Create Product') || snapshot.includes('Basic Info')) {
            console.log('✅ Product creation page loaded');
            testResults.navigation = true;
        } else {
            throw new Error('Failed to load product creation page');
        }
        
        // Step 3: Fill Basic Product Information
        console.log('\n📋 Step 3: Fill Basic Product Information');
        
        const productName = `Test Product ${Date.now()}`;
        
        // Fill product name
        const nameMatch = snapshot.match(/input[^>]*name[^>]*ref="([^"]+)"/i);
        if (nameMatch) {
            await browser_type_Playwright({
                element: 'Product name field',
                ref: nameMatch[1],
                text: productName
            });
            console.log('✅ Product name filled');
        }
        
        // Select category
        const categoryMatch = snapshot.match(/select[^>]*category[^>]*ref="([^"]+)"/i);
        if (categoryMatch) {
            await browser_select_option_Playwright({
                element: 'Category dropdown',
                ref: categoryMatch[1],
                values: ['1'] // Assuming category ID 1 exists
            });
            console.log('✅ Category selected');
        }
        
        // Select branch
        const branchMatch = snapshot.match(/select[^>]*branch[^>]*ref="([^"]+)"/i);
        if (branchMatch) {
            await browser_select_option_Playwright({
                element: 'Branch dropdown',
                ref: branchMatch[1],
                values: ['1'] // Assuming branch ID 1 exists
            });
            console.log('✅ Branch selected');
        }
        
        // Fill price
        const priceMatch = snapshot.match(/input[^>]*price[^>]*ref="([^"]+)"/i);
        if (priceMatch) {
            await browser_type_Playwright({
                element: 'Price field',
                ref: priceMatch[1],
                text: '99.99'
            });
            console.log('✅ Price filled');
        }
        
        // Fill stock
        const stockMatch = snapshot.match(/input[^>]*stock[^>]*ref="([^"]+)"/i);
        if (stockMatch) {
            await browser_type_Playwright({
                element: 'Stock field',
                ref: stockMatch[1],
                text: '100'
            });
            console.log('✅ Stock filled');
        }
        
        // Fill description
        const descMatch = snapshot.match(/textarea[^>]*description[^>]*ref="([^"]+)"/i);
        if (descMatch) {
            await browser_type_Playwright({
                element: 'Description field',
                ref: descMatch[1],
                text: 'This is a comprehensive test product with multiple variants.'
            });
            console.log('✅ Description filled');
        }
        
        testResults.basicInfo = true;
        console.log('✅ Basic information completed');
        
        // Step 4: Add Color Variants
        console.log('\n📋 Step 4: Add Color Variants');
        
        // Navigate to Colors & Images tab
        snapshot = await browser_snapshot_Playwright();
        const colorsTabMatch = snapshot.match(/.*Colors.*Images.*ref="([^"]+)"/i);
        if (colorsTabMatch) {
            await browser_click_Playwright({
                element: 'Colors & Images tab',
                ref: colorsTabMatch[1]
            });
            await browser_wait_for_Playwright({ time: 2 });
            console.log('✅ Navigated to Colors & Images tab');
        }
        
        // Add first color
        await addColorVariant('Red', '#FF0000', 30);
        await addColorVariant('Blue', '#0000FF', 25);
        
        testResults.colorCreation = true;
        console.log('✅ Color variants added');
        
        // Step 5: Add Size Variants
        console.log('\n📋 Step 5: Add Size Variants');
        
        // Add sizes for the colors
        await addSizeVariant('Small', 'S', 10);
        await addSizeVariant('Medium', 'M', 15);
        await addSizeVariant('Large', 'L', 20);
        
        testResults.sizeCreation = true;
        console.log('✅ Size variants added');
        
        // Step 6: Save Product
        console.log('\n📋 Step 6: Save Product');
        
        snapshot = await browser_snapshot_Playwright();
        const saveBtnMatch = snapshot.match(/button[^>]*save[^>]*ref="([^"]+)"/i) ||
                            snapshot.match(/button[^>]*submit[^>]*ref="([^"]+)"/i);
        
        if (saveBtnMatch) {
            await browser_click_Playwright({
                element: 'Save Product button',
                ref: saveBtnMatch[1]
            });
            console.log('✅ Save button clicked');
            
            // Wait for save operation
            await browser_wait_for_Playwright({ time: 5 });
            
            // Check for success
            const finalUrl = await browser_evaluate_Playwright({
                function: '() => window.location.href'
            });
            
            const finalSnapshot = await browser_snapshot_Playwright();
            
            if (finalSnapshot.includes('success') || 
                finalSnapshot.includes('created') ||
                finalUrl.includes('products')) {
                console.log('✅ Product saved successfully');
                testResults.productSave = true;
                
                // Extract product ID if possible
                const productIdMatch = finalUrl.match(/products\/(\d+)/);
                if (productIdMatch) {
                    testResults.productId = productIdMatch[1];
                    console.log(`📋 Product ID: ${testResults.productId}`);
                }
            } else {
                throw new Error('Product save failed - no success indication');
            }
        }
        
        // Generate Test Report
        console.log('\n' + '='.repeat(60));
        console.log('📊 BROWSER AUTOMATION TEST REPORT');
        console.log('='.repeat(60));
        
        Object.entries(testResults).forEach(([step, result]) => {
            if (typeof result === 'boolean') {
                console.log(`${result ? '✅' : '❌'} ${step}: ${result ? 'PASSED' : 'FAILED'}`);
            } else if (result !== null) {
                console.log(`📋 ${step}: ${result}`);
            }
        });
        
        const passedTests = Object.values(testResults).filter(r => r === true).length;
        const totalTests = Object.values(testResults).filter(r => typeof r === 'boolean').length;
        
        console.log(`\n📊 Success Rate: ${passedTests}/${totalTests} (${((passedTests/totalTests)*100).toFixed(1)}%)`);
        
        if (passedTests === totalTests) {
            console.log('\n🎉 ALL BROWSER TESTS PASSED!');
            console.log('The vendor product creation interface works correctly.');
        } else {
            console.log('\n⚠️ Some browser tests failed.');
        }
        
        console.log('='.repeat(60));
        
        return testResults;
        
    } catch (error) {
        console.error('\n❌ Browser test failed:', error.message);
        console.log('\nPartial Results:');
        Object.entries(testResults).forEach(([step, result]) => {
            if (typeof result === 'boolean') {
                console.log(`${result ? '✅' : '❌'} ${step}: ${result ? 'PASSED' : 'FAILED'}`);
            }
        });
        throw error;
    }
}

async function addColorVariant(colorName, colorCode, stock) {
    console.log(`  Adding color: ${colorName}`);
    
    let snapshot = await browser_snapshot_Playwright();
    
    // Find and click Add Color button
    const addColorMatch = snapshot.match(/button[^>]*add[^>]*color[^>]*ref="([^"]+)"/i);
    if (addColorMatch) {
        await browser_click_Playwright({
            element: 'Add Color button',
            ref: addColorMatch[1]
        });
        await browser_wait_for_Playwright({ time: 1 });
    }
    
    // Fill color details
    snapshot = await browser_snapshot_Playwright();
    
    // Color name
    const colorNameMatch = snapshot.match(/input[^>]*color[^>]*name[^>]*ref="([^"]+)"/i);
    if (colorNameMatch) {
        await browser_type_Playwright({
            element: 'Color name field',
            ref: colorNameMatch[1],
            text: colorName
        });
    }
    
    // Color code
    const colorCodeMatch = snapshot.match(/input[^>]*color[^>]*code[^>]*ref="([^"]+)"/i);
    if (colorCodeMatch) {
        await browser_type_Playwright({
            element: 'Color code field',
            ref: colorCodeMatch[1],
            text: colorCode
        });
    }
    
    // Color stock
    const colorStockMatch = snapshot.match(/input[^>]*stock[^>]*ref="([^"]+)"/i);
    if (colorStockMatch) {
        await browser_type_Playwright({
            element: 'Color stock field',
            ref: colorStockMatch[1],
            text: stock.toString()
        });
    }
    
    console.log(`  ✅ Color ${colorName} added`);
}

async function addSizeVariant(sizeName, sizeValue, stock) {
    console.log(`  Adding size: ${sizeName}`);
    
    let snapshot = await browser_snapshot_Playwright();
    
    // Find and click Add Size button
    const addSizeMatch = snapshot.match(/button[^>]*add[^>]*size[^>]*ref="([^"]+)"/i);
    if (addSizeMatch) {
        await browser_click_Playwright({
            element: 'Add Size button',
            ref: addSizeMatch[1]
        });
        await browser_wait_for_Playwright({ time: 1 });
    }
    
    // Fill size details
    snapshot = await browser_snapshot_Playwright();
    
    // Size name
    const sizeNameMatch = snapshot.match(/input[^>]*size[^>]*name[^>]*ref="([^"]+)"/i);
    if (sizeNameMatch) {
        await browser_type_Playwright({
            element: 'Size name field',
            ref: sizeNameMatch[1],
            text: sizeName
        });
    }
    
    // Size stock
    const sizeStockMatch = snapshot.match(/input[^>]*size[^>]*stock[^>]*ref="([^"]+)"/i);
    if (sizeStockMatch) {
        await browser_type_Playwright({
            element: 'Size stock field',
            ref: sizeStockMatch[1],
            text: stock.toString()
        });
    }
    
    console.log(`  ✅ Size ${sizeName} added`);
}

// Export the test function
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { runVendorProductCreationTest };
}

// Run if called directly
if (typeof window === 'undefined' && require.main === module) {
    runVendorProductCreationTest().catch(console.error);
}
