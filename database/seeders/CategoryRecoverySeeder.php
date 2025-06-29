<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CategoryRecoverySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔄 Starting category recovery...');
        
        // Create the comprehensive categories that were lost
        $this->createComprehensiveCategories();
        
        $this->command->info('✅ Category recovery completed successfully!');
        
        // Show final statistics
        $totalCategories = Category::where('type', 'product')->count();
        $this->command->info("📊 Total Product Categories: {$totalCategories}");
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
}
