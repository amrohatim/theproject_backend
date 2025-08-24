<?php

/**
 * Standalone test and execution of ServiceCategoriesSeeder
 * This script will test the directory structure and then run the seeder
 */

// Include Laravel bootstrap
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Category;
use Illuminate\Support\Facades\File;

echo "Service Categories Seeder Test & Execution\n";
echo "==========================================\n\n";

// Test 1: Directory Structure
echo "1. Testing Directory Structure\n";
echo "------------------------------\n";

$serviceImagePath = base_path('app service category images');
echo "Checking: {$serviceImagePath}\n";

if (!File::exists($serviceImagePath)) {
    echo "❌ ERROR: Service category images directory not found!\n";
    echo "Please ensure the 'app service category images' directory exists.\n";
    exit(1);
}

echo "✅ Directory found!\n\n";

$directories = File::directories($serviceImagePath);

if (empty($directories)) {
    echo "❌ ERROR: No subdirectories found!\n";
    exit(1);
}

echo "✅ Found " . count($directories) . " category directories:\n";

$totalParentImages = 0;
$totalChildCategories = 0;

foreach ($directories as $directory) {
    $categoryName = basename($directory);
    echo "  📁 {$categoryName}\n";
    
    $imageFiles = File::files($directory);
    $parentImageFound = false;
    $childCount = 0;
    
    foreach ($imageFiles as $imageFile) {
        $filename = $imageFile->getFilenameWithoutExtension();
        if ($filename === $categoryName) {
            $parentImageFound = true;
            $totalParentImages++;
        } else {
            $childCount++;
        }
    }
    
    echo "     📸 " . count($imageFiles) . " images (" . ($parentImageFound ? "✅" : "❌") . " parent, {$childCount} children)\n";
    $totalChildCategories += $childCount;
}

echo "\n📊 Summary:\n";
echo "   Categories: " . count($directories) . "\n";
echo "   Parent images: {$totalParentImages}\n";
echo "   Child categories: {$totalChildCategories}\n";
echo "   Total categories to create: " . (count($directories) + $totalChildCategories) . "\n\n";

// Test 2: Database Connection
echo "2. Testing Database Connection\n";
echo "------------------------------\n";

try {
    $currentServiceCategories = Category::where('type', 'service')->count();
    echo "✅ Database connection successful!\n";
    echo "   Current service categories: {$currentServiceCategories}\n\n";
} catch (Exception $e) {
    echo "❌ ERROR: Database connection failed!\n";
    echo "   Error: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 3: Run the Seeder
echo "3. Running Service Categories Seeder\n";
echo "------------------------------------\n";

try {
    // Create an instance of the seeder
    $seeder = new Database\Seeders\ServiceCategoriesSeeder();
    
    // Create a mock command object for output
    $command = new class {
        public function info($message) {
            echo "ℹ️  {$message}\n";
        }
        
        public function error($message) {
            echo "❌ {$message}\n";
        }
        
        public function confirm($question, $default = false) {
            echo "❓ {$question} ";
            echo $default ? "[Y/n]: " : "[y/N]: ";
            
            // For automated testing, return false (don't clear existing)
            echo "n (automated response)\n";
            return false;
        }
    };
    
    // Set the command property
    $reflection = new ReflectionClass($seeder);
    $commandProperty = $reflection->getProperty('command');
    $commandProperty->setAccessible(true);
    $commandProperty->setValue($seeder, $command);
    
    // Run the seeder
    echo "Starting seeder execution...\n\n";
    $seeder->run();
    
    echo "\n✅ Seeder execution completed!\n\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: Seeder execution failed!\n";
    echo "   Error: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    exit(1);
}

// Test 4: Verify Results
echo "4. Verifying Results\n";
echo "--------------------\n";

try {
    $newServiceCategories = Category::where('type', 'service')->count();
    $parentCategories = Category::where('type', 'service')->whereNull('parent_id')->count();
    $childCategories = Category::where('type', 'service')->whereNotNull('parent_id')->count();
    
    echo "✅ Verification completed!\n";
    echo "   Total service categories: {$newServiceCategories}\n";
    echo "   Parent categories: {$parentCategories}\n";
    echo "   Child categories: {$childCategories}\n\n";
    
    // Show some examples
    echo "📋 Sample Categories Created:\n";
    $sampleParents = Category::where('type', 'service')->whereNull('parent_id')->limit(3)->get();
    
    foreach ($sampleParents as $parent) {
        echo "   📁 {$parent->name} (ID: {$parent->id})\n";
        $children = Category::where('parent_id', $parent->id)->limit(3)->get();
        foreach ($children as $child) {
            echo "      └── {$child->name} (ID: {$child->id})\n";
        }
        if ($parent->children()->count() > 3) {
            echo "      └── ... and " . ($parent->children()->count() - 3) . " more\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: Verification failed!\n";
    echo "   Error: " . $e->getMessage() . "\n";
}

echo "\n🎉 Service Categories Seeder Test Completed Successfully!\n";
echo "\nYou can now:\n";
echo "1. Check the admin panel to see the new categories\n";
echo "2. Use these categories when creating services\n";
echo "3. Run the seeder again if needed (it will ask before clearing existing data)\n";
