<?php

namespace Tests\Feature\Validation;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class FormValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_validation_rules()
    {
        // Test required fields
        $response = $this->postJson('/api/register', []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);

        // Test name validation
        $response = $this->postJson('/api/register', [
            'name' => '',
            'email' => 'john@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);

        // Test email validation
        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'invalid-email',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);

        // Test password validation
        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'weak',
            'password_confirmation' => 'weak',
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);

        // Test password confirmation
        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'DifferentPassword123!',
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_login_validation_rules()
    {
        // Test required fields
        $response = $this->postJson('/api/login', []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);

        // Test email format
        $response = $this->postJson('/api/login', [
            'email' => 'invalid-email',
            'password' => 'Password123!',
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);

        // Test remember field (should be boolean)
        $response = $this->postJson('/api/login', [
            'email' => 'john@example.com',
            'password' => 'Password123!',
            'remember' => 'not-boolean',
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['remember']);
    }

    public function test_profile_update_validation_rules()
    {
        $user = $this->createAuthenticatedUser();

        // Test required fields
        $response = $this->putJson('/api/user/profile', []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email']);

        // Test name validation
        $response = $this->putJson('/api/user/profile', [
            'name' => '',
            'email' => 'updated@example.com',
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);

        // Test email validation
        $response = $this->putJson('/api/user/profile', [
            'name' => 'Updated Name',
            'email' => 'invalid-email',
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);

        // Test unique email validation
        $existingUser = $this->createUser(['email' => 'existing@example.com']);
        $response = $this->putJson('/api/user/profile', [
            'name' => 'Updated Name',
            'email' => 'existing@example.com',
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_password_change_validation_rules()
    {
        $user = $this->createAuthenticatedUser([
            'password' => Hash::make('OldPassword123!'),
        ]);

        // Test required fields
        $response = $this->putJson('/api/user/password', []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['current_password', 'password']);

        // Test current password validation
        $response = $this->putJson('/api/user/password', [
            'current_password' => 'WrongPassword',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['current_password']);

        // Test new password validation
        $response = $this->putJson('/api/user/password', [
            'current_password' => 'OldPassword123!',
            'password' => 'weak',
            'password_confirmation' => 'weak',
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);

        // Test password confirmation
        $response = $this->putJson('/api/user/password', [
            'current_password' => 'OldPassword123!',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'DifferentPassword123!',
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_password_reset_validation_rules()
    {
        // Test forgot password validation
        $response = $this->postJson('/api/forgot-password', []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);

        $response = $this->postJson('/api/forgot-password', [
            'email' => 'invalid-email',
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);

        // Test reset password validation
        $response = $this->postJson('/api/reset-password', []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['token', 'email', 'password']);

        $response = $this->postJson('/api/reset-password', [
            'token' => 'test-token',
            'email' => 'invalid-email',
            'password' => 'weak',
            'password_confirmation' => 'weak',
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_validation_error_messages_are_meaningful()
    {
        $response = $this->postJson('/api/register', [
            'name' => '',
            'email' => 'invalid-email',
            'password' => 'weak',
        ]);

        $response->assertStatus(422);
        $errors = $response->json('errors');

        $this->assertArrayHasKey('name', $errors);
        $this->assertArrayHasKey('email', $errors);
        $this->assertArrayHasKey('password', $errors);

        // Check that error messages are not empty
        $this->assertNotEmpty($errors['name']);
        $this->assertNotEmpty($errors['email']);
        $this->assertNotEmpty($errors['password']);
    }

    public function test_validation_handles_special_characters()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'José María',
            'email' => 'josé@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertStatus(201);
    }

    public function test_validation_handles_unicode_characters()
    {
        $response = $this->postJson('/api/register', [
            'name' => '张三',
            'email' => 'zhangsan@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertStatus(201);
    }

    public function test_validation_handles_long_strings()
    {
        $response = $this->postJson('/api/register', [
            'name' => str_repeat('a', 256), // Too long
            'email' => 'john@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_validation_handles_empty_strings()
    {
        $response = $this->postJson('/api/register', [
            'name' => '   ', // Whitespace only
            'email' => 'john@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_validation_handles_null_values()
    {
        $response = $this->postJson('/api/register', [
            'name' => null,
            'email' => null,
            'password' => null,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_validation_handles_array_values()
    {
        $response = $this->postJson('/api/register', [
            'name' => ['John', 'Doe'],
            'email' => ['john@example.com'],
            'password' => ['Password123!'],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_validation_handles_boolean_values()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'john@example.com',
            'password' => 'Password123!',
            'remember' => true,
        ]);

        // Should not fail validation for boolean remember field
        $this->assertNotEquals(422, $response->getStatusCode());
    }
}
