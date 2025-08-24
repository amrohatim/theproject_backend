<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TrendingService;

class CalculateTrendingScores extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trending:calculate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate trending scores for categories, vendors, and branches';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $trendingService = new TrendingService();

        $this->info('Calculating trending scores for categories...');
        $trendingService->calculateTrendingScores();

        $this->info('Calculating scores for vendors...');
        $trendingService->calculateVendorScores();

        $this->info('Calculating popularity scores for branches...');
        $trendingService->calculateBranchPopularityScores();

        $this->info('All popularity scores calculated successfully.');

        return Command::SUCCESS;
    }
}
