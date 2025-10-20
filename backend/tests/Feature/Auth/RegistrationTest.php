<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_with_valid_data()
    {
        $userData = $this->getValidRegistrationData();

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'email_verified_at'
                ]
            ])
            ->assertJson([
                'message' => 'User registered successfully. Please check your email for verification.',
                'user' => [
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'email_verified_at' => null
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'name' => $userData['name'],
            'email' => $userData['email'],
        ]);

        $user = User::where('email', $userData['email'])->first();
        $this->assertTrue(Hash::check($userData['password'], $user->password));
    }

    public function test_user_cannot_register_with_invalid_email()
    {
        $userData = $this->getValidRegistrationData();
        $userData['email'] = 'invalid-email';

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_user_cannot_register_with_weak_password()
    {
        $userData = $this->getValidRegistrationData();
        $userData['password'] = 'weak';
        $userData['password_confirmation'] = 'weak';

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_user_cannot_register_with_mismatched_passwords()
    {
        $userData = $this->getValidRegistrationData();
        $userData['password_confirmation'] = 'DifferentPassword123!';

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_user_cannot_register_with_existing_email()
    {
        $existingUser = $this->createUser(['email' => 'john@example.com']);
        
        $userData = $this->getValidRegistrationData();

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_user_cannot_register_without_required_fields()
    {
        $response = $this->postJson('/api/register', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_user_cannot_register_with_empty_name()
    {
        $userData = $this->getValidRegistrationData();
        $userData['name'] = '';

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_user_cannot_register_with_too_long_name()
    {
        $userData = $this->getValidRegistrationData();
        $userData['name'] = str_repeat('a', 256);

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_registration_sends_verification_email()
    {
        Notification::fake();

        $userData = $this->getValidRegistrationData();

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(201);

        $user = User::where('email', $userData['email'])->first();
        Notification::assertSentTo($user, \App\Notifications\VerifyEmailNotification::class);
    }

    public function test_registration_rate_limiting()
    {
        // Test that rate limiting is applied (5 requests per minute)
        for ($i = 0; $i < 5; $i++) {
            $userData = $this->getValidRegistrationData();
            $userData['email'] = "user{$i}@example.com";
            
            $response = $this->postJson('/api/register', $userData);
            $response->assertStatus(201);
        }

        // 6th request should be rate limited
        $userData = $this->getValidRegistrationData();
        $userData['email'] = "user6@example.com";
        
        $response = $this->postJson('/api/register', $userData);
        $response->assertStatus(429);
    }
}
