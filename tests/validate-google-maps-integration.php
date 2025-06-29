<?php

/**
 * Google Maps Integration Validation Script
 * 
 * This script validates that the Google Maps integration is properly configured
 * and that all necessary components are in place.
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🗺️  Google Maps Integration Validation\n";
echo "=====================================\n\n";

// Test 1: Check Google Maps API Key Configuration
echo "1. Checking Google Maps API Key Configuration...\n";
$apiKey = config('googlemaps.api_key');
if ($apiKey) {
    echo "   ✅ API Key configured: " . substr($apiKey, 0, 10) . "...\n";
} else {
    echo "   ❌ API Key not configured\n";
    exit(1);
}

// Test 2: Check Google Maps Config File
echo "\n2. Checking Google Maps Config File...\n";
$configPath = config_path('googlemaps.php');
if (file_exists($configPath)) {
    echo "   ✅ Config file exists: $configPath\n";
    
    $config = config('googlemaps');
    echo "   ✅ Default center: " . $config['default_center']['lat'] . ", " . $config['default_center']['lng'] . "\n";
    echo "   ✅ Libraries: " . implode(', ', $config['libraries']) . "\n";
} else {
    echo "   ❌ Config file not found\n";
    exit(1);
}

// Test 3: Check Merchant Registration View
echo "\n3. Checking Merchant Registration View...\n";
$viewPath = resource_path('views/auth/merchant-register.blade.php');
if (file_exists($viewPath)) {
    echo "   ✅ View file exists: $viewPath\n";
    
    $viewContent = file_get_contents($viewPath);
    
    // Check for Google Maps integration elements
    $checks = [
        'location-search' => 'Location search input',
        'google-map' => 'Google Maps container',
        'initGoogleMaps' => 'Google Maps initialization function',
        'maps.googleapis.com' => 'Google Maps API script',
        'store_location_lat' => 'Latitude field',
        'store_location_lng' => 'Longitude field',
        'store_location_address' => 'Address field',
        'clearLocation' => 'Clear location function',
        'autocomplete' => 'Places autocomplete',
    ];
    
    foreach ($checks as $needle => $description) {
        if (strpos($viewContent, $needle) !== false) {
            echo "   ✅ $description found\n";
        } else {
            echo "   ❌ $description not found\n";
        }
    }
} else {
    echo "   ❌ View file not found\n";
    exit(1);
}

// Test 4: Check Database Schema
echo "\n4. Checking Database Schema...\n";
try {
    $connection = DB::connection();
    
    // Check if merchants table has location fields
    $columns = DB::select("SHOW COLUMNS FROM merchants LIKE 'store_location_%'");
    
    $expectedColumns = ['store_location_lat', 'store_location_lng', 'store_location_address'];
    $foundColumns = array_column($columns, 'Field');
    
    foreach ($expectedColumns as $column) {
        if (in_array($column, $foundColumns)) {
            echo "   ✅ Column '$column' exists\n";
        } else {
            echo "   ❌ Column '$column' missing\n";
        }
    }
} catch (Exception $e) {
    echo "   ❌ Database connection failed: " . $e->getMessage() . "\n";
}

// Test 5: Check Routes
echo "\n5. Checking Routes...\n";
$routes = [
    'register.merchant' => 'GET /register/merchant',
    'register.merchant.submit' => 'POST /register/merchant',
];

foreach ($routes as $routeName => $description) {
    try {
        $route = route($routeName);
        echo "   ✅ Route '$routeName' exists: $route\n";
    } catch (Exception $e) {
        echo "   ❌ Route '$routeName' not found\n";
    }
}

// Test 6: Check Controller Methods
echo "\n6. Checking Controller Methods...\n";
$controllerPath = app_path('Http/Controllers/Web/RegistrationController.php');
if (file_exists($controllerPath)) {
    $controllerContent = file_get_contents($controllerPath);
    
    $methods = [
        'showMerchantRegistration' => 'Show merchant registration form',
        'registerMerchant' => 'Handle merchant registration',
    ];
    
    foreach ($methods as $method => $description) {
        if (strpos($controllerContent, "function $method") !== false) {
            echo "   ✅ Method '$method' exists\n";
        } else {
            echo "   ❌ Method '$method' not found\n";
        }
    }
} else {
    echo "   ❌ Controller file not found\n";
}

// Test 7: Validate API Key Format
echo "\n7. Validating API Key Format...\n";
if (preg_match('/^AIza[0-9A-Za-z_-]{35}$/', $apiKey)) {
    echo "   ✅ API Key format is valid\n";
} else {
    echo "   ⚠️  API Key format may be invalid (should start with 'AIza' and be 39 characters)\n";
}

echo "\n🎉 Google Maps Integration Validation Complete!\n";
echo "===============================================\n";

echo "\nNext Steps:\n";
echo "1. Test the registration form manually in a browser\n";
echo "2. Run the automated test suite: ./tests/run-merchant-registration-tests.sh\n";
echo "3. Verify Google Maps functionality with real user interactions\n";
echo "4. Check browser console for any JavaScript errors\n";
echo "5. Test on mobile devices for responsive design\n";
