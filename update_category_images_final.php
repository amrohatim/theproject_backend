<?php

/**
 * Category Image Path Update Script
 * 
 * This script updates category image paths from descriptive names to hash-based filenames
 * 
 * Usage: php update_category_images_final.php [--dry-run]
 */

// Check for dry-run flag
$dryRun = in_array('--dry-run', $argv);

echo "=== CATEGORY IMAGE PATH UPDATE SCRIPT ===\n";
echo "Mode: " . ($dryRun ? "DRY RUN (no changes will be made)" : "LIVE UPDATE") . "\n\n";

// Step 1: Check storage directory
$storageDir = __DIR__ . '/storage/app/public/categories';
echo "1. Checking storage directory: $storageDir\n";

if (!is_dir($storageDir)) {
    echo "   ERROR: Storage directory does not exist!\n";
    echo "   Please ensure the Laravel storage structure is set up correctly.\n";
    exit(1);
}

// Get all hash files from storage
$files = scandir($storageDir);
$hashFiles = [];

foreach ($files as $file) {
    if ($file !== '.' && $file !== '..' && is_file($storageDir . '/' . $file)) {
        $nameWithoutExt = pathinfo($file, PATHINFO_FILENAME);
        // Only include files that look like hash-based names (40+ characters)
        if (strlen($nameWithoutExt) >= 40 && preg_match('/^[a-zA-Z0-9]+$/', $nameWithoutExt)) {
            $hashFiles[] = [
                'filename' => $file,
                'hash' => $nameWithoutExt,
                'extension' => pathinfo($file, PATHINFO_EXTENSION),
                'size' => filesize($storageDir . '/' . $file)
            ];
        }
    }
}

echo "   Found " . count($hashFiles) . " hash-based image files\n";

if (count($hashFiles) === 0) {
    echo "   ERROR: No hash-based image files found in storage!\n";
    exit(1);
}

// Step 2: Connect to database
echo "\n2. Connecting to database...\n";

try {
    $host = '127.0.0.1';
    $dbname = 'marketplace_windsurf';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "   Database connection successful!\n";
    
} catch (PDOException $e) {
    echo "   ERROR: Database connection failed: " . $e->getMessage() . "\n";
    echo "   Please ensure MySQL is running and the database exists.\n";
    exit(1);
}

// Step 3: Get categories with images
echo "\n3. Analyzing current category image paths...\n";

try {
    $stmt = $pdo->query("SELECT id, name, image FROM categories WHERE image IS NOT NULL AND image != ''");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "   Found " . count($categories) . " categories with images\n";
    
} catch (PDOException $e) {
    echo "   ERROR: Failed to query categories: " . $e->getMessage() . "\n";
    exit(1);
}

// Step 4: Categorize current image paths
$descriptivePatterns = [];
$hashPatterns = [];
$otherPatterns = [];

foreach ($categories as $category) {
    $imagePath = $category['image'];
    
    // Check if already hash-based (no extension in path)
    if (preg_match('/\/images\/categories\/[a-zA-Z0-9]{40,}$/', $imagePath) || 
        preg_match('/\/storage\/categories\/[a-zA-Z0-9]{40,}$/', $imagePath)) {
        $hashPatterns[] = $category;
    } 
    // Check if descriptive name with extension
    elseif (preg_match('/\/images\/categories\/[^\/]+\.(jpg|jpeg|png|gif)$/i', $imagePath)) {
        $descriptivePatterns[] = $category;
    } 
    else {
        $otherPatterns[] = $category;
    }
}

echo "\n4. Pattern Analysis:\n";
echo "   - Descriptive names (need updating): " . count($descriptivePatterns) . "\n";
echo "   - Hash-based names (already correct): " . count($hashPatterns) . "\n";
echo "   - Other patterns: " . count($otherPatterns) . "\n";

// Step 5: Show what needs to be updated
if (!empty($descriptivePatterns)) {
    echo "\n5. Categories that will be updated:\n";
    foreach ($descriptivePatterns as $i => $category) {
        echo "   " . ($i + 1) . ". ID: {$category['id']} | {$category['name']} | {$category['image']}\n";
    }
} else {
    echo "\n5. No categories need updating - all are already using hash-based paths!\n";
    exit(0);
}

// Step 6: Confirmation (skip in dry-run mode)
if (!$dryRun) {
    echo "\n6. Confirmation:\n";
    echo "   This will update " . count($descriptivePatterns) . " category image paths.\n";
    echo "   Are you sure you want to proceed? (y/N): ";
    
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    fclose($handle);
    
    if (trim(strtolower($line)) !== 'y') {
        echo "   Operation cancelled.\n";
        exit(0);
    }
}

// Step 7: Perform updates
echo "\n" . ($dryRun ? "7. DRY RUN - Showing what would be updated:" : "7. Updating category image paths:") . "\n";

$updateCount = 0;
$errorCount = 0;

foreach ($descriptivePatterns as $i => $category) {
    if ($i >= count($hashFiles)) {
        echo "   WARNING: No more hash files available for category ID {$category['id']}\n";
        $errorCount++;
        continue;
    }
    
    $hashFile = $hashFiles[$i];
    $newPath = "/images/categories/{$hashFile['hash']}";
    
    echo "   Updating: {$category['name']} (ID: {$category['id']})\n";
    echo "     FROM: {$category['image']}\n";
    echo "     TO:   $newPath\n";
    
    if (!$dryRun) {
        try {
            $updateStmt = $pdo->prepare("UPDATE categories SET image = ? WHERE id = ?");
            $result = $updateStmt->execute([$newPath, $category['id']]);
            
            if ($result) {
                echo "     ✓ Updated successfully\n";
                $updateCount++;
            } else {
                echo "     ✗ Failed to update\n";
                $errorCount++;
            }
        } catch (PDOException $e) {
            echo "     ✗ Database error: " . $e->getMessage() . "\n";
            $errorCount++;
        }
    } else {
        echo "     → Would be updated\n";
        $updateCount++;
    }
    
    echo "\n";
}

// Step 8: Summary
echo "8. Summary:\n";
echo "   Total categories processed: " . count($descriptivePatterns) . "\n";
echo "   Successfully updated: $updateCount\n";
echo "   Errors: $errorCount\n";

if ($dryRun) {
    echo "\n   This was a dry run. To apply changes, run:\n";
    echo "   php update_category_images_final.php\n";
} else {
    echo "\n   Update complete!\n";
}

echo "\n=== SCRIPT FINISHED ===\n";
