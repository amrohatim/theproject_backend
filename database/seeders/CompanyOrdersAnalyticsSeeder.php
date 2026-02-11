<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CompanyOrdersAnalyticsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = User::where('role', 'customer')->get();

        if ($customers->isEmpty()) {
            $customer = User::create([
                'name' => 'Analytics Customer',
                'email' => 'analytics.customer@example.com',
                'password' => bcrypt('password'),
                'role' => 'customer',
                'status' => 'active',
            ]);
            $customers = collect([$customer]);
        }

        $products = Product::with(['branch.company'])
            ->get()
            ->filter(function ($product) {
                return $product->branch && $product->branch->company_id;
            })
            ->values();

        if ($products->isEmpty()) {
            $this->command->info('No company-linked products found. Skipping analytics orders seeding.');
            return;
        }

        $orderSequence = 1;
        $statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        $paymentStatuses = ['pending', 'paid', 'failed', 'refunded'];
        $paymentMethods = ['credit_card', 'paypal', 'cash_on_delivery', 'bank_transfer'];
        $hasVendorId = Schema::hasColumn('order_items', 'vendor_id');
        $hasBranchId = Schema::hasColumn('order_items', 'branch_id');
        $hasOriginalPrice = Schema::hasColumn('order_items', 'original_price');
        $hasDiscountPercentage = Schema::hasColumn('order_items', 'discount_percentage');
        $hasDiscountAmount = Schema::hasColumn('order_items', 'discount_amount');
        $hasAppliedDealId = Schema::hasColumn('order_items', 'applied_deal_id');
        $hasStatus = Schema::hasColumn('order_items', 'status');
        $hasSpecifications = Schema::hasColumn('order_items', 'specifications');

        $hotCount = max(1, (int) ceil($products->count() * 0.25));
        $hotProducts = $products->count() <= $hotCount ? $products : $products->random($hotCount);
        $hotProductIds = $hotProducts->pluck('id')->flip();

        $purchaseQueue = [];
        foreach ($products as $product) {
            $purchaseCount = $hotProductIds->has($product->id) ? rand(3, 8) : rand(1, 2);
            for ($i = 0; $i < $purchaseCount; $i++) {
                $purchaseQueue[] = $product->id;
            }
        }

        shuffle($purchaseQueue);
        $productsById = $products->keyBy('id');
        $minItemsPerOrder = count($purchaseQueue) > 1 ? 2 : 1;
        $maxItemsPerOrder = 5;

        while (!empty($purchaseQueue)) {
            $targetItems = min(rand($minItemsPerOrder, $maxItemsPerOrder), count($purchaseQueue));
            $orderProducts = [];
            $usedCompanies = [];

            for ($i = 0; $i < $targetItems && !empty($purchaseQueue); $i++) {
                $index = $this->pickProductIndex($purchaseQueue, $productsById, $usedCompanies);
                $productId = $purchaseQueue[$index];
                array_splice($purchaseQueue, $index, 1);

                $product = $productsById->get($productId);
                if (!$product) {
                    continue;
                }

                $orderProducts[] = $product;
                $companyId = $product->branch->company_id;
                $usedCompanies[$companyId] = true;
            }

            if (empty($orderProducts)) {
                continue;
            }

            $customer = $customers->random();
            $orderDate = Carbon::now()->subDays(rand(1, 120));

            $status = $statuses[array_rand($statuses)];
            if ($status === 'delivered') {
                $paymentStatus = 'paid';
            } elseif ($status === 'cancelled') {
                $paymentStatus = 'refunded';
            } else {
                $paymentStatus = $paymentStatuses[array_rand($paymentStatuses)];
            }

            $order = Order::create([
                'user_id' => $customer->id,
                'order_number' => $this->makeOrderNumber($orderSequence++),
                'total' => 0,
                'status' => $status,
                'payment_status' => $paymentStatus,
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'shipping_address' => $this->makeAddress($customer),
                'billing_address' => $this->makeAddress($customer),
                'notes' => 'Analytics seed order (multi-vendor)',
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ]);

            $orderTotal = 0;

            foreach ($orderProducts as $product) {
                $quantity = rand(1, 4);
                $price = (float) ($product->price ?? 0);
                $itemTotal = $price * $quantity;
                $orderTotal += $itemTotal;

                $itemData = [
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'total' => $itemTotal,
                ];

                if ($hasVendorId) {
                    $itemData['vendor_id'] = $product->branch->company_id;
                }

                if ($hasOriginalPrice) {
                    $itemData['original_price'] = $product->original_price ?? $price;
                }

                if ($hasDiscountPercentage) {
                    $itemData['discount_percentage'] = 0;
                }

                if ($hasDiscountAmount) {
                    $itemData['discount_amount'] = 0;
                }

                if ($hasAppliedDealId) {
                    $itemData['applied_deal_id'] = null;
                }

                if ($hasStatus) {
                    $itemData['status'] = 'pending';
                }

                if ($hasSpecifications) {
                    $itemData['specifications'] = [];
                }

                $orderItem = OrderItem::create($itemData);

                if ($hasBranchId) {
                    $orderItem->branch_id = $product->branch_id;
                    $orderItem->save();
                }
            }

            $order->update(['total' => $orderTotal]);
        }
    }

    private function pickProductIndex(array $queue, $productsById, array $usedCompanies): int
    {
        foreach ($queue as $index => $productId) {
            $product = $productsById->get($productId);
            if (!$product || !$product->branch) {
                continue;
            }

            $companyId = $product->branch->company_id;
            if (!isset($usedCompanies[$companyId])) {
                return $index;
            }
        }

        return 0;
    }

    private function makeOrderNumber(int $sequence): string
    {
        return 'ORD-' . strtoupper(Str::random(6)) . '-' . str_pad((string) $sequence, 6, '0', STR_PAD_LEFT);
    }

    private function makeAddress(User $customer): array
    {
        return [
            'name' => $customer->name,
            'address_line1' => rand(10, 999) . ' Market Street',
            'city' => 'Dubai',
            'state' => 'DU',
            'postal_code' => (string) rand(10000, 99999),
            'country' => 'UAE',
            'phone' => $customer->phone,
        ];
    }
}
