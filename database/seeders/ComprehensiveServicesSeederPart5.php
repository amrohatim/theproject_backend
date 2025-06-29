<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Category;
use App\Models\Branch;

class ComprehensiveServicesSeederPart5 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting Comprehensive Services Seeder Part 5...');

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

        $this->command->info("Service seeding Part 5 completed successfully!");
        $this->command->info("Total services created in Part 5: {$totalServicesCreated}");
    }

    /**
     * Get comprehensive services data for therapy and wellness categories
     */
    private function getServicesData(): array
    {
        return [
            // Therapy Sessions - Individual Therapy
            'Individual Therapy' => [
                [
                    'name' => 'Cognitive Behavioral Therapy (CBT)',
                    'description' => 'Evidence-based individual therapy focusing on identifying and changing negative thought patterns and behaviors that contribute to emotional distress and mental health challenges. CBT is a structured, goal-oriented approach that helps you develop practical coping strategies and problem-solving skills for managing anxiety, depression, trauma, and other mental health concerns. You\'ll work with a licensed therapist to understand the connection between thoughts, feelings, and behaviors, learning to recognize and challenge unhelpful thinking patterns. Sessions include homework assignments, skill-building exercises, and practical tools you can use in daily life. Our CBT specialists are trained in the latest evidence-based techniques and tailor the approach to your specific needs and goals. Perfect for individuals dealing with anxiety disorders, depression, PTSD, OCD, or anyone wanting to develop better emotional regulation and coping skills. The structured approach provides measurable progress and lasting tools for mental wellness.',
                    'price' => 120.00,
                    'duration' => 50,
                    'image' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.9,
                    'featured' => true,
                ],
                [
                    'name' => 'Trauma-Informed Therapy and EMDR',
                    'description' => 'Specialized therapy for individuals who have experienced trauma, using evidence-based approaches including Eye Movement Desensitization and Reprocessing (EMDR) to help process and heal from traumatic experiences. This gentle yet effective approach helps your brain process traumatic memories in a way that reduces their emotional impact and allows for healing. You\'ll work with a trauma-specialized therapist who understands the complex effects of trauma on the mind and body. Sessions are conducted at your pace with careful attention to safety and stabilization before processing work begins. Our trauma therapists are trained in multiple modalities including EMDR, somatic approaches, and trauma-focused CBT. The therapy helps reduce symptoms of PTSD, anxiety, depression, and other trauma-related conditions while building resilience and post-traumatic growth. Perfect for survivors of abuse, accidents, military trauma, or any overwhelming life experiences that continue to impact daily functioning.',
                    'price' => 140.00,
                    'duration' => 60,
                    'image' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.8,
                    'featured' => false,
                ],
                [
                    'name' => 'Mindfulness-Based Therapy',
                    'description' => 'Integrative therapy approach that combines traditional talk therapy with mindfulness meditation and awareness practices to help you develop a healthier relationship with thoughts, emotions, and life experiences. This holistic approach teaches you to observe your thoughts and feelings without judgment, reducing reactivity and increasing emotional regulation. You\'ll learn practical mindfulness techniques that can be used in daily life to manage stress, anxiety, and difficult emotions. Sessions include guided meditations, breathing exercises, and mindful awareness practices alongside traditional therapeutic conversation. Our mindfulness-trained therapists help you develop present-moment awareness and acceptance while working through specific mental health concerns. The approach is particularly effective for anxiety, depression, chronic pain, and stress-related conditions. Perfect for individuals interested in holistic approaches to mental health, those who want to develop meditation skills, or anyone seeking to cultivate greater peace and emotional balance in their lives.',
                    'price' => 110.00,
                    'duration' => 55,
                    'image' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.7,
                    'featured' => false,
                ],
                [
                    'name' => 'Life Transitions and Adjustment Counseling',
                    'description' => 'Supportive therapy designed to help individuals navigate major life changes, transitions, and adjustment challenges with greater ease and resilience. Life transitions can be overwhelming even when they\'re positive, and this specialized counseling provides tools and support for managing change effectively. You\'ll work with a therapist who understands the psychological impact of transitions and can help you process the emotions, fears, and excitement that come with change. Sessions focus on developing coping strategies, building resilience, and finding meaning and opportunity within challenging circumstances. Our transition specialists help with career changes, relationship changes, loss and grief, relocation, retirement, parenthood, and other major life shifts. The therapy provides a safe space to explore your feelings about change while developing practical skills for adaptation. Perfect for anyone facing major life transitions, those feeling stuck or overwhelmed by change, or individuals wanting to approach life transitions with greater confidence and clarity.',
                    'price' => 100.00,
                    'duration' => 50,
                    'image' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.8,
                    'featured' => false,
                ],
                [
                    'name' => 'Anxiety and Stress Management Therapy',
                    'description' => 'Specialized therapy focused specifically on understanding and managing anxiety disorders, panic attacks, and chronic stress through evidence-based techniques and personalized coping strategies. This targeted approach helps you understand the root causes of your anxiety while developing practical tools for managing symptoms and preventing escalation. You\'ll learn relaxation techniques, breathing exercises, cognitive restructuring, and exposure therapy methods tailored to your specific anxiety triggers. Sessions include education about the anxiety response, identification of personal triggers, and development of a comprehensive anxiety management plan. Our anxiety specialists are trained in the latest research and techniques for treating various anxiety disorders including generalized anxiety, social anxiety, panic disorder, and specific phobias. The therapy provides both immediate relief strategies and long-term tools for maintaining emotional balance. Perfect for individuals struggling with anxiety disorders, those experiencing chronic stress, or anyone wanting to develop better stress management skills for improved quality of life.',
                    'price' => 115.00,
                    'duration' => 50,
                    'image' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.9,
                    'featured' => false,
                ],
            ],

            // Nutrition Counseling - Diet plans
            'Diet plans' => [
                [
                    'name' => 'Personalized Weight Management Program',
                    'description' => 'Comprehensive, science-based weight management program that creates a personalized nutrition and lifestyle plan tailored to your individual needs, preferences, and health goals. This holistic approach goes beyond simple calorie counting to address the complex factors that influence weight, including metabolism, hormones, lifestyle, and psychological relationship with food. You\'ll receive a detailed assessment of your current eating patterns, medical history, and lifestyle factors, followed by a customized plan that fits your schedule and preferences. Our registered dietitians provide ongoing support, meal planning assistance, and regular adjustments to ensure sustainable progress. The program includes education about nutrition science, portion control, mindful eating, and strategies for maintaining long-term success. Perfect for individuals wanting to lose weight sustainably, those who have struggled with yo-yo dieting, or anyone seeking a healthy, balanced approach to weight management that doesn\'t involve restrictive or extreme measures.',
                    'price' => 150.00,
                    'duration' => 90,
                    'image' => 'https://images.unsplash.com/photo-1490645935967-10de6ba17061?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.8,
                    'featured' => true,
                ],
                [
                    'name' => 'Medical Nutrition Therapy',
                    'description' => 'Specialized nutrition counseling for individuals with medical conditions that require dietary management, including diabetes, heart disease, kidney disease, digestive disorders, and other chronic health conditions. This evidence-based approach uses nutrition as a therapeutic tool to manage symptoms, slow disease progression, and improve overall health outcomes. You\'ll work with a registered dietitian who specializes in medical nutrition therapy and understands the complex interactions between food, medications, and health conditions. The service includes detailed meal planning that considers your medical restrictions, medication timing, and treatment goals while ensuring nutritional adequacy and meal enjoyment. Our medical nutrition specialists coordinate with your healthcare team to ensure your nutrition plan supports your overall treatment plan. Perfect for individuals newly diagnosed with chronic conditions, those struggling to manage their condition through diet, or anyone wanting to optimize their nutrition for better health outcomes and disease management.',
                    'price' => 130.00,
                    'duration' => 75,
                    'image' => 'https://images.unsplash.com/photo-1490645935967-10de6ba17061?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.9,
                    'featured' => false,
                ],
                [
                    'name' => 'Plant-Based Nutrition Transition',
                    'description' => 'Comprehensive guidance for transitioning to a plant-based diet safely and sustainably, ensuring nutritional adequacy while exploring the health, environmental, and ethical benefits of plant-based eating. This specialized service addresses common concerns about protein, vitamins, minerals, and meal planning while making the transition enjoyable and sustainable. You\'ll receive education about plant-based nutrition science, meal planning strategies, shopping guides, and cooking techniques that make plant-based eating delicious and satisfying. Our plant-based nutrition specialists help you navigate social situations, dining out, and family meal planning while ensuring you meet all nutritional needs. The program includes gradual transition strategies, recipe suggestions, and ongoing support to help you maintain your new eating pattern long-term. Perfect for individuals interested in plant-based eating for health reasons, those concerned about environmental impact, or anyone wanting to reduce animal product consumption while maintaining optimal nutrition and meal satisfaction.',
                    'price' => 110.00,
                    'duration' => 60,
                    'image' => 'https://images.unsplash.com/photo-1490645935967-10de6ba17061?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.7,
                    'featured' => false,
                ],
                [
                    'name' => 'Sports Nutrition and Performance Optimization',
                    'description' => 'Specialized nutrition counseling for athletes and active individuals focused on optimizing performance, recovery, and body composition through strategic nutrition timing and food choices. This performance-focused approach considers your training schedule, sport-specific demands, and individual goals to create a nutrition plan that enhances athletic performance. You\'ll learn about pre-workout nutrition, post-workout recovery, hydration strategies, and competition day fueling that maximizes your athletic potential. Our sports nutrition specialists understand the unique nutritional needs of different sports and training phases, providing guidance on supplements, meal timing, and body composition goals. The service includes practical strategies for meal prep, travel nutrition, and managing nutrition during different training seasons. Perfect for competitive athletes, weekend warriors, fitness enthusiasts, or anyone wanting to optimize their nutrition for better workout performance, faster recovery, and improved body composition while maintaining overall health and energy.',
                    'price' => 125.00,
                    'duration' => 70,
                    'image' => 'https://images.unsplash.com/photo-1490645935967-10de6ba17061?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.8,
                    'featured' => false,
                ],
                [
                    'name' => 'Intuitive Eating and Mindful Nutrition',
                    'description' => 'Revolutionary approach to nutrition that helps you develop a healthy, sustainable relationship with food by learning to trust your body\'s natural hunger and fullness cues while rejecting diet culture and food restrictions. This anti-diet approach focuses on healing your relationship with food, body image, and eating behaviors rather than pursuing weight loss or following external food rules. You\'ll learn to distinguish between physical and emotional hunger, practice mindful eating techniques, and develop body neutrality and acceptance. Our intuitive eating counselors are trained in Health at Every Size principles and help you unlearn diet mentality while rediscovering the joy and satisfaction of eating. The approach addresses emotional eating, food guilt, and the psychological aspects of eating while promoting overall well-being regardless of weight. Perfect for individuals recovering from disordered eating, those tired of diet culture, chronic dieters wanting to break the cycle, or anyone seeking a peaceful, sustainable relationship with food and their body.',
                    'price' => 120.00,
                    'duration' => 60,
                    'image' => 'https://images.unsplash.com/photo-1490645935967-10de6ba17061?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.9,
                    'featured' => false,
                ],
            ],
        ];
    }
}
