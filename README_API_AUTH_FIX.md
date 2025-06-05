# API Authentication Fix

This document explains how to fix the 500 Internal Server Error that occurs during login from the Flutter mobile app.

## The Problem

The error occurs because the `personal_access_tokens` table is missing from the database. This table is required by Laravel Sanctum for API token authentication.

## Solution 1: Run Migrations

The simplest solution is to run the Laravel migrations to create all required tables:

```bash
php artisan migrate
```

If that doesn't work, try:

```bash
php artisan migrate --force
```

## Solution 2: Create the Table Manually

If migrations don't work, you can create the table manually using the provided SQL script:

```bash
mysql -u root -p marketplace_windsurf < create_tokens_table.sql
```

Or you can run the SQL commands directly in your database management tool (phpMyAdmin, MySQL Workbench, etc.):

```sql
-- Create the personal_access_tokens table if it doesn't exist
CREATE TABLE IF NOT EXISTS personal_access_tokens (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tokenable_type VARCHAR(255) NOT NULL,
    tokenable_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    token VARCHAR(64) NOT NULL,
    abilities TEXT NULL,
    last_used_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE INDEX personal_access_tokens_token_unique (token),
    INDEX personal_access_tokens_tokenable_type_tokenable_id_index (tokenable_type, tokenable_id)
);

-- Add migration record if it doesn't exist
INSERT IGNORE INTO migrations (migration, batch) 
SELECT '2019_12_14_000001_create_personal_access_tokens_table', COALESCE(MAX(batch), 0) + 1 
FROM migrations;
```

## Solution 3: Use the PHP Script

We've also provided a PHP script that will create the table for you:

```bash
php create_tokens_table.php
```

## Temporary Workaround

We've modified the AuthController to handle the case where the `personal_access_tokens` table doesn't exist. This allows the app to function without API token authentication, but it's not a permanent solution.

The modified controller will:
1. Try to create/delete tokens as normal
2. If it fails due to the missing table, it will return a special response
3. Your Flutter app should handle this special response and continue without token authentication

## Flutter App Changes

You may need to update your Flutter app to handle the special response from the API. Look for the `token` value of `authentication-not-available` in the response, which indicates that token authentication is not available.

Example:

```dart
if (response.data['token'] == 'authentication-not-available') {
  // Handle the case where token authentication is not available
  // Perhaps show a warning to the user or use a different authentication method
}
```

## Long-term Solution

The proper long-term solution is to ensure that all migrations are run correctly and that the `personal_access_tokens` table exists in your database. This is required for proper API token authentication with Laravel Sanctum.
