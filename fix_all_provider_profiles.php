<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "Starting provider profiles fix script for all users...\n";

// Get all provider users
$providerUsers = DB::table('users')->where('role', 'provider')->get();
echo "Found " . count($providerUsers) . " provider users.\n";

$createdCount = 0;
$existingCount = 0;

foreach ($providerUsers as $user) {
    // Check if the user has a provider profile
    $providerProfile = DB::table('provider_profiles')->where('user_id', $user->id)->first();

    if (!$providerProfile) {
        // Check if the user has a provider record
        $provider = DB::table('providers')->where('user_id', $user->id)->first();

        if (!$provider) {
            try {
                // Create a provider record first
                $providerId = DB::table('providers')->insertGetId([
                    'user_id' => $user->id,
                    'business_name' => "{$user->name}'s Business",
                    'status' => 'active',
                    'is_verified' => false,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                echo "Created provider record for user ID {$user->id} ({$user->name}).\n";
                $provider = DB::table('providers')->where('id', $providerId)->first();
            } catch (\Exception $e) {
                echo "Error creating provider record for user ID {$user->id}: {$e->getMessage()}\n";
                continue; // Skip to next user if provider creation fails
            }
        }

        try {
            // Create a provider profile for this user
            DB::table('provider_profiles')->insert([
                'user_id' => $user->id,
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
            $createdCount++;
            echo "Created provider profile for user ID {$user->id} ({$user->name}).\n";
        } catch (\Exception $e) {
            echo "Error creating provider profile for user ID {$user->id}: {$e->getMessage()}\n";
        }
    } else {
        $existingCount++;
        echo "Provider profile already exists for user ID {$user->id} ({$user->name}).\n";
    }
}

echo "Summary: Created {$createdCount} new provider profiles, {$existingCount} already existed.\n";
echo "Fix script completed.\n";
