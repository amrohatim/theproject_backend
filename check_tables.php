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
    
    // Check if provider_profiles table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'provider_profiles'");
    $providerProfilesExists = $stmt->rowCount() > 0;
    
    if ($providerProfilesExists) {
        echo "The provider_profiles table exists.\n";
        
        // Check the structure of provider_profiles table
        $stmt = $pdo->query("DESCRIBE provider_profiles");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "provider_profiles table structure:\n";
        foreach ($columns as $column) {
            echo "- {$column['Field']}: {$column['Type']} " . 
                 ($column['Null'] === 'NO' ? 'NOT NULL' : 'NULL') . 
                 ($column['Key'] === 'PRI' ? ' PRIMARY KEY' : '') . "\n";
        }
    } else {
        echo "The provider_profiles table does not exist.\n";
    }
    
    // Check if provider_locations table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'provider_locations'");
    $providerLocationsExists = $stmt->rowCount() > 0;
    
    if ($providerLocationsExists) {
        echo "\nThe provider_locations table exists.\n";
        
        // Check the structure of provider_locations table
        $stmt = $pdo->query("DESCRIBE provider_locations");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "provider_locations table structure:\n";
        foreach ($columns as $column) {
            echo "- {$column['Field']}: {$column['Type']} " . 
                 ($column['Null'] === 'NO' ? 'NOT NULL' : 'NULL') . 
                 ($column['Key'] === 'PRI' ? ' PRIMARY KEY' : '') . "\n";
        }
    } else {
        echo "\nThe provider_locations table does not exist.\n";
        
        // Create the provider_locations table
        echo "Creating provider_locations table...\n";
        
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
        
        // Add the migration record to the migrations table
        $stmt = $pdo->query("SELECT * FROM migrations WHERE migration = '2024_07_01_000001_create_provider_locations_table'");
        $migrationExists = $stmt->rowCount() > 0;
        
        if (!$migrationExists) {
            $sql = "INSERT INTO migrations (migration, batch) VALUES ('2024_07_01_000001_create_provider_locations_table', (SELECT COALESCE(MAX(batch), 1) FROM migrations))";
            $pdo->exec($sql);
            echo "Migration record added to the migrations table.\n";
        }
    }
    
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage() . "\n");
}
