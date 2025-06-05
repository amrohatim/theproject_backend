<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ViewTrackingService;

class CleanupViewTracking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'view-tracking:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old view tracking records (older than 30 days)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting view tracking cleanup...');

        $viewTrackingService = app(ViewTrackingService::class);
        $deletedCount = $viewTrackingService->cleanupOldRecords();

        $this->info("Cleanup completed. Deleted {$deletedCount} old view tracking records.");

        return Command::SUCCESS;
    }
}
