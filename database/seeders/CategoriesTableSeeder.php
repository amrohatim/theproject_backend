<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Helpers\UnsplashImageHelper;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Product parent categories
        $productParentCategories = [
            [
                'name' => 'Electronics',
                'description' => 'Electronic devices and accessories',
                'image' => UnsplashImageHelper::downloadAndSaveImage('electronics gadgets', 'public/images/categories/electronics.jpg', 800, 600),
                'is_active' => true,
                'type' => 'product',
                'icon' => 'fas fa-laptop',
            ],
            [
                'name' => 'Home & Kitchen',
                'description' => 'Products for your home and kitchen',
                'image' => UnsplashImageHelper::downloadAndSaveImage('home kitchen', 'public/images/categories/home-kitchen.jpg', 800, 600),
                'is_active' => true,
                'type' => 'product',
                'icon' => 'fas fa-home',
            ],
            [
                'name' => 'Food & Beverages',
                'description' => 'Food products and beverages',
                'image' => UnsplashImageHelper::downloadAndSaveImage('food and drinks', 'public/images/categories/food-beverages.jpg', 800, 600),
                'is_active' => true,
                'type' => 'product',
                'icon' => 'fas fa-utensils',
            ],
        ];

        // Create product parent categories
        $productParentIds = [];
        foreach ($productParentCategories as $category) {
            $createdCategory = Category::create($category);
            $productParentIds[$createdCategory->name] = $createdCategory->id;
        }

        // Product subcategories
        $productSubcategories = [
            // Electronics subcategories
            [
                'name' => 'Smartphones',
                'description' => 'Mobile phones and accessories',
                'image' => UnsplashImageHelper::downloadAndSaveImage('smartphones', 'public/images/categories/smartphones.jpg', 800, 600),
                'parent_id' => $productParentIds['Electronics'],
                'is_active' => true,
                'type' => 'product',
                'icon' => 'fas fa-mobile-alt',
            ],
            [
                'name' => 'Laptops',
                'description' => 'Portable computers',
                'image' => '/images/categories/laptops.jpg',
                'parent_id' => $productParentIds['Electronics'],
                'is_active' => true,
                'type' => 'product',
                'icon' => 'fas fa-laptop',
            ],
            [
                'name' => 'Audio',
                'description' => 'Headphones, speakers, and audio equipment',
                'image' => '/images/categories/audio.jpg',
                'parent_id' => $productParentIds['Electronics'],
                'is_active' => true,
                'type' => 'product',
                'icon' => 'fas fa-headphones',
            ],

            // Home & Kitchen subcategories
            [
                'name' => 'Furniture',
                'description' => 'Tables, chairs, and other furniture',
                'image' => '/images/categories/furniture.jpg',
                'parent_id' => $productParentIds['Home & Kitchen'],
                'is_active' => true,
                'type' => 'product',
                'icon' => 'fas fa-couch',
            ],
            [
                'name' => 'Appliances',
                'description' => 'Kitchen and home appliances',
                'image' => '/images/categories/appliances.jpg',
                'parent_id' => $productParentIds['Home & Kitchen'],
                'is_active' => true,
                'type' => 'product',
                'icon' => 'fas fa-blender',
            ],

            // Food & Beverages subcategories
            [
                'name' => 'Snacks',
                'description' => 'Chips, nuts, and other snack foods',
                'image' => '/images/categories/snacks.jpg',
                'parent_id' => $productParentIds['Food & Beverages'],
                'is_active' => true,
                'type' => 'product',
                'icon' => 'fas fa-cookie',
            ],
            [
                'name' => 'Beverages',
                'description' => 'Drinks and beverages',
                'image' => '/images/categories/beverages.jpg',
                'parent_id' => $productParentIds['Food & Beverages'],
                'is_active' => true,
                'type' => 'product',
                'icon' => 'fas fa-wine-glass-alt',
            ],
        ];

        // Create product subcategories
        foreach ($productSubcategories as $category) {
            Category::create($category);
        }

        // Service parent categories - using local service category images
        $serviceParentCategories = [
            [
                'name' => 'Healthcare & Femtech',
                'description' => 'Women\'s health and femtech services',
                'image' => $this->getServiceCategoryImage('Healthcare & Femtech'),
                'is_active' => true,
                'type' => 'service',
                'icon' => 'fas fa-heartbeat',
            ],
            [
                'name' => 'Beauty & Wellness',
                'description' => 'Beauty, spa and wellness services',
                'image' => $this->getServiceCategoryImage('Spa Treatments'),
                'is_active' => true,
                'type' => 'service',
                'icon' => 'fas fa-spa',
            ],
            [
                'name' => 'Fitness & Wellness',
                'description' => 'Fitness classes and wellness workshops',
                'image' => $this->getServiceCategoryImage('Fitness Classes'),
                'is_active' => true,
                'type' => 'service',
                'icon' => 'fas fa-dumbbell',
            ],
            [
                'name' => 'Personal Care Services',
                'description' => 'Personal care and grooming services',
                'image' => $this->getServiceCategoryImage('Salon Services'),
                'is_active' => true,
                'type' => 'service',
                'icon' => 'fas fa-cut',
            ],
            [
                'name' => 'Therapy & Support',
                'description' => 'Therapy sessions and support services',
                'image' => $this->getServiceCategoryImage('Therapy Sessions'),
                'is_active' => true,
                'type' => 'service',
                'icon' => 'fas fa-hands-helping',
            ],
            [
                'name' => 'Creative & Artistic',
                'description' => 'Creative and artistic services',
                'image' => $this->getServiceCategoryImage('Artistic Services'),
                'is_active' => true,
                'type' => 'service',
                'icon' => 'fas fa-palette',
            ],
            [
                'name' => 'Care Services',
                'description' => 'Elderly care and companionship services',
                'image' => $this->getServiceCategoryImage('Elderly Care & Companionship Services'),
                'is_active' => true,
                'type' => 'service',
                'icon' => 'fas fa-heart',
            ],
        ];

        // Create service parent categories
        $serviceParentIds = [];
        foreach ($serviceParentCategories as $category) {
            $createdCategory = Category::create($category);
            $serviceParentIds[$createdCategory->name] = $createdCategory->id;
        }

        // Service subcategories - using local service category images
        $serviceSubcategories = [
            // Healthcare & Femtech subcategories
            [
                'name' => 'Women\'s Health',
                'description' => 'Women\'s health services and consultations',
                'image' => $this->getServiceCategoryImage('Healthcare & Femtech', 'Women\'s Health'),
                'parent_id' => $serviceParentIds['Healthcare & Femtech'],
                'is_active' => true,
                'type' => 'service',
                'icon' => 'fas fa-female',
            ],
            [
                'name' => 'Mental Health Support',
                'description' => 'Mental health support and counseling',
                'image' => $this->getServiceCategoryImage('Healthcare & Femtech', 'Mental Health Support'),
                'parent_id' => $serviceParentIds['Healthcare & Femtech'],
                'is_active' => true,
                'type' => 'service',
                'icon' => 'fas fa-brain',
            ],
            [
                'name' => 'Fertility monitoring',
                'description' => 'Fertility tracking and monitoring services',
                'image' => $this->getServiceCategoryImage('Healthcare & Femtech', 'Fertility monitoring'),
                'parent_id' => $serviceParentIds['Healthcare & Femtech'],
                'is_active' => true,
                'type' => 'service',
                'icon' => 'fas fa-heartbeat',
            ],
            [
                'name' => 'Pregnancy guides',
                'description' => 'Pregnancy guidance and support services',
                'image' => $this->getServiceCategoryImage('Healthcare & Femtech', 'Pregnancy guides'),
                'parent_id' => $serviceParentIds['Healthcare & Femtech'],
                'is_active' => true,
                'type' => 'service',
                'icon' => 'fas fa-baby',
            ],
            [
                'name' => 'Menstrual tracking',
                'description' => 'Menstrual cycle tracking and health services',
                'image' => $this->getServiceCategoryImage('Healthcare & Femtech', 'Menstrual tracking'),
                'parent_id' => $serviceParentIds['Healthcare & Femtech'],
                'is_active' => true,
                'type' => 'service',
                'icon' => 'fas fa-calendar-alt',
            ],

            // Beauty & Wellness subcategories
            [
                'name' => 'Spa Treatments',
                'description' => 'Relaxing spa treatments and therapies',
                'image' => $this->getServiceCategoryImage('Spa Treatments', 'Spa Treatments'),
                'parent_id' => $serviceParentIds['Beauty & Wellness'],
                'is_active' => true,
                'type' => 'service',
                'icon' => 'fas fa-spa',
            ],
            [
                'name' => 'Facials',
                'description' => 'Professional facial treatments',
                'image' => $this->getServiceCategoryImage('Spa Treatments', 'Facials'),
                'parent_id' => $serviceParentIds['Beauty & Wellness'],
                'is_active' => true,
                'type' => 'service',
                'icon' => 'fas fa-smile',
            ],
            [
                'name' => 'Massages',
                'description' => 'Therapeutic and relaxation massages',
                'image' => $this->getServiceCategoryImage('Spa Treatments', 'Massages'),
                'parent_id' => $serviceParentIds['Beauty & Wellness'],
                'is_active' => true,
                'type' => 'service',
                'icon' => 'fas fa-hands',
            ],
            [
                'name' => 'Body scrubs',
                'description' => 'Exfoliating body scrub treatments',
                'image' => $this->getServiceCategoryImage('Spa Treatments', 'Body scrubs'),
                'parent_id' => $serviceParentIds['Beauty & Wellness'],
                'is_active' => true,
                'type' => 'service',
                'icon' => 'fas fa-shower',
            ],

            // Fitness & Wellness subcategories
            [
                'name' => 'Yoga',
                'description' => 'Yoga classes and sessions',
                'image' => $this->getServiceCategoryImage('Fitness Classes', 'Yoga'),
                'parent_id' => $serviceParentIds['Fitness & Wellness'],
                'is_active' => true,
                'type' => 'service',
                'icon' => 'fas fa-leaf',
            ],
            [
                'name' => 'Pilates',
                'description' => 'Pilates classes and training',
                'image' => $this->getServiceCategoryImage('Fitness Classes', 'Pilates'),
                'parent_id' => $serviceParentIds['Fitness & Wellness'],
                'is_active' => true,
                'type' => 'service',
                'icon' => 'fas fa-dumbbell',
            ],
            [
                'name' => 'Zumba',
                'description' => 'Zumba dance fitness classes',
                'image' => $this->getServiceCategoryImage('Fitness Classes', 'Zumba'),
                'parent_id' => $serviceParentIds['Fitness & Wellness'],
                'is_active' => true,
                'type' => 'service',
                'icon' => 'fas fa-music',
            ],
            [
                'name' => 'Strength training',
                'description' => 'Strength and resistance training',
                'image' => $this->getServiceCategoryImage('Fitness Classes', 'Strength training'),
                'parent_id' => $serviceParentIds['Fitness & Wellness'],
                'is_active' => true,
                'type' => 'service',
                'icon' => 'fas fa-dumbbell',
            ],
            [
                'name' => 'Wellness Workshops',
                'description' => 'Health and wellness workshops',
                'image' => $this->getServiceCategoryImage('Wellness Workshops', 'Wellness Workshops'),
                'parent_id' => $serviceParentIds['Fitness & Wellness'],
                'is_active' => true,
                'type' => 'service',
                'icon' => 'fas fa-heart',
            ],
            [
                'name' => 'Mindfulness',
                'description' => 'Mindfulness and meditation sessions',
                'image' => $this->getServiceCategoryImage('Wellness Workshops', 'Mindfulness'),
                'parent_id' => $serviceParentIds['Fitness & Wellness'],
                'is_active' => true,
                'type' => 'service',
                'icon' => 'fas fa-brain',
            ],
            [
                'name' => 'Stress management',
                'description' => 'Stress management workshops and techniques',
                'image' => $this->getServiceCategoryImage('Wellness Workshops', 'Stress management'),
                'parent_id' => $serviceParentIds['Fitness & Wellness'],
                'is_active' => true,
                'type' => 'service',
                'icon' => 'fas fa-leaf',
            ],
            [
                'name' => 'Healthy cooking',
                'description' => 'Healthy cooking classes and workshops',
                'image' => $this->getServiceCategoryImage('Wellness Workshops', 'Healthy cooking'),
                'parent_id' => $serviceParentIds['Fitness & Wellness'],
                'is_active' => true,
                'type' => 'service',
                'icon' => 'fas fa-utensils',
            ],

            // Personal Care Services subcategories
            [
                'name' => 'Haircuts',
                'description' => 'Professional hair cutting services',
                'image' => $this->getServiceCategoryImage('Salon Services', 'Haircuts'),
                'parent_id' => $serviceParentIds['Personal Care Services'],
                'is_active' => true,
                'type' => 'service',
                'icon' => 'fas fa-cut',
            ],
            [
                'name' => 'Styling',
                'description' => 'Hair styling and design services',
                'image' => $this->getServiceCategoryImage('Salon Services', 'Styling'),
                'parent_id' => $serviceParentIds['Personal Care Services'],
                'is_active' => true,
                'type' => 'service',
                'icon' => 'fas fa-magic',
            ],
            [
                'name' => 'Coloring',
                'description' => 'Hair coloring and highlighting services',
                'image' => $this->getServiceCategoryImage('Salon Services', 'Coloring'),
                'parent_id' => $serviceParentIds['Personal Care Services'],
                'is_active' => true,
                'type' => 'service',
                'icon' => 'fas fa-palette',
            ],
            [
                'name' => 'Makeup Services',
                'description' => 'Professional makeup application services',
                'image' => $this->getServiceCategoryImage('Makeup Services', 'Makeup Services'),
                'parent_id' => $serviceParentIds['Personal Care Services'],
                'is_active' => true,
                'type' => 'service',
                'icon' => 'fas fa-paint-brush',
            ],
            [
                'name' => 'Bridal makeup',
                'description' => 'Bridal makeup and styling services',
                'image' => $this->getServiceCategoryImage('Makeup Services', 'Bridal makeup'),
                'parent_id' => $serviceParentIds['Personal Care Services'],
                'is_active' => true,
                'type' => 'service',
                'icon' => 'fas fa-ring',
            ],
            [
                'name' => 'Event makeup',
                'description' => 'Special event makeup services',
                'image' => $this->getServiceCategoryImage('Makeup Services', 'Event makeup'),
                'parent_id' => $serviceParentIds['Personal Care Services'],
                'is_active' => true,
                'type' => 'service',
                'icon' => 'fas fa-star',
            ],
            [
                'name' => 'Nail Care',
                'description' => 'Professional nail care services',
                'image' => $this->getServiceCategoryImage('Nail Care', 'Nail Care'),
                'parent_id' => $serviceParentIds['Personal Care Services'],
                'is_active' => true,
                'type' => 'service',
                'icon' => 'fas fa-hand-sparkles',
            ],
            [
                'name' => 'Manicures',
                'description' => 'Professional manicure services',
                'image' => $this->getServiceCategoryImage('Nail Care', 'Manicures'),
                'parent_id' => $serviceParentIds['Personal Care Services'],
                'is_active' => true,
                'type' => 'service',
                'icon' => 'fas fa-hand-paper',
            ],
            [
                'name' => 'Pedicures',
                'description' => 'Professional pedicure services',
                'image' => $this->getServiceCategoryImage('Nail Care', 'Pedicures'),
                'parent_id' => $serviceParentIds['Personal Care Services'],
                'is_active' => true,
                'type' => 'service',
                'icon' => 'fas fa-shoe-prints',
            ],
            [
                'name' => 'Nail art',
                'description' => 'Creative nail art and design services',
                'image' => $this->getServiceCategoryImage('Nail Care', 'Nail art'),
                'parent_id' => $serviceParentIds['Personal Care Services'],
                'is_active' => true,
                'type' => 'service',
                'icon' => 'fas fa-paint-brush',
            ],

            // Therapy & Support subcategories
            [
                'name' => 'Individual Therapy',
                'description' => 'Individual therapy and counseling sessions',
                'image' => $this->getServiceCategoryImage('Therapy Sessions', 'Individual Therapy'),
                'parent_id' => $serviceParentIds['Therapy & Support'],
                'is_active' => true,
                'type' => 'service',
                'icon' => 'fas fa-user',
            ],
            [
                'name' => 'Couple Therapy',
                'description' => 'Couples therapy and relationship counseling',
                'image' => $this->getServiceCategoryImage('Therapy Sessions', 'Couple Therapy'),
                'parent_id' => $serviceParentIds['Therapy & Support'],
                'is_active' => true,
                'type' => 'service',
                'icon' => 'fas fa-heart',
            ],
            [
                'name' => 'Family therapy',
                'description' => 'Family therapy and counseling services',
                'image' => $this->getServiceCategoryImage('Therapy Sessions', 'Family therapy'),
                'parent_id' => $serviceParentIds['Therapy & Support'],
                'is_active' => true,
                'type' => 'service',
                'icon' => 'fas fa-users',
            ],
            [
                'name' => 'Nutrition Counseling',
                'description' => 'Nutrition counseling and dietary guidance',
                'image' => $this->getServiceCategoryImage('Nutrition Counseling', 'Nutrition Counseling'),
                'parent_id' => $serviceParentIds['Therapy & Support'],
                'is_active' => true,
                'type' => 'service',
                'icon' => 'fas fa-apple-alt',
            ],
            [
                'name' => 'Diet plans',
                'description' => 'Personalized diet planning services',
                'image' => $this->getServiceCategoryImage('Nutrition Counseling', 'Diet plans'),
                'parent_id' => $serviceParentIds['Therapy & Support'],
                'is_active' => true,
                'type' => 'service',
                'icon' => 'fas fa-clipboard-list',
            ],
            [
                'name' => 'Weight management programs',
                'description' => 'Weight management and fitness programs',
                'image' => $this->getServiceCategoryImage('Nutrition Counseling', 'Weight management programs'),
                'parent_id' => $serviceParentIds['Therapy & Support'],
                'is_active' => true,
                'type' => 'service',
                'icon' => 'fas fa-weight',
            ],

            // Creative & Artistic subcategories
            [
                'name' => 'Photography sessions',
                'description' => 'Professional photography sessions',
                'image' => $this->getServiceCategoryImage('Artistic Services', 'Photography sessions'),
                'parent_id' => $serviceParentIds['Creative & Artistic'],
                'is_active' => true,
                'type' => 'service',
                'icon' => 'fas fa-camera',
            ],
            [
                'name' => 'Painting classes',
                'description' => 'Art and painting classes',
                'image' => $this->getServiceCategoryImage('Artistic Services', 'Painting classes'),
                'parent_id' => $serviceParentIds['Creative & Artistic'],
                'is_active' => true,
                'type' => 'service',
                'icon' => 'fas fa-paint-brush',
            ],
            [
                'name' => 'Pottery making',
                'description' => 'Pottery and ceramics classes',
                'image' => $this->getServiceCategoryImage('Artistic Services', 'Pottery making'),
                'parent_id' => $serviceParentIds['Creative & Artistic'],
                'is_active' => true,
                'type' => 'service',
                'icon' => 'fas fa-hands',
            ],
            [
                'name' => 'Craft workshops',
                'description' => 'Creative craft workshops and classes',
                'image' => $this->getServiceCategoryImage('Artistic Services', 'Craft workshops'),
                'parent_id' => $serviceParentIds['Creative & Artistic'],
                'is_active' => true,
                'type' => 'service',
                'icon' => 'fas fa-cut',
            ],

            // Care Services subcategories
            [
                'name' => 'In-home care',
                'description' => 'In-home elderly care services',
                'image' => $this->getServiceCategoryImage('Elderly Care & Companionship Services', 'In-home care'),
                'parent_id' => $serviceParentIds['Care Services'],
                'is_active' => true,
                'type' => 'service',
                'icon' => 'fas fa-home',
            ],
            [
                'name' => 'Companionship visits',
                'description' => 'Companionship and social visits',
                'image' => $this->getServiceCategoryImage('Elderly Care & Companionship Services', 'Companionship visits'),
                'parent_id' => $serviceParentIds['Care Services'],
                'is_active' => true,
                'type' => 'service',
                'icon' => 'fas fa-handshake',
            ],
        ];

        // Create service subcategories
        foreach ($serviceSubcategories as $category) {
            Category::create($category);
        }
    }

    /**
     * Get service category image path from app service category images directory
     */
    private function getServiceCategoryImage(string $categoryName, ?string $subcategoryName = null): string
    {
        $imagePath = base_path("app service category images/{$categoryName}");

        if (File::exists($imagePath)) {
            $imageFiles = File::files($imagePath);
            if (!empty($imageFiles)) {
                // If subcategory is specified, look for specific image
                if ($subcategoryName) {
                    $specificImage = collect($imageFiles)->first(function ($file) use ($subcategoryName) {
                        return Str::contains($file->getFilename(), $subcategoryName);
                    });

                    if ($specificImage) {
                        return "app service category images/{$categoryName}/{$specificImage->getFilename()}";
                    }
                }

                // Look for main category image
                $mainImage = collect($imageFiles)->first(function ($file) use ($categoryName) {
                    return Str::contains($file->getFilename(), $categoryName);
                });

                if ($mainImage) {
                    return "app service category images/{$categoryName}/{$mainImage->getFilename()}";
                }

                // Fallback to first image in directory
                return "app service category images/{$categoryName}/{$imageFiles[0]->getFilename()}";
            }
        }

        // Fallback to placeholder
        return '/images/categories/placeholder.jpg';
    }
}
