<?php

// Database connection parameters
$host = '127.0.0.1';
$db   = 'marketplace_windsurf';
$user = 'root';
$pass = '';

try {
    // Connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Test query to simulate the one that was failing
    $stmt = $pdo->query("SELECT * FROM provider_profiles LIMIT 1");
    $providerProfile = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($providerProfile) {
        $providerId = $providerProfile['id'];
        
        // Try to query the provider_locations table
        $stmt = $pdo->query("SELECT * FROM provider_locations WHERE provider_id = $providerId");
        $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $result = "Provider ID: $providerId\n";
        $result .= "Number of locations: " . count($locations) . "\n";
        
        if (count($locations) > 0) {
            $result .= "Locations:\n";
            foreach ($locations as $location) {
                $result .= "- ID: {$location['id']}, Label: {$location['label']}, Emirate: {$location['emirate']}\n";
            }
        } else {
            $result .= "No locations found for this provider.\n";
            
            // Let's insert a sample location for testing
            $stmt = $pdo->prepare("INSERT INTO provider_locations (provider_id, label, emirate, latitude, longitude, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
            $stmt->execute([$providerId, 'Test Location', 'Dubai', 25.2048, 55.2708]);
            
            $result .= "Added a sample location for testing.\n";
            
            // Verify it was added
            $stmt = $pdo->query("SELECT * FROM provider_locations WHERE provider_id = $providerId");
            $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $result .= "After adding: Number of locations: " . count($locations) . "\n";
            if (count($locations) > 0) {
                $result .= "Locations:\n";
                foreach ($locations as $location) {
                    $result .= "- ID: {$location['id']}, Label: {$location['label']}, Emirate: {$location['emirate']}\n";
                }
            }
        }
    } else {
        $result = "No provider profiles found in the database.\n";
    }
    
    file_put_contents('provider_locations_test.txt', $result);
    
} catch(PDOException $e) {
    file_put_contents('provider_locations_test.txt', "Error: " . $e->getMessage() . "\n");
}
