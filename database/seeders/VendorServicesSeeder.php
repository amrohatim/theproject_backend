<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Service;
use App\Models\Category;
use App\Models\Branch;

class VendorServicesSeeder extends Seeder
{
    private $vendor;
    private $company;
    private $branches;
    private $categories;
    private $serviceNames;
    private $descriptions;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting vendor services seeding...');
        
        // Initialize data
        $this->initializeVendorData();
        $this->initializeServiceData();
        
        // Seed services in batches
        $this->seedServices();
        
        $this->command->info('✅ Vendor services seeding completed successfully!');
    }

    /**
     * Initialize vendor data
     */
    private function initializeVendorData(): void
    {
        // Find the vendor user
        $this->vendor = User::where('email', 'gogoh3296@gmail.com')->first();
        
        if (!$this->vendor) {
            $this->command->error('❌ Vendor user with email gogoh3296@gmail.com not found!');
            $this->command->info('Please ensure the vendor user exists before running this seeder.');
            return;
        }

        $this->command->info("✅ Found vendor: {$this->vendor->name} (ID: {$this->vendor->id})");

        // Get vendor's company
        $this->company = $this->vendor->company;
        if (!$this->company) {
            $this->command->error('❌ Vendor has no company associated!');
            return;
        }

        $this->command->info("✅ Found company: {$this->company->name} (ID: {$this->company->id})");

        // Get company branches
        $this->branches = $this->company->branches()->whereNotIn('id' , [1,6,7])->get();
        if ($this->branches->isEmpty()) {
            $this->command->error('❌ No branches found for the vendor company!');
            return;
        }

        $this->command->info("✅ Found {$this->branches->count()} branch(es)");

        // Get service categories (only child categories - those with parent_id)
        $this->categories = Category::where('type', 'service')
            ->where('is_active', true)
            ->whereNotNull('parent_id') // Only child categories
            ->get();

        if ($this->categories->isEmpty()) {
            $this->command->error('❌ No service categories found!');
            return;
        }

        $this->command->info("✅ Found {$this->categories->count()} service categories");
    }

    /**
     * Initialize service data arrays
     */
    private function initializeServiceData(): void
    {
        $this->serviceNames = [
            'Hair Styling', 'Hair Cutting', 'Hair Coloring', 'Hair Treatment', 'Beard Trimming',
            'Facial Treatment', 'Deep Cleansing Facial', 'Anti-Aging Facial', 'Acne Treatment', 'Skin Consultation',
            'Manicure', 'Pedicure', 'Nail Art', 'Gel Polish', 'Nail Extension',
            'Full Body Massage', 'Swedish Massage', 'Deep Tissue Massage', 'Hot Stone Massage', 'Aromatherapy Massage',
            'Eyebrow Threading', 'Eyebrow Tinting', 'Eyelash Extension', 'Eyelash Tinting', 'Makeup Application',
            'Bridal Makeup', 'Party Makeup', 'Professional Makeup', 'Makeup Consultation', 'Makeup Lesson',
            'Personal Training', 'Group Fitness', 'Yoga Session', 'Pilates Class', 'Cardio Training',
            'Weight Training', 'Nutrition Consultation', 'Fitness Assessment', 'Stretching Session', 'Dance Class',
            'House Cleaning', 'Deep Cleaning', 'Office Cleaning', 'Carpet Cleaning', 'Window Cleaning',
            'Laundry Service', 'Ironing Service', 'Dry Cleaning', 'Upholstery Cleaning', 'Kitchen Cleaning',
            'Plumbing Repair', 'Electrical Work', 'AC Maintenance', 'Appliance Repair', 'Painting Service',
            'Carpentry Work', 'Tile Installation', 'Furniture Assembly', 'Home Inspection', 'Pest Control',
            'Car Wash', 'Car Detailing', 'Oil Change', 'Tire Service', 'Car Inspection',
            'Engine Repair', 'Brake Service', 'Battery Replacement', 'AC Repair', 'Car Polish',
            'Photography Session', 'Event Photography', 'Portrait Photography', 'Product Photography', 'Wedding Photography',
            'Video Production', 'Photo Editing', 'Drone Photography', 'Studio Rental', 'Photography Lesson',
            'Web Design', 'Mobile App Development', 'SEO Service', 'Social Media Management', 'Content Writing',
            'Graphic Design', 'Logo Design', 'Digital Marketing', 'Website Maintenance', 'IT Consultation',
            'Language Tutoring', 'Math Tutoring', 'Science Tutoring', 'Music Lesson', 'Art Class',
            'Cooking Class', 'Dance Lesson', 'Swimming Lesson', 'Driving Lesson', 'Computer Training',
            'Legal Consultation', 'Tax Preparation', 'Business Consultation', 'Financial Planning', 'Insurance Consultation',
            'Real Estate Consultation', 'Investment Advice', 'Accounting Service', 'Bookkeeping', 'Audit Service',
            'Pet Grooming', 'Pet Training', 'Pet Sitting', 'Dog Walking', 'Veterinary Consultation',
            'Pet Boarding', 'Pet Photography', 'Pet Taxi', 'Pet Food Delivery', 'Pet Health Check',
            'Garden Design', 'Lawn Maintenance', 'Tree Trimming', 'Plant Care', 'Irrigation Installation',
            'Landscape Design', 'Garden Consultation', 'Pest Control for Plants', 'Fertilizer Application', 'Seasonal Cleanup'
        ];

        $this->descriptions = [
            'Professional service delivered by experienced specialists with attention to detail.',
            'High-quality service using premium products and modern techniques.',
            'Personalized service tailored to meet your specific needs and preferences.',
            'Expert service with guaranteed satisfaction and professional results.',
            'Comprehensive service package including consultation and follow-up care.',
            'Premium service experience with luxury amenities and comfort.',
            'Efficient and reliable service with flexible scheduling options.',
            'Specialized service using industry-leading equipment and methods.',
            'Complete service solution with transparent pricing and no hidden fees.',
            'Professional service with certified technicians and quality assurance.',
        ];
    }

    /**
     * Seed services in batches
     */
    private function seedServices(): void
    {
        $totalServices = 2000;
        $batchSize = 100;
        $batches = ceil($totalServices / $batchSize);

        $this->command->info("Creating {$totalServices} services in {$batches} batches of {$batchSize}...");

        for ($batch = 0; $batch < $batches; $batch++) {
            $startIndex = $batch * $batchSize;
            $endIndex = min($startIndex + $batchSize, $totalServices);

            $this->command->info("Processing batch " . ($batch + 1) . "/{$batches} (Services {$startIndex}-{$endIndex})...");

            DB::beginTransaction();
            try {
                for ($i = $startIndex; $i < $endIndex; $i++) {
                    $this->createService($i + 1);
                }
                DB::commit();
                $this->command->info("✅ Batch " . ($batch + 1) . " completed successfully!");
            } catch (\Exception $e) {
                DB::rollBack();
                $this->command->error("❌ Error in batch " . ($batch + 1) . ": " . $e->getMessage());
                Log::error('VendorServicesSeeder batch error', [
                    'batch' => $batch + 1,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                break;
            }
        }
    }

    /**
     * Create a single service
     */
    private function createService(int $serviceNumber): void
    {
        // Generate service data
        $serviceName = $this->generateServiceName($serviceNumber);
        $serviceNameArabic = $this->generateArabicName($serviceName);
        $description = $this->descriptions[array_rand($this->descriptions)];
        $descriptionArabic = $this->generateArabicDescription($description);
        $price = $this->generatePrice();
        $duration = $this->generateDuration();

        // Select random branch and category
        $branch = $this->branches->random();
        $category = $this->categories->random();

        // Create the service
        Service::create([
            'branch_id' => $branch->id,
            'category_id' => $category->id,
            'name' => $serviceName,
            'service_name_arabic' => $serviceNameArabic,
            'description' => $description,
            'service_description_arabic' => $descriptionArabic,
            'price' => $price,
            'duration' => $duration,
            'image' => $this->generateRandomImage(),
            'is_available' => true,
            'home_service' => rand(1, 4) === 1, // 25% chance of being home service
            'featured' => rand(1, 10) === 1, // 10% chance of being featured
            'rating' => round(rand(35, 50) / 10, 1), // Rating between 3.5 and 5.0
        ]);
    }

    /**
     * Generate a unique service name
     */
    private function generateServiceName(int $serviceNumber): string
    {
        $baseName = $this->serviceNames[array_rand($this->serviceNames)];
        $adjectives = ['Premium', 'Professional', 'Expert', 'Luxury', 'Advanced', 'Complete', 'Specialized', 'Quality', 'Express', 'Deluxe'];

        // 70% chance to add adjective, 30% chance to use base name only
        if (rand(1, 10) <= 7) {
            $adjective = $adjectives[array_rand($adjectives)];
            return "{$adjective} {$baseName} #{$serviceNumber}";
        }

        return "{$baseName} #{$serviceNumber}";
    }

    /**
     * Generate Arabic name (simplified)
     */
    private function generateArabicName(string $englishName): string
    {
        $arabicNames = [
            'خدمة فاخرة', 'خدمة احترافية', 'خدمة متخصصة', 'خدمة عالية الجودة', 'خدمة متطورة',
            'خدمة شاملة', 'خدمة سريعة', 'خدمة مميزة', 'خدمة راقية', 'خدمة متكاملة',
            'خدمة حديثة', 'خدمة موثوقة', 'خدمة مبتكرة', 'خدمة عملية', 'خدمة مريحة'
        ];

        return $arabicNames[array_rand($arabicNames)] . ' ' . rand(1, 2000);
    }

    /**
     * Generate Arabic description (simplified)
     */
    private function generateArabicDescription(string $englishDescription): string
    {
        $arabicDescriptions = [
            'خدمة احترافية يقدمها متخصصون ذوو خبرة مع الاهتمام بالتفاصيل.',
            'خدمة عالية الجودة باستخدام منتجات فاخرة وتقنيات حديثة.',
            'خدمة شخصية مصممة لتلبية احتياجاتك وتفضيلاتك المحددة.',
            'خدمة متخصصة مع ضمان الرضا والنتائج المهنية.',
            'باقة خدمة شاملة تشمل الاستشارة والمتابعة.',
            'تجربة خدمة فاخرة مع وسائل الراحة والرفاهية.',
            'خدمة فعالة وموثوقة مع خيارات جدولة مرنة.',
            'خدمة متخصصة باستخدام معدات وطرق رائدة في الصناعة.',
            'حل خدمة متكامل بأسعار شفافة وبدون رسوم خفية.',
            'خدمة احترافية مع فنيين معتمدين وضمان الجودة.',
        ];

        return $arabicDescriptions[array_rand($arabicDescriptions)];
    }

    /**
     * Generate a realistic price for services
     */
    private function generatePrice(): float
    {
        $priceRanges = [
            [25, 75],    // Basic services
            [75, 150],   // Standard services
            [150, 300],  // Premium services
            [300, 600],  // Luxury services
        ];

        $range = $priceRanges[array_rand($priceRanges)];
        return round(rand($range[0] * 100, $range[1] * 100) / 100, 2);
    }

    /**
     * Generate realistic service duration in minutes
     */
    private function generateDuration(): int
    {
        $durations = [
            15, 30, 45, 60,     // Short services
            90, 120,            // Medium services
            150, 180, 240,      // Long services
            300, 360            // Extended services
        ];

        return $durations[array_rand($durations)];
    }

    /**
     * Generate random image URL from picsum.photos
     */
    private function generateRandomImage(): string
    {
        $width = 800;
        $height = 600;
        $imageId = rand(1, 1000); // Random image ID for variety

        return "https://picsum.photos/{$width}/{$height}?random={$imageId}";
    }
}
