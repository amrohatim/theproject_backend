<?php

namespace App\Services;

use App\Models\PaymentMethod;
use App\Models\PaymentTransaction;
use App\Models\PayoutMethod;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentService
{
    /**
     * Create a new payment method.
     *
     * @param mixed $user
     * @param array $data
     * @return PaymentMethod
     */
    public function createPaymentMethod($user, array $data): PaymentMethod
    {
        DB::beginTransaction();

        try {
            // If this is the default payment method, unset any existing default
            if (isset($data['is_default']) && $data['is_default']) {
                $user->paymentMethods()->update(['is_default' => false]);
            }

            // Create the payment method
            $paymentMethod = new PaymentMethod($data);
            $paymentMethod->user_id = $user->id;
            $paymentMethod->save();

            DB::commit();

            return $paymentMethod;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating payment method: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'data' => $data,
                'exception' => $e
            ]);

            throw $e;
        }
    }

    /**
     * Update a payment method.
     *
     * @param PaymentMethod $paymentMethod
     * @param array $data
     * @return PaymentMethod
     */
    public function updatePaymentMethod(PaymentMethod $paymentMethod, array $data): PaymentMethod
    {
        DB::beginTransaction();

        try {
            $user = $paymentMethod->user;

            // If this is the default payment method, unset any existing default
            if (isset($data['is_default']) && $data['is_default'] && !$paymentMethod->is_default) {
                $user->paymentMethods()->where('id', '!=', $paymentMethod->id)->update(['is_default' => false]);
            }

            // Update the payment method
            $paymentMethod->fill($data);
            $paymentMethod->save();

            DB::commit();

            return $paymentMethod;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating payment method: ' . $e->getMessage(), [
                'payment_method_id' => $paymentMethod->id,
                'data' => $data,
                'exception' => $e
            ]);

            throw $e;
        }
    }

    /**
     * Delete a payment method.
     *
     * @param PaymentMethod $paymentMethod
     * @return bool
     */
    public function deletePaymentMethod(PaymentMethod $paymentMethod): bool
    {
        DB::beginTransaction();

        try {
            $user = $paymentMethod->user;

            // If this is the default payment method, find another one to make default
            if ($paymentMethod->is_default) {
                $newDefault = $user->paymentMethods()->where('id', '!=', $paymentMethod->id)->first();
                if ($newDefault) {
                    $newDefault->is_default = true;
                    $newDefault->save();
                }
            }

            // Delete the payment method
            $paymentMethod->delete();

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting payment method: ' . $e->getMessage(), [
                'payment_method_id' => $paymentMethod->id,
                'exception' => $e
            ]);

            throw $e;
        }
    }

    /**
     * Create a new payout method.
     *
     * @param mixed $user
     * @param array $data
     * @return PayoutMethod
     */
    public function createPayoutMethod($user, array $data): PayoutMethod
    {
        DB::beginTransaction();

        try {
            // If this is the default payout method, unset any existing default
            if (isset($data['is_default']) && $data['is_default']) {
                $user->payoutMethods()->update(['is_default' => false]);
            }

            // Create the payout method
            $payoutMethod = new PayoutMethod($data);
            $payoutMethod->user_id = $user->id;

            // Set last_four for bank accounts
            if (isset($data['account_number']) && $data['payout_type'] === 'bank_account') {
                $payoutMethod->last_four = substr($data['account_number'], -4);
            }

            $payoutMethod->save();

            DB::commit();

            return $payoutMethod;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating payout method: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'data' => $data,
                'exception' => $e
            ]);

            throw $e;
        }
    }

    /**
     * Create a payment transaction.
     *
     * @param mixed $user
     * @param array $data
     * @return PaymentTransaction
     */
    public function createTransaction($user, array $data): PaymentTransaction
    {
        DB::beginTransaction();

        try {
            // Generate a UUID for the transaction
            $data['transaction_uuid'] = (string) Str::uuid();
            $data['user_id'] = $user->id;

            // Set the net amount (amount - fee)
            if (!isset($data['net_amount']) && isset($data['amount']) && isset($data['fee'])) {
                $data['net_amount'] = $data['amount'] - $data['fee'];
            }

            // Set the processed_at timestamp if not provided
            if (!isset($data['processed_at'])) {
                $data['processed_at'] = Carbon::now();
            }

            // Create the transaction
            $transaction = new PaymentTransaction($data);
            $transaction->save();

            DB::commit();

            return $transaction;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating transaction: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'data' => $data,
                'exception' => $e
            ]);

            throw $e;
        }
    }

    /**
     * Process a payment.
     *
     * @param mixed $user
     * @param PaymentMethod $paymentMethod
     * @param float $amount
     * @param string $description
     * @param array $metadata
     * @return PaymentTransaction
     */
    public function processPayment($user, PaymentMethod $paymentMethod, float $amount, string $description, array $metadata = []): PaymentTransaction
    {
        // This is a placeholder for actual payment processing logic
        // In a real application, this would integrate with a payment gateway like Stripe or PayPal

        // For now, we'll just create a transaction record
        return $this->createTransaction($user, [
            'transaction_type' => 'payment',
            'status' => 'completed',
            'provider' => $paymentMethod->provider_type,
            'provider_transaction_id' => 'test_' . Str::random(10),
            'provider_status' => 'succeeded',
            'description' => $description,
            'amount' => $amount,
            'fee' => $amount * 0.029 + 0.30, // Example fee calculation (2.9% + $0.30)
            'currency' => 'USD',
            'payment_method_id' => $paymentMethod->id,
            'meta_data' => $metadata,
        ]);
    }
}
