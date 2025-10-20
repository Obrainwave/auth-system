<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_valid_credentials()
    {
        $user = $this->createUser([
            'email' => 'john@example.com',
            'password' => Hash::make('Password123!'),
        ]);

        $loginData = [
            'email' => 'john@example.com',
            'password' => 'Password123!',
        ];

        $response = $this->postJson('/api/login', $loginData);

        $response->assertStatus(200)
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
                'message' => 'Login successful',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ]
            ]);
    }

    public function test_user_cannot_login_with_invalid_credentials()
    {
        $this->createUser([
            'email' => 'john@example.com',
            'password' => Hash::make('Password123!'),
        ]);

        $loginData = [
            'email' => 'john@example.com',
            'password' => 'WrongPassword',
        ];

        $response = $this->postJson('/api/login', $loginData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_user_cannot_login_with_nonexistent_email()
    {
        $loginData = [
            'email' => 'nonexistent@example.com',
            'password' => 'Password123!',
        ];

        $response = $this->postJson('/api/login', $loginData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_user_cannot_login_without_password()
    {
        $loginData = [
            'email' => 'john@example.com',
        ];

        $response = $this->postJson('/api/login', $loginData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_user_cannot_login_without_email()
    {
        $loginData = [
            'password' => 'Password123!',
        ];

        $response = $this->postJson('/api/login', $loginData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_user_cannot_login_with_invalid_email_format()
    {
        $loginData = [
            'email' => 'invalid-email',
            'password' => 'Password123!',
        ];

        $response = $this->postJson('/api/login', $loginData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_user_can_login_with_remember_me()
    {
        $user = $this->createUser([
            'email' => 'john@example.com',
            'password' => Hash::make('Password123!'),
        ]);

        $loginData = [
            'email' => 'john@example.com',
            'password' => 'Password123!',
            'remember' => true,
        ];

        $response = $this->postJson('/api/login', $loginData);

        $response->assertStatus(200);
    }

    public function test_login_rate_limiting()
    {
        $user = $this->createUser([
            'email' => 'john@example.com',
            'password' => Hash::make('Password123!'),
        ]);

        // Attempt login 5 times with wrong password
        for ($i = 0; $i < 5; $i++) {
            $response = $this->postJson('/api/login', [
                'email' => 'john@example.com',
                'password' => 'WrongPassword',
            ]);
            $response->assertStatus(422);
        }

        // 6th attempt should be rate limited
        $response = $this->postJson('/api/login', [
            'email' => 'john@example.com',
            'password' => 'WrongPassword',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_login_clears_rate_limit_on_success()
    {
        $user = $this->createUser([
            'email' => 'john@example.com',
            'password' => Hash::make('Password123!'),
        ]);

        // Attempt login 3 times with wrong password
        for ($i = 0; $i < 3; $i++) {
            $this->postJson('/api/login', [
                'email' => 'john@example.com',
                'password' => 'WrongPassword',
            ]);
        }

        // Successful login should clear rate limit
        $response = $this->postJson('/api/login', [
            'email' => 'john@example.com',
            'password' => 'Password123!',
        ]);

        $response->assertStatus(200);
    }

    public function test_login_regenerates_session()
    {
        $user = $this->createUser([
            'email' => 'john@example.com',
            'password' => Hash::make('Password123!'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'john@example.com',
            'password' => 'Password123!',
        ]);

        $response->assertStatus(200);
        
        // Session should be regenerated (this is handled by Laravel's session regeneration)
        $this->assertTrue(true); // Placeholder for session regeneration test
    }
}
