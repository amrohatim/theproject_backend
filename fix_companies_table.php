<?php

// Script to fix the companies table by adding missing columns
// Run with: php fix_companies_table.php

require __DIR__ . '/vendor/autoload.php';

// Load environment variables
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

try {
    echo "Fixing companies table...\n";
    
    // Check if the companies table exists
    if (Schema::hasTable('companies')) {
        echo "- companies table exists\n";
        
        // Check if business_type column exists
        if (!Schema::hasColumn('companies', 'business_type')) {
            echo "- Adding business_type column to companies table\n";
            Schema::table('companies', function (Blueprint $table) {
                $table->string('business_type')->nullable()->after('name');
            });
            echo "- business_type column added successfully\n";
        } else {
            echo "- business_type column already exists\n";
        }
        
        // Check if registration_number column exists
        if (!Schema::hasColumn('companies', 'registration_number')) {
            echo "- Adding registration_number column to companies table\n";
            Schema::table('companies', function (Blueprint $table) {
                $table->string('registration_number')->nullable()->after('business_type');
            });
            echo "- registration_number column added successfully\n";
        } else {
            echo "- registration_number column already exists\n";
        }
        
        // Check if can_deliver column exists
        if (!Schema::hasColumn('companies', 'can_deliver')) {
            echo "- Adding can_deliver column to companies table\n";
            Schema::table('companies', function (Blueprint $table) {
                $table->boolean('can_deliver')->default(true)->after('status');
            });
            echo "- can_deliver column added successfully\n";
        } else {
            echo "- can_deliver column already exists\n";
        }
        
        // Update the migrations table to mark the migrations as run
        $migrations = [
            '2023_07_02_000002_add_business_fields_to_companies_table',
            '2025_06_10_000001_add_can_deliver_to_companies_table'
        ];
        
        $maxBatch = DB::table('migrations')->max('batch') + 1;
        
        foreach ($migrations as $migration) {
            $exists = DB::table('migrations')->where('migration', $migration)->exists();
            
            if (!$exists) {
                DB::table('migrations')->insert([
                    'migration' => $migration,
                    'batch' => $maxBatch
                ]);
                echo "- Added migration record for {$migration}\n";
            } else {
                echo "- Migration record for {$migration} already exists\n";
            }
        }
        
        echo "Companies table fixed successfully!\n";
    } else {
        echo "- companies table does NOT exist\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " (Line: " . $e->getLine() . ")\n";
}
