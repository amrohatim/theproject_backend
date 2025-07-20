<?php

/**
 * Test script to verify vendor product creation validation fixes
 * Run with: php test_vendor_product_validation.php
 */

require_once 'vendor/autoload.php';

class VendorProductValidationTest
{
    private $baseUrl = 'http://localhost:8000';
    
    public function runTests()
    {
        echo "🧪 Testing Vendor Product Creation Validation Fixes\n";
        echo "=" . str_repeat("=", 50) . "\n\n";
        
        $this->testValidationRulesExist();
        $this->testErrorHandlingStructure();
        $this->testFormValidation();
        
        echo "\n✅ All validation tests completed!\n";
        echo "📋 Please run manual tests using vendor_product_creation_test.md\n";
    }
    
    private function testValidationRulesExist()
    {
        echo "1️⃣ Testing validation rules in ProductController...\n";
        
        $controllerPath = 'app/Http/Controllers/Vendor/ProductController.php';
        $content = file_get_contents($controllerPath);
        
        // Check if color_images validation is nullable
        if (strpos($content, "'color_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'") !== false) {
            echo "   ✅ color_images validation rule is correctly set to nullable\n";
        } else {
            echo "   ❌ color_images validation rule not found or incorrect\n";
        }
        
        // Check if custom validation logic exists
        if (strpos($content, 'Custom validation: Ensure each color has an image') !== false) {
            echo "   ✅ Custom color image validation logic exists\n";
        } else {
            echo "   ❌ Custom color image validation logic not found\n";
        }
        
        // Check if AJAX error handling exists
        if (strpos($content, 'expectsJson() || $request->ajax()') !== false) {
            echo "   ✅ AJAX error handling exists\n";
        } else {
            echo "   ❌ AJAX error handling not found\n";
        }
        
        echo "\n";
    }
    
    private function testErrorHandlingStructure()
    {
        echo "2️⃣ Testing frontend error handling structure...\n";
        
        $vuePath = 'resources/js/components/vendor/VendorProductCreateApp.vue';
        $content = file_get_contents($vuePath);
        
        // Check if clearErrors function exists
        if (strpos($content, 'const clearErrors = () => {') !== false) {
            echo "   ✅ clearErrors function exists\n";
        } else {
            echo "   ❌ clearErrors function not found\n";
        }
        
        // Check if error handling for validation errors exists
        if (strpos($content, 'Object.assign(errors, errorData.errors)') !== false) {
            echo "   ✅ Server validation error handling exists\n";
        } else {
            echo "   ❌ Server validation error handling not found\n";
        }
        
        // Check if errors are passed to color component
        if (strpos($content, ':errors="errors"') !== false) {
            echo "   ✅ Errors are passed to color component\n";
        } else {
            echo "   ❌ Errors not passed to color component\n";
        }
        
        echo "\n";
    }
    
    private function testFormValidation()
    {
        echo "3️⃣ Testing color component error display...\n";
        
        $colorComponentPath = 'resources/js/components/vendor/VendorColorVariantCard.vue';
        $content = file_get_contents($colorComponentPath);
        
        // Check if errors prop exists
        if (strpos($content, 'errors: {') !== false && strpos($content, 'type: Object') !== false) {
            echo "   ✅ Errors prop exists in color component\n";
        } else {
            echo "   ❌ Errors prop not found in color component\n";
        }
        
        // Check if color name error display exists
        if (strpos($content, 'errors[`colors.${index}.name`]') !== false) {
            echo "   ✅ Color name error display exists\n";
        } else {
            echo "   ❌ Color name error display not found\n";
        }
        
        // Check if color image error display exists
        if (strpos($content, 'errors[`color_images.${index}`]') !== false) {
            echo "   ✅ Color image error display exists\n";
        } else {
            echo "   ❌ Color image error display not found\n";
        }
        
        echo "\n";
    }
}

// Run the tests
$tester = new VendorProductValidationTest();
$tester->runTests();
