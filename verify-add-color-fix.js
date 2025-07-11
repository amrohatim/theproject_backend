/**
 * Add Color Functionality Verification Script
 * Run this in the browser console on the merchant product edit page to verify the fix
 */

(function() {
    'use strict';
    
    console.log('🧪 Starting Add Color Functionality Verification...');
    
    // Test 1: Check if browser compatibility polyfills are loaded
    function testBrowserCompatibility() {
        console.log('\n📋 Test 1: Browser Compatibility');
        
        const tests = [
            { name: 'Element.matches', test: () => typeof Element.prototype.matches === 'function' },
            { name: 'Element.closest', test: () => typeof Element.prototype.closest === 'function' },
            { name: 'Array.from', test: () => typeof Array.from === 'function' },
            { name: 'Object.assign', test: () => typeof Object.assign === 'function' },
            { name: 'String.includes', test: () => typeof String.prototype.includes === 'function' },
            { name: 'CustomEvent', test: () => typeof window.CustomEvent === 'function' },
            { name: 'safeMatches helper', test: () => typeof window.safeMatches === 'function' },
            { name: 'safeClosest helper', test: () => typeof window.safeClosest === 'function' }
        ];
        
        tests.forEach(test => {
            const result = test.test();
            console.log(`${result ? '✅' : '❌'} ${test.name}: ${result ? 'Available' : 'Missing'}`);
        });
    }
    
    // Test 2: Check if external libraries are loaded correctly
    function testLibraryLoading() {
        console.log('\n📋 Test 2: External Library Loading');
        
        const libraries = [
            { name: 'Enhanced Color Selection', test: () => window.enhancedColorSelection && typeof window.enhancedColorSelection === 'object' },
            { name: 'Dynamic Color Size Manager', test: () => window.dynamicColorSizeManager && typeof window.dynamicColorSizeManager === 'object' },
            { name: 'Coloris (Color Picker)', test: () => typeof window.Coloris !== 'undefined' }
        ];
        
        libraries.forEach(lib => {
            const result = lib.test();
            console.log(`${result ? '✅' : '❌'} ${lib.name}: ${result ? 'Loaded' : 'Not loaded'}`);
        });
    }
    
    // Test 3: Check DOM elements
    function testDOMElements() {
        console.log('\n📋 Test 3: DOM Elements');
        
        const elements = [
            { name: 'Add Color Button', selector: '#add-color' },
            { name: 'Colors Container', selector: '#colors-container' },
            { name: 'Colors Tab', selector: '[data-tab="colors"]' },
            { name: 'Existing Color Items', selector: '.color-item' }
        ];
        
        elements.forEach(element => {
            const el = document.querySelector(element.selector);
            const exists = !!el;
            const visible = exists && el.offsetParent !== null;
            console.log(`${exists ? '✅' : '❌'} ${element.name}: ${exists ? (visible ? 'Visible' : 'Hidden') : 'Not found'}`);
        });
    }
    
    // Test 4: Test Add Color button functionality
    function testAddColorButton() {
        console.log('\n📋 Test 4: Add Color Button Functionality');
        
        const addColorButton = document.getElementById('add-color');
        if (!addColorButton) {
            console.log('❌ Add Color button not found');
            return;
        }
        
        // Check if button has event listener attached
        const hasListener = addColorButton.hasAttribute('data-listener-attached');
        console.log(`${hasListener ? '✅' : '❌'} Event listener attached: ${hasListener}`);
        
        // Check if colors container exists
        const colorsContainer = document.getElementById('colors-container');
        if (!colorsContainer) {
            console.log('❌ Colors container not found');
            return;
        }
        
        // Count existing color items
        const initialColorItems = colorsContainer.querySelectorAll('.color-item').length;
        console.log(`📊 Initial color items: ${initialColorItems}`);
        
        // Test button click (simulate)
        try {
            console.log('🖱️ Simulating button click...');
            addColorButton.click();
            
            // Wait a moment for DOM changes
            setTimeout(() => {
                const finalColorItems = colorsContainer.querySelectorAll('.color-item').length;
                console.log(`📊 Final color items: ${finalColorItems}`);
                
                if (finalColorItems > initialColorItems) {
                    console.log('✅ Add Color functionality working correctly!');
                } else {
                    console.log('❌ Add Color functionality not working');
                }
            }, 1000);
            
        } catch (error) {
            console.log('❌ Error clicking Add Color button:', error);
        }
    }
    
    // Test 5: Check for JavaScript errors
    function testJavaScriptErrors() {
        console.log('\n📋 Test 5: JavaScript Error Monitoring');
        
        // Store original console.error
        const originalError = console.error;
        const errors = [];
        
        // Override console.error to capture errors
        console.error = function(...args) {
            errors.push(args.join(' '));
            originalError.apply(console, args);
        };
        
        // Listen for unhandled errors
        const errorHandler = (event) => {
            errors.push(`Unhandled error: ${event.error ? event.error.message : event.message}`);
        };
        
        window.addEventListener('error', errorHandler);
        
        // Test for 5 seconds
        setTimeout(() => {
            window.removeEventListener('error', errorHandler);
            console.error = originalError;
            
            if (errors.length === 0) {
                console.log('✅ No JavaScript errors detected');
            } else {
                console.log(`❌ ${errors.length} JavaScript errors detected:`);
                errors.forEach(error => console.log(`   - ${error}`));
            }
        }, 5000);
        
        console.log('⏱️ Monitoring for JavaScript errors for 5 seconds...');
    }
    
    // Test 6: Test responsive behavior
    function testResponsiveBehavior() {
        console.log('\n📋 Test 6: Responsive Behavior');
        
        const viewports = [
            { name: 'Mobile', width: 375 },
            { name: 'Tablet', width: 768 },
            { name: 'Desktop', width: 1200 }
        ];
        
        const currentWidth = window.innerWidth;
        console.log(`📐 Current viewport width: ${currentWidth}px`);
        
        viewports.forEach(viewport => {
            const isCurrentViewport = Math.abs(currentWidth - viewport.width) < 100;
            console.log(`${isCurrentViewport ? '📱' : '📏'} ${viewport.name} (${viewport.width}px): ${isCurrentViewport ? 'Current' : 'Not current'}`);
        });
        
        // Test button visibility at current viewport
        const addColorButton = document.getElementById('add-color');
        if (addColorButton) {
            const rect = addColorButton.getBoundingClientRect();
            const isVisible = rect.width > 0 && rect.height > 0;
            console.log(`${isVisible ? '✅' : '❌'} Add Color button visible at current viewport: ${isVisible}`);
        }
    }
    
    // Run all tests
    function runAllTests() {
        console.log('🚀 Running comprehensive Add Color functionality tests...\n');
        
        testBrowserCompatibility();
        testLibraryLoading();
        testDOMElements();
        testAddColorButton();
        testJavaScriptErrors();
        testResponsiveBehavior();
        
        console.log('\n🎉 Verification complete! Check the results above.');
        console.log('💡 If any tests fail, please check the browser console for additional error messages.');
    }
    
    // Auto-run tests after a short delay
    setTimeout(runAllTests, 1000);
    
    // Make functions available globally for manual testing
    window.addColorVerification = {
        runAllTests,
        testBrowserCompatibility,
        testLibraryLoading,
        testDOMElements,
        testAddColorButton,
        testJavaScriptErrors,
        testResponsiveBehavior
    };
    
    console.log('🔧 Add Color verification script loaded. Tests will run automatically in 1 second.');
    console.log('💡 You can also run individual tests manually using window.addColorVerification.testName()');
    
})();
