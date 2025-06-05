<?php

try {
    $host = '127.0.0.1';
    $db   = 'marketplace';
    $user = 'root';
    $pass = '';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    // Check if database exists
    $stmt = $pdo->query("SHOW DATABASES LIKE 'marketplace'");
    $databases = $stmt->fetchAll();
    
    if (count($databases) > 0) {
        echo "Database 'marketplace' exists.\n";
    } else {
        echo "Database 'marketplace' does not exist.\n";
        exit;
    }
    
    // Check tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (count($tables) > 0) {
        echo "Tables found in 'marketplace' database:\n";
        foreach ($tables as $table) {
            echo "- $table\n";
        }
    } else {
        echo "No tables found in 'marketplace' database.\n";
    }
    
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
