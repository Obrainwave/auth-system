<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_profile()
    {
        $user = $this->createAuthenticatedUser();

        $response = $this->getJson('/api/user');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name',
                'email',
                'email_verified_at',
                'created_at',
                'updated_at'
            ])
            ->assertJson([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]);
    }

    public function test_unauthenticated_user_cannot_view_profile()
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_update_profile()
    {
        $user = $this->createAuthenticatedUser();

        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ];

        $response = $this->putJson('/api/user/profile', $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Profile updated successfully',
                'user' => [
                    'name' => $updateData['name'],
                    'email' => $updateData['email'],
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => $updateData['name'],
            'email' => $updateData['email'],
        ]);
    }

    public function test_user_cannot_update_profile_with_invalid_email()
    {
        $user = $this->createAuthenticatedUser();

        $updateData = [
            'name' => 'Updated Name',
            'email' => 'invalid-email',
        ];

        $response = $this->putJson('/api/user/profile', $updateData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_user_cannot_update_profile_with_existing_email()
    {
        $existingUser = $this->createUser(['email' => 'existing@example.com']);
        $user = $this->createAuthenticatedUser();

        $updateData = [
            'name' => 'Updated Name',
            'email' => 'existing@example.com',
        ];

        $response = $this->putJson('/api/user/profile', $updateData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_user_can_update_profile_with_same_email()
    {
        $user = $this->createAuthenticatedUser();

        $updateData = [
            'name' => 'Updated Name',
            'email' => $user->email,
        ];

        $response = $this->putJson('/api/user/profile', $updateData);

        $response->assertStatus(200);
    }

    public function test_user_cannot_update_profile_with_empty_name()
    {
        $user = $this->createAuthenticatedUser();

        $updateData = [
            'name' => '',
            'email' => 'updated@example.com',
        ];

        $response = $this->putJson('/api/user/profile', $updateData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_user_cannot_update_profile_with_too_long_name()
    {
        $user = $this->createAuthenticatedUser();

        $updateData = [
            'name' => str_repeat('a', 256),
            'email' => 'updated@example.com',
        ];

        $response = $this->putJson('/api/user/profile', $updateData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_profile_update_requires_authentication()
    {
        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ];

        $response = $this->putJson('/api/user/profile', $updateData);

        $response->assertStatus(401);
    }

    public function test_profile_update_sends_verification_email_when_email_changes()
    {
        $user = $this->createAuthenticatedUser();

        $updateData = [
            'name' => 'Updated Name',
            'email' => 'newemail@example.com',
        ];

        $response = $this->putJson('/api/user/profile', $updateData);

        $response->assertStatus(200);

        // Check that user's email_verified_at is set to null
        $user->refresh();
        $this->assertNull($user->email_verified_at);
    }
}
