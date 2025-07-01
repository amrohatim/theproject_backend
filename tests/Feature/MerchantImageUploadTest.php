<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Service;
use App\Models\Category;
use App\Models\Branch;

class MerchantImageUploadTest extends TestCase
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
            'status' => 'approved',
            'email_verified_at' => now(),
        ]);

        // Create a category
        $this->category = Category::factory()->create([
            'is_active' => true,
        ]);

        // Create a branch for the merchant
        $this->branch = Branch::factory()->create([
            'user_id' => $this->merchant->id,
            'status' => 'active',
        ]);

        // Fake the storage disk
        Storage::fake('public');
    }

    /** @test */
    public function merchant_can_create_product_with_valid_image()
    {
        $image = UploadedFile::fake()->image('product.jpg', 800, 600)->size(1024); // 1MB

        $response = $this->actingAs($this->merchant)
            ->post(route('merchant.products.store'), [
                'name' => 'Test Product',
                'description' => 'Test product description',
                'price' => 99.99,
                'category_id' => $this->category->id,
                'stock' => 10,
                'image' => $image,
            ]);

        $response->assertRedirect(route('merchant.products.index'));
        $response->assertSessionHas('success', 'Product created successfully.');

        // Assert product was created
        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'user_id' => $this->merchant->id,
        ]);

        // Assert image was stored
        $product = Product::where('name', 'Test Product')->first();
        $this->assertNotNull($product->image);
        Storage::disk('public')->assertExists($product->getRawImagePath());
    }

    /** @test */
    public function merchant_can_create_service_with_valid_image()
    {
        $image = UploadedFile::fake()->image('service.png', 600, 400)->size(1500); // 1.5MB

        $response = $this->actingAs($this->merchant)
            ->post(route('merchant.services.store'), [
                'name' => 'Test Service',
                'description' => 'Test service description',
                'price' => 149.99,
                'category_id' => $this->category->id,
                'duration' => 60,
                'image' => $image,
            ]);

        $response->assertRedirect(route('merchant.services.index'));
        $response->assertSessionHas('success', 'Service created successfully.');

        // Assert service was created
        $this->assertDatabaseHas('services', [
            'name' => 'Test Service',
            'branch_id' => $this->branch->id,
        ]);

        // Assert image was stored
        $service = Service::where('name', 'Test Service')->first();
        $this->assertNotNull($service->image);
        Storage::disk('public')->assertExists($service->getRawImagePath());
    }

    /** @test */
    public function image_upload_rejects_oversized_files()
    {
        $image = UploadedFile::fake()->image('large.jpg', 1200, 800)->size(3072); // 3MB

        $response = $this->actingAs($this->merchant)
            ->post(route('merchant.products.store'), [
                'name' => 'Test Product',
                'description' => 'Test product description',
                'price' => 99.99,
                'category_id' => $this->category->id,
                'image' => $image,
            ]);

        $response->assertSessionHasErrors(['image']);
        $this->assertDatabaseMissing('products', [
            'name' => 'Test Product',
        ]);
    }

    /** @test */
    public function image_upload_rejects_invalid_file_types()
    {
        $file = UploadedFile::fake()->create('document.pdf', 1024);

        $response = $this->actingAs($this->merchant)
            ->post(route('merchant.products.store'), [
                'name' => 'Test Product',
                'description' => 'Test product description',
                'price' => 99.99,
                'category_id' => $this->category->id,
                'image' => $file,
            ]);

        $response->assertSessionHasErrors(['image']);
        $this->assertDatabaseMissing('products', [
            'name' => 'Test Product',
        ]);
    }

    /** @test */
    public function merchant_can_update_product_image()
    {
        // Create product with initial image
        $initialImage = UploadedFile::fake()->image('initial.jpg', 600, 400)->size(1024);
        
        $product = Product::factory()->create([
            'user_id' => $this->merchant->id,
            'category_id' => $this->category->id,
            'image' => 'products/initial_image.jpg',
        ]);

        // Store the initial image file
        Storage::disk('public')->put('products/initial_image.jpg', 'fake content');

        // Update with new image
        $newImage = UploadedFile::fake()->image('updated.png', 800, 600)->size(1500);

        $response = $this->actingAs($this->merchant)
            ->put(route('merchant.products.update', $product->id), [
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'category_id' => $product->category_id,
                'image' => $newImage,
            ]);

        $response->assertRedirect(route('merchant.products.index'));
        $response->assertSessionHas('success', 'Product updated successfully.');

        // Assert old image was deleted
        Storage::disk('public')->assertMissing('products/initial_image.jpg');

        // Assert new image was stored
        $product->refresh();
        $this->assertNotNull($product->image);
        $this->assertNotEquals('products/initial_image.jpg', $product->getRawImagePath());
        Storage::disk('public')->assertExists($product->getRawImagePath());
    }

    /** @test */
    public function merchant_can_update_service_image()
    {
        // Create service with initial image
        $service = Service::factory()->create([
            'branch_id' => $this->branch->id,
            'category_id' => $this->category->id,
            'image' => 'services/initial_service.jpg',
        ]);

        // Store the initial image file
        Storage::disk('public')->put('services/initial_service.jpg', 'fake content');

        // Update with new image
        $newImage = UploadedFile::fake()->image('updated_service.gif', 500, 300)->size(800);

        $response = $this->actingAs($this->merchant)
            ->put(route('merchant.services.update', $service->id), [
                'name' => $service->name,
                'description' => $service->description,
                'price' => $service->price,
                'category_id' => $service->category_id,
                'image' => $newImage,
            ]);

        $response->assertRedirect(route('merchant.services.index'));
        $response->assertSessionHas('success', 'Service updated successfully.');

        // Assert old image was deleted
        Storage::disk('public')->assertMissing('services/initial_service.jpg');

        // Assert new image was stored
        $service->refresh();
        $this->assertNotNull($service->image);
        $this->assertNotEquals('services/initial_service.jpg', $service->getRawImagePath());
        Storage::disk('public')->assertExists($service->getRawImagePath());
    }

    /** @test */
    public function product_deletion_removes_associated_image()
    {
        // Create product with image
        $product = Product::factory()->create([
            'user_id' => $this->merchant->id,
            'category_id' => $this->category->id,
            'image' => 'products/test_product.jpg',
        ]);

        // Store the image file
        Storage::disk('public')->put('products/test_product.jpg', 'fake content');
        Storage::disk('public')->assertExists('products/test_product.jpg');

        // Delete the product
        $response = $this->actingAs($this->merchant)
            ->delete(route('merchant.products.destroy', $product->id));

        $response->assertRedirect(route('merchant.products.index'));
        $response->assertSessionHas('success', 'Product deleted successfully.');

        // Assert product was deleted
        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);

        // Assert image was deleted
        Storage::disk('public')->assertMissing('products/test_product.jpg');
    }

    /** @test */
    public function service_deletion_removes_associated_image()
    {
        // Create service with image
        $service = Service::factory()->create([
            'branch_id' => $this->branch->id,
            'category_id' => $this->category->id,
            'image' => 'services/test_service.png',
        ]);

        // Store the image file
        Storage::disk('public')->put('services/test_service.png', 'fake content');
        Storage::disk('public')->assertExists('services/test_service.png');

        // Delete the service
        $response = $this->actingAs($this->merchant)
            ->delete(route('merchant.services.destroy', $service->id));

        $response->assertRedirect(route('merchant.services.index'));
        $response->assertSessionHas('success', 'Service deleted successfully.');

        // Assert service was deleted
        $this->assertDatabaseMissing('services', [
            'id' => $service->id,
        ]);

        // Assert image was deleted
        Storage::disk('public')->assertMissing('services/test_service.png');
    }

    /** @test */
    public function products_and_services_can_be_created_without_images()
    {
        // Create product without image
        $response = $this->actingAs($this->merchant)
            ->post(route('merchant.products.store'), [
                'name' => 'Product Without Image',
                'description' => 'Test product description',
                'price' => 99.99,
                'category_id' => $this->category->id,
            ]);

        $response->assertRedirect(route('merchant.products.index'));
        $this->assertDatabaseHas('products', [
            'name' => 'Product Without Image',
            'image' => null,
        ]);

        // Create service without image
        $response = $this->actingAs($this->merchant)
            ->post(route('merchant.services.store'), [
                'name' => 'Service Without Image',
                'description' => 'Test service description',
                'price' => 149.99,
                'category_id' => $this->category->id,
            ]);

        $response->assertRedirect(route('merchant.services.index'));
        $this->assertDatabaseHas('services', [
            'name' => 'Service Without Image',
            'image' => null,
        ]);
    }
}
