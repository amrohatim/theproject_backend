<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\ProductsManager;
use App\Models\User;
use App\Models\VendorNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductsManagerNotificationsTest extends TestCase
{
    use RefreshDatabase;

    private function createProductsManagerUserForCompany(Company $company, array $overrides = []): User
    {
        $user = User::factory()->create(array_merge([
            'role' => 'products_manager',
            'status' => 'active',
            'registration_step' => 'verified',
        ], $overrides));

        ProductsManager::create([
            'user_id' => $user->id,
            'company_id' => $company->id,
        ]);

        return $user;
    }

    public function test_products_manager_sees_only_company_vendor_notifications(): void
    {
        $vendorOwner = User::factory()->create(['role' => 'vendor', 'status' => 'active']);
        $company = Company::create(['user_id' => $vendorOwner->id, 'name' => 'Company A', 'status' => 'active']);

        $otherVendor = User::factory()->create(['role' => 'vendor', 'status' => 'active']);
        $otherCompany = Company::create(['user_id' => $otherVendor->id, 'name' => 'Company B', 'status' => 'active']);

        $productsManagerUser = $this->createProductsManagerUserForCompany($company);

        VendorNotification::create([
            'notification_type' => VendorNotification::TYPE_PRODUCT,
            'sender_name' => 'admin',
            'message' => 'Visible PM notification',
            'message_arabic' => 'اشعار مدير منتجات مرئي',
            'recipient_type' => VendorNotification::RECIPIENT_VENDOR,
            'recipient_id' => $company->id,
            'is_opened' => false,
        ]);

        VendorNotification::create([
            'notification_type' => VendorNotification::TYPE_PRODUCT,
            'sender_name' => 'admin',
            'message' => 'Hidden PM notification',
            'message_arabic' => 'اشعار مدير منتجات مخفي',
            'recipient_type' => VendorNotification::RECIPIENT_VENDOR,
            'recipient_id' => $otherCompany->id,
            'is_opened' => false,
        ]);

        $response = $this->actingAs($productsManagerUser)->get(route('products-manager.notifications.index'));

        $response->assertOk();
        $response->assertSee('Visible PM notification');
        $response->assertDontSee('Hidden PM notification');
    }

    public function test_products_manager_notifications_are_marked_read_per_user_via_receipts(): void
    {
        $vendorOwner = User::factory()->create(['role' => 'vendor', 'status' => 'active']);
        $company = Company::create(['user_id' => $vendorOwner->id, 'name' => 'Company A', 'status' => 'active']);

        $productsManagerUser = $this->createProductsManagerUserForCompany($company);
        $otherProductsManagerUser = $this->createProductsManagerUserForCompany($company, ['email' => 'pm-other@example.com']);

        $notification = VendorNotification::create([
            'notification_type' => VendorNotification::TYPE_SERVICE,
            'sender_name' => 'admin',
            'message' => 'Per-user PM read receipt',
            'message_arabic' => 'إيصال قراءة لكل مستخدم',
            'recipient_type' => VendorNotification::RECIPIENT_VENDOR,
            'recipient_id' => $company->id,
            'is_opened' => false,
        ]);

        $this->actingAs($productsManagerUser)->get(route('products-manager.notifications.index'))->assertOk();

        $this->assertDatabaseHas('vendor_notification_reads', [
            'vendor_notification_id' => $notification->id,
            'user_id' => $productsManagerUser->id,
        ]);

        $this->assertDatabaseMissing('vendor_notification_reads', [
            'vendor_notification_id' => $notification->id,
            'user_id' => $otherProductsManagerUser->id,
        ]);
    }
}
