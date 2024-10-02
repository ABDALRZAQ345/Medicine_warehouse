<?php

namespace Tests\Feature\Controllers\Medicine;

use App\Http\Requests\Medicine\MedicineRequest;
use App\Http\Resources\MedicineResource;
use App\Models\Manufacturer;
use App\Models\Medicine;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateMedicineTest extends TestCase
{
    public function test_it_can_update_medicine_with_valid_data()
    {
        // Create a user and a medicine
        $user = User::factory()->create();
        $user->assignRole('admin');
        $medicine = Medicine::factory()->create();
        Sanctum::actingAs($user);
        $manufacturer= Manufacturer::factory()->create(['name'=> 'd']);
        // Send update request with valid data
        $response = $this->putJson(route('medicines.update', $medicine->id), [
            'type' => 'capsule',
            'scientific_name' => 'Updated Scientific Name',
            'trade_name' => 'Updated Trade Name',
            'price' => 200,
            'quantity' => 100,
            'manufacturer_id' => $manufacturer->id,
            'days' => 30,
            'months' => 2,
            'years' => 1,
            'discount' => 0,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['medicine']);

        // Assert that the medicine was updated in the database
        $this->assertDatabaseHas('medicines', [
            'id' => $medicine->id,
            'scientific_name' => 'Updated Scientific Name',
            'trade_name' => 'Updated Trade Name',
        ]);
    }
    ///
    public function test_non_admins_cannot_update_medicine()
    {
        // Create a user and a medicine
        $user = User::factory()->create();
        $medicine = Medicine::factory()->create();
        Sanctum::actingAs($user);
        $manufacturer= Manufacturer::factory()->create(['name'=> 'd']);
        // Send update request with valid data
        $response = $this->putJson(route('medicines.update', $medicine->id), [
            'type' => 'capsule',
            'scientific_name' => 'Updated Scientific Name',
            'trade_name' => 'Updated Trade Name',
            'price' => 200,
            'quantity' => 100,
            'manufacturer_id' => $manufacturer->id,
            'days' => 30,
            'months' => 2,
            'years' => 1,
            'discount' => 0,
        ]);

        $response->assertStatus(403);


        // Assert that the medicine was updated in the database
        $this->assertDatabaseMissing('medicines', [
            'id' => $medicine->id,
            'scientific_name' => 'Updated Scientific Name',
            'trade_name' => 'Updated Trade Name',
        ]);
    }
    public function test_it_validates_medicine_update_request()
    {
        // Create a user and a medicine
        $user = User::factory()->create();
        $user->assignRole('admin');
        $medicine = Medicine::factory()->create();
        Sanctum::actingAs($user);

        // Send update request with missing required fields
        $response = $this->putJson(route('medicines.update', $medicine->id), []);

        // Assert validation errors
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['type', 'scientific_name', 'trade_name', 'price', 'quantity', 'manufacturer_id', 'days', 'months', 'years','discount']);
    }


}
