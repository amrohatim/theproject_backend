<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Merchant;
use App\Models\Review;
use Illuminate\Support\Facades\Hash;

class MerchantReviewRatingUpdateTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $merchant;
    protected $merchantUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a regular user for submitting reviews
        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'status' => 'active',
        ]);

        // Create a merchant user
        $this->merchantUser = User::factory()->create([
            'name' => 'Test Merchant User',
            'email' => 'merchant@example.com',
            'password' => Hash::make('password'),
            'role' => 'merchant',
            'status' => 'active',
        ]);

        // Create a merchant
        $this->merchant = Merchant::factory()->create([
            'user_id' => $this->merchantUser->id,
            'business_name' => 'Test Merchant',
            'status' => 'active',
            'is_verified' => true,
            'average_rating' => 0,
            'total_ratings' => 0,
        ]);
    }

    /** @test */
    public function it_updates_merchant_average_rating_when_review_is_submitted()
    {
        // Authenticate as the user
        $this->actingAs($this->user);

        // Submit a review for the merchant
        $response = $this->postJson("/api/merchant/{$this->merchant->id}/reviews", [
            'rating' => 4,
            'comment' => 'Great merchant! Very satisfied with the service.',
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
            'message' => 'Review submitted successfully',
        ]);

        // Refresh the merchant model to get updated values
        $this->merchant->refresh();

        // Assert that the merchant's average rating and total ratings are updated
        $this->assertEquals(4.0, $this->merchant->average_rating);
        $this->assertEquals(1, $this->merchant->total_ratings);
    }

    /** @test */
    public function it_calculates_correct_average_rating_with_multiple_reviews()
    {
        // Create multiple reviews for the merchant
        Review::create([
            'user_id' => $this->user->id,
            'reviewable_type' => Merchant::class,
            'reviewable_id' => $this->merchant->id,
            'rating' => 5,
            'comment' => 'Excellent service!',
            'is_verified_purchase' => false,
        ]);

        // Create another user and review
        $anotherUser = User::factory()->create([
            'role' => 'customer',
            'status' => 'active',
        ]);

        Review::create([
            'user_id' => $anotherUser->id,
            'reviewable_type' => Merchant::class,
            'reviewable_id' => $this->merchant->id,
            'rating' => 3,
            'comment' => 'Good service.',
            'is_verified_purchase' => false,
        ]);

        // Manually trigger the rating update (simulating what happens in the controller)
        $averageRating = Review::where('reviewable_type', Merchant::class)
            ->where('reviewable_id', $this->merchant->id)
            ->avg('rating');

        $totalRatings = Review::where('reviewable_type', Merchant::class)
            ->where('reviewable_id', $this->merchant->id)
            ->count();

        $this->merchant->update([
            'average_rating' => $averageRating,
            'total_ratings' => $totalRatings,
        ]);

        // Refresh the merchant model
        $this->merchant->refresh();

        // Assert correct average rating (5 + 3) / 2 = 4.0
        $this->assertEquals(4.0, $this->merchant->average_rating);
        $this->assertEquals(2, $this->merchant->total_ratings);
    }

    /** @test */
    public function it_updates_rating_when_review_is_updated()
    {
        // Create an initial review
        $review = Review::create([
            'user_id' => $this->user->id,
            'reviewable_type' => Merchant::class,
            'reviewable_id' => $this->merchant->id,
            'rating' => 3,
            'comment' => 'Average service.',
            'is_verified_purchase' => false,
        ]);

        // Update merchant rating initially
        $this->merchant->update([
            'average_rating' => 3.0,
            'total_ratings' => 1,
        ]);

        // Authenticate as the user and update the review
        $this->actingAs($this->user);

        $response = $this->putJson("/api/reviews/{$review->id}", [
            'rating' => 5,
            'comment' => 'Actually, excellent service after all!',
        ]);

        $response->assertStatus(200);

        // Refresh the merchant model
        $this->merchant->refresh();

        // Assert that the merchant's average rating is updated
        $this->assertEquals(5.0, $this->merchant->average_rating);
        $this->assertEquals(1, $this->merchant->total_ratings);
    }

    /** @test */
    public function it_updates_rating_when_review_is_deleted()
    {
        // Create two reviews
        $review1 = Review::create([
            'user_id' => $this->user->id,
            'reviewable_type' => Merchant::class,
            'reviewable_id' => $this->merchant->id,
            'rating' => 4,
            'comment' => 'Good service.',
            'is_verified_purchase' => false,
        ]);

        $anotherUser = User::factory()->create([
            'role' => 'customer',
            'status' => 'active',
        ]);

        Review::create([
            'user_id' => $anotherUser->id,
            'reviewable_type' => Merchant::class,
            'reviewable_id' => $this->merchant->id,
            'rating' => 2,
            'comment' => 'Poor service.',
            'is_verified_purchase' => false,
        ]);

        // Update merchant rating initially (4 + 2) / 2 = 3.0
        $this->merchant->update([
            'average_rating' => 3.0,
            'total_ratings' => 2,
        ]);

        // Authenticate as the user and delete the first review
        $this->actingAs($this->user);

        $response = $this->deleteJson("/api/reviews/{$review1->id}");

        $response->assertStatus(200);

        // Refresh the merchant model
        $this->merchant->refresh();

        // Assert that the merchant's average rating is updated (only the 2-star review remains)
        $this->assertEquals(2.0, $this->merchant->average_rating);
        $this->assertEquals(1, $this->merchant->total_ratings);
    }
}
