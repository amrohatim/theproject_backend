-- Check if the table exists
SELECT COUNT(*) INTO @table_exists FROM information_schema.tables 
WHERE table_schema = 'marketplace_windsurf' AND table_name = 'provider_locations';

-- Create the table if it doesn't exist
SET @create_table = CONCAT("
CREATE TABLE IF NOT EXISTS `provider_locations` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");

PREPARE stmt FROM @create_table;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add the migration record if it doesn't exist
SELECT COUNT(*) INTO @migration_exists FROM migrations 
WHERE migration = '2024_07_01_000001_create_provider_locations_table';

SET @max_batch = (SELECT COALESCE(MAX(batch), 1) FROM migrations);

SET @insert_migration = CONCAT("
INSERT INTO migrations (migration, batch) 
SELECT '2024_07_01_000001_create_provider_locations_table', ", @max_batch, " + 1
WHERE NOT EXISTS (SELECT 1 FROM migrations WHERE migration = '2024_07_01_000001_create_provider_locations_table');
");

PREPARE stmt FROM @insert_migration;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
