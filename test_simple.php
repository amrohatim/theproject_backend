<?php
echo "=== BRANCHES TABLE CHECKER ===\n\n";

// Database configurations to try
$configs = [
    ['host' => '127.0.0.1', 'db' => 'marketplace_windsurf'],
    ['host' => '127.0.0.1', 'db' => 'marketplace'],
    ['host' => 'localhost', 'db' => 'marketplace_windsurf'],
    ['host' => 'localhost', 'db' => 'marketplace']
];

$pdo = null;
$connected_db = null;

foreach ($configs as $config) {
    try {
        $dsn = "mysql:host={$config['host']};dbname={$config['db']};charset=utf8mb4";
        $pdo = new PDO($dsn, 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "Connected to: {$config['db']} on {$config['host']}\n\n";
        $connected_db = $config['db'];
        break;
    } catch (PDOException $e) {
        echo "Failed to connect to {$config['db']}: " . $e->getMessage() . "\n";
    }
}

if (!$pdo) {
    echo "Could not connect to any database.\n";
    exit(1);
}

try {
    // Check if branches table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'branches'");
    if (!$stmt->fetch()) {
        echo "Branches table does not exist in $connected_db\n";
        
        // Show available tables
        $stmt = $pdo->query("SHOW TABLES");
        echo "Available tables:\n";
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            echo "  - {$row[0]}\n";
        }
        exit(1);
    }
    
    echo "Branches table found!\n\n";
    
    // Get table structure
    echo "TABLE STRUCTURE:\n";
    echo str_repeat("-", 50) . "\n";
    $stmt = $pdo->query("DESCRIBE branches");
    while ($col = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo sprintf("%-20s %-15s %s\n", $col['Field'], $col['Type'], $col['Null']);
    }
    
    // Count branches
    $stmt = $pdo->query("SELECT COUNT(*) FROM branches");
    $count = $stmt->fetchColumn();
    echo "\nTotal branches: $count\n\n";
    
    if ($count > 0) {
        echo "BRANCHES DATA:\n";
        echo str_repeat("=", 60) . "\n";
        
        $stmt = $pdo->query("SELECT * FROM branches ORDER BY id");
        $index = 1;
        while ($branch = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "Branch #$index (ID: {$branch['id']}):\n";
            foreach ($branch as $key => $value) {
                if ($key == 'opening_hours' && $value) {
                    $hours = json_decode($value, true);
                    if ($hours) {
                        echo "  $key: Schedule below\n";
                        foreach ($hours as $day => $schedule) {
                            $time = $schedule['is_open'] ? 
                                "{$schedule['open']}-{$schedule['close']}" : "Closed";
                            echo "    $day: $time\n";
                        }
                    } else {
                        echo "  $key: $value\n";
                    }
                } else {
                    echo "  $key: " . ($value ?: '[NULL]') . "\n";
                }
            }
            echo "\n";
            $index++;
        }
        
        // Statistics
        echo "STATISTICS:\n";
        echo str_repeat("-", 30) . "\n";
        $stmt = $pdo->query("SELECT 
            COUNT(CASE WHEN status = 'active' THEN 1 END) as active,
            COUNT(CASE WHEN status = 'inactive' THEN 1 END) as inactive,
            COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
            COUNT(CASE WHEN image IS NOT NULL THEN 1 END) as with_images,
            AVG(rating) as avg_rating
        FROM branches");
        
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "Active: {$stats['active']}\n";
        echo "Inactive: {$stats['inactive']}\n";
        echo "Pending: {$stats['pending']}\n";
        echo "With images: {$stats['with_images']}\n";
        echo "Avg rating: " . ($stats['avg_rating'] ? number_format($stats['avg_rating'], 2) : 'None') . "\n";
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
