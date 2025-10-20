<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_created()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertTrue(Hash::check('password', $user->password));
    }

    public function test_user_has_fillable_attributes()
    {
        $user = new User();
        $fillable = $user->getFillable();

        $this->assertContains('name', $fillable);
        $this->assertContains('email', $fillable);
        $this->assertContains('password', $fillable);
    }

    public function test_user_password_is_hashed()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'plaintext',
        ]);

        $this->assertNotEquals('plaintext', $user->password);
        $this->assertTrue(Hash::check('plaintext', $user->password));
    }

    public function test_user_email_is_unique()
    {
        User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        User::create([
            'name' => 'Jane Doe',
            'email' => 'john@example.com', // Same email
            'password' => Hash::make('password'),
        ]);
    }

    public function test_user_can_have_email_verified_at()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
        ]);

        $user->email_verified_at = now();
        $user->save();

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_user_email_verified_at_is_null_by_default()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->assertNull($user->email_verified_at);
    }

    public function test_user_has_timestamps()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->assertNotNull($user->created_at);
        $this->assertNotNull($user->updated_at);
    }

    public function test_user_can_be_updated()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
        ]);

        $user->update([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);

        $this->assertEquals('Jane Doe', $user->name);
        $this->assertEquals('jane@example.com', $user->email);
    }

    public function test_user_can_be_deleted()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
        ]);

        $userId = $user->id;
        $user->delete();

        $this->assertDatabaseMissing('users', ['id' => $userId]);
    }

    public function test_user_can_be_found_by_email()
    {
        User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
        ]);

        $user = User::where('email', 'john@example.com')->first();

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('john@example.com', $user->email);
    }

    public function test_user_can_be_found_by_id()
    {
        $createdUser = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
        ]);

        $user = User::find($createdUser->id);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($createdUser->id, $user->id);
    }

    public function test_user_has_many_personal_access_tokens()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\MorphMany::class, $user->tokens());
    }

    public function test_user_can_create_personal_access_token()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
        ]);

        $token = $user->createToken('test-token');

        $this->assertInstanceOf(\Laravel\Sanctum\NewAccessToken::class, $token);
        $this->assertNotNull($token->plainTextToken);
    }

    public function test_user_can_revoke_all_tokens()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
        ]);

        $user->createToken('test-token-1');
        $user->createToken('test-token-2');

        $this->assertEquals(2, $user->tokens()->count());

        $user->tokens()->delete();

        $this->assertEquals(0, $user->tokens()->count());
    }

    public function test_user_can_send_email_verification_notification()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
        ]);

        $notification = $user->newEmailVerificationNotification();
        $this->assertInstanceOf(\App\Notifications\VerifyEmailNotification::class, $notification);
    }

    public function test_user_can_send_password_reset_notification()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
        ]);

        $token = 'test-token';
        $notification = $user->newPasswordResetNotification($token);
        $this->assertInstanceOf(\App\Notifications\ResetPasswordNotification::class, $notification);
    }
}
