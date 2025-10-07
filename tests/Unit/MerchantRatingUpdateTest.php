<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Merchant;
use App\Models\Review;
use App\Http\Controllers\API\ReviewController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Mockery;

class MerchantRatingUpdateTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_updates_merchant_with_correct_column_names()
    {
        // Mock the Review model to return specific values
        $reviewMock = Mockery::mock('alias:' . Review::class);
        $reviewMock->shouldReceive('where')
            ->with('reviewable_type', Merchant::class)
            ->andReturnSelf();
        $reviewMock->shouldReceive('where')
            ->with('reviewable_id', 1)
            ->andReturnSelf();
        $reviewMock->shouldReceive('avg')
            ->with('rating')
            ->andReturn(4.5);
        $reviewMock->shouldReceive('count')
            ->andReturn(3);

        // Mock the Merchant model
        $merchantMock = Mockery::mock(Merchant::class);
        $merchantMock->id = 1;
        $merchantMock->shouldReceive('update')
            ->once()
            ->with([
                'average_rating' => 4.5,
                'total_ratings' => 3,
            ])
            ->andReturn(true);

        // Create a reflection of the ReviewController to access private method
        $controller = new ReviewController();
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('updateAverageRating');
        $method->setAccessible(true);

        // Call the private method
        $method->invoke($controller, $merchantMock);

        // The test passes if no exceptions are thrown and the mock expectations are met
        $this->assertTrue(true);
    }

    /** @test */
    public function it_handles_different_model_types_correctly()
    {
        // Test with a mock Product model (should use 'rating' column)
        $productMock = Mockery::mock('App\Models\Product');
        $productMock->id = 1;
        $productMock->shouldReceive('getFillable')
            ->andReturn(['name', 'price', 'rating', 'description']);
        $productMock->shouldReceive('update')
            ->once()
            ->with(['rating' => 3.8])
            ->andReturn(true);

        // Mock Review queries for Product
        $reviewMock = Mockery::mock('alias:' . Review::class);
        $reviewMock->shouldReceive('where')
            ->with('reviewable_type', get_class($productMock))
            ->andReturnSelf();
        $reviewMock->shouldReceive('where')
            ->with('reviewable_id', 1)
            ->andReturnSelf();
        $reviewMock->shouldReceive('avg')
            ->with('rating')
            ->andReturn(3.8);
        $reviewMock->shouldReceive('count')
            ->andReturn(2);

        // Create a reflection of the ReviewController to access private method
        $controller = new ReviewController();
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('updateAverageRating');
        $method->setAccessible(true);

        // Call the private method
        $method->invoke($controller, $productMock);

        // The test passes if no exceptions are thrown and the mock expectations are met
        $this->assertTrue(true);
    }

    /** @test */
    public function it_handles_models_with_total_ratings_column()
    {
        // Test with a mock model that has total_ratings in fillable
        $modelMock = Mockery::mock('App\Models\Service');
        $modelMock->id = 1;
        $modelMock->shouldReceive('getFillable')
            ->andReturn(['name', 'price', 'rating', 'total_ratings', 'description']);
        $modelMock->shouldReceive('update')
            ->once()
            ->with([
                'rating' => 4.2,
                'total_ratings' => 5,
            ])
            ->andReturn(true);

        // Mock Review queries
        $reviewMock = Mockery::mock('alias:' . Review::class);
        $reviewMock->shouldReceive('where')
            ->with('reviewable_type', get_class($modelMock))
            ->andReturnSelf();
        $reviewMock->shouldReceive('where')
            ->with('reviewable_id', 1)
            ->andReturnSelf();
        $reviewMock->shouldReceive('avg')
            ->with('rating')
            ->andReturn(4.2);
        $reviewMock->shouldReceive('count')
            ->andReturn(5);

        // Create a reflection of the ReviewController to access private method
        $controller = new ReviewController();
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('updateAverageRating');
        $method->setAccessible(true);

        // Call the private method
        $method->invoke($controller, $modelMock);

        // The test passes if no exceptions are thrown and the mock expectations are met
        $this->assertTrue(true);
    }
}
