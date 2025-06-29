<?php

// Script to manually run the product specification migration

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Get the migration file
$migrationFile = __DIR__ . '/database/migrations/2025_08_01_000001_create_product_specification_tables.php';

if (!file_exists($migrationFile)) {
    echo "Migration file not found: $migrationFile\n";
    exit(1);
}

// Load the migration class
$migration = require $migrationFile;

// Run the migration
try {
    echo "Running migration...\n";
    $migration->up();
    echo "Migration completed successfully.\n";
} catch (\Exception $e) {
    echo "Error running migration: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    exit(1);
}

echo "Done!\n";
