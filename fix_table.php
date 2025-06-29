<?php

// This script checks if the provider_locations table exists and creates it if it doesn't

// Connect to the database
$mysqli = new mysqli('127.0.0.1', 'root', '', 'marketplace_windsurf');

// Check connection
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
}

// Check if the table exists
$result = $mysqli->query("SHOW TABLES LIKE 'provider_locations'");
if ($result->num_rows == 0) {
    // Table doesn't exist, create it
    echo "provider_locations table doesn't exist. Creating it now...\n";
    
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
    
    if ($mysqli->query($sql) === TRUE) {
        echo "Table provider_locations created successfully\n";
    } else {
        echo "Error creating table: " . $mysqli->error . "\n";
    }
} else {
    echo "provider_locations table already exists.\n";
}

// Close connection
$mysqli->close();
