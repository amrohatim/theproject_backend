<?php

/**
 * Verification Script for Category Image Path Updates
 * 
 * This script verifies that category image paths have been successfully updated
 * to use hash-based filenames without extensions.
 */

echo "=== CATEGORY IMAGE UPDATE VERIFICATION ===\n\n";

// Step 1: Connect to database
echo "1. Connecting to database...\n";

try {
    $host = '127.0.0.1';
    $dbname = 'marketplace_windsurf';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "   ✓ Database connection successful!\n";
    
} catch (PDOException $e) {
    echo "   ✗ Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Step 2: Get all categories with images
echo "\n2. Analyzing category image paths...\n";

try {
    $stmt = $pdo->query("SELECT id, name, image FROM categories WHERE image IS NOT NULL AND image != ''");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "   Found " . count($categories) . " categories with images\n";
    
} catch (PDOException $e) {
    echo "   ✗ Failed to query categories: " . $e->getMessage() . "\n";
    exit(1);
}

// Step 3: Categorize image paths
$hashBasedPaths = [];
$descriptivePaths = [];
$otherPaths = [];

foreach ($categories as $category) {
    $imagePath = $category['image'];
    
    // Check if hash-based (no extension, 40+ character hash)
    if (preg_match('/\/images\/categories\/[a-zA-Z0-9]{40,}$/', $imagePath)) {
        $hashBasedPaths[] = $category;
    }
    // Check if descriptive name with extension
    elseif (preg_match('/\/images\/categories\/[^\/]+\.(jpg|jpeg|png|gif)$/i', $imagePath)) {
        $descriptivePaths[] = $category;
    }
    else {
        $otherPaths[] = $category;
    }
}

// Step 4: Display results
echo "\n3. Verification Results:\n";
echo "   ✓ Hash-based paths (correct format): " . count($hashBasedPaths) . "\n";
echo "   ✗ Descriptive paths (still need updating): " . count($descriptivePaths) . "\n";
echo "   ? Other path formats: " . count($otherPaths) . "\n";

// Step 5: Show examples
if (!empty($hashBasedPaths)) {
    echo "\n4. Examples of correctly updated paths:\n";
    for ($i = 0; $i < min(5, count($hashBasedPaths)); $i++) {
        $cat = $hashBasedPaths[$i];
        echo "   ✓ {$cat['name']}: {$cat['image']}\n";
    }
}

if (!empty($descriptivePaths)) {
    echo "\n5. Categories still needing updates:\n";
    foreach ($descriptivePaths as $cat) {
        echo "   ✗ {$cat['name']}: {$cat['image']}\n";
    }
}

if (!empty($otherPaths)) {
    echo "\n6. Categories with other path formats:\n";
    foreach ($otherPaths as $cat) {
        echo "   ? {$cat['name']}: {$cat['image']}\n";
    }
}

// Step 6: Overall status
echo "\n7. Overall Status:\n";
if (count($descriptivePaths) === 0) {
    echo "   🎉 SUCCESS: All category images are using hash-based paths!\n";
    echo "   Total categories updated: " . count($hashBasedPaths) . "\n";
} else {
    echo "   ⚠️  INCOMPLETE: " . count($descriptivePaths) . " categories still need updating.\n";
    echo "   Please run the update script again.\n";
}

// Step 7: File existence check
echo "\n8. Checking if image files exist in storage...\n";
$storageDir = __DIR__ . '/storage/app/public/categories';
$missingFiles = 0;
$existingFiles = 0;

foreach ($hashBasedPaths as $category) {
    $imagePath = $category['image'];
    // Extract hash from path
    $hash = basename($imagePath);
    
    // Check for files with this hash (any extension)
    $found = false;
    $files = glob($storageDir . '/' . $hash . '.*');
    
    if (!empty($files)) {
        $existingFiles++;
        $found = true;
    } else {
        $missingFiles++;
        echo "   ✗ Missing file for {$category['name']}: $hash\n";
    }
}

echo "   ✓ Files found: $existingFiles\n";
echo "   ✗ Files missing: $missingFiles\n";

// Final summary
echo "\n=== VERIFICATION SUMMARY ===\n";
echo "Total categories: " . count($categories) . "\n";
echo "Hash-based paths: " . count($hashBasedPaths) . "\n";
echo "Descriptive paths: " . count($descriptivePaths) . "\n";
echo "Other paths: " . count($otherPaths) . "\n";
echo "Files existing: $existingFiles\n";
echo "Files missing: $missingFiles\n";

if (count($descriptivePaths) === 0 && $missingFiles === 0) {
    echo "\n🎉 VERIFICATION PASSED: All category images are properly configured!\n";
} else {
    echo "\n⚠️  VERIFICATION FAILED: Issues found that need attention.\n";
}

echo "\n=== VERIFICATION COMPLETE ===\n";
