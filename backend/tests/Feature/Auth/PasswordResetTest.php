<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_request_password_reset()
    {
        $user = $this->createUser(['email' => 'john@example.com']);

        $response = $this->postJson('/api/forgot-password', [
            'email' => 'john@example.com'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Password reset link sent to your email'
            ]);
    }

    public function test_password_reset_request_requires_valid_email()
    {
        $response = $this->postJson('/api/forgot-password', [
            'email' => 'invalid-email'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_password_reset_request_requires_email()
    {
        $response = $this->postJson('/api/forgot-password', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_password_reset_request_sends_email()
    {
        Notification::fake();

        $user = $this->createUser(['email' => 'john@example.com']);

        $response = $this->postJson('/api/forgot-password', [
            'email' => 'john@example.com'
        ]);

        $response->assertStatus(200);

        Notification::assertSentTo($user, \App\Notifications\ResetPasswordNotification::class);
    }

    public function test_password_reset_request_works_with_nonexistent_email()
    {
        $response = $this->postJson('/api/forgot-password', [
            'email' => 'nonexistent@example.com'
        ]);

        // Should still return 200 to prevent email enumeration
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Password reset link sent to your email'
            ]);
    }

    public function test_password_reset_request_rate_limiting()
    {
        $user = $this->createUser(['email' => 'john@example.com']);

        // Make 3 requests (rate limit is 3 per minute)
        for ($i = 0; $i < 3; $i++) {
            $response = $this->postJson('/api/forgot-password', [
                'email' => 'john@example.com'
            ]);
            $response->assertStatus(200);
        }

        // 4th request should be rate limited
        $response = $this->postJson('/api/forgot-password', [
            'email' => 'john@example.com'
        ]);

        $response->assertStatus(429);
    }

    public function test_user_can_reset_password_with_valid_token()
    {
        $user = $this->createUser(['email' => 'john@example.com']);
        $token = app('auth.password.broker')->createToken($user);

        $resetData = [
            'token' => $token,
            'email' => 'john@example.com',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ];

        $response = $this->postJson('/api/reset-password', $resetData);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Password reset successfully'
            ]);

        // Verify password was actually changed
        $user->refresh();
        $this->assertTrue(Hash::check('NewPassword123!', $user->password));
    }

    public function test_user_cannot_reset_password_with_invalid_token()
    {
        $user = $this->createUser(['email' => 'john@example.com']);

        $resetData = [
            'token' => 'invalid-token',
            'email' => 'john@example.com',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ];

        $response = $this->postJson('/api/reset-password', $resetData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_user_cannot_reset_password_with_expired_token()
    {
        $user = $this->createUser(['email' => 'john@example.com']);
        $token = app('auth.password.broker')->createToken($user);

        // Simulate token expiration by clearing it
        app('auth.password.broker')->deleteToken($user);

        $resetData = [
            'token' => $token,
            'email' => 'john@example.com',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ];

        $response = $this->postJson('/api/reset-password', $resetData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_password_reset_requires_valid_email()
    {
        $resetData = [
            'token' => 'some-token',
            'email' => 'invalid-email',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ];

        $response = $this->postJson('/api/reset-password', $resetData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_password_reset_requires_strong_password()
    {
        $user = $this->createUser(['email' => 'john@example.com']);
        $token = app('auth.password.broker')->createToken($user);

        $resetData = [
            'token' => $token,
            'email' => 'john@example.com',
            'password' => 'weak',
            'password_confirmation' => 'weak',
        ];

        $response = $this->postJson('/api/reset-password', $resetData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_password_reset_requires_password_confirmation()
    {
        $user = $this->createUser(['email' => 'john@example.com']);
        $token = app('auth.password.broker')->createToken($user);

        $resetData = [
            'token' => $token,
            'email' => 'john@example.com',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'DifferentPassword123!',
        ];

        $response = $this->postJson('/api/reset-password', $resetData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_password_reset_rate_limiting()
    {
        $user = $this->createUser(['email' => 'john@example.com']);
        $token = app('auth.password.broker')->createToken($user);

        // Make 5 requests (rate limit is 5 per minute)
        for ($i = 0; $i < 5; $i++) {
            $response = $this->postJson('/api/reset-password', [
                'token' => $token,
                'email' => 'john@example.com',
                'password' => 'NewPassword123!',
                'password_confirmation' => 'NewPassword123!',
            ]);
            $response->assertStatus(422); // Token gets consumed
        }

        // 6th request should be rate limited
        $response = $this->postJson('/api/reset-password', [
            'token' => $token,
            'email' => 'john@example.com',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertStatus(429);
    }
}
