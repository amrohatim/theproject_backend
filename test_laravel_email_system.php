<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use App\Mail\EmailVerification;
use App\Models\User;

echo "🧪 Testing Laravel Email System\n";
echo "===============================\n\n";

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "📧 Test 1: Checking Laravel Mail Configuration...\n";

// Get current mail configuration
$mailDriver = config('mail.default');
$mailHost = config('mail.mailers.smtp.host');
$mailPort = config('mail.mailers.smtp.port');
$mailUsername = config('mail.mailers.smtp.username');
$mailFromAddress = config('mail.from.address');
$mailFromName = config('mail.from.name');

echo "✓ Mail Driver: {$mailDriver}\n";
echo "✓ SMTP Host: {$mailHost}\n";
echo "✓ SMTP Port: {$mailPort}\n";
echo "✓ SMTP Username: {$mailUsername}\n";
echo "✓ From Address: {$mailFromAddress}\n";
echo "✓ From Name: {$mailFromName}\n\n";

echo "📧 Test 2: Testing Laravel Mail Sending...\n";

try {
    // Create a test user object
    $testUser = new User();
    $testUser->id = 999;
    $testUser->name = 'Test User';
    $testUser->email = 'gogoh3296@gmail.com';
    
    $verificationCode = '123456';
    $userType = 'vendor';
    
    echo "Attempting to send email to: {$testUser->email}\n";
    echo "Verification code: {$verificationCode}\n";
    echo "User type: {$userType}\n\n";
    
    // Send email using Laravel's Mail facade
    Mail::to($testUser->email)->send(new EmailVerification($testUser, $verificationCode, $userType));
    
    echo "✅ Email sent successfully via Laravel Mail system!\n";
    echo "📬 Please check your email at {$testUser->email}\n\n";
    
} catch (\Exception $e) {
    echo "❌ Failed to send email via Laravel: " . $e->getMessage() . "\n";
    echo "📋 Error details: " . $e->getTraceAsString() . "\n\n";
}

echo "📧 Test 3: Testing Raw SMTP Connection...\n";

try {
    // Test raw SMTP connection
    $smtp = fsockopen($mailHost, $mailPort, $errno, $errstr, 30);
    
    if ($smtp) {
        echo "✅ SMTP connection successful to {$mailHost}:{$mailPort}\n";
        
        // Read server response
        $response = fgets($smtp, 512);
        echo "📤 Server response: " . trim($response) . "\n";
        
        fclose($smtp);
    } else {
        echo "❌ SMTP connection failed: {$errstr} ({$errno})\n";
    }
    
} catch (\Exception $e) {
    echo "❌ SMTP connection test failed: " . $e->getMessage() . "\n";
}

echo "\n📧 Test 4: Checking Email Template...\n";

try {
    // Check if email template exists
    $templatePath = resource_path('views/emails/email-verification.blade.php');
    
    if (file_exists($templatePath)) {
        echo "✅ Email template exists at: {$templatePath}\n";
        echo "📄 Template preview (first 200 chars):\n";
        echo substr(file_get_contents($templatePath), 0, 200) . "...\n\n";
    } else {
        echo "❌ Email template not found at: {$templatePath}\n\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Template check failed: " . $e->getMessage() . "\n\n";
}

echo "🎯 Summary:\n";
echo "- Laravel is configured to use: {$mailDriver}\n";
echo "- SMTP Host: {$mailHost}\n";
echo "- From Address: {$mailFromAddress}\n";
echo "- Check the logs for detailed error information\n";
