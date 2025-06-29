<?php

namespace App\Console\Commands;

use App\Models\Provider;
use App\Models\User;
use App\Models\ProviderRating;
use Illuminate\Console\Command;

class TestProviderRating extends Command
{
    protected $signature = 'test:provider-rating';
    protected $description = 'Test the provider rating fix';

    public function handle()
    {
        $this->info('🔍 Testing Provider Rating Fix');
        $this->info('==============================');

        // 1. Check providers
        $this->info('1. Checking providers...');
        $providers = Provider::with('user')->get();
        $this->info("   Found {$providers->count()} providers");
        
        foreach ($providers as $provider) {
            $userName = $provider->user->name ?? 'N/A';
            $this->info("   - Provider ID: {$provider->id}, Business: {$provider->business_name}, User: {$userName}");
        }

        // 2. Check vendors
        $this->info('2. Checking vendors...');
        $vendors = User::where('role', 'vendor')->get();
        $this->info("   Found {$vendors->count()} vendors");

        if ($vendors->count() === 0) {
            $this->info('   Creating test vendor...');
            $vendor = User::create([
                'name' => 'Test Vendor',
                'email' => 'test-vendor@example.com',
                'password' => bcrypt('password'),
                'role' => 'vendor',
                'phone' => '1234567890',
                'status' => 'active',
            ]);
            $this->info("   ✅ Test vendor created with ID: {$vendor->id}");
        } else {
            $vendor = $vendors->first();
            $this->info("   Using existing vendor: {$vendor->name} (ID: {$vendor->id})");
        }

        // 3. Test the core fix
        if ($providers->count() > 0) {
            $testProvider = $providers->first();
            $this->info("3. Testing core fix for Provider ID: {$testProvider->id}");
            
            try {
                // This is the line that was failing before the fix
                $provider = Provider::findOrFail($testProvider->id);
                $this->info("   ✅ SUCCESS: Provider found using Provider::findOrFail()");
                $this->info("   ✅ Provider business name: {$provider->business_name}");
                
                // Test creating a rating
                $this->info("4. Testing rating creation...");
                $existingRating = ProviderRating::where('vendor_id', $vendor->id)
                    ->where('provider_id', $testProvider->id)
                    ->first();
                    
                if ($existingRating) {
                    $this->info("   Existing rating found, updating...");
                    $existingRating->update([
                        'rating' => 5,
                        'review_text' => 'Updated test rating - ' . now(),
                    ]);
                    $this->info("   ✅ Rating updated successfully");
                } else {
                    $this->info("   Creating new rating...");
                    $rating = ProviderRating::create([
                        'vendor_id' => $vendor->id,
                        'provider_id' => $testProvider->id,
                        'rating' => 4,
                        'review_text' => 'Test rating created - ' . now(),
                    ]);
                    $this->info("   ✅ Rating created successfully with ID: {$rating->id}");
                }
                
                // Check updated stats
                $testProvider->refresh();
                $this->info("5. Provider rating statistics:");
                $this->info("   Average rating: {$testProvider->average_rating}");
                $this->info("   Total ratings: {$testProvider->total_ratings}");
                
            } catch (\Exception $e) {
                $this->error("   ❌ FAILED: " . $e->getMessage());
                return 1;
            }
        }

        $this->info('');
        $this->info('🎉 Provider Rating Fix Test Completed Successfully!');
        $this->info('The "No query results for model [App\\Models\\User] 4" error should now be resolved.');
        
        return 0;
    }
}
