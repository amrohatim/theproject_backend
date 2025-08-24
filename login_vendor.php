<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Vendor Login Script ===" . PHP_EOL;

// Get the vendor user who should own product 60
$vendor = \App\Models\User::find(96); // Luffy
if (!$vendor) {
    echo "❌ Vendor user 96 (Luffy) not found!" . PHP_EOL;
    exit;
}

echo "✅ Found vendor: {$vendor->name} (ID: {$vendor->id})" . PHP_EOL;
echo "- Email: {$vendor->email}" . PHP_EOL;

// Check if vendor has a password set
if (!$vendor->password) {
    echo "❌ Vendor has no password set!" . PHP_EOL;
    
    // Set a temporary password
    $vendor->password = \Illuminate\Support\Facades\Hash::make('password123');
    $vendor->save();
    echo "✅ Set temporary password: password123" . PHP_EOL;
}

echo PHP_EOL . "=== Login Instructions ===" . PHP_EOL;
echo "To test the vendor product edit functionality:" . PHP_EOL;
echo "1. Go to: http://localhost:8000/vendor/login" . PHP_EOL;
echo "2. Login with:" . PHP_EOL;
echo "   Email: {$vendor->email}" . PHP_EOL;
echo "   Password: password123" . PHP_EOL;
echo "3. Then navigate to: http://localhost:8000/vendor/products/60/edit" . PHP_EOL;

echo PHP_EOL . "=== Creating Test Route for Direct Login ===" . PHP_EOL;

// Create a simple test route file
$testRouteContent = '<?php

// Temporary test route for vendor login
Route::get(\'/test-vendor-login\', function () {
    $vendor = \App\Models\User::find(96);
    if ($vendor) {
        \Illuminate\Support\Facades\Auth::login($vendor);
        return redirect(\'/vendor/products/60/edit\')->with(\'success\', \'Logged in as \' . $vendor->name);
    }
    return \'Vendor not found\';
});
';

file_put_contents('test_route.php', $testRouteContent);
echo "✅ Created test_route.php" . PHP_EOL;
echo "You can also visit: http://localhost:8000/test-vendor-login" . PHP_EOL;
echo "This will automatically log you in as the vendor and redirect to the product edit page." . PHP_EOL;
