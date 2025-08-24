<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;
use App\Models\Company;
use App\Helpers\UnsplashImageHelper;

class BranchesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all companies
        $companies = Company::all();

        // Default opening hours template
        $defaultOpeningHours = [
            'monday' => ['is_open' => true, 'open' => '09:00', 'close' => '18:00'],
            'tuesday' => ['is_open' => true, 'open' => '09:00', 'close' => '18:00'],
            'wednesday' => ['is_open' => true, 'open' => '09:00', 'close' => '18:00'],
            'thursday' => ['is_open' => true, 'open' => '09:00', 'close' => '18:00'],
            'friday' => ['is_open' => true, 'open' => '09:00', 'close' => '18:00'],
            'saturday' => ['is_open' => true, 'open' => '10:00', 'close' => '16:00'],
            'sunday' => ['is_open' => false, 'open' => null, 'close' => null],
        ];

        // Branch data templates
        $branchTemplates = [
            // Tech Solutions branches
            [
                'name' => 'Downtown Tech Hub',
                'address' => '123 Main Street, San Francisco, CA 94105',
                'lat' => 37.7749,
                'lng' => -122.4194,
                'status' => 'active',
                'image' => UnsplashImageHelper::downloadAndSaveImage('tech office downtown', 'public/images/branches/tech-downtown.jpg', 800, 600),
                'description' => 'Our flagship technology center in the heart of downtown.',
                'rating' => 4.8,
                'phone' => '1234567890',
                'email' => 'downtown@techsolutions.example.com',
                'opening_hours' => $defaultOpeningHours,
            ],
            [
                'name' => 'Silicon Valley Office',
                'address' => '456 Innovation Drive, Palo Alto, CA 94301',
                'lat' => 37.4419,
                'lng' => -122.1430,
                'status' => 'active',
                'image' => UnsplashImageHelper::downloadAndSaveImage('silicon valley office', 'public/images/branches/tech-valley.jpg', 800, 600),
                'description' => 'Our Silicon Valley branch specializing in cutting-edge research.',
                'rating' => 4.7,
                'phone' => '1234567891',
                'email' => 'valley@techsolutions.example.com',
                'opening_hours' => $defaultOpeningHours,
            ],

            // Wellness Center branches
            [
                'name' => 'Beverly Hills Wellness',
                'address' => '789 Relaxation Ave, Beverly Hills, CA 90210',
                'lat' => 34.0736,
                'lng' => -118.4004,
                'status' => 'active',
                'image' => UnsplashImageHelper::downloadAndSaveImage('luxury spa beverly hills', 'public/images/branches/wellness-beverly.jpg', 800, 600),
                'description' => 'Luxury wellness services in the heart of Beverly Hills.',
                'rating' => 4.9,
                'phone' => '2345678901',
                'email' => 'beverly@wellness.example.com',
                'opening_hours' => $defaultOpeningHours,
            ],
            [
                'name' => 'Santa Monica Beach Center',
                'address' => '101 Ocean View Blvd, Santa Monica, CA 90401',
                'lat' => 34.0195,
                'lng' => -118.4912,
                'status' => 'active',
                'image' => UnsplashImageHelper::downloadAndSaveImage('beach spa santa monica', 'public/images/branches/wellness-santa-monica.jpg', 800, 600),
                'description' => 'Beachside wellness center with ocean views and fresh air.',
                'rating' => 4.6,
                'phone' => '2345678902',
                'email' => 'santamonica@wellness.example.com',
                'opening_hours' => $defaultOpeningHours,
            ],

            // Gourmet Delights branches
            [
                'name' => 'Manhattan Gourmet',
                'address' => '222 Fifth Avenue, New York, NY 10001',
                'lat' => 40.7128,
                'lng' => -74.0060,
                'status' => 'active',
                'image' => UnsplashImageHelper::downloadAndSaveImage('gourmet restaurant manhattan', 'public/images/branches/gourmet-manhattan.jpg', 800, 600),
                'description' => 'Our flagship gourmet store in Manhattan.',
                'rating' => 4.7,
                'phone' => '3456789012',
                'email' => 'manhattan@gourmet.example.com',
                'opening_hours' => $defaultOpeningHours,
            ],
            [
                'name' => 'Brooklyn Artisanal',
                'address' => '333 Williamsburg St, Brooklyn, NY 11211',
                'lat' => 40.7128,
                'lng' => -73.9500,
                'status' => 'active',
                'image' => UnsplashImageHelper::downloadAndSaveImage('artisanal food brooklyn', 'public/images/branches/gourmet-brooklyn.jpg', 800, 600),
                'description' => 'Artisanal food products in trendy Williamsburg.',
                'rating' => 4.5,
                'phone' => '3456789013',
                'email' => 'brooklyn@gourmet.example.com',
                'opening_hours' => $defaultOpeningHours,
            ],
        ];

        // Create branches for each company
        foreach ($companies as $index => $company) {
            // Determine which branch templates to use based on company index
            $startIndex = $index * 2;
            $endIndex = $startIndex + 1;

            // Create branches for this company
            for ($i = $startIndex; $i <= $endIndex && $i < count($branchTemplates); $i++) {
                $branchData = $branchTemplates[$i];
                $branchData['user_id'] = $company->user_id;
                $branchData['company_id'] = $company->id;
                Branch::create($branchData);
            }
        }
    }
}
