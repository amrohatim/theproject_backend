<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Company;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductColor;
use App\Models\ProductSpecification;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class VendorProductEditTest extends DuskTestCase
{
    use DatabaseMigrations;

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

        // Create product with colors and specifications
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

        ProductSpecification::factory()->create([
            'product_id' => $this->product->id,
            'name' => 'Size',
            'value' => 'Large'
        ]);
    }

    /**
     * Test vendor can access product edit page with correct UI layout
     */
    public function test_vendor_can_access_product_edit_page_with_correct_layout()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->vendor)
                    ->visit("/vendor/products/{$this->product->id}/edit")
                    ->waitFor('#vendor-product-edit-app', 10)
                    ->assertSee('Edit Product')
                    ->assertSee('Update product information, colors, and specifications')
                    
                    // Verify the layout uses dashboard layout (Tailwind-based)
                    ->assertPresent('.container.mx-auto')
                    ->assertPresent('.bg-white.dark\\:bg-gray-800.rounded-lg.shadow')
                    
                    // Verify header structure matches create page
                    ->assertSee('Back to Products')
                    ->assertSee('Save Changes')
                    
                    // Verify stock progress indicator
                    ->assertSee('Stock Allocation Progress')
                    ->assertSee('50 units allocated') // Total stock
                    
                    // Verify tab navigation
                    ->assertSee('Basic Info')
                    ->assertSee('Colors & Images')
                    ->assertSee('Specifications');
        });
    }

    /**
     * Test all three tabs are functional and display correct content
     */
    public function test_all_tabs_are_functional_and_display_correct_content()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->vendor)
                    ->visit("/vendor/products/{$this->product->id}/edit")
                    ->waitFor('#vendor-product-edit-app', 10)
                    
                    // Test Basic Info tab (should be active by default)
                    ->assertSee('Product Name')
                    ->assertInputValue('input[name="name"]', 'Test Product')
                    ->assertSee('Category')
                    ->assertSee('Branch')
                    ->assertSee('Price')
                    ->assertInputValue('input[name="price"]', '100')
                    ->assertSee('Original Price')
                    ->assertInputValue('input[name="original_price"]', '120')
                    ->assertSee('Stock')
                    ->assertInputValue('input[name="stock"]', '50')
                    
                    // Test Colors & Images tab
                    ->click('button:contains("Colors & Images")')
                    ->waitFor('.vue-tab-content', 2)
                    ->assertSee('Product Colors')
                    ->assertSee('Red') // First color
                    ->assertSee('Blue') // Second color
                    ->assertSee('Add New Color')
                    
                    // Test Specifications tab
                    ->click('button:contains("Specifications")')
                    ->waitFor('.vue-tab-content', 2)
                    ->assertSee('Product Specifications')
                    ->assertSee('Material') // First specification
                    ->assertSee('Cotton')
                    ->assertSee('Size') // Second specification
                    ->assertSee('Large')
                    ->assertSee('Add New Specification');
        });
    }

    /**
     * Test editing basic product information
     */
    public function test_editing_basic_product_information()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->vendor)
                    ->visit("/vendor/products/{$this->product->id}/edit")
                    ->waitFor('#vendor-product-edit-app', 10)

                    // Edit basic information
                    ->clear('input[name="name"]')
                    ->type('input[name="name"]', 'Updated Test Product')
                    ->clear('input[name="description"]')
                    ->type('input[name="description"]', 'Updated product description')
                    ->clear('input[name="price"]')
                    ->type('input[name="price"]', '150')
                    ->clear('input[name="original_price"]')
                    ->type('input[name="original_price"]', '180')
                    ->clear('input[name="stock"]')
                    ->type('input[name="stock"]', '75')

                    // Save changes
                    ->click('button:contains("Save Changes")')
                    ->waitFor('.bg-green-100', 5) // Wait for success message
                    ->assertSee('Success!')
                    ->assertSee('Product updated successfully!')

                    // Verify changes were saved
                    ->click('button:contains("Continue")')
                    ->waitUntilMissing('.bg-green-100', 5)
                    ->assertInputValue('input[name="name"]', 'Updated Test Product')
                    ->assertInputValue('input[name="price"]', '150')
                    ->assertInputValue('input[name="stock"]', '75');
        });
    }

    /**
     * Test color management functionality
     */
    public function test_color_management_functionality()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->vendor)
                    ->visit("/vendor/products/{$this->product->id}/edit")
                    ->waitFor('#vendor-product-edit-app', 10)

                    // Navigate to Colors & Images tab
                    ->click('button:contains("Colors & Images")')
                    ->waitFor('.vue-tab-content', 2)

                    // Verify existing colors are displayed
                    ->assertSee('Red')
                    ->assertSee('Blue')

                    // Test adding a new color
                    ->click('button:contains("Add New Color")')
                    ->waitFor('input[placeholder*="color name"]', 2)
                    ->type('input[placeholder*="color name"]', 'Green')
                    ->type('input[type="color"]', '#00FF00')
                    ->type('input[placeholder*="stock"]', '25')

                    // Save the product to persist color changes
                    ->click('button:contains("Save Changes")')
                    ->waitFor('.bg-green-100', 5)
                    ->assertSee('Success!')
                    ->click('button:contains("Continue")')
                    ->waitUntilMissing('.bg-green-100', 5)

                    // Verify new color was added
                    ->click('button:contains("Colors & Images")')
                    ->waitFor('.vue-tab-content', 2)
                    ->assertSee('Green');
        });
    }

    /**
     * Test UI consistency with create page
     */
    public function test_ui_consistency_with_create_page()
    {
        $this->browse(function (Browser $browser) {
            // First visit create page to capture UI elements
            $browser->loginAs($this->vendor)
                    ->visit('/vendor/products/create')
                    ->waitFor('#vendor-product-create-app', 10);

            $createPageElements = [
                '.container.mx-auto',
                '.bg-white.dark\\:bg-gray-800.rounded-lg.shadow',
                '.border-b.border-gray-200.dark\\:border-gray-700',
                'button:contains("Basic Info")',
                'button:contains("Colors & Images")',
                'button:contains("Specifications")'
            ];

            // Verify create page has expected elements
            foreach ($createPageElements as $element) {
                $browser->assertPresent($element);
            }

            // Now visit edit page and verify same elements exist
            $browser->visit("/vendor/products/{$this->product->id}/edit")
                    ->waitFor('#vendor-product-edit-app', 10);

            foreach ($createPageElements as $element) {
                $browser->assertPresent($element);
            }

            // Verify both pages use the same layout structure
            $browser->assertPresent('.vue-app-container')
                    ->assertPresent('.p-6') // Tab content padding
                    ->assertPresent('.space-y-6'); // Content spacing
        });
    }

    /**
     * Test navigation and back button functionality
     */
    public function test_navigation_and_back_button_functionality()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->vendor)
                    ->visit("/vendor/products/{$this->product->id}/edit")
                    ->waitFor('#vendor-product-edit-app', 10)

                    // Test back button
                    ->click('a:contains("Back to Products")')
                    ->waitForLocation('/vendor/products')
                    ->assertPathIs('/vendor/products')
                    ->assertSee('Products')
                    ->assertSee('Manage your products');
        });
    }
}
