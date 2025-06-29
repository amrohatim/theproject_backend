<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "Starting provider profiles fix script...\n";

// Check if the user_id column exists in provider_profiles
if (!Schema::hasColumn('provider_profiles', 'user_id')) {
    echo "Adding user_id column to provider_profiles table...\n";
    Schema::table('provider_profiles', function ($table) {
        $table->unsignedBigInteger('user_id')->nullable()->after('id');
        $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
    });
    echo "user_id column added successfully.\n";
}

// Check if provider_id is a self-reference
$selfReference = DB::select("
    SELECT * FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
    WHERE TABLE_NAME = 'provider_profiles' 
    AND COLUMN_NAME = 'provider_id' 
    AND REFERENCED_TABLE_NAME = 'provider_profiles'
");

if (!empty($selfReference)) {
    echo "Fixing provider_id self-reference...\n";
    
    // Drop the self-reference foreign key
    $constraintName = $selfReference[0]->CONSTRAINT_NAME;
    DB::statement("ALTER TABLE provider_profiles DROP FOREIGN KEY {$constraintName}");
    
    // Check if providers table exists
    if (Schema::hasTable('providers')) {
        // Add foreign key to providers table
        DB::statement("ALTER TABLE provider_profiles ADD CONSTRAINT provider_profiles_provider_id_foreign FOREIGN KEY (provider_id) REFERENCES providers(id) ON DELETE CASCADE");
        echo "provider_id now references providers table.\n";
    } else {
        echo "providers table does not exist, skipping foreign key creation.\n";
    }
}

// Create provider profiles for users who have a provider record but no provider profile
$providers = DB::table('providers')->get();
$count = 0;

foreach ($providers as $provider) {
    $existingProfile = DB::table('provider_profiles')
        ->where('user_id', $provider->user_id)
        ->first();

    if (!$existingProfile) {
        try {
            DB::table('provider_profiles')->insert([
                'user_id' => $provider->user_id,
                'provider_id' => $provider->id,
                'business_name' => $provider->business_name,
                'company_name' => $provider->business_name,
                'status' => 'active',
                'product_name' => 'Default Product',
                'stock' => 0,
                'price' => 0.00,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $count++;
            echo "Created provider profile for user ID {$provider->user_id}.\n";
        } catch (\Exception $e) {
            echo "Error creating provider profile for user ID {$provider->user_id}: {$e->getMessage()}\n";
        }
    }
}

echo "Created {$count} new provider profiles.\n";
echo "Fix script completed.\n";
