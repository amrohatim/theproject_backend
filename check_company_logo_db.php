<?php

// Bootstrap Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\DB;

echo "🔍 Checking company logo in database...\n";

// Find the user
$user = User::where('email', 'gogofifa56@gmail.com')->first();

if (!$user) {
    echo "❌ User not found!\n";
    exit(1);
}

echo "✅ User found: {$user->name} (ID: {$user->id})\n";

// Find the company
$company = Company::where('user_id', $user->id)->first();

if (!$company) {
    echo "❌ Company not found!\n";
    exit(1);
}

echo "✅ Company found: {$company->name} (ID: {$company->id})\n";
echo "📄 Raw logo field in database: " . ($company->getRawOriginal('logo') ?? 'NULL') . "\n";
echo "🔗 Logo accessor result: " . ($company->logo ?? 'NULL') . "\n";

// Check if there are any files that might belong to this company
echo "\n🔍 Checking storage files...\n";
$storageDir = storage_path('app/public/companies');
if (is_dir($storageDir)) {
    $files = scandir($storageDir);
    $files = array_filter($files, function($file) {
        return !in_array($file, ['.', '..']);
    });
    
    echo "📁 Files in storage/app/public/companies:\n";
    foreach ($files as $file) {
        $filePath = $storageDir . '/' . $file;
        $fileSize = filesize($filePath);
        $fileDate = date('Y-m-d H:i:s', filemtime($filePath));
        echo "  - {$file} ({$fileSize} bytes, modified: {$fileDate})\n";
    }
} else {
    echo "❌ Storage directory does not exist\n";
}

// Check public storage symlink
echo "\n🔍 Checking public storage symlink...\n";
$publicStorageDir = public_path('storage/companies');
if (is_dir($publicStorageDir)) {
    $files = scandir($publicStorageDir);
    $files = array_filter($files, function($file) {
        return !in_array($file, ['.', '..']);
    });
    
    echo "📁 Files in public/storage/companies:\n";
    foreach ($files as $file) {
        echo "  - {$file}\n";
    }
} else {
    echo "❌ Public storage directory does not exist\n";
}

echo "\n✅ Check completed!\n";