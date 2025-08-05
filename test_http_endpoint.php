<?php

echo "🔍 Testing HTTP endpoint from backend...\n\n";

// Get the base URL from environment
$baseUrl = 'http://192.168.70.64:8000';

try {
    // Test the HTTP endpoint
    $url = $baseUrl . '/api/product-colors';
    
    echo "📡 Making HTTP request to: $url\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Content-Type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "❌ cURL Error: $error\n";
        exit(1);
    }
    
    echo "📡 HTTP Status: $httpCode\n";
    echo "📋 Raw Response: $response\n\n";
    
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        
        if (json_last_error() === JSON_ERROR_NONE) {
            echo "✅ JSON decoded successfully\n";
            echo "📊 Response structure:\n";
            echo "   - Success: " . ($data['success'] ? 'true' : 'false') . "\n";
            echo "   - Count: " . ($data['count'] ?? 'N/A') . "\n";
            echo "   - Total in DB: " . ($data['total_in_db'] ?? 'N/A') . "\n";
            
            if (isset($data['colors']) && is_array($data['colors'])) {
                echo "   - Colors array length: " . count($data['colors']) . "\n\n";
                
                echo "📋 First few colors from HTTP response:\n";
                foreach (array_slice($data['colors'], 0, 3) as $index => $color) {
                    echo "   🎨 Color $index:\n";
                    echo "      - ID: " . ($color['id'] ?? 'NULL') . "\n";
                    echo "      - Name: '" . ($color['name'] ?? 'NULL') . "'\n";
                    echo "      - Color Code: " . ($color['color_code'] ?? 'NULL') . "\n";
                    echo "      - Raw data: " . json_encode($color) . "\n\n";
                }
            } else {
                echo "❌ No colors array found in response\n";
            }
        } else {
            echo "❌ JSON decode error: " . json_last_error_msg() . "\n";
        }
    } else {
        echo "❌ HTTP request failed with status $httpCode\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
