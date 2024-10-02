<?php

namespace Tests\Feature\Controllers\Auth;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    public function test_it_can_login_a_user_with_valid_credentials()
    {
        // Create a user with a password
        $user = User::factory()->create([
            'email' => 'someuser@gmail.com',
            'password' => Hash::make('password123*'),
        ]);

        // Attempt to log in
        $response = $this->postJson(route('login'), [
            'email' => $user->email,
            'password' => 'password123*',
        ]);

        // Assert the response
        $response->assertStatus(200);

        // Assert the token is created
        $this->assertNotNull($response->json('access_token'));
    }

    public function test_it_returns_error_for_invalid_credentials()
    {
        // Attempt to log in with invalid credentials
        $response = $this->postJson(route('login'), [
            'email' => 'nonexistent@gmail.com',
            'password' => 'wrongpassword',
        ]);

        // Assert the response
        $response->assertStatus(401)
            ->assertJson(['error' => 'email or password are not correct ']);
    }

    public function test_it_returns_error_if_email_is_not_provided()
    {
        // Attempt to log in without an email
        $response = $this->postJson(route('login'), [
            'password' => 'password123*',
        ]);

        // Assert validation error response
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_it_returns_error_if_password_is_not_provided()
    {
        // Attempt to log in without a password
        $response = $this->postJson(route('login'), [
            'email' => 'user@example.com',
        ]);

        // Assert validation error response
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }
}
