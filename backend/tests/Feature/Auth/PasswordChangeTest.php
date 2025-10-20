<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordChangeTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_change_password()
    {
        $user = $this->createAuthenticatedUser([
            'password' => Hash::make('OldPassword123!'),
        ]);

        $passwordData = [
            'current_password' => 'OldPassword123!',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ];

        $response = $this->putJson('/api/user/password', $passwordData);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Password changed successfully'
            ]);

        // Verify password was actually changed
        $user->refresh();
        $this->assertTrue(Hash::check('NewPassword123!', $user->password));
        $this->assertFalse(Hash::check('OldPassword123!', $user->password));
    }

    public function test_user_cannot_change_password_with_wrong_current_password()
    {
        $user = $this->createAuthenticatedUser([
            'password' => Hash::make('OldPassword123!'),
        ]);

        $passwordData = [
            'current_password' => 'WrongPassword',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ];

        $response = $this->putJson('/api/user/password', $passwordData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['current_password']);
    }

    public function test_user_cannot_change_password_without_current_password()
    {
        $user = $this->createAuthenticatedUser();

        $passwordData = [
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ];

        $response = $this->putJson('/api/user/password', $passwordData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['current_password']);
    }

    public function test_user_cannot_change_password_with_weak_new_password()
    {
        $user = $this->createAuthenticatedUser([
            'password' => Hash::make('OldPassword123!'),
        ]);

        $passwordData = [
            'current_password' => 'OldPassword123!',
            'password' => 'weak',
            'password_confirmation' => 'weak',
        ];

        $response = $this->putJson('/api/user/password', $passwordData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_user_cannot_change_password_with_mismatched_passwords()
    {
        $user = $this->createAuthenticatedUser([
            'password' => Hash::make('OldPassword123!'),
        ]);

        $passwordData = [
            'current_password' => 'OldPassword123!',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'DifferentPassword123!',
        ];

        $response = $this->putJson('/api/user/password', $passwordData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_user_cannot_change_password_without_new_password()
    {
        $user = $this->createAuthenticatedUser([
            'password' => Hash::make('OldPassword123!'),
        ]);

        $passwordData = [
            'current_password' => 'OldPassword123!',
        ];

        $response = $this->putJson('/api/user/password', $passwordData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_password_change_requires_authentication()
    {
        $passwordData = [
            'current_password' => 'OldPassword123!',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ];

        $response = $this->putJson('/api/user/password', $passwordData);

        $response->assertStatus(401);
    }

    public function test_user_cannot_use_same_password()
    {
        $user = $this->createAuthenticatedUser([
            'password' => Hash::make('Password123!'),
        ]);

        $passwordData = [
            'current_password' => 'Password123!',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ];

        $response = $this->putJson('/api/user/password', $passwordData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_password_change_requires_confirmation()
    {
        $user = $this->createAuthenticatedUser([
            'password' => Hash::make('OldPassword123!'),
        ]);

        $passwordData = [
            'current_password' => 'OldPassword123!',
            'password' => 'NewPassword123!',
        ];

        $response = $this->putJson('/api/user/password', $passwordData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }
}
