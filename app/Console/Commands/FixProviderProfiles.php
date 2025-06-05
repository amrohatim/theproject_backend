<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Provider;
use App\Models\ProviderProfile;

class FixProviderProfiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:provider-profiles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix provider profiles for all provider users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting provider profiles fix...');

        // Get all provider users
        $providerUsers = User::where('role', 'provider')->get();
        $this->info('Found ' . count($providerUsers) . ' provider users.');

        $createdCount = 0;
        $existingCount = 0;

        foreach ($providerUsers as $user) {
            // Check if the user has a provider record
            $provider = Provider::where('user_id', $user->id)->first();
            
            if ($provider) {
                // Check if the user has a provider profile
                $providerProfile = ProviderProfile::where('user_id', $user->id)->first();
                
                if (!$providerProfile) {
                    try {
                        // Create a provider profile for this user
                        $providerProfile = new ProviderProfile([
                            'user_id' => $user->id,
                            'provider_id' => $provider->id,
                            'business_name' => $provider->business_name,
                            'company_name' => $provider->business_name,
                            'status' => 'active',
                            'product_name' => 'Default Product',
                            'stock' => 0,
                            'price' => 0.00,
                            'is_active' => 1
                        ]);
                        $providerProfile->save();
                        
                        $createdCount++;
                        $this->info("Created provider profile for user ID {$user->id} ({$user->name}).");
                    } catch (\Exception $e) {
                        $this->error("Error creating provider profile for user ID {$user->id}: {$e->getMessage()}");
                    }
                } else {
                    $existingCount++;
                    $this->info("Provider profile already exists for user ID {$user->id} ({$user->name}).");
                }
            } else {
                $this->warn("No provider record found for user ID {$user->id} ({$user->name}).");
            }
        }

        $this->info("Summary: Created {$createdCount} new provider profiles, {$existingCount} already existed.");
        $this->info('Fix completed.');
    }
}
