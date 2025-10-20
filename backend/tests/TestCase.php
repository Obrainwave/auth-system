<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Set up test environment
        $this->artisan('migrate');
    }

    /**
     * Create a test user
     */
    protected function createUser(array $attributes = [])
    {
        return \App\Models\User::factory()->create($attributes);
    }

    /**
     * Create an authenticated user
     */
    protected function createAuthenticatedUser(array $attributes = [])
    {
        $user = $this->createUser($attributes);
        $this->actingAs($user, 'sanctum');
        return $user;
    }

    /**
     * Get valid registration data
     */
    protected function getValidRegistrationData()
    {
        return [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ];
    }

    /**
     * Get valid login data
     */
    protected function getValidLoginData()
    {
        return [
            'email' => 'john@example.com',
            'password' => 'Password123!',
        ];
    }
}
