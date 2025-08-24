<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Company;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductColor;
use App\Models\ProductSpecification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendorProductEditUITest extends TestCase
{
    use RefreshDatabase;

    protected $vendor;
    protected $company;
    protected $branch;
    protected $category;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupTestData();
    }

    protected function setupTestData()
    {
        // Create vendor user
        $this->vendor = User::factory()->create([
            'email' => 'vendor@test.com',
            'password' => bcrypt('password'),
            'user_type' => 'vendor',
            'registration_status' => 'active'
        ]);

        // Create company
        $this->company = Company::factory()->create([
            'user_id' => $this->vendor->id,
            'name' => 'Test Company',
            'status' => 'active'
        ]);

        // Create branch
        $this->branch = Branch::factory()->create([
            'company_id' => $this->company->id,
            'name' => 'Test Branch',
            'status' => 'active'
        ]);

        // Create category
        $this->category = Category::factory()->create([
            'name' => 'Test Category',
            'type' => 'product'
        ]);

        // Create product
        $this->product = Product::factory()->create([
            'user_id' => $this->vendor->id,
            'branch_id' => $this->branch->id,
            'category_id' => $this->category->id,
            'name' => 'Test Product',
            'description' => 'Test product description',
            'price' => 100.00,
            'original_price' => 120.00,
            'stock' => 50,
            'is_available' => true
        ]);

        // Create product colors
        ProductColor::factory()->create([
            'product_id' => $this->product->id,
            'color_name' => 'Red',
            'color_code' => '#FF0000',
            'stock' => 20,
            'is_default' => true
        ]);

        ProductColor::factory()->create([
            'product_id' => $this->product->id,
            'color_name' => 'Blue',
            'color_code' => '#0000FF',
            'stock' => 30,
            'is_default' => false
        ]);

        // Create product specifications
        ProductSpecification::factory()->create([
            'product_id' => $this->product->id,
            'name' => 'Material',
            'value' => 'Cotton'
        ]);
    }

    /**
     * Test that vendor product edit page loads with correct layout
     */
    public function test_vendor_product_edit_page_loads_with_correct_layout()
    {
        $response = $this->actingAs($this->vendor)
                         ->get("/vendor/products/{$this->product->id}/edit");

        $response->assertStatus(200)
                 ->assertSee('Edit Product')
                 ->assertSee('vue-app-container') // Verify Tailwind-based layout
                 ->assertSee('vendor-product-edit-app') // Verify Vue app container
                 ->assertSee($this->product->name)
                 ->assertViewIs('vendor.products.edit-vue');
    }

    /**
     * Test that edit page uses dashboard layout (not vendor layout)
     */
    public function test_edit_page_uses_dashboard_layout()
    {
        $response = $this->actingAs($this->vendor)
                         ->get("/vendor/products/{$this->product->id}/edit");

        // Check that it extends layouts.dashboard
        $response->assertSee('Dala3Chic Admin'); // Dashboard layout title format
        
        // Check for Tailwind CSS classes (dashboard layout)
        $response->assertSee('vue-app-container')
                 ->assertSee('container mx-auto'); // Tailwind container classes
    }

    /**
     * Test that product data is correctly passed to Vue component
     */
    public function test_product_data_is_correctly_passed_to_vue_component()
    {
        $response = $this->actingAs($this->vendor)
                         ->get("/vendor/products/{$this->product->id}/edit");

        $response->assertStatus(200)
                 ->assertSee('data-product-id="' . $this->product->id . '"')
                 ->assertSee('data-back-url');
    }

    /**
     * Test product update functionality
     */
    public function test_product_update_functionality()
    {
        $updateData = [
            'name' => 'Updated Test Product',
            'description' => 'Updated description',
            'price' => 150.00,
            'original_price' => 180.00,
            'stock' => 75,
            'category_id' => $this->category->id,
            'branch_id' => $this->branch->id,
            'is_available' => true
        ];

        $response = $this->actingAs($this->vendor)
                         ->putJson("/vendor/products/{$this->product->id}", $updateData);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Product updated successfully!'
                 ]);

        // Verify product was updated in database
        $this->assertDatabaseHas('products', [
            'id' => $this->product->id,
            'name' => 'Updated Test Product',
            'price' => 150.00,
            'stock' => 75
        ]);
    }

    /**
     * Test that vendor can only edit their own products
     */
    public function test_vendor_can_only_edit_own_products()
    {
        // Create another vendor and product
        $otherVendor = User::factory()->create([
            'user_type' => 'vendor',
            'registration_status' => 'active'
        ]);

        $otherCompany = Company::factory()->create([
            'user_id' => $otherVendor->id
        ]);

        $otherBranch = Branch::factory()->create([
            'company_id' => $otherCompany->id
        ]);

        $otherProduct = Product::factory()->create([
            'user_id' => $otherVendor->id,
            'branch_id' => $otherBranch->id,
            'category_id' => $this->category->id
        ]);

        // Try to access other vendor's product
        $response = $this->actingAs($this->vendor)
                         ->get("/vendor/products/{$otherProduct->id}/edit");

        $response->assertStatus(404); // Should not be found
    }

    /**
     * Test validation errors are handled properly
     */
    public function test_validation_errors_are_handled_properly()
    {
        $invalidData = [
            'name' => '', // Required field
            'price' => -10, // Invalid price
            'stock' => -5, // Invalid stock
        ];

        $response = $this->actingAs($this->vendor)
                         ->putJson("/vendor/products/{$this->product->id}", $invalidData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name', 'price', 'stock']);
    }

    /**
     * Test CSS and JavaScript assets are loaded correctly
     */
    public function test_css_and_javascript_assets_are_loaded_correctly()
    {
        $response = $this->actingAs($this->vendor)
                         ->get("/vendor/products/{$this->product->id}/edit");

        // Check for Vite asset loading
        $response->assertSee('vendor-product-edit.js')
                 ->assertSee('Font Awesome'); // CSS framework
    }

    /**
     * Test that the page includes proper Vue.js configuration
     */
    public function test_page_includes_proper_vue_configuration()
    {
        $response = $this->actingAs($this->vendor)
                         ->get("/vendor/products/{$this->product->id}/edit");

        $response->assertSee('vendorProductEditConfig')
                 ->assertSee('apiBaseUrl')
                 ->assertSee('csrfToken');
    }
}
