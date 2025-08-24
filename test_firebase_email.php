<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Log;
use App\Providers\FirebaseServiceProvider;

echo "🧪 Testing Firebase Email Verification\n";
echo "======================================\n\n";

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "📧 Test 1: Checking Firebase Configuration...\n";

// Check if Firebase service account file exists
$serviceAccountPath = base_path('dala3chic-e2b81-firebase-adminsdk-fbsvc-e5c52a715e.json');
echo "Service Account Path: {$serviceAccountPath}\n";

if (file_exists($serviceAccountPath)) {
    echo "✅ Firebase service account file exists\n";
    
    // Check if file is readable and valid JSON
    $content = file_get_contents($serviceAccountPath);
    $json = json_decode($content, true);
    
    if ($json && isset($json['project_id'])) {
        echo "✅ Service account file is valid JSON\n";
        echo "✓ Project ID: " . $json['project_id'] . "\n";
        echo "✓ Client Email: " . $json['client_email'] . "\n";
    } else {
        echo "❌ Service account file is not valid JSON\n";
    }
} else {
    echo "❌ Firebase service account file not found\n";
}

echo "\n📧 Test 2: Testing Firebase Service Provider...\n";

try {
    $isConfigured = FirebaseServiceProvider::isConfigured();
    echo "Firebase configured: " . ($isConfigured ? "✅ Yes" : "❌ No") . "\n";
    
    $auth = FirebaseServiceProvider::getAuth();
    echo "Firebase Auth instance: " . ($auth ? "✅ Available" : "❌ Not available") . "\n";
    
} catch (\Exception $e) {
    echo "❌ Firebase Service Provider error: " . $e->getMessage() . "\n";
}

echo "\n📧 Test 3: Testing Firebase Email Verification...\n";

try {
    // Test email for verification
    $testEmail = 'gogoh3296@gmail.com';
    $testPassword = 'TestPassword123!';
    
    echo "Attempting to create Firebase user for: {$testEmail}\n";
    
    $auth = FirebaseServiceProvider::getAuth();
    
    if (!$auth) {
        echo "❌ Firebase Auth not available\n";
    } else {
        echo "✅ Firebase Auth is available\n";
        
        // Try to create a user (or get existing user)
        try {
            $userProperties = [
                'email' => $testEmail,
                'password' => $testPassword,
                'emailVerified' => false,
            ];
            
            // First try to get existing user
            try {
                $existingUser = $auth->getUserByEmail($testEmail);
                echo "✅ User already exists in Firebase: " . $existingUser->uid . "\n";
                $uid = $existingUser->uid;
            } catch (\Kreait\Firebase\Exception\Auth\UserNotFound $e) {
                // User doesn't exist, create new one
                echo "Creating new Firebase user...\n";
                $createdUser = $auth->createUser($userProperties);
                echo "✅ Firebase user created: " . $createdUser->uid . "\n";
                $uid = $createdUser->uid;
            }
            
            // Now try to send email verification
            echo "Attempting to send email verification...\n";
            $auth->sendEmailVerificationLink($testEmail);
            echo "✅ Email verification sent successfully!\n";
            echo "📬 Please check your email at {$testEmail}\n";
            
        } catch (\Exception $e) {
            echo "❌ Firebase user/email operation failed: " . $e->getMessage() . "\n";
            echo "Error details: " . $e->getTraceAsString() . "\n";
        }
    }
    
} catch (\Exception $e) {
    echo "❌ Firebase email verification test failed: " . $e->getMessage() . "\n";
}

echo "\n📧 Test 4: Testing Laravel Logs...\n";

try {
    // Check recent Laravel logs for Firebase errors
    $logPath = storage_path('logs/laravel.log');
    
    if (file_exists($logPath)) {
        echo "✅ Laravel log file exists\n";
        
        // Get last 20 lines of log file
        $lines = file($logPath);
        $recentLines = array_slice($lines, -20);
        
        echo "📄 Recent log entries (last 20 lines):\n";
        foreach ($recentLines as $line) {
            if (stripos($line, 'firebase') !== false || stripos($line, 'email') !== false) {
                echo "  " . trim($line) . "\n";
            }
        }
    } else {
        echo "❌ Laravel log file not found at: {$logPath}\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Log check failed: " . $e->getMessage() . "\n";
}

echo "\n🎯 Summary:\n";
echo "- Check if Firebase service account file exists and is valid\n";
echo "- Test Firebase Auth initialization\n";
echo "- Attempt to send email verification via Firebase\n";
echo "- Check Laravel logs for any Firebase-related errors\n";
