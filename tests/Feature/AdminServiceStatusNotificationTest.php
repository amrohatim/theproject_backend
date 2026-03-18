<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Company;
use App\Models\Merchant;
use App\Models\Service;
use App\Models\User;
use App\Models\VendorNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminServiceStatusNotificationTest extends TestCase
{
    use RefreshDatabase;

    private function createAdminUser(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);
    }

    private function createServiceCategory(): Category
    {
        return Category::create([
            'name' => 'Service Category',
            'type' => 'service',
            'is_active' => true,
        ]);
    }

    private function createVendorOwnedService(string $status = 'pending'): Service
    {
        $vendorUser = User::factory()->create([
            'role' => 'vendor',
            'status' => 'active',
        ]);

        $company = Company::create([
            'user_id' => $vendorUser->id,
            'name' => 'Vendor Company',
            'status' => 'active',
        ]);

        $branch = Branch::create([
            'user_id' => $vendorUser->id,
            'company_id' => $company->id,
            'name' => 'Vendor Branch',
            'address' => 'Vendor Address',
            'lat' => 25.2048,
            'lng' => 55.2708,
            'status' => 'active',
        ]);

        return Service::create([
            'branch_id' => $branch->id,
            'category_id' => $this->createServiceCategory()->id,
            'name' => 'Vendor Service',
            'service_name_arabic' => 'خدمة بائع',
            'price' => 100,
            'duration' => 60,
            'status' => $status,
            'is_available' => true,
        ]);
    }

    private function createMerchantOwnedService(string $status = 'pending'): Service
    {
        $merchantUser = User::factory()->create([
            'role' => 'merchant',
            'status' => 'active',
        ]);

        $merchant = Merchant::create([
            'user_id' => $merchantUser->id,
            'business_name' => 'Merchant Business',
            'status' => 'active',
            'license_status' => 'verified',
            'license_verified' => true,
        ]);

        return Service::create([
            'branch_id' => null,
            'merchant_id' => $merchantUser->id,
            'merchant_name' => $merchant->business_name,
            'category_id' => $this->createServiceCategory()->id,
            'name' => 'Merchant Service',
            'service_name_arabic' => 'خدمة تاجر',
            'price' => 90,
            'duration' => 45,
            'status' => $status,
            'is_available' => true,
        ]);
    }

    public function test_admin_status_update_creates_vendor_notification_for_vendor_owned_service(): void
    {
        $admin = $this->createAdminUser();
        $service = $this->createVendorOwnedService('pending');
        $companyId = $service->branch->company_id;

        $response = $this->actingAs($admin)->put(route('admin.services.update', $service->id), [
            'featured' => 1,
            'status' => 'approved',
        ]);

        $response->assertRedirect(route('admin.services.index'));

        $this->assertDatabaseHas('vendor_notifications', [
            'notification_type' => VendorNotification::TYPE_SERVICE,
            'sender_name' => 'admin',
            'recipient_type' => VendorNotification::RECIPIENT_VENDOR,
            'recipient_id' => $companyId,
            'service_id' => $service->id,
            'is_opened' => false,
        ]);
    }

    public function test_admin_status_update_creates_merchant_notification_for_merchant_owned_service(): void
    {
        $admin = $this->createAdminUser();
        $service = $this->createMerchantOwnedService('pending');
        $merchantRecipientId = Merchant::where('user_id', $service->merchant_id)->value('id');

        $response = $this->actingAs($admin)->put(route('admin.services.update', $service->id), [
            'featured' => 0,
            'status' => 'rejected',
        ]);

        $response->assertRedirect(route('admin.services.index'));

        $this->assertDatabaseHas('vendor_notifications', [
            'notification_type' => VendorNotification::TYPE_SERVICE,
            'sender_name' => 'admin',
            'recipient_type' => VendorNotification::RECIPIENT_MERCHANT,
            'recipient_id' => $merchantRecipientId,
            'service_id' => $service->id,
            'is_opened' => false,
        ]);
    }

    public function test_admin_status_update_does_not_create_service_notification_when_status_is_unchanged(): void
    {
        $admin = $this->createAdminUser();
        $service = $this->createVendorOwnedService('approved');

        $response = $this->actingAs($admin)->put(route('admin.services.update', $service->id), [
            'featured' => 1,
            'status' => 'approved',
        ]);

        $response->assertRedirect(route('admin.services.index'));
        $this->assertDatabaseCount('vendor_notifications', 0);
    }
}
