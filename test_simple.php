<?php
echo "PHP is working!\n";
echo "Current directory: " . __DIR__ . "\n";

$storageDir = __DIR__ . '/storage/app/public/categories';
echo "Storage directory: $storageDir\n";
echo "Storage directory exists: " . (is_dir($storageDir) ? "YES" : "NO") . "\n";

if (is_dir($storageDir)) {
    $files = scandir($storageDir);
    echo "Files in storage: " . (count($files) - 2) . "\n"; // -2 for . and ..
}
