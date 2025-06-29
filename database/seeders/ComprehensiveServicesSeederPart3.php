<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Category;
use App\Models\Branch;

class ComprehensiveServicesSeederPart3 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting Comprehensive Services Seeder Part 3...');

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

        $this->command->info("Service seeding Part 3 completed successfully!");
        $this->command->info("Total services created in Part 3: {$totalServicesCreated}");
    }

    /**
     * Get comprehensive services data for healthcare, beauty, and wellness categories
     */
    private function getServicesData(): array
    {
        return [
            // Healthcare & Femtech - Fertility monitoring
            'Fertility monitoring' => [
                [
                    'name' => 'Comprehensive Fertility Assessment',
                    'description' => 'Complete fertility evaluation including hormone testing, ovulation tracking, and reproductive health analysis to help women understand their fertility status and optimize conception chances. This comprehensive service includes detailed consultation with fertility specialists, review of menstrual cycle patterns, and personalized recommendations for improving fertility. You\'ll receive guidance on nutrition, lifestyle factors, and timing strategies that can enhance reproductive health. Our certified fertility counselors provide education about the menstrual cycle, ovulation signs, and optimal timing for conception. The assessment includes recommendations for fertility-friendly supplements, stress management techniques, and when to seek additional medical intervention. Perfect for women trying to conceive, those with irregular cycles, or anyone wanting to understand their reproductive health better. The service provides valuable insights and actionable steps to support your fertility journey.',
                    'price' => 150.00,
                    'duration' => 90,
                    'image' => 'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.8,
                    'featured' => true,
                ],
                [
                    'name' => 'Ovulation Tracking and Cycle Optimization',
                    'description' => 'Personalized ovulation tracking service using advanced monitoring techniques to identify your most fertile days and optimize timing for conception. This service combines traditional fertility awareness methods with modern technology to provide accurate ovulation prediction. You\'ll learn to track basal body temperature, cervical mucus changes, and other fertility signs while using digital tools for enhanced accuracy. Our fertility specialists provide ongoing support and interpretation of your data, helping you understand your unique cycle patterns. The service includes education about fertility windows, lifestyle factors that affect ovulation, and strategies for maximizing conception chances. Perfect for women with irregular cycles, those who have been trying to conceive, or anyone wanting to understand their body\'s natural rhythms better. Regular monitoring helps identify potential issues early and provides valuable data for healthcare providers.',
                    'price' => 85.00,
                    'duration' => 60,
                    'image' => 'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.7,
                    'featured' => false,
                ],
                [
                    'name' => 'Preconception Health Counseling',
                    'description' => 'Comprehensive preconception counseling service designed to optimize health and wellness before pregnancy, addressing nutrition, lifestyle, and medical factors that can impact fertility and pregnancy outcomes. This service includes detailed health assessment, nutritional analysis, and personalized recommendations for preparing your body for pregnancy. You\'ll receive guidance on prenatal vitamins, dietary changes, exercise recommendations, and lifestyle modifications that support fertility and healthy pregnancy. Our certified preconception counselors address concerns about age, previous pregnancy complications, and family history while providing evidence-based recommendations. The service covers topics like weight management, stress reduction, environmental toxin exposure, and partner health considerations. Perfect for women planning pregnancy, those with previous pregnancy complications, or couples wanting to optimize their health before conceiving. The comprehensive approach helps ensure the best possible start for both mother and baby.',
                    'price' => 120.00,
                    'duration' => 75,
                    'image' => 'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.9,
                    'featured' => false,
                ],
                [
                    'name' => 'Male Fertility Support and Education',
                    'description' => 'Specialized fertility support service for men focusing on male reproductive health, lifestyle factors, and partner support during the conception journey. This service addresses the often-overlooked male component of fertility, providing education about sperm health, lifestyle factors that affect male fertility, and ways men can support their partners during fertility treatments. You\'ll receive guidance on nutrition, exercise, stress management, and environmental factors that impact sperm quality. Our male fertility specialists provide confidential consultation about concerns, testing recommendations, and lifestyle modifications that can improve fertility outcomes. The service includes partner communication strategies and emotional support for men navigating fertility challenges. Perfect for men wanting to optimize their fertility, those with known fertility issues, or partners supporting women through fertility treatments. The comprehensive approach recognizes that fertility is a shared responsibility and provides men with actionable steps to contribute positively to conception efforts.',
                    'price' => 100.00,
                    'duration' => 60,
                    'image' => 'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.6,
                    'featured' => false,
                ],
                [
                    'name' => 'Fertility Technology and App Integration',
                    'description' => 'Modern fertility tracking service that integrates cutting-edge technology, apps, and wearable devices to provide comprehensive fertility monitoring and data analysis. This tech-savvy approach combines traditional fertility awareness with digital tools for enhanced accuracy and convenience. You\'ll learn to use fertility apps, wearable temperature monitors, and other digital tools while understanding how to interpret and act on the data they provide. Our tech-certified fertility counselors help you choose the right tools for your needs and integrate multiple data sources for comprehensive fertility tracking. The service includes troubleshooting technology issues, data interpretation, and recommendations for the most effective digital fertility tools. Perfect for tech-savvy women, busy professionals who need convenient tracking methods, or anyone wanting to leverage technology for fertility optimization. The modern approach makes fertility tracking more accessible and accurate while providing valuable data for healthcare providers.',
                    'price' => 95.00,
                    'duration' => 45,
                    'image' => 'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.5,
                    'featured' => false,
                ],
            ],

            // Healthcare & Femtech - Menstrual tracking
            'Menstrual tracking' => [
                [
                    'name' => 'Comprehensive Menstrual Health Assessment',
                    'description' => 'Detailed evaluation of menstrual health patterns, symptoms, and overall reproductive wellness to identify potential issues and optimize menstrual cycle health. This comprehensive service includes analysis of cycle length, flow patterns, PMS symptoms, and associated health concerns. You\'ll receive personalized recommendations for managing menstrual symptoms, improving cycle regularity, and supporting overall reproductive health. Our certified menstrual health specialists provide education about normal vs. abnormal menstrual patterns and when to seek medical attention. The assessment covers lifestyle factors that affect menstrual health, including nutrition, exercise, stress, and sleep patterns. Perfect for women with irregular cycles, severe PMS, or anyone wanting to better understand their menstrual health. The service provides valuable insights for optimizing reproductive wellness and identifying potential health concerns early.',
                    'price' => 80.00,
                    'duration' => 60,
                    'image' => 'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.8,
                    'featured' => true,
                ],
                [
                    'name' => 'PMS and PMDD Management Program',
                    'description' => 'Specialized program for managing premenstrual syndrome (PMS) and premenstrual dysphoric disorder (PMDD) through natural approaches, lifestyle modifications, and symptom tracking. This comprehensive service addresses the physical and emotional symptoms that can significantly impact quality of life during the premenstrual phase. You\'ll learn evidence-based strategies for managing mood changes, physical discomfort, and other PMS symptoms through nutrition, exercise, stress management, and targeted supplements. Our certified PMS specialists provide personalized treatment plans based on your specific symptoms and lifestyle. The program includes tracking tools to identify patterns and triggers, helping you anticipate and manage symptoms more effectively. Perfect for women experiencing severe PMS, those diagnosed with PMDD, or anyone wanting natural approaches to menstrual symptom management. The holistic approach addresses root causes while providing practical tools for immediate symptom relief.',
                    'price' => 110.00,
                    'duration' => 75,
                    'image' => 'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.7,
                    'featured' => false,
                ],
                [
                    'name' => 'Menstrual Cycle Education and Empowerment',
                    'description' => 'Educational service designed to help women understand their menstrual cycles, normalize menstrual experiences, and develop a positive relationship with their reproductive health. This empowering program covers menstrual cycle physiology, hormonal changes throughout the cycle, and how these changes affect mood, energy, and overall well-being. You\'ll learn to work with your natural rhythms rather than against them, optimizing productivity and self-care based on cycle phases. Our certified menstrual educators provide evidence-based information while addressing cultural taboos and misconceptions about menstruation. The service includes practical guidance on menstrual products, pain management, and when to seek medical care. Perfect for young women beginning their menstrual journey, those wanting to better understand their cycles, or anyone seeking to develop a healthier relationship with their reproductive health. The educational approach promotes body literacy and menstrual confidence.',
                    'price' => 65.00,
                    'duration' => 90,
                    'image' => 'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.9,
                    'featured' => false,
                ],
                [
                    'name' => 'Hormonal Balance Optimization',
                    'description' => 'Comprehensive service focusing on natural hormone balance through lifestyle modifications, nutrition, and targeted interventions to support healthy menstrual cycles and overall hormonal wellness. This holistic approach addresses the root causes of hormonal imbalances that can affect menstrual health, mood, energy, and overall well-being. You\'ll receive personalized recommendations for nutrition, exercise, stress management, and sleep optimization that support healthy hormone production and metabolism. Our certified hormone health specialists provide guidance on natural supplements, herbal remedies, and lifestyle practices that promote hormonal balance. The service includes education about how different life phases affect hormones and strategies for supporting hormonal health throughout these transitions. Perfect for women experiencing hormonal imbalances, irregular cycles, or those wanting to optimize their hormonal health naturally. The comprehensive approach addresses multiple factors that influence hormonal wellness for lasting results.',
                    'price' => 125.00,
                    'duration' => 80,
                    'image' => 'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.8,
                    'featured' => false,
                ],
                [
                    'name' => 'Digital Menstrual Tracking Setup and Support',
                    'description' => 'Technology-focused service that helps women set up and optimize digital menstrual tracking tools, apps, and devices for comprehensive cycle monitoring and health insights. This modern approach to menstrual tracking combines convenience with accuracy, helping you gather valuable data about your reproductive health. You\'ll learn to use various tracking apps, understand which metrics are most important to monitor, and how to interpret the data for health insights. Our tech-savvy menstrual health specialists help you choose the right digital tools for your needs and integrate tracking into your daily routine. The service includes troubleshooting common issues, privacy considerations, and how to share data effectively with healthcare providers. Perfect for busy women who want convenient tracking methods, those interested in data-driven health insights, or anyone wanting to leverage technology for better menstrual health management. The digital approach makes tracking more accessible and provides valuable long-term health data.',
                    'price' => 55.00,
                    'duration' => 45,
                    'image' => 'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'rating' => 4.6,
                    'featured' => false,
                ],
            ],
        ];
    }
}
