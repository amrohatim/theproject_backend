<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FeaturedDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mark some products as featured
        $this->markFeaturedProducts();

        // Mark some services as featured
        $this->markFeaturedServices();

        // Mark some branches as featured
        $this->markFeaturedBranches();

        $this->command->info('Featured products, services, and branches have been marked successfully!');
    }

    /**
     * Mark some products as featured
     */
    private function markFeaturedProducts(): void
    {
        // Get 5 random products to mark as featured
        $products = Product::inRandomOrder()->take(5)->get();

        foreach ($products as $product) {
            $product->featured = true;
            $product->save();
            $this->command->info("Product '{$product->name}' marked as featured.");
        }
    }

    /**
     * Mark some services as featured
     */
    private function markFeaturedServices(): void
    {
        // Get 5 random services to mark as featured
        $services = Service::inRandomOrder()->take(5)->get();

        foreach ($services as $service) {
            $service->featured = true;
            $service->save();
            $this->command->info("Service '{$service->name}' marked as featured.");
        }
    }

    /**
     * Mark some branches as featured
     */
    private function markFeaturedBranches(): void
    {
        // Get 4 random branches to mark as featured
        $branches = Branch::inRandomOrder()->take(4)->get();

        foreach ($branches as $branch) {
            $branch->featured = true;
            $branch->save();
            $this->command->info("Branch '{$branch->name}' marked as featured.");
        }
    }
}
