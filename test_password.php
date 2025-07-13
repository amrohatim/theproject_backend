<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "🔐 Testing Password for gogofifa56@gmail.com\n";
echo "============================================\n\n";

$email = 'gogofifa56@gmail.com';
$password = 'Fifa2021';

$user = User::where('email', $email)->first();

if (!$user) {
    echo "❌ User not found!\n";
    exit(1);
}

echo "✅ User found: {$user->name}\n";
echo "  - Email: {$user->email}\n";
echo "  - Role: {$user->role}\n";
echo "  - Stored password hash: " . substr($user->password, 0, 20) . "...\n";

echo "\n🔐 Testing password verification...\n";

// Test with Hash::check
$hashCheck = Hash::check($password, $user->password);
echo "  - Hash::check result: " . ($hashCheck ? 'SUCCESS' : 'FAILED') . "\n";

// Test with password_verify
$passwordVerify = password_verify($password, $user->password);
echo "  - password_verify result: " . ($passwordVerify ? 'SUCCESS' : 'FAILED') . "\n";

// Test different password variations
$variations = [
    'Fifa2021',
    'fifa2021',
    'FIFA2021',
    'password123',
    'password',
];

echo "\n🧪 Testing password variations...\n";
foreach ($variations as $testPassword) {
    $result = Hash::check($testPassword, $user->password);
    echo "  - '{$testPassword}': " . ($result ? 'SUCCESS ✅' : 'FAILED ❌') . "\n";
}

echo "\n✅ Password test completed!\n";
