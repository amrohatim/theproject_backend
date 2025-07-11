<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use App\Models\Service;
use App\Models\Category;
use App\Models\Branch;
use App\Models\Merchant;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class MerchantServiceImageTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $merchant;
    protected $service;
    protected $category;
    protected $branch;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test merchant user
        $this->merchant = User::factory()->create([
            'role' => 'merchant',
            'status' => 'active',
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'registration_step' => 'verified',
        ]);

        // Create merchant record
        Merchant::factory()->create([
            'user_id' => $this->merchant->id,
            'is_verified' => true,
            'status' => 'active',
        ]);

        // Create category
        $this->category = Category::factory()->create([
            'is_active' => true,
        ]);

        // Create branch
        $this->branch = Branch::factory()->create([
            'user_id' => $this->merchant->id,
            'status' => 'active',
        ]);

        // Create service with image
        $this->service = Service::factory()->create([
            'branch_id' => $this->branch->id,
            'category_id' => $this->category->id,
            'name' => 'Test Massage Service',
            'image' => 'services/test-service-image.png',
            'price' => 100.00,
            'duration' => 60,
        ]);

        // Create a test image file in storage
        Storage::fake('public');
        $testImage = UploadedFile::fake()->image('test-service-image.png', 300, 300);
        Storage::disk('public')->putFileAs('services', $testImage, 'test-service-image.png');
        
        // Also create the image in the public storage directory for testing
        $publicPath = public_path('storage/services');
        if (!file_exists($publicPath)) {
            mkdir($publicPath, 0755, true);
        }
        copy($testImage->getPathname(), $publicPath . '/test-service-image.png');
    }

    /** @test */
    public function merchant_services_page_displays_images_correctly_desktop()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->merchant)
                    ->visit('/merchant/services')
                    ->waitFor('table')
                    ->assertSee($this->service->name);

            // Check that the image element exists and has correct src
            $browser->assertPresent('img[alt="' . $this->service->name . '"]');
            
            // Get the image src attribute
            $imageSrc = $browser->attribute('img[alt="' . $this->service->name . '"]', 'src');
            
            // Verify the image src is a valid URL and contains the expected path
            $this->assertStringContainsString('storage/services/test-service-image.png', $imageSrc);
            $this->assertStringStartsWith('http', $imageSrc);
            
            // Verify image is visible and has proper dimensions
            $browser->assertVisible('img[alt="' . $this->service->name . '"]')
                    ->assertAttribute('img[alt="' . $this->service->name . '"]', 'style', 'width: 60px; height: 60px; object-fit: cover; border-radius: 8px;');
        });
    }

    /** @test */
    public function merchant_services_page_displays_images_correctly_mobile()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(375, 667) // iPhone SE dimensions
                    ->loginAs($this->merchant)
                    ->visit('/merchant/services')
                    ->waitFor('table')
                    ->assertSee($this->service->name);

            // Check that the image element exists and has correct src
            $browser->assertPresent('img[alt="' . $this->service->name . '"]');
            
            // Get the image src attribute
            $imageSrc = $browser->attribute('img[alt="' . $this->service->name . '"]', 'src');
            
            // Verify the image src is a valid URL and contains the expected path
            $this->assertStringContainsString('storage/services/test-service-image.png', $imageSrc);
            $this->assertStringStartsWith('http', $imageSrc);
            
            // Verify image is visible
            $browser->assertVisible('img[alt="' . $this->service->name . '"]');
        });
    }

    /** @test */
    public function service_detail_page_displays_image_correctly()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->merchant)
                    ->visit('/merchant/services/' . $this->service->id)
                    ->waitFor('.discord-card')
                    ->assertSee($this->service->name);

            // Check that the image element exists in the service detail view
            $browser->assertPresent('img[alt="' . $this->service->name . '"]');
            
            // Get the image src attribute
            $imageSrc = $browser->attribute('img[alt="' . $this->service->name . '"]', 'src');
            
            // Verify the image src is a valid URL and contains the expected path
            $this->assertStringContainsString('storage/services/test-service-image.png', $imageSrc);
            $this->assertStringStartsWith('http', $imageSrc);
            
            // Verify image is visible and has proper styling
            $browser->assertVisible('img[alt="' . $this->service->name . '"]')
                    ->assertAttribute('img[alt="' . $this->service->name . '"]', 'class', 'img-fluid');
        });
    }

    /** @test */
    public function images_load_successfully_without_broken_placeholders()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->merchant)
                    ->visit('/merchant/services')
                    ->waitFor('table');

            // Execute JavaScript to check if images are loaded successfully
            $imageLoadStatus = $browser->script('
                const img = document.querySelector("img[alt=\'' . $this->service->name . '\']");
                if (!img) return "not_found";
                if (img.complete && img.naturalHeight !== 0) return "loaded";
                if (img.complete && img.naturalHeight === 0) return "broken";
                return "loading";
            ')[0];

            $this->assertEquals('loaded', $imageLoadStatus, 'Service image should load successfully without showing broken placeholder');
        });
    }

    /** @test */
    public function service_without_image_shows_placeholder()
    {
        // Create a service without an image
        $serviceWithoutImage = Service::factory()->create([
            'branch_id' => $this->branch->id,
            'category_id' => $this->category->id,
            'name' => 'Service Without Image',
            'image' => null,
            'price' => 50.00,
            'duration' => 30,
        ]);

        $this->browse(function (Browser $browser) use ($serviceWithoutImage) {
            $browser->loginAs($this->merchant)
                    ->visit('/merchant/services')
                    ->waitFor('table')
                    ->assertSee($serviceWithoutImage->name);

            // Check that the placeholder div is shown instead of an image
            $browser->assertMissing('img[alt="' . $serviceWithoutImage->name . '"]')
                    ->assertPresent('div[style*="background-color: var(--discord-darkest)"]')
                    ->assertPresent('i.fas.fa-concierge-bell');
        });
    }

    /** @test */
    public function image_urls_are_properly_formatted()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->merchant)
                    ->visit('/merchant/services')
                    ->waitFor('table');

            // Get the image src attribute
            $imageSrc = $browser->attribute('img[alt="' . $this->service->name . '"]', 'src');
            
            // Verify the URL doesn't have double storage paths or malformed URLs
            $this->assertStringNotContainsString('storage/storage/', $imageSrc, 'Image URL should not contain duplicate storage paths');
            $this->assertStringNotContainsString('https://https://', $imageSrc, 'Image URL should not contain duplicate protocols');
            $this->assertStringNotContainsString('storage/https://', $imageSrc, 'Image URL should not mix relative and absolute paths');
            
            // Verify it's a properly formatted URL
            $this->assertTrue(filter_var($imageSrc, FILTER_VALIDATE_URL) !== false, 'Image src should be a valid URL');
        });
    }
}
