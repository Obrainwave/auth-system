<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_logout()
    {
        $user = $this->createAuthenticatedUser();

        $response = $this->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Logged out successfully'
            ]);
    }

    public function test_unauthenticated_user_cannot_logout()
    {
        $response = $this->postJson('/api/logout');

        $response->assertStatus(401);
    }

    public function test_logout_invalidates_user_session()
    {
        $user = $this->createAuthenticatedUser();

        $response = $this->postJson('/api/logout');

        $response->assertStatus(200);

        // After logout, user should not be able to access protected routes
        $response = $this->getJson('/api/user');
        $response->assertStatus(401);
    }

    public function test_logout_requires_authentication()
    {
        $response = $this->postJson('/api/logout');

        $response->assertStatus(401);
    }
}
