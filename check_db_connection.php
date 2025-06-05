<?php

// Simple script to check database connection
try {
    // Get database configuration from Laravel's .env file
    $envFile = __DIR__ . '/.env';
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $env = [];
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                list($key, $value) = explode('=', $line, 2);
                $env[trim($key)] = trim($value);
            }
        }
        
        echo "Database Configuration:\n";
        echo "- Connection: " . ($env['DB_CONNECTION'] ?? 'Not set') . "\n";
        
        if (isset($env['DB_CONNECTION']) && $env['DB_CONNECTION'] === 'sqlite') {
            echo "- Database: " . ($env['DB_DATABASE'] ?? 'Not set') . "\n";
            
            // Check if the SQLite database file exists
            $dbFile = $env['DB_DATABASE'] ?? __DIR__ . '/database/database.sqlite';
            if (file_exists($dbFile)) {
                echo "- SQLite database file exists\n";
            } else {
                echo "- SQLite database file does not exist\n";
                
                // Try to create the database file
                if (!file_exists(dirname($dbFile))) {
                    mkdir(dirname($dbFile), 0777, true);
                }
                
                if (touch($dbFile)) {
                    echo "- Created SQLite database file\n";
                } else {
                    echo "- Failed to create SQLite database file\n";
                }
            }
            
            // Try to connect to the SQLite database
            try {
                $pdo = new PDO('sqlite:' . $dbFile);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                echo "- Successfully connected to SQLite database\n";
                
                // Check if the migrations table exists
                $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='migrations'");
                $migrationsTableExists = $stmt->fetchColumn() !== false;
                
                if ($migrationsTableExists) {
                    echo "- Migrations table exists\n";
                    
                    // Check if the product_specifications table exists
                    $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='product_specifications'");
                    $specTableExists = $stmt->fetchColumn() !== false;
                    
                    if ($specTableExists) {
                        echo "- product_specifications table exists\n";
                    } else {
                        echo "- product_specifications table does not exist\n";
                    }
                } else {
                    echo "- Migrations table does not exist\n";
                }
            } catch (PDOException $e) {
                echo "- Failed to connect to SQLite database: " . $e->getMessage() . "\n";
            }
        } else {
            echo "- Host: " . ($env['DB_HOST'] ?? 'Not set') . "\n";
            echo "- Port: " . ($env['DB_PORT'] ?? 'Not set') . "\n";
            echo "- Database: " . ($env['DB_DATABASE'] ?? 'Not set') . "\n";
            echo "- Username: " . ($env['DB_USERNAME'] ?? 'Not set') . "\n";
            echo "- Password: " . (isset($env['DB_PASSWORD']) ? '******' : 'Not set') . "\n";
            
            // Try to connect to the database
            try {
                $dsn = $env['DB_CONNECTION'] . ':host=' . ($env['DB_HOST'] ?? '127.0.0.1') . ';port=' . ($env['DB_PORT'] ?? '3306') . ';dbname=' . ($env['DB_DATABASE'] ?? 'laravel');
                $pdo = new PDO($dsn, $env['DB_USERNAME'] ?? 'root', $env['DB_PASSWORD'] ?? '');
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                echo "- Successfully connected to database\n";
                
                // Check if the migrations table exists
                $stmt = $pdo->query("SHOW TABLES LIKE 'migrations'");
                $migrationsTableExists = $stmt->rowCount() > 0;
                
                if ($migrationsTableExists) {
                    echo "- Migrations table exists\n";
                    
                    // Check if the product_specifications table exists
                    $stmt = $pdo->query("SHOW TABLES LIKE 'product_specifications'");
                    $specTableExists = $stmt->rowCount() > 0;
                    
                    if ($specTableExists) {
                        echo "- product_specifications table exists\n";
                    } else {
                        echo "- product_specifications table does not exist\n";
                    }
                } else {
                    echo "- Migrations table does not exist\n";
                }
            } catch (PDOException $e) {
                echo "- Failed to connect to database: " . $e->getMessage() . "\n";
            }
        }
    } else {
        echo ".env file not found\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
