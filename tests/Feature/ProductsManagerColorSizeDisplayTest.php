<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\ProductsManager;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductSize;
use App\Models\ProductColorSize;
use App\Models\Category;
use App\Models\Branch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class ProductsManagerColorSizeDisplayTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $productsManager;
    protected $company;
    protected $vendorUser;
    protected $product;
    protected $category;
    protected $branch;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a vendor user (company owner)
        $this->vendorUser = User::factory()->create([
            'role' => 'vendor',
            'email' => 'vendor@test.com',
            'registration_status' => 'active'
        ]);

        // Create a company
        $this->company = Company::factory()->create([
            'user_id' => $this->vendorUser->id,
            'name' => 'Test Company',
            'status' => 'active'
        ]);

        // Create a products manager user
        $productsManagerUser = User::factory()->create([
            'role' => 'products_manager',
            'email' => 'pr@gmail.com',
            'password' => bcrypt('Fifa2021'),
            'registration_status' => 'active'
        ]);

        // Create products manager record
        $this->productsManager = ProductsManager::create([
            'user_id' => $productsManagerUser->id,
            'company_id' => $this->company->id,
            'name' => 'Test Products Manager',
            'email' => 'pr@gmail.com',
            'phone' => '+971501234567'
        ]);

        // Create category and branch
        $this->category = Category::factory()->create(['name' => 'Test Category']);
        $this->branch = Branch::factory()->create([
            'company_id' => $this->company->id,
            'name' => 'Test Branch'
        ]);

        // Create a test product with colors and sizes
        $this->product = Product::factory()->create([
            'user_id' => $this->vendorUser->id,
            'category_id' => $this->category->id,
            'branch_id' => $this->branch->id,
            'name' => 'Test Product for Color-Size Display',
            'stock' => 100,
            'price' => 50.00
        ]);

        // Create color variants
        $color1 = ProductColor::factory()->create([
            'product_id' => $this->product->id,
            'name' => 'Red',
            'color_code' => '#FF0000',
            'stock' => 50
        ]);

        $color2 = ProductColor::factory()->create([
            'product_id' => $this->product->id,
            'name' => 'Blue',
            'color_code' => '#0000FF',
            'stock' => 50
        ]);

        // Create size variants
        $sizeM = ProductSize::factory()->create([
            'product_id' => $this->product->id,
            'name' => 'Medium',
            'value' => 'M',
            'stock' => 30
        ]);

        $sizeL = ProductSize::factory()->create([
            'product_id' => $this->product->id,
            'name' => 'Large',
            'value' => 'L',
            'stock' => 20
        ]);

        // Create color-size combinations
        ProductColorSize::factory()->create([
            'product_id' => $this->product->id,
            'product_color_id' => $color1->id,
            'product_size_id' => $sizeM->id,
            'stock' => 25
        ]);

        ProductColorSize::factory()->create([
            'product_id' => $this->product->id,
            'product_color_id' => $color1->id,
            'product_size_id' => $sizeL->id,
            'stock' => 15
        ]);

        ProductColorSize::factory()->create([
            'product_id' => $this->product->id,
            'product_color_id' => $color2->id,
            'product_size_id' => $sizeM->id,
            'stock' => 10
        ]);
    }

    /**
     * Test that Products Manager can access the product edit page
     */
    public function test_products_manager_can_access_product_edit_page()
    {
        $response = $this->actingAs($this->productsManager->user)
                         ->get("/products-manager/products/{$this->product->id}/edit");

        $response->assertStatus(200)
                 ->assertSee('Edit Product')
                 ->assertSee($this->product->name);
    }

    /**
     * Test that Products Manager can fetch sizes for a specific color via API
     */
    public function test_products_manager_can_fetch_sizes_for_color()
    {
        $color = $this->product->colors()->first();

        $response = $this->actingAs($this->productsManager->user)
                         ->postJson('/products-manager/api/color-sizes/get-sizes-for-color', [
                             'color_id' => $color->id,
                             'product_id' => $this->product->id
                         ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'sizes' => [
                         '*' => [
                             'id',
                             'name',
                             'value',
                             'stock',
                             'price_adjustment'
                         ]
                     ]
                 ]);

        $responseData = $response->json();
        $this->assertTrue($responseData['success']);
        $this->assertCount(2, $responseData['sizes']); // Red color should have 2 sizes (M and L)
    }

    /**
     * Test that different colors return different sizes
     */
    public function test_different_colors_return_different_sizes()
    {
        $redColor = $this->product->colors()->where('name', 'Red')->first();
        $blueColor = $this->product->colors()->where('name', 'Blue')->first();

        // Test Red color sizes
        $redResponse = $this->actingAs($this->productsManager->user)
                           ->postJson('/products-manager/api/color-sizes/get-sizes-for-color', [
                               'color_id' => $redColor->id,
                               'product_id' => $this->product->id
                           ]);

        // Test Blue color sizes
        $blueResponse = $this->actingAs($this->productsManager->user)
                            ->postJson('/products-manager/api/color-sizes/get-sizes-for-color', [
                                'color_id' => $blueColor->id,
                                'product_id' => $this->product->id
                            ]);

        $redResponse->assertStatus(200);
        $blueResponse->assertStatus(200);

        $redData = $redResponse->json();
        $blueData = $blueResponse->json();

        // Red should have 2 sizes (M and L)
        $this->assertCount(2, $redData['sizes']);
        
        // Blue should have 1 size (M only)
        $this->assertCount(1, $blueData['sizes']);
    }

    /**
     * Test that unauthorized users cannot access the API
     */
    public function test_unauthorized_users_cannot_access_color_size_api()
    {
        $color = $this->product->colors()->first();

        $response = $this->postJson('/products-manager/api/color-sizes/get-sizes-for-color', [
            'color_id' => $color->id,
            'product_id' => $this->product->id
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test that Products Manager cannot access products from other companies
     */
    public function test_products_manager_cannot_access_other_company_products()
    {
        // Create another company and product
        $otherVendor = User::factory()->create(['role' => 'vendor']);
        $otherCompany = Company::factory()->create(['user_id' => $otherVendor->id]);
        $otherProduct = Product::factory()->create([
            'user_id' => $otherVendor->id,
            'category_id' => $this->category->id,
            'branch_id' => Branch::factory()->create(['company_id' => $otherCompany->id])->id
        ]);

        $otherColor = ProductColor::factory()->create([
            'product_id' => $otherProduct->id,
            'name' => 'Green',
            'color_code' => '#00FF00'
        ]);

        $response = $this->actingAs($this->productsManager->user)
                         ->postJson('/products-manager/api/color-sizes/get-sizes-for-color', [
                             'color_id' => $otherColor->id,
                             'product_id' => $otherProduct->id
                         ]);

        $response->assertStatus(404); // Should not find the product
    }
}
