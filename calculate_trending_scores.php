<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use App\Services\TrendingService;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

echo "🔥 Calculating trending scores for all entities...\n\n";

$trendingService = new TrendingService();

// Calculate vendor scores
echo "📊 Calculating vendor scores...\n";
$trendingService->calculateVendorScores();
echo "✅ Vendor scores calculated successfully!\n\n";

// Calculate branch popularity scores
echo "🏪 Calculating branch popularity scores...\n";
$trendingService->calculateBranchPopularityScores();
echo "✅ Branch popularity scores calculated successfully!\n\n";

// Calculate trending scores for categories (already done by seeder, but let's refresh)
echo "🔥 Recalculating trending scores for categories...\n";
$trendingService->calculateTrendingScores();
echo "✅ Category trending scores recalculated successfully!\n\n";

echo "🎉 All trending scores have been calculated!\n";
echo "You can now test the trending endpoints again.\n";
