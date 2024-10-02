<?php

namespace Tests\Feature\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ChangePasswordTest extends TestCase
{
    public function test_user_can_change_password_with_correct_old_password()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123**'),
        ]);
        // Send request with correct old password and new password
        Sanctum::actingAs($user);
        $response = $this->putJson(route('change_password'), [
            'old_password' => 'password123**', // Correct old password
            'password' => 'newpassword123**',
            'password_confirmation' => 'newpassword123**', // Confirmation field
        ]);

        // Assert the password is changed
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Your password has been changed']);

        // Check that the new password is actually hashed and stored
        $this->assertTrue(Hash::check('newpassword123**', $user->password));
    }

    public function test_user_cannot_change_password_with_incorrect_old_password()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123**'),
        ]);
        // Send request with incorrect old password
        Sanctum::actingAs($user);
        $response = $this->putJson(route('change_password'), [
            'old_password' => 'wrongpassword123**', // Incorrect old password
            'password' => 'newpassword123**',
            'password_confirmation' => 'newpassword123**',
        ]);

        // Assert the response status and message
        $response->assertStatus(401);
        $response->assertJson(['message' => 'Your old password is incorrect']);
        $user = User::find($user->id);
        $this->assertTrue(Hash::check('password123**', $user->password));
    }

    public function test_user_cannot_change_password_if_validation_fails()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123**'),
        ]);
        Sanctum::actingAs($user);
        // Send request with missing or invalid data
        $response = $this->putJson(route('change_password'), [
            'old_password' => 'oldpassword', // Valid old password
            'password' => 'new', // Invalid new password (too short)
            'password_confirmation' => 'newpassword', // Confirmation mismatch
        ]);

        // Assert validation error
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
        $user = User::find($user->id);
        $this->assertTrue(Hash::check('password123**', $user->password));
    }
}
