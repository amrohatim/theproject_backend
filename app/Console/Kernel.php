<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Calculate trending scores (products + services + categories) hourly
        $schedule->command('trending:calculate')->hourly();

        // Refresh trending scores (including products and services) hourly via script
        $schedule->exec('php ' . base_path('calculate_trending_scores.php'))->hourly();

        // Check license expiration daily at 2 AM
        $schedule->command('license:check-expiration')->dailyAt('02:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
