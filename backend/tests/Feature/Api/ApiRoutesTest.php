<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ApiRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_routes_are_accessible()
    {
        $routes = [
            'POST /api/register',
            'POST /api/login',
            'POST /api/forgot-password',
            'POST /api/reset-password',
        ];

        foreach ($routes as $route) {
            [$method, $uri] = explode(' ', $route);
            
            $response = $this->call($method, $uri);
            
            // Should not return 404 (route exists)
            $this->assertNotEquals(404, $response->getStatusCode(), "Route {$route} should exist");
        }
    }

    public function test_protected_routes_require_authentication()
    {
        $protectedRoutes = [
            'GET /api/user',
            'POST /api/logout',
            'PUT /api/user/profile',
            'PUT /api/user/password',
            'POST /api/email/verification-notification',
        ];

        foreach ($protectedRoutes as $route) {
            [$method, $uri] = explode(' ', $route);
            
            $response = $this->call($method, $uri);
            
            // Should return 401 (unauthorized)
            $this->assertEquals(401, $response->getStatusCode(), "Route {$route} should require authentication");
        }
    }

    public function test_authenticated_user_can_access_protected_routes()
    {
        $user = $this->createAuthenticatedUser();

        $protectedRoutes = [
            'GET /api/user',
            'POST /api/logout',
            'PUT /api/user/profile',
            'PUT /api/user/password',
            'POST /api/email/verification-notification',
        ];

        foreach ($protectedRoutes as $route) {
            [$method, $uri] = explode(' ', $route);
            
            $response = $this->call($method, $uri);
            
            // Should not return 401 (should be accessible to authenticated users)
            $this->assertNotEquals(401, $response->getStatusCode(), "Route {$route} should be accessible to authenticated users");
        }
    }

    public function test_api_routes_return_json()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $this->assertTrue($response->headers->get('content-type') === 'application/json' || 
                         str_contains($response->headers->get('content-type'), 'application/json'));
    }

    public function test_api_routes_handle_cors()
    {
        $response = $this->options('/api/register');

        $this->assertTrue($response->headers->has('Access-Control-Allow-Origin') || 
                         $response->headers->has('Access-Control-Allow-Methods'));
    }

    public function test_api_routes_handle_csrf_protection()
    {
        // Test that API routes work without CSRF token (they should be protected by Sanctum)
        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $this->assertNotEquals(419, $response->getStatusCode()); // 419 is CSRF token mismatch
    }

    public function test_api_routes_handle_rate_limiting()
    {
        // Test registration rate limiting
        for ($i = 0; $i < 5; $i++) {
            $response = $this->postJson('/api/register', [
                'name' => 'John Doe',
                'email' => "john{$i}@example.com",
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
            ]);
            $this->assertEquals(201, $response->getStatusCode());
        }

        // 6th request should be rate limited
        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'john6@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $this->assertEquals(429, $response->getStatusCode());
    }

    public function test_api_routes_handle_validation_errors()
    {
        $response = $this->postJson('/api/register', [
            'name' => '',
            'email' => 'invalid-email',
            'password' => 'weak',
        ]);

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertArrayHasKey('errors', $response->json());
    }

    public function test_api_routes_handle_server_errors()
    {
        // Test with invalid data that might cause server errors
        $response = $this->postJson('/api/register', [
            'name' => str_repeat('a', 1000), // Very long name
            'email' => str_repeat('a', 1000) . '@example.com', // Very long email
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $this->assertNotEquals(500, $response->getStatusCode()); // Should handle gracefully
    }

    public function test_api_routes_handle_missing_parameters()
    {
        $response = $this->postJson('/api/login', []);

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertArrayHasKey('errors', $response->json());
    }

    public function test_api_routes_handle_invalid_methods()
    {
        $response = $this->get('/api/register');
        $this->assertEquals(405, $response->getStatusCode()); // Method not allowed

        $response = $this->put('/api/login');
        $this->assertEquals(405, $response->getStatusCode()); // Method not allowed
    }

    public function test_api_routes_handle_malformed_json()
    {
        $response = $this->call('POST', '/api/register', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], 'invalid json');

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function test_api_routes_handle_large_payloads()
    {
        $largeData = [
            'name' => str_repeat('a', 10000),
            'email' => 'john@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ];

        $response = $this->postJson('/api/register', $largeData);

        $this->assertEquals(422, $response->getStatusCode()); // Should validate and reject
    }

    public function test_api_routes_handle_special_characters()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'José María',
            'email' => 'josé@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $this->assertEquals(201, $response->getStatusCode());
    }
}
