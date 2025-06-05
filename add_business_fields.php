<?php

// Script to add business_type and registration_number columns to the companies table
// Run with: php add_business_fields.php

require __DIR__ . '/vendor/autoload.php';

// Load environment variables
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    echo "Adding business fields to companies table...\n";
    
    // Check if the companies table exists
    if (Schema::hasTable('companies')) {
        echo "- companies table exists\n";
        
        // Check if business_type column exists
        if (!Schema::hasColumn('companies', 'business_type')) {
            // Add business_type column
            DB::statement('ALTER TABLE companies ADD COLUMN business_type VARCHAR(255) NULL AFTER name');
            echo "- business_type column added\n";
        } else {
            echo "- business_type column already exists\n";
        }
        
        // Check if registration_number column exists
        if (!Schema::hasColumn('companies', 'registration_number')) {
            // Add registration_number column
            DB::statement('ALTER TABLE companies ADD COLUMN registration_number VARCHAR(255) NULL AFTER business_type');
            echo "- registration_number column added\n";
        } else {
            echo "- registration_number column already exists\n";
        }
        
        // Update the migrations table to mark the migration as run
        $migration = '2023_07_02_000002_add_business_fields_to_companies_table';
        $exists = DB::table('migrations')->where('migration', $migration)->exists();
        
        if (!$exists) {
            $maxBatch = DB::table('migrations')->max('batch') + 1;
            DB::table('migrations')->insert([
                'migration' => $migration,
                'batch' => $maxBatch
            ]);
            echo "- Added migration record for {$migration}\n";
        } else {
            echo "- Migration record for {$migration} already exists\n";
        }
        
        echo "Business fields added successfully!\n";
    } else {
        echo "- companies table does NOT exist\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " (Line: " . $e->getLine() . ")\n";
}
