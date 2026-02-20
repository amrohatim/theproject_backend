<?php

echo "=== CHECKING USER AND PRODUCTS ===\n\n";

// Direct database connection using the same credentials from .env
try {
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=tower;charset=utf8mb4", "glowlabs", "Fifafifa2021");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Database connected successfully\n\n";
    
    // 1. Find the user with email gogoh3296@gmail.com
    echo "1. FINDING USER:\n";
    $stmt = $pdo->prepare("SELECT id, name, email, role FROM users WHERE email = ?");
    $stmt->execute(['gogoh3296@gmail.com']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "   User found:\n";
        echo "   - ID: {$user['id']}\n";
        echo "   - Name: {$user['name']}\n";
        echo "   - Email: {$user['email']}\n";
        echo "   - Role: {$user['role']}\n\n";
        
        $userId = $user['id'];
        
        // 2. Check products created by this user
        echo "2. PRODUCTS CREATED BY THIS USER:\n";
        $stmt = $pdo->prepare("SELECT id, name, price, stock, user_id, created_at FROM products WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ($products) {
            echo "   Found " . count($products) . " products:\n";
            foreach ($products as $product) {
                echo "   - Product ID: {$product['id']}\n";
                echo "     Name: {$product['name']}\n";
                echo "     Price: \${$product['price']}\n";
                echo "     Stock: {$product['stock']}\n";
                echo "     User ID: {$product['user_id']}\n";
                echo "     Created: {$product['created_at']}\n\n";
            }
        } else {
            echo "   No products found for this user.\n\n";
        }
        
        // 3. Check all products in the database
        echo "3. ALL PRODUCTS IN DATABASE:\n";
        $stmt = $pdo->query("SELECT id, name, price, stock, user_id, created_at FROM products ORDER BY created_at DESC LIMIT 10");
        $allProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ($allProducts) {
            echo "   Found " . count($allProducts) . " recent products:\n";
            foreach ($allProducts as $product) {
                echo "   - Product ID: {$product['id']}\n";
                echo "     Name: {$product['name']}\n";
                echo "     Price: \${$product['price']}\n";
                echo "     Stock: {$product['stock']}\n";
                echo "     User ID: {$product['user_id']}\n";
                echo "     Created: {$product['created_at']}\n\n";
            }
        } else {
            echo "   No products found in database.\n\n";
        }
        
        // 4. Try to create a simple product directly in the database
        echo "4. CREATING TEST PRODUCT DIRECTLY:\n";
        try {
            // Get a valid branch_id and category_id
            $stmt = $pdo->query("SELECT id FROM branches LIMIT 1");
            $branch = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $stmt = $pdo->query("SELECT id FROM categories LIMIT 1");
            $category = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($branch && $category) {
                $stmt = $pdo->prepare("INSERT INTO products (branch_id, category_id, user_id, name, price, stock, description, is_available, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
                $result = $stmt->execute([
                    $branch['id'],
                    $category['id'],
                    $userId,
                    'Test Product via Database',
                    29.99,
                    100,
                    'This product was created directly via database to test user_id assignment.',
                    1
                ]);
                
                if ($result) {
                    $newProductId = $pdo->lastInsertId();
                    echo "   ✅ Product created successfully!\n";
                    echo "   - Product ID: {$newProductId}\n";
                    echo "   - User ID: {$userId}\n";
                    echo "   - Branch ID: {$branch['id']}\n";
                    echo "   - Category ID: {$category['id']}\n\n";
                    
                    // Verify the product was created with correct user_id
                    $stmt = $pdo->prepare("SELECT id, name, user_id FROM products WHERE id = ?");
                    $stmt->execute([$newProductId]);
                    $verifyProduct = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($verifyProduct && $verifyProduct['user_id'] == $userId) {
                        echo "   ✅ VERIFICATION SUCCESSFUL!\n";
                        echo "   - Product '{$verifyProduct['name']}' (ID: {$verifyProduct['id']}) has correct user_id: {$verifyProduct['user_id']}\n";
                    } else {
                        echo "   ❌ VERIFICATION FAILED!\n";
                        echo "   - Product user_id does not match expected user_id\n";
                    }
                } else {
                    echo "   ❌ Failed to create product\n";
                }
            } else {
                echo "   ❌ Could not find valid branch or category\n";
            }
        } catch (Exception $e) {
            echo "   ❌ Error creating product: " . $e->getMessage() . "\n";
        }
        
    } else {
        echo "   ❌ User not found with email: gogoh3296@gmail.com\n";
    }
    
    echo "\n🎉 Database check completed!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
