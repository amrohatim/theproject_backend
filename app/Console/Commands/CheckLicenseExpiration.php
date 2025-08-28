<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\License;
use App\Models\Merchant;
use App\Models\BranchLicense;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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
    protected $description = 'Check for expired licenses and update verification status for all users (vendors, providers, merchants) and branch licenses';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting license expiration check...');

        $dryRun = $this->option('dry-run');
        $today = Carbon::today();

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }

        // Check licenses table (for vendors and providers)
        $this->checkLicensesTable($today, $dryRun);

        // Check merchants table
        $this->checkMerchantsTable($today, $dryRun);

        // Check branch licenses table
        $this->checkBranchLicensesTable($today, $dryRun);

        // Also check for licenses expiring soon (within 30 days)
        $this->checkUpcomingExpirations($dryRun);

        $this->info('License expiration check completed.');
        return Command::SUCCESS;
    }

    /**
     * Check and update expired licenses in the licenses table.
     */
    private function checkLicensesTable(Carbon $today, bool $dryRun)
    {
        $this->info('Checking licenses table...');

        // Find active licenses that have expired
        $expiredLicenses = License::where('status', 'active')
            ->where('end_date', '<', $today)
            ->get();

        if ($expiredLicenses->isEmpty()) {
            $this->info('No expired licenses found in licenses table.');
            return;
        }

        $this->info("Found {$expiredLicenses->count()} expired licenses in licenses table:");

        foreach ($expiredLicenses as $license) {
            $user = $license->user;
            $userType = $user ? $user->role : 'unknown';
            $userName = $user ? $user->name : 'Unknown User';
            $companyName = $user && $user->company ? $user->company->name : 'No Company';

            $this->line("- License ID: {$license->id}");
            $this->line("  User: {$userName} ({$userType})");
            $this->line("  Company: {$companyName}");
            $this->line("  Expired: {$license->end_date->format('Y-m-d')} ({$license->end_date->diffInDays($today)} days ago)");

            if (!$dryRun) {
                $license->update(['status' => 'expired']);
                $this->info("  ✓ Updated to expired status");

                // Log the change
                Log::info('License expired automatically', [
                    'license_id' => $license->id,
                    'user_id' => $license->user_id,
                    'user_name' => $userName,
                    'user_role' => $userType,
                    'company_name' => $companyName,
                    'expired_date' => $license->end_date->format('Y-m-d'),
                    'days_overdue' => $license->end_date->diffInDays($today)
                ]);
            } else {
                $this->comment("  → Would update to expired status");
            }

            $this->line('');
        }
    }

    /**
     * Check and update expired licenses in the merchants table.
     */
    private function checkMerchantsTable(Carbon $today, bool $dryRun)
    {
        $this->info('Checking merchants table...');

        // Find merchants with expired licenses that are still marked as verified
        $expiredMerchants = Merchant::where('license_verified', true)
            ->where('license_status', 'verified')
            ->where('license_expiry_date', '<', $today)
            ->get();

        if ($expiredMerchants->isEmpty()) {
            $this->info('No expired merchant licenses found.');
            return;
        }

        $this->info("Found {$expiredMerchants->count()} expired merchant licenses:");

        $updatedCount = 0;

        foreach ($expiredMerchants as $merchant) {
            $user = $merchant->user;
            $userName = $user ? $user->name : 'Unknown User';
            $daysExpired = $today->diffInDays($merchant->license_expiry_date);

            $this->line("- Merchant ID: {$merchant->id}");
            $this->line("  Business: {$merchant->business_name}");
            $this->line("  User: {$userName}");
            $this->line("  Expired: {$merchant->license_expiry_date->format('Y-m-d')} ({$daysExpired} days ago)");

            if (!$dryRun) {
                // Update merchant verification status
                $merchant->update([
                    'license_verified' => false,
                    'license_status' => 'expired',
                    'is_verified' => false, // Also update main verification status
                ]);

                $this->info("  ✓ Updated verification status to expired");
                $updatedCount++;

                // Log the change
                Log::info('Merchant license expired automatically', [
                    'merchant_id' => $merchant->id,
                    'user_id' => $merchant->user_id,
                    'user_name' => $userName,
                    'business_name' => $merchant->business_name,
                    'expired_date' => $merchant->license_expiry_date->format('Y-m-d'),
                    'days_overdue' => $daysExpired
                ]);

                // TODO: Send notification to merchant about expired license
                // This could be implemented as an email notification

            } else {
                $this->comment("  → Would update verification status to expired");
            }

            $this->line('');
        }

        if ($dryRun) {
            $this->warn("DRY RUN: {$expiredMerchants->count()} merchant(s) would be updated.");
        } else {
            $this->info("Successfully updated {$updatedCount} merchant(s) with expired licenses.");
        }

    }

    /**
     * Check and update expired licenses in the branch licenses table.
     */
    private function checkBranchLicensesTable(Carbon $today, bool $dryRun)
    {
        $this->info('Checking branch licenses table...');

        // Find active branch licenses that have expired
        $expiredBranchLicenses = BranchLicense::where('status', 'active')
            ->where('end_date', '<', $today)
            ->with(['branch', 'branch.company', 'branch.user'])
            ->get();

        if ($expiredBranchLicenses->isEmpty()) {
            $this->info('No expired branch licenses found.');
            return;
        }

        $this->info("Found {$expiredBranchLicenses->count()} expired branch licenses:");

        $updatedCount = 0;

        foreach ($expiredBranchLicenses as $branchLicense) {
            $branch = $branchLicense->branch;
            $branchName = $branch ? $branch->name : 'Unknown Branch';
            $companyName = $branch && $branch->company ? $branch->company->name : 'No Company';
            $userName = $branch && $branch->user ? $branch->user->name : 'Unknown User';
            $daysExpired = $today->diffInDays($branchLicense->end_date);

            $this->line("- Branch License ID: {$branchLicense->id}");
            $this->line("  Branch: {$branchName}");
            $this->line("  Company: {$companyName}");
            $this->line("  User: {$userName}");
            $this->line("  Expired: {$branchLicense->end_date->format('Y-m-d')} ({$daysExpired} days ago)");

            if (!$dryRun) {
                $branchLicense->update(['status' => 'expired']);
                $this->info("  ✓ Updated to expired status");
                $updatedCount++;

                // Log the change
                Log::info('Branch license expired automatically', [
                    'branch_license_id' => $branchLicense->id,
                    'branch_id' => $branchLicense->branch_id,
                    'branch_name' => $branchName,
                    'company_name' => $companyName,
                    'user_name' => $userName,
                    'expired_date' => $branchLicense->end_date->format('Y-m-d'),
                    'days_overdue' => $daysExpired
                ]);
            } else {
                $this->comment("  → Would update to expired status");
            }

            $this->line('');
        }

        if ($dryRun) {
            $this->warn("DRY RUN: {$expiredBranchLicenses->count()} branch license(s) would be updated.");
        } else {
            $this->info("Successfully updated {$updatedCount} branch license(s) with expired licenses.");
        }
    }

    /**
     * Check for licenses expiring soon and log warnings.
     */
    private function checkUpcomingExpirations($dryRun = false)
    {
        $this->info('Checking for licenses expiring within 30 days...');

        $today = Carbon::today();
        $thirtyDaysFromNow = Carbon::today()->addDays(30);

        // Check licenses table
        $expiringLicenses = License::where('status', 'active')
            ->whereBetween('end_date', [$today, $thirtyDaysFromNow])
            ->get();

        // Check merchants table
        $expiringMerchants = Merchant::where('license_verified', true)
            ->where('license_status', 'verified')
            ->whereNotNull('license_expiry_date')
            ->whereBetween('license_expiry_date', [$today, $thirtyDaysFromNow])
            ->get();

        // Check branch licenses table
        $expiringBranchLicenses = BranchLicense::where('status', 'active')
            ->whereBetween('end_date', [$today, $thirtyDaysFromNow])
            ->with(['branch', 'branch.company', 'branch.user'])
            ->get();

        $totalExpiring = $expiringLicenses->count() + $expiringMerchants->count() + $expiringBranchLicenses->count();

        if ($totalExpiring === 0) {
            $this->info('No licenses expiring within 30 days.');
            return;
        }

        $this->warn("Found {$totalExpiring} license(s) expiring within 30 days:");

        // Show expiring licenses from licenses table
        foreach ($expiringLicenses as $license) {
            $user = $license->user;
            $userType = $user ? $user->role : 'unknown';
            $userName = $user ? $user->name : 'Unknown User';
            $companyName = $user && $user->company ? $user->company->name : 'No Company';
            $daysUntilExpiry = $today->diffInDays($license->end_date);

            $this->line("  - {$userName} ({$userType}) - {$companyName}: expires {$license->end_date->format('Y-m-d')} ({$daysUntilExpiry} days)");

            // TODO: Send reminder notification to user
        }

        // Show expiring merchant licenses
        foreach ($expiringMerchants as $merchant) {
            $daysUntilExpiry = $today->diffInDays($merchant->license_expiry_date);
            $this->line("  - {$merchant->business_name} (merchant): expires {$merchant->license_expiry_date->format('Y-m-d')} ({$daysUntilExpiry} days)");

            // TODO: Send reminder notification to merchant
        }

        // Show expiring branch licenses
        foreach ($expiringBranchLicenses as $branchLicense) {
            $branch = $branchLicense->branch;
            $branchName = $branch ? $branch->name : 'Unknown Branch';
            $companyName = $branch && $branch->company ? $branch->company->name : 'No Company';
            $daysUntilExpiry = $today->diffInDays($branchLicense->end_date);

            $this->line("  - {$branchName} - {$companyName} (branch): expires {$branchLicense->end_date->format('Y-m-d')} ({$daysUntilExpiry} days)");

            // TODO: Send reminder notification to branch owner
        }
    }
}
