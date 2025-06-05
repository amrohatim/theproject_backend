<?php

/**
 * Production Environment Validation Script
 * 
 * This script validates your production configuration
 */

require_once 'vendor/autoload.php';

echo "🔍 Validating Production Configuration...\n\n";

$errors = [];
$warnings = [];
$success = [];

// Load environment
if (file_exists('.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
} else {
    $errors[] = ".env file not found";
}

// 1. Check APP_URL
echo "1. Checking APP_URL configuration...\n";
$appUrl = $_ENV['APP_URL'] ?? '';
if ($appUrl === 'http://82.25.109.98') {
    $success[] = "APP_URL correctly set to production IP";
    echo "   ✅ APP_URL: $appUrl\n";
} else {
    $errors[] = "APP_URL should be set to http://82.25.109.98, currently: $appUrl";
    echo "   ❌ APP_URL: $appUrl\n";
}

// 2. Check APP_ENV
echo "\n2. Checking APP_ENV...\n";
$appEnv = $_ENV['APP_ENV'] ?? '';
if ($appEnv === 'production') {
    $success[] = "APP_ENV correctly set to production";
    echo "   ✅ APP_ENV: $appEnv\n";
} else {
    $warnings[] = "APP_ENV should be 'production' for production deployment, currently: $appEnv";
    echo "   ⚠️  APP_ENV: $appEnv\n";
}

// 3. Check APP_DEBUG
echo "\n3. Checking APP_DEBUG...\n";
$appDebug = $_ENV['APP_DEBUG'] ?? '';
if ($appDebug === 'false' || $appDebug === false) {
    $success[] = "APP_DEBUG correctly disabled";
    echo "   ✅ APP_DEBUG: disabled\n";
} else {
    $errors[] = "APP_DEBUG should be false in production";
    echo "   ❌ APP_DEBUG: enabled (SECURITY RISK!)\n";
}

// 4. Check APP_KEY
echo "\n4. Checking APP_KEY...\n";
$appKey = $_ENV['APP_KEY'] ?? '';
if (!empty($appKey) && strlen($appKey) > 10) {
    $success[] = "APP_KEY is set";
    echo "   ✅ APP_KEY: configured\n";
} else {
    $errors[] = "APP_KEY is not set or too short";
    echo "   ❌ APP_KEY: not configured\n";
}

// 5. Check Database Configuration
echo "\n5. Checking database configuration...\n";
$dbConnection = $_ENV['DB_CONNECTION'] ?? '';
$dbHost = $_ENV['DB_HOST'] ?? '';
$dbDatabase = $_ENV['DB_DATABASE'] ?? '';
$dbUsername = $_ENV['DB_USERNAME'] ?? '';

if (!empty($dbConnection) && !empty($dbHost) && !empty($dbDatabase) && !empty($dbUsername)) {
    $success[] = "Database configuration appears complete";
    echo "   ✅ Database: $dbConnection on $dbHost\n";
} else {
    $warnings[] = "Database configuration may be incomplete";
    echo "   ⚠️  Database: configuration incomplete\n";
}

// 6. Check FILESYSTEM_DISK
echo "\n6. Checking filesystem configuration...\n";
$filesystemDisk = $_ENV['FILESYSTEM_DISK'] ?? '';
if ($filesystemDisk === 'public') {
    $success[] = "Filesystem disk correctly set to public";
    echo "   ✅ FILESYSTEM_DISK: $filesystemDisk\n";
} else {
    $warnings[] = "FILESYSTEM_DISK should be 'public' for proper asset serving";
    echo "   ⚠️  FILESYSTEM_DISK: $filesystemDisk\n";
}

// 7. Check Aramex Configuration
echo "\n7. Checking Aramex shipping configuration...\n";
$aramexAccount = $_ENV['ARAMEX_ACCOUNT_NUMBER'] ?? '';
$aramexUsername = $_ENV['ARAMEX_USERNAME'] ?? '';
$aramexShipperName = $_ENV['ARAMEX_SHIPPER_NAME'] ?? '';

if (!empty($aramexAccount) && !empty($aramexUsername) && !empty($aramexShipperName)) {
    $success[] = "Aramex configuration appears complete";
    echo "   ✅ Aramex: configuration found\n";
} else {
    $warnings[] = "Aramex shipping configuration may be incomplete";
    echo "   ⚠️  Aramex: configuration incomplete\n";
}

// 8. Check file permissions
echo "\n8. Checking file permissions...\n";
$storageWritable = is_writable('storage');
$bootstrapWritable = is_writable('bootstrap/cache');

if ($storageWritable && $bootstrapWritable) {
    $success[] = "File permissions are correct";
    echo "   ✅ Permissions: storage and bootstrap/cache are writable\n";
} else {
    $errors[] = "File permissions need to be fixed";
    echo "   ❌ Permissions: storage or bootstrap/cache not writable\n";
}

// 9. Check storage link
echo "\n9. Checking storage link...\n";
if (file_exists('public/storage')) {
    $success[] = "Storage link exists";
    echo "   ✅ Storage link: exists\n";
} else {
    $warnings[] = "Storage link not found - run 'php artisan storage:link'";
    echo "   ⚠️  Storage link: not found\n";
}

// 10. Check products directory
echo "\n10. Checking products directory...\n";
if (file_exists('public/products')) {
    $success[] = "Products directory exists";
    echo "   ✅ Products directory: exists\n";
} else {
    $warnings[] = "Products directory not found";
    echo "   ⚠️  Products directory: not found\n";
}

// Display summary
echo "\n" . str_repeat("=", 60) . "\n";
echo "📊 VALIDATION SUMMARY\n";
echo str_repeat("=", 60) . "\n\n";

echo "✅ SUCCESS (" . count($success) . "):\n";
foreach ($success as $item) {
    echo "   • $item\n";
}

if (!empty($warnings)) {
    echo "\n⚠️  WARNINGS (" . count($warnings) . "):\n";
    foreach ($warnings as $item) {
        echo "   • $item\n";
    }
}

if (!empty($errors)) {
    echo "\n❌ ERRORS (" . count($errors) . "):\n";
    foreach ($errors as $item) {
        echo "   • $item\n";
    }
}

echo "\n" . str_repeat("=", 60) . "\n";

if (empty($errors)) {
    echo "🎉 VALIDATION PASSED! Your application is ready for production.\n";
    echo "🚀 You can now deploy to http://82.25.109.98\n";
} else {
    echo "❌ VALIDATION FAILED! Please fix the errors above before deploying.\n";
}

echo str_repeat("=", 60) . "\n";
