<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // WARNING: Truncation disabled to prevent accidental data loss
        // Only truncate if explicitly requested via --force flag
        if ($this->command->option('force')) {
            $this->command->warn('⚠️  Force flag detected - truncating all tables!');
            $this->truncateTables();
        } else {
            $this->command->info('ℹ️  Skipping truncation to preserve existing data. Use --force to truncate.');
        }

        // Run our custom seeders in the correct order
        $this->call([
            UsersTableSeeder::class,
            CategoriesTableSeeder::class, // Updated with comprehensive service categories
            CompaniesTableSeeder::class,
            BranchesTableSeeder::class,
            ComprehensiveProductSeeder::class, // Comprehensive product seeding with variants

            // Comprehensive service seeding using local images
            ComprehensiveServiceCategorySeeder::class, // Part 1: Healthcare & Beauty services
            ComprehensiveServiceSeederPart2::class, // Part 2: Fitness services
            ComprehensiveServiceSeederPart3::class, // Part 3: Personal care services
            ComprehensiveServiceSeederPart4::class, // Part 4: Makeup & nail care services
            ComprehensiveServiceSeederPart5::class, // Part 5: Nutrition, artistic & care services

            DealsTableSeeder::class, // Add deals seeder
            PaymentMethodsTableSeeder::class,
            OrdersTableSeeder::class,
            BookingsTableSeeder::class,
            ReviewsTableSeeder::class,
            FeaturedDataSeeder::class,
            ProviderSeeder::class,
            TrendingDataSeeder::class, // Add trending data seeder
        ]);
    }

    /**
     * Truncate all tables to ensure clean data.
     */
    private function truncateTables(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Truncate tables in reverse order of dependencies
        DB::table('reviews')->truncate();
        DB::table('bookings')->truncate();
        DB::table('order_items')->truncate();
        DB::table('orders')->truncate();
        DB::table('payment_methods')->truncate();
        DB::table('services')->truncate();

        // Product-related tables
        DB::table('product_color_sizes')->truncate();
        DB::table('product_sizes')->truncate();
        DB::table('product_colors')->truncate();
        DB::table('product_specifications')->truncate();
        DB::table('products')->truncate();
        DB::table('branches')->truncate();
        DB::table('companies')->truncate();
        DB::table('categories')->truncate();
        DB::table('users')->truncate();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
