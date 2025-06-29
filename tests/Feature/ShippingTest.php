<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\ShippingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ShippingTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test that a vendor can be marked as able to deliver.
     *
     * @return void
     */
    public function test_vendor_can_deliver_flag()
    {
        // Create a vendor company
        $company = Company::factory()->create([
            'can_deliver' => true,
        ]);

        $this->assertTrue($company->can_deliver);

        // Update to not deliver
        $company->can_deliver = false;
        $company->save();

        $this->assertFalse($company->can_deliver);
    }

    /**
     * Test that an order can be assigned vendor delivery.
     *
     * @return void
     */
    public function test_order_can_be_assigned_vendor_delivery()
    {
        // Create a user
        $user = User::factory()->create();

        // Create a vendor company that can deliver
        $company = Company::factory()->create([
            'can_deliver' => true,
        ]);

        // Create a branch for the company
        $branch = Branch::factory()->create([
            'company_id' => $company->id,
        ]);

        // Create a product for the branch
        $product = Product::factory()->create([
            'branch_id' => $branch->id,
        ]);

        // Create an order
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'branch_id' => $branch->id,
        ]);

        // Create order items
        $order->items()->create([
            'product_id' => $product->id,
            'vendor_id' => $company->id,
            'quantity' => 1,
            'price' => $product->price,
            'total' => $product->price,
        ]);

        // Assign delivery
        $shippingService = new ShippingService();
        $result = $shippingService->assignDelivery($order);

        $this->assertTrue($result);
        $this->assertEquals('vendor', $order->shipping_method);
        $this->assertEquals('pending', $order->shipping_status);
    }

    /**
     * Test that an order can be assigned Aramex delivery.
     *
     * @return void
     */
    public function test_order_can_be_assigned_aramex_delivery()
    {
        // Create a user
        $user = User::factory()->create();

        // Create a vendor company that cannot deliver
        $company = Company::factory()->create([
            'can_deliver' => false,
        ]);

        // Create a branch for the company
        $branch = Branch::factory()->create([
            'company_id' => $company->id,
        ]);

        // Create a product for the branch
        $product = Product::factory()->create([
            'branch_id' => $branch->id,
        ]);

        // Create an order
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'branch_id' => $branch->id,
        ]);

        // Create order items
        $order->items()->create([
            'product_id' => $product->id,
            'vendor_id' => $company->id,
            'quantity' => 1,
            'price' => $product->price,
            'total' => $product->price,
        ]);

        // Assign delivery
        $shippingService = new ShippingService();
        $result = $shippingService->assignDelivery($order);

        $this->assertTrue($result);
        $this->assertEquals('aramex', $order->shipping_method);
        $this->assertEquals('pending', $order->shipping_status);
        
        // Check that a shipment was created
        $this->assertNotNull($order->shipment);
        $this->assertEquals('pending', $order->shipment->status);
    }

    /**
     * Test that shipping status can be updated.
     *
     * @return void
     */
    public function test_shipping_status_can_be_updated()
    {
        // Create a user
        $user = User::factory()->create();

        // Create a vendor company
        $company = Company::factory()->create();

        // Create a branch for the company
        $branch = Branch::factory()->create([
            'company_id' => $company->id,
        ]);

        // Create an order
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'branch_id' => $branch->id,
            'shipping_method' => 'vendor',
            'shipping_status' => 'pending',
        ]);

        // Update shipping status
        $shippingService = new ShippingService();
        $result = $shippingService->updateShippingStatus($order, 'shipped');

        $this->assertTrue($result);
        $this->assertEquals('shipped', $order->shipping_status);
    }
}
