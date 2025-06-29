<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\SizeCategory;

class CategorySizeMappingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get size categories
        $clothesCategory = SizeCategory::where('name', 'clothes')->first();
        $shoesCategory = SizeCategory::where('name', 'shoes')->first();
        $hatsCategory = SizeCategory::where('name', 'hats')->first();

        if (!$clothesCategory || !$shoesCategory || !$hatsCategory) {
            $this->command->error('Size categories not found. Please run SizeCategoriesSeeder first.');
            return;
        }

        // Define category to size category mappings
        $categoryMappings = [
            // Clothing categories
            'Clothing' => $clothesCategory->id,
            'Fashion' => $clothesCategory->id,
            'Apparel' => $clothesCategory->id,
            'Men\'s Clothing' => $clothesCategory->id,
            'Women\'s Clothing' => $clothesCategory->id,
            'Kids\' Clothing' => $clothesCategory->id,
            'T-Shirts' => $clothesCategory->id,
            'Shirts' => $clothesCategory->id,
            'Pants' => $clothesCategory->id,
            'Jeans' => $clothesCategory->id,
            'Dresses' => $clothesCategory->id,
            'Jackets' => $clothesCategory->id,
            'Coats' => $clothesCategory->id,
            'Sweaters' => $clothesCategory->id,
            'Hoodies' => $clothesCategory->id,
            'Underwear' => $clothesCategory->id,
            'Swimwear' => $clothesCategory->id,
            'Activewear' => $clothesCategory->id,
            'Sportswear' => $clothesCategory->id,

            // Shoe categories
            'Shoes' => $shoesCategory->id,
            'Footwear' => $shoesCategory->id,
            'Sneakers' => $shoesCategory->id,
            'Boots' => $shoesCategory->id,
            'Sandals' => $shoesCategory->id,
            'Heels' => $shoesCategory->id,
            'Flats' => $shoesCategory->id,
            'Athletic Shoes' => $shoesCategory->id,
            'Running Shoes' => $shoesCategory->id,
            'Casual Shoes' => $shoesCategory->id,
            'Formal Shoes' => $shoesCategory->id,
            'Kids\' Shoes' => $shoesCategory->id,

            // Hat categories
            'Hats' => $hatsCategory->id,
            'Caps' => $hatsCategory->id,
            'Headwear' => $hatsCategory->id,
            'Baseball Caps' => $hatsCategory->id,
            'Beanies' => $hatsCategory->id,
            'Sun Hats' => $hatsCategory->id,
            'Winter Hats' => $hatsCategory->id,
            'Fedoras' => $hatsCategory->id,
            'Bucket Hats' => $hatsCategory->id,
        ];

        // Update categories with their default size categories
        foreach ($categoryMappings as $categoryName => $sizeCategoryId) {
            $categories = Category::where('name', 'LIKE', "%{$categoryName}%")
                ->where('type', 'product')
                ->get();

            foreach ($categories as $category) {
                $category->update(['default_size_category_id' => $sizeCategoryId]);
                $this->command->info("Updated category '{$category->name}' with size category ID: {$sizeCategoryId}");
            }
        }

        // Also update based on exact matches
        $exactMatches = [
            'Clothing' => $clothesCategory->id,
            'Shoes' => $shoesCategory->id,
            'Hats' => $hatsCategory->id,
        ];

        foreach ($exactMatches as $categoryName => $sizeCategoryId) {
            $category = Category::where('name', $categoryName)
                ->where('type', 'product')
                ->first();

            if ($category) {
                $category->update(['default_size_category_id' => $sizeCategoryId]);
                $this->command->info("Updated category '{$category->name}' with size category ID: {$sizeCategoryId}");
            }
        }

        $this->command->info('Category size mappings completed successfully.');
    }
}
