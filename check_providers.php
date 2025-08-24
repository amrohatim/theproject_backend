<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Provider;

$providers = Provider::all();
echo "Providers: " . $providers->count() . "\n";
foreach($providers as $p) {
    echo "ID: " . $p->id . ", Name: " . $p->business_name . "\n";
}
