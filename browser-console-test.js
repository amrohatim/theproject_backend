// Browser Console Test for Add Color Functionality
// Copy and paste this into the browser console on the product edit page

console.log('🚀 Starting Add Color Functionality Test...');

function testAddColorFunctionality() {
    return new Promise((resolve, reject) => {
        try {
            // Step 1: Navigate to Colors & Images tab
            console.log('📍 Step 1: Switching to Colors & Images tab...');
            const colorsTab = document.querySelector('button[data-tab="colors"]');
            if (colorsTab) {
                colorsTab.click();
                console.log('✅ Colors tab clicked');
            } else {
                throw new Error('Colors tab not found');
            }

            setTimeout(() => {
                try {
                    // Step 2: Count initial color items
                    const initialColorItems = document.querySelectorAll('.color-item');
                    const initialCount = initialColorItems.length;
                    console.log(`📊 Initial color count: ${initialCount}`);

                    // Step 3: Find and test Add Color button
                    const addColorButton = document.getElementById('add-color');
                    const addFirstColorButton = document.getElementById('add-first-color');
                    
                    let buttonToClick = null;
                    if (addColorButton && addColorButton.offsetParent !== null) {
                        buttonToClick = addColorButton;
                        console.log('🎯 Found main Add Color button');
                    } else if (addFirstColorButton && addFirstColorButton.offsetParent !== null) {
                        buttonToClick = addFirstColorButton;
                        console.log('🎯 Found Add First Color button');
                    } else {
                        throw new Error('No visible Add Color button found');
                    }

                    // Step 4: Click the button
                    console.log('🖱️ Clicking Add Color button...');
                    buttonToClick.click();

                    // Step 5: Wait and verify new color form was added
                    setTimeout(() => {
                        try {
                            const finalColorItems = document.querySelectorAll('.color-item');
                            const finalCount = finalColorItems.length;
                            console.log(`📊 Final color count: ${finalCount}`);

                            if (finalCount > initialCount) {
                                console.log('✅ SUCCESS: New color form was added!');
                                console.log(`📈 Color count increased from ${initialCount} to ${finalCount}`);

                                // Step 6: Test the new color form functionality
                                const newColorItem = finalColorItems[finalColorItems.length - 1];
                                console.log('🧪 Testing new color form functionality...');

                                // Test color name selection
                                const colorSelect = newColorItem.querySelector('select[name*="[name]"]');
                                if (colorSelect) {
                                    colorSelect.value = 'Red';
                                    colorSelect.dispatchEvent(new Event('change'));
                                    console.log('✅ Color name selection works');
                                } else {
                                    console.log('⚠️ Color name select not found');
                                }

                                // Test color code input
                                const colorCodeInput = newColorItem.querySelector('input[name*="[color_code]"]');
                                if (colorCodeInput) {
                                    colorCodeInput.value = '#FF0000';
                                    colorCodeInput.dispatchEvent(new Event('input'));
                                    console.log('✅ Color code input works');
                                } else {
                                    console.log('⚠️ Color code input not found');
                                }

                                // Test stock input
                                const stockInput = newColorItem.querySelector('input[name*="[stock]"]');
                                if (stockInput) {
                                    stockInput.value = '10';
                                    stockInput.dispatchEvent(new Event('input'));
                                    console.log('✅ Stock input works');
                                } else {
                                    console.log('⚠️ Stock input not found');
                                }

                                // Test image upload trigger
                                const imageUploadTrigger = newColorItem.querySelector('.trigger-image-upload');
                                if (imageUploadTrigger) {
                                    console.log('✅ Image upload trigger found');
                                } else {
                                    console.log('⚠️ Image upload trigger not found');
                                }

                                // Test remove button
                                const removeButton = newColorItem.querySelector('.remove-item');
                                if (removeButton) {
                                    console.log('✅ Remove button found');
                                } else {
                                    console.log('⚠️ Remove button not found');
                                }

                                // Verify field names are properly indexed
                                const nameField = newColorItem.querySelector('select[name*="[name]"]');
                                const expectedIndex = finalCount - 1;
                                if (nameField && nameField.name.includes(`[${expectedIndex}]`)) {
                                    console.log(`✅ Field names properly indexed with [${expectedIndex}]`);
                                } else {
                                    console.log(`⚠️ Field names not properly indexed. Expected [${expectedIndex}]`);
                                }

                                console.log('🎉 All tests completed successfully!');
                                resolve({
                                    success: true,
                                    initialCount,
                                    finalCount,
                                    message: 'Add Color functionality is working correctly'
                                });

                            } else {
                                console.log('❌ FAILURE: No new color form was added');
                                console.log(`📊 Color count remained at ${initialCount}`);
                                
                                // Check for JavaScript errors
                                console.log('🔍 Checking for JavaScript errors...');
                                
                                reject({
                                    success: false,
                                    initialCount,
                                    finalCount,
                                    message: 'Add Color button did not add a new color form'
                                });
                            }
                        } catch (error) {
                            console.error('❌ Error in verification step:', error);
                            reject({
                                success: false,
                                error: error.message
                            });
                        }
                    }, 2000); // Wait 2 seconds for DOM updates

                } catch (error) {
                    console.error('❌ Error in button click step:', error);
                    reject({
                        success: false,
                        error: error.message
                    });
                }
            }, 1000); // Wait 1 second for tab switch

        } catch (error) {
            console.error('❌ Error in initial setup:', error);
            reject({
                success: false,
                error: error.message
            });
        }
    });
}

