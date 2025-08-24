<?php

// Script to create the personal_access_tokens table directly

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

// Connect to database
try {
    $dsn = "$connection:host=$host;port=$port;dbname=$database";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected to database successfully\n";
    
    // Check if the table already exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'personal_access_tokens'");
    $tableExists = $stmt->rowCount() > 0;
    
    if ($tableExists) {
        echo "Table 'personal_access_tokens' already exists\n";
    } else {
        // Create the personal_access_tokens table
        $sql = "CREATE TABLE personal_access_tokens (
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
            UNIQUE INDEX personal_access_tokens_token_unique (token)
        )";
        
        $pdo->exec($sql);
        echo "Table 'personal_access_tokens' created successfully\n";
        
        // Add index for tokenable
        $sql = "CREATE INDEX personal_access_tokens_tokenable_type_tokenable_id_index ON personal_access_tokens (tokenable_type, tokenable_id)";
        $pdo->exec($sql);
        echo "Index for tokenable created successfully\n";
        
        // Add migration record
        $sql = "INSERT INTO migrations (migration, batch) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        
        // Get the current max batch number
        $batchStmt = $pdo->query("SELECT MAX(batch) as max_batch FROM migrations");
        $batchRow = $batchStmt->fetch(PDO::FETCH_ASSOC);
        $batch = ($batchRow['max_batch'] ?? 0) + 1;
        
        $stmt->execute(['2019_12_14_000001_create_personal_access_tokens_table', $batch]);
        echo "Migration record added successfully\n";
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "Done!\n";
