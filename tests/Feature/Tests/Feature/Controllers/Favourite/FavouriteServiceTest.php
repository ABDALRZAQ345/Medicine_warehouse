<?php

namespace Tests\Feature\Controllers\Auth;

use App\Models\Medicine;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FavouriteServiceTest extends TestCase
{
    protected $favouriteService;

    protected function setUp(): void
    {
        parent::setUp();

        // Inject the service
        $this->favouriteService = new \App\Services\FavouriteService;
    }

    public function it_does_not_add_more_than_allowed_favourites()
    {
        // Set max favourites to 1 for this test
        config(['app.data.max_favourites' => 1]);

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $medicines = Medicine::factory(2)->create();

        // Add the first favourite
        $this->favouriteService->store($user, $medicines[0]);

        // Attempt to add the second favourite
        $response = $this->favouriteService->store($user, $medicines[1]);

        // Assert that the limit is enforced
        $response->assertJson([
            'message' => 'favourite limit exceeded you cant add more than 1 medicines to favourites',
        ]);

        // Assert the second favourite was not added
        $this->assertDatabaseMissing('favourites', [
            'user_id' => $user->id,
            'medicine_id' => $medicines[1]->id,
        ]);

        // Ensure the first favourite is still there
        $this->assertDatabaseHas('favourites', [
            'user_id' => $user->id,
            'medicine_id' => $medicines[0]->id,
        ]);
    }
}
