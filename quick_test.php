<?php

// Quick test to check basic functionality
echo "Quick Test\n";
echo "==========\n";

// Test 1: Check if directory exists
$dir = __DIR__ . '/app service category images';
echo "Directory exists: " . (is_dir($dir) ? "YES" : "NO") . "\n";

if (is_dir($dir)) {
    $subdirs = array_filter(glob($dir . '/*'), 'is_dir');
    echo "Subdirectories found: " . count($subdirs) . "\n";
    
    foreach ($subdirs as $subdir) {
        echo "  - " . basename($subdir) . "\n";
    }
}

// Test 2: Check if seeder file exists
$seederFile = __DIR__ . '/database/seeders/ServiceCategoriesSeeder.php';
echo "\nSeeder file exists: " . (file_exists($seederFile) ? "YES" : "NO") . "\n";

// Test 3: Basic PHP syntax check
if (file_exists($seederFile)) {
    $output = [];
    $return_var = 0;
    exec("php -l {$seederFile}", $output, $return_var);
    echo "Syntax check: " . ($return_var === 0 ? "PASSED" : "FAILED") . "\n";
    if ($return_var !== 0) {
        echo "Error: " . implode("\n", $output) . "\n";
    }
}

echo "\nTest completed.\n";
