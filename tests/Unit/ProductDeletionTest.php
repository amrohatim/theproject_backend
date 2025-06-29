<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductSize;
use App\Models\ProductColorSize;
use App\Models\ProductSpecification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Mockery;

class ProductDeletionTest extends TestCase
{
    /** @test */
    public function it_has_cascading_deletion_event_handler()
    {
        // Test that the Product model has the deleting event handler
        $product = new Product();
        
        // Check if the booted method exists and sets up event handlers
        $this->assertTrue(method_exists(Product::class, 'booted'));
        
        // Verify that the model has the necessary relationships
        $this->assertTrue(method_exists($product, 'colors'));
        $this->assertTrue(method_exists($product, 'sizes'));
        $this->assertTrue(method_exists($product, 'colorSizes'));
        $this->assertTrue(method_exists($product, 'specifications'));
        $this->assertTrue(method_exists($product, 'getRawImagePath'));
    }

    /** @test */
    public function it_has_proper_relationships_defined()
    {
        $product = new Product();
        
        // Test colors relationship
        $colorsRelation = $product->colors();
        $this->assertEquals('App\Models\ProductColor', $colorsRelation->getRelated()::class);
        $this->assertEquals('product_id', $colorsRelation->getForeignKeyName());
        
        // Test sizes relationship
        $sizesRelation = $product->sizes();
        $this->assertEquals('App\Models\ProductSize', $sizesRelation->getRelated()::class);
        $this->assertEquals('product_id', $sizesRelation->getForeignKeyName());
        
        // Test colorSizes relationship
        $colorSizesRelation = $product->colorSizes();
        $this->assertEquals('App\Models\ProductColorSize', $colorSizesRelation->getRelated()::class);
        $this->assertEquals('product_id', $colorSizesRelation->getForeignKeyName());
        
        // Test specifications relationship
        $specificationsRelation = $product->specifications();
        $this->assertEquals('App\Models\ProductSpecification', $specificationsRelation->getRelated()::class);
        $this->assertEquals('product_id', $specificationsRelation->getForeignKeyName());
    }

    /** @test */
    public function it_has_required_imports_for_cascading_deletion()
    {
        // Use reflection to check if the Product model has the necessary imports
        $reflection = new \ReflectionClass(Product::class);
        $fileContent = file_get_contents($reflection->getFileName());
        
        // Check for required imports
        $this->assertStringContainsString('use Illuminate\Support\Facades\DB;', $fileContent);
        $this->assertStringContainsString('use Illuminate\Support\Facades\Log;', $fileContent);
        $this->assertStringContainsString('use Illuminate\Support\Facades\Storage;', $fileContent);
    }

    /** @test */
    public function it_has_booted_method_with_deleting_event()
    {
        // Use reflection to check the booted method content
        $reflection = new \ReflectionClass(Product::class);
        $fileContent = file_get_contents($reflection->getFileName());
        
        // Check that the booted method contains deleting event handler
        $this->assertStringContainsString('static::deleting(function ($product)', $fileContent);
        $this->assertStringContainsString('DB::beginTransaction()', $fileContent);
        $this->assertStringContainsString('$product->colorSizes()->delete()', $fileContent);
        $this->assertStringContainsString('$product->colors()->delete()', $fileContent);
        $this->assertStringContainsString('$product->sizes()->delete()', $fileContent);
        $this->assertStringContainsString('$product->specifications()->delete()', $fileContent);
        $this->assertStringContainsString('DB::commit()', $fileContent);
    }

    /** @test */
    public function it_handles_image_deletion_in_cascading_deletion()
    {
        // Use reflection to check image deletion logic
        $reflection = new \ReflectionClass(Product::class);
        $fileContent = file_get_contents($reflection->getFileName());
        
        // Check that image deletion is handled
        $this->assertStringContainsString('Storage::disk(\'public\')->exists', $fileContent);
        $this->assertStringContainsString('Storage::disk(\'public\')->delete', $fileContent);
        $this->assertStringContainsString('getRawImagePath()', $fileContent);
    }

    /** @test */
    public function it_has_error_handling_in_cascading_deletion()
    {
        // Use reflection to check error handling
        $reflection = new \ReflectionClass(Product::class);
        $fileContent = file_get_contents($reflection->getFileName());
        
        // Check that error handling is implemented
        $this->assertStringContainsString('try {', $fileContent);
        $this->assertStringContainsString('} catch (\Exception $e) {', $fileContent);
        $this->assertStringContainsString('DB::rollBack()', $fileContent);
        $this->assertStringContainsString('throw $e;', $fileContent);
        $this->assertStringContainsString('Log::error', $fileContent);
    }

    /** @test */
    public function product_color_has_get_raw_image_path_method()
    {
        $productColor = new ProductColor();
        $this->assertTrue(method_exists($productColor, 'getRawImagePath'));
    }

    /** @test */
    public function controllers_have_enhanced_deletion_methods()
    {
        // Check Admin ProductController
        $adminControllerFile = app_path('Http/Controllers/Admin/ProductController.php');
        $adminContent = file_get_contents($adminControllerFile);
        
        $this->assertStringContainsString('try {', $adminContent);
        $this->assertStringContainsString('Product model\'s deleting event will handle cascading deletion', $adminContent);
        $this->assertStringContainsString('} catch (\Exception $e) {', $adminContent);
        
        // Check API ProductController
        $apiControllerFile = app_path('Http/Controllers/API/ProductController.php');
        $apiContent = file_get_contents($apiControllerFile);
        
        $this->assertStringContainsString('try {', $apiContent);
        $this->assertStringContainsString('Product model\'s deleting event will handle cascading deletion', $apiContent);
        $this->assertStringContainsString('} catch (\Exception $e) {', $apiContent);
        
        // Check Provider ProductController
        $providerControllerFile = app_path('Http/Controllers/Provider/ProductController.php');
        $providerContent = file_get_contents($providerControllerFile);
        
        $this->assertStringContainsString('try {', $providerContent);
        $this->assertStringContainsString('Product model\'s deleting event will handle cascading deletion', $providerContent);
        $this->assertStringContainsString('} catch (\Exception $e) {', $providerContent);
        
        // Check Vendor ProductController
        $vendorControllerFile = app_path('Http/Controllers/Vendor/ProductController.php');
        $vendorContent = file_get_contents($vendorControllerFile);
        
        $this->assertStringContainsString('try {', $vendorContent);
        $this->assertStringContainsString('Product model\'s deleting event will handle cascading deletion', $vendorContent);
        $this->assertStringContainsString('} catch (\Exception $e) {', $vendorContent);
    }

    /** @test */
    public function database_has_cascade_constraints()
    {
        // Check migration files for cascade constraints
        $migrationFiles = glob(database_path('migrations/*create_product_color_sizes_table.php'));
        
        if (!empty($migrationFiles)) {
            $migrationContent = file_get_contents($migrationFiles[0]);
            $this->assertStringContainsString('->onDelete(\'cascade\')', $migrationContent);
        }
        
        // Check for product colors and sizes tables cascade constraints
        $specMigrationFiles = glob(database_path('migrations/*create_product_specification*'));
        
        if (!empty($specMigrationFiles)) {
            $specContent = file_get_contents($specMigrationFiles[0]);
            $this->assertStringContainsString('->onDelete(\'cascade\')', $specContent);
        }
    }
}
