<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BranchesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get vendor user
        $vendor = User::where('role', 'vendor')->first();

        // If no vendor, create one
        if (!$vendor) {
            $vendor = User::create([
                'name' => 'Vendor User',
                'email' => 'vendor@example.com',
                'password' => bcrypt('password123'),
                'role' => 'vendor',
                'status' => 'active',
            ]);
        }

        // Get or create a company for the vendor
        $company = Company::where('user_id', $vendor->id)->first();

        if (!$company) {
            $company = Company::create([
                'name' => 'Vendor Company',
                'user_id' => $vendor->id,
                'email' => 'info@vendorcompany.com',
                'phone' => '123-456-7890',
                'website' => 'https://vendorcompany.com',
                'description' => 'A sample vendor company',
                'logo' => null,
                'status' => 'active',
                'business_type' => 'service',
                'registration_number' => 'REG123456',
                'tax_id' => 'TAX123456',
                'address' => '123 Main St',
                'city' => 'Anytown',
                'state' => 'CA',
                'zip_code' => '12345',
                'country' => 'USA',
            ]);
        }

        // Create branches for the company
        $branchesData = [
            [
                'name' => 'Main Branch',
                'address' => '123 Main Street, Anytown, USA',
                'phone' => '123-456-7890',
                'email' => 'main@vendorcompany.com',
                'lat' => 40.7128,
                'lng' => -74.0060,
                'opening_hours' => json_encode([
                    'monday' => ['is_open' => true, 'open' => '09:00', 'close' => '17:00'],
                    'tuesday' => ['is_open' => true, 'open' => '09:00', 'close' => '17:00'],
                    'wednesday' => ['is_open' => true, 'open' => '09:00', 'close' => '17:00'],
                    'thursday' => ['is_open' => true, 'open' => '09:00', 'close' => '17:00'],
                    'friday' => ['is_open' => true, 'open' => '09:00', 'close' => '17:00'],
                    'saturday' => ['is_open' => false, 'open' => null, 'close' => null],
                    'sunday' => ['is_open' => false, 'open' => null, 'close' => null],
                ]),
            ],
            [
                'name' => 'Downtown Branch',
                'address' => '456 Downtown Avenue, Anytown, USA',
                'phone' => '123-456-7891',
                'email' => 'downtown@vendorcompany.com',
                'lat' => 40.7112,
                'lng' => -74.0055,
                'opening_hours' => json_encode([
                    'monday' => ['is_open' => true, 'open' => '10:00', 'close' => '18:00'],
                    'tuesday' => ['is_open' => true, 'open' => '10:00', 'close' => '18:00'],
                    'wednesday' => ['is_open' => true, 'open' => '10:00', 'close' => '18:00'],
                    'thursday' => ['is_open' => true, 'open' => '10:00', 'close' => '18:00'],
                    'friday' => ['is_open' => true, 'open' => '10:00', 'close' => '18:00'],
                    'saturday' => ['is_open' => true, 'open' => '10:00', 'close' => '15:00'],
                    'sunday' => ['is_open' => false, 'open' => null, 'close' => null],
                ]),
            ],
        ];

        foreach ($branchesData as $branchData) {
            Branch::create(array_merge($branchData, [
                'company_id' => $company->id,
                'user_id' => $vendor->id,
                'status' => 'active',
            ]));
        }
    }
}
