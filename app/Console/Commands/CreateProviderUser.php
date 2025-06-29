<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateProviderUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:provider';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a provider user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Create admin user if it doesn't exist
        if (!User::where('email', 'admin@example.com')->exists()) {
            User::create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '1234567890',
                'status' => 'active',
                'email_verified_at' => now(),
            ]);
            $this->info('Admin user created.');
        } else {
            $this->info('Admin user already exists.');
        }

        // Create provider user if it doesn't exist
        if (!User::where('email', 'provider@example.com')->exists()) {
            User::create([
                'name' => 'Provider User',
                'email' => 'provider@example.com',
                'password' => Hash::make('password'),
                'role' => 'provider',
                'phone' => '1234567891',
                'status' => 'active',
                'email_verified_at' => now(),
            ]);
            $this->info('Provider user created.');
        } else {
            $this->info('Provider user already exists.');
        }

        // Create customer user if it doesn't exist
        if (!User::where('email', 'customer@example.com')->exists()) {
            User::create([
                'name' => 'Customer User',
                'email' => 'customer@example.com',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'phone' => '1234567892',
                'status' => 'active',
                'email_verified_at' => now(),
            ]);
            $this->info('Customer user created.');
        } else {
            $this->info('Customer user already exists.');
        }

        $this->info('Done.');
    }
}
