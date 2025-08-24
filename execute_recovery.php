<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Product;

echo "🔄 Starting database recovery...\n";

try {
    // Check current state
    $currentProducts = Product::count();
    $currentCategories = Category::where('type', 'product')->count();
    
    echo "📊 Current state:\n";
    echo "   - Products: {$currentProducts}\n";
    echo "   - Product Categories: {$currentCategories}\n\n";
    
    // Create comprehensive categories
    echo "📂 Creating comprehensive categories...\n";
    
    $parentCategories = [
        ['name' => 'Clothes', 'description' => 'Women\'s clothing and apparel', 'icon' => 'fas fa-tshirt'],
        ['name' => 'Ethnic & Traditional Wear', 'description' => 'Traditional and ethnic clothing', 'icon' => 'fas fa-user-tie'],
        ['name' => 'Footwear', 'description' => 'Shoes and footwear for all occasions', 'icon' => 'fas fa-shoe-prints'],
        ['name' => 'Accessories', 'description' => 'Fashion accessories and add-ons', 'icon' => 'fas fa-glasses'],
        ['name' => 'Bags', 'description' => 'Handbags, purses and carrying bags', 'icon' => 'fas fa-shopping-bag'],
        ['name' => 'Jewelry', 'description' => 'Fashion jewelry and accessories', 'icon' => 'fas fa-gem'],
        ['name' => 'Makeup', 'description' => 'Cosmetics and beauty products', 'icon' => 'fas fa-palette'],
        ['name' => 'Skincare', 'description' => 'Skincare and beauty treatments', 'icon' => 'fas fa-spa'],
        ['name' => 'Haircare', 'description' => 'Hair care and styling products', 'icon' => 'fas fa-cut'],
        ['name' => 'Hair Accessories', 'description' => 'Hair styling accessories', 'icon' => 'fas fa-ribbon'],
        ['name' => 'Fragrances', 'description' => 'Perfumes and body fragrances', 'icon' => 'fas fa-spray-can'],
        ['name' => 'Intimates', 'description' => 'Undergarments and intimate apparel', 'icon' => 'fas fa-heart'],
        ['name' => 'Maternity Essentials', 'description' => 'Products for expecting and new mothers', 'icon' => 'fas fa-baby'],
        ['name' => 'Baby Clothing', 'description' => 'Clothing for babies and toddlers', 'icon' => 'fas fa-baby-carriage'],
        ['name' => 'Baby Gear', 'description' => 'Essential baby equipment and gear', 'icon' => 'fas fa-child'],
        ['name' => 'Feeding', 'description' => 'Baby feeding essentials', 'icon' => 'fas fa-baby-bottle'],
        ['name' => 'Watches', 'description' => 'Timepieces and smart watches', 'icon' => 'fas fa-clock'],
    ];
    
    $createdParents = [];
    
    foreach ($parentCategories as $parentData) {
        $existing = Category::where('name', $parentData['name'])
            ->where('type', 'product')
            ->whereNull('parent_id')
            ->first();
            
        if (!$existing) {
            $category = Category::create([
                'name' => $parentData['name'],
                'description' => $parentData['description'],
                'image' => '/images/categories/' . strtolower(str_replace(' ', '-', $parentData['name'])) . '.jpg',
                'is_active' => true,
                'type' => 'product',
                'icon' => $parentData['icon'],
            ]);
            
            $createdParents[$parentData['name']] = $category->id;
            echo "✅ Created parent category: {$parentData['name']}\n";
        } else {
            $createdParents[$parentData['name']] = $existing->id;
            echo "ℹ️  Parent category already exists: {$parentData['name']}\n";
        }
    }
    
    // Create subcategories
    echo "\n📁 Creating subcategories...\n";
    
    $subcategories = [
        'Clothes' => [
            'Activewear' => 'Sports and fitness clothing',
            'Bottoms (jeans, skirts)' => 'Pants, jeans, skirts and bottom wear',
            'Dresses' => 'Casual and formal dresses',
            'Loungewear' => 'Comfortable home and leisure wear',
            'Maternity wear' => 'Clothing for expecting mothers',
            'Outerwear (jackets, coats)' => 'Jackets, coats and outer garments',
            'Tops (blouses, tunics)' => 'Shirts, blouses and top wear',
        ],
        'Ethnic & Traditional Wear' => [
            'Abayas' => 'Traditional Islamic robes',
            'Kaftans' => 'Loose-fitting traditional dresses',
            'Salwar Kameez' => 'Traditional South Asian clothing',
            'Sarees' => 'Traditional Indian garments',
            'Pray Clothes' => 'Religious and prayer clothing',
        ],
        'Footwear' => [
            'Boots' => 'Ankle boots, knee-high boots',
            'Flats' => 'Flat shoes and ballet flats',
            'Heels' => 'High heels and stilettos',
            'Sandals' => 'Open-toe sandals and flip-flops',
            'Sneakers' => 'Athletic and casual sneakers',
        ],
        'Accessories' => [
            'Belts' => 'Leather and fabric belts',
            'Hats' => 'Caps, hats and headwear',
            'Scarves' => 'Silk and cotton scarves',
            'Sunglasses' => 'Designer and casual sunglasses',
        ],
        'Bags' => [
            'Backpacks' => 'School and travel backpacks',
            'Crossbody bags' => 'Small crossbody and shoulder bags',
            'Tote bags' => 'Large tote and shopping bags',
        ],
        'Jewelry' => [
            'Anklets' => 'Ankle bracelets and chains',
            'Bracelets' => 'Wrist bracelets and bangles',
            'Earrings' => 'Stud, hoop and drop earrings',
            'Necklaces' => 'Chains, pendants and chokers',
            'Rings' => 'Fashion and statement rings',
        ],
        'Makeup' => [
            'Blushes' => 'Cheek color and bronzers',
            'Eyeshadows' => 'Eye makeup and palettes',
            'Foundations' => 'Base makeup and concealers',
            'Lipsticks' => 'Lip color and glosses',
            'Mascaras' => 'Eyelash makeup and primers',
        ],
        'Skincare' => [
            'Cleansers' => 'Face wash and cleansing products',
            'Face masks' => 'Treatment and hydrating masks',
            'Moisturizers' => 'Face and body moisturizers',
            'Serums' => 'Treatment serums and essences',
            'Sunscreens' => 'UV protection and SPF products',
        ],
        'Haircare' => [
            'Conditioners' => 'Hair conditioners and treatments',
            'Hair oils' => 'Nourishing hair oils and serums',
            'Shampoos' => 'Cleansing shampoos for all hair types',
        ],
        'Hair Accessories' => [
            'Clips' => 'Hair clips and pins',
            'Hairbands' => 'Headbands and hair ties',
            'Scrunchies' => 'Fabric hair ties and scrunchies',
        ],
        'Fragrances' => [
            'Body mists' => 'Light body sprays and mists',
            'Deodorants' => 'Antiperspirants and deodorants',
            'Perfumes' => 'Eau de parfum and cologne',
        ],
        'Intimates' => [
            'Bras' => 'Support bras and bralettes',
            'Lingerie' => 'Intimate and sleepwear',
            'Panties' => 'Underwear and briefs',
            'Shapewear' => 'Body shaping undergarments',
        ],
        'Maternity Essentials' => [
            'Belly support belts' => 'Maternity support belts',
            'Maternity clothing' => 'Pregnancy-friendly clothing',
            'Nursing bras' => 'Breastfeeding support bras',
        ],
        'Baby Clothing' => [
            'Onesies' => 'Baby bodysuits and onesies',
            'Outerwear' => 'Baby jackets and coats',
            'Sleepwear' => 'Baby pajamas and sleep suits',
        ],
        'Baby Gear' => [
            'Baby carriers' => 'Baby slings and carriers',
            'Car seats' => 'Infant and toddler car seats',
            'Strollers' => 'Baby strollers and pushchairs',
        ],
        'Feeding' => [
            'Bottles' => 'Baby bottles and sippy cups',
            'Breast pumps' => 'Electric and manual breast pumps',
            'High chairs' => 'Baby feeding chairs and boosters',
            'Sterilizers' => 'Bottle sterilizers and cleaners',
        ],
        'Watches' => [
            'Analog' => 'Traditional analog watches',
            'Digital' => 'Digital display watches',
            'Smartwatches' => 'Smart and fitness watches',
        ],
    ];
    
    foreach ($subcategories as $parentName => $subs) {
        if (!isset($createdParents[$parentName])) {
            echo "⚠️  Parent category '{$parentName}' not found, skipping subcategories\n";
            continue;
        }
        
        $parentId = $createdParents[$parentName];
        
        foreach ($subs as $subName => $subDescription) {
            $existing = Category::where('name', $subName)
                ->where('type', 'product')
                ->where('parent_id', $parentId)
                ->first();
                
            if (!$existing) {
                Category::create([
                    'name' => $subName,
                    'description' => $subDescription,
                    'image' => '/images/categories/' . strtolower(str_replace(' ', '-', $subName)) . '.jpg',
                    'parent_id' => $parentId,
                    'is_active' => true,
                    'type' => 'product',
                    'icon' => 'fas fa-tag',
                ]);
                
                echo "  ✅ Created subcategory: {$subName}\n";
            } else {
                echo "  ℹ️  Subcategory already exists: {$subName}\n";
            }
        }
    }
    
    // Final statistics
    $finalProducts = Product::count();
    $finalCategories = Category::where('type', 'product')->count();
    
    echo "\n📊 Final Statistics:\n";
    echo "   - Products: {$finalProducts}\n";
    echo "   - Product Categories: {$finalCategories}\n";
    echo "   - Categories Added: " . ($finalCategories - $currentCategories) . "\n";
    
    echo "\n✅ Database recovery completed successfully!\n";
    
} catch (Exception $e) {
    echo "❌ Error during recovery: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
