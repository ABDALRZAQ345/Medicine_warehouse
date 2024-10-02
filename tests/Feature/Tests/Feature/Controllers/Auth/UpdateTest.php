<?php

namespace Tests\Feature\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    public function test_it_updates_user_with_valid_phone_and_photo()
    {
        // Fake the storage for file uploads
        Storage::fake('public');

        // Create a user
        $user = User::factory()->create();

        // Authenticate the user
        Sanctum::actingAs($user);

        // Prepare data for the request
        $data = [
            'phone' => '1234567890', // Valid phone number
            'photo' => UploadedFile::fake()->image('profile.jpg'), // Valid photo
        ];

        // Send a request to update the user's profile
        $response = $this->putJson(route('update'), $data);

        // Assert the response is successful
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'updated successfully',
            ]);

        // Assert the file was stored
        Storage::disk('public')->assertExists('profile/photos/'.$data['photo']->hashName());

        // Assert the user's phone number was updated
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'phone' => '1234567890',
        ]);
    }

    public function test_it_updates_user_without_providing_a_phone()
    {
        // Create a user with an existing phone number
        $user = User::factory()->create([
            'phone' => '0987654321',
        ]);

        // Authenticate the user
        Sanctum::actingAs($user);

        // Prepare data without the phone
        $data = [
            'photo' => UploadedFile::fake()->image('profile.jpg'), // Valid photo
        ];

        // Send a request to update the user's profile
        $response = $this->putJson(route('update'), $data);

        // Assert the response is successful
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'updated successfully',
            ]);

        // Assert the user's phone number was not changed
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'phone' => '0987654321',
        ]);
    }

    ///
    public function test_it_does_not_update_with_invalid_phone()
    {
        // Create a user
        $user = User::factory()->create();

        // Authenticate the user
        Sanctum::actingAs($user);

        // Prepare data with an invalid phone number
        $data = [
            'phone' => 'invalidphone', // Invalid phone
        ];

        // Send a request to update the user's profile
        $response = $this->putJson(route('update'), $data);

        // Assert validation error response
        $response->assertStatus(422)  // 422 is the status code for validation errors
            ->assertJsonValidationErrors(['phone']);
    }

    public function test_it_does_not_update_with_invalid_photo()
    {
        // Create a user
        $user = User::factory()->create();

        // Authenticate the user
        Sanctum::actingAs($user);

        // Prepare data with an invalid photo (non-image file)
        $data = [
            'photo' => UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'), // Invalid photo
        ];

        // Send a request to update the user's profile
        $response = $this->putJson(route('update'), $data);

        // Assert validation error response
        $response->assertStatus(422)  // 422 is the status code for validation errors
            ->assertJsonValidationErrors(['photo']);
    }
}
