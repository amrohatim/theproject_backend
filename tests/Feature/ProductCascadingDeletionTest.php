<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductSize;
use App\Models\ProductColorSize;
use App\Models\ProductSpecification;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

class ProductCascadingDeletionTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $company;
    protected $branch;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test user
        $this->user = User::factory()->create([
            'role' => 'vendor'
        ]);
        
        // Create test company
        $this->company = Company::factory()->create([
            'user_id' => $this->user->id
        ]);
        
        // Create test branch
        $this->branch = Branch::factory()->create([
            'company_id' => $this->company->id
        ]);
        
        // Create test category
        $this->category = Category::factory()->create([
            'type' => 'product'
        ]);
        
        // Mock storage
        Storage::fake('public');
    }

    /** @test */
    public function it_cascades_deletion_of_all_related_records_when_product_is_deleted()
    {
        // Create a product with all related data
        $product = Product::factory()->create([
            'branch_id' => $this->branch->id,
            'category_id' => $this->category->id,
            'name' => 'Test Product',
            'price' => 100.00,
            'stock' => 50,
            'image' => 'products/test-main-image.jpg'
        ]);

        // Create specifications
        $specification1 = ProductSpecification::factory()->create([
            'product_id' => $product->id,
            'key' => 'Material',
            'value' => 'Cotton'
        ]);
        
        $specification2 = ProductSpecification::factory()->create([
            'product_id' => $product->id,
            'key' => 'Brand',
            'value' => 'Test Brand'
        ]);

        // Create colors with images
        $color1 = ProductColor::factory()->create([
            'product_id' => $product->id,
            'name' => 'Red',
            'color_code' => '#FF0000',
            'image' => 'product-colors/red-image.jpg',
            'is_default' => true
        ]);
        
        $color2 = ProductColor::factory()->create([
            'product_id' => $product->id,
            'name' => 'Blue',
            'color_code' => '#0000FF',
            'image' => 'product-colors/blue-image.jpg',
            'is_default' => false
        ]);

        // Create sizes
        $size1 = ProductSize::factory()->create([
            'product_id' => $product->id,
            'name' => 'Small',
            'value' => 'S'
        ]);
        
        $size2 = ProductSize::factory()->create([
            'product_id' => $product->id,
            'name' => 'Medium',
            'value' => 'M'
        ]);

        // Create color-size combinations
        $colorSize1 = ProductColorSize::factory()->create([
            'product_id' => $product->id,
            'product_color_id' => $color1->id,
            'product_size_id' => $size1->id,
            'stock' => 10
        ]);
        
        $colorSize2 = ProductColorSize::factory()->create([
            'product_id' => $product->id,
            'product_color_id' => $color1->id,
            'product_size_id' => $size2->id,
            'stock' => 15
        ]);
        
        $colorSize3 = ProductColorSize::factory()->create([
            'product_id' => $product->id,
            'product_color_id' => $color2->id,
            'product_size_id' => $size1->id,
            'stock' => 8
        ]);

        // Create fake image files
        Storage::disk('public')->put('products/test-main-image.jpg', 'fake main image content');
        Storage::disk('public')->put('product-colors/red-image.jpg', 'fake red image content');
        Storage::disk('public')->put('product-colors/blue-image.jpg', 'fake blue image content');

        // Verify all records exist before deletion
        $this->assertDatabaseHas('products', ['id' => $product->id]);
        $this->assertDatabaseHas('product_specifications', ['id' => $specification1->id]);
        $this->assertDatabaseHas('product_specifications', ['id' => $specification2->id]);
        $this->assertDatabaseHas('product_colors', ['id' => $color1->id]);
        $this->assertDatabaseHas('product_colors', ['id' => $color2->id]);
        $this->assertDatabaseHas('product_sizes', ['id' => $size1->id]);
        $this->assertDatabaseHas('product_sizes', ['id' => $size2->id]);
        $this->assertDatabaseHas('product_color_sizes', ['id' => $colorSize1->id]);
        $this->assertDatabaseHas('product_color_sizes', ['id' => $colorSize2->id]);
        $this->assertDatabaseHas('product_color_sizes', ['id' => $colorSize3->id]);

        // Verify images exist
        Storage::disk('public')->assertExists('products/test-main-image.jpg');
        Storage::disk('public')->assertExists('product-colors/red-image.jpg');
        Storage::disk('public')->assertExists('product-colors/blue-image.jpg');

        // Delete the product
        $product->delete();

        // Verify all related records are deleted
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        $this->assertDatabaseMissing('product_specifications', ['id' => $specification1->id]);
        $this->assertDatabaseMissing('product_specifications', ['id' => $specification2->id]);
        $this->assertDatabaseMissing('product_colors', ['id' => $color1->id]);
        $this->assertDatabaseMissing('product_colors', ['id' => $color2->id]);
        $this->assertDatabaseMissing('product_sizes', ['id' => $size1->id]);
        $this->assertDatabaseMissing('product_sizes', ['id' => $size2->id]);
        $this->assertDatabaseMissing('product_color_sizes', ['id' => $colorSize1->id]);
        $this->assertDatabaseMissing('product_color_sizes', ['id' => $colorSize2->id]);
        $this->assertDatabaseMissing('product_color_sizes', ['id' => $colorSize3->id]);

        // Verify images are deleted
        Storage::disk('public')->assertMissing('products/test-main-image.jpg');
        Storage::disk('public')->assertMissing('product-colors/red-image.jpg');
        Storage::disk('public')->assertMissing('product-colors/blue-image.jpg');
    }

    /** @test */
    public function it_handles_deletion_gracefully_when_no_related_records_exist()
    {
        // Create a simple product without any related data
        $product = Product::factory()->create([
            'branch_id' => $this->branch->id,
            'category_id' => $this->category->id,
            'name' => 'Simple Product',
            'price' => 50.00,
            'stock' => 10
        ]);

        // Verify product exists
        $this->assertDatabaseHas('products', ['id' => $product->id]);

        // Delete the product - should not throw any errors
        $product->delete();

        // Verify product is deleted
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    /** @test */
    public function it_handles_missing_image_files_gracefully()
    {
        // Create a product with image paths that don't exist
        $product = Product::factory()->create([
            'branch_id' => $this->branch->id,
            'category_id' => $this->category->id,
            'name' => 'Product with Missing Images',
            'price' => 75.00,
            'stock' => 20,
            'image' => 'products/non-existent-main.jpg'
        ]);

        // Create colors with non-existent images
        $color = ProductColor::factory()->create([
            'product_id' => $product->id,
            'name' => 'Green',
            'image' => 'product-colors/non-existent-color.jpg'
        ]);

        // Verify records exist
        $this->assertDatabaseHas('products', ['id' => $product->id]);
        $this->assertDatabaseHas('product_colors', ['id' => $color->id]);

        // Delete the product - should not throw errors even with missing files
        $product->delete();

        // Verify records are deleted
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        $this->assertDatabaseMissing('product_colors', ['id' => $color->id]);
    }
}
