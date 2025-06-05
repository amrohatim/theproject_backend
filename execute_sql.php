<?php

// Script to execute SQL to create product specification tables

// Get database configuration from .env file
$envFile = __DIR__ . '/.env';
if (!file_exists($envFile)) {
    echo ".env file not found\n";
    exit(1);
}

$lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$env = [];
foreach ($lines as $line) {
    if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
        list($key, $value) = explode('=', $line, 2);
        $env[trim($key)] = trim($value);
    }
}

// Get database connection details
$connection = $env['DB_CONNECTION'] ?? 'mysql';
$host = $env['DB_HOST'] ?? '127.0.0.1';
$port = $env['DB_PORT'] ?? '3306';
$database = $env['DB_DATABASE'] ?? 'laravel';
$username = $env['DB_USERNAME'] ?? 'root';
$password = $env['DB_PASSWORD'] ?? '';

echo "Database Configuration:\n";
echo "- Connection: $connection\n";
echo "- Host: $host\n";
echo "- Port: $port\n";
echo "- Database: $database\n";
echo "- Username: $username\n";
echo "- Password: " . (empty($password) ? 'Not set' : '******') . "\n";

// Read SQL file
$sqlFile = __DIR__ . '/create_spec_tables.sql';
if (!file_exists($sqlFile)) {
    echo "SQL file not found: $sqlFile\n";
    exit(1);
}

$sql = file_get_contents($sqlFile);

// Connect to database
try {
    if ($connection === 'sqlite') {
        $pdo = new PDO("sqlite:$database");
    } else {
        $dsn = "$connection:host=$host;port=$port;dbname=$database";
        $pdo = new PDO($dsn, $username, $password);
    }
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected to database successfully\n";
    
    // Execute SQL
    echo "Executing SQL...\n";
    
    // Split SQL into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            try {
                $pdo->exec($statement);
                echo "Executed: " . substr($statement, 0, 50) . "...\n";
            } catch (PDOException $e) {
                echo "Error executing statement: " . $e->getMessage() . "\n";
                echo "Statement: $statement\n";
            }
        }
    }
    
    echo "SQL execution completed\n";
    
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "Done!\n";
