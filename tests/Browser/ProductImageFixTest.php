<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductColor;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProductImageFixTest extends DuskTestCase
{
    use DatabaseTransactions;

    /**
     * Test that product color images load correctly without 403 errors
     *
     * @return void
     */
    public function test_product_color_images_load_without_403_errors()
    {
        // Create a merchant user
        $merchant = User::factory()->create([
            'role' => 'merchant',
            'email_verified_at' => now(),
        ]);

        // Find a product with color images
        $product = Product::whereHas('colors', function($query) {
            $query->whereNotNull('image');
        })->first();

        if (!$product) {
            $this->markTestSkipped('No products with color images found for testing');
        }

        $this->browse(function (Browser $browser) use ($merchant, $product) {
            $browser->loginAs($merchant)
                    ->visit("/merchant/products/{$product->id}/edit")
                    ->waitFor('.color-item', 10)
                    ->assertSee('Edit Product');

            // Check that color images are displayed without errors
            $colorImages = $browser->elements('.image-preview');
            
            foreach ($colorImages as $img) {
                $src = $img->getAttribute('src');
                if (!empty($src)) {
                    // Make a direct request to the image URL to verify it loads
                    $response = $browser->driver->executeScript("
                        return fetch('{$src}').then(response => response.status);
                    ");
                    
                    $this->assertEquals(200, $response, "Image failed to load: {$src}");
                }
            }

            // Check browser console for any 403 errors
            $logs = $browser->driver->manage()->getLog('browser');
            $has403Error = false;
            
            foreach ($logs as $log) {
                if (strpos($log['message'], '403') !== false) {
                    $has403Error = true;
                    break;
                }
            }
            
            $this->assertFalse($has403Error, 'Found 403 errors in browser console');
        });
    }

    /**
     * Test specific problematic image that was causing 403 errors
     *
     * @return void
     */
    public function test_specific_problematic_image_loads_correctly()
    {
        $this->browse(function (Browser $browser) {
            // Test the specific image that was causing issues
            $problematicImageUrl = route('images.products', ['filename' => '1751640943_uBysKFaIUZ.png']);
            
            $browser->visit($problematicImageUrl);
            
            // Check that we don't get a 403 error page
            $browser->assertDontSee('403')
                    ->assertDontSee('Forbidden')
                    ->assertDontSee('Access Denied');
            
            // Verify the response is an image by checking content type
            $contentType = $browser->driver->executeScript("
                return document.contentType || document.querySelector('meta[http-equiv=\"content-type\"]')?.content;
            ");
            
            $this->assertStringContains('image', $contentType, 'Response is not an image');
        });
    }

    /**
     * Test that all product color image URLs are accessible
     *
     * @return void
     */
    public function test_all_product_color_images_are_accessible()
    {
        $colors = ProductColor::whereNotNull('image')->limit(5)->get();
        
        if ($colors->isEmpty()) {
            $this->markTestSkipped('No product colors with images found for testing');
        }

        $this->browse(function (Browser $browser) use ($colors) {
            foreach ($colors as $color) {
                $imageUrl = $color->image;
                
                $browser->visit($imageUrl);
                
                // Verify we don't get error pages
                $browser->assertDontSee('403')
                        ->assertDontSee('404')
                        ->assertDontSee('Forbidden')
                        ->assertDontSee('Not Found');
                
                // Verify response status using JavaScript
                $status = $browser->driver->executeScript("
                    return fetch('{$imageUrl}').then(response => response.status);
                ");
                
                $this->assertEquals(200, $status, "Image URL returned non-200 status: {$imageUrl}");
            }
        });
    }
}
