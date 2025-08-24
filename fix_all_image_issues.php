<?php

// This script runs all the image fixes in the correct order

echo "=== FIXING ALL IMAGE ISSUES ===\n\n";

// 1. Run the storage link fix
echo "Step 1: Fixing storage link...\n";
include __DIR__ . '/fix_storage_link.php';
echo "\n";

// 2. Create placeholder images
echo "Step 2: Creating placeholder images...\n";
include __DIR__ . '/create_placeholder_images.php';
echo "\n";

// 3. Run the diagnostic script
echo "Step 3: Running diagnostics...\n";
include __DIR__ . '/diagnose_images.php';
echo "\n";

// 4. Run the direct fix script
echo "Step 4: Applying direct fixes...\n";
include __DIR__ . '/fix_product_images_direct.php';
echo "\n";

// 5. Run the diagnostics again to verify fixes
echo "Step 5: Verifying fixes...\n";
include __DIR__ . '/diagnose_images.php';
echo "\n";

echo "=== ALL FIXES COMPLETED ===\n";
echo "Please check your vendor dashboard now. Images should be displaying correctly.\n";
