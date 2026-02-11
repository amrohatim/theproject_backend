<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get users with role 'customer'
        $customers = User::where('role', 'customer')->get();

        // If no customers, create one
        if ($customers->isEmpty()) {
            $customer = User::create([
                'name' => 'Test Customer',
                'email' => 'customer@example.com',
                'password' => bcrypt('password'),
                'role' => 'customer',
                'status' => 'active',
            ]);
            $customers = collect([$customer]);
        }

        // Get branches
        $branches = Branch::all();

        // If no branches, skip seeding
        if ($branches->isEmpty()) {
            $this->command->info('No branches found. Skipping order seeding.');
            return;
        }

        // Get products
        $products = Product::all();

        // If no products, skip seeding
        if ($products->isEmpty()) {
            $this->command->info('No products found. Skipping order seeding.');
            return;
        }

        // Create 10 orders
        for ($i = 0; $i < 10; $i++) {
            $customer = $customers->random();
            $branch = $branches->random();

            $order = Order::create([
                'user_id' => $customer->id,
                'order_number' => 'ORD-' . str_pad($i + 1, 6, '0', STR_PAD_LEFT),
                'total' => 0, // Will be calculated based on items
                'status' => ['pending', 'processing', 'shipped', 'delivered', 'cancelled'][rand(0, 4)],
                'payment_status' => ['pending', 'paid', 'failed', 'refunded'][rand(0, 3)],
                'payment_method' => ['credit_card', 'paypal', 'bank_transfer', 'cash_on_delivery'][rand(0, 3)],
                'shipping_address' => json_encode([
                    'address' => '123 Main St',
                    'city' => 'Anytown',
                    'state' => 'CA',
                    'zip' => '12345',
                    'country' => 'USA',
                ]),
                'billing_address' => json_encode([
                    'address' => '123 Main St',
                    'city' => 'Anytown',
                    'state' => 'CA',
                    'zip' => '12345',
                    'country' => 'USA',
                ]),
                'notes' => 'Test order ' . ($i + 1),
            ]);

            // Add 1-3 items to the order
            $orderTotal = 0;
            $numItems = rand(1, 3);
            $orderProducts = $products->random($numItems);

            foreach ($orderProducts as $product) {
                $quantity = rand(1, 5);
                $price = $product->price;
                $total = $price * $quantity;
                $orderTotal += $total;

                // Get the vendor (company) for this product
                $branch = Branch::find($product->branch_id);
                $vendorId = $branch ? $branch->company_id : null;

                try {
                    // First try with all discount-related fields
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'vendor_id' => $vendorId, // Add vendor_id
                        'quantity' => $quantity,
                        'price' => $price,
                        'original_price' => $product->original_price ?? $price,
                        'discount_percentage' => 0,
                        'discount_amount' => 0,
                        'total' => $total,
                        'applied_deal_id' => null,
                        'status' => 'pending',
                        'specifications' => [],
                    ]);
                } catch (\Exception $columnException) {
                    // If we get a column not found error, try without the discount-related fields
                    if (strpos($columnException->getMessage(), 'Unknown column') !== false) {
                        \Illuminate\Support\Facades\Log::warning('Discount columns not found in order_items table. Creating order item without discount fields.');

                        OrderItem::create([
                            'order_id' => $order->id,
                            'product_id' => $product->id,
                            'vendor_id' => $vendorId, // Add vendor_id
                            'quantity' => $quantity,
                            'price' => $price,
                            'total' => $total,
                        ]);
                    } else {
                        // If it's a different error, rethrow it
                        throw $columnException;
                    }
                }
            }

            // Update order total
            $order->update(['total' => $orderTotal]);
        }
    }
}
