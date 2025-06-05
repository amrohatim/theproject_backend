<?php

namespace App\Console\Commands;

use App\Models\Branch;
use App\Models\Company;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateBranch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-branch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a branch for testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating branch...');

        // Get vendor user
        $vendor = User::where('email', 'vendor@example.com')->first();
        if (!$vendor) {
            $this->error('Vendor user not found! Please run app:create-vendor-user first.');
            return;
        }

        // Create company if it doesn't exist
        $company = Company::where('user_id', $vendor->id)->first();
        if (!$company) {
            $company = Company::create([
                'user_id' => $vendor->id,
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
            $this->info('Company created: ' . $company->name);
        } else {
            $this->info('Company already exists: ' . $company->name);
        }

        // Create branch
        $branch = Branch::where('user_id', $vendor->id)->first();
        if (!$branch) {
            $branch = Branch::create([
                'user_id' => $vendor->id,
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
            $this->info('Branch created: ' . $branch->name);
        } else {
            $this->info('Branch already exists: ' . $branch->name);
        }
    }
}
