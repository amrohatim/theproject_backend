<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\PaymentMethod;
use App\Models\PayoutMethod;
use App\Models\PayoutPreference;
use App\Models\PaymentTransaction;

class PaymentSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get vendor users
        $vendors = User::where('role', 'vendor')->get();

        foreach ($vendors as $vendor) {
            // Create payment methods
            $this->createPaymentMethods($vendor);

            // Create payout methods
            $this->createPayoutMethods($vendor);

            // Create payout preference
            $this->createPayoutPreference($vendor);

            // Create payment transactions
            $this->createPaymentTransactions($vendor);
        }
    }

    /**
     * Create payment methods for a user.
     */
    private function createPaymentMethods(User $user): void
    {
        // Create a credit card payment method
        PaymentMethod::create([
            'user_id' => $user->id,
            'type' => 'credit_card',
            'name' => 'My Visa Card',
            'card_type' => 'visa',
            'last_four' => '4242',
            'expiry_month' => '12',
            'expiry_year' => date('Y') + 2,
            'is_default' => true,
        ]);

        // Create a PayPal payment method
        PaymentMethod::create([
            'user_id' => $user->id,
            'type' => 'paypal',
            'name' => 'My PayPal Account',
            'email' => $user->email,
            'is_default' => false,
        ]);
    }

    /**
     * Create payout methods for a user.
     */
    private function createPayoutMethods(User $user): void
    {
        // Create a bank account payout method
        PayoutMethod::create([
            'user_id' => $user->id,
            'type' => 'bank_account',
            'name' => 'My Bank Account',
            'bank_name' => 'Chase Bank',
            'account_number' => '123456789',
            'last_four' => '6789',
            'routing_number' => '987654321',
            'account_type' => 'checking',
            'is_default' => true,
        ]);

        // Create a PayPal payout method
        PayoutMethod::create([
            'user_id' => $user->id,
            'type' => 'paypal',
            'name' => 'My PayPal Account',
            'email' => $user->email,
            'is_default' => false,
        ]);
    }

    /**
     * Create payout preference for a user.
     */
    private function createPayoutPreference(User $user): void
    {
        $defaultPayoutMethod = $user->payoutMethods()->where('is_default', true)->first();

        PayoutPreference::create([
            'user_id' => $user->id,
            'payout_frequency' => 'weekly',
            'minimum_payout_amount' => 50.00,
            'currency' => 'USD',
            'default_payout_method_id' => $defaultPayoutMethod ? $defaultPayoutMethod->id : null,
        ]);
    }

    /**
     * Create payment transactions for a user.
     */
    private function createPaymentTransactions(User $user): void
    {
        $statuses = ['completed', 'pending', 'failed'];
        $types = ['payment', 'payout', 'refund'];
        $paymentMethod = $user->paymentMethods()->first();
        $payoutMethod = $user->payoutMethods()->first();

        // Create 5 random transactions
        for ($i = 0; $i < 5; $i++) {
            $type = $types[array_rand($types)];
            $status = $statuses[array_rand($statuses)];
            $amount = rand(10, 1000) / 10;

            PaymentTransaction::create([
                'user_id' => $user->id,
                'transaction_type' => $type,
                'description' => ucfirst($type) . ' transaction #' . rand(1000, 9999),
                'amount' => $amount,
                'currency' => 'USD',
                'status' => $status,
                'transaction_id' => 'txn_' . uniqid(),
                'payment_method_id' => $type === 'payment' ? $paymentMethod->id : null,
                'payout_method_id' => $type === 'payout' ? $payoutMethod->id : null,
                'created_at' => now()->subDays(rand(1, 30)),
            ]);
        }
    }
}
