<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "Checking database schema for branches table...\n\n";

// Check if view_count column exists
$columns = Schema::getColumnListing('branches');
echo "Columns in branches table:\n";
echo implode(', ', $columns) . "\n\n";

if (in_array('view_count', $columns)) {
    echo "✅ view_count column exists in branches table\n";
    
    // Check column type
    $columnType = DB::getSchemaBuilder()->getColumnType('branches', 'view_count');
    echo "Column type: $columnType\n";
    
    // Get default value
    $columnInfo = DB::select("SHOW COLUMNS FROM branches WHERE Field = 'view_count'")[0];
    echo "Default value: {$columnInfo->Default}\n";
    echo "Nullable: " . ($columnInfo->Null === "YES" ? "Yes" : "No") . "\n\n";
} else {
    echo "❌ view_count column DOES NOT exist in branches table\n\n";
}

// Check a few branch records
echo "Checking branch records...\n";
$branches = DB::table('branches')->select('id', 'name', 'view_count')->limit(5)->get();

foreach ($branches as $branch) {
    echo "Branch ID: {$branch->id}, Name: {$branch->name}, View Count: " . 
        (isset($branch->view_count) ? $branch->view_count : 'NULL') . "\n";
}
echo "\n";

// Attempt a manual update
echo "Attempting manual update of view_count...\n";
try {
    $updateResult = DB::table('branches')
        ->where('id', 1)
        ->update(['view_count' => DB::raw('COALESCE(view_count, 0) + 1')]);
    
    echo "Update result: " . ($updateResult ? "Success" : "Failed") . "\n";
    
    // Check the result
    $branch = DB::table('branches')->select('id', 'name', 'view_count')->where('id', 1)->first();
    echo "After update - Branch ID: {$branch->id}, Name: {$branch->name}, View Count: " . 
        (isset($branch->view_count) ? $branch->view_count : 'NULL') . "\n";
} catch (Exception $e) {
    echo "Error updating view_count: " . $e->getMessage() . "\n";
}

// Also check if the view_tracking table has records
echo "\nChecking view_tracking records...\n";
$trackingRecords = DB::table('view_tracking')
    ->where('entity_type', 'branch')
    ->select('entity_id', DB::raw('count(*) as total_views'))
    ->groupBy('entity_id')
    ->get();

if (count($trackingRecords) > 0) {
    foreach ($trackingRecords as $record) {
        echo "Branch ID: {$record->entity_id}, Total Tracked Views: {$record->total_views}\n";
    }
} else {
    echo "No view_tracking records found for branches.\n";
}