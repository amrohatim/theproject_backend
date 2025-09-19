# Vendor Products with Colors Seeder

This seeder creates 2000 products with color variants for the specified vendor user.

## Overview

The `VendorProductsWithColorsSeeder` generates:
- **2000 products** with realistic data
- **One color variant per product** (as specified)
- **Random images** from https://picsum.photos
- **Proper data relationships** (vendor, branches, categories)
- **Arabic translations** for product names and descriptions
- **Unique SKUs** for each product
- **Realistic pricing** and stock levels

## Prerequisites

Before running the seeder, ensure:

1. **Vendor User Exists**: The vendor with email `gogoh3296@gmail.com` must exist
2. **Company Record**: The vendor must have an associated company
3. **Branches**: The company must have at least one branch
4. **Categories**: There must be leaf categories (categories with parents but no children) for products

## Pre-Flight Check

Run the pre-flight check script to verify all requirements:

```bash
php check_vendor_for_seeding.php
```

This script will verify:
- ✅ Vendor user exists
- ✅ Company is associated with the vendor
- ✅ Branches exist for the company
- ✅ Product categories are available

## Running the Seeder

### Method 1: Run Specific Seeder
```bash
php artisan db:seed --class=VendorProductsWithColorsSeeder
```

### Method 2: Add to DatabaseSeeder
Add this line to your `DatabaseSeeder.php`:
```php
$this->call(VendorProductsWithColorsSeeder::class);
```

Then run:
```bash
php artisan db:seed
```

## Seeder Features

### Batch Processing
- Products are created in **batches of 100**
- Progress is displayed every batch
- Database transactions ensure data integrity
- Error handling with rollback on failure

### Product Data Generation
- **Names**: Combination of adjectives + base names + unique numbers
- **Descriptions**: Realistic product descriptions in English and Arabic
- **Pricing**: Realistic price ranges from $10 to $1000
- **Stock**: Random stock levels between 10-100 units
- **SKUs**: Auto-generated based on product names
- **Images**: Random images from Picsum Photos (800x600)

### Color Variants
Each product gets exactly one color variant with:
- **29 predefined colors** with proper hex codes
- **Random color selection** from the predefined list
- **Color-specific images** from Picsum Photos
- **Price adjustments** (0-20 additional cost)
- **Stock allocation** (portion of total product stock)
- **Default flag** set to true (since it's the only color)

### Supported Colors
The seeder includes 29 colors:
- DarkRed (#8B0000), IndianRed (#CD5C5C), LightCoral (#F08080)
- Salmon (#FA8072), Orange (#FFA500), Red (#FF0000)
- Blue (#0000FF), Green (#008000), Navy Blue (#000080)
- Black (#000000), White (#FFFFFF), Gray (#808080)
- Yellow (#FFFF00), Purple (#800080), Pink (#FFC0CB)
- Brown (#A52A2A), Silver (#C0C0C0), Gold (#FFD700)
- And many more...

## Database Structure

### Products Table Fields
- `user_id`: Links to vendor user
- `branch_id`: Random branch from vendor's company
- `category_id`: Random leaf category
- `name`: Generated product name
- `product_name_arabic`: Arabic translation
- `description`: English description
- `product_description_arabic`: Arabic description
- `price`: Base price
- `original_price`: Higher price for discount display
- `stock`: Total stock quantity
- `sku`: Auto-generated SKU
- `image`: Random image URL
- `is_available`: Always true
- `featured`: 10% chance of being featured
- `rating`: Random rating between 3.5-5.0

### Product Colors Table Fields
- `product_id`: Links to parent product
- `name`: Color name (e.g., "Navy Blue")
- `color_code`: Hex color code (e.g., "#000080")
- `image`: Color-specific image URL
- `price_adjustment`: Additional cost for this color (0-20)
- `stock`: Stock for this color variant
- `display_order`: Always 0 (single color)
- `is_default`: Always true (single color)

## Error Handling

The seeder includes comprehensive error handling:
- **Transaction rollback** on batch failures
- **Detailed logging** of errors
- **Progress tracking** with batch information
- **Graceful failure** with informative messages

## Performance

- **Batch processing** prevents memory issues
- **Database transactions** ensure consistency
- **Progress updates** every 100 products
- **Estimated time**: ~5-10 minutes for 2000 products

## Troubleshooting

### Common Issues

1. **Vendor not found**
   - Ensure user with email `gogoh3296@gmail.com` exists
   - Check user role is 'vendor'

2. **No company found**
   - Vendor must have associated company record
   - Check `companies` table for `user_id` matching vendor

3. **No branches found**
   - Company must have at least one branch
   - Check `branches` table for `company_id`

4. **No categories found**
   - Need leaf categories (categories with parent but no children)
   - Check categories with `type = 'product'` and `parent_id IS NOT NULL`

5. **Memory issues**
   - Increase PHP memory limit: `php -d memory_limit=512M artisan db:seed`
   - Reduce batch size in seeder if needed

### Verification

After running the seeder, verify the results:

```sql
-- Check total products created
SELECT COUNT(*) FROM products WHERE user_id = (SELECT id FROM users WHERE email = 'gogoh3296@gmail.com');

-- Check products with colors
SELECT COUNT(*) FROM product_colors pc 
JOIN products p ON pc.product_id = p.id 
WHERE p.user_id = (SELECT id FROM users WHERE email = 'gogoh3296@gmail.com');

-- Sample products
SELECT p.name, p.price, p.stock, pc.name as color_name, pc.color_code 
FROM products p 
JOIN product_colors pc ON p.id = pc.product_id 
WHERE p.user_id = (SELECT id FROM users WHERE email = 'gogoh3296@gmail.com')
LIMIT 10;
```

## Support

If you encounter issues:
1. Run the pre-flight check script first
2. Check Laravel logs for detailed error messages
3. Verify database constraints and relationships
4. Ensure sufficient disk space and memory