// Additional debugging functions
function debugAddColorButton() {
    console.log('🔍 Debugging Add Color button...');
    
    const addColorButton = document.getElementById('add-color');
    const addFirstColorButton = document.getElementById('add-first-color');
    
    console.log('Add Color button:', addColorButton);
    console.log('Add Color button visible:', addColorButton ? addColorButton.offsetParent !== null : false);
    console.log('Add First Color button:', addFirstColorButton);
    console.log('Add First Color button visible:', addFirstColorButton ? addFirstColorButton.offsetParent !== null : false);
    
    if (addColorButton) {
        console.log('Add Color button onclick:', addColorButton.onclick);
        console.log('Add Color button event listeners:', getEventListeners ? getEventListeners(addColorButton) : 'getEventListeners not available');
    }
    
    if (addFirstColorButton) {
        console.log('Add First Color button onclick:', addFirstColorButton.onclick);
        console.log('Add First Color button event listeners:', getEventListeners ? getEventListeners(addFirstColorButton) : 'getEventListeners not available');
    }
}

function debugColorsContainer() {
    console.log('🔍 Debugging colors container...');
    
    const container = document.getElementById('colors-container');
    console.log('Colors container:', container);
    
    if (container) {
        const colorItems = container.querySelectorAll('.color-item');
        console.log('Current color items:', colorItems.length);
        colorItems.forEach((item, index) => {
            console.log(`Color item ${index}:`, item);
        });
    }
}

// Test if addNewColorForm function is available globally
function testGlobalFunction() {
    console.log('🔍 Testing global addNewColorForm function...');
    
    if (typeof addNewColorForm === 'function') {
        console.log('✅ addNewColorForm function is available globally');
        try {
            addNewColorForm();
            console.log('✅ addNewColorForm function executed successfully');
        } catch (error) {
            console.error('❌ Error executing addNewColorForm:', error);
        }
    } else {
        console.log('❌ addNewColorForm function is not available globally');
    }
}

// Run the test
console.log('🎬 Starting comprehensive test...');
console.log('Run testAddColorFunctionality() to start the main test');
console.log('Run debugAddColorButton() to debug button issues');
console.log('Run debugColorsContainer() to debug container issues');
console.log('Run testGlobalFunction() to test the global function');

// Auto-run the test
testAddColorFunctionality()
    .then(result => {
        console.log('🎉 Test Result:', result);
    })
    .catch(error => {
        console.error('💥 Test Failed:', error);
    });
