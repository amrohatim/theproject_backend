<?php

// Simple test endpoint to debug Firebase issues
require_once 'vendor/autoload.php';

use Kreait\Firebase\Factory;
use GuzzleHttp\Client;

header('Content-Type: application/json');

try {
    echo json_encode(['status' => 'Starting Firebase test...']) . "\n";
    
    $serviceAccountPath = __DIR__ . '/dala3chic-e2b81-firebase-adminsdk-fbsvc-e5c52a715e.json';
    
    if (!file_exists($serviceAccountPath)) {
        throw new Exception('Service account file not found');
    }
    
    echo json_encode(['status' => 'Service account file found']) . "\n";
    
    // Create HTTP client with SSL configuration
    $httpClientOptions = [
        'verify' => false,
        'curl' => [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]
    ];
    
    echo json_encode(['status' => 'Creating HTTP client...']) . "\n";
    
    $httpClient = new Client($httpClientOptions);
    
    echo json_encode(['status' => 'HTTP client created']) . "\n";
    
    // Initialize Firebase
    $factory = (new Factory)
        ->withServiceAccount($serviceAccountPath)
        ->withHttpClient($httpClient);
    
    echo json_encode(['status' => 'Factory created']) . "\n";
    
    $auth = $factory->createAuth();
    
    echo json_encode(['status' => 'Firebase Auth created']) . "\n";
    
    // Test creating a user
    $testEmail = 'test' . time() . '@example.com';
    $userProperties = [
        'email' => $testEmail,
        'password' => 'TestPassword123!',
        'emailVerified' => false,
    ];
    
    echo json_encode(['status' => 'Attempting to create test user...']) . "\n";
    
    $createdUser = $auth->createUser($userProperties);
    
    echo json_encode([
        'status' => 'SUCCESS',
        'message' => 'Firebase test completed successfully',
        'user_uid' => $createdUser->uid,
        'test_email' => $testEmail
    ]) . "\n";
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'ERROR',
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]) . "\n";
}
