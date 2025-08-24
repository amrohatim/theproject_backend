<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Production Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains production-specific configuration settings
    | for your Laravel application deployment.
    |
    */

    'server_ip' => '82.25.109.98',
    
    'asset_url' => env('APP_URL', 'http://82.25.109.98'),
    
    'image_base_url' => env('APP_URL', 'http://82.25.109.98'),
    
    'api_base_url' => env('APP_URL', 'http://82.25.109.98') . '/api',
    
    'storage_url' => env('APP_URL', 'http://82.25.109.98') . '/storage',
    
    'products_url' => env('APP_URL', 'http://82.25.109.98') . '/products',
    
    'allowed_origins' => [
        'http://82.25.109.98',
        'https://82.25.109.98',
        // Add your Flutter app domains here when you have a domain
    ],
    
    'security' => [
        'force_https' => false, // Set to true when you have SSL certificate
        'secure_cookies' => false, // Set to true when using HTTPS
        'same_site_cookies' => 'lax',
    ],
    
    'performance' => [
        'cache_config' => true,
        'cache_routes' => true,
        'cache_views' => true,
        'optimize_autoloader' => true,
    ],
];
