<?php

namespace Tests\Feature;

use App\Http\Middleware\ProviderMiddleware;
use App\Models\Provider;
use App\Models\User;
use App\Models\VendorNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProviderNotificationsTest extends TestCase
{
    use RefreshDatabase;

    private function createProviderUser(bool $withProviderRecord = true, array $overrides = []): array
    {
        $user = User::factory()->create(array_merge([
            'role' => 'provider',
            'status' => 'active',
            'registration_step' => 'verified',
            'phone_verified_at' => now(),
            'email_verified_at' => now(),
        ], $overrides));

        $provider = null;
        if ($withProviderRecord) {
            $provider = Provider::create([
                'user_id' => $user->id,
                'business_name' => 'Provider ' . $user->id,
                'status' => 'active',
                'is_verified' => true,
            ]);
        }

        return [$user, $provider];
    }

    public function test_provider_full_list_shows_only_its_notifications(): void
    {
        [$providerUser, $provider] = $this->createProviderUser();
        [, $otherProvider] = $this->createProviderUser(true, ['email' => 'other-provider@example.com']);

        VendorNotification::create([
            'notification_type' => VendorNotification::TYPE_ORDER,
            'sender_name' => 'admin',
            'message' => 'Provider visible notification',
            'message_arabic' => 'اشعار مرئي لمقدم الخدمة',
            'recipient_type' => VendorNotification::RECIPIENT_PROVIDER,
            'recipient_id' => $provider->id,
            'is_opened' => false,
        ]);

        VendorNotification::create([
            'notification_type' => VendorNotification::TYPE_ORDER,
            'sender_name' => 'admin',
            'message' => 'Other provider hidden notification',
            'message_arabic' => 'اشعار مخفي لمقدم خدمة اخر',
            'recipient_type' => VendorNotification::RECIPIENT_PROVIDER,
            'recipient_id' => $otherProvider->id,
            'is_opened' => false,
        ]);

        $response = $this->withoutMiddleware(ProviderMiddleware::class)
            ->actingAs($providerUser)
            ->get(route('provider.notifications.index'));

        $response->assertOk();
        $response->assertSee('Provider visible notification');
        $response->assertDontSee('Other provider hidden notification');
    }

    public function test_opening_provider_full_list_marks_only_provider_notifications_as_opened(): void
    {
        [$providerUser, $provider] = $this->createProviderUser();
        [, $otherProvider] = $this->createProviderUser(true, ['email' => 'another-provider@example.com']);

        $targetNotification = VendorNotification::create([
            'notification_type' => VendorNotification::TYPE_PRODUCT,
            'sender_name' => 'admin',
            'message' => 'Provider unread notification',
            'message_arabic' => 'اشعار غير مقروء لمقدم الخدمة',
            'recipient_type' => VendorNotification::RECIPIENT_PROVIDER,
            'recipient_id' => $provider->id,
            'is_opened' => false,
        ]);

        $otherNotification = VendorNotification::create([
            'notification_type' => VendorNotification::TYPE_PRODUCT,
            'sender_name' => 'admin',
            'message' => 'Other provider unread notification',
            'message_arabic' => 'اشعار غير مقروء لمقدم خدمة اخر',
            'recipient_type' => VendorNotification::RECIPIENT_PROVIDER,
            'recipient_id' => $otherProvider->id,
            'is_opened' => false,
        ]);

        $this->withoutMiddleware(ProviderMiddleware::class)
            ->actingAs($providerUser)
            ->get(route('provider.notifications.index'))
            ->assertOk();

        $this->assertDatabaseHas('vendor_notifications', [
            'id' => $targetNotification->id,
            'is_opened' => true,
        ]);

        $this->assertDatabaseHas('vendor_notifications', [
            'id' => $otherNotification->id,
            'is_opened' => false,
        ]);
    }

    public function test_provider_without_profile_gets_safe_empty_state(): void
    {
        [$providerUser] = $this->createProviderUser(false);

        $response = $this->withoutMiddleware(ProviderMiddleware::class)
            ->actingAs($providerUser)
            ->get(route('provider.notifications.index'));

        $response->assertOk();
        $response->assertSee(__('messages.no_items_found'));
    }

    public function test_provider_dashboard_topbar_preview_is_limited_to_ten_latest(): void
    {
        [$providerUser, $provider] = $this->createProviderUser();

        for ($i = 1; $i <= 11; $i++) {
            $message = $i === 1 ? 'Provider preview oldest unique' : 'Provider preview ' . $i;

            VendorNotification::create([
                'notification_type' => VendorNotification::TYPE_ORDER,
                'sender_name' => 'admin',
                'message' => $message,
                'message_arabic' => 'معاينة مقدم الخدمة ' . $i,
                'recipient_type' => VendorNotification::RECIPIENT_PROVIDER,
                'recipient_id' => $provider->id,
                'is_opened' => false,
                'created_at' => now()->addSeconds($i),
                'updated_at' => now()->addSeconds($i),
            ]);
        }

        $response = $this->withoutMiddleware(ProviderMiddleware::class)
            ->actingAs($providerUser)
            ->get(route('provider.dashboard'));

        $response->assertOk();
        $response->assertSee('fa-bell', false);
        $response->assertSee('Provider preview 11');
        $response->assertDontSee('Provider preview oldest unique');
    }
}
