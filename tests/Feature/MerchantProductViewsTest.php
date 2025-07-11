<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class MerchantProductViewsTest extends TestCase
{
    use WithoutMiddleware;

    public function test_merchant_product_create_view_compiles()
    {
        // Test that the enhanced create view compiles without errors
        $view = view('merchant.products.create', [
            'parentCategories' => collect([]),
            'branches' => collect([])
        ])->with('errors', new \Illuminate\Support\ViewErrorBag());

        $this->assertStringContainsString('Add New Product', $view->render());
        $this->assertStringContainsString('Product Colors, Images, and Sizes', $view->render());
        $this->assertStringContainsString('Product Specifications', $view->render());
    }

    public function test_merchant_product_edit_view_compiles()
    {
        // Create a mock product object
        $product = (object) [
            'id' => 1,
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 99.99,
            'original_price' => null,
            'stock' => 10,
            'category_id' => 1,
            'branch_id' => 1,
            'is_available' => true,
            'colors' => collect([]),
            'specifications' => collect([])
        ];

        // Test that the enhanced edit view compiles without errors
        $view = view('merchant.products.edit', [
            'product' => $product,
            'parentCategories' => collect([]),
            'branches' => collect([])
        ])->with('errors', new \Illuminate\Support\ViewErrorBag());

        $this->assertStringContainsString('Edit Product: Test Product', $view->render());
        $this->assertStringContainsString('Product Colors, Images, and Sizes', $view->render());
        $this->assertStringContainsString('Product Specifications', $view->render());
    }

    public function test_enhanced_views_include_required_javascript()
    {
        $createView = view('merchant.products.create', [
            'parentCategories' => collect([]),
            'branches' => collect([])
        ])->with('errors', new \Illuminate\Support\ViewErrorBag())->render();

        $editView = view('merchant.products.edit', [
            'product' => (object) [
                'id' => 1,
                'name' => 'Test Product',
                'description' => 'Test Description',
                'price' => 99.99,
                'original_price' => null,
                'stock' => 10,
                'category_id' => 1,
                'branch_id' => 1,
                'is_available' => true,
                'colors' => collect([]),
                'specifications' => collect([])
            ],
            'parentCategories' => collect([]),
            'branches' => collect([])
        ])->with('errors', new \Illuminate\Support\ViewErrorBag())->render();

        // Check for JavaScript includes
        $this->assertStringContainsString('color-picker.js', $createView);
        $this->assertStringContainsString('dynamic-color-size-management.js', $createView);
        $this->assertStringContainsString('color-picker.js', $editView);
        $this->assertStringContainsString('dynamic-color-size-management.js', $editView);
    }

    public function test_enhanced_views_include_color_management_sections()
    {
        $createView = view('merchant.products.create', [
            'parentCategories' => collect([]),
            'branches' => collect([])
        ])->with('errors', new \Illuminate\Support\ViewErrorBag())->render();

        // Check for color management sections
        $this->assertStringContainsString('colors-container', $createView);
        $this->assertStringContainsString('add-color', $createView);
        $this->assertStringContainsString('Color Name', $createView);
        $this->assertStringContainsString('Color Image', $createView);
        $this->assertStringContainsString('Default Color', $createView);
    }

    public function test_enhanced_views_include_specifications_section()
    {
        $createView = view('merchant.products.create', [
            'parentCategories' => collect([]),
            'branches' => collect([])
        ])->with('errors', new \Illuminate\Support\ViewErrorBag())->render();

        // Check for specifications section
        $this->assertStringContainsString('specifications-container', $createView);
        $this->assertStringContainsString('add-specification', $createView);
        $this->assertStringContainsString('Product Specifications', $createView);
    }
}
