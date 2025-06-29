<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\EmailVerificationNotification;

class VendorRegistrationEmailVerificationTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function vendor_can_complete_email_verification_flow()
    {
        // Fake notifications to prevent actual email sending
        Notification::fake();

        $this->browse(function (Browser $browser) {
            $testEmail = 'vendor' . time() . '@example.com';
            
            // Step 1: Fill vendor basic information
            $browser->visit('/register/vendor/step1')
                    ->waitFor('#vendorForm')
                    ->type('name', 'Test Vendor')
                    ->type('email', $testEmail)
                    ->type('password', 'TestPassword123!')
                    ->type('password_confirmation', 'TestPassword123!')
                    ->type('phone', '+971501234567')
                    ->click('#submitBtn')
                    ->waitForText('Information validated successfully')
                    ->assertSee('Information validated successfully');

            // Step 2: Email verification
            $browser->visit('/register/vendor/step2')
                    ->waitFor('#sendEmailBtn')
                    ->assertSee('Email Verification')
                    ->click('#sendEmailBtn')
                    ->waitForText('Verification email sent!')
                    ->assertSee('Verification email sent!');

            // Verify that notification was sent
            Notification::assertSentTo(
                new \Illuminate\Notifications\AnonymousNotifiable,
                EmailVerificationNotification::class
            );

            // Get the verification token from database
            $tokenRecord = DB::table('email_verification_tokens')
                ->where('email', $testEmail)
                ->where('type', 'vendor_registration')
                ->first();

            $this->assertNotNull($tokenRecord, 'Verification token should be created');

            // Simulate clicking the verification link
            $verificationUrl = "/verify-email/{$tokenRecord->token}?type=vendor_registration";
            
            $browser->visit($verificationUrl)
                    ->waitForText('Email Verified Successfully!')
                    ->assertSee('Email Verified Successfully!')
                    ->assertSee('Continue Registration')
                    ->click('a[href*="step3"]');

            // Verify we're redirected to step 3
            $browser->waitForLocation('/register/vendor/step3')
                    ->assertPathIs('/register/vendor/step3')
                    ->assertSee('Company Information');

            // Verify token is marked as verified in database
            $verifiedToken = DB::table('email_verification_tokens')
                ->where('id', $tokenRecord->id)
                ->first();
            
            $this->assertNotNull($verifiedToken->verified_at, 'Token should be marked as verified');
        });
    }

    /** @test */
    public function vendor_can_check_verification_status()
    {
        Notification::fake();

        $this->browse(function (Browser $browser) {
            $testEmail = 'vendor' . time() . '@example.com';
            
            // Complete step 1
            $browser->visit('/register/vendor/step1')
                    ->waitFor('#vendorForm')
                    ->type('name', 'Test Vendor')
                    ->type('email', $testEmail)
                    ->type('password', 'TestPassword123!')
                    ->type('password_confirmation', 'TestPassword123!')
                    ->type('phone', '+971501234567')
                    ->click('#submitBtn')
                    ->waitForText('Information validated successfully');

            // Go to step 2 and send verification email
            $browser->visit('/register/vendor/step2')
                    ->waitFor('#sendEmailBtn')
                    ->click('#sendEmailBtn')
                    ->waitForText('Verification email sent!');

            // Get token and verify it manually (simulating user clicking email link)
            $tokenRecord = DB::table('email_verification_tokens')
                ->where('email', $testEmail)
                ->where('type', 'vendor_registration')
                ->first();

            // Mark token as verified
            DB::table('email_verification_tokens')
                ->where('id', $tokenRecord->id)
                ->update(['verified_at' => now()]);

            // Now check verification status
            $browser->click('#checkVerificationBtn')
                    ->waitForText('Email verified successfully!')
                    ->assertSee('Email verified successfully!')
                    ->assertVisible('#continueBtn');
        });
    }

    /** @test */
    public function invalid_verification_token_shows_error()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/verify-email/invalid-token?type=vendor_registration')
                    ->waitForText('Email Verification Failed')
                    ->assertSee('Email Verification Failed')
                    ->assertSee('The verification link is invalid or has expired')
                    ->assertSee('Try Again');
        });
    }

    /** @test */
    public function expired_verification_token_shows_error()
    {
        // Create an expired token
        $expiredToken = hash('sha256', 'expired-token-' . time());
        DB::table('email_verification_tokens')->insert([
            'email' => 'expired@example.com',
            'token' => $expiredToken,
            'type' => 'vendor_registration',
            'metadata' => json_encode(['name' => 'Test User']),
            'expires_at' => now()->subHours(2), // Expired 2 hours ago
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->browse(function (Browser $browser) use ($expiredToken) {
            $browser->visit("/verify-email/{$expiredToken}?type=vendor_registration")
                    ->waitForText('Email Verification Failed')
                    ->assertSee('Email Verification Failed')
                    ->assertSee('invalid or expired')
                    ->assertSee('Try Again');
        });
    }

    /** @test */
    public function vendor_cannot_access_step3_without_email_verification()
    {
        $this->browse(function (Browser $browser) {
            $testEmail = 'vendor' . time() . '@example.com';
            
            // Complete step 1 only
            $browser->visit('/register/vendor/step1')
                    ->waitFor('#vendorForm')
                    ->type('name', 'Test Vendor')
                    ->type('email', $testEmail)
                    ->type('password', 'TestPassword123!')
                    ->type('password_confirmation', 'TestPassword123!')
                    ->type('phone', '+971501234567')
                    ->click('#submitBtn')
                    ->waitForText('Information validated successfully');

            // Try to access step 3 directly without email verification
            $browser->visit('/register/vendor/step3')
                    ->assertPathIs('/register/vendor/step2'); // Should be redirected back to step 2
        });
    }
}
