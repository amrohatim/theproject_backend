# Service Categories Seeder

This document explains how to use the `ServiceCategoriesSeeder` to populate your database with service categories based on the image directory structure.

## Overview

The `ServiceCategoriesSeeder` automatically scans the `app service category images` directory and creates:
- **Parent categories** from folder names
- **Child categories** from image filenames within each folder
- **Proper parent-child relationships** in the database
- **Image path associations** for each category

## Directory Structure

The seeder expects the following directory structure:

```
app service category images/
├── Artistic Services/
│   ├── Artistic Services.jpg          (Parent category image)
│   ├── Craft workshops.jpg            (Child category)
│   ├── Painting classes.jpg           (Child category)
│   ├── Photography sessions.jpg       (Child category)
│   └── Pottery making.jpg             (Child category)
├── Healthcare & Femtech/
│   ├── Healthcare & Femtech.jpg       (Parent category image)
│   ├── Fertility monitoring.jpg       (Child category)
│   ├── Mental Health Support.jpg      (Child category)
│   ├── Pregnancy guides.jpg           (Child category)
│   └── Women's Health.jpg             (Child category)
└── ... (other categories)
```

## Key Features

### 1. Dynamic Directory Scanning
- Automatically detects all folders in the `app service category images` directory
- Creates parent categories from folder names
- No need to manually update the seeder when adding new categories

### 2. Intelligent Image Mapping
- Parent category images: Files with the same name as the folder
- Child category images: All other image files in the folder
- Proper image path generation for database storage

### 3. Comprehensive Metadata
- Pre-defined descriptions and icons for known categories
- Fallback descriptions and icons for unknown categories
- FontAwesome icons for consistent UI representation

### 4. Error Handling
- Graceful handling of missing directories or images
- Detailed console output for debugging
- Option to clear existing service categories before seeding

## Usage Instructions

### 1. Test the Seeder (Recommended)

Before running the actual seeder, test the directory structure:

```bash
php test_service_categories_seeder.php
```

This will:
- Verify the directory structure
- Show what categories will be created
- Test metadata generation
- Provide a preview of the seeding process

### 2. Run the Seeder

#### Option A: Run only the Service Categories Seeder
```bash
php artisan db:seed --class=ServiceCategoriesSeeder
```

#### Option B: Run with confirmation prompts
The seeder will ask if you want to clear existing service categories before seeding.

#### Option C: Include in DatabaseSeeder
Add to your `DatabaseSeeder.php`:

```php
$this->call([
    // ... other seeders
    ServiceCategoriesSeeder::class,
]);
```

Then run:
```bash
php artisan db:seed
```

### 3. Verify Results

Check your database to ensure:
- Parent categories were created with `parent_id = null`
- Child categories were created with proper `parent_id` references
- Image paths are correctly stored
- All categories have `type = 'service'`

## Database Schema

The seeder populates the `categories` table with these fields:

| Field | Description | Example |
|-------|-------------|---------|
| `name` | Category name from folder/filename | "Yoga" |
| `description` | Generated description | "Yoga classes and sessions" |
| `image` | Relative path to image | "app service category images/Fitness Classes/Yoga.jpg" |
| `parent_id` | ID of parent category (null for parents) | 15 |
| `is_active` | Always set to true | true |
| `type` | Always set to 'service' | "service" |
| `icon` | FontAwesome icon class | "fas fa-leaf" |

## Supported Categories

The seeder includes pre-defined metadata for these categories:

### Parent Categories:
- Artistic Services
- Elderly Care & Companionship Services
- Fitness Classes
- Healthcare & Femtech
- Makeup Services
- Nail Care
- Nutrition Counseling
- Salon Services
- Spa Treatments
- Therapy Sessions
- Wellness Workshops

### Child Categories:
Over 30 child categories with specific icons and descriptions (see the seeder code for complete list).

## Customization

### Adding New Categories
1. Create a new folder in `app service category images/`
2. Add the parent category image (same name as folder)
3. Add child category images
4. Run the seeder - it will automatically detect the new structure

### Updating Metadata
Edit the `getCategoryMetadata()` and related methods in the seeder to:
- Add descriptions for new categories
- Update icons
- Modify fallback behavior

## Troubleshooting

### Common Issues:

1. **Directory not found**
   - Ensure `app service category images` exists in the project root
   - Check file permissions

2. **No categories created**
   - Verify image files exist in category folders
   - Check that folder names don't contain special characters

3. **Missing parent category images**
   - Ensure each folder has an image with the same name as the folder
   - The seeder will use the first available image as fallback

4. **Database errors**
   - Ensure the `categories` table exists and has the required columns
   - Check for foreign key constraints

### Debug Mode:
The seeder provides detailed console output showing:
- Number of directories found
- Categories being processed
- Number of child categories created
- Any errors encountered

## Best Practices

1. **Backup your database** before running the seeder
2. **Test with the test script** first
3. **Use consistent naming** for folders and images
4. **Organize images logically** within category folders
5. **Run the seeder in a development environment** first

## Integration with Existing Code

This seeder is designed to work alongside existing category seeders. It:
- Only creates categories with `type = 'service'`
- Doesn't interfere with product categories
- Can be run multiple times (with clearing option)
- Follows Laravel seeding best practices
