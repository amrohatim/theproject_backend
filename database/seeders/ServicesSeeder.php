<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get categories for services
        $categories = Category::where('type', 'service')->get();

        // If no service categories, create one
        if ($categories->isEmpty()) {
            $category = Category::create([
                'name' => 'General Services',
                'type' => 'service',
                'icon' => 'fa-concierge-bell',
            ]);
            $categories = collect([$category]);
        }

        // Get branches
        $branches = Branch::all();

        // If no branches, skip seeding
        if ($branches->isEmpty()) {
            $this->command->info('No branches found. Skipping service seeding.');
            return;
        }

        // Sample services data
        $servicesData = [
            [
                'name' => 'Haircut',
                'description' => 'Professional haircut by our expert stylists',
                'price' => 35.00,
                'duration' => 30,
                'image' => 'https://images.unsplash.com/photo-1560066984-138dadb4c035?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1674&q=80',
            ],
            [
                'name' => 'Hair Coloring',
                'description' => 'Full hair coloring service with premium products',
                'price' => 75.00,
                'duration' => 90,
                'image' => 'https://images.unsplash.com/photo-1562322140-8baeececf3df?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1769&q=80',
            ],
            [
                'name' => 'Manicure',
                'description' => 'Professional manicure service',
                'price' => 25.00,
                'duration' => 45,
                'image' => 'https://images.unsplash.com/photo-1610992015732-2449b76344bc?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1770&q=80',
            ],
            [
                'name' => 'Pedicure',
                'description' => 'Relaxing pedicure treatment',
                'price' => 30.00,
                'duration' => 60,
                'image' => 'https://images.unsplash.com/photo-1519415510236-718bdfcd89c8?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1770&q=80',
            ],
            [
                'name' => 'Facial Treatment',
                'description' => 'Rejuvenating facial treatment for all skin types',
                'price' => 50.00,
                'duration' => 60,
                'image' => 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1770&q=80',
            ],
        ];

        foreach ($servicesData as $serviceData) {
            $branch = $branches->random();
            $category = $categories->random();

            Service::create([
                'name' => $serviceData['name'],
                'description' => $serviceData['description'],
                'price' => $serviceData['price'],
                'duration' => $serviceData['duration'],
                'image' => $serviceData['image'],
                'branch_id' => $branch->id,
                'category_id' => $category->id,
                'is_available' => true,
            ]);
        }
    }
}
