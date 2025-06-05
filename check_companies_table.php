<?php

// Script to check the structure of the companies table
// Run with: php check_companies_table.php

require __DIR__ . '/vendor/autoload.php';

// Load environment variables
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

try {
    echo "Checking companies table structure...\n";
    
    // Check if the companies table exists
    if (Schema::hasTable('companies')) {
        echo "- companies table exists\n";
        
        // Get the columns of the companies table
        $columns = Schema::getColumnListing('companies');
        
        echo "- Columns in the companies table:\n";
        foreach ($columns as $column) {
            echo "  - $column\n";
        }
        
        // Check if business_type column exists
        if (Schema::hasColumn('companies', 'business_type')) {
            echo "- business_type column exists\n";
        } else {
            echo "- business_type column does NOT exist\n";
            
            // Add the business_type column if it doesn't exist
            echo "- Adding business_type column to companies table...\n";
            Schema::table('companies', function ($table) {
                $table->string('business_type')->nullable()->after('name');
            });
            echo "- business_type column added successfully\n";
        }
        
        // Check if registration_number column exists
        if (Schema::hasColumn('companies', 'registration_number')) {
            echo "- registration_number column exists\n";
        } else {
            echo "- registration_number column does NOT exist\n";
            
            // Add the registration_number column if it doesn't exist
            echo "- Adding registration_number column to companies table...\n";
            Schema::table('companies', function ($table) {
                $table->string('registration_number')->nullable()->after('business_type');
            });
            echo "- registration_number column added successfully\n";
        }
    } else {
        echo "- companies table does NOT exist\n";
    }
    
    echo "Check completed.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " (Line: " . $e->getLine() . ")\n";
}
