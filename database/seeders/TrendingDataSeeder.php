<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Services\TrendingService;

class TrendingDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding trending data for categories...');

        // Get all active categories
        $categories = Category::where('is_active', true)->get();

        if ($categories->isEmpty()) {
            $this->command->warn('No active categories found. Please run CategorySeeder first.');
            return;
        }

        // Seed some realistic view and purchase counts for categories
        $trendingData = [
            'Electronics' => ['views' => 1250, 'purchases' => 85],
            'Home & Kitchen' => ['views' => 980, 'purchases' => 67],
            'Clothing' => ['views' => 1100, 'purchases' => 92],
            'Beauty & Personal Care' => ['views' => 750, 'purchases' => 45],
            'Sports & Outdoors' => ['views' => 650, 'purchases' => 38],
            'Books' => ['views' => 420, 'purchases' => 28],
            'Toys & Games' => ['views' => 580, 'purchases' => 34],
            'Health & Wellness' => ['views' => 690, 'purchases' => 41],
            'Automotive' => ['views' => 380, 'purchases' => 22],
            'Garden & Outdoor' => ['views' => 320, 'purchases' => 18],
            
            // Service categories
            'Cleaning' => ['views' => 890, 'purchases' => 56],
            'Repair' => ['views' => 720, 'purchases' => 43],
            'Beauty & Spa' => ['views' => 650, 'purchases' => 39],
            'Installation' => ['views' => 480, 'purchases' => 29],
            'Consultation' => ['views' => 360, 'purchases' => 21],
            'Training' => ['views' => 290, 'purchases' => 16],
            'Design' => ['views' => 410, 'purchases' => 24],
            'Maintenance' => ['views' => 520, 'purchases' => 31],
        ];

        foreach ($categories as $category) {
            // Check if we have specific data for this category
            if (isset($trendingData[$category->name])) {
                $data = $trendingData[$category->name];
                $viewCount = $data['views'];
                $purchaseCount = $data['purchases'];
            } else {
                // Generate random but realistic data for other categories
                $viewCount = rand(100, 800);
                $purchaseCount = rand(10, 50);
            }

            // Update the category with trending data
            $category->update([
                'view_count' => $viewCount,
                'purchase_count' => $purchaseCount,
            ]);

            $this->command->info("Updated {$category->name}: {$viewCount} views, {$purchaseCount} purchases");
        }

        // Calculate trending scores using the TrendingService
        $this->command->info('Calculating trending scores...');
        $trendingService = new TrendingService();
        $trendingService->calculateTrendingScores();

        // Display the top trending categories
        $topTrending = Category::where('is_active', true)
            ->orderBy('trending_score', 'desc')
            ->take(10)
            ->get();

        $this->command->info('Top 10 trending categories:');
        foreach ($topTrending as $index => $category) {
            $this->command->info(
                ($index + 1) . ". {$category->name} (Score: {$category->trending_score}, Views: {$category->view_count}, Purchases: {$category->purchase_count})"
            );
        }

        $this->command->info('Trending data seeded successfully!');
    }
}
