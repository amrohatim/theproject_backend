<?php

// Database connection parameters
$host = '127.0.0.1';
$db   = 'marketplace_windsurf';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

try {
    // Connect to the database
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    // Check if provider_locations table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'provider_locations'");
    $tableExists = $stmt->rowCount() > 0;
    
    if ($tableExists) {
        echo "The provider_locations table exists.\n";
        
        // Check if there are any records
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM provider_locations");
        $count = $stmt->fetch()['count'];
        
        echo "Number of records in provider_locations: $count\n";
    } else {
        echo "The provider_locations table does not exist.\n";
    }
    
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage() . "\n");
}
