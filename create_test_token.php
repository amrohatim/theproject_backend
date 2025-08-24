<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\User;
use Illuminate\Support\Facades\Artisan;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Creating test user and getting token...\n";

try {
    // Create or get test user
    $user = User::where('email', 'test@example.com')->first();
    if (!$user) {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
            'phone' => '1234567890',
            'address' => 'Test Address',
            'lat' => 25.2048,
            'lng' => 55.2419,
        ]);
        echo "Created new test user\n";
    } else {
        echo "Using existing test user: {$user->name}\n";
    }

    // Create token
    try {
        $token = $user->createToken('test_token')->plainTextToken;
        echo "✅ Token created successfully!\n";
        echo "Token: $token\n";
        echo "\nYou can now test the API with this token:\n";
        echo "curl -X GET \"http://localhost:8000/api/branches/nearby?latitude=25.2048&longitude=55.2419&radius=50&limit=10\" \\\n";
        echo "  -H \"Accept: application/json\" \\\n";
        echo "  -H \"Authorization: Bearer $token\"\n";
    } catch (Exception $e) {
        echo "❌ Error creating token: " . $e->getMessage() . "\n";
        echo "This might be because personal_access_tokens table doesn't exist.\n";
        echo "Let's try to create it...\n";
        
        try {
            // Run Sanctum migrations
            Artisan::call('migrate', ['--path' => 'vendor/laravel/sanctum/database/migrations']);
            echo "✅ Sanctum migrations run successfully\n";
            
            // Try creating token again
            $token = $user->createToken('test_token')->plainTextToken;
            echo "✅ Token created after migration!\n";
            echo "Token: $token\n";
            echo "\nYou can now test the API with this token:\n";
            echo "curl -X GET \"http://localhost:8000/api/branches/nearby?latitude=25.2048&longitude=55.2419&radius=50&limit=10\" \\\n";
            echo "  -H \"Accept: application/json\" \\\n";
            echo "  -H \"Authorization: Bearer $token\"\n";
        } catch (Exception $e2) {
            echo "❌ Still failed after migration: " . $e2->getMessage() . "\n";
            echo "You may need to run: php artisan migrate\n";
        }
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

?>
