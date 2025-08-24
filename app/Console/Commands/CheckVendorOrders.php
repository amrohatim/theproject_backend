<?php

namespace App\Console\Commands;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use Illuminate\Console\Command;

class CheckVendorOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:check-vendor {vendor_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check orders for a vendor';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $vendorId = $this->argument('vendor_id');
        
        if ($vendorId) {
            $vendors = User::where('id', $vendorId)->where('role', 'vendor')->get();
        } else {
            $vendors = User::where('role', 'vendor')->get();
        }
        
        foreach ($vendors as $vendor) {
            $this->info("Checking orders for vendor: {$vendor->name} (ID: {$vendor->id})");
            
            $company = Company::where('user_id', $vendor->id)->first();
            
            if (!$company) {
                $this->warn("No company found for vendor {$vendor->name}");
                continue;
            }
            
            $this->info("Company: {$company->name} (ID: {$company->id})");
            
            $branchIds = Branch::where('company_id', $company->id)->pluck('id')->toArray();
            
            if (empty($branchIds)) {
                $this->warn("No branches found for company {$company->name}");
                continue;
            }
            
            $this->info("Branch IDs: " . implode(', ', $branchIds));
            
            // Check orders for branches
            $branchOrders = Order::whereIn('branch_id', $branchIds)->get();
            $this->info("Orders for branches: {$branchOrders->count()}");
            
            // Check orders with items for this vendor
            $itemOrders = Order::whereHas('items', function($query) use ($company) {
                $query->where('vendor_id', $company->id);
            })->get();
            $this->info("Orders with items for vendor: {$itemOrders->count()}");
            
            // Combined query
            $combinedOrders = Order::where(function($q) use ($branchIds, $company) {
                $q->whereIn('branch_id', $branchIds)
                  ->orWhereHas('items', function($itemQuery) use ($company) {
                      $itemQuery->where('vendor_id', $company->id);
                  });
            })->get();
            $this->info("Combined orders: {$combinedOrders->count()}");
            
            // List the orders
            $this->info("Order IDs: " . implode(', ', $combinedOrders->pluck('id')->toArray()));
            
            $this->newLine();
        }
        
        return Command::SUCCESS;
    }
}
