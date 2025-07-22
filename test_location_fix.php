<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Http\Controllers\Provider\LocationController;
use App\Models\User;
use App\Models\Provider;
use App\Models\ProviderLocation;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing Provider Location Fix ===\n\n";

try {
    // Find or create a test user and provider
    $user = User::where('email', 'test@provider.com')->first();
    if (!$user) {
        $user = User::create([
            'name' => 'Test Provider',
            'email' => 'test@provider.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
        echo "✅ Created test user\n";
    } else {
        echo "✅ Found existing test user\n";
    }

    $provider = $user->provider;
    if (!$provider) {
        $provider = Provider::create([
            'user_id' => $user->id,
            'business_name' => 'Test Business',
            'status' => 'active',
            'is_verified' => false
        ]);
        echo "✅ Created test provider\n";
    } else {
        echo "✅ Found existing test provider\n";
    }

    // Clear existing locations for clean test
    ProviderLocation::where('provider_id', $provider->id)->delete();
    echo "✅ Cleared existing locations\n";

    // Create an initial location
    $existingLocation = ProviderLocation::create([
        'provider_id' => $provider->id,
        'label' => 'Initial Location',
        'emirate' => 'Dubai',
        'latitude' => 25.2048,
        'longitude' => 55.2708,
    ]);
    echo "✅ Created initial location with ID: {$existingLocation->id}\n";

    // Test the controller with mixed data (existing + new locations)
    $controller = new LocationController();

    // Simulate authentication
    \Illuminate\Support\Facades\Auth::setUser($user);
    
    // Create request data that includes the existing location (with ID) and a new location (without ID)
    $requestData = [
        'locations' => [
            [
                'id' => $existingLocation->id,
                'label' => 'Updated Initial Location',
                'emirate' => 'Dubai',
                'latitude' => 25.2048,
                'longitude' => 55.2708,
            ],
            [
                'id' => null,
                'label' => 'New Location',
                'emirate' => 'Abu Dhabi',
                'latitude' => 24.4539,
                'longitude' => 54.3773,
            ]
        ]
    ];

    $request = new Request($requestData);
    
    echo "\n📋 Testing the store method with mixed data...\n";
    echo "   - Existing location ID: {$existingLocation->id}\n";
    echo "   - New location: no ID (should create new)\n";

    // Count locations before
    $countBefore = ProviderLocation::where('provider_id', $provider->id)->count();
    echo "   - Locations before: $countBefore\n";

    // Call the store method
    $response = $controller->store($request);
    $responseData = json_decode($response->getContent(), true);

    // Count locations after
    $countAfter = ProviderLocation::where('provider_id', $provider->id)->count();
    echo "   - Locations after: $countAfter\n";

    if ($responseData['success']) {
        echo "✅ Store method succeeded\n";
        
        // Check if we have exactly 2 locations (no duplicates)
        if ($countAfter == 2) {
            echo "✅ Correct number of locations (2) - no duplicates created!\n";
            
            // Check if the existing location was updated
            $updatedLocation = ProviderLocation::find($existingLocation->id);
            if ($updatedLocation && $updatedLocation->label == 'Updated Initial Location') {
                echo "✅ Existing location was updated correctly\n";
            } else {
                echo "❌ Existing location was not updated correctly\n";
            }
            
            // Check if the new location was created
            $newLocation = ProviderLocation::where('label', 'New Location')->first();
            if ($newLocation && $newLocation->emirate == 'Abu Dhabi') {
                echo "✅ New location was created correctly\n";
            } else {
                echo "❌ New location was not created correctly\n";
            }
            
        } else {
            echo "❌ Wrong number of locations! Expected 2, got $countAfter\n";
            if ($countAfter > 2) {
                echo "   This indicates the bug still exists - duplicates were created\n";
            }
        }
    } else {
        echo "❌ Store method failed: " . ($responseData['message'] ?? 'Unknown error') . "\n";
    }

    // Show all locations for debugging
    echo "\n📋 All locations for provider {$provider->id}:\n";
    $allLocations = ProviderLocation::where('provider_id', $provider->id)->get();
    foreach ($allLocations as $location) {
        echo "   - ID: {$location->id}, Label: '{$location->label}', Emirate: {$location->emirate}\n";
    }

    echo "\n=== Test completed ===\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
