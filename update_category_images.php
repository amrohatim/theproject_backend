<?php

// Direct database connection approach
echo "Starting category image path update script...\n";

// Check if storage directory exists first
$storageDir = __DIR__ . '/storage/app/public/categories';
echo "Checking storage directory: $storageDir\n";

if (!is_dir($storageDir)) {
    echo "ERROR: Storage directory does not exist: $storageDir\n";
    echo "Please ensure the storage directory structure is correct.\n";
    exit(1);
}

echo "Storage directory exists. Checking files...\n";

// Get all files in storage directory
$files = scandir($storageDir);
$hashFiles = [];

foreach ($files as $file) {
    if ($file !== '.' && $file !== '..' && is_file($storageDir . '/' . $file)) {
        $nameWithoutExt = pathinfo($file, PATHINFO_FILENAME);
        $hashFiles[] = [
            'filename' => $file,
            'hash' => $nameWithoutExt,
            'extension' => pathinfo($file, PATHINFO_EXTENSION)
        ];
    }
}

echo "Found " . count($hashFiles) . " files in storage directory\n";

// Show first few files as examples
echo "First 5 files:\n";
for ($i = 0; $i < min(5, count($hashFiles)); $i++) {
    echo "- {$hashFiles[$i]['filename']}\n";
}

try {
    // Database configuration
    $host = '127.0.0.1';
    $dbname = 'marketplace_windsurf';
    $username = 'root';
    $password = '';

    echo "Attempting to connect to database: $dbname\n";

    // Connect to database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Database connection successful!\n";

    echo "=== CATEGORY IMAGE PATH UPDATE SCRIPT ===\n\n";

    // Get all categories with images
    $stmt = $pdo->query("SELECT id, name, image FROM categories WHERE image IS NOT NULL");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Found " . count($categories) . " categories with images:\n\n";
    
    // Analyze current patterns
    $descriptivePatterns = [];
    $hashPatterns = [];
    $otherPatterns = [];
    
    foreach ($categories as $category) {
        $imagePath = $category['image'];
        
        // Check if it's already hash-based
        if (preg_match('/\/images\/categories\/[a-zA-Z0-9]{40,}$/', $imagePath) || 
            preg_match('/\/storage\/categories\/[a-zA-Z0-9]{40,}$/', $imagePath)) {
            $hashPatterns[] = $category;
        } elseif (preg_match('/\/images\/categories\/[a-zA-Z0-9\-&\(\),\s\.]+\.(jpg|jpeg|png|gif)$/i', $imagePath)) {
            $descriptivePatterns[] = $category;
        } else {
            $otherPatterns[] = $category;
        }
    }
    
    echo "=== PATTERN ANALYSIS ===\n";
    echo "Categories with descriptive names (need updating): " . count($descriptivePatterns) . "\n";
    echo "Categories with hash-based names (already correct): " . count($hashPatterns) . "\n";
    echo "Categories with other patterns: " . count($otherPatterns) . "\n\n";
    
    if (!empty($descriptivePatterns)) {
        echo "=== CATEGORIES NEEDING UPDATE ===\n";
        foreach ($descriptivePatterns as $category) {
            echo "ID: {$category['id']} | Name: {$category['name']} | Current: {$category['image']}\n";
        }
        echo "\n";
    }
    
    if (!empty($hashPatterns)) {
        echo "=== CATEGORIES ALREADY USING HASH-BASED NAMES ===\n";
        foreach (array_slice($hashPatterns, 0, 5) as $category) {
            echo "ID: {$category['id']} | Name: {$category['name']} | Current: {$category['image']}\n";
        }
        if (count($hashPatterns) > 5) {
            echo "... and " . (count($hashPatterns) - 5) . " more\n";
        }
        echo "\n";
    }
    
    // Show available hash files
    echo "=== AVAILABLE HASH FILES (first 10) ===\n";
    for ($i = 0; $i < min(10, count($hashFiles)); $i++) {
        echo "- {$hashFiles[$i]['filename']}\n";
    }
    if (count($hashFiles) > 10) {
        echo "... and " . (count($hashFiles) - 10) . " more files\n";
    }
    echo "\n";
    
    // Ask for confirmation to proceed
    echo "Do you want to proceed with updating the descriptive names to hash-based names? (y/N): ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    fclose($handle);
    
    if (trim(strtolower($line)) !== 'y') {
        echo "Operation cancelled.\n";
        exit(0);
    }
    
    // Proceed with updates
    echo "\n=== UPDATING CATEGORY IMAGE PATHS ===\n";
    
    $updateCount = 0;
    $hashIndex = 0;
    
    foreach ($descriptivePatterns as $category) {
        if ($hashIndex >= count($hashFiles)) {
            echo "WARNING: No more hash files available for category ID {$category['id']}\n";
            break;
        }
        
        $hashFile = $hashFiles[$hashIndex];
        $newPath = "/images/categories/{$hashFile['hash']}";
        
        echo "Updating category ID {$category['id']} ({$category['name']}):\n";
        echo "  FROM: {$category['image']}\n";
        echo "  TO:   $newPath\n";
        
        // Update the database
        $updateStmt = $pdo->prepare("UPDATE categories SET image = ? WHERE id = ?");
        $result = $updateStmt->execute([$newPath, $category['id']]);
        
        if ($result) {
            echo "  ✓ Updated successfully\n";
            $updateCount++;
        } else {
            echo "  ✗ Failed to update\n";
        }
        
        $hashIndex++;
        echo "\n";
    }
    
    echo "=== UPDATE COMPLETE ===\n";
    echo "Successfully updated $updateCount categories\n";
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
