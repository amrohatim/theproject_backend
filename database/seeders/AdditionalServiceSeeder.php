<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Category;
use App\Models\Branch;
use App\Helpers\UnsplashImageHelper;

class AdditionalServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all service subcategories (only child categories with parent_id)
        $subcategories = Category::where('type', 'service')
            ->whereNotNull('parent_id')
            ->get();

        // Get all branches
        $branches = Branch::all();

        if ($branches->isEmpty()) {
            $this->command->error('No branches found. Please run BranchesTableSeeder first.');
            return;
        }

        $this->command->info("Found {$subcategories->count()} service subcategories to populate.");
        $this->command->info("Found {$branches->count()} branches available.");

        // Define comprehensive service data for each subcategory
        $servicesByCategory = $this->getServicesByCategory();

        $totalServicesCreated = 0;

        foreach ($subcategories as $subcategory) {
            $categoryName = $subcategory->name;
            
            if (!isset($servicesByCategory[$categoryName])) {
                $this->command->warn("No services defined for subcategory: {$categoryName}");
                continue;
            }

            $services = $servicesByCategory[$categoryName];
            $this->command->info("Creating services for: {$categoryName}");

            foreach ($services as $serviceData) {
                // Randomly assign to a branch
                $branch = $branches->random();
                
                Service::create([
                    'branch_id' => $branch->id,
                    'category_id' => $subcategory->id,
                    'name' => $serviceData['name'],
                    'price' => $serviceData['price'],
                    'duration' => $serviceData['duration'],
                    'description' => $serviceData['description'],
                    'image' => $serviceData['image'],
                    'rating' => $serviceData['rating'],
                    'is_available' => true,
                    'featured' => $serviceData['featured'] ?? false,
                    'home_service' => $serviceData['home_service'] ?? false,
                ]);
                
                $totalServicesCreated++;
            }
        }

        $this->command->info("Additional service seeding completed successfully! Created {$totalServicesCreated} services.");
    }

    /**
     * Get comprehensive service data organized by category name
     */
    private function getServicesByCategory(): array
    {
        return [
            // Pilates services
            'Pilates' => [
                [
                    'name' => 'Mat Pilates Class',
                    'price' => 28.00,
                    'duration' => 60,
                    'description' => 'Experience the core-strengthening benefits of traditional mat Pilates in this comprehensive class suitable for all fitness levels. Focus on building core strength, improving posture, and developing body awareness through controlled movements and precise breathing techniques. Each class includes a warm-up sequence, fundamental Pilates exercises, and a relaxing cool-down period. Learn proper alignment and breathing patterns while working on flexibility, balance, and coordination. Our certified instructor provides modifications for different fitness levels and physical limitations. The class emphasizes quality of movement over quantity, helping you develop a strong foundation in Pilates principles. You\'ll work on exercises that target deep stabilizing muscles, improve spinal mobility, and enhance overall body strength. Perfect for beginners new to Pilates or those looking to refine their technique in a supportive group environment.',
                    'image' => UnsplashImageHelper::getRandomImageUrl('mat pilates class core strength', 800, 600),
                    'rating' => 4.6,
                    'featured' => false,
                    'home_service' => true,
                ],
                [
                    'name' => 'Reformer Pilates Session',
                    'price' => 45.00,
                    'duration' => 50,
                    'description' => 'Challenge your body with our dynamic Reformer Pilates sessions that utilize specialized equipment to provide resistance and support for a full-body workout. The reformer\'s spring system creates variable resistance that helps build strength, improve flexibility, and enhance coordination while maintaining proper alignment. Each session includes exercises targeting all major muscle groups with emphasis on core stability and postural awareness. Learn to work with the reformer\'s moving carriage and pulley system to perform exercises that would be difficult on a mat alone. Our experienced instructor provides personalized attention and adjustments to ensure proper form and maximize benefits. The reformer allows for both challenging strength-building exercises and gentle rehabilitation movements. Perfect for those looking to advance their Pilates practice or anyone seeking a low-impact, high-intensity workout that improves strength, flexibility, and body awareness.',
                    'image' => UnsplashImageHelper::getRandomImageUrl('reformer pilates equipment workout', 800, 600),
                    'rating' => 4.8,
                    'featured' => true,
                    'home_service' => false,
                ],
                [
                    'name' => 'Pilates for Beginners',
                    'price' => 25.00,
                    'duration' => 75,
                    'description' => 'Start your Pilates journey with confidence in this comprehensive beginner-friendly class designed to introduce fundamental principles and movements. Learn the basic concepts of Pilates including proper breathing, core engagement, and spinal alignment while building a foundation of essential exercises. The class covers fundamental movements like the hundred, roll-ups, and single-leg stretches with detailed instruction on proper form and technique. Understand how to modify exercises for your current fitness level and any physical limitations. Our patient instructor provides individual attention and ensures everyone feels comfortable and supported. The slower pace allows time to understand each movement and develop muscle memory for correct execution. You\'ll learn home practice sequences and receive guidance on progressing your Pilates practice. Perfect for complete beginners, those returning to exercise after injury, or anyone wanting to establish a strong foundation in Pilates principles.',
                    'image' => UnsplashImageHelper::getRandomImageUrl('pilates beginners class instruction fundamentals', 800, 600),
                    'rating' => 4.7,
                    'featured' => false,
                    'home_service' => false,
                ],
                [
                    'name' => 'Advanced Pilates Workshop',
                    'price' => 55.00,
                    'duration' => 90,
                    'description' => 'Challenge yourself with advanced Pilates movements and sequences in this intensive workshop designed for experienced practitioners. Explore complex exercises including advanced teaser variations, control balance, and challenging transitions that require significant core strength and coordination. Learn to perform flowing sequences that combine multiple exercises while maintaining precise control and proper alignment. The workshop covers advanced breathing techniques, deeper core engagement strategies, and how to progress challenging movements safely. Work on exercises that require advanced flexibility, strength, and body awareness while understanding the biomechanics behind each movement. Our expert instructor provides detailed breakdowns of complex exercises and offers progressions for continued advancement. You\'ll explore variations and modifications that keep advanced practitioners challenged and engaged. Perfect for dedicated Pilates students looking to deepen their practice and experienced fitness enthusiasts seeking new challenges.',
                    'image' => UnsplashImageHelper::getRandomImageUrl('advanced pilates workshop challenging movements', 800, 600),
                    'rating' => 4.9,
                    'featured' => true,
                    'home_service' => false,
                ],
                [
                    'name' => 'Pilates Rehabilitation Session',
                    'price' => 65.00,
                    'duration' => 60,
                    'description' => 'Focus on healing and recovery with therapeutic Pilates sessions designed for injury rehabilitation and chronic pain management. Work with our certified instructor who has specialized training in rehabilitation Pilates to address specific physical limitations and movement dysfunctions. Each session is individually tailored to your needs, whether recovering from injury, managing chronic conditions, or addressing postural imbalances. Learn gentle, controlled movements that promote healing while building strength and stability around injured or vulnerable areas. The approach emphasizes proper alignment, breathing, and gradual progression to restore function and prevent re-injury. Sessions include assessment of movement patterns, identification of compensations, and development of corrective exercise strategies. You\'ll receive a personalized home program to support your recovery between sessions. Perfect for those recovering from back injuries, joint problems, or anyone dealing with chronic pain who wants to return to active living safely.',
                    'image' => UnsplashImageHelper::getRandomImageUrl('pilates rehabilitation therapy recovery', 800, 600),
                    'rating' => 4.8,
                    'featured' => false,
                    'home_service' => true,
                ],
            ],

            // Strength training services
            'Strength training' => [
                [
                    'name' => 'Personal Strength Training',
                    'price' => 75.00,
                    'duration' => 60,
                    'description' => 'Achieve your strength and fitness goals with personalized one-on-one training sessions designed specifically for your needs and objectives. Work with our certified personal trainer to develop a customized strength training program that addresses your current fitness level, goals, and any physical limitations. Each session includes proper warm-up, targeted strength exercises using free weights and machines, and cool-down stretching. Learn correct form and technique for compound movements like squats, deadlifts, and presses while understanding the science behind effective strength training. Your trainer will provide motivation, ensure safety, and adjust workouts based on your progress and feedback. Sessions include nutritional guidance and lifestyle recommendations to support your strength-building goals. You\'ll receive workout logs and home exercise recommendations to maintain progress between sessions. Perfect for beginners new to strength training or experienced lifters looking to break through plateaus and achieve new personal records.',
                    'image' => UnsplashImageHelper::getRandomImageUrl('personal strength training weights gym', 800, 600),
                    'rating' => 4.8,
                    'featured' => true,
                    'home_service' => false,
                ],
                [
                    'name' => 'Functional Strength Class',
                    'price' => 35.00,
                    'duration' => 45,
                    'description' => 'Build real-world strength with functional training that improves your ability to perform daily activities with ease and confidence. This dynamic class focuses on movement patterns that translate directly to everyday life, including lifting, carrying, pushing, and pulling motions. Use a variety of equipment including kettlebells, resistance bands, medicine balls, and bodyweight exercises to develop strength, power, and coordination. Each class includes exercises that challenge multiple muscle groups simultaneously while improving balance, stability, and core strength. Learn proper movement mechanics that reduce injury risk and enhance performance in sports and daily activities. The varied workout format keeps sessions engaging while progressively building strength and endurance. Our experienced instructor provides modifications for different fitness levels and ensures proper form throughout the workout. Perfect for anyone looking to build practical strength that enhances quality of life and athletic performance.',
                    'image' => UnsplashImageHelper::getRandomImageUrl('functional strength training kettlebells movement', 800, 600),
                    'rating' => 4.7,
                    'featured' => false,
                    'home_service' => false,
                ],
                [
                    'name' => 'Powerlifting Fundamentals',
                    'price' => 85.00,
                    'duration' => 90,
                    'description' => 'Master the three powerlifting movements - squat, bench press, and deadlift - in this comprehensive fundamentals course designed for serious strength athletes. Learn proper technique, safety protocols, and programming principles for each lift while understanding the biomechanics that maximize performance and minimize injury risk. Each session includes detailed instruction on setup, execution, and common technical errors for all three lifts. Work on mobility and accessory exercises that support powerlifting performance while developing the mental focus required for heavy lifting. Our certified powerlifting coach provides individualized feedback and helps you establish realistic strength goals and training progressions. Learn about periodization, competition preparation, and how to structure effective powerlifting programs. You\'ll receive detailed technique notes and training recommendations to continue your powerlifting journey. Perfect for beginners interested in powerlifting or intermediate lifters looking to refine technique and increase their total.',
                    'image' => UnsplashImageHelper::getRandomImageUrl('powerlifting squat deadlift bench press', 800, 600),
                    'rating' => 4.9,
                    'featured' => true,
                    'home_service' => false,
                ],
                [
                    'name' => 'Bodyweight Strength Training',
                    'price' => 30.00,
                    'duration' => 50,
                    'description' => 'Build impressive strength using only your bodyweight in this challenging and accessible training class that requires no equipment. Learn progressive calisthenics movements including push-up variations, pull-ups, squats, and core exercises that can be performed anywhere. Each class focuses on proper form, progressive overload principles, and how to modify exercises for different strength levels. Work on fundamental movement patterns while building the strength foundation needed for advanced bodyweight skills like handstands and muscle-ups. The class includes flexibility and mobility work to support strength development and prevent injury. Our instructor demonstrates proper progressions and regressions for each exercise, ensuring everyone can participate regardless of current fitness level. Learn to create effective bodyweight workouts you can do at home, while traveling, or anywhere you have limited space. Perfect for beginners to fitness, those who prefer equipment-free workouts, or anyone looking to master their own bodyweight.',
                    'image' => UnsplashImageHelper::getRandomImageUrl('bodyweight strength training calisthenics', 800, 600),
                    'rating' => 4.6,
                    'featured' => false,
                    'home_service' => true,
                ],
                [
                    'name' => 'Olympic Weightlifting Introduction',
                    'price' => 95.00,
                    'duration' => 120,
                    'description' => 'Learn the technical and explosive Olympic lifts - snatch and clean & jerk - in this comprehensive introduction designed for athletes and fitness enthusiasts seeking to develop power and athleticism. Master the complex movement patterns that require coordination, flexibility, timing, and strength while understanding the progression from basic positions to full lifts. Each session includes detailed instruction on proper setup, pulling mechanics, receiving positions, and overhead stability for both lifts. Work on mobility exercises and accessory movements that support Olympic lifting performance while developing the explosive power that transfers to sports and general fitness. Our certified Olympic lifting coach provides individualized feedback and helps you establish a foundation for continued learning. Learn about programming, competition basics, and how Olympic lifting can enhance athletic performance. You\'ll receive technique videos and training recommendations to support your Olympic lifting journey. Perfect for athletes looking to improve power output or fitness enthusiasts seeking new challenges.',
                    'image' => UnsplashImageHelper::getRandomImageUrl('olympic weightlifting snatch clean jerk', 800, 600),
                    'rating' => 4.8,
                    'featured' => false,
                    'home_service' => false,
                ],
            ],
        ];
    }
}
