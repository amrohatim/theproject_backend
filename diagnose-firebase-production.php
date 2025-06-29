<?php

/**
 * Firebase Production Diagnostic Script
 * 
 * This script diagnoses Firebase configuration issues in production
 */

require_once 'vendor/autoload.php';

echo "🔥 Firebase Production Diagnostic\n";
echo "================================\n\n";

// 1. Check service account file
echo "1. Checking Firebase service account file...\n";
$serviceAccountPath = base_path('dala3chic-e2b81-firebase-adminsdk-fbsvc-e5c52a715e.json');

if (file_exists($serviceAccountPath)) {
    echo "   ✅ Service account file exists: $serviceAccountPath\n";
    
    // Check file permissions
    $permissions = substr(sprintf('%o', fileperms($serviceAccountPath)), -4);
    echo "   📁 File permissions: $permissions\n";
    
    // Check file contents
    try {
        $serviceAccount = json_decode(file_get_contents($serviceAccountPath), true);
        if ($serviceAccount) {
            echo "   ✅ Service account file is valid JSON\n";
            echo "   📋 Project ID: " . ($serviceAccount['project_id'] ?? 'NOT SET') . "\n";
            echo "   📧 Client Email: " . ($serviceAccount['client_email'] ?? 'NOT SET') . "\n";
            echo "   🔑 Private Key ID: " . ($serviceAccount['private_key_id'] ?? 'NOT SET') . "\n";
            echo "   🔐 Private Key: " . (isset($serviceAccount['private_key']) ? 'PRESENT' : 'MISSING') . "\n";
        } else {
            echo "   ❌ Service account file contains invalid JSON\n";
        }
    } catch (Exception $e) {
        echo "   ❌ Error reading service account file: " . $e->getMessage() . "\n";
    }
} else {
    echo "   ❌ Service account file NOT FOUND: $serviceAccountPath\n";
}

echo "\n";

// 2. Check environment variables
echo "2. Checking Firebase environment variables...\n";
$firebaseEnvVars = [
    'FIREBASE_PROJECT_ID',
    'FIREBASE_PRIVATE_KEY_ID', 
    'FIREBASE_PRIVATE_KEY',
    'FIREBASE_CLIENT_EMAIL',
    'FIREBASE_CLIENT_ID',
    'FIREBASE_WEB_API_KEY'
];

foreach ($firebaseEnvVars as $var) {
    $value = env($var);
    if ($value) {
        if ($var === 'FIREBASE_PRIVATE_KEY') {
            echo "   ✅ $var: PRESENT (length: " . strlen($value) . ")\n";
        } else {
            echo "   ✅ $var: $value\n";
        }
    } else {
        echo "   ❌ $var: NOT SET\n";
    }
}

echo "\n";

// 3. Test Firebase initialization
echo "3. Testing Firebase initialization...\n";
try {
    $factory = new \Kreait\Firebase\Factory();
    
    // Try service account file first
    if (file_exists($serviceAccountPath)) {
        echo "   🔄 Attempting initialization with service account file...\n";
        $factory = $factory->withServiceAccount($serviceAccountPath);
    } else {
        echo "   🔄 Attempting initialization with environment variables...\n";
        $serviceAccount = [
            'type' => 'service_account',
            'project_id' => env('FIREBASE_PROJECT_ID'),
            'private_key_id' => env('FIREBASE_PRIVATE_KEY_ID'),
            'private_key' => str_replace('\\n', "\n", env('FIREBASE_PRIVATE_KEY')),
            'client_email' => env('FIREBASE_CLIENT_EMAIL'),
            'client_id' => env('FIREBASE_CLIENT_ID'),
            'auth_uri' => 'https://accounts.google.com/o/oauth2/auth',
            'token_uri' => 'https://oauth2.googleapis.com/token',
            'auth_provider_x509_cert_url' => 'https://www.googleapis.com/oauth2/v1/certs',
            'client_x509_cert_url' => env('FIREBASE_CLIENT_X509_CERT_URL'),
        ];
        $factory = $factory->withServiceAccount($serviceAccount);
    }
    
    $auth = $factory->createAuth();
    echo "   ✅ Firebase Auth initialized successfully\n";
    
    // Test a simple operation
    echo "   🔄 Testing Firebase connection...\n";
    $customToken = $auth->createCustomToken('test-uid');
    echo "   ✅ Firebase connection test successful\n";
    
} catch (Exception $e) {
    echo "   ❌ Firebase initialization failed: " . $e->getMessage() . "\n";
    echo "   📋 Error details: " . $e->getTraceAsString() . "\n";
}

echo "\n";

// 4. Check system time (important for JWT tokens)
echo "4. Checking system time synchronization...\n";
$systemTime = time();
$googleTime = @file_get_contents('http://worldtimeapi.org/api/timezone/UTC');
if ($googleTime) {
    $timeData = json_decode($googleTime, true);
    if ($timeData && isset($timeData['unixtime'])) {
        $timeDiff = abs($systemTime - $timeData['unixtime']);
        if ($timeDiff < 300) { // 5 minutes tolerance
            echo "   ✅ System time is synchronized (diff: {$timeDiff}s)\n";
        } else {
            echo "   ⚠️  System time may be out of sync (diff: {$timeDiff}s)\n";
        }
    }
} else {
    echo "   ⚠️  Could not check time synchronization\n";
}

echo "\n";

// 5. Check Laravel configuration
echo "5. Checking Laravel configuration...\n";
echo "   📋 APP_ENV: " . env('APP_ENV', 'NOT SET') . "\n";
echo "   📋 APP_DEBUG: " . (env('APP_DEBUG') ? 'true' : 'false') . "\n";
echo "   📋 APP_URL: " . env('APP_URL', 'NOT SET') . "\n";

echo "\n";

// 6. Recommendations
echo "6. Recommendations:\n";
echo "   💡 If service account file is missing, copy it to production server\n";
echo "   💡 If 'invalid_grant' persists, check system time synchronization\n";
echo "   💡 Ensure service account has proper Firebase Authentication permissions\n";
echo "   💡 Check Firebase project settings and service account status\n";

echo "\n🏁 Diagnostic complete!\n";
