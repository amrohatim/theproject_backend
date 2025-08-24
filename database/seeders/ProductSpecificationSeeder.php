<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductSpecification;
use App\Models\ProductColor;
use App\Models\ProductSize;
use App\Models\ProductBranch;
use App\Models\Branch;

class ProductSpecificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all products
        $products = Product::all();
        
        if ($products->isEmpty()) {
            $this->command->info('No products found. Skipping product specification seeding.');
            return;
        }
        
        $this->command->info('Seeding product specifications...');
        
        // Sample specifications
        $specificationTemplates = [
            'Electronics' => [
                ['key' => 'Brand', 'value' => 'TechPro'],
                ['key' => 'Model', 'value' => 'X-2000'],
                ['key' => 'Warranty', 'value' => '1 Year'],
                ['key' => 'Dimensions', 'value' => '15 x 10 x 2 cm'],
                ['key' => 'Weight', 'value' => '250g'],
            ],
            'Clothing' => [
                ['key' => 'Material', 'value' => 'Cotton'],
                ['key' => 'Care Instructions', 'value' => 'Machine wash cold'],
                ['key' => 'Country of Origin', 'value' => 'USA'],
                ['key' => 'Style', 'value' => 'Casual'],
                ['key' => 'Season', 'value' => 'All Season'],
            ],
            'Food' => [
                ['key' => 'Ingredients', 'value' => 'Natural ingredients'],
                ['key' => 'Allergens', 'value' => 'May contain nuts'],
                ['key' => 'Storage', 'value' => 'Store in a cool, dry place'],
                ['key' => 'Shelf Life', 'value' => '6 months'],
                ['key' => 'Nutritional Info', 'value' => 'See packaging'],
            ],
            'Default' => [
                ['key' => 'Material', 'value' => 'High quality'],
                ['key' => 'Dimensions', 'value' => 'Standard size'],
                ['key' => 'Weight', 'value' => 'Light weight'],
                ['key' => 'Warranty', 'value' => '30 days'],
                ['key' => 'Origin', 'value' => 'Imported'],
            ],
        ];
        
        // Sample colors
        $colorTemplates = [
            ['name' => 'Red', 'color_code' => '#FF0000', 'price_adjustment' => 0, 'stock' => 10, 'is_default' => false],
            ['name' => 'Blue', 'color_code' => '#0000FF', 'price_adjustment' => 0, 'stock' => 15, 'is_default' => true],
            ['name' => 'Green', 'color_code' => '#00FF00', 'price_adjustment' => 2.00, 'stock' => 8, 'is_default' => false],
            ['name' => 'Black', 'color_code' => '#000000', 'price_adjustment' => 0, 'stock' => 20, 'is_default' => false],
            ['name' => 'White', 'color_code' => '#FFFFFF', 'price_adjustment' => 0, 'stock' => 12, 'is_default' => false],
        ];
        
        // Sample sizes
        $sizeTemplates = [
            ['name' => 'Small', 'value' => 'S', 'price_adjustment' => 0, 'stock' => 10, 'is_default' => false],
            ['name' => 'Medium', 'value' => 'M', 'price_adjustment' => 0, 'stock' => 15, 'is_default' => true],
            ['name' => 'Large', 'value' => 'L', 'price_adjustment' => 5.00, 'stock' => 8, 'is_default' => false],
            ['name' => 'X-Large', 'value' => 'XL', 'price_adjustment' => 10.00, 'stock' => 5, 'is_default' => false],
        ];
        
        // Add specifications, colors, and sizes to each product
        foreach ($products as $product) {
            // Determine which specification template to use based on the product category
            $categoryName = $product->category ? $product->category->name : 'Default';
            $specTemplate = $specificationTemplates['Default']; // Default template
            
            // Try to find a more specific template
            foreach ($specificationTemplates as $category => $specs) {
                if (stripos($categoryName, $category) !== false) {
                    $specTemplate = $specs;
                    break;
                }
            }
            
            // Add specifications
            foreach ($specTemplate as $index => $spec) {
                ProductSpecification::create([
                    'product_id' => $product->id,
                    'key' => $spec['key'],
                    'value' => $spec['value'],
                    'display_order' => $index,
                ]);
            }
            
            // Add colors (3 random colors)
            $selectedColors = collect($colorTemplates)->random(3);
            foreach ($selectedColors as $index => $color) {
                ProductColor::create([
                    'product_id' => $product->id,
                    'name' => $color['name'],
                    'color_code' => $color['color_code'],
                    'price_adjustment' => $color['price_adjustment'],
                    'stock' => $color['stock'],
                    'display_order' => $index,
                    'is_default' => $index === 0, // Make the first one default
                ]);
            }
            
            // Add sizes (all sizes)
            foreach ($sizeTemplates as $index => $size) {
                ProductSize::create([
                    'product_id' => $product->id,
                    'name' => $size['name'],
                    'value' => $size['value'],
                    'price_adjustment' => $size['price_adjustment'],
                    'stock' => $size['stock'],
                    'display_order' => $index,
                    'is_default' => $index === 1, // Make Medium the default
                ]);
            }
            
            // Add product to multiple branches if there are more than one branch
            $branches = Branch::all();
            if ($branches->count() > 1) {
                // Set the product as multi-branch
                $product->update(['is_multi_branch' => true]);
                
                // Add the product to 2-3 random branches
                $selectedBranches = $branches->random(min(3, $branches->count()));
                foreach ($selectedBranches as $branch) {
                    // Skip if it's the product's main branch
                    if ($branch->id === $product->branch_id) {
                        continue;
                    }
                    
                    ProductBranch::create([
                        'product_id' => $product->id,
                        'branch_id' => $branch->id,
                        'stock' => rand(5, 20),
                        'is_available' => true,
                        'price' => $product->price + rand(-5, 10), // Slightly different price
                    ]);
                }
            }
        }
        
        $this->command->info('Product specifications seeded successfully!');
    }
}
