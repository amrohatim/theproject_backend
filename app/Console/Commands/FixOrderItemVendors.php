<?php

namespace App\Console\Commands;

use App\Models\Branch;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixOrderItemVendors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:fix-vendors';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix vendor_id for all order items based on product branch company';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to fix vendor_id for order items...');
        
        // Get all order items
        $orderItems = OrderItem::all();
        
        $this->info("Found {$orderItems->count()} order items");
        
        $bar = $this->output->createProgressBar($orderItems->count());
        $bar->start();
        
        $updated = 0;
        $errors = 0;
        
        foreach ($orderItems as $item) {
            try {
                // Get the product
                $product = Product::find($item->product_id);
                
                if (!$product) {
                    $this->error("Product not found for order item {$item->id}");
                    $errors++;
                    $bar->advance();
                    continue;
                }
                
                // Get the branch
                $branch = Branch::find($product->branch_id);
                
                if (!$branch) {
                    $this->error("Branch not found for product {$product->id}");
                    $errors++;
                    $bar->advance();
                    continue;
                }
                
                // Get the company ID
                $companyId = $branch->company_id;
                
                // Check if vendor_id needs to be updated
                if ($item->vendor_id != $companyId) {
                    $oldVendorId = $item->vendor_id;
                    $item->vendor_id = $companyId;
                    $item->save();
                    $updated++;
                    $this->info("Updated order item {$item->id} vendor_id from {$oldVendorId} to {$companyId}");
                }
            } catch (\Exception $e) {
                $this->error("Error updating order item {$item->id}: {$e->getMessage()}");
                $errors++;
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine(2);
        
        $this->info("Updated {$updated} order items");
        
        if ($errors > 0) {
            $this->warn("Encountered {$errors} errors");
        }
        
        $this->info('Done!');
        
        return Command::SUCCESS;
    }
}
