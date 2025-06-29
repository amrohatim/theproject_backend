<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use App\Services\EmailVerificationService;
use App\Notifications\EmailVerificationNotification;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected $emailVerificationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->emailVerificationService = new EmailVerificationService();
    }

    /** @test */
    public function it_can_send_verification_email()
    {
        // Fake notifications to prevent actual email sending
        Notification::fake();

        $email = 'test@example.com';
        $type = 'vendor_registration';
        $metadata = ['name' => 'Test User', 'phone' => '+971501234567'];

        $result = $this->emailVerificationService->sendVerificationEmail($email, $type, $metadata);

        $this->assertTrue($result['success']);
        $this->assertEquals('Verification email sent successfully', $result['message']);

        // Check that token was stored in database
        $this->assertDatabaseHas('email_verification_tokens', [
            'email' => $email,
            'type' => $type,
        ]);

        // Check that notification was sent
        Notification::assertSentTo(
            new \Illuminate\Notifications\AnonymousNotifiable,
            EmailVerificationNotification::class
        );
    }

    /** @test */
    public function it_can_verify_token()
    {
        // First send verification email
        Notification::fake();
        
        $email = 'test@example.com';
        $type = 'vendor_registration';
        
        $this->emailVerificationService->sendVerificationEmail($email, $type);
        
        // Get the token from database
        $tokenRecord = DB::table('email_verification_tokens')
            ->where('email', $email)
            ->where('type', $type)
            ->first();

        $this->assertNotNull($tokenRecord);

        // Verify the token
        $result = $this->emailVerificationService->verifyToken($tokenRecord->token, $type);

        $this->assertTrue($result['success']);
        $this->assertTrue($result['verified']);
        $this->assertEquals($email, $result['email']);

        // Check that token is marked as verified
        $this->assertDatabaseHas('email_verification_tokens', [
            'id' => $tokenRecord->id,
            'email' => $email,
            'type' => $type,
        ]);

        $updatedRecord = DB::table('email_verification_tokens')->find($tokenRecord->id);
        $this->assertNotNull($updatedRecord->verified_at);
    }

    /** @test */
    public function it_rejects_invalid_token()
    {
        $result = $this->emailVerificationService->verifyToken('invalid-token', 'vendor_registration');

        $this->assertFalse($result['success']);
        $this->assertFalse($result['verified']);
        $this->assertEquals('Invalid or expired verification token', $result['message']);
    }

    /** @test */
    public function it_can_check_verification_status()
    {
        Notification::fake();
        
        $email = 'test@example.com';
        $type = 'vendor_registration';
        
        // Initially not verified
        $status = $this->emailVerificationService->getVerificationStatus($email, $type);
        $this->assertFalse($status['verified']);
        $this->assertFalse($status['token_sent']);

        // Send verification email
        $this->emailVerificationService->sendVerificationEmail($email, $type);
        
        $status = $this->emailVerificationService->getVerificationStatus($email, $type);
        $this->assertFalse($status['verified']);
        $this->assertTrue($status['token_sent']);

        // Verify email
        $tokenRecord = DB::table('email_verification_tokens')
            ->where('email', $email)
            ->where('type', $type)
            ->first();
        
        $this->emailVerificationService->verifyToken($tokenRecord->token, $type);
        
        $status = $this->emailVerificationService->getVerificationStatus($email, $type);
        $this->assertTrue($status['verified']);
        $this->assertTrue($status['token_sent']);
    }

    /** @test */
    public function it_cleans_up_old_tokens_when_sending_new_one()
    {
        Notification::fake();
        
        $email = 'test@example.com';
        $type = 'vendor_registration';
        
        // Send first verification email
        $this->emailVerificationService->sendVerificationEmail($email, $type);
        
        $firstCount = DB::table('email_verification_tokens')
            ->where('email', $email)
            ->where('type', $type)
            ->count();
        
        $this->assertEquals(1, $firstCount);
        
        // Send second verification email (should clean up first one)
        $this->emailVerificationService->sendVerificationEmail($email, $type);
        
        $secondCount = DB::table('email_verification_tokens')
            ->where('email', $email)
            ->where('type', $type)
            ->count();
        
        $this->assertEquals(1, $secondCount);
    }

    /** @test */
    public function it_can_clean_up_expired_tokens()
    {
        // Create an expired token manually
        DB::table('email_verification_tokens')->insert([
            'email' => 'expired@example.com',
            'token' => 'expired-token',
            'type' => 'vendor_registration',
            'metadata' => json_encode([]),
            'expires_at' => now()->subHours(2), // Expired 2 hours ago
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assertDatabaseHas('email_verification_tokens', [
            'email' => 'expired@example.com',
        ]);

        $deletedCount = $this->emailVerificationService->cleanupExpiredTokens();
        
        $this->assertEquals(1, $deletedCount);
        
        $this->assertDatabaseMissing('email_verification_tokens', [
            'email' => 'expired@example.com',
        ]);
    }
}
