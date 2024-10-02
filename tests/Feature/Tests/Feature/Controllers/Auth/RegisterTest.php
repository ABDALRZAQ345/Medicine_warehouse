<?php

namespace Tests\Feature\Controllers\Auth;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Events\RegisteredEvent;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    public function test_registriation_password_must_be_strong_password()
    {
        \Event::fake();
        $data = [
            'first_name' => 'John Doe',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];
        $response = $this->postJson(route('register'), $data);

        $response->assertStatus(422);
        $this->assertDatabaseMissing('users', [
            'email' => 'john@example.com',
        ]);
    }

    public function test_registeration_password_must_be_verifed()
    {
        \Event::fake();
        $data = [
            'first_name' => 'John Doe',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password123**',
            'password_confirmation' => 'password',
        ];
        $response = $this->postJson(route('register'), $data);

        $response->assertStatus(422);
        $this->assertDatabaseMissing('users', [
            'email' => 'john@example.com',
        ]);
    }

    public function test_all_registeration_fields_are_required()
    {
        \Event::fake();
        $data = [];
        $response = $this->postJson(route('register'), $data);

        $response->assertStatus(422);
        $response->assertJsonCount(4, 'errors');
        $this->assertDatabaseMissing('users', [
            'email' => 'john@example.com',
        ]);
    }

    public function test_registeration_email_must_be_real_email()
    {
        \Event::fake();
        $data = [
            'first_name' => 'John Doe',
            'last_name' => 'Doe',
            'email' => 'ddddesds',
            'password' => 'password123**',
            'password_confirmation' => 'password123**',
        ];
        $response = $this->postJson(route('register'), $data);

        $response->assertStatus(422);
        $this->assertDatabaseMissing('users', [
            'email' => 'john@example.com',
        ]);
    }

    public function test_email_must_be_unique()
    {
        \Event::fake();
        $user1 = User::factory()->create([
            'email' => 'john@gmail.com',
            'first_name' => 'John Doe',
            'last_name' => 'Doe',
            'password' => 'password123**',
        ]);
        $data = [
            'first_name' => 'Test User',
            'last_name' => 'd',
            'email' => $user1->email,  // Same email as the first user
            'password' => 'password123**',
            'password_confirmation' => 'password123**',
        ];

        $response = $this->postJson(route('register'), $data);
        $response->assertStatus(422);

        // Assert that the JSON response contains the validation error for the email field
        $response->assertJsonValidationErrors('email');

        // Ensure that a second user was not created with the same email
        $this->assertEquals(1, User::where('email', $user1->email)->count());

    }

    public function test_it_register_new_user()
    {
        \Event::fake();

        $data = [
            'first_name' => 'Test User',
            'last_name' => 'd',
            'email' => 'new@gmail.com',  // Same email as the first user
            'password' => 'password123**',
            'password_confirmation' => 'password123**',
        ];
        $response = $this->postJson(route('register'), $data);
        $this->assertDatabaseHas('users', [
            'email' => 'new@gmail.com',
        ]);
        $user = User::where('email', 'new@gmail.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('password123**', $user->password));
        $this->assertTrue($user->hasRole('user'));
        \Event::assertDispatched(RegisteredEvent::class, function ($event) use ($user) {
            return $event->user->id === $user->id;
        });
        $this->assertDatabaseHas('email_verification_tokens', [
            'user_id' => $user->id,
        ]);
        $response->assertStatus(200);

    }
}
