<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Company;
use App\Models\Branch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateVendorUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-vendor-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a vendor user with company and branch';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating vendor user...');

        // Check if vendor user already exists
        $vendorExists = User::where('email', 'vendor@example.com')->exists();

        if ($vendorExists) {
            $this->info('Vendor user already exists!');
            return;
        }

        // Create vendor user
        $user = User::create([
            'name' => 'Vendor User',
            'email' => 'vendor@example.com',
            'password' => Hash::make('password123'),
            'role' => 'vendor',
            'phone' => '+1234567890',
            'status' => 'active',
        ]);

        $this->info('Vendor user created successfully!');
        $this->info('Email: vendor@example.com');
        $this->info('Password: password123');

        // Create company for vendor
        $company = Company::create([
            'user_id' => $user->id,
            'name' => 'Beauty & Wellness Co.',
            'description' => 'Premium beauty and wellness products and services',
            'email' => 'contact@example.com',
            'phone' => '+1234567890',
            'address' => '123 Main St',
            'city' => 'New York',
            'state' => 'NY',
            'zip_code' => '10001',
            'country' => 'USA',
            'status' => 'active',
        ]);

        $this->info('Company created for vendor: ' . $company->name);

        // Create branch for vendor
        $branch = Branch::create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'name' => 'Beauty Salon',
            'address' => '123 Main St, New York, NY 10001',
            'lat' => 40.7128,
            'lng' => -74.0060,
            'phone' => '+1234567890',
            'email' => 'salon@example.com',
            'description' => 'Our flagship beauty salon',
            'status' => 'active',
        ]);

        $this->info('Branch created for vendor: ' . $branch->name);
    }
}
