<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Merchant;
use App\Models\SubscriptionType;
use App\Models\MerchantSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class MerchantSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $merchant;
    protected $subscriptionType;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user
        $this->user = User::factory()->create([
            'role' => 'merchant',
            'email' => 'test@merchant.com',
        ]);

        // Create a merchant
        $this->merchant = Merchant::create([
            'user_id' => $this->user->id,
            'business_name' => 'Test Merchant',
            'status' => 'active',
        ]);

        // Create a subscription type
        $this->subscriptionType = SubscriptionType::create([
            'type' => 'merchant',
            'period' => 'monthly',
            'charge' => 99.99,
            'title' => 'Monthly Merchant Plan',
            'description' => 'Monthly subscription for merchants',
        ]);
    }

    /** @test */
    public function it_can_create_a_merchant_subscription()
    {
        $subscription = MerchantSubscription::create([
            'subscription_type_id' => $this->subscriptionType->id,
            'merchant_id' => $this->merchant->id,
            'status' => 'active',
            'start_at' => Carbon::now(),
            'end_at' => Carbon::now()->addMonth(),
        ]);

        $this->assertDatabaseHas('merchant_subscriptions', [
            'merchant_id' => $this->merchant->id,
            'subscription_type_id' => $this->subscriptionType->id,
            'status' => 'active',
        ]);

        $this->assertEquals('active', $subscription->status);
    }

    /** @test */
    public function it_has_relationship_with_subscription_type()
    {
        $subscription = MerchantSubscription::create([
            'subscription_type_id' => $this->subscriptionType->id,
            'merchant_id' => $this->merchant->id,
            'status' => 'active',
            'start_at' => Carbon::now(),
            'end_at' => Carbon::now()->addMonth(),
        ]);

        $this->assertInstanceOf(SubscriptionType::class, $subscription->subscriptionType);
        $this->assertEquals($this->subscriptionType->id, $subscription->subscriptionType->id);
    }

    /** @test */
    public function it_has_relationship_with_merchant()
    {
        $subscription = MerchantSubscription::create([
            'subscription_type_id' => $this->subscriptionType->id,
            'merchant_id' => $this->merchant->id,
            'status' => 'active',
            'start_at' => Carbon::now(),
            'end_at' => Carbon::now()->addMonth(),
        ]);

        $this->assertInstanceOf(Merchant::class, $subscription->merchant);
        $this->assertEquals($this->merchant->id, $subscription->merchant->id);
    }

    /** @test */
    public function merchant_has_subscriptions_relationship()
    {
        MerchantSubscription::create([
            'subscription_type_id' => $this->subscriptionType->id,
            'merchant_id' => $this->merchant->id,
            'status' => 'active',
            'start_at' => Carbon::now(),
            'end_at' => Carbon::now()->addMonth(),
        ]);

        $this->assertCount(1, $this->merchant->subscriptions);
        $this->assertInstanceOf(MerchantSubscription::class, $this->merchant->subscriptions->first());
    }

    /** @test */
    public function it_formats_dates_correctly()
    {
        $startDate = Carbon::create(2025, 1, 15);
        $endDate = Carbon::create(2025, 2, 15);

        $subscription = MerchantSubscription::create([
            'subscription_type_id' => $this->subscriptionType->id,
            'merchant_id' => $this->merchant->id,
            'status' => 'active',
            'start_at' => $startDate,
            'end_at' => $endDate,
        ]);

        $this->assertEquals('15-01-2025', $subscription->formatted_start_date);
        $this->assertEquals('15-02-2025', $subscription->formatted_end_date);
    }

    /** @test */
    public function it_can_check_if_subscription_is_expired()
    {
        $expiredSubscription = MerchantSubscription::create([
            'subscription_type_id' => $this->subscriptionType->id,
            'merchant_id' => $this->merchant->id,
            'status' => 'active',
            'start_at' => Carbon::now()->subMonths(2),
            'end_at' => Carbon::now()->subMonth(),
        ]);

        $activeSubscription = MerchantSubscription::create([
            'subscription_type_id' => $this->subscriptionType->id,
            'merchant_id' => $this->merchant->id,
            'status' => 'active',
            'start_at' => Carbon::now(),
            'end_at' => Carbon::now()->addMonth(),
        ]);

        $this->assertTrue($expiredSubscription->isExpired());
        $this->assertFalse($activeSubscription->isExpired());
    }

    /** @test */
    public function it_can_check_if_subscription_is_expiring_soon()
    {
        $expiringSoonSubscription = MerchantSubscription::create([
            'subscription_type_id' => $this->subscriptionType->id,
            'merchant_id' => $this->merchant->id,
            'status' => 'active',
            'start_at' => Carbon::now()->subMonth(),
            'end_at' => Carbon::now()->addDays(5),
        ]);

        $notExpiringSoonSubscription = MerchantSubscription::create([
            'subscription_type_id' => $this->subscriptionType->id,
            'merchant_id' => $this->merchant->id,
            'status' => 'active',
            'start_at' => Carbon::now(),
            'end_at' => Carbon::now()->addMonth(),
        ]);

        $this->assertTrue($expiringSoonSubscription->isExpiringSoon());
        $this->assertFalse($notExpiringSoonSubscription->isExpiringSoon());
    }

    /** @test */
    public function it_can_scope_active_subscriptions()
    {
        MerchantSubscription::create([
            'subscription_type_id' => $this->subscriptionType->id,
            'merchant_id' => $this->merchant->id,
            'status' => 'active',
            'start_at' => Carbon::now(),
            'end_at' => Carbon::now()->addMonth(),
        ]);

        MerchantSubscription::create([
            'subscription_type_id' => $this->subscriptionType->id,
            'merchant_id' => $this->merchant->id,
            'status' => 'inactive',
            'start_at' => Carbon::now()->subMonth(),
            'end_at' => Carbon::now()->addMonth(),
        ]);

        $activeSubscriptions = MerchantSubscription::active()->get();
        $this->assertCount(1, $activeSubscriptions);
        $this->assertEquals('active', $activeSubscriptions->first()->status);
    }

    /** @test */
    public function it_can_scope_expired_subscriptions()
    {
        MerchantSubscription::create([
            'subscription_type_id' => $this->subscriptionType->id,
            'merchant_id' => $this->merchant->id,
            'status' => 'active',
            'start_at' => Carbon::now()->subMonths(2),
            'end_at' => Carbon::now()->subMonth(),
        ]);

        MerchantSubscription::create([
            'subscription_type_id' => $this->subscriptionType->id,
            'merchant_id' => $this->merchant->id,
            'status' => 'active',
            'start_at' => Carbon::now(),
            'end_at' => Carbon::now()->addMonth(),
        ]);

        $expiredSubscriptions = MerchantSubscription::expired()->get();
        $this->assertCount(1, $expiredSubscriptions);
    }

    /** @test */
    public function it_returns_correct_status_color()
    {
        $activeSubscription = MerchantSubscription::create([
            'subscription_type_id' => $this->subscriptionType->id,
            'merchant_id' => $this->merchant->id,
            'status' => 'active',
            'start_at' => Carbon::now(),
            'end_at' => Carbon::now()->addMonth(),
        ]);

        $inactiveSubscription = MerchantSubscription::create([
            'subscription_type_id' => $this->subscriptionType->id,
            'merchant_id' => $this->merchant->id,
            'status' => 'inactive',
            'start_at' => Carbon::now(),
            'end_at' => Carbon::now()->addMonth(),
        ]);

        $cancelledSubscription = MerchantSubscription::create([
            'subscription_type_id' => $this->subscriptionType->id,
            'merchant_id' => $this->merchant->id,
            'status' => 'cancelled',
            'start_at' => Carbon::now(),
            'end_at' => Carbon::now()->addMonth(),
        ]);

        $this->assertEquals('green', $activeSubscription->status_color);
        $this->assertEquals('yellow', $inactiveSubscription->status_color);
        $this->assertEquals('red', $cancelledSubscription->status_color);
    }

    /** @test */
    public function it_cascades_delete_when_merchant_is_deleted()
    {
        $subscription = MerchantSubscription::create([
            'subscription_type_id' => $this->subscriptionType->id,
            'merchant_id' => $this->merchant->id,
            'status' => 'active',
            'start_at' => Carbon::now(),
            'end_at' => Carbon::now()->addMonth(),
        ]);

        $subscriptionId = $subscription->id;

        $this->merchant->delete();

        $this->assertDatabaseMissing('merchant_subscriptions', [
            'id' => $subscriptionId,
        ]);
    }

    /** @test */
    public function it_cascades_delete_when_subscription_type_is_deleted()
    {
        $subscription = MerchantSubscription::create([
            'subscription_type_id' => $this->subscriptionType->id,
            'merchant_id' => $this->merchant->id,
            'status' => 'active',
            'start_at' => Carbon::now(),
            'end_at' => Carbon::now()->addMonth(),
        ]);

        $subscriptionId = $subscription->id;

        $this->subscriptionType->delete();

        $this->assertDatabaseMissing('merchant_subscriptions', [
            'id' => $subscriptionId,
        ]);
    }
}

