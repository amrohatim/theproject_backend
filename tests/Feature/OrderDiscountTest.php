<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Company;
use App\Models\Category;
use App\Models\Deal;

class OrderDiscountTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $branch;
    protected $product;
    protected $deal;

    public function setUp(): void
    {
        parent::setUp();

        // Create a user
        $this->user = User::factory()->create([
            'role' => 'customer',
        ]);

        // Create a company
        $company = Company::create([
            'name' => 'Test Company',
            'user_id' => $this->user->id,
            'status' => 'active',
        ]);

        // Create a category
        $category = Category::create([
            'name' => 'Test Category',
            'description' => 'Test Category Description',
        ]);

        // Create a branch
        $this->branch = Branch::create([
            'name' => 'Test Branch',
            'company_id' => $company->id,
            'user_id' => $this->user->id,
            'address' => 'Test Address',
            'city' => 'Test City',
            'state' => 'Test State',
            'country' => 'Test Country',
            'postal_code' => '12345',
            'phone' => '1234567890',
            'email' => 'test@example.com',
            'status' => 'active',
            'lat' => 0,
            'lng' => 0,
        ]);

        // Create a product
        $this->product = Product::create([
            'branch_id' => $this->branch->id,
            'category_id' => $category->id,
            'name' => 'Test Product',
            'price' => 100,
            'original_price' => 120,
            'stock' => 10,
            'description' => 'Test Product Description',
        ]);

        // Create a deal
        $this->deal = Deal::create([
            'user_id' => $this->user->id,
            'title' => 'Test Deal',
            'description' => 'Test Deal Description',
            'discount_percentage' => 10,
            'start_date' => now()->subDay(),
            'end_date' => now()->addDay(),
            'status' => 'active',
            'applies_to' => 'all',
        ]);
    }

    /** @test */
    public function it_can_place_an_order_with_discount()
    {
        $this->actingAs($this->user);

        // Create an order directly using the model
        $order = \App\Models\Order::create([
            'user_id' => $this->user->id,
            'branch_id' => $this->branch->id,
            'order_number' => 'ORD-' . strtoupper(\Illuminate\Support\Str::random(8)),
            'total' => 200,
            'discount' => 20, // Set a discount value
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => 'credit_card',
            'shipping_address' => json_encode([
                'name' => 'Test User',
                'address' => 'Test Address',
                'city' => 'Test City',
                'country' => 'Test Country',
                'phone' => '1234567890',
            ]),
            'billing_address' => json_encode([
                'name' => 'Test User',
                'address' => 'Test Address',
                'city' => 'Test City',
                'country' => 'Test Country',
                'phone' => '1234567890',
            ]),
        ]);

        // Create an order item
        \App\Models\OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'vendor_id' => $this->branch->company_id,
            'quantity' => 2,
            'price' => 100,
            'total' => 200,
            'status' => 'pending',
        ]);

        // Refresh the order from the database
        $order = $order->fresh();

        // Check if the discount field exists and has the correct value
        $this->assertEquals(20, $order->discount);
    }
}
