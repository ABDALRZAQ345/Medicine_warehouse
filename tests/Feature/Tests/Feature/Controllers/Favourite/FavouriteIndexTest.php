<?php

namespace Tests\Feature\Controllers\Auth;

use App\Models\Medicine;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FavouriteIndexTest extends TestCase
{
    protected $favouriteService;

    protected function setUp(): void
    {
        parent::setUp();

        // Inject the service
        $this->favouriteService = new \App\Services\FavouriteService;
    }

    public function test_it_shows_all_favourites_for_authenticated_user()
    {
        // Arrange: Create a user and authenticate with Sanctum
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Create some favorite medicines for the user (assuming many-to-many relationship)
        $medicines = Medicine::factory(3)->create();  // Assuming you have a Medicine factory

        // Attach these medicines as favorites to the user
        foreach ($medicines as $medicine) {
            $user->favourites()->attach($medicine->id);  // Assuming you have a favourites relationship
        }

        // Act: Send a GET request to retrieve the user's favorites
        $response = $this->getJson('/api/favourites');

        // Assert: Check that the response status is 200
        $response->assertStatus(200);

        // Assert: Check the structure of the JSON response (without pivot or any unwanted fields)
        $response->assertJsonStructure([
            'favourites' => [
                '*' => [
                    'id',
                    'creator_id',
                    'manufacturer_id',
                    'scientific_name',
                    'trade_name',
                    'type',
                    'quantity',
                    'price',
                    'expires_at',
                    'deleted_at',
                    'created_at',
                    'updated_at',
                    'discount',
                    'photo',
                ],
            ],
        ]);

        // Assert that the IDs of the medicines in the response match the created ones
        $response->assertJsonFragment(['id' => $medicines[0]->id]);
        $response->assertJsonFragment(['id' => $medicines[1]->id]);
        $response->assertJsonFragment(['id' => $medicines[2]->id]);
    }
}
