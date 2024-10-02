<?php

namespace Tests\Feature\Controllers\Auth;

use App\Models\Medicine;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StoreFavouriteTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_it_adds_medicine_to_favorites_if_not_already_favorited()
    {
        // Create a user and log them in
        $user = User::factory()->create();
        Sanctum::actingAs($user);  // Assumes you're testing an API route

        // Create a medicine
        $medicine = Medicine::factory()->create();

        // Send POST request to add the medicine to favorites
        $response = $this->postJson(route('favourites.store', [$medicine]));

        // Assert that the response indicates the medicine was added to favorites
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'favourite added successfully',
            ]);

        // Assert that the medicine is now in the user's favorites
        $this->assertDatabaseHas('favourites', [
            'user_id' => $user->id,
            'medicine_id' => $medicine->id,
        ]);
    }

    public function test_it_delete_medicine_from_favorites_if_it_already_favorited()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Create a medicine
        $medicine = Medicine::factory()->create();

        // Send POST request to add the medicine to favorites
        $response = $this->postJson(route('favourites.store', [$medicine]));

        // Assert that the response indicates the medicine was added to favorites
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'favourite added successfully',
            ]);

        // Assert that the medicine is now in the user's favorites
        $this->assertDatabaseHas('favourites', [
            'user_id' => $user->id,
            'medicine_id' => $medicine->id,
        ]);
        $response = $this->postJson(route('favourites.store', $medicine));
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'favourite deleted successfully',
            ]);
        $this->assertDatabaseMissing('favourites', [
            'user_id' => $user->id,
            'medicine_id' => $medicine->id,
        ]);
    }
}
