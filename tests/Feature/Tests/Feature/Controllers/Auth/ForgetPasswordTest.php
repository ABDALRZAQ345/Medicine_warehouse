<?php

namespace Tests\Feature\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Password;
use Notification;
use Tests\TestCase;

class ForgetPasswordTest extends TestCase
{
    public function test_it_sends_reset_password_link_successfully()
    {
        // Disable actual notification sending (emails)
        Notification::fake();

        // Create a user to test with
        $user = User::factory()->create();

        // Mock the Password broker's response for sending the reset link
        Password::shouldReceive('sendResetLink')
            ->once()
            ->with(['email' => $user->email])
            ->andReturn(Password::RESET_LINK_SENT);

        // Send the POST request to the password reset route
        $response = $this->postJson(route('forget_password'), [
            'email' => $user->email,
        ]);

        // Assert that the response is successful and contains the correct message
        $response->assertStatus(200)
            ->assertJson(['message' => __('passwords.sent')]);
    }

    ///
    public function test_it_fails_when_email_is_invalid_or_not_registered()
    {
        // Send the POST request with an invalid email
        $response = $this->postJson(route('forget_password'), [
            'email' => 'invalid-email@gmail.com',
        ]);

        // Assert validation fails with 'exists' rule error
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_it_returns_error_if_password_reset_fails_due_to_exception()
    {
        $this->withoutExceptionHandling();  // This disables default Laravel exception handling so you can see the errors

        Notification::fake();

        // Create a user
        $user = User::factory()->create([
            'email' => 'qwccq@gmail.com',
        ]);

        // Simulate an exception occurring during password reset link generation
        Password::shouldReceive('sendResetLink')
            ->once()
            ->andThrow(new \Exception('some error'));  // Simulates the exception

        // Send the POST request to the password reset route
        $response = $this->postJson(route('forget_password'), [
            'email' => $user->email,
        ]);

        // Assert that the response contains the correct error message and status code
        $response->assertStatus(400);
    }
}
