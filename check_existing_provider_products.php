<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ProviderProduct;

$pp = ProviderProduct::first();
if($pp) {
    echo "Provider ID: " . $pp->provider_id . ", Product: " . $pp->product_name . "\n";
} else {
    echo "No provider products found\n";
}
