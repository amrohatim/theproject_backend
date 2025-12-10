<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\Category;

class VendorProductsWithColorsSeeder extends Seeder
{
    private $vendor;
    private $company;
    private $branches;
    private $categories;
    private $colorOptions;
    private $productNames;
    private $descriptions;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting vendor products with colors seeding...');
        
        // Initialize data
        $this->initializeVendorData();
        $this->initializeColorOptions();
        $this->initializeProductData();
        
        // Seed products in batches
        $this->seedProducts();
        
        $this->command->info('✅ Vendor products with colors seeding completed successfully!');
    }

    /**
     * Initialize vendor data
     */
    private function initializeVendorData(): void
    {
        // Find the vendor user
        $this->vendor = User::where('email', 'gogoh3296@gmail.com')->first();
        
        if (!$this->vendor) {
            $this->command->error('❌ Vendor user with email gogoh3296@gmail.com not found!');
            $this->command->info('Please ensure the vendor user exists before running this seeder.');
            return;
        }

        $this->command->info("✅ Found vendor: {$this->vendor->name} (ID: {$this->vendor->id})");

        // Get vendor's company
        $this->company = $this->vendor->company;
        if (!$this->company) {
            $this->command->error('❌ Vendor has no company associated!');
            return;
        }

        $this->command->info("✅ Found company: {$this->company->name} (ID: {$this->company->id})");

        // Get company branches
        $this->branches = $this->company->branches;
        if ($this->branches->isEmpty()) {
            $this->command->error('❌ No branches found for the vendor company!');
            return;
        }

        $this->command->info("✅ Found {$this->branches->count()} branch(es)");

        // Get leaf categories (categories that can be selected for products)
        $this->categories = Category::where('type', 'product')
            ->whereNotNull('parent_id')
            ->whereDoesntHave('children')
            ->get();

        if ($this->categories->isEmpty()) {
            $this->command->error('❌ No leaf categories found for products!');
            return;
        }

        $this->command->info("✅ Found {$this->categories->count()} product categories");
    }

    /**
     * Initialize color options
     */
    private function initializeColorOptions(): void
    {
        $this->colorOptions = [
            ['name' => 'DarkRed', 'name_arabic' => 'أحمر داكن', 'code' => '#8B0000'],
            ['name' => 'IndianRed', 'name_arabic' => 'أحمر هندي', 'code' => '#CD5C5C'],
            ['name' => 'LightCoral', 'name_arabic' => 'كورال فاتح', 'code' => '#F08080'],
            ['name' => 'Salmon', 'name_arabic' => 'سلموني', 'code' => '#FA8072'],
            ['name' => 'DarkSalmon', 'name_arabic' => 'سلموني داكن', 'code' => '#E9967A'],
            ['name' => 'LightSalmon', 'name_arabic' => 'سلموني فاتح', 'code' => '#FFA07A'],
            ['name' => 'Orange', 'name_arabic' => 'برتقالي', 'code' => '#FFA500'],
            ['name' => 'DarkOrange', 'name_arabic' => 'برتقالي داكن', 'code' => '#FF8C00'],
            ['name' => 'Coral', 'name_arabic' => 'كورال', 'code' => '#FF7F50'],
            ['name' => 'Red', 'name_arabic' => 'أحمر', 'code' => '#FF0000'],
            ['name' => 'Blue', 'name_arabic' => 'أزرق', 'code' => '#0000FF'],
            ['name' => 'Green', 'name_arabic' => 'أخضر', 'code' => '#008000'],
            ['name' => 'Navy Blue', 'name_arabic' => 'أزرق كحلي', 'code' => '#000080'],
            ['name' => 'Forest Green', 'name_arabic' => 'أخضر غامق', 'code' => '#228B22'],
            ['name' => 'Black', 'name_arabic' => 'أسود', 'code' => '#000000'],
            ['name' => 'White', 'name_arabic' => 'أبيض', 'code' => '#FFFFFF'],
            ['name' => 'Gray', 'name_arabic' => 'رمادي', 'code' => '#808080'],
            ['name' => 'Yellow', 'name_arabic' => 'أصفر', 'code' => '#FFFF00'],
            ['name' => 'Purple', 'name_arabic' => 'بنفسجي', 'code' => '#800080'],
            ['name' => 'Pink', 'name_arabic' => 'وردي', 'code' => '#FFC0CB'],
            ['name' => 'Brown', 'name_arabic' => 'بني', 'code' => '#A52A2A'],
            ['name' => 'Silver', 'name_arabic' => 'فضي', 'code' => '#C0C0C0'],
            ['name' => 'Gold', 'name_arabic' => 'ذهبي', 'code' => '#FFD700'],
            ['name' => 'Maroon', 'name_arabic' => 'خمري', 'code' => '#800000'],
            ['name' => 'Teal', 'name_arabic' => 'تركوازي', 'code' => '#008080'],
            ['name' => 'Olive', 'name_arabic' => 'زيتي', 'code' => '#808000'],
            ['name' => 'Lime', 'name_arabic' => 'ليموني', 'code' => '#00FF00'],
            ['name' => 'Aqua', 'name_arabic' => 'فيروزي', 'code' => '#00FFFF'],
            ['name' => 'Fuchsia', 'name_arabic' => 'فوشيا', 'code' => '#FF00FF'],
        ];
    }

    /**
     * Initialize product data arrays
     */
    private function initializeProductData(): void
    {
        $this->productNames = [
            'Premium Cotton T-Shirt', 'Luxury Silk Scarf', 'Designer Handbag', 'Wireless Bluetooth Headphones',
            'Organic Face Cream', 'Stainless Steel Water Bottle', 'Leather Wallet', 'Smartphone Case',
            'Running Shoes', 'Yoga Mat', 'Coffee Mug', 'Sunglasses', 'Watch', 'Backpack', 'Perfume',
            'Notebook', 'Pen Set', 'Candle', 'Phone Charger', 'Bluetooth Speaker', 'Fitness Tracker',
            'Hair Dryer', 'Electric Toothbrush', 'Moisturizer', 'Lip Balm', 'Hand Cream', 'Shampoo',
            'Conditioner', 'Body Wash', 'Deodorant', 'Razor', 'Shaving Cream', 'Aftershave',
            'Cologne', 'Earrings', 'Necklace', 'Bracelet', 'Ring', 'Keychain', 'Umbrella',
            'Gloves', 'Hat', 'Scarf', 'Belt', 'Tie', 'Socks', 'Underwear', 'Pajamas', 'Robe',
            'Slippers', 'Sandals', 'Boots', 'Sneakers', 'Dress Shoes', 'Casual Shoes', 'Flip Flops'
        ];

        $this->descriptions = [
            'High-quality product made with premium materials and excellent craftsmanship.',
            'Stylish and functional design that meets modern lifestyle needs.',
            'Durable construction ensures long-lasting performance and reliability.',
            'Elegant design with attention to detail and superior quality.',
            'Perfect for everyday use with comfortable and practical features.',
            'Modern style combined with traditional quality and craftsmanship.',
            'Innovative design that offers both functionality and aesthetic appeal.',
            'Premium quality materials ensure durability and long-term satisfaction.',
            'Carefully crafted with precision and attention to every detail.',
            'Versatile product suitable for various occasions and uses.',
        ];
    }

    /**
     * Seed products in batches
     */
    private function seedProducts(): void
    {
        $totalProducts = 2000;
        $batchSize = 100;
        $batches = ceil($totalProducts / $batchSize);

        $this->command->info("Creating {$totalProducts} products in {$batches} batches of {$batchSize}...");

        for ($batch = 0; $batch < $batches; $batch++) {
            $startIndex = $batch * $batchSize;
            $endIndex = min($startIndex + $batchSize, $totalProducts);

            $this->command->info("Processing batch " . ($batch + 1) . "/{$batches} (Products {$startIndex}-{$endIndex})...");

            DB::beginTransaction();
            try {
                for ($i = $startIndex; $i < $endIndex; $i++) {
                    $this->createProductWithColor($i + 1);
                }
                DB::commit();
                $this->command->info("✅ Batch " . ($batch + 1) . " completed successfully!");
            } catch (\Exception $e) {
                DB::rollBack();
                $this->command->error("❌ Error in batch " . ($batch + 1) . ": " . $e->getMessage());
                Log::error('VendorProductsWithColorsSeeder batch error', [
                    'batch' => $batch + 1,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                break;
            }
        }
    }

    /**
     * Create a single product with one color variant
     */
    private function createProductWithColor(int $productNumber): void
    {
        // Generate product data
        $productName = $this->generateProductName($productNumber);
        $productNameArabic = $this->generateArabicName($productName);
        $description = $this->descriptions[array_rand($this->descriptions)];
        $descriptionArabic = $this->generateArabicDescription($description);
        $price = $this->generatePrice();
        $originalPrice = $price + rand(5, 50);
        $stock = rand(10, 100);
        $sku = $this->generateSKU($productName, $productNumber);

        // Select random branch and category
        $branch = $this->branches->random();
        $category = $this->categories->random();

        // Create the product
        $product = Product::create([
            'user_id' => $this->vendor->id,
            'branch_id' => $branch->id,
            'category_id' => $category->id,
            'name' => $productName,
            'product_name_arabic' => $productNameArabic,
            'description' => $description,
            'product_description_arabic' => $descriptionArabic,
            'price' => $price,
            'original_price' => $originalPrice,
            'stock' => $stock,
            'sku' => $sku,
            'image' => $this->generateRandomImage(),
            'is_available' => true,
            'featured' => rand(1, 10) === 1, // 10% chance of being featured
            'rating' => round(rand(35, 50) / 10, 1), // Rating between 3.5 and 5.0
        ]);

        // Create one color variant for the product
        $this->createColorVariant($product, $stock);
    }

    /**
     * Create a color variant for the product
     */
    private function createColorVariant(Product $product, int $totalStock): void
    {
        $colorOption = $this->colorOptions[array_rand($this->colorOptions)];
        $colorStock = rand(5, $totalStock);
        $priceAdjustment = rand(0, 20);

        ProductColor::create([
            'product_id' => $product->id,
            'name' => $colorOption['name'],
            'name_arabic' => $colorOption['name_arabic'],
            'color_code' => $colorOption['code'],
            'image' => $this->generateRandomImage(),
            'price_adjustment' => $priceAdjustment,
            'stock' => $colorStock,
            'display_order' => 0,
            'is_default' => true, // Since it's the only color, it's default
        ]);
    }

    /**
     * Generate a unique product name
     */
    private function generateProductName(int $productNumber): string
    {
        $baseName = $this->productNames[array_rand($this->productNames)];
        $adjectives = ['Premium', 'Luxury', 'Professional', 'Classic', 'Modern', 'Elegant', 'Stylish', 'Comfort', 'Deluxe', 'Superior'];
        $adjective = $adjectives[array_rand($adjectives)];

        return "{$adjective} {$baseName} #{$productNumber}";
    }

    /**
     * Generate Arabic name (simplified)
     */
    private function generateArabicName(string $englishName): string
    {
        $arabicNames = [
            'منتج فاخر', 'منتج عالي الجودة', 'منتج مميز', 'منتج أنيق', 'منتج حديث',
            'منتج كلاسيكي', 'منتج مريح', 'منتج متطور', 'منتج راقي', 'منتج متميز'
        ];

        return $arabicNames[array_rand($arabicNames)] . ' ' . rand(1, 2000);
    }

    /**
     * Generate Arabic description (simplified)
     */
    private function generateArabicDescription(string $englishDescription): string
    {
        $arabicDescriptions = [
            'منتج عالي الجودة مصنوع من مواد فاخرة وحرفية ممتازة.',
            'تصميم أنيق وعملي يلبي احتياجات الحياة العصرية.',
            'بناء متين يضمن الأداء طويل المدى والموثوقية.',
            'تصميم أنيق مع الاهتمام بالتفاصيل والجودة العالية.',
            'مثالي للاستخدام اليومي مع ميزات مريحة وعملية.',
        ];

        return $arabicDescriptions[array_rand($arabicDescriptions)];
    }

    /**
     * Generate a realistic price
     */
    private function generatePrice(): float
    {
        $priceRanges = [
            [10, 50],    // Low range
            [50, 150],   // Medium range
            [150, 500],  // High range
            [500, 1000], // Premium range
        ];

        $range = $priceRanges[array_rand($priceRanges)];
        return round(rand($range[0] * 100, $range[1] * 100) / 100, 2);
    }

    /**
     * Generate SKU for product
     */
    private function generateSKU(string $productName, int $productNumber): string
    {
        // Extract first letters of each word
        $words = explode(' ', $productName);
        $acronym = '';
        foreach ($words as $word) {
            if (!empty($word) && strlen($acronym) < 4) {
                $acronym .= strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $word), 0, 1));
            }
        }

        // Ensure we have at least 2 characters
        if (strlen($acronym) < 2) {
            $acronym = 'PRD';
        }

        // Add product number with padding
        $paddedNumber = str_pad($productNumber, 4, '0', STR_PAD_LEFT);

        return "{$acronym}-{$paddedNumber}";
    }

    /**
     * Generate random image URL from picsum.photos
     */
    private function generateRandomImage(): string
    {
        $width = 800;
        $height = 600;
        $imageId = rand(1, 1000); // Random image ID for variety

        return "https://picsum.photos/{$width}/{$height}?random={$imageId}";
    }
}
