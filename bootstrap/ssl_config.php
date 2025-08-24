<?php

/**
 * SSL Configuration for Firebase SDK
 * 
 * This file configures SSL settings for the Firebase PHP SDK to work
 * properly in development environments, particularly on Windows.
 */

// Only run in development environment
if (env('APP_ENV') === 'local' && env('FIREBASE_DISABLE_SSL_VERIFICATION', false)) {
    
    // Configure curl default options
    $curlDefaults = [
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_CONNECTTIMEOUT => 30,
    ];
    
    // Set curl defaults globally
    foreach ($curlDefaults as $option => $value) {
        curl_setopt_array(curl_init(), [$option => $value]);
    }
    
    // Configure stream context defaults
    $streamContext = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true,
        ],
        'http' => [
            'timeout' => 60,
            'ignore_errors' => true,
        ]
    ];
    
    stream_context_set_default($streamContext);
    
    // Set PHP ini settings
    ini_set('curl.cainfo', '');
    ini_set('openssl.cafile', '');
    ini_set('auto_detect_line_endings', true);
    
    // Set environment variable for Guzzle
    putenv('GUZZLE_CURL_OPTIONS=' . json_encode([
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ]));
    
    // Log the configuration
    if (function_exists('error_log')) {
        error_log('SSL verification disabled for Firebase SDK in development environment');
    }
}
