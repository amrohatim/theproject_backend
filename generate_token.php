<?php

// This script generates a bearer token for API testing

// Include the autoloader
require __DIR__ . '/vendor/autoload.php';

// Initialize Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Auth;
use App\Models\User;

echo "Generating bearer token for API testing\n\n";

// Find a user to authenticate
$user = User::first();

if (!$user) {
    echo "No users found in the database. Please create a user first.\n";
    exit(1);
}

// Create a token
$token = $user->createToken('api-test-token')->plainTextToken;

echo "User: {$user->name} (ID: {$user->id})\n";
echo "Email: {$user->email}\n";
echo "Role: {$user->role}\n\n";

echo "Bearer Token:\n{$token}\n\n";

// Display usage instructions
echo "Usage Instructions:\n";
echo "1. Copy the bearer token above\n";
echo "2. Use it in your API requests with the Authorization header:\n";
echo "   Authorization: Bearer {token}\n\n";

echo "Example curl command:\n";
echo "curl -X GET http://localhost:8000/api/products/38/colors \\\n";
echo "  -H \"Accept: application/json\" \\\n";
echo "  -H \"Authorization: Bearer {$token}\"\n\n";

echo "Example URL for the test page:\n";
echo "http://localhost:8000/test_colors_api.html?token={$token}&id=38\n\n";

echo "Direct API test URL:\n";
echo "http://localhost:8000/test_colors_api.php?token={$token}&id=38\n";
