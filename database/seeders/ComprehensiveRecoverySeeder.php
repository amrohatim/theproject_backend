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
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ComprehensiveRecoverySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔄 Starting comprehensive database recovery...');
        
        // First, create the comprehensive categories that were lost
        $this->createComprehensiveCategories();
        
        // Then, restore the products with their images
        $this->restoreProducts();
        
        $this->command->info('✅ Database recovery completed successfully!');
        
        // Show final statistics
        $totalProducts = Product::count();
        $totalCategories = Category::where('type', 'product')->count();
        $this->command->info("📊 Final Statistics:");
        $this->command->info("   - Total Products: {$totalProducts}");
        $this->command->info("   - Total Product Categories: {$totalCategories}");
    }

    /**
     * Create comprehensive categories based on the original structure
     */
    private function createComprehensiveCategories(): void
    {
        $this->command->info('📂 Creating comprehensive categories...');

        // Define the comprehensive category structure
        $categoryStructure = [
            'Clothes' => [
                'description' => 'Women\'s clothing and apparel',
                'icon' => 'fas fa-tshirt',
                'subcategories' => [
                    'Activewear' => 'Sports and fitness clothing',
                    'Bottoms (jeans, skirts)' => 'Pants, jeans, skirts and bottom wear',
                    'Dresses' => 'Casual and formal dresses',
                    'Loungewear' => 'Comfortable home and leisure wear',
                    'Maternity wear' => 'Clothing for expecting mothers',
                    'Outerwear (jackets, coats)' => 'Jackets, coats and outer garments',
                    'Tops (blouses, tunics)' => 'Shirts, blouses and top wear',
                ]
            ],
            'Ethnic & Traditional Wear' => [
                'description' => 'Traditional and ethnic clothing',
                'icon' => 'fas fa-user-tie',
                'subcategories' => [
                    'Abayas' => 'Traditional Islamic robes',
                    'Kaftans' => 'Loose-fitting traditional dresses',
                    'Salwar Kameez' => 'Traditional South Asian clothing',
                    'Sarees' => 'Traditional Indian garments',
                    'Pray Clothes' => 'Religious and prayer clothing',
                ]
            ],
            'Footwear' => [
                'description' => 'Shoes and footwear for all occasions',
                'icon' => 'fas fa-shoe-prints',
                'subcategories' => [
                    'Boots' => 'Ankle boots, knee-high boots',
                    'Flats' => 'Flat shoes and ballet flats',
                    'Heels' => 'High heels and stilettos',
                    'Sandals' => 'Open-toe sandals and flip-flops',
                    'Sneakers' => 'Athletic and casual sneakers',
                ]
            ],
            'Accessories' => [
                'description' => 'Fashion accessories and add-ons',
                'icon' => 'fas fa-glasses',
                'subcategories' => [
                    'Belts' => 'Leather and fabric belts',
                    'Hats' => 'Caps, hats and headwear',
                    'Scarves' => 'Silk and cotton scarves',
                    'Sunglasses' => 'Designer and casual sunglasses',
                ]
            ],
            'Bags' => [
                'description' => 'Handbags, purses and carrying bags',
                'icon' => 'fas fa-shopping-bag',
                'subcategories' => [
                    'Backpacks' => 'School and travel backpacks',
                    'Crossbody bags' => 'Small crossbody and shoulder bags',
                    'Tote bags' => 'Large tote and shopping bags',
                ]
            ],
            'Jewelry' => [
                'description' => 'Fashion jewelry and accessories',
                'icon' => 'fas fa-gem',
                'subcategories' => [
                    'Anklets' => 'Ankle bracelets and chains',
                    'Bracelets' => 'Wrist bracelets and bangles',
                    'Earrings' => 'Stud, hoop and drop earrings',
                    'Necklaces' => 'Chains, pendants and chokers',
                    'Rings' => 'Fashion and statement rings',
                ]
            ],
            'Makeup' => [
                'description' => 'Cosmetics and beauty products',
                'icon' => 'fas fa-palette',
                'subcategories' => [
                    'Blushes' => 'Cheek color and bronzers',
                    'Eyeshadows' => 'Eye makeup and palettes',
                    'Foundations' => 'Base makeup and concealers',
                    'Lipsticks' => 'Lip color and glosses',
                    'Mascaras' => 'Eyelash makeup and primers',
                ]
            ],
            'Skincare' => [
                'description' => 'Skincare and beauty treatments',
                'icon' => 'fas fa-spa',
                'subcategories' => [
                    'Cleansers' => 'Face wash and cleansing products',
                    'Face masks' => 'Treatment and hydrating masks',
                    'Moisturizers' => 'Face and body moisturizers',
                    'Serums' => 'Treatment serums and essences',
                    'Sunscreens' => 'UV protection and SPF products',
                ]
            ],
            'Haircare' => [
                'description' => 'Hair care and styling products',
                'icon' => 'fas fa-cut',
                'subcategories' => [
                    'Conditioners' => 'Hair conditioners and treatments',
                    'Hair oils' => 'Nourishing hair oils and serums',
                    'Shampoos' => 'Cleansing shampoos for all hair types',
                ]
            ],
            'Hair Accessories' => [
                'description' => 'Hair styling accessories',
                'icon' => 'fas fa-ribbon',
                'subcategories' => [
                    'Clips' => 'Hair clips and pins',
                    'Hairbands' => 'Headbands and hair ties',
                    'Scrunchies' => 'Fabric hair ties and scrunchies',
                ]
            ],
            'Fragrances' => [
                'description' => 'Perfumes and body fragrances',
                'icon' => 'fas fa-spray-can',
                'subcategories' => [
                    'Body mists' => 'Light body sprays and mists',
                    'Deodorants' => 'Antiperspirants and deodorants',
                    'Perfumes' => 'Eau de parfum and cologne',
                ]
            ],
            'Intimates' => [
                'description' => 'Undergarments and intimate apparel',
                'icon' => 'fas fa-heart',
                'subcategories' => [
                    'Bras' => 'Support bras and bralettes',
                    'Lingerie' => 'Intimate and sleepwear',
                    'Panties' => 'Underwear and briefs',
                    'Shapewear' => 'Body shaping undergarments',
                ]
            ],
            'Maternity Essentials' => [
                'description' => 'Products for expecting and new mothers',
                'icon' => 'fas fa-baby',
                'subcategories' => [
                    'Belly support belts' => 'Maternity support belts',
                    'Maternity clothing' => 'Pregnancy-friendly clothing',
                    'Nursing bras' => 'Breastfeeding support bras',
                ]
            ],
            'Baby Clothing' => [
                'description' => 'Clothing for babies and toddlers',
                'icon' => 'fas fa-baby-carriage',
                'subcategories' => [
                    'Onesies' => 'Baby bodysuits and onesies',
                    'Outerwear' => 'Baby jackets and coats',
                    'Sleepwear' => 'Baby pajamas and sleep suits',
                ]
            ],
            'Baby Gear' => [
                'description' => 'Essential baby equipment and gear',
                'icon' => 'fas fa-child',
                'subcategories' => [
                    'Baby carriers' => 'Baby slings and carriers',
                    'Car seats' => 'Infant and toddler car seats',
                    'Strollers' => 'Baby strollers and pushchairs',
                ]
            ],
            'Feeding' => [
                'description' => 'Baby feeding essentials',
                'icon' => 'fas fa-baby-bottle',
                'subcategories' => [
                    'Bottles' => 'Baby bottles and sippy cups',
                    'Breast pumps' => 'Electric and manual breast pumps',
                    'High chairs' => 'Baby feeding chairs and boosters',
                    'Sterilizers' => 'Bottle sterilizers and cleaners',
                ]
            ],
            'Watches' => [
                'description' => 'Timepieces and smart watches',
                'icon' => 'fas fa-clock',
                'subcategories' => [
                    'Analog' => 'Traditional analog watches',
                    'Digital' => 'Digital display watches',
                    'Smartwatches' => 'Smart and fitness watches',
                ]
            ],
        ];

        // Create parent categories and their subcategories
        foreach ($categoryStructure as $parentName => $parentData) {
            // Check if parent category already exists
            $parentCategory = Category::where('name', $parentName)
                ->where('type', 'product')
                ->whereNull('parent_id')
                ->first();

            if (!$parentCategory) {
                // Create parent category
                $parentCategory = Category::create([
                    'name' => $parentName,
                    'description' => $parentData['description'],
                    'image' => $this->getCategoryImage($parentName),
                    'is_active' => true,
                    'type' => 'product',
                    'icon' => $parentData['icon'],
                ]);
                
                $this->command->info("✅ Created parent category: {$parentName}");
            } else {
                $this->command->info("ℹ️  Parent category already exists: {$parentName}");
            }

            // Create subcategories
            foreach ($parentData['subcategories'] as $subName => $subDescription) {
                $existingSubcategory = Category::where('name', $subName)
                    ->where('type', 'product')
                    ->where('parent_id', $parentCategory->id)
                    ->first();

                if (!$existingSubcategory) {
                    Category::create([
                        'name' => $subName,
                        'description' => $subDescription,
                        'image' => $this->getCategoryImage($subName),
                        'parent_id' => $parentCategory->id,
                        'is_active' => true,
                        'type' => 'product',
                        'icon' => $parentData['icon'],
                    ]);
                    
                    $this->command->info("  ✅ Created subcategory: {$subName}");
                } else {
                    $this->command->info("  ℹ️  Subcategory already exists: {$subName}");
                }
            }
        }
    }

    /**
     * Get category image path from app images directory
     */
    private function getCategoryImage(string $categoryName): string
    {
        $imagePath = base_path("app images/{$categoryName}");
        
        if (File::exists($imagePath)) {
            $imageFiles = File::files($imagePath);
            if (!empty($imageFiles)) {
                $mainImage = collect($imageFiles)->first(function ($file) use ($categoryName) {
                    return Str::contains($file->getFilename(), $categoryName);
                });
                
                if ($mainImage) {
                    return "app images/{$categoryName}/{$mainImage->getFilename()}";
                }
                
                // Fallback to first image in directory
                return "app images/{$categoryName}/{$imageFiles[0]->getFilename()}";
            }
        }
        
        // Fallback to placeholder
        return '/images/categories/placeholder.jpg';
    }

    /**
     * Restore products based on available product images
     */
    private function restoreProducts(): void
    {
        $this->command->info('📦 Restoring products from available images...');

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

        $productImagesPath = base_path('Products images');

        if (!File::exists($productImagesPath)) {
            $this->command->error('Products images directory not found.');
            return;
        }

        $imageFiles = File::files($productImagesPath);
        $createdProducts = 0;

        foreach ($imageFiles as $imageFile) {
            $filename = $imageFile->getFilenameWithoutExtension();

            // Parse product information from filename
            $productInfo = $this->parseProductFromFilename($filename);

            if (!$productInfo) {
                continue;
            }

            // Find matching category
            $category = $this->findMatchingCategory($productInfo['category'], $categories);

            if (!$category) {
                $this->command->warn("No matching category found for: {$productInfo['category']}");
                continue;
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

            // Add colors if specified
            if (!empty($productInfo['color'])) {
                $this->addProductColor($product, $productInfo['color']);
            }

            $createdProducts++;

            if ($createdProducts % 10 == 0) {
                $this->command->info("Created {$createdProducts} products...");
            }
        }

        $this->command->info("✅ Created {$createdProducts} products from available images.");
    }

    /**
     * Parse product information from filename
     */
    private function parseProductFromFilename(string $filename): ?array
    {
        // Handle different filename patterns
        $patterns = [
            // Pattern: "Premium Activewear blue"
            '/^(Premium|Classic)\s+(.+?)\s+([a-z]+(?:\s+[a-z]+)*)$/i',
            // Pattern: "Athletic Running Sneakers blue"
            '/^(.+?)\s+([a-z]+(?:\s+[a-z]+)*)$/i',
            // Pattern: "Elegant Maxi Dress"
            '/^(.+)$/i'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $filename, $matches)) {
                $quality = 'Classic';
                $productName = '';
                $color = '';

                if (count($matches) == 4) {
                    // Premium/Classic + Product + Color
                    $quality = $matches[1];
                    $productName = trim($matches[2]);
                    $color = trim($matches[3]);
                } elseif (count($matches) == 3) {
                    // Product + Color
                    $productName = trim($matches[1]);
                    $color = trim($matches[2]);
                } else {
                    // Just product name
                    $productName = trim($matches[1]);
                }

                // Determine category from product name
                $category = $this->determineCategoryFromProductName($productName);

                if (!$category) {
                    continue;
                }

                // Generate pricing based on quality
                $basePrice = $this->generateBasePrice($category, $quality);

                return [
                    'name' => $productName,
                    'category' => $category,
                    'color' => $color,
                    'quality' => $quality,
                    'price' => $basePrice,
                    'original_price' => $quality === 'Premium' ? $basePrice * 1.3 : null,
                    'stock' => rand(15, 85),
                    'rating' => rand(35, 50) / 10,
                    'featured' => rand(1, 10) <= 2, // 20% chance
                ];
            }
        }

        return null;
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
            ProductSpecification::create([
                'product_id' => $product->id,
                'name' => $name,
                'value' => $value,
            ]);
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

    /**
     * Add product color
     */
    private function addProductColor(Product $product, string $colorName): void
    {
        $colorHex = $this->getColorHex($colorName);

        ProductColor::create([
            'product_id' => $product->id,
            'name' => ucfirst($colorName),
            'hex_code' => $colorHex,
            'stock' => $product->stock,
        ]);
    }

    /**
     * Get hex code for color name
     */
    private function getColorHex(string $colorName): string
    {
        $colorMap = [
            'black' => '#000000',
            'white' => '#FFFFFF',
            'red' => '#FF0000',
            'blue' => '#0000FF',
            'green' => '#008000',
            'yellow' => '#FFFF00',
            'orange' => '#FFA500',
            'purple' => '#800080',
            'pink' => '#FFC0CB',
            'brown' => '#A52A2A',
            'gray' => '#808080',
            'grey' => '#808080',
            'violet' => '#8A2BE2',
            'cyan' => '#00FFFF',
            'gold' => '#FFD700',
            'silver' => '#C0C0C0',
            'navy' => '#000080',
            'darkblue' => '#00008B',
            'lightblue' => '#ADD8E6',
            'darkgreen' => '#006400',
            'lightgreen' => '#90EE90',
            'darkred' => '#8B0000',
            'lightyellow' => '#FFFFE0',
            'darkorange' => '#FF8C00',
            'lightgray' => '#D3D3D3',
            'darkgray' => '#A9A9A9',
        ];

        $normalizedColor = strtolower(str_replace(' ', '', $colorName));

        return $colorMap[$normalizedColor] ?? '#808080'; // Default to gray
    }
}
