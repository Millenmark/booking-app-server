<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.api.key' => 'test_api_key']);
    }

    private function apiHeaders()
    {
        return ['X-Api-Key' => 'test_api_key'];
    }

    public function test_user_can_register(): void
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/register', $userData, $this->apiHeaders());

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => ['id', 'name', 'email', 'role'],
                'token'
            ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'customer',
        ]);
    }

    public function test_user_can_login(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ], $this->apiHeaders());

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => ['id', 'name', 'email', 'role'],
                'token'
            ]);
    }

    public function test_user_cannot_login_with_wrong_credentials(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ], $this->apiHeaders());

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'timestamp'
            ])
            ->assertJson([
                'message' => 'The provided credentials do not match our records.'
            ]);
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('test')->plainTextToken;

        $response = $this->postJson('/api/logout', [], array_merge($this->apiHeaders(), [
            'Authorization' => 'Bearer ' . $token,
        ]));

        $response->assertStatus(200)
            ->assertJson(['message' => 'Logout successful']);
    }

    public function test_forgot_password(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/forgot-password', [
            'email' => $user->email,
        ], $this->apiHeaders());

        $response->assertStatus(200)
            ->assertJson(['message' => 'We have emailed your password reset link.']);
    }

    public function test_reset_password(): void
    {
        $user = User::factory()->create();

        // Note: Reset password test would require mocking the password reset token
        // For simplicity, we'll test the validation
        $response = $this->postJson('/api/reset-password', [
            'token' => 'invalid',
            'email' => $user->email,
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ], $this->apiHeaders());

        $response->assertStatus(400);
    }
}
