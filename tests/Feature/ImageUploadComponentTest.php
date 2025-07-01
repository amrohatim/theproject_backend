<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\View\Components\ImageUpload;

class ImageUploadComponentTest extends TestCase
{
    use RefreshDatabase;

    protected $merchant;
    protected $category;

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
    }

    /** @test */
    public function image_upload_component_renders_correctly()
    {
        $component = new ImageUpload();
        
        $this->assertEquals('image', $component->name);
        $this->assertEquals('Image', $component->label);
        $this->assertNull($component->currentImage);
        $this->assertFalse($component->required);
        $this->assertEquals('image/*', $component->accept);
        $this->assertEquals('2MB', $component->maxSize);
        $this->assertEquals('PNG, JPG, JPEG, GIF', $component->allowedFormats);
    }

    /** @test */
    public function image_upload_component_accepts_custom_parameters()
    {
        $component = new ImageUpload(
            name: 'product_image',
            label: 'Product Photo',
            currentImage: 'https://example.com/image.jpg',
            required: true,
            maxSize: '5MB',
            allowedFormats: 'JPG, PNG only'
        );
        
        $this->assertEquals('product_image', $component->name);
        $this->assertEquals('Product Photo', $component->label);
        $this->assertEquals('https://example.com/image.jpg', $component->currentImage);
        $this->assertTrue($component->required);
        $this->assertEquals('5MB', $component->maxSize);
        $this->assertEquals('JPG, PNG only', $component->allowedFormats);
    }

    /** @test */
    public function product_create_page_includes_image_upload_component()
    {
        $response = $this->actingAs($this->merchant)
            ->get(route('merchant.products.create'));

        $response->assertStatus(200);
        $response->assertSee('data-name="image"', false);
        $response->assertSee('Product Image');
        $response->assertSee('Click to upload or drag and drop');
        $response->assertSee('PNG, JPG, GIF up to 2MB');
    }

    /** @test */
    public function service_create_page_includes_image_upload_component()
    {
        $response = $this->actingAs($this->merchant)
            ->get(route('merchant.services.create'));

        $response->assertStatus(200);
        $response->assertSee('data-name="image"', false);
        $response->assertSee('Service Image');
        $response->assertSee('Click to upload or drag and drop');
        $response->assertSee('PNG, JPG, GIF up to 2MB');
    }

    /** @test */
    public function image_upload_component_shows_current_image_in_edit_mode()
    {
        // Create a product with an image
        $product = \App\Models\Product::factory()->create([
            'user_id' => $this->merchant->id,
            'category_id' => $this->category->id,
            'image' => 'products/test_image.jpg',
        ]);

        $response = $this->actingAs($this->merchant)
            ->get(route('merchant.products.edit', $product->id));

        $response->assertStatus(200);
        $response->assertSee('data-name="image"', false);
        $response->assertSee('Product Image');
        
        // Should show the current image
        $response->assertSee($product->image, false);
    }

    /** @test */
    public function image_upload_component_displays_validation_errors()
    {
        $response = $this->actingAs($this->merchant)
            ->post(route('merchant.products.store'), [
                'name' => 'Test Product',
                'description' => 'Test description',
                'price' => 'invalid_price', // This will cause validation error
                'category_id' => $this->category->id,
            ]);

        $response->assertSessionHasErrors();
        
        // Follow redirect to see the form with errors
        $response = $this->actingAs($this->merchant)
            ->get(route('merchant.products.create'));

        $response->assertStatus(200);
        $response->assertSee('data-name="image"', false);
    }

    /** @test */
    public function image_upload_component_includes_required_javascript()
    {
        $response = $this->actingAs($this->merchant)
            ->get(route('merchant.products.create'));

        $response->assertStatus(200);
        
        // Check for JavaScript functions
        $response->assertSee('initializeImageUpload');
        $response->assertSee('handleFileSelection');
        $response->assertSee('dragover');
        $response->assertSee('dragleave');
        $response->assertSee('drop');
    }

    /** @test */
    public function image_upload_component_has_proper_file_input_attributes()
    {
        $response = $this->actingAs($this->merchant)
            ->get(route('merchant.products.create'));

        $response->assertStatus(200);
        
        // Check for proper input attributes
        $response->assertSee('type="file"', false);
        $response->assertSee('name="image"', false);
        $response->assertSee('accept="image/*"', false);
        $response->assertSee('class="image-input"', false);
    }

    /** @test */
    public function image_upload_component_shows_proper_styling()
    {
        $response = $this->actingAs($this->merchant)
            ->get(route('merchant.products.create'));

        $response->assertStatus(200);
        
        // Check for Discord-themed styling
        $response->assertSee('var(--discord-darkest)', false);
        $response->assertSee('var(--discord-light)', false);
        $response->assertSee('var(--discord-red)', false);
        $response->assertSee('border: 2px dashed', false);
    }

    /** @test */
    public function image_upload_component_includes_accessibility_features()
    {
        $response = $this->actingAs($this->merchant)
            ->get(route('merchant.products.create'));

        $response->assertStatus(200);
        
        // Check for accessibility attributes
        $response->assertSee('alt="Preview"', false);
        $response->assertSee('form-label', false);
    }

    /** @test */
    public function image_upload_component_handles_error_display()
    {
        $component = new ImageUpload(
            name: 'test_image',
            error: 'File size too large'
        );
        
        $this->assertEquals('File size too large', $component->error);
    }
}
