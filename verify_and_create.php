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
    
    // Check if the table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'provider_locations'");
    $tableExists = $stmt->rowCount() > 0;
    
    // Write the result to a file
    $result = "Table provider_locations " . ($tableExists ? "exists" : "does not exist") . "\n";
    file_put_contents('table_check_result.txt', $result);
    
    if (!$tableExists) {
        // Create the table
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
        file_put_contents('table_check_result.txt', $result . "Table created successfully!\n", FILE_APPEND);
        
        // Add the migration record
        $stmt = $pdo->query("SELECT MAX(batch) as max_batch FROM migrations");
        $maxBatch = $stmt->fetch(PDO::FETCH_ASSOC)['max_batch'] + 1;
        
        $sql = "INSERT INTO migrations (migration, batch) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['2024_07_01_000001_create_provider_locations_table', $maxBatch]);
        
        file_put_contents('table_check_result.txt', $result . "Table created successfully!\nMigration record added successfully!\n", FILE_APPEND);
    }
    
} catch(PDOException $e) {
    file_put_contents('table_check_result.txt', "Error: " . $e->getMessage() . "\n");
}
