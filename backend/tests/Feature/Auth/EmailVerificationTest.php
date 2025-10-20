<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_resend_verification_email()
    {
        $user = $this->createAuthenticatedUser();

        $response = $this->postJson('/api/email/verification-notification');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Verification email sent'
            ]);
    }

    public function test_unauthenticated_user_cannot_resend_verification_email()
    {
        $response = $this->postJson('/api/email/verification-notification');

        $response->assertStatus(401);
    }

    public function test_verification_email_resend_sends_notification()
    {
        Notification::fake();

        $user = $this->createAuthenticatedUser();

        $response = $this->postJson('/api/email/verification-notification');

        $response->assertStatus(200);

        Notification::assertSentTo($user, \App\Notifications\VerifyEmailNotification::class);
    }

    public function test_verification_email_resend_rate_limiting()
    {
        $user = $this->createAuthenticatedUser();

        // Make 3 requests (rate limit is 3 per minute)
        for ($i = 0; $i < 3; $i++) {
            $response = $this->postJson('/api/email/verification-notification');
            $response->assertStatus(200);
        }

        // 4th request should be rate limited
        $response = $this->postJson('/api/email/verification-notification');

        $response->assertStatus(429);
    }

    public function test_user_can_verify_email_with_valid_link()
    {
        $user = $this->createUser(['email_verified_at' => null]);
        
        // Create a signed URL for email verification
        $verificationUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->get($verificationUrl);

        $response->assertStatus(200);

        // Check that user's email is now verified
        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
    }

    public function test_user_cannot_verify_email_with_invalid_hash()
    {
        $user = $this->createUser(['email_verified_at' => null]);
        
        // Create a signed URL with invalid hash
        $verificationUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => 'invalid-hash']
        );

        $response = $this->get($verificationUrl);

        $response->assertStatus(403);

        // Check that user's email is still not verified
        $user->refresh();
        $this->assertNull($user->email_verified_at);
    }

    public function test_user_cannot_verify_email_with_expired_link()
    {
        $user = $this->createUser(['email_verified_at' => null]);
        
        // Create an expired signed URL
        $verificationUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'verification.verify',
            now()->subMinutes(60), // Expired
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->get($verificationUrl);

        $response->assertStatus(403);

        // Check that user's email is still not verified
        $user->refresh();
        $this->assertNull($user->email_verified_at);
    }

    public function test_user_cannot_verify_email_with_invalid_id()
    {
        $user = $this->createUser(['email_verified_at' => null]);
        
        // Create a signed URL with invalid user ID
        $verificationUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => 999999, 'hash' => sha1($user->email)]
        );

        $response = $this->get($verificationUrl);

        $response->assertStatus(403);

        // Check that user's email is still not verified
        $user->refresh();
        $this->assertNull($user->email_verified_at);
    }

    public function test_already_verified_user_can_still_access_verification_link()
    {
        $user = $this->createUser(['email_verified_at' => now()]);
        
        // Create a signed URL for already verified user
        $verificationUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->get($verificationUrl);

        $response->assertStatus(200);
    }

    public function test_verification_link_requires_signed_url()
    {
        $user = $this->createUser(['email_verified_at' => null]);
        
        // Create unsigned URL
        $verificationUrl = "/api/email/verify/{$user->id}/" . sha1($user->email);

        $response = $this->get($verificationUrl);

        $response->assertStatus(403);
    }
}
