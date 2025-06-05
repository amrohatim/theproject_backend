<?php

// This script demonstrates how to add a category using the API

// Replace with your actual API URL
$apiUrl = 'http://127.0.0.1:8000/api';

// Step 1: Login to get an authentication token
function getAuthToken($apiUrl, $email, $password) {
    $loginData = [
        'email' => $email,
        'password' => $password
    ];
    
    $ch = curl_init($apiUrl . '/login');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loginData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        echo "Login failed with HTTP code: $httpCode\n";
        echo "Response: $response\n";
        return null;
    }
    
    $responseData = json_decode($response, true);
    return $responseData['token'] ?? null;
}

// Step 2: Create a category
function createCategory($apiUrl, $token, $categoryData) {
    $ch = curl_init($apiUrl . '/categories');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($categoryData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Bearer ' . $token
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 201) {
        echo "Category creation failed with HTTP code: $httpCode\n";
        echo "Response: $response\n";
        return null;
    }
    
    return json_decode($response, true);
}

// Main execution
echo "Adding a category via API\n";
echo "------------------------\n";

// Get admin credentials
echo "Enter admin email (default: admin@example.com): ";
$email = trim(fgets(STDIN));
if (empty($email)) {
    $email = 'admin@example.com';
}

echo "Enter admin password (default: password123): ";
$password = trim(fgets(STDIN));
if (empty($password)) {
    $password = 'password123';
}

// Get category details
echo "Enter category name: ";
$name = trim(fgets(STDIN));

echo "Enter category type (product/service): ";
$type = trim(fgets(STDIN));
if (!in_array($type, ['product', 'service'])) {
    echo "Invalid type. Using 'product' as default.\n";
    $type = 'product';
}

echo "Enter category description (optional): ";
$description = trim(fgets(STDIN));

echo "Is this a subcategory? (y/n): ";
$isSubcategory = strtolower(trim(fgets(STDIN))) === 'y';

$parentId = null;
if ($isSubcategory) {
    echo "Enter parent category ID: ";
    $parentId = (int)trim(fgets(STDIN));
}

// Prepare category data
$categoryData = [
    'name' => $name,
    'type' => $type,
    'description' => $description,
    'is_active' => true
];

if ($isSubcategory && $parentId > 0) {
    $categoryData['parent_id'] = $parentId;
}

// Get auth token
echo "\nLogging in as $email...\n";
$token = getAuthToken($apiUrl, $email, $password);

if (!$token) {
    echo "Failed to get authentication token. Exiting.\n";
    exit(1);
}

echo "Successfully logged in.\n";

// Create category
echo "Creating category...\n";
$result = createCategory($apiUrl, $token, $categoryData);

if ($result) {
    echo "Category created successfully!\n";
    echo "Category ID: " . $result['category']['id'] . "\n";
    echo "Category Name: " . $result['category']['name'] . "\n";
} else {
    echo "Failed to create category.\n";
}
