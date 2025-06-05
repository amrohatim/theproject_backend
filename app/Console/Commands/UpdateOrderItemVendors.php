<?php

namespace App\Console\Commands;

use App\Models\Branch;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateOrderItemVendors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:update-vendors';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update vendor_id for all order items based on product branch company';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to update vendor_id for order items...');
        
        // Get all order items without vendor_id
        $orderItems = OrderItem::whereNull('vendor_id')->get();
        
        $this->info("Found {$orderItems->count()} order items without vendor_id");
        
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
                
                // Update the vendor_id
                $item->vendor_id = $branch->company_id;
                $item->save();
                
                $updated++;
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
