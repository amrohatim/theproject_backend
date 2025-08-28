<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;

// Find admin user
$admin = User::where('email', 'admin@example.com')->first();

if (!$admin) {
    echo "❌ Admin user not found!\n";
    exit(1);
}

echo "✅ Admin user found: {$admin->name} (Role: {$admin->role})\n";

// Login the admin user
Auth::login($admin);

echo "✅ Admin user logged in successfully\n";
echo "🔗 You can now visit: http://127.0.0.1:8000/admin/business-types\n";
echo "🔗 Or admin dashboard: http://127.0.0.1:8000/admin/dashboard\n";
