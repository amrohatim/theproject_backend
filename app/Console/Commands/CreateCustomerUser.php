<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateCustomerUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-customer-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a customer user for the application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating customer user...');

        // Check if customer user already exists
        $customerExists = User::where('email', 'customer@example.com')->exists();

        if ($customerExists) {
            $this->info('Customer user already exists!');
            return;
        }

        // Create customer user
        $user = User::create([
            'name' => 'Customer User',
            'email' => 'customer@example.com',
            'password' => Hash::make('password123'),
            'role' => 'customer',
            'phone' => '+1234567891',
            'status' => 'active',
        ]);

        $this->info('Customer user created successfully!');
        $this->info('Email: customer@example.com');
        $this->info('Password: password123');
    }
}
