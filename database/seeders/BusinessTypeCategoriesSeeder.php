<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BusinessType;
use App\Models\Category;

class BusinessTypeCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some categories
        $productCategories = Category::where('type', 'product')->take(5)->pluck('id')->toArray();
        $serviceCategories = Category::where('type', 'service')->take(3)->pluck('id')->toArray();

        // Update business types with categories
        $businessTypes = [
            'Restaurant' => [
                'product_categories' => array_slice($productCategories, 0, 3),
                'service_categories' => array_slice($serviceCategories, 0, 2),
            ],
            'Clothes' => [
                'product_categories' => array_slice($productCategories, 1, 4),
                'service_categories' => [],
            ],
            'Saloon' => [
                'product_categories' => array_slice($productCategories, 0, 2),
                'service_categories' => $serviceCategories,
            ],
        ];

        foreach ($businessTypes as $businessName => $categories) {
            BusinessType::where('business_name', $businessName)->update([
                'product_categories' => $categories['product_categories'],
                'service_categories' => $categories['service_categories'],
            ]);
        }
    }
}
