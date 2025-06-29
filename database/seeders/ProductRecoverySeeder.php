<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Branch;
use App\Models\ProductSpecification;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProductRecoverySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔄 Starting product recovery...');
        
        // Get all branches and categories
        $branches = Branch::all();
        $categories = Category::where('type', 'product')->whereNotNull('parent_id')->get();

        if ($branches->isEmpty()) {
            $this->command->error('No branches found. Cannot create products.');
            return;
        }

        if ($categories->isEmpty()) {
            $this->command->error('No product categories found. Cannot create products.');
            return;
        }

        $this->command->info("Found {$branches->count()} branches and {$categories->count()} categories");

        $productImagesPath = base_path('Products images');
        
        if (!File::exists($productImagesPath)) {
            $this->command->error('Products images directory not found.');
            return;
        }

        $imageFiles = File::files($productImagesPath);
        $createdProducts = 0;
        $skippedProducts = 0;

        $this->command->info("Found " . count($imageFiles) . " product image files");

        foreach ($imageFiles as $imageFile) {
            $filename = $imageFile->getFilenameWithoutExtension();
            
            // Parse product information from filename
            $productInfo = $this->parseProductFromFilename($filename);
            
            if (!$productInfo) {
                $skippedProducts++;
                continue;
            }

            // Find matching category
            $category = $this->findMatchingCategory($productInfo['category'], $categories);
            
            if (!$category) {
                // Try to find a default category
                $category = $categories->first();
                if (!$category) {
                    $skippedProducts++;
                    continue;
                }
            }

            // Check if product already exists
            $existingProduct = Product::where('name', $productInfo['name'])
                ->where('category_id', $category->id)
                ->first();

            if ($existingProduct) {
                continue; // Skip if product already exists
            }

            // Create the product
            $branch = $branches->random();
            
            try {
                $product = Product::create([
                    'name' => $productInfo['name'],
                    'description' => $this->generateProductDescription($productInfo),
                    'price' => $productInfo['price'],
                    'original_price' => $productInfo['original_price'],
                    'stock' => $productInfo['stock'],
                    'image' => "Products images/{$imageFile->getFilename()}",
                    'branch_id' => $branch->id,
                    'category_id' => $category->id,
                    'sku' => $this->generateSKU($category->name, $productInfo['name']),
                    'rating' => $productInfo['rating'],
                    'is_available' => true,
                    'featured' => $productInfo['featured'],
                ]);

                // Add product specifications
                $this->addProductSpecifications($product, $productInfo);

                $createdProducts++;
                
                if ($createdProducts % 10 == 0) {
                    $this->command->info("Created {$createdProducts} products...");
                }
            } catch (\Exception $e) {
                $this->command->warn("Failed to create product {$productInfo['name']}: " . $e->getMessage());
                $skippedProducts++;
            }
        }

        $this->command->info("✅ Product recovery completed!");
        $this->command->info("   - Created: {$createdProducts} products");
        $this->command->info("   - Skipped: {$skippedProducts} products");
        
        $totalProducts = Product::count();
        $this->command->info("   - Total products in database: {$totalProducts}");
        
        if ($totalProducts >= 102) {
            $this->command->info("🎉 SUCCESS! Database now has {$totalProducts} products (target: 102+)");
        } else {
            $needed = 102 - $totalProducts;
            $this->command->warn("⚠️  Need {$needed} more products to reach target of 102+");
        }
    }

    /**
     * Parse product information from filename
     */
    private function parseProductFromFilename(string $filename): ?array
    {
        // Clean up the filename
        $cleanName = str_replace(['_', '-'], ' ', $filename);
        
        // Extract color if present (usually at the end)
        $color = '';
        $productName = $cleanName;
        
        // Common color patterns
        $colors = ['black', 'white', 'red', 'blue', 'green', 'yellow', 'orange', 'purple', 'pink', 'brown', 'gray', 'grey', 'violet', 'cyan', 'gold', 'silver', 'navy', 'darkblue', 'lightblue', 'darkgreen', 'lightgreen', 'darkred', 'lightyellow', 'darkorange', 'lightgray', 'darkgray'];
        
        foreach ($colors as $colorName) {
            if (Str::endsWith(strtolower($cleanName), ' ' . $colorName)) {
                $color = $colorName;
                $productName = trim(str_replace(' ' . $colorName, '', $cleanName));
                break;
            }
        }
        
        // Determine quality (Premium or Classic)
        $quality = 'Classic';
        if (Str::startsWith($productName, 'Premium ')) {
            $quality = 'Premium';
            $productName = str_replace('Premium ', '', $productName);
        } elseif (Str::startsWith($productName, 'Classic ')) {
            $quality = 'Classic';
            $productName = str_replace('Classic ', '', $productName);
        }
        
        // Determine category from product name
        $category = $this->determineCategoryFromProductName($productName);
        
        if (!$category) {
            return null;
        }

        // Generate pricing based on quality
        $basePrice = $this->generateBasePrice($category, $quality);
        
        return [
            'name' => $productName,
            'category' => $category,
            'color' => $color,
            'quality' => $quality,
            'price' => $basePrice,
            'original_price' => $quality === 'Premium' ? round($basePrice * 1.3, 2) : null,
            'stock' => rand(15, 85),
            'rating' => rand(35, 50) / 10,
            'featured' => rand(1, 10) <= 2, // 20% chance
        ];
    }

    /**
     * Determine category from product name
     */
    private function determineCategoryFromProductName(string $productName): ?string
    {
        $categoryMappings = [
            // Clothing
            'Activewear' => 'Activewear',
            'Dress' => 'Dresses',
            'Maxi Dress' => 'Dresses',
            'Summer Dress' => 'Dresses',
            'Loungewear' => 'Loungewear',
            'Outerwear' => 'Outerwear (jackets, coats)',
            'Maternity wear' => 'Maternity wear',
            'Maternity clothing' => 'Maternity clothing',
            'Bottoms' => 'Bottoms (jeans, skirts)',
            'Tops' => 'Tops (blouses, tunics)',
            
            // Footwear
            'Sneakers' => 'Sneakers',
            'Running Sneakers' => 'Sneakers',
            'Canvas Sneakers' => 'Sneakers',
            'Athletic' => 'Sneakers',
            'Boots' => 'Boots',
            'Flats' => 'Flats',
            'Heels' => 'Heels',
            'Stiletto Heels' => 'Heels',
            'Sandals' => 'Sandals',
            
            // Ethnic & Traditional
            'Abaya' => 'Abayas',
            'Hijab' => 'Pray Clothes',
            'Kaftans' => 'Kaftans',
            'Salwar Kameez' => 'Salwar Kameez',
            'Sarees' => 'Sarees',
            'Pray Clothes' => 'Pray Clothes',
            
            // Accessories
            'Belts' => 'Belts',
            'Hats' => 'Hats',
            'Hat' => 'Hats',
            'Scarves' => 'Scarves',
            'Sunglasses' => 'Sunglasses',
            
            // Bags
            'Backpacks' => 'Backpacks',
            'Handbag' => 'Crossbody bags',
            'Leather Handbag' => 'Crossbody bags',
            'Crossbody bags' => 'Crossbody bags',
            'Tote bags' => 'Tote bags',
            
            // Jewelry
            'Anklets' => 'Anklets',
            'Bracelets' => 'Bracelets',
            'Earrings' => 'Earrings',
            'Necklaces' => 'Necklaces',
            'Rings' => 'Rings',
            
            // Beauty & Makeup
            'Foundation' => 'Foundations',
            'Blushes' => 'Blushes',
            'Eyeshadows' => 'Eyeshadows',
            'Lipsticks' => 'Lipsticks',
            'Mascaras' => 'Mascaras',
            'Perfume' => 'Perfumes',
            'Rose Perfume' => 'Perfumes',
            'Oriental Perfume' => 'Perfumes',
            
            // Skincare
            'Cleansers' => 'Cleansers',
            'Face masks' => 'Face masks',
            'Moisturizers' => 'Moisturizers',
            'Serums' => 'Serums',
            'Sunscreens' => 'Sunscreens',
            
            // Haircare
            'Shampoo' => 'Shampoos',
            'Conditioners' => 'Conditioners',
            'Hair oils' => 'Hair oils',
            
            // Hair Accessories
            'Clips' => 'Clips',
            'Hairbands' => 'Hairbands',
            'Scrunchies' => 'Scrunchies',
            
            // Intimates
            'Bras' => 'Bras',
            'Nursing bras' => 'Nursing bras',
            'Lingerie' => 'Lingerie',
            'Panties' => 'Panties',
            'Shapewear' => 'Shapewear',
            
            // Baby & Maternity
            'Belly support belts' => 'Belly support belts',
            'Onesies' => 'Onesies',
            'Baby carriers' => 'Baby carriers',
            'Car seats' => 'Car seats',
            'Strollers' => 'Strollers',
            'Bottles' => 'Bottles',
            'Breast pumps' => 'Breast pumps',
            'High chairs' => 'High chairs',
            'Sterilizers' => 'Sterilizers',
            
            // Watches
            'Analog' => 'Analog',
            'Digital' => 'Digital',
            'Smartwatches' => 'Smartwatches',
            
            // Fragrances
            'Body mists' => 'Body mists',
            'Deodorants' => 'Deodorants',
        ];

        foreach ($categoryMappings as $keyword => $category) {
            if (Str::contains($productName, $keyword)) {
                return $category;
            }
        }

        return null;
    }

    /**
     * Find matching category from available categories
     */
    private function findMatchingCategory(string $categoryName, $categories)
    {
        return $categories->first(function ($category) use ($categoryName) {
            return $category->name === $categoryName;
        });
    }

    /**
     * Generate base price based on category and quality
     */
    private function generateBasePrice(string $category, string $quality): float
    {
        $basePrices = [
            // Clothing
            'Activewear' => [25, 85],
            'Dresses' => [35, 120],
            'Loungewear' => [20, 60],
            'Outerwear (jackets, coats)' => [45, 150],
            'Maternity wear' => [30, 90],
            'Maternity clothing' => [30, 90],
            'Bottoms (jeans, skirts)' => [25, 80],
            'Tops (blouses, tunics)' => [20, 70],

            // Footwear
            'Sneakers' => [40, 120],
            'Boots' => [50, 150],
            'Flats' => [25, 80],
            'Heels' => [35, 100],
            'Sandals' => [20, 70],

            // Accessories & Bags
            'Belts' => [15, 50],
            'Hats' => [15, 45],
            'Scarves' => [20, 60],
            'Sunglasses' => [25, 80],
            'Backpacks' => [30, 90],
            'Crossbody bags' => [35, 120],
            'Tote bags' => [25, 85],

            // Jewelry
            'Anklets' => [10, 35],
            'Bracelets' => [15, 50],
            'Earrings' => [12, 40],
            'Necklaces' => [20, 70],
            'Rings' => [15, 55],

            // Beauty & Makeup
            'Foundations' => [15, 45],
            'Blushes' => [12, 35],
            'Eyeshadows' => [10, 30],
            'Lipsticks' => [8, 25],
            'Mascaras' => [10, 30],
            'Perfumes' => [25, 80],

            // Default ranges
            'default' => [15, 60],
        ];

        $priceRange = $basePrices[$category] ?? $basePrices['default'];
        $basePrice = rand($priceRange[0] * 100, $priceRange[1] * 100) / 100;

        // Adjust for quality
        if ($quality === 'Premium') {
            $basePrice *= 1.5;
        }

        return round($basePrice, 2);
    }

    /**
     * Generate product description
     */
    private function generateProductDescription(array $productInfo): string
    {
        $quality = $productInfo['quality'];
        $category = $productInfo['category'];
        $color = $productInfo['color'];

        $descriptions = [
            'Premium' => [
                'High-quality {category} crafted with premium materials and attention to detail.',
                'Luxurious {category} designed for comfort and style.',
                'Premium {category} featuring superior craftsmanship and elegant design.',
            ],
            'Classic' => [
                'Classic {category} with timeless design and reliable quality.',
                'Traditional {category} perfect for everyday wear.',
                'Comfortable {category} with classic styling.',
            ]
        ];

        $templates = $descriptions[$quality] ?? $descriptions['Classic'];
        $template = $templates[array_rand($templates)];

        $description = str_replace('{category}', strtolower($category), $template);

        if ($color) {
            $description .= " Available in beautiful {$color} color.";
        }

        return $description;
    }

    /**
     * Generate SKU for product
     */
    private function generateSKU(string $categoryName, string $productName): string
    {
        $categoryCode = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $categoryName), 0, 3));
        $productCode = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $productName), 0, 3));
        $randomNumber = str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);

        return "{$categoryCode}{$productCode}{$randomNumber}";
    }

    /**
     * Add product specifications
     */
    private function addProductSpecifications(Product $product, array $productInfo): void
    {
        $specifications = [
            'Quality' => $productInfo['quality'],
            'Brand' => $productInfo['quality'] . ' Brand',
        ];

        if ($productInfo['color']) {
            $specifications['Color'] = ucfirst($productInfo['color']);
        }

        // Add category-specific specifications
        $categorySpecs = $this->getCategorySpecifications($productInfo['category']);
        $specifications = array_merge($specifications, $categorySpecs);

        foreach ($specifications as $name => $value) {
            try {
                ProductSpecification::create([
                    'product_id' => $product->id,
                    'name' => $name,
                    'value' => $value,
                ]);
            } catch (\Exception $e) {
                // Ignore specification creation errors
            }
        }
    }

    /**
     * Get category-specific specifications
     */
    private function getCategorySpecifications(string $category): array
    {
        $specs = [
            // Clothing
            'Activewear' => ['Material' => 'Polyester Blend', 'Care' => 'Machine Washable'],
            'Dresses' => ['Material' => 'Cotton Blend', 'Fit' => 'Regular'],
            'Loungewear' => ['Material' => 'Cotton', 'Comfort' => 'Soft'],

            // Footwear
            'Sneakers' => ['Material' => 'Synthetic', 'Sole' => 'Rubber'],
            'Boots' => ['Material' => 'Leather', 'Height' => 'Ankle'],
            'Heels' => ['Height' => 'Medium', 'Material' => 'Synthetic'],

            // Beauty
            'Foundations' => ['Coverage' => 'Medium', 'Finish' => 'Natural'],
            'Perfumes' => ['Type' => 'Eau de Parfum', 'Size' => '50ml'],

            // Default
            'default' => ['Origin' => 'Imported', 'Warranty' => '30 Days'],
        ];

        return $specs[$category] ?? $specs['default'];
    }
}
