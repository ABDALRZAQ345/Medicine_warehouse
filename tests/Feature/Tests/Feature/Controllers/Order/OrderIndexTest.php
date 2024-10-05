<?php

namespace Tests\Feature\Controllers\Order;

use App\Models\Order;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrderIndexTest extends TestCase
{
    public function test_admin_can_see_all_orders()
    {
        // Create admin user
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $user = User::factory()->create()->assignRole('user');
        $user2 = User::factory()->create()->assignRole('user');

        // Create orders
        $order1 = $user->orders()->create([
            'total_price' => 2000,
        ]);
        $order2 = $user2->orders()->create([
            'total_price' => 2000,
        ]);

        // Authenticate as admin
        Sanctum::actingAs($admin);

        // Make a GET request to fetch orders
        $response = $this->getJson(route('orders.index'));

        // Assert response status
        $response->assertStatus(200);

        // Assert that both orders are visible to the admin
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['id' => $order1->id]);
        $response->assertJsonFragment(['id' => $order2->id]);
    }

    public function test_user_can_only_see_their_own_orders()
    {
        // Create user
        $user = User::factory()->create();
        $user->assignRole('user');

        // Create orders for this user
        $order1 = $user->orders()->create([]);
        $order2 = $user->orders()->create([]);

        // Create an order for another user
        $otherOrder = Order::factory()->create();

        // Authenticate as the user
        Sanctum::actingAs($user);

        // Make a GET request to fetch orders
        $response = $this->getJson(route('orders.index'));

        // Assert response status
        $response->assertStatus(200);

        // Assert that only the user's orders are visible
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['id' => $order1->id]);
        $response->assertJsonFragment(['id' => $order2->id]);
        $response->assertJsonMissing(['id' => $otherOrder->id]);
    }

    ///
    public function test_order_filtering_by_payment_status_and_status()
    {
        // Create user
        $user = User::factory()->create();
        $user->assignRole('user');

        // Create orders for this user with different statuses
        $order1 = $user->orders()->create(['payment_status' => 1, 'status' => 0]);
        $order2 = $user->orders()->create(['payment_status' => 0, 'status' => 1]);
        // Authenticate as the user
        Sanctum::actingAs($user);

        // Make a GET request with filters
        $response = $this->getJson(route('orders.index', ['payment_status' => 1, 'status' => 0]));

        // Assert response status
        $response->assertStatus(200);

        // Assert that only the filtered order is visible
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $order1->id]);
        $response->assertJsonMissing(['id' => $order2->id]);
    }
}
