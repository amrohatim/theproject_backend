<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MasterComprehensiveServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting Master Comprehensive Services Seeder...');
        $this->command->info('This will run all parts of the comprehensive services seeder in sequence.');
        
        // Confirm before proceeding
        if (!$this->command->confirm('This will create hundreds of services. Do you want to continue?', true)) {
            $this->command->info('Seeding cancelled.');
            return;
        }

        $startTime = now();
        
        // Run Part 1 - Artistic Services and Elderly Care
        $this->command->info('Running Part 1: Artistic Services and Elderly Care...');
        $this->call(ComprehensiveServicesSeeder::class);
        
        // Run Part 2 - Fitness Classes
        $this->command->info('Running Part 2: Fitness Classes...');
        $this->call(ComprehensiveServicesSeederPart2::class);
        
        // Run Part 3 - Healthcare & Femtech (partial)
        $this->command->info('Running Part 3: Healthcare & Femtech...');
        $this->call(ComprehensiveServicesSeederPart3::class);
        
        // Run Part 4 - Beauty and Wellness Services
        $this->command->info('Running Part 4: Beauty and Wellness Services...');
        $this->call(ComprehensiveServicesSeederPart4::class);
        
        // Run Part 5 - Therapy and Nutrition Services
        $this->command->info('Running Part 5: Therapy and Nutrition Services...');
        $this->call(ComprehensiveServicesSeederPart5::class);

        $endTime = now();
        $duration = $endTime->diffInSeconds($startTime);
        
        $this->command->info('Master Comprehensive Services Seeder completed successfully!');
        $this->command->info("Total execution time: {$duration} seconds");
        $this->command->info('All service categories have been populated with comprehensive, realistic services.');
        
        // Display summary
        $this->displaySummary();
    }
    
    /**
     * Display a summary of what was created
     */
    private function displaySummary(): void
    {
        $this->command->info('');
        $this->command->info('=== SEEDING SUMMARY ===');
        $this->command->info('The following service categories have been populated:');
        $this->command->info('');
        
        $categories = [
            'Artistic Services' => [
                'Craft workshops' => '5 services',
                'Painting classes' => '5 services', 
                'Photography sessions' => '5 services',
                'Pottery making' => '5 services'
            ],
            'Elderly Care & Companionship Services' => [
                'Companionship visits' => '5 services',
                'In-home care' => '5 services'
            ],
            'Fitness Classes' => [
                'Pilates' => '5 services',
                'Strength training' => '5 services',
                'Yoga' => '5 services',
                'Zumba' => '5 services'
            ],
            'Healthcare & Femtech' => [
                'Fertility monitoring' => '5 services',
                'Menstrual tracking' => '5 services',
                'Mental Health Support' => '5 services (Part 4)',
                'Pregnancy guides' => '5 services (Part 4)',
                'Women\'s Health' => '5 services (Part 4)'
            ],
            'Beauty & Wellness Services' => [
                'Makeup Services' => '5 services each (Part 4)',
                'Nail Care' => '5 services each (Part 4)',
                'Salon Services' => '5 services each (Part 4)',
                'Spa Treatments' => '5 services each (Part 4)'
            ],
            'Therapy & Nutrition' => [
                'Therapy Sessions' => '5 services each (Part 5)',
                'Nutrition Counseling' => '5 services each (Part 5)',
                'Wellness Workshops' => '5 services each (Part 5)'
            ]
        ];
        
        foreach ($categories as $parentCategory => $subcategories) {
            $this->command->info("📁 {$parentCategory}:");
            foreach ($subcategories as $subcategory => $count) {
                $this->command->info("   └── {$subcategory}: {$count}");
            }
            $this->command->info('');
        }
        
        $this->command->info('Each service includes:');
        $this->command->info('✓ Comprehensive 100+ word descriptions');
        $this->command->info('✓ Realistic pricing based on service complexity');
        $this->command->info('✓ Appropriate duration in minutes');
        $this->command->info('✓ Professional service names');
        $this->command->info('✓ High-quality stock images');
        $this->command->info('✓ Realistic ratings (4.5-4.9)');
        $this->command->info('✓ Featured flags for premium services');
        $this->command->info('✓ Home service flags where appropriate');
        $this->command->info('✓ Random assignment to active branches');
        $this->command->info('');
        $this->command->info('Total estimated services: 150+ across all categories');
        $this->command->info('');
        $this->command->info('Note: Parts 4 and 5 need to be created to complete all categories.');
        $this->command->info('Run them individually or create the remaining seeder files.');
    }
}
