<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Helpers\ImageHelper;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;

class ImageHelperTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock the config values
        Config::shouldReceive('get')
            ->with('app.url')
            ->andReturn('https://dala3chic.com');
    }

    /** @test */
    public function it_returns_null_for_null_input()
    {
        $result = ImageHelper::getFullImageUrl(null);
        $this->assertNull($result);
    }

    /** @test */
    public function it_returns_null_for_empty_string()
    {
        $result = ImageHelper::getFullImageUrl('');
        $this->assertNull($result);
    }

    /** @test */
    public function it_returns_full_url_for_absolute_url()
    {
        $absoluteUrl = 'https://example.com/image.jpg';
        $result = ImageHelper::getFullImageUrl($absoluteUrl);
        $this->assertEquals($absoluteUrl, $result);
    }

    /** @test */
    public function it_handles_products_path_correctly()
    {
        $imagePath = 'products/test_image.jpg';
        $result = ImageHelper::getFullImageUrl($imagePath);
        $this->assertEquals('https://dala3chic.com/storage/products/test_image.jpg', $result);
    }

    /** @test */
    public function it_handles_services_path_correctly()
    {
        $imagePath = 'services/test_service.png';
        $result = ImageHelper::getFullImageUrl($imagePath);
        $this->assertEquals('https://dala3chic.com/storage/services/test_service.png', $result);
    }

    /** @test */
    public function it_handles_storage_prefix_correctly()
    {
        $imagePath = 'storage/products/test_image.jpg';
        $result = ImageHelper::getFullImageUrl($imagePath);
        $this->assertEquals('https://dala3chic.com/storage/products/test_image.jpg', $result);
    }

    /** @test */
    public function it_handles_slash_storage_prefix_correctly()
    {
        $imagePath = '/storage/services/test_service.gif';
        $result = ImageHelper::getFullImageUrl($imagePath);
        $this->assertEquals('https://dala3chic.com/storage/services/test_service.gif', $result);
    }

    /** @test */
    public function it_normalizes_multiple_slashes()
    {
        $imagePath = '//storage//products//test_image.jpg';
        $result = ImageHelper::getFullImageUrl($imagePath);
        $this->assertEquals('https://dala3chic.com/storage/products/test_image.jpg', $result);
    }

    /** @test */
    public function it_handles_relative_paths_without_folder_prefix()
    {
        $imagePath = 'test_image.jpg';
        $result = ImageHelper::getFullImageUrl($imagePath);
        // Should return a valid URL even for files without folder prefix
        $this->assertStringStartsWith('https://dala3chic.com', $result);
        $this->assertStringContains('test_image.jpg', $result);
    }

    /** @test */
    public function it_preserves_file_extensions()
    {
        $testCases = [
            'products/image.jpg' => 'https://dala3chic.com/storage/products/image.jpg',
            'services/image.png' => 'https://dala3chic.com/storage/services/image.png',
            'products/image.gif' => 'https://dala3chic.com/storage/products/image.gif',
            'services/image.jpeg' => 'https://dala3chic.com/storage/services/image.jpeg',
        ];

        foreach ($testCases as $input => $expected) {
            $result = ImageHelper::getFullImageUrl($input);
            $this->assertEquals($expected, $result, "Failed for input: {$input}");
        }
    }

    /** @test */
    public function it_handles_filenames_with_special_characters()
    {
        $imagePath = 'products/test_image_123-abc.jpg';
        $result = ImageHelper::getFullImageUrl($imagePath);
        $this->assertEquals('https://dala3chic.com/storage/products/test_image_123-abc.jpg', $result);
    }

    /** @test */
    public function it_handles_unicode_filenames()
    {
        $imagePath = 'products/تست_صورة.jpg';
        $result = ImageHelper::getFullImageUrl($imagePath);
        $this->assertStringStartsWith('https://dala3chic.com/storage/products/', $result);
        $this->assertStringContains('تست_صورة.jpg', $result);
    }

    /** @test */
    public function it_handles_very_long_filenames()
    {
        $longFilename = str_repeat('a', 200) . '.jpg';
        $imagePath = "products/{$longFilename}";
        $result = ImageHelper::getFullImageUrl($imagePath);
        $this->assertStringStartsWith('https://dala3chic.com/storage/products/', $result);
        $this->assertStringContains($longFilename, $result);
    }

    /** @test */
    public function it_handles_nested_folder_structures()
    {
        $imagePath = 'products/category/subcategory/image.jpg';
        $result = ImageHelper::getFullImageUrl($imagePath);
        $this->assertEquals('https://dala3chic.com/storage/products/category/subcategory/image.jpg', $result);
    }

    /** @test */
    public function it_handles_case_sensitivity()
    {
        $testCases = [
            'Products/Image.JPG' => 'https://dala3chic.com/storage/Products/Image.JPG',
            'SERVICES/IMAGE.PNG' => 'https://dala3chic.com/storage/SERVICES/IMAGE.PNG',
        ];

        foreach ($testCases as $input => $expected) {
            $result = ImageHelper::getFullImageUrl($input);
            $this->assertEquals($expected, $result, "Failed for input: {$input}");
        }
    }

    /** @test */
    public function it_handles_query_parameters_in_urls()
    {
        $imagePath = 'products/image.jpg?v=123&size=large';
        $result = ImageHelper::getFullImageUrl($imagePath);
        $this->assertEquals('https://dala3chic.com/storage/products/image.jpg?v=123&size=large', $result);
    }

    /** @test */
    public function it_handles_fragments_in_urls()
    {
        $imagePath = 'services/image.png#section1';
        $result = ImageHelper::getFullImageUrl($imagePath);
        $this->assertEquals('https://dala3chic.com/storage/services/image.png#section1', $result);
    }

    /** @test */
    public function it_handles_mixed_slashes()
    {
        $imagePath = 'products\\image.jpg';
        $result = ImageHelper::getFullImageUrl($imagePath);
        // Should normalize backslashes to forward slashes
        $this->assertStringNotContains('\\', $result);
        $this->assertStringContains('products/image.jpg', $result);
    }
}
