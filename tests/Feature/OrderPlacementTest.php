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

class OrderPlacementTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $branch;
    protected $product;

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
    }

    /** @test */
    public function it_can_place_an_order()
    {
        $this->actingAs($this->user);

        $response = $this->postJson('/api/orders', [
            'branch_id' => $this->branch->id,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 2,
                ],
            ],
            'address' => 'Test Address',
            'payment_method' => 'credit_card',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Order created successfully',
            ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'branch_id' => $this->branch->id,
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => 'credit_card',
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $this->product->id,
            'quantity' => 2,
            'price' => 100,
        ]);
    }
}
