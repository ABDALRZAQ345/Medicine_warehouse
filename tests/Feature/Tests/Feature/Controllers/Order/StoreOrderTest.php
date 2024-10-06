<?php

namespace Tests\Feature\Controllers\Order;

use App\Models\Medicine;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StoreOrderTest extends TestCase
{
    public function test_it_places_an_order_successfully()
    {
        $user = User::factory()->create();
        $user->assignRole('user');
        $medicine = Medicine::factory()->create(['quantity' => 50, 'price' => 100]);

        Sanctum::actingAs($user);

        $response = $this->postJson(route('orders.store'), [
            'medicines' => [
                ['id' => $medicine->id, 'quantity' => 5],
            ],
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Order placed successfully',
                'order' => [
                    'orderer_id' => $user->id,
                ],
            ]);

        $this->assertDatabaseHas('orders', ['orderer_id' => $user->id]);
        $this->assertDatabaseHas('medicines', ['quantity' => 45]); // Updated stock
        $this->assertDatabaseHas('order_items', ['medicine_id' => $medicine->id]);
    }

    public function test_un_verified_email_can_not_store_order()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);
        $user->assignRole('user');

        $medicine = Medicine::factory()->create(['quantity' => 50, 'price' => 100]);

        Sanctum::actingAs($user);

        $response = $this->postJson(route('orders.store'), [
            'medicines' => [
                ['id' => $medicine->id, 'quantity' => 5],
            ],
        ]);

        $response->assertStatus(401);

        $this->assertDatabaseMissing('orders', ['orderer_id' => $user->id]);
        $this->assertDatabaseMissing('medicines', ['quantity' => 45]); // Updated stock
        $this->assertDatabaseMissing('order_items', ['medicine_id' => $medicine->id]);
    }

    public function test_it_fails_to_place_order_if_not_enough_stock()
    {
        $user = User::factory()->create();
        $user->assignRole('user');
        $medicine = Medicine::factory()->create(['quantity' => 5]);

        Sanctum::actingAs($user);

        $response = $this->postJson(route('orders.store'), [
            'medicines' => [
                ['id' => $medicine->id, 'quantity' => 10],
            ],
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'error' => 'Not enough medicines available for medicine with id '.$medicine->id,
            ]);
    }

    public function test_admins_can_not_place_order()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $medicine = Medicine::factory()->create(['quantity' => 5]);

        Sanctum::actingAs($user);

        $response = $this->postJson(route('orders.store'), [
            'medicines' => [
                ['id' => $medicine->id, 'quantity' => 10],
            ],
        ]);

        $response->assertStatus(403);
    }

    public function test_it_fails_when_medicines_is_missing()
    {
        $user = User::factory()->create()->assignRole('user');

        Sanctum::actingAs($user);

        $response = $this->postJson(route('orders.store'), []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('medicines');
    }

    public function test_it_fails_when_medicine_id_is_invalid()
    {
        $user = User::factory()->create()->assignRole('user');
        $medicine = Medicine::factory()->create();

        $this->actingAs($user, 'sanctum');

        $response = $this->postJson(route('orders.store'), [
            'medicines' => [
                [
                    'id' => 999, // Non-existent medicine ID
                    'quantity' => 5,
                ],
            ],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('medicines.0.id');
    }

    public function test_it_fails_when_quantity_is_missing_or_invalid()
    {
        $user = User::factory()->create()->assignRole('user');
        $medicine = Medicine::factory()->create();

        $this->actingAs($user, 'sanctum');

        // Test missing quantity
        $response = $this->postJson(route('orders.store'), [
            'medicines' => [
                [
                    'id' => $medicine->id,
                    // 'quantity' => missing
                ],
            ],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('medicines.0.quantity');

        // Test invalid quantity
        $response = $this->postJson(route('orders.store'), [
            'medicines' => [
                [
                    'id' => $medicine->id,
                    'quantity' => -1, // Invalid quantity
                ],
            ],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('medicines.0.quantity');
    }
}
