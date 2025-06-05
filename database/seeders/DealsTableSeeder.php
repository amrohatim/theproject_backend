<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Deal;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Carbon\Carbon;

class DealsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding deals...');

        // Get vendor users (assuming they have role 'vendor' or similar)
        $vendors = User::where('role', 'vendor')->get();
        
        if ($vendors->isEmpty()) {
            // If no vendors found, get any users
            $vendors = User::take(3)->get();
        }

        if ($vendors->isEmpty()) {
            $this->command->warn('No users found to create deals for');
            return;
        }

        // Get some categories and products for deals
        $categories = Category::where('type', 'product')->take(5)->get();
        $products = Product::take(10)->get();

        if ($categories->isEmpty() || $products->isEmpty()) {
            $this->command->warn('No categories or products found to create deals for');
            return;
        }

        $deals = [];

        foreach ($vendors as $index => $vendor) {
            // Create different types of deals for each vendor
            
            // Deal 1: Category-specific deal
            if ($categories->count() > $index) {
                $category = $categories->get($index);
                $deals[] = [
                    'user_id' => $vendor->id,
                    'title' => "Special {$category->name} Sale",
                    'description' => "Get amazing discounts on all {$category->name} products!",
                    'promotional_message' => 'Limited Time!',
                    'discount_percentage' => 20.00 + ($index * 5), // 20%, 25%, 30%
                    'start_date' => Carbon::now()->subDays(5),
                    'end_date' => Carbon::now()->addDays(30),
                    'image' => null,
                    'status' => 'active',
                    'applies_to' => 'categories',
                    'category_ids' => json_encode([$category->id]),
                    'product_ids' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Deal 2: Product-specific deal
            if ($products->count() > $index * 2) {
                $product = $products->get($index * 2);
                $deals[] = [
                    'user_id' => $vendor->id,
                    'title' => "Flash Sale: {$product->name}",
                    'description' => "Exclusive discount on {$product->name}",
                    'promotional_message' => 'Flash Sale!',
                    'discount_percentage' => 15.00 + ($index * 3), // 15%, 18%, 21%
                    'start_date' => Carbon::now()->subDays(3),
                    'end_date' => Carbon::now()->addDays(15),
                    'image' => null,
                    'status' => 'active',
                    'applies_to' => 'products',
                    'category_ids' => null,
                    'product_ids' => json_encode([$product->id]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Deal 3: All products deal (for first vendor only)
            if ($index === 0) {
                $deals[] = [
                    'user_id' => $vendor->id,
                    'title' => 'Store-wide Mega Sale',
                    'description' => 'Incredible discounts on all our products!',
                    'promotional_message' => 'Mega Sale!',
                    'discount_percentage' => 25.00,
                    'start_date' => Carbon::now()->subDays(7),
                    'end_date' => Carbon::now()->addDays(45),
                    'image' => null,
                    'status' => 'active',
                    'applies_to' => 'all',
                    'category_ids' => null,
                    'product_ids' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Insert deals
        Deal::insert($deals);

        $this->command->info('Created ' . count($deals) . ' deals successfully!');
        
        // Display created deals
        $this->command->info('Created deals:');
        foreach ($deals as $deal) {
            $this->command->info("- {$deal['title']} ({$deal['discount_percentage']}% off, applies to: {$deal['applies_to']})");
        }
    }
}
