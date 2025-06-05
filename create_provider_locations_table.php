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
    
    // Check if the table already exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'provider_locations'");
    $tableExists = $stmt->rowCount() > 0;
    
    if (!$tableExists) {
        // Create the provider_locations table
        $sql = "CREATE TABLE `provider_locations` (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `provider_id` bigint(20) UNSIGNED NOT NULL,
            `label` varchar(255) DEFAULT NULL,
            `emirate` varchar(255) NOT NULL,
            `latitude` decimal(10,8) NOT NULL,
            `longitude` decimal(11,8) NOT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `provider_locations_provider_id_foreign` (`provider_id`),
            CONSTRAINT `provider_locations_provider_id_foreign` FOREIGN KEY (`provider_id`) REFERENCES `provider_profiles` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        
        $pdo->exec($sql);
        echo "The provider_locations table has been created successfully.\n";
    } else {
        echo "The provider_locations table already exists.\n";
    }
    
    // Check if the migrations table needs to be updated
    $stmt = $pdo->query("SELECT * FROM migrations WHERE migration = '2024_07_01_000001_create_provider_locations_table'");
    $migrationExists = $stmt->rowCount() > 0;
    
    if (!$migrationExists) {
        // Add the migration record to the migrations table
        $sql = "INSERT INTO migrations (migration, batch) VALUES ('2024_07_01_000001_create_provider_locations_table', (SELECT MAX(batch) FROM migrations))";
        $pdo->exec($sql);
        echo "Migration record added to the migrations table.\n";
    } else {
        echo "Migration record already exists in the migrations table.\n";
    }
    
    echo "Process completed successfully.\n";
    
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage() . "\n");
}
