<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Merchant;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductSize;
use App\Models\ProductColorSize;
use App\Models\Category;
use App\Models\SizeCategory;

class MerchantProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting merchant products seeder...');

        // Find the merchant with business name "amroqr"
        $merchant = Merchant::where('business_name', 'amorq')->first();

        if (!$merchant) {
            $this->command->error('Merchant "amroqr" not found!');
            return;
        }

        $this->command->info("Found merchant: {$merchant->business_name} (ID: {$merchant->id})");

        // Get all child categories (categories with parent_id NOT NULL)
        $childCategories = Category::whereNotNull('parent_id')->pluck('id')->toArray();

        if (empty($childCategories)) {
            $this->command->error('No child categories found!');
            return;
        }

        $this->command->info('Found ' . count($childCategories) . ' child categories');

        // Get size categories for variants
        $sizeCategories = SizeCategory::all();
        $this->command->info('Found ' . $sizeCategories->count() . ' size categories');

        // Product name templates
        $productNames = [
            'Premium', 'Luxury', 'Classic', 'Modern', 'Elegant', 'Stylish', 'Trendy',
            'Designer', 'Exclusive', 'Limited Edition', 'Professional', 'Essential',
            'Ultimate', 'Superior', 'Deluxe', 'Elite', 'Signature', 'Vintage'
        ];

        $productTypes = [
            'Shirt', 'Pants', 'Dress', 'Jacket', 'Shoes', 'Bag', 'Watch', 'Sunglasses',
            'Hat', 'Scarf', 'Belt', 'Wallet', 'Backpack', 'Sneakers', 'Boots', 'Sandals',
            'Jeans', 'Sweater', 'Coat', 'Blazer', 'Skirt', 'Shorts', 'T-Shirt', 'Hoodie'
        ];

        // Color options
        $colors = [
            ['name' => 'Black', 'code' => '#000000'],
            ['name' => 'White', 'code' => '#FFFFFF'],
            ['name' => 'Red', 'code' => '#FF0000'],
            ['name' => 'Blue', 'code' => '#0000FF'],
            ['name' => 'Green', 'code' => '#00FF00'],
            ['name' => 'Navy', 'code' => '#000080'],
            ['name' => 'Gray', 'code' => '#808080'],
            ['name' => 'Brown', 'code' => '#8B4513'],
            ['name' => 'Beige', 'code' => '#F5F5DC'],
            ['name' => 'Pink', 'code' => '#FFC0CB'],
        ];

        // Size options by category
        $sizesByCategory = [
            'clothes' => ['XS', 'S', 'M', 'L', 'XL', 'XXL'],
            'shoes' => ['36', '37', '38', '39', '40', '41', '42', '43', '44', '45'],
            'hats' => ['S', 'M', 'L', 'XL'],
        ];

        $progressBar = $this->command->getOutput()->createProgressBar(500);
        $progressBar->start();

        DB::beginTransaction();

        try {
            for ($i = 1; $i <= 500; $i++) {
                // Generate product name
                $name = $productNames[array_rand($productNames)] . ' ' . 
                        $productTypes[array_rand($productTypes)] . ' ' . 
                        $i;

                // Generate description
                $description = "High-quality {$name} with excellent craftsmanship and attention to detail. " .
                              "Perfect for everyday wear or special occasions. Made from premium materials " .
                              "that ensure durability and comfort.";

                // Random price between 50 and 500
                $price = rand(50, 500);
                $originalPrice = $price + rand(10, 100);

                // Random stock between 10 and 200
                $stock = rand(10, 200);

                // Random category
                $categoryId = $childCategories[array_rand($childCategories)];

                // Generate unique image seed for Picsum
                $imageSeed = "product-{$merchant->id}-{$i}-" . time();
                $imageUrl = "https://picsum.photos/seed/{$imageSeed}/800/800";

                // Create product (merchants don't have branches, so branch_id should be null)
                $product = Product::create([
                    'branch_id' => null, // Merchants don't have branches
                    'merchant_id' => $merchant->id,
                    'user_id' => $merchant->user_id,
                    'category_id' => $categoryId,
                    'name' => $name,
                    'description' => $description,
                    'price' => $price,
                    'original_price' => $originalPrice,
                    'stock' => $stock,
                    'sku' => 'SKU-' . strtoupper(uniqid()),
                    'image' => $imageUrl,
                    'rating' => rand(35, 50) / 10, // 3.5 to 5.0
                    'is_available' => true,
                    'featured' => rand(0, 10) > 7, // 30% chance of being featured
                    'is_merchant' => true,
                    'merchant_name' => $merchant->business_name,
                ]);

                // Randomly decide on variants (40% chance of having variants)
                if (rand(1, 100) <= 40) {
                    $variantType = rand(1, 4);

                    switch ($variantType) {
                        case 1: // Colors only
                            $this->addColorsOnly($product, $colors, $stock);
                            break;

                        case 2: // Sizes only
                            $this->addSizesOnly($product, $sizesByCategory, $sizeCategories, $stock);
                            break;

                        case 3: // Both colors and sizes
                            $this->addColorsAndSizes($product, $colors, $sizesByCategory, $sizeCategories, $stock);
                            break;

                        case 4: // Neither (simple product)
                        default:
                            // No variants
                            break;
                    }
                }

                $progressBar->advance();
            }

            DB::commit();
            $progressBar->finish();
            $this->command->newLine();
            $this->command->info('Successfully seeded 500 products for merchant "amroqr"!');

        } catch (\Exception $e) {
            DB::rollBack();
            $progressBar->finish();
            $this->command->newLine();
            $this->command->error('Error seeding products: ' . $e->getMessage());
            $this->command->error($e->getTraceAsString());
        }
    }

    /**
     * Add colors only to a product
     */
    private function addColorsOnly($product, $colors, $totalStock)
    {
        $numColors = rand(2, 4);
        $selectedColors = array_rand($colors, $numColors);
        $stockPerColor = intval($totalStock / $numColors);

        foreach ($selectedColors as $index => $colorIndex) {
            $color = $colors[$colorIndex];
            $isDefault = $index === 0;

            ProductColor::create([
                'product_id' => $product->id,
                'name' => $color['name'],
                'color_code' => $color['code'],
                'stock' => $stockPerColor,
                'price_adjustment' => 0,
                'display_order' => $index,
                'is_default' => $isDefault,
                'image' => null,
            ]);
        }
    }

    /**
     * Add sizes only to a product
     */
    private function addSizesOnly($product, $sizesByCategory, $sizeCategories, $totalStock)
    {
        $categoryName = array_rand($sizesByCategory);
        $sizes = $sizesByCategory[$categoryName];
        $numSizes = rand(3, count($sizes));
        $selectedSizes = array_rand(array_flip($sizes), $numSizes);
        
        if (!is_array($selectedSizes)) {
            $selectedSizes = [$selectedSizes];
        }

        $stockPerSize = intval($totalStock / count($selectedSizes));

        // Get size category ID
        $sizeCategory = $sizeCategories->where('name', $categoryName)->first();
        $sizeCategoryId = $sizeCategory ? $sizeCategory->id : null;

        foreach ($selectedSizes as $index => $sizeName) {
            ProductSize::create([
                'product_id' => $product->id,
                'size_category_id' => $sizeCategoryId,
                'name' => $sizeName,
                'value' => $sizeName,
                'stock' => $stockPerSize,
                'price_adjustment' => 0,
                'display_order' => $index,
                'is_default' => $index === 0,
            ]);
        }
    }

    /**
     * Add both colors and sizes to a product
     */
    private function addColorsAndSizes($product, $colors, $sizesByCategory, $sizeCategories, $totalStock)
    {
        // Add colors
        $numColors = rand(2, 3);
        $selectedColors = array_rand($colors, $numColors);
        
        if (!is_array($selectedColors)) {
            $selectedColors = [$selectedColors];
        }

        $colorModels = [];
        foreach ($selectedColors as $index => $colorIndex) {
            $color = $colors[$colorIndex];
            $colorStock = intval($totalStock / $numColors);

            $colorModel = ProductColor::create([
                'product_id' => $product->id,
                'name' => $color['name'],
                'color_code' => $color['code'],
                'stock' => $colorStock,
                'price_adjustment' => 0,
                'display_order' => $index,
                'is_default' => $index === 0,
                'image' => null,
            ]);

            $colorModels[] = $colorModel;
        }

        // Add sizes
        $categoryName = array_rand($sizesByCategory);
        $sizes = $sizesByCategory[$categoryName];
        $numSizes = rand(3, min(5, count($sizes)));
        $selectedSizes = array_rand(array_flip($sizes), $numSizes);
        
        if (!is_array($selectedSizes)) {
            $selectedSizes = [$selectedSizes];
        }

        // Get size category ID
        $sizeCategory = $sizeCategories->where('name', $categoryName)->first();
        $sizeCategoryId = $sizeCategory ? $sizeCategory->id : null;

        $sizeModels = [];
        foreach ($selectedSizes as $index => $sizeName) {
            $sizeModel = ProductSize::create([
                'product_id' => $product->id,
                'size_category_id' => $sizeCategoryId,
                'name' => $sizeName,
                'value' => $sizeName,
                'stock' => 0, // Stock is managed in color-size combinations
                'price_adjustment' => 0,
                'display_order' => $index,
                'is_default' => $index === 0,
            ]);

            $sizeModels[] = $sizeModel;
        }

        // Create color-size combinations
        foreach ($colorModels as $colorModel) {
            $stockPerSize = intval($colorModel->stock / count($sizeModels));

            foreach ($sizeModels as $sizeModel) {
                ProductColorSize::create([
                    'product_id' => $product->id,
                    'product_color_id' => $colorModel->id,
                    'product_size_id' => $sizeModel->id,
                    'stock' => $stockPerSize,
                    'price_adjustment' => 0,
                    'is_available' => true,
                ]);
            }
        }
    }
}

