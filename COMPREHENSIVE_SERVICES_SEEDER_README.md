# Comprehensive Services Seeder Documentation

This document explains how to use the comprehensive services seeders to populate your Laravel marketplace with realistic, detailed services across all service categories.

## Overview

The Comprehensive Services Seeder system creates 5-10 detailed services for each service subcategory identified in your service categories structure. Each service includes:

- **Comprehensive descriptions** (100+ words each)
- **Realistic pricing** based on service complexity
- **Appropriate duration** in minutes
- **Professional service names**
- **High-quality stock images**
- **Realistic ratings** (4.5-4.9)
- **Featured flags** for premium services
- **Home service flags** where appropriate
- **Random assignment** to active branches

## Prerequisites

Before running the services seeders, ensure you have:

1. **Service Categories**: Run `ServiceCategoriesSeeder` first to create the category structure
2. **Active Branches**: Ensure you have active branches in your database
3. **Database Connection**: Verify your database connection is working

## Seeder Structure

The comprehensive services are split into multiple parts for manageability:

### Part 1: Artistic Services & Elderly Care
- **File**: `ComprehensiveServicesSeeder.php`
- **Categories**: 
  - Craft workshops (5 services)
  - Painting classes (5 services)
  - Photography sessions (5 services)
  - Pottery making (5 services)
  - Companionship visits (5 services)
  - In-home care (5 services)

### Part 2: Fitness Classes
- **File**: `ComprehensiveServicesSeederPart2.php`
- **Categories**:
  - Pilates (5 services)
  - Strength training (5 services)
  - Yoga (5 services)
  - Zumba (5 services)

### Part 3: Healthcare & Femtech (Partial)
- **File**: `ComprehensiveServicesSeederPart3.php`
- **Categories**:
  - Fertility monitoring (5 services)
  - Menstrual tracking (5 services)

### Part 4: Beauty & Makeup Services
- **File**: `ComprehensiveServicesSeederPart4.php`
- **Categories**:
  - Bridal makeup (5 services)
  - Event makeup (5 services)

### Part 5: Therapy & Nutrition
- **File**: `ComprehensiveServicesSeederPart5.php`
- **Categories**:
  - Individual Therapy (5 services)
  - Diet plans (5 services)

## Usage Instructions

### Option 1: Run All Parts (Recommended)
```bash
php artisan db:seed --class=MasterComprehensiveServicesSeeder
```

This runs all parts in sequence and provides a comprehensive summary.

### Option 2: Test with Part 1 Only
```bash
php artisan db:seed --class=TestComprehensiveServicesSeeder
```

This runs only Part 1 to test functionality before running the full seeder.

### Option 3: Run Individual Parts
```bash
# Part 1: Artistic Services & Elderly Care
php artisan db:seed --class=ComprehensiveServicesSeeder

# Part 2: Fitness Classes
php artisan db:seed --class=ComprehensiveServicesSeederPart2

# Part 3: Healthcare & Femtech
php artisan db:seed --class=ComprehensiveServicesSeederPart3

# Part 4: Beauty & Makeup Services
php artisan db:seed --class=ComprehensiveServicesSeederPart4

# Part 5: Therapy & Nutrition
php artisan db:seed --class=ComprehensiveServicesSeederPart5
```

## Service Categories Covered

### Artistic Services
- **Craft workshops**: Jewelry making, woodworking, textile arts, candle making, leatherworking
- **Painting classes**: Watercolor landscapes, acrylic portraits, abstract art, oil painting, plein air
- **Photography sessions**: Professional portraits, wedding photography, nature/wildlife, street photography, product photography
- **Pottery making**: Wheel throwing, ceramic sculpture, glazing workshops, functional pottery, kids classes

### Elderly Care & Companionship Services
- **Companionship visits**: Weekly sessions, memory care, social outings, technology assistance, grief support
- **In-home care**: Personal care, medication management, housekeeping, dementia care, respite care

### Fitness Classes
- **Pilates**: Beginner mat, reformer intermediate, seniors, prenatal, rehabilitation
- **Strength training**: Beginner fundamentals, powerlifting, functional strength, women's bootcamp, athletic performance
- **Yoga**: Hatha beginners, power vinyasa, prenatal, restorative, hot yoga
- **Zumba**: Fitness party, gold for seniors, aqua zumba, toning with weights, kids dance

### Healthcare & Femtech
- **Fertility monitoring**: Comprehensive assessment, ovulation tracking, preconception counseling, male fertility, technology integration
- **Menstrual tracking**: Health assessment, PMS management, cycle education, hormonal balance, digital tracking

### Beauty & Makeup Services
- **Bridal makeup**: Complete packages, bridal party, destination weddings, vintage themes, DIY lessons
- **Event makeup**: Red carpet glamour, corporate/professional, prom/formal, holiday/seasonal, photography

### Therapy & Nutrition
- **Individual Therapy**: CBT, trauma/EMDR, mindfulness-based, life transitions, anxiety management
- **Diet plans**: Weight management, medical nutrition therapy, plant-based transition, sports nutrition, intuitive eating

## Service Details

Each service includes:

### Comprehensive Descriptions
- Minimum 100 words per service
- Professional, marketing-friendly language
- Specific benefits and what's included
- Target audience identification
- Expected outcomes

### Realistic Pricing
- **Basic services**: $12-45 (kids classes, basic fitness)
- **Standard services**: $50-100 (most wellness and beauty services)
- **Premium services**: $100-200 (specialized therapy, bridal services)
- **Luxury services**: $200-300 (destination weddings, comprehensive programs)

### Appropriate Durations
- **Quick services**: 30-45 minutes (basic beauty, kids classes)
- **Standard sessions**: 50-75 minutes (therapy, fitness classes)
- **Extended services**: 90-180 minutes (comprehensive assessments, workshops)
- **Full programs**: 240-360 minutes (intensive workshops, care services)

### Professional Features
- High-quality Unsplash images
- Realistic ratings (4.5-4.9 range)
- Featured flags for premium services
- Home service flags where appropriate
- Random branch assignment for distribution

## Extending the Seeders

To add more categories or services:

1. **Identify missing categories** from your service category images
2. **Create additional seeder parts** following the existing pattern
3. **Add comprehensive service data** with 100+ word descriptions
4. **Update the master seeder** to include new parts
5. **Test thoroughly** before running in production

## Troubleshooting

### Common Issues

1. **No branches found**: Ensure you have active branches in your database
2. **No service categories**: Run `ServiceCategoriesSeeder` first
3. **Memory issues**: Run parts individually instead of the master seeder
4. **Timeout issues**: Increase PHP execution time for large datasets

### Verification

After running the seeders, verify:
- Services are created in the database
- Services are properly linked to categories and branches
- Images are accessible
- Descriptions are complete and professional
- Pricing and durations are realistic

## Production Considerations

- **Backup your database** before running seeders
- **Test in development** environment first
- **Consider running in parts** during off-peak hours
- **Monitor performance** during seeding process
- **Verify data integrity** after completion

## Support

If you encounter issues:
1. Check the console output for specific error messages
2. Verify prerequisites are met
3. Test with the TestComprehensiveServicesSeeder first
4. Run individual parts to isolate issues
5. Check database logs for constraint violations

This comprehensive seeder system provides a solid foundation of realistic, professional services for your marketplace platform.
