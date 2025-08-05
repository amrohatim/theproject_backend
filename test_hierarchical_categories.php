<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\API\SearchController;
use Illuminate\Http\Request;
use App\Models\Category;

echo "🧪 Testing Hierarchical Category Filtering Logic\n\n";

// First, let's check the current category structure
echo "📋 Current Category Structure:\n";
$categories = Category::with('children')->whereNull('parent_id')->get();

foreach ($categories as $parent) {
    echo "📁 {$parent->name} (ID: {$parent->id})\n";
    if ($parent->children && $parent->children->count() > 0) {
        foreach ($parent->children as $child) {
            echo "   📄 {$child->name} (ID: {$child->id})\n";
        }
    }
    echo "\n";
}

// Test scenarios
echo "🧪 Testing Hierarchical Category Filter Scenarios:\n\n";

// Create a test controller instance
$controller = new SearchController();

// Use reflection to access the private method
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('processHierarchicalCategoryFilter');
$method->setAccessible(true);

// Test Scenario 1: Parent category only
echo "1️⃣ Scenario 1: Parent Category Only\n";
echo "   Input: Parent category ID (should include all children)\n";

if ($categories->count() > 0) {
    $parentId = $categories->first()->id;
    $testInput1 = [$parentId];
    
    echo "   Testing with parent ID: $parentId\n";
    $result1 = $method->invoke($controller, $testInput1);
    echo "   Result: " . json_encode($result1) . "\n";
    echo "   Expected: Parent ID + all children IDs\n\n";
}

// Test Scenario 2: Parent + specific subcategory
echo "2️⃣ Scenario 2: Parent + Specific Subcategory\n";
echo "   Input: Parent + one of its children (should only include the child)\n";

if ($categories->count() > 0 && $categories->first()->children && $categories->first()->children->count() > 0) {
    $parentId = $categories->first()->id;
    $childId = $categories->first()->children->first()->id;
    $testInput2 = [$parentId, $childId];
    
    echo "   Testing with parent ID: $parentId and child ID: $childId\n";
    $result2 = $method->invoke($controller, $testInput2);
    echo "   Result: " . json_encode($result2) . "\n";
    echo "   Expected: Only the child ID (parent overridden)\n\n";
}

// Test Scenario 3: Multiple subcategories only
echo "3️⃣ Scenario 3: Multiple Subcategories Only\n";
echo "   Input: Multiple children from same parent (should include only those children)\n";

if ($categories->count() > 0 && $categories->first()->children && $categories->first()->children->count() >= 2) {
    $child1Id = $categories->first()->children->get(0)->id;
    $child2Id = $categories->first()->children->get(1)->id;
    $testInput3 = [$child1Id, $child2Id];
    
    echo "   Testing with child IDs: $child1Id, $child2Id\n";
    $result3 = $method->invoke($controller, $testInput3);
    echo "   Result: " . json_encode($result3) . "\n";
    echo "   Expected: Only the specified child IDs\n\n";
}

// Test Scenario 4: Mixed parents and children from different families
echo "4️⃣ Scenario 4: Mixed Categories from Different Families\n";
echo "   Input: Parent from one family + child from another family\n";

if ($categories->count() >= 2) {
    $parent1Id = $categories->get(0)->id;
    $parent2 = $categories->get(1);
    
    if ($parent2->children && $parent2->children->count() > 0) {
        $child2Id = $parent2->children->first()->id;
        $testInput4 = [$parent1Id, $child2Id];
        
        echo "   Testing with parent ID: $parent1Id and unrelated child ID: $child2Id\n";
        $result4 = $method->invoke($controller, $testInput4);
        echo "   Result: " . json_encode($result4) . "\n";
        echo "   Expected: Parent1 + all its children + Child2\n\n";
    }
}

echo "✅ Hierarchical Category Filter Testing Complete!\n";
