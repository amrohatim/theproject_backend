<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Branch;
use App\Models\ProductSpecification;
use App\Models\ProductColor;
use App\Models\ProductSize;
use App\Models\ProductColorSize;
use App\Models\SizeCategory;
use App\Models\StandardizedSize;
use App\Helpers\UnsplashImageHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ComprehensiveProductSeeder extends Seeder
{
    private $clothesSizeCategory;
    private $shoesSizeCategory;
    private $hatsSizeCategory;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting comprehensive product seeding...');

        // Get size categories
        $this->clothesSizeCategory = SizeCategory::where('name', 'clothes')->first();
        $this->shoesSizeCategory = SizeCategory::where('name', 'shoes')->first();
        $this->hatsSizeCategory = SizeCategory::where('name', 'hats')->first();

        // Get all branches and product categories
        $branches = Branch::all();
        $categories = Category::where('type', 'product')->whereNotNull('parent_id')->get();

        if ($branches->isEmpty()) {
            $this->command->error('No branches found. Please run BranchesTableSeeder first.');
            return;
        }

        if ($categories->isEmpty()) {
            $this->command->error('No product categories found. Please run CategoriesTableSeeder first.');
            return;
        }

        $this->command->info("Found {$branches->count()} branches and {$categories->count()} categories");

        // Create products for each category
        foreach ($categories as $category) {
            $this->createProductsForCategory($category, $branches);
        }

        $this->command->info('✅ Comprehensive product seeding completed successfully!');
    }

    /**
     * Create products for a specific category
     */
    private function createProductsForCategory(Category $category, $branches): void
    {
        $this->command->info("📦 Creating products for category: {$category->name}");

        $productTemplates = $this->getProductTemplatesForCategory($category);

        foreach ($productTemplates as $template) {
            $branch = $branches->random();

            // Create the main product
            $product = Product::create([
                'name' => $template['name'],
                'description' => $template['description'],
                'price' => $template['price'],
                'original_price' => $template['original_price'] ?? null,
                'stock' => $template['stock'],
                'image' => $this->downloadProductImage($template['image_search'], $category->name, $template['name']),
                'branch_id' => $branch->id,
                'category_id' => $category->id,
                'sku' => $this->generateSKU($category->name, $template['name']),
                'rating' => $template['rating'] ?? rand(35, 50) / 10,
                'is_available' => true,
                'featured' => rand(1, 10) <= 2, // 20% chance of being featured
            ]);

            // Add specifications
            $this->addProductSpecifications($product, $template['specifications'] ?? []);

            // Add colors and sizes based on category type
            if ($this->categoryRequiresVariants($category)) {
                $this->addProductVariants($product, $category, $template);
            }
        }
    }

    /**
     * Get product templates for a specific category
     */
    private function getProductTemplatesForCategory(Category $category): array
    {
        $categoryName = strtolower($category->name);

        // Define product templates for each category
        $templates = [
            // Clothing categories
            'hijap' => [
                [
                    'name' => 'Premium Silk Hijab',
                    'description' => 'Luxurious silk hijab with elegant drape and comfortable fit',
                    'price' => 45.99,
                    'original_price' => 59.99,
                    'stock' => 50,
                    'image_search' => 'silk hijab fashion',
                    'specifications' => [
                        'Material' => '100% Silk',
                        'Care Instructions' => 'Hand wash only',
                        'Origin' => 'Turkey',
                        'Style' => 'Traditional'
                    ]
                ],
                [
                    'name' => 'Cotton Everyday Hijab',
                    'description' => 'Comfortable cotton hijab perfect for daily wear',
                    'price' => 19.99,
                    'stock' => 75,
                    'image_search' => 'cotton hijab daily wear',
                    'specifications' => [
                        'Material' => '100% Cotton',
                        'Care Instructions' => 'Machine washable',
                        'Origin' => 'Egypt',
                        'Style' => 'Modern'
                    ]
                ]
            ],
            'dresses' => [
                [
                    'name' => 'Elegant Maxi Dress',
                    'description' => 'Flowing maxi dress perfect for special occasions',
                    'price' => 89.99,
                    'original_price' => 119.99,
                    'stock' => 30,
                    'image_search' => 'elegant maxi dress fashion',
                    'specifications' => [
                        'Material' => 'Polyester blend',
                        'Length' => 'Maxi',
                        'Sleeve Type' => 'Long sleeve',
                        'Occasion' => 'Formal'
                    ]
                ],
                [
                    'name' => 'Casual Summer Dress',
                    'description' => 'Light and breezy dress for warm weather',
                    'price' => 39.99,
                    'stock' => 60,
                    'image_search' => 'casual summer dress',
                    'specifications' => [
                        'Material' => 'Cotton',
                        'Length' => 'Knee-length',
                        'Sleeve Type' => 'Sleeveless',
                        'Occasion' => 'Casual'
                    ]
                ]
            ],
            'abayas' => [
                [
                    'name' => 'Traditional Black Abaya',
                    'description' => 'Classic black abaya with intricate embroidery',
                    'price' => 129.99,
                    'original_price' => 159.99,
                    'stock' => 25,
                    'image_search' => 'traditional black abaya embroidery',
                    'specifications' => [
                        'Material' => 'Crepe',
                        'Style' => 'Traditional',
                        'Embellishment' => 'Hand embroidery',
                        'Origin' => 'UAE'
                    ]
                ]
            ],
            'sneakers' => [
                [
                    'name' => 'Athletic Running Sneakers',
                    'description' => 'High-performance running shoes with advanced cushioning',
                    'price' => 129.99,
                    'original_price' => 149.99,
                    'stock' => 40,
                    'image_search' => 'athletic running sneakers',
                    'specifications' => [
                        'Brand' => 'SportMax',
                        'Type' => 'Running',
                        'Sole Material' => 'Rubber',
                        'Upper Material' => 'Mesh'
                    ]
                ],
                [
                    'name' => 'Casual Canvas Sneakers',
                    'description' => 'Comfortable canvas sneakers for everyday wear',
                    'price' => 59.99,
                    'stock' => 55,
                    'image_search' => 'casual canvas sneakers',
                    'specifications' => [
                        'Brand' => 'ComfortWalk',
                        'Type' => 'Casual',
                        'Sole Material' => 'Rubber',
                        'Upper Material' => 'Canvas'
                    ]
                ]
            ],
            'heels' => [
                [
                    'name' => 'Classic Stiletto Heels',
                    'description' => 'Elegant stiletto heels for formal occasions',
                    'price' => 89.99,
                    'original_price' => 109.99,
                    'stock' => 35,
                    'image_search' => 'classic stiletto heels formal',
                    'specifications' => [
                        'Heel Height' => '4 inches',
                        'Material' => 'Leather',
                        'Occasion' => 'Formal',
                        'Closure' => 'Slip-on'
                    ]
                ]
            ],
            'handbags' => [
                [
                    'name' => 'Luxury Leather Handbag',
                    'description' => 'Premium leather handbag with gold hardware',
                    'price' => 199.99,
                    'original_price' => 249.99,
                    'stock' => 20,
                    'image_search' => 'luxury leather handbag gold hardware',
                    'specifications' => [
                        'Material' => 'Genuine Leather',
                        'Hardware' => 'Gold-plated',
                        'Closure' => 'Zipper',
                        'Interior' => 'Fabric lined'
                    ]
                ]
            ],
            'perfumes' => [
                [
                    'name' => 'Oriental Rose Perfume',
                    'description' => 'Luxurious oriental fragrance with rose and oud notes',
                    'price' => 79.99,
                    'original_price' => 99.99,
                    'stock' => 45,
                    'image_search' => 'oriental rose perfume bottle',
                    'specifications' => [
                        'Volume' => '50ml',
                        'Fragrance Family' => 'Oriental',
                        'Top Notes' => 'Rose, Bergamot',
                        'Base Notes' => 'Oud, Amber'
                    ]
                ]
            ],
            'shampoos' => [
                [
                    'name' => 'Argan Oil Shampoo',
                    'description' => 'Nourishing shampoo enriched with argan oil',
                    'price' => 24.99,
                    'stock' => 80,
                    'image_search' => 'argan oil shampoo bottle',
                    'specifications' => [
                        'Volume' => '400ml',
                        'Hair Type' => 'All hair types',
                        'Key Ingredient' => 'Argan Oil',
                        'Sulfate Free' => 'Yes'
                    ]
                ]
            ],
            'foundations' => [
                [
                    'name' => 'Full Coverage Foundation',
                    'description' => 'Long-lasting full coverage foundation for all skin types',
                    'price' => 34.99,
                    'stock' => 60,
                    'image_search' => 'full coverage foundation makeup',
                    'specifications' => [
                        'Coverage' => 'Full',
                        'Finish' => 'Matte',
                        'SPF' => '15',
                        'Volume' => '30ml'
                    ]
                ]
            ]
        ];

        // Return templates for the category, or generate generic ones if not found
        return $templates[$categoryName] ?? $this->generateGenericTemplates($category);
    }

    /**
     * Generate generic product templates for categories not explicitly defined
     */
    private function generateGenericTemplates(Category $category): array
    {
        $categoryName = $category->name;
        $searchTerm = strtolower(str_replace(['&', ' '], ['', ' '], $categoryName));

        return [
            [
                'name' => "Premium {$categoryName}",
                'description' => "High-quality {$categoryName} with excellent craftsmanship and attention to detail",
                'price' => rand(2999, 9999) / 100,
                'original_price' => rand(3999, 12999) / 100,
                'stock' => rand(20, 80),
                'image_search' => $searchTerm,
                'specifications' => [
                    'Brand' => 'Premium Brand',
                    'Quality' => 'High',
                    'Warranty' => '1 Year',
                    'Origin' => 'Imported'
                ]
            ],
            [
                'name' => "Classic {$categoryName}",
                'description' => "Traditional {$categoryName} with timeless design and reliable quality",
                'price' => rand(1999, 5999) / 100,
                'stock' => rand(30, 90),
                'image_search' => $searchTerm,
                'specifications' => [
                    'Brand' => 'Classic Brand',
                    'Style' => 'Traditional',
                    'Quality' => 'Standard',
                    'Origin' => 'Local'
                ]
            ]
        ];
    }

    /**
     * Download and save product image
     */
    private function downloadProductImage(string $searchTerm, string $categoryName, string $productName): string
    {
        try {
            $filename = Str::slug($categoryName . '-' . $productName) . '-' . time() . '.jpg';
            $imagePath = "public/images/products/{$filename}";

            return UnsplashImageHelper::downloadAndSaveImage($searchTerm, $imagePath, 800, 600);
        } catch (\Exception $e) {
            Log::warning("Failed to download image for {$productName}: " . $e->getMessage());
            return 'images/products/placeholder.jpg';
        }
    }

    /**
     * Generate SKU for product
     */
    private function generateSKU(string $categoryName, string $productName): string
    {
        $categoryCode = strtoupper(substr(str_replace(' ', '', $categoryName), 0, 3));
        $productCode = strtoupper(substr(str_replace(' ', '', $productName), 0, 3));
        $randomNumber = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

        return "{$categoryCode}-{$productCode}-{$randomNumber}";
    }

    /**
     * Add product specifications
     */
    private function addProductSpecifications(Product $product, array $specifications): void
    {
        $displayOrder = 0;
        foreach ($specifications as $key => $value) {
            ProductSpecification::create([
                'product_id' => $product->id,
                'key' => $key,
                'value' => $value,
                'display_order' => $displayOrder++,
            ]);
        }
    }

    /**
     * Check if category requires color and size variants
     */
    private function categoryRequiresVariants(Category $category): bool
    {
        $categoryName = strtolower($category->name);
        $parentName = strtolower($category->parent->name ?? '');

        // Categories that require variants
        $variantCategories = [
            'clothing', 'footwear', 'accessories', 'bags', 'intimates',
            'ethnic', 'baby clothing', 'maternity', 'hijap', 'dresses',
            'abayas', 'sneakers', 'heels', 'boots', 'sandals', 'flats',
            'handbags', 'backpacks', 'tote bags', 'crossbody bags',
            'hats', 'scarves', 'belts', 'sunglasses'
        ];

        // Categories that don't require variants
        $noVariantCategories = [
            'skincare', 'makeup', 'haircare', 'fragrances', 'perfumes',
            'shampoos', 'conditioners', 'cleansers', 'moisturizers',
            'foundations', 'lipsticks', 'mascaras', 'feeding', 'baby gear'
        ];

        // Check if category or parent is in no-variant list
        foreach ($noVariantCategories as $noVariant) {
            if (str_contains($categoryName, $noVariant) || str_contains($parentName, $noVariant)) {
                return false;
            }
        }

        // Check if category or parent is in variant list
        foreach ($variantCategories as $variant) {
            if (str_contains($categoryName, $variant) || str_contains($parentName, $variant)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add product variants (colors and sizes)
     */
    private function addProductVariants(Product $product, Category $category, array $template): void
    {
        // Add colors
        $colors = $this->getColorsForCategory($category);
        $createdColors = [];

        foreach ($colors as $index => $colorData) {
            $colorImage = $this->downloadColorVariantImage($template['image_search'], $colorData['name'], $category->name, $product->name);

            $color = ProductColor::create([
                'product_id' => $product->id,
                'name' => $colorData['name'],
                'color_code' => $colorData['color_code'],
                'image' => $colorImage,
                'price_adjustment' => $colorData['price_adjustment'],
                'stock' => rand(10, 30),
                'display_order' => $index,
                'is_default' => $index === 0,
            ]);

            $createdColors[] = $color;
        }

        // Add sizes if applicable
        if ($this->categoryRequiresSizes($category)) {
            $sizes = $this->getSizesForCategory($category);
            $createdSizes = [];

            foreach ($sizes as $index => $sizeData) {
                $size = ProductSize::create([
                    'product_id' => $product->id,
                    'size_category_id' => $sizeData['size_category_id'],
                    'standardized_size_id' => $sizeData['standardized_size_id'],
                    'name' => $sizeData['name'],
                    'value' => $sizeData['value'],
                    'additional_info' => $sizeData['additional_info'],
                    'price_adjustment' => $sizeData['price_adjustment'],
                    'stock' => rand(5, 20),
                    'display_order' => $index,
                    'is_default' => $index === 2, // Make medium/size 8 default
                ]);

                $createdSizes[] = $size;
            }

            // Create color-size combinations
            $this->createColorSizeCombinations($product, $createdColors, $createdSizes);
        }
    }

    /**
     * Download color variant image
     */
    private function downloadColorVariantImage(string $baseSearchTerm, string $colorName, string $categoryName, string $productName): string
    {
        try {
            $searchTerm = $baseSearchTerm . ' ' . strtolower($colorName);
            $filename = Str::slug($categoryName . '-' . $productName . '-' . $colorName) . '-' . time() . '.jpg';
            $imagePath = "public/images/product-colors/{$filename}";

            return UnsplashImageHelper::downloadAndSaveImage($searchTerm, $imagePath, 800, 600);
        } catch (\Exception $e) {
            Log::warning("Failed to download color variant image for {$productName} in {$colorName}: " . $e->getMessage());
            return 'images/product-colors/placeholder.jpg';
        }
    }

    /**
     * Get colors for category
     */
    private function getColorsForCategory(Category $category): array
    {
        $categoryName = strtolower($category->name);

        // Define color sets for different categories
        if (str_contains($categoryName, 'hijap') || str_contains($categoryName, 'abaya')) {
            return [
                ['name' => 'Black', 'color_code' => '#000000', 'price_adjustment' => 0],
                ['name' => 'Navy Blue', 'color_code' => '#000080', 'price_adjustment' => 0],
                ['name' => 'Dark Brown', 'color_code' => '#654321', 'price_adjustment' => 0],
            ];
        }

        if (str_contains($categoryName, 'dress') || str_contains($categoryName, 'clothing')) {
            return [
                ['name' => 'Black', 'color_code' => '#000000', 'price_adjustment' => 0],
                ['name' => 'Red', 'color_code' => '#FF0000', 'price_adjustment' => 5],
                ['name' => 'Blue', 'color_code' => '#0000FF', 'price_adjustment' => 0],
                ['name' => 'White', 'color_code' => '#FFFFFF', 'price_adjustment' => 0],
            ];
        }

        if (str_contains($categoryName, 'shoe') || str_contains($categoryName, 'sneaker') || str_contains($categoryName, 'heel')) {
            return [
                ['name' => 'Black', 'color_code' => '#000000', 'price_adjustment' => 0],
                ['name' => 'Brown', 'color_code' => '#8B4513', 'price_adjustment' => 0],
                ['name' => 'White', 'color_code' => '#FFFFFF', 'price_adjustment' => 0],
            ];
        }

        if (str_contains($categoryName, 'bag') || str_contains($categoryName, 'handbag')) {
            return [
                ['name' => 'Black', 'color_code' => '#000000', 'price_adjustment' => 0],
                ['name' => 'Brown', 'color_code' => '#8B4513', 'price_adjustment' => 10],
                ['name' => 'Beige', 'color_code' => '#F5F5DC', 'price_adjustment' => 0],
            ];
        }

        // Default colors for other categories
        return [
            ['name' => 'Black', 'color_code' => '#000000', 'price_adjustment' => 0],
            ['name' => 'Blue', 'color_code' => '#0000FF', 'price_adjustment' => 0],
            ['name' => 'Red', 'color_code' => '#FF0000', 'price_adjustment' => 5],
        ];
    }

    /**
     * Check if category requires sizes
     */
    private function categoryRequiresSizes(Category $category): bool
    {
        $categoryName = strtolower($category->name);
        $parentName = strtolower($category->parent->name ?? '');

        // Categories that require sizes
        $sizeCategories = [
            'clothing', 'footwear', 'hijap', 'dresses', 'abayas', 'tops', 'bottoms',
            'sneakers', 'heels', 'boots', 'sandals', 'flats', 'hats', 'baby clothing'
        ];

        foreach ($sizeCategories as $sizeCategory) {
            if (str_contains($categoryName, $sizeCategory) || str_contains($parentName, $sizeCategory)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get sizes for category
     */
    private function getSizesForCategory(Category $category): array
    {
        $categoryName = strtolower($category->name);
        $parentName = strtolower($category->parent->name ?? '');

        // Determine size category
        if (str_contains($categoryName, 'shoe') || str_contains($categoryName, 'sneaker') ||
            str_contains($categoryName, 'heel') || str_contains($categoryName, 'boot') ||
            str_contains($categoryName, 'sandal') || str_contains($categoryName, 'flat')) {

            return $this->getShoesSizes();
        }

        if (str_contains($categoryName, 'hat') || str_contains($parentName, 'hat')) {
            return $this->getHatsSizes();
        }

        // Default to clothing sizes
        return $this->getClothingSizes();
    }

    /**
     * Get clothing sizes
     */
    private function getClothingSizes(): array
    {
        if (!$this->clothesSizeCategory) return [];

        $standardizedSizes = StandardizedSize::where('size_category_id', $this->clothesSizeCategory->id)
            ->where('is_active', true)
            ->orderBy('display_order')
            ->take(6) // Get first 6 sizes (XXS to XL)
            ->get();

        $sizes = [];
        foreach ($standardizedSizes as $index => $size) {
            $sizes[] = [
                'size_category_id' => $this->clothesSizeCategory->id,
                'standardized_size_id' => $size->id,
                'name' => $size->name,
                'value' => $size->value,
                'additional_info' => $size->additional_info,
                'price_adjustment' => $index > 3 ? 5 : 0, // XL+ costs more
            ];
        }

        return $sizes;
    }

    /**
     * Get shoes sizes
     */
    private function getShoesSizes(): array
    {
        if (!$this->shoesSizeCategory) return [];

        // Get common shoe sizes (EU 36-42, roughly US 6-12)
        $standardizedSizes = StandardizedSize::where('size_category_id', $this->shoesSizeCategory->id)
            ->where('is_active', true)
            ->whereBetween('name', ['36', '42'])
            ->orderBy('display_order')
            ->get();

        $sizes = [];
        foreach ($standardizedSizes as $size) {
            $sizes[] = [
                'size_category_id' => $this->shoesSizeCategory->id,
                'standardized_size_id' => $size->id,
                'name' => $size->name,
                'value' => $size->value,
                'additional_info' => $size->additional_info,
                'price_adjustment' => 0,
            ];
        }

        return $sizes;
    }

    /**
     * Get hats sizes
     */
    private function getHatsSizes(): array
    {
        if (!$this->hatsSizeCategory) return [];

        // Get adult hat sizes (EU 56-62)
        $standardizedSizes = StandardizedSize::where('size_category_id', $this->hatsSizeCategory->id)
            ->where('is_active', true)
            ->whereBetween('name', ['56', '62'])
            ->orderBy('display_order')
            ->get();

        $sizes = [];
        foreach ($standardizedSizes as $size) {
            $sizes[] = [
                'size_category_id' => $this->hatsSizeCategory->id,
                'standardized_size_id' => $size->id,
                'name' => $size->name,
                'value' => $size->value,
                'additional_info' => $size->additional_info,
                'price_adjustment' => 0,
            ];
        }

        return $sizes;
    }

    /**
     * Create color-size combinations
     */
    private function createColorSizeCombinations(Product $product, array $colors, array $sizes): void
    {
        foreach ($colors as $color) {
            foreach ($sizes as $size) {
                ProductColorSize::create([
                    'product_id' => $product->id,
                    'product_color_id' => $color->id,
                    'product_size_id' => $size->id,
                    'stock' => rand(2, 10),
                    'price_adjustment' => $color->price_adjustment + $size->price_adjustment,
                    'is_available' => true,
                ]);
            }
        }
    }
}
