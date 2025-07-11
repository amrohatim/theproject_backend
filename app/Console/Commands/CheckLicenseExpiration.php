<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Merchant;
use Carbon\Carbon;

class CheckLicenseExpiration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'license:check-expiration {--dry-run : Show what would be updated without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for expired merchant licenses and update verification status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting license expiration check...');

        $dryRun = $this->option('dry-run');
        $today = Carbon::today();

        // Find merchants with expired licenses that are still marked as verified
        $expiredLicenses = Merchant::where('license_verified', true)
            ->where('license_status', 'verified')
            ->where('license_expiry_date', '<', $today)
            ->get();

        if ($expiredLicenses->isEmpty()) {
            $this->info('No expired licenses found.');
            return Command::SUCCESS;
        }

        $this->info("Found {$expiredLicenses->count()} expired license(s).");

        $updatedCount = 0;

        foreach ($expiredLicenses as $merchant) {
            $daysExpired = $today->diffInDays($merchant->license_expiry_date);

            $this->line("Processing merchant: {$merchant->business_name} (ID: {$merchant->id})");
            $this->line("  License expired: {$merchant->license_expiry_date->format('Y-m-d')} ({$daysExpired} days ago)");

            if (!$dryRun) {
                // Update merchant verification status
                $merchant->update([
                    'license_verified' => false,
                    'license_status' => 'expired',
                    'is_verified' => false, // Also update main verification status
                ]);

                $this->line("  ✓ Updated verification status to expired");
                $updatedCount++;

                // TODO: Send notification to merchant about expired license
                // This could be implemented as an email notification

            } else {
                $this->line("  [DRY RUN] Would update verification status to expired");
            }

            $this->line('');
        }

        if ($dryRun) {
            $this->warn("DRY RUN: {$expiredLicenses->count()} merchant(s) would be updated.");
        } else {
            $this->info("Successfully updated {$updatedCount} merchant(s) with expired licenses.");
        }

        // Also check for licenses expiring soon (within 30 days)
        $this->checkUpcomingExpirations($dryRun);

        return Command::SUCCESS;
    }

    /**
     * Check for licenses expiring soon and log warnings.
     */
    private function checkUpcomingExpirations($dryRun = false)
    {
        $this->info('Checking for licenses expiring within 30 days...');

        $thirtyDaysFromNow = Carbon::today()->addDays(30);

        $expiringLicenses = Merchant::where('license_verified', true)
            ->where('license_status', 'verified')
            ->whereBetween('license_expiry_date', [Carbon::today(), $thirtyDaysFromNow])
            ->get();

        if ($expiringLicenses->isEmpty()) {
            $this->info('No licenses expiring within 30 days.');
            return;
        }

        $this->warn("Found {$expiringLicenses->count()} license(s) expiring within 30 days:");

        foreach ($expiringLicenses as $merchant) {
            $daysUntilExpiry = Carbon::today()->diffInDays($merchant->license_expiry_date);
            $this->line("  - {$merchant->business_name}: expires {$merchant->license_expiry_date->format('Y-m-d')} ({$daysUntilExpiry} days)");

            // TODO: Send reminder notification to merchant
        }
    }
}
