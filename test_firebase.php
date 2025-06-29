<?php

require_once 'vendor/autoload.php';

use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Log;

// Test Firebase configuration
echo "Testing Firebase Configuration...\n";

try {
    // Test 1: Check if service account file exists
    $serviceAccountPath = __DIR__ . '/dala3chic-e2b81-firebase-adminsdk-fbsvc-e5c52a715e.json';
    echo "1. Checking service account file: " . $serviceAccountPath . "\n";
    
    if (!file_exists($serviceAccountPath)) {
        echo "   ❌ Service account file not found!\n";
        exit(1);
    }
    echo "   ✅ Service account file exists\n";
    
    // Test 2: Validate JSON structure
    echo "2. Validating service account JSON...\n";
    $serviceAccountContent = file_get_contents($serviceAccountPath);
    $serviceAccount = json_decode($serviceAccountContent, true);
    
    if (!$serviceAccount) {
        echo "   ❌ Invalid JSON in service account file!\n";
        exit(1);
    }
    
    $requiredFields = ['type', 'project_id', 'private_key_id', 'private_key', 'client_email', 'client_id'];
    foreach ($requiredFields as $field) {
        if (!isset($serviceAccount[$field])) {
            echo "   ❌ Missing required field: $field\n";
            exit(1);
        }
    }
    echo "   ✅ Service account JSON is valid\n";
    echo "   Project ID: " . $serviceAccount['project_id'] . "\n";
    echo "   Client Email: " . $serviceAccount['client_email'] . "\n";
    
    // Test 3: Initialize Firebase
    echo "3. Initializing Firebase...\n";
    $factory = (new Factory)->withServiceAccount($serviceAccountPath);
    $auth = $factory->createAuth();
    echo "   ✅ Firebase initialized successfully\n";
    
    // Test 4: Test connection with a simple operation
    echo "4. Testing Firebase connection...\n";
    try {
        // Create a test custom token to verify the connection
        $customToken = $auth->createCustomToken('test-connection-' . time());
        echo "   ✅ Firebase connection test successful\n";
        echo "   Custom token created (first 50 chars): " . substr($customToken->toString(), 0, 50) . "...\n";
    } catch (Exception $e) {
        echo "   ❌ Firebase connection test failed: " . $e->getMessage() . "\n";
        
        // Check for specific error types
        if (strpos($e->getMessage(), 'invalid_grant') !== false) {
            echo "   🔍 This is an 'invalid_grant' error - possible causes:\n";
            echo "      - System clock is not synchronized\n";
            echo "      - Service account credentials are invalid or expired\n";
            echo "      - Network connectivity issues\n";
            echo "      - Firewall blocking HTTPS requests to Google APIs\n";
        }
        
        throw $e;
    }
    
    echo "\n🎉 All Firebase tests passed successfully!\n";
    
} catch (Exception $e) {
    echo "\n❌ Firebase test failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
