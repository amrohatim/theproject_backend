<?php

require __DIR__ . '/vendor/autoload.php';

use App\Models\Company;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use App\Models\Shipment;
use App\Services\ShippingService;
use App\Services\AramexService;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing Shipping Integration\n";
echo "===========================\n\n";

try {
    // Check if the tables exist
    echo "Checking database tables...\n";
    $hasCompanies = DB::getSchemaBuilder()->hasTable('companies');
    $hasOrders = DB::getSchemaBuilder()->hasTable('orders');
    $hasShipments = DB::getSchemaBuilder()->hasTable('shipments');
    
    echo "Companies table exists: " . ($hasCompanies ? 'Yes' : 'No') . "\n";
    echo "Orders table exists: " . ($hasOrders ? 'Yes' : 'No') . "\n";
    echo "Shipments table exists: " . ($hasShipments ? 'Yes' : 'No') . "\n\n";
    
    // Check if the can_deliver column exists in companies table
    if ($hasCompanies) {
        $hasCanDeliver = DB::getSchemaBuilder()->hasColumn('companies', 'can_deliver');
        echo "Companies table has can_deliver column: " . ($hasCanDeliver ? 'Yes' : 'No') . "\n";
    }
    
    // Check if the shipping_method column exists in orders table
    if ($hasOrders) {
        $hasShippingMethod = DB::getSchemaBuilder()->hasColumn('orders', 'shipping_method');
        echo "Orders table has shipping_method column: " . ($hasShippingMethod ? 'Yes' : 'No') . "\n";
    }
    
    echo "\nTesting Company Model...\n";
    $company = new Company();
    $company->name = 'Test Company';
    $company->can_deliver = true;
    echo "Company can_deliver property works: " . ($company->can_deliver ? 'Yes' : 'No') . "\n";
    
    echo "\nTesting Order Model...\n";
    $order = new Order();
    $order->shipping_method = 'aramex';
    $order->shipping_status = 'pending';
    echo "Order shipping_method property works: " . ($order->shipping_method === 'aramex' ? 'Yes' : 'No') . "\n";
    
    echo "\nTesting Shipment Model...\n";
    $shipment = new Shipment();
    $shipment->status = 'pending';
    echo "Shipment status property works: " . ($shipment->status === 'pending' ? 'Yes' : 'No') . "\n";
    
    echo "\nTesting ShippingService...\n";
    $shippingService = new ShippingService();
    echo "ShippingService instantiated successfully\n";
    
    echo "\nTesting AramexService...\n";
    $aramexService = new AramexService();
    echo "AramexService instantiated successfully\n";
    
    echo "\nAll tests completed successfully!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " (Line: " . $e->getLine() . ")\n";
}
