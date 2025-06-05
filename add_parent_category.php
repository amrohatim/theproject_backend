<?php

// Bootstrap the Laravel application
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Category;

// Check if arguments are provided
if ($argc < 3) {
    echo "Usage: php add_parent_category.php <name> <type> [description] [icon]\n";
    echo "Example: php add_parent_category.php \"Pet Supplies\" product \"Products for pets\" \"fas fa-paw\"\n";
    exit(1);
}

// Get arguments
$name = $argv[1];
$type = $argv[2];
$description = $argc > 3 ? $argv[3] : null;
$icon = $argc > 4 ? $argv[4] : null;

// Validate type
if (!in_array($type, ['product', 'service'])) {
    echo "Error: Type must be either 'product' or 'service'.\n";
    exit(1);
}

// Check if category already exists
$existingCategory = Category::where('name', $name)->whereNull('parent_id')->first();
if ($existingCategory) {
    echo "Error: A parent category with the name '{$name}' already exists.\n";
    exit(1);
}

// Create the parent category
$category = Category::create([
    'name' => $name,
    'type' => $type,
    'description' => $description,
    'icon' => $icon,
    'is_active' => true,
]);

echo "Parent category '{$name}' created successfully with ID: {$category->id}\n";

// Ask if user wants to add subcategories
echo "Do you want to add subcategories to this parent category? (y/n): ";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
if (trim($line) == 'y') {
    echo "Enter subcategories in the format: name|description (one per line, empty line to finish):\n";
    
    while (true) {
        echo "> ";
        $line = fgets($handle);
        $line = trim($line);
        
        if (empty($line)) {
            break;
        }
        
        $parts = explode('|', $line);
        $subcategoryName = $parts[0];
        $subcategoryDescription = isset($parts[1]) ? $parts[1] : null;
        
        Category::create([
            'name' => $subcategoryName,
            'type' => $type,
            'description' => $subcategoryDescription,
            'parent_id' => $category->id,
            'is_active' => true,
        ]);
        
        echo "Subcategory '{$subcategoryName}' added.\n";
    }
}

echo "Done!\n";
