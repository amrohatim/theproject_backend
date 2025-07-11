<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductSize;
use App\Models\ProductSpecification;
use App\Models\ProductColorSize;
use App\Models\Category;
use App\Models\Branch;

class EnhancedMerchantProductManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $merchant;
    protected $category;
    protected $branch;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a merchant user
        $this->merchant = User::factory()->create([
            'role' => 'merchant',
            'email_verified_at' => now(),
            'status' => 'active',
        ]);

        // Create a category
        $this->category = Category::factory()->create([
            'name' => 'Test Category',
            'type' => 'product',
            'is_active' => true,
        ]);

        // Create a branch for the merchant
        $this->branch = Branch::factory()->create([
            'user_id' => $this->merchant->id,
            'name' => 'Test Branch',
            'status' => 'active',
        ]);

        Storage::fake('public');
    }

    /** @test */
    public function merchant_can_access_enhanced_product_create_page()
    {
        $response = $this->actingAs($this->merchant)
            ->get(route('merchant.products.create'));

        $response->assertStatus(200);
        $response->assertViewIs('merchant.products.create');
        $response->assertViewHas(['parentCategories', 'branches']);
    }

    /** @test */
    public function merchant_can_create_product_with_colors_and_specifications()
    {
        $colorImage = UploadedFile::fake()->image('color.jpg', 300, 400);

        $productData = [
            'name' => 'Enhanced Test Product',
            'category_id' => $this->category->id,
            'branch_id' => $this->branch->id,
            'price' => 99.99,
            'original_price' => 129.99,
            'stock' => 100,
            'description' => 'Test product with enhanced features',
            'is_available' => '1',
            
            // Colors data
            'colors' => [
                [
                    'name' => 'Red',
                    'color_code' => '#FF0000',
                    'price_adjustment' => 0,
                    'stock' => 50,
                    'display_order' => 0,
                    'is_default' => '1',
                ]
            ],
            
            // Specifications data
            'specifications' => [
                [
                    'key' => 'Material',
                    'value' => 'Cotton',
                    'display_order' => 0,
                ],
                [
                    'key' => 'Care Instructions',
                    'value' => 'Machine wash cold',
                    'display_order' => 1,
                ]
            ]
        ];

        $response = $this->actingAs($this->merchant)
            ->post(route('merchant.products.store'), $productData + [
                'color_images' => [0 => $colorImage]
            ]);

        $response->assertRedirect(route('merchant.products.index'));
        $response->assertSessionHas('success');

        // Verify product was created
        $this->assertDatabaseHas('products', [
            'name' => 'Enhanced Test Product',
            'user_id' => $this->merchant->id,
            'category_id' => $this->category->id,
            'branch_id' => $this->branch->id,
            'price' => 99.99,
        ]);

        $product = Product::where('name', 'Enhanced Test Product')->first();

        // Verify color was created
        $this->assertDatabaseHas('product_colors', [
            'product_id' => $product->id,
            'name' => 'Red',
            'color_code' => '#FF0000',
            'is_default' => true,
        ]);

        // Verify specifications were created
        $this->assertDatabaseHas('product_specifications', [
            'product_id' => $product->id,
            'key' => 'Material',
            'value' => 'Cotton',
        ]);

        $this->assertDatabaseHas('product_specifications', [
            'product_id' => $product->id,
            'key' => 'Care Instructions',
            'value' => 'Machine wash cold',
        ]);
    }

    /** @test */
    public function merchant_can_edit_product_with_enhanced_features()
    {
        // Create a product with colors and specifications
        $product = Product::factory()->create([
            'user_id' => $this->merchant->id,
            'category_id' => $this->category->id,
            'branch_id' => $this->branch->id,
        ]);

        $color = ProductColor::factory()->create([
            'product_id' => $product->id,
            'name' => 'Blue',
            'is_default' => true,
        ]);

        $specification = ProductSpecification::factory()->create([
            'product_id' => $product->id,
            'key' => 'Size',
            'value' => 'Large',
        ]);

        $response = $this->actingAs($this->merchant)
            ->get(route('merchant.products.edit', $product->id));

        $response->assertStatus(200);
        $response->assertViewIs('merchant.products.edit');
        $response->assertViewHas(['product', 'parentCategories', 'branches']);
        
        // Verify the product data is loaded with relationships
        $viewProduct = $response->viewData('product');
        $this->assertTrue($viewProduct->colors->isNotEmpty());
        $this->assertTrue($viewProduct->specifications->isNotEmpty());
    }

    /** @test */
    public function merchant_can_update_product_with_new_colors_and_specifications()
    {
        $product = Product::factory()->create([
            'user_id' => $this->merchant->id,
            'category_id' => $this->category->id,
            'branch_id' => $this->branch->id,
        ]);

        $newColorImage = UploadedFile::fake()->image('new-color.jpg', 300, 400);

        $updateData = [
            'name' => 'Updated Product Name',
            'category_id' => $this->category->id,
            'branch_id' => $this->branch->id,
            'price' => 149.99,
            'stock' => 75,
            'description' => 'Updated description',
            'is_available' => '1',
            
            'colors' => [
                [
                    'name' => 'Green',
                    'color_code' => '#00FF00',
                    'price_adjustment' => 5.00,
                    'stock' => 25,
                    'display_order' => 0,
                    'is_default' => '1',
                ]
            ],
            
            'specifications' => [
                [
                    'key' => 'Updated Material',
                    'value' => 'Polyester',
                    'display_order' => 0,
                ]
            ]
        ];

        $response = $this->actingAs($this->merchant)
            ->put(route('merchant.products.update', $product->id), $updateData + [
                'color_images' => [0 => $newColorImage]
            ]);

        $response->assertRedirect(route('merchant.products.index'));
        $response->assertSessionHas('success');

        // Verify product was updated
        $product->refresh();
        $this->assertEquals('Updated Product Name', $product->name);
        $this->assertEquals(149.99, $product->price);

        // Verify new color was created (old ones should be deleted)
        $this->assertDatabaseHas('product_colors', [
            'product_id' => $product->id,
            'name' => 'Green',
            'color_code' => '#00FF00',
            'is_default' => true,
        ]);

        // Verify new specification was created
        $this->assertDatabaseHas('product_specifications', [
            'product_id' => $product->id,
            'key' => 'Updated Material',
            'value' => 'Polyester',
        ]);
    }

    /** @test */
    public function enhanced_product_creation_validates_required_fields()
    {
        $response = $this->actingAs($this->merchant)
            ->post(route('merchant.products.store'), []);

        $response->assertSessionHasErrors([
            'name',
            'category_id',
            'branch_id',
            'price',
            'stock',
            'colors',
        ]);
    }

    /** @test */
    public function merchant_cannot_access_other_merchants_products()
    {
        $otherMerchant = User::factory()->create(['role' => 'merchant']);
        $otherProduct = Product::factory()->create(['user_id' => $otherMerchant->id]);

        $response = $this->actingAs($this->merchant)
            ->get(route('merchant.products.edit', $otherProduct->id));

        $response->assertStatus(404);
    }
}
