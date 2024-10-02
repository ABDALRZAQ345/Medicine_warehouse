<?php

namespace Tests\Feature\Controllers\Auth;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Order;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    public function test_it_prevents_account_deletion_when_user_has_unpaid_orders()
    {
        // Create a user
        $user = User::factory()->create();

        // Create an unpaid order for the user (payment_status = 0)

        $user->orders()->create();

        // Simulate the user logging in
        Sanctum::actingAs($user);

        // Send a request to delete the account
        $response = $this->deleteJson(route('delete-account'));

        // Assert that the response contains the correct error message
        $response->assertStatus(400)
            ->assertJson([
                'message' => 'account cant be deleted because  you have  unpaid orders please complete payment first
                 \n if you think that you have paid all your orders please contact with us',
            ]);

        // Assert that the user still exists in the database
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    public function test_it_allows_account_deletion_when_user_has_no_unpaid_orders()
    {
        // Create a user
        $user = User::factory()->create();

        // Create a paid order for the user (payment_status = 1)
        $user->orders()->create(
            [
                'payment_status' => 1,
            ]
        );

        // Simulate the user logging in
        Sanctum::actingAs($user);

        // Send a request to delete the account
        $response = $this->deleteJson(route('delete-account'));

        // Assert successful response
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'account deleted successfully',
            ]);

        // Assert that the user's tokens are deleted
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
        ]);

        // Assert that the user is deleted from the database
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
        $this->assertDatabaseMissing('orders', [
            'orderer_id' => $user->id,
        ]);

    }

    public function test_it_prevents_unauthenticated_users_from_deleting_account()
    {
        // Attempt to delete the account without authenticating
        $response = $this->deleteJson(route('delete-account'));

        // Assert that the response is unauthorized (401)
        $response->assertStatus(401);
    }
}
