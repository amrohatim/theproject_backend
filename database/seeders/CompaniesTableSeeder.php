<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\User;
use App\Helpers\UnsplashImageHelper;

class CompaniesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get vendor users
        $vendors = User::where('role', 'vendor')->get();

        // Company data
        $companies = [
            [
                'name' => 'Tech Solutions Inc.',
                'description' => 'Leading provider of technology solutions for businesses of all sizes.',
                'logo' => UnsplashImageHelper::downloadAndSaveImage('tech company logo', 'public/images/companies/tech-solutions.jpg', 400, 400),
                'website' => 'https://techsolutions.example.com',
                'email' => 'info@techsolutions.example.com',
                'phone' => '1234567890',
                'address' => '123 Tech Street',
                'city' => 'San Francisco',
                'state' => 'CA',
                'zip_code' => '94105',
                'country' => 'USA',
                'tax_id' => 'TS-12345',
                'status' => 'active',
                'business_type' => 'Technology',
                'registration_number' => 'REG-TS-2023',
            ],
            [
                'name' => 'Wellness Center',
                'description' => 'Comprehensive wellness services for mind and body.',
                'logo' => UnsplashImageHelper::downloadAndSaveImage('wellness spa logo', 'public/images/companies/wellness-center.jpg', 400, 400),
                'website' => 'https://wellness.example.com',
                'email' => 'info@wellness.example.com',
                'phone' => '2345678901',
                'address' => '456 Health Avenue',
                'city' => 'Los Angeles',
                'state' => 'CA',
                'zip_code' => '90001',
                'country' => 'USA',
                'tax_id' => 'WC-67890',
                'status' => 'active',
                'business_type' => 'Health & Wellness',
                'registration_number' => 'REG-WC-2023',
            ],
            [
                'name' => 'Gourmet Delights',
                'description' => 'Premium food products and catering services.',
                'logo' => UnsplashImageHelper::downloadAndSaveImage('gourmet food logo', 'public/images/companies/gourmet-delights.jpg', 400, 400),
                'website' => 'https://gourmet.example.com',
                'email' => 'info@gourmet.example.com',
                'phone' => '3456789012',
                'address' => '789 Culinary Blvd',
                'city' => 'New York',
                'state' => 'NY',
                'zip_code' => '10001',
                'country' => 'USA',
                'tax_id' => 'GD-24680',
                'status' => 'active',
                'business_type' => 'Food & Beverage',
                'registration_number' => 'REG-GD-2023',
            ],
        ];

        // Create companies for each vendor
        foreach ($vendors as $index => $vendor) {
            if (isset($companies[$index])) {
                $companyData = $companies[$index];
                $companyData['user_id'] = $vendor->id;
                Company::create($companyData);
            }
        }
    }
}
