<?php

namespace Tests\Feature\Controllers\Auth;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    public function test_it_logs_out_authenticated_user()
    {
        // Create a user
        $user = User::factory()->create();

        // Simulate login by creating an access token for the user
        Sanctum::actingAs($user); // Authenticates user using Sanctum

        // Make a request to logout
        $response = $this->postJson(route('logout'));

        // Assert that the response is successful
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Logged out successfully!',
            ]);

        // Check that the user's access token is deleted
        $this->assertNull($user->tokens()->first());
    }

    public function test_it_prevents_logout_for_unauthenticated_users()
    {
        // Make a request to logout without authenticating a user
        $response = $this->postJson(route('logout'));

        // Assert that the response returns an authentication error (401)
        $response->assertStatus(401);
    }
}
