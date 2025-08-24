<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

// Boot the application
$app->boot();

try {
    // Test if SetLocale middleware can be instantiated
    $middleware = new \App\Http\Middleware\SetLocale();
    echo "✅ SetLocale middleware class loaded successfully\n";
    
    // Create a mock request
    $request = \Illuminate\Http\Request::create('/test', 'GET', ['lang' => 'ar']);
    
    echo "📝 Testing middleware execution...\n";
    
    // Test the middleware
    $response = $middleware->handle($request, function($req) {
        return response('Test response');
    });
    
    echo "✅ Middleware executed successfully\n";
    echo "📊 Response: " . $response->getContent() . "\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "🔍 Trace: " . $e->getTraceAsString() . "\n";
}