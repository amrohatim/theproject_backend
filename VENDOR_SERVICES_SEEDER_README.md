# Vendor Services Seeder

This seeder creates 2000 services for the specified vendor user following the exact data structure from the ServiceController.

## Overview

The `VendorServicesSeeder` generates:
- **2000 services** with realistic data
- **Random images** from https://picsum.photos
- **Arabic translations** for service names and descriptions
- **Proper data relationships** (vendor, branches, categories)
- **Realistic pricing** and duration values
- **Home service flags** (25% chance)
- **Featured flags** (10% chance)
- **Rating values** between 3.5-5.0

## Prerequisites

Before running the seeder, ensure:

1. **Vendor User Exists**: The vendor with email `gogoh3296@gmail.com` must exist
2. **Company Record**: The vendor must have an associated company
3. **Branches**: The company must have at least one branch
4. **Service Categories**: There must be active child service categories (categories with parent_id) in the database
5. **Database Schema**: All required columns must exist in the services table

## Pre-Flight Check

Run the pre-flight check script to verify all requirements:

```bash
php check_vendor_for_services_seeding.php
```

This script will verify:
- ✅ Vendor user exists
- ✅ Company is associated with the vendor
- ✅ Branches exist for the company
- ✅ Child service categories are available (categories with parent_id)
- ✅ Services table has all required columns

## Running the Seeder

### Method 1: Run Specific Seeder
```bash
php artisan db:seed --class=VendorServicesSeeder
```

### Method 2: Add to DatabaseSeeder
Add this line to your `DatabaseSeeder.php`:
```php
$this->call(VendorServicesSeeder::class);
```

Then run:
```bash
php artisan db:seed
```

## Seeder Features

### Batch Processing
- Services are created in **batches of 100**
- Progress is displayed every batch
- Database transactions ensure data integrity
- Error handling with rollback on failure

### Service Data Generation
- **Names**: 100+ predefined service types with adjectives and unique numbers
- **Descriptions**: Realistic service descriptions in English and Arabic
- **Pricing**: Realistic price ranges from $25 to $600
- **Duration**: Realistic durations from 15 minutes to 6 hours
- **Images**: Random images from Picsum Photos (800x600)
- **Categories**: Random selection from available service categories

### Service Types Included
The seeder includes diverse service categories:
- **Beauty Services**: Hair styling, facial treatments, manicures, makeup
- **Wellness Services**: Massages, fitness training, yoga, pilates
- **Home Services**: Cleaning, maintenance, repairs, pest control
- **Automotive Services**: Car wash, detailing, repairs, maintenance
- **Professional Services**: Photography, web design, tutoring, consulting
- **Pet Services**: Grooming, training, sitting, veterinary
- **Garden Services**: Design, maintenance, landscaping

### Arabic Support
- **Service Names**: Contextual Arabic translations
- **Descriptions**: Professional Arabic service descriptions
- **RTL Compatibility**: Proper Arabic text formatting

## Database Structure

### Services Table Fields
- `branch_id`: Links to vendor's branch
- `category_id`: Random service category
- `name`: Generated service name
- `service_name_arabic`: Arabic translation
- `description`: English description
- `service_description_arabic`: Arabic description
- `price`: Service price ($25-$600)
- `duration`: Service duration (15-360 minutes)
- `image`: Random image URL from Picsum
- `is_available`: Always true
- `home_service`: 25% chance of being true
- `featured`: 10% chance of being true
- `rating`: Random rating between 3.5-5.0

### Validation Compliance
The seeder follows the exact validation rules from ServiceController:
- ✅ `name`: Required string, max 255 characters
- ✅ `service_name_arabic`: Required string, max 255 characters
- ✅ `category_id`: Valid category ID
- ✅ `branch_id`: Valid branch ID belonging to vendor
- ✅ `price`: Numeric, minimum 0
- ✅ `duration`: Integer, minimum 1
- ✅ `description`: Optional string
- ✅ `service_description_arabic`: Optional string
- ✅ `image`: Valid image URL format

## Error Handling

The seeder includes comprehensive error handling:
- **Transaction rollback** on batch failures
- **Detailed logging** of errors
- **Progress tracking** with batch information
- **Graceful failure** with informative messages

## Performance

- **Batch processing** prevents memory issues
- **Database transactions** ensure consistency
- **Progress updates** every 100 services
- **Estimated time**: ~3-7 minutes for 2000 services

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

4. **No service categories found**
   - Need active child service categories (categories with parent_id)
   - Check categories with `type = 'service'`, `is_active = true`, and `parent_id IS NOT NULL`
   - Services can only be assigned to child categories, not parent categories

5. **Missing table columns**
   - Run migrations to add Arabic columns
   - Ensure `home_service`, `featured`, and other columns exist

### Required Migrations
Ensure these migrations have been run:
- `2025_07_31_000001_add_arabic_columns_to_services_table.php`
- `2025_08_15_000001_add_home_service_to_services_table.php`
- `2025_05_06_135909_add_featured_flag_to_services_table.php`

### Verification

After running the seeder, verify the results:

```sql
-- Check total services created
SELECT COUNT(*) FROM services s
JOIN branches b ON s.branch_id = b.id
JOIN companies c ON b.company_id = c.id
WHERE c.user_id = (SELECT id FROM users WHERE email = 'gogoh3296@gmail.com');

-- Sample services with details
SELECT s.name, s.service_name_arabic, s.price, s.duration, s.home_service, c.name as category
FROM services s
JOIN categories c ON s.category_id = c.id
JOIN branches b ON s.branch_id = b.id
JOIN companies co ON b.company_id = co.id
WHERE co.user_id = (SELECT id FROM users WHERE email = 'gogoh3296@gmail.com')
LIMIT 10;

-- Check service distribution by category
SELECT c.name, COUNT(*) as service_count
FROM services s
JOIN categories c ON s.category_id = c.id
JOIN branches b ON s.branch_id = b.id
JOIN companies co ON b.company_id = co.id
WHERE co.user_id = (SELECT id FROM users WHERE email = 'gogoh3296@gmail.com')
GROUP BY c.name
ORDER BY service_count DESC;
```

## Support

If you encounter issues:
1. Run the pre-flight check script first
2. Check Laravel logs for detailed error messages
3. Verify database schema matches requirements
4. Ensure all required migrations have been run
