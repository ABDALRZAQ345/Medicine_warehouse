<?php

namespace Tests\Feature\Controllers\Auth;

use App\Events\RegisteredEvent;
use App\Models\EmailVerificationToken;
use App\Models\User;
use Event;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ResendEmailVerificationTokenTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_it_resends_verification_email_and_creates_new_token()
    {
        // Fake events so we can check for the RegisteredEvent dispatch
        Event::fake();

        // Create a user and log in
        $user = User::factory()->create();

        // Simulate the existence of an old email verification token
        $oldToken = $user->Email_verification_tokens()->create([
            'token' => Hash::make('old-token'),
            'expires_at' => now()->addHours(24),
        ]);

        // Act as the logged-in user and hit the resend endpoint
        $this->actingAs($user)
            ->postJson(route('resend-email-verification-link'))
            ->assertStatus(200)
            ->assertJson([
                'message' => 'email verification link sent please check your email and verify your account',
            ]);

        // Assert that the old token was deleted and a new token was created
        $this->assertDatabaseMissing('email_verification_tokens', [
            'id' => $oldToken->id,  // The old token should be deleted
        ]);

        $this->assertDatabaseHas('email_verification_tokens', [
            'user_id' => $user->id,
        ]);

        // Fetch the new token
        $newToken = EmailVerificationToken::where('user_id', $user->id)->first();
        $this->assertNotNull($newToken);

        // Assert that the event was dispatched
        Event::assertDispatched(RegisteredEvent::class, function ($event) use ($user) {
            return $event->user->id === $user->id;
        });
    }
}
