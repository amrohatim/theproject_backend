<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SizeCategory;
use App\Models\StandardizedSize;

class SizeCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create size categories
        $clothesCategory = SizeCategory::create([
            'name' => 'clothes',
            'display_name' => 'Clothes',
            'description' => 'Clothing sizes from XXS to 5XL with symbol representations',
            'is_active' => true,
            'display_order' => 1,
        ]);

        $shoesCategory = SizeCategory::create([
            'name' => 'shoes',
            'display_name' => 'Shoes',
            'description' => 'EU shoe sizes from 16 to 48 with foot length mappings',
            'is_active' => true,
            'display_order' => 2,
        ]);

        $hatsCategory = SizeCategory::create([
            'name' => 'hats',
            'display_name' => 'Hats',
            'description' => 'EU hat sizes from 40 to 64 with age group mappings',
            'is_active' => true,
            'display_order' => 3,
        ]);

        // Clothes sizes
        $clothesSizes = [
            ['name' => 'XXS', 'value' => 'Extra Extra Small', 'display_order' => 1],
            ['name' => 'XS', 'value' => 'Extra Small', 'display_order' => 2],
            ['name' => 'S', 'value' => 'Small', 'display_order' => 3],
            ['name' => 'M', 'value' => 'Medium', 'display_order' => 4],
            ['name' => 'L', 'value' => 'Large', 'display_order' => 5],
            ['name' => 'XL', 'value' => 'Extra Large', 'display_order' => 6],
            ['name' => 'XXL', 'value' => 'Extra Extra Large', 'display_order' => 7],
            ['name' => '3XL', 'value' => 'Triple Extra Large', 'display_order' => 8],
            ['name' => '4XL', 'value' => 'Quadruple Extra Large', 'display_order' => 9],
            ['name' => '5XL', 'value' => 'Quintuple Extra Large', 'display_order' => 10],
        ];

        foreach ($clothesSizes as $size) {
            StandardizedSize::create([
                'size_category_id' => $clothesCategory->id,
                'name' => $size['name'],
                'value' => $size['value'],
                'additional_info' => null,
                'display_order' => $size['display_order'],
                'is_active' => true,
            ]);
        }

        // Shoes sizes with foot length mappings
        $shoesSizes = [
            ['name' => '16', 'value' => 'EU 16', 'additional_info' => '9.7cm', 'display_order' => 1],
            ['name' => '17', 'value' => 'EU 17', 'additional_info' => '10.4cm', 'display_order' => 2],
            ['name' => '18', 'value' => 'EU 18', 'additional_info' => '11.0cm', 'display_order' => 3],
            ['name' => '19', 'value' => 'EU 19', 'additional_info' => '11.7cm', 'display_order' => 4],
            ['name' => '20', 'value' => 'EU 20', 'additional_info' => '12.3cm', 'display_order' => 5],
            ['name' => '21', 'value' => 'EU 21', 'additional_info' => '13.0cm', 'display_order' => 6],
            ['name' => '22', 'value' => 'EU 22', 'additional_info' => '13.7cm', 'display_order' => 7],
            ['name' => '23', 'value' => 'EU 23', 'additional_info' => '14.3cm', 'display_order' => 8],
            ['name' => '24', 'value' => 'EU 24', 'additional_info' => '15.0cm', 'display_order' => 9],
            ['name' => '25', 'value' => 'EU 25', 'additional_info' => '15.7cm', 'display_order' => 10],
            ['name' => '26', 'value' => 'EU 26', 'additional_info' => '16.3cm', 'display_order' => 11],
            ['name' => '27', 'value' => 'EU 27', 'additional_info' => '17.0cm', 'display_order' => 12],
            ['name' => '28', 'value' => 'EU 28', 'additional_info' => '17.7cm', 'display_order' => 13],
            ['name' => '29', 'value' => 'EU 29', 'additional_info' => '18.3cm', 'display_order' => 14],
            ['name' => '30', 'value' => 'EU 30', 'additional_info' => '19.0cm', 'display_order' => 15],
            ['name' => '31', 'value' => 'EU 31', 'additional_info' => '19.7cm', 'display_order' => 16],
            ['name' => '32', 'value' => 'EU 32', 'additional_info' => '20.3cm', 'display_order' => 17],
            ['name' => '33', 'value' => 'EU 33', 'additional_info' => '21.0cm', 'display_order' => 18],
            ['name' => '34', 'value' => 'EU 34', 'additional_info' => '21.7cm', 'display_order' => 19],
            ['name' => '35', 'value' => 'EU 35', 'additional_info' => '22.5cm', 'display_order' => 20],
            ['name' => '36', 'value' => 'EU 36', 'additional_info' => '23.0cm', 'display_order' => 21],
            ['name' => '37', 'value' => 'EU 37', 'additional_info' => '23.5cm', 'display_order' => 22],
            ['name' => '38', 'value' => 'EU 38', 'additional_info' => '24.0cm', 'display_order' => 23],
            ['name' => '39', 'value' => 'EU 39', 'additional_info' => '24.5cm', 'display_order' => 24],
            ['name' => '40', 'value' => 'EU 40', 'additional_info' => '25.0cm', 'display_order' => 25],
            ['name' => '41', 'value' => 'EU 41', 'additional_info' => '25.5cm', 'display_order' => 26],
            ['name' => '42', 'value' => 'EU 42', 'additional_info' => '26.0cm', 'display_order' => 27],
            ['name' => '43', 'value' => 'EU 43', 'additional_info' => '26.5cm', 'display_order' => 28],
            ['name' => '44', 'value' => 'EU 44', 'additional_info' => '27.0cm', 'display_order' => 29],
            ['name' => '45', 'value' => 'EU 45', 'additional_info' => '27.5cm', 'display_order' => 30],
            ['name' => '46', 'value' => 'EU 46', 'additional_info' => '28.0cm', 'display_order' => 31],
            ['name' => '47', 'value' => 'EU 47', 'additional_info' => '28.5cm', 'display_order' => 32],
            ['name' => '48', 'value' => 'EU 48', 'additional_info' => '29.0cm', 'display_order' => 33],
        ];

        foreach ($shoesSizes as $size) {
            StandardizedSize::create([
                'size_category_id' => $shoesCategory->id,
                'name' => $size['name'],
                'value' => $size['value'],
                'additional_info' => $size['additional_info'],
                'display_order' => $size['display_order'],
                'is_active' => true,
            ]);
        }

        // Hats sizes with age group mappings
        $hatsSizes = [
            ['name' => '40', 'value' => 'EU 40', 'additional_info' => 'Newborn (0-3 months)', 'display_order' => 1],
            ['name' => '42', 'value' => 'EU 42', 'additional_info' => 'Baby (3-6 months)', 'display_order' => 2],
            ['name' => '44', 'value' => 'EU 44', 'additional_info' => 'Baby (6-12 months)', 'display_order' => 3],
            ['name' => '46', 'value' => 'EU 46', 'additional_info' => 'Toddler (1-2 years)', 'display_order' => 4],
            ['name' => '48', 'value' => 'EU 48', 'additional_info' => 'Toddler (2-3 years)', 'display_order' => 5],
            ['name' => '50', 'value' => 'EU 50', 'additional_info' => 'Child (3-5 years)', 'display_order' => 6],
            ['name' => '52', 'value' => 'EU 52', 'additional_info' => 'Child (5-8 years)', 'display_order' => 7],
            ['name' => '54', 'value' => 'EU 54', 'additional_info' => 'Child (8-12 years)', 'display_order' => 8],
            ['name' => '56', 'value' => 'EU 56', 'additional_info' => 'Teen/Adult Small', 'display_order' => 9],
            ['name' => '57', 'value' => 'EU 57', 'additional_info' => 'Adult Small', 'display_order' => 10],
            ['name' => '58', 'value' => 'EU 58', 'additional_info' => 'Adult Medium', 'display_order' => 11],
            ['name' => '59', 'value' => 'EU 59', 'additional_info' => 'Adult Medium-Large', 'display_order' => 12],
            ['name' => '60', 'value' => 'EU 60', 'additional_info' => 'Adult Large', 'display_order' => 13],
            ['name' => '61', 'value' => 'EU 61', 'additional_info' => 'Adult Large', 'display_order' => 14],
            ['name' => '62', 'value' => 'EU 62', 'additional_info' => 'Adult Extra Large', 'display_order' => 15],
            ['name' => '63', 'value' => 'EU 63', 'additional_info' => 'Adult Extra Large', 'display_order' => 16],
            ['name' => '64', 'value' => 'EU 64', 'additional_info' => 'Adult XXL', 'display_order' => 17],
        ];

        foreach ($hatsSizes as $size) {
            StandardizedSize::create([
                'size_category_id' => $hatsCategory->id,
                'name' => $size['name'],
                'value' => $size['value'],
                'additional_info' => $size['additional_info'],
                'display_order' => $size['display_order'],
                'is_active' => true,
            ]);
        }
    }
}
