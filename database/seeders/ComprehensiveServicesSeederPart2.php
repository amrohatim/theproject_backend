<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Category;
use App\Models\Branch;

class ComprehensiveServicesSeederPart2 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting Comprehensive Services Seeder Part 2...');

        // Get all branches
        $branches = Branch::where('status', 'active')->get();
        if ($branches->isEmpty()) {
            $this->command->error('No active branches found. Please run BranchesSeeder first.');
            return;
        }

        // Get all service subcategories (child categories with type 'service')
        $subcategories = Category::where('type', 'service')
            ->whereNotNull('parent_id')
            ->get();

        if ($subcategories->isEmpty()) {
            $this->command->error('No service subcategories found. Please run ServiceCategoriesSeeder first.');
            return;
        }

        $totalServicesCreated = 0;

        // Define services for each subcategory
        $servicesByCategory = $this->getServicesData();

        foreach ($subcategories as $subcategory) {
            $categoryName = $subcategory->name;
            
            if (!isset($servicesByCategory[$categoryName])) {
                continue; // Skip categories not in this part
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

        $this->command->info("Service seeding Part 2 completed successfully!");
        $this->command->info("Total services created in Part 2: {$totalServicesCreated}");
    }

    /**
     * Get comprehensive services data for fitness and wellness categories
     */
    private function getServicesData(): array
    {
        return [
            // Fitness Classes - Pilates
            'Pilates' => [
                [
                    'name' => 'Beginner Mat Pilates',
                    'description' => 'Introduction to Pilates fundamentals focusing on core strength, flexibility, and body awareness using mat-based exercises. This beginner-friendly class teaches proper breathing techniques, basic Pilates principles, and foundational movements that form the basis of all Pilates practice. You\'ll learn to engage your powerhouse (core muscles), improve posture, and develop mind-body connection through controlled, precise movements. Our certified instructor provides modifications for all fitness levels and physical limitations, ensuring everyone can participate safely and effectively. The class emphasizes quality over quantity, teaching you to move with intention and control. Perfect for those new to Pilates, recovering from injury, or anyone wanting to build a strong foundation in this transformative exercise method. Regular practice improves flexibility, strength, balance, and overall body awareness.',
                    'price' => 25.00,
                    'duration' => 60,
                    'image' => 'https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.7,
                    'featured' => true,
                ],
                [
                    'name' => 'Reformer Pilates Intermediate',
                    'description' => 'Challenge your Pilates practice with this intermediate reformer class that utilizes spring resistance and moving carriage to deepen your workout and enhance muscle engagement. The reformer provides both assistance and resistance, allowing for more precise muscle targeting and increased exercise variety. You\'ll work on advanced movement patterns, complex sequences, and challenging positions that build significant strength and flexibility. This class requires previous Pilates experience and good understanding of basic principles. Our expert instructor guides you through flowing sequences that challenge stability, coordination, and strength while maintaining the precision and control that defines Pilates. Perfect for those ready to advance their practice and experience the unique benefits of reformer training. The reformer\'s versatility allows for modifications and progressions to suit individual needs and goals.',
                    'price' => 45.00,
                    'duration' => 55,
                    'image' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.8,
                    'featured' => false,
                ],
                [
                    'name' => 'Pilates for Seniors',
                    'description' => 'Gentle, age-appropriate Pilates class designed specifically for older adults focusing on maintaining mobility, balance, and strength while accommodating physical limitations and health considerations. This specialized class emphasizes fall prevention, joint mobility, and functional movement patterns that support daily activities. Exercises are performed seated, standing, or lying down with props and modifications to ensure safety and accessibility. Our senior-certified instructor understands the unique needs of older adults and creates a supportive, non-intimidating environment. The class improves bone density, reduces arthritis pain, enhances balance and coordination, and promotes better posture. Perfect for seniors wanting to stay active, those with chronic conditions like osteoporosis or arthritis, or anyone seeking a gentle yet effective exercise program. Regular participation helps maintain independence and quality of life through improved physical function.',
                    'price' => 20.00,
                    'duration' => 45,
                    'image' => 'https://images.unsplash.com/photo-1506629905607-c28b47e8b6b1?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.9,
                    'featured' => false,
                ],
                [
                    'name' => 'Prenatal Pilates',
                    'description' => 'Safe, effective Pilates practice specifically designed for pregnant women to maintain fitness, prepare for childbirth, and support the changing body throughout pregnancy. This specialized class focuses on strengthening the pelvic floor, maintaining core stability, and improving posture to alleviate common pregnancy discomforts. Exercises are carefully selected and modified for each trimester, avoiding positions and movements that could be harmful during pregnancy. Our prenatal-certified instructor provides guidance on breathing techniques, relaxation methods, and exercises that can help during labor and delivery. The class also addresses common pregnancy issues like back pain, swelling, and fatigue through targeted movements and stretches. Perfect for expectant mothers at any stage of pregnancy who want to stay active safely. Regular practice can lead to easier labor, faster recovery, and better overall pregnancy experience.',
                    'price' => 35.00,
                    'duration' => 50,
                    'image' => 'https://images.unsplash.com/photo-1506629905607-c28b47e8b6b1?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.8,
                    'featured' => false,
                ],
                [
                    'name' => 'Pilates Rehabilitation and Injury Recovery',
                    'description' => 'Therapeutic Pilates class designed for individuals recovering from injury or dealing with chronic pain conditions, focusing on safe movement patterns and gradual strength building. This specialized class works closely with physical therapy principles to support healing and prevent re-injury. Our instructor, trained in rehabilitation techniques, assesses individual needs and provides personalized modifications and progressions. The class emphasizes proper alignment, gentle strengthening, and mobility restoration while respecting healing timelines and medical restrictions. Common conditions addressed include back pain, neck issues, joint problems, and post-surgical recovery. Perfect for those cleared by their healthcare provider to begin gentle exercise or anyone dealing with chronic pain who needs a careful, knowledgeable approach to movement. The therapeutic benefits of Pilates can significantly support recovery and long-term pain management when practiced correctly.',
                    'price' => 40.00,
                    'duration' => 60,
                    'image' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.9,
                    'featured' => false,
                ],
            ],

            // Fitness Classes - Strength training
            'Strength training' => [
                [
                    'name' => 'Beginner Strength Training Fundamentals',
                    'description' => 'Comprehensive introduction to strength training covering proper form, safety protocols, and fundamental movement patterns essential for building a strong foundation in resistance exercise. This beginner-friendly class teaches the basics of weightlifting including squat, deadlift, bench press, and overhead press techniques using barbells, dumbbells, and bodyweight exercises. You\'ll learn about progressive overload, workout structure, and how to design effective training programs. Our certified trainer provides individual attention to ensure proper form and prevent injury while building confidence in the weight room. The class covers equipment familiarization, warm-up and cool-down protocols, and basic nutrition principles for muscle building. Perfect for complete beginners, those returning to exercise after a break, or anyone wanting to learn proper lifting techniques. Regular participation builds functional strength, improves bone density, and boosts metabolism for long-term health benefits.',
                    'price' => 35.00,
                    'duration' => 75,
                    'image' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.8,
                    'featured' => true,
                ],
                [
                    'name' => 'Advanced Powerlifting Training',
                    'description' => 'Intensive powerlifting-focused training for experienced lifters looking to maximize strength in the squat, bench press, and deadlift through advanced programming and technique refinement. This class is designed for serious strength athletes who want to compete or simply achieve their maximum strength potential. You\'ll work with percentage-based programming, learn advanced techniques like pause reps and tempo work, and receive coaching on competition-style lifting. Our powerlifting coach provides individualized feedback on form, helps identify and correct weaknesses, and guides you through periodized training cycles. The class covers meet preparation, equipment usage, and mental preparation for maximum lifts. Perfect for experienced lifters ready to take their strength to the next level or those interested in powerlifting competition. Requires solid foundation in basic lifts and ability to handle heavy weights safely.',
                    'price' => 55.00,
                    'duration' => 90,
                    'image' => 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.9,
                    'featured' => false,
                ],
                [
                    'name' => 'Functional Strength for Daily Life',
                    'description' => 'Practical strength training focused on movements and exercises that directly improve your ability to perform daily activities with ease and confidence. This functional approach emphasizes multi-joint movements, core stability, and real-world strength applications rather than isolated muscle building. You\'ll practice movements like lifting, carrying, pushing, pulling, and climbing using various equipment including kettlebells, resistance bands, and bodyweight exercises. Our trainer teaches you to move efficiently and safely in all planes of motion while building strength that translates to everyday tasks. The class is perfect for older adults, busy professionals, parents, or anyone who wants strength training that makes daily life easier. Regular participation improves balance, coordination, and functional capacity while reducing injury risk during daily activities.',
                    'price' => 30.00,
                    'duration' => 60,
                    'image' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.7,
                    'featured' => false,
                ],
                [
                    'name' => 'Women\'s Strength Training Bootcamp',
                    'description' => 'Empowering strength training class designed specifically for women, creating a supportive environment to build confidence and strength while dispelling myths about women and weightlifting. This class combines strength training with metabolic conditioning to build lean muscle, increase bone density, and boost metabolism. You\'ll learn that lifting weights won\'t make you bulky but will create a strong, toned physique and improve overall health. Our female trainer provides guidance on training during different life phases, addresses common concerns, and creates workout programs that fit busy lifestyles. The class covers proper nutrition for muscle building, body composition changes, and how strength training supports hormonal health. Perfect for women of all fitness levels who want to feel strong and confident. The supportive group environment encourages women to challenge themselves and achieve goals they never thought possible.',
                    'price' => 40.00,
                    'duration' => 60,
                    'image' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.8,
                    'featured' => false,
                ],
                [
                    'name' => 'Athletic Performance Training',
                    'description' => 'Sport-specific strength and conditioning program designed to enhance athletic performance through targeted training that improves power, speed, agility, and sport-specific strength. This advanced class incorporates plyometrics, Olympic lifting variations, and movement patterns that directly translate to improved athletic performance. You\'ll work on explosive power development, injury prevention protocols, and recovery strategies used by elite athletes. Our performance coach designs programs based on your specific sport and position requirements, addressing individual weaknesses and maximizing strengths. The class covers periodization for competitive seasons, testing and assessment protocols, and mental preparation techniques. Perfect for competitive athletes, weekend warriors, or anyone wanting to train like an athlete. The comprehensive approach addresses all aspects of performance including strength, power, speed, agility, and injury prevention for optimal athletic development.',
                    'price' => 50.00,
                    'duration' => 75,
                    'image' => 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.9,
                    'featured' => false,
                ],
            ],

            // Fitness Classes - Yoga
            'Yoga' => [
                [
                    'name' => 'Hatha Yoga for Beginners',
                    'description' => 'Gentle introduction to yoga focusing on basic postures, breathing techniques, and relaxation methods perfect for those new to yoga practice. This slow-paced class emphasizes proper alignment, flexibility development, and stress reduction through mindful movement and breath awareness. You\'ll learn fundamental poses, basic breathing exercises (pranayama), and meditation techniques that form the foundation of all yoga styles. Our experienced instructor provides detailed instruction and modifications to ensure everyone can participate safely regardless of fitness level or flexibility. The class creates a non-competitive, supportive environment where students can explore their bodies\' capabilities without judgment. Perfect for beginners, those with physical limitations, or anyone seeking a gentle approach to fitness and stress relief. Regular practice improves flexibility, strength, balance, and mental clarity while reducing stress and promoting overall well-being.',
                    'price' => 20.00,
                    'duration' => 75,
                    'image' => 'https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.8,
                    'featured' => true,
                ],
                [
                    'name' => 'Power Vinyasa Flow',
                    'description' => 'Dynamic, challenging yoga class that combines strength-building poses with flowing movements synchronized with breath to create a moving meditation that builds heat and energy. This intermediate to advanced class features creative sequences, arm balances, and inversions that challenge both physical and mental strength. You\'ll move through flowing transitions that require focus, coordination, and stamina while building lean muscle and improving cardiovascular fitness. Our skilled instructor guides you through creative sequences that vary each class, keeping the practice fresh and engaging. The class emphasizes the connection between breath and movement, creating a meditative flow state that reduces stress while building physical strength. Perfect for those with yoga experience looking for a challenging workout that combines fitness with mindfulness. Regular practice develops strength, flexibility, balance, and mental resilience.',
                    'price' => 30.00,
                    'duration' => 60,
                    'image' => 'https://images.unsplash.com/photo-1506629905607-c28b47e8b6b1?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.7,
                    'featured' => false,
                ],
                [
                    'name' => 'Prenatal Yoga',
                    'description' => 'Specialized yoga practice designed to support expectant mothers through the physical and emotional changes of pregnancy while preparing the body and mind for childbirth. This gentle class focuses on poses that are safe during pregnancy, breathing techniques for labor, and relaxation methods to reduce pregnancy-related stress and discomfort. You\'ll learn modifications for each trimester, poses that help with common pregnancy issues like back pain and swelling, and techniques to connect with your growing baby. Our prenatal-certified instructor creates a supportive community where expectant mothers can share experiences and concerns. The class includes pelvic floor strengthening, hip opening poses, and relaxation techniques that can be used during labor. Perfect for pregnant women at any stage who want to stay active safely and prepare for childbirth naturally. Regular practice can lead to easier labor, reduced pregnancy discomfort, and better emotional well-being.',
                    'price' => 25.00,
                    'duration' => 60,
                    'image' => 'https://images.unsplash.com/photo-1506629905607-c28b47e8b6b1?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.9,
                    'featured' => false,
                ],
                [
                    'name' => 'Restorative Yoga and Deep Relaxation',
                    'description' => 'Deeply relaxing yoga practice using props and supported poses to promote complete physical and mental relaxation, stress relief, and nervous system restoration. This gentle class uses bolsters, blankets, and blocks to support the body in comfortable positions held for extended periods, allowing for deep release and healing. You\'ll experience the benefits of passive stretching, guided meditation, and breathing exercises designed to activate the parasympathetic nervous system and promote deep relaxation. Our instructor creates a peaceful, nurturing environment that supports letting go of tension and stress. The class is perfect for anyone dealing with stress, anxiety, insomnia, or chronic pain, as well as those who simply want to balance more active practices with deep restoration. Regular practice improves sleep quality, reduces stress hormones, and promotes overall healing and well-being.',
                    'price' => 25.00,
                    'duration' => 75,
                    'image' => 'https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.8,
                    'featured' => false,
                ],
                [
                    'name' => 'Hot Yoga Challenge',
                    'description' => 'Intensive yoga practice performed in a heated room (95-105°F) that combines challenging poses with heat therapy to promote deep muscle relaxation, increased flexibility, and detoxification through sweating. This demanding class features a set sequence of poses designed to work every muscle, joint, and organ in the body while building mental discipline and focus. The heat allows for deeper stretching and helps prevent injury while promoting cardiovascular benefits similar to aerobic exercise. Our experienced hot yoga instructor guides you through proper hydration, breathing techniques, and safety protocols essential for practicing in heated conditions. The class builds mental toughness, physical strength, and flexibility while promoting detoxification and stress relief. Perfect for experienced yoga practitioners looking for an intense challenge or those who enjoy heat therapy benefits. Regular practice dramatically improves flexibility, strength, and mental resilience.',
                    'price' => 35.00,
                    'duration' => 90,
                    'image' => 'https://images.unsplash.com/photo-1506629905607-c28b47e8b6b1?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.6,
                    'featured' => false,
                ],
            ],

            // Fitness Classes - Zumba
            'Zumba' => [
                [
                    'name' => 'Zumba Fitness Party',
                    'description' => 'High-energy dance fitness class that combines Latin and international music with fun, easy-to-follow dance moves to create an effective workout that feels like a party. This beginner-friendly class features a mix of salsa, merengue, cumbia, reggaeton, and other dance styles that get your heart pumping while improving coordination and rhythm. You\'ll burn calories, tone muscles, and boost cardiovascular fitness while having so much fun you\'ll forget you\'re exercising. Our certified Zumba instructor creates an inclusive, judgment-free environment where everyone can move at their own pace and style. The class welcomes all fitness levels and dance experience - no coordination required, just a willingness to move and have fun. Perfect for those who find traditional exercise boring or anyone who loves music and dancing. Regular participation improves cardiovascular health, coordination, and mood while providing an excellent calorie burn.',
                    'price' => 18.00,
                    'duration' => 60,
                    'image' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.7,
                    'featured' => true,
                ],
                [
                    'name' => 'Zumba Gold for Seniors',
                    'description' => 'Modified Zumba program designed specifically for older adults and beginners, featuring lower-impact movements and a slower pace while maintaining the fun, party atmosphere of traditional Zumba. This age-appropriate class focuses on balance, coordination, and cardiovascular health while accommodating physical limitations and varying fitness levels. You\'ll enjoy the same great music and dance styles but with movements that are easier on joints and more accessible for seniors. Our Zumba Gold certified instructor emphasizes safety, provides chair modifications when needed, and creates a supportive community atmosphere. The class improves balance, flexibility, and cognitive function while providing social interaction and mood enhancement. Perfect for active seniors, those with mobility limitations, or anyone preferring a gentler approach to dance fitness. Regular participation helps maintain independence, improves quality of life, and provides the joy of movement and music.',
                    'price' => 15.00,
                    'duration' => 45,
                    'image' => 'https://images.unsplash.com/photo-1506629905607-c28b47e8b6b1?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.8,
                    'featured' => false,
                ],
                [
                    'name' => 'Aqua Zumba Water Fitness',
                    'description' => 'Innovative water-based Zumba class that combines the fun of dance with the benefits of aquatic exercise, creating a low-impact, high-energy workout perfect for all fitness levels. The water provides natural resistance while supporting joints, making this class ideal for those with arthritis, injuries, or anyone who wants an effective workout without high impact stress. You\'ll enjoy the same great Zumba music and moves adapted for the pool environment, creating a unique and refreshing fitness experience. Our Aqua Zumba instructor leads you through choreography designed specifically for water, taking advantage of water\'s resistance and buoyancy properties. The class provides excellent cardiovascular conditioning, muscle toning, and flexibility improvement while being gentle on joints. Perfect for seniors, those recovering from injury, pregnant women, or anyone who loves water and wants to try something different. The cooling effect of water makes this an ideal summer workout.',
                    'price' => 22.00,
                    'duration' => 50,
                    'image' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.6,
                    'featured' => false,
                ],
                [
                    'name' => 'Zumba Toning with Weights',
                    'description' => 'Enhanced Zumba class that incorporates lightweight toning sticks (or dumbbells) to add resistance training to the dance party, creating a complete workout that builds lean muscle while burning calories. This intermediate class combines the cardiovascular benefits of Zumba with strength training elements to sculpt and tone the entire body. You\'ll use 1-2 pound weights during specific songs to target arms, core, and other muscle groups while maintaining the fun, dance-based format. Our instructor demonstrates proper form for weighted movements and provides modifications for different fitness levels. The class alternates between pure dance cardio and toning segments, creating variety and targeting different energy systems. Perfect for those who want to add strength training to their cardio routine or Zumba lovers looking to take their workout to the next level. Regular participation improves muscle definition, bone density, and overall body composition.',
                    'price' => 25.00,
                    'duration' => 60,
                    'image' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.7,
                    'featured' => false,
                ],
                [
                    'name' => 'Kids Zumba Dance Party',
                    'description' => 'Fun, energetic dance class designed specifically for children ages 4-12, combining age-appropriate music with simple dance moves to promote physical activity, coordination, and self-expression. This class helps kids develop a love for movement and music while improving motor skills, rhythm, and confidence. You\'ll see your child learn basic dance steps, follow directions, and express creativity through movement in a supportive, non-competitive environment. Our kid-friendly instructor uses games, props, and interactive activities to keep children engaged while sneaking in a great workout. The class promotes social skills, teamwork, and following instructions while building physical fitness and coordination. Perfect for active kids, those who love music and dancing, or parents looking for a fun way to get their children moving. Regular participation improves coordination, builds confidence, and establishes healthy exercise habits that can last a lifetime.',
                    'price' => 12.00,
                    'duration' => 30,
                    'image' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.9,
                    'featured' => false,
                ],
            ],
        ];
    }
}
