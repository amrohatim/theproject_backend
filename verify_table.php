<?php

// Connect to the database
$mysqli = new mysqli('127.0.0.1', 'root', '', 'marketplace_windsurf');

// Check connection
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
}

// Check if the table exists
$result = $mysqli->query("SHOW TABLES LIKE 'provider_locations'");
if ($result->num_rows > 0) {
    echo "SUCCESS: provider_locations table exists.\n";
    
    // Check the structure
    $result = $mysqli->query("DESCRIBE provider_locations");
    echo "Table structure:\n";
    while ($row = $result->fetch_assoc()) {
        echo "- {$row['Field']}: {$row['Type']} " . 
             ($row['Null'] === 'NO' ? 'NOT NULL' : 'NULL') . 
             ($row['Key'] === 'PRI' ? ' PRIMARY KEY' : '') . "\n";
    }
} else {
    echo "ERROR: provider_locations table does not exist.\n";
}

// Close connection
$mysqli->close();
