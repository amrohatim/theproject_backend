<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Branch;
use App\Models\Product;
use Carbon\Carbon;

class OrdersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get customer users
        $customers = User::where('role', 'customer')->get();

        // Get all branches
        $branches = Branch::all();

        // Get all products
        $products = Product::all();

        // Order statuses
        $statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

        // Payment statuses
        $paymentStatuses = ['pending', 'paid', 'failed', 'refunded'];

        // Payment methods
        $paymentMethods = ['credit_card', 'paypal', 'cash_on_delivery'];

        // Create orders for each customer
        foreach ($customers as $customer) {
            // Create 2-5 orders per customer
            $numOrders = rand(2, 5);

            for ($i = 0; $i < $numOrders; $i++) {
                // Select a random branch
                $branch = $branches->random();

                // Generate a random order date within the last 3 months
                $orderDate = Carbon::now()->subDays(rand(1, 90));

                // Generate a unique order number
                $orderNumber = 'ORD-' . strtoupper(substr(uniqid(), -6)) . '-' . date('Ymd');

                // Select a random status
                $status = $statuses[array_rand($statuses)];

                // If order is delivered, payment status is paid
                // If order is cancelled, payment status is refunded
                // Otherwise, randomly select a payment status
                if ($status === 'delivered') {
                    $paymentStatus = 'paid';
                } elseif ($status === 'cancelled') {
                    $paymentStatus = 'refunded';
                } else {
                    $paymentStatus = $paymentStatuses[array_rand($paymentStatuses)];
                }

                // Select a random payment method
                $paymentMethod = $paymentMethods[array_rand($paymentMethods)];

                // Create a shipping address
                $shippingAddress = [
                    'name' => $customer->name,
                    'address_line1' => rand(100, 999) . ' ' . $this->getRandomStreetName(),
                    'city' => $this->getRandomCity(),
                    'state' => $this->getRandomState(),
                    'postal_code' => rand(10000, 99999),
                    'country' => 'USA',
                    'phone' => $customer->phone,
                ];

                // Create a billing address (same as shipping for simplicity)
                $billingAddress = $shippingAddress;

                // Create the order
                $order = Order::create([
                    'user_id' => $customer->id,
                    'order_number' => $orderNumber,
                    'total' => 0, // Will be updated after adding items
                    'status' => $status,
                    'payment_status' => $paymentStatus,
                    'payment_method' => $paymentMethod,
                    'shipping_address' => $shippingAddress,
                    'billing_address' => $billingAddress,
                    'notes' => $this->getRandomOrderNote(),
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate,
                ]);

                // Add 1-5 items to the order
                $numItems = rand(1, 5);
                $orderTotal = 0;

                // Get random products from the branch
                $branchProducts = $products->where('branch_id', $branch->id);

                // If branch has no products, use random products
                if ($branchProducts->isEmpty()) {
                    $branchProducts = $products->random(min($numItems, $products->count()));
                }

                // Ensure we have enough products
                $numItems = min($numItems, $branchProducts->count());

                // Add items to the order
                for ($j = 0; $j < $numItems; $j++) {
                    // Select a random product
                    $product = $branchProducts->random();

                    // Determine quantity
                    $quantity = rand(1, 3);

                    // Calculate item total
                    $itemTotal = $product->price * $quantity;

                    // Add to order total
                    $orderTotal += $itemTotal;

                    // Get the vendor (company) for this product
                    $vendorId = $product->branch->company_id;

                    // Create the order item
                    try {
                        // First try with all discount-related fields
                        OrderItem::create([
                            'order_id' => $order->id,
                            'product_id' => $product->id,
                            'vendor_id' => $vendorId, // Add vendor_id
                            'quantity' => $quantity,
                            'price' => $product->price,
                            'original_price' => $product->original_price ?? $product->price,
                            'discount_percentage' => 0,
                            'discount_amount' => 0,
                            'total' => $itemTotal,
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
                                'price' => $product->price,
                                'total' => $itemTotal,
                            ]);
                        } else {
                            // If it's a different error, rethrow it
                            throw $columnException;
                        }
                    }
                }

                // Update the order total
                $order->update(['total' => $orderTotal]);
            }
        }
    }

    /**
     * Get a random street name.
     */
    private function getRandomStreetName(): string
    {
        $streets = [
            'Main Street', 'Oak Avenue', 'Maple Drive', 'Cedar Lane', 'Pine Road',
            'Elm Street', 'Washington Avenue', 'Park Place', 'Lake View Drive', 'Sunset Boulevard',
            'Highland Avenue', 'River Road', 'Mountain View Drive', 'Spring Street', 'Willow Lane',
        ];

        return $streets[array_rand($streets)];
    }

    /**
     * Get a random city name.
     */
    private function getRandomCity(): string
    {
        $cities = [
            'New York', 'Los Angeles', 'Chicago', 'Houston', 'Phoenix',
            'Philadelphia', 'San Antonio', 'San Diego', 'Dallas', 'San Jose',
            'Austin', 'Jacksonville', 'Fort Worth', 'Columbus', 'San Francisco',
        ];

        return $cities[array_rand($cities)];
    }

    /**
     * Get a random state.
     */
    private function getRandomState(): string
    {
        $states = [
            'AL', 'AK', 'AZ', 'AR', 'CA', 'CO', 'CT', 'DE', 'FL', 'GA',
            'HI', 'ID', 'IL', 'IN', 'IA', 'KS', 'KY', 'LA', 'ME', 'MD',
            'MA', 'MI', 'MN', 'MS', 'MO', 'MT', 'NE', 'NV', 'NH', 'NJ',
            'NM', 'NY', 'NC', 'ND', 'OH', 'OK', 'OR', 'PA', 'RI', 'SC',
            'SD', 'TN', 'TX', 'UT', 'VT', 'VA', 'WA', 'WV', 'WI', 'WY',
        ];

        return $states[array_rand($states)];
    }

    /**
     * Get a random order note.
     */
    private function getRandomOrderNote(): string
    {
        $notes = [
            'Please leave the package at the front door.',
            'Call before delivery.',
            'Please deliver after 5 PM.',
            'The doorbell is broken, please knock.',
            'Fragile items, handle with care.',
            'No signature required.',
            'Please deliver to the back entrance.',
            '',
            '',
            '',
        ];

        return $notes[array_rand($notes)];
    }
}
