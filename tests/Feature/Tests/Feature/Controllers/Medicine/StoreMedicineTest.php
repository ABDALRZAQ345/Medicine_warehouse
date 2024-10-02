<?php

namespace Tests\Feature\Controllers\Medicine;

use App\Models\Manufacturer;
use App\Models\Medicine;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StoreMedicineTest extends TestCase
{
    public function test_it_can_store_medicine_with_photo()
    {
        // Set up fake storage for testing file uploads
        Storage::fake('public');

        // Create a user
        $user = User::factory()->create();
        $user->assignRole('admin');
        Sanctum::actingAs($user);
        $manufactrer = Manufacturer::factory()->create(['name' => 'name']);
        // Create a fake photo file
        $photo = UploadedFile::fake()->image('medicine.jpg');

        // Send request to store the medicine
        $response = $this->postJson(route('medicines.store'), [
            'type' => 'tablet',
            'scientific_name' => 'Test Scientific Name',
            'trade_name' => 'Test Trade Name',
            'price' => 100,
            'quantity' => 50,
            'manufacturer_id' => $manufactrer->id,
            'days' => 10,
            'months' => 2,
            'years' => 1,
            'photo' => $photo,
            'discount' => 0,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['medicine']);

        // Assert that the file was stored
        Storage::disk('public')->assertExists('photos/'.$photo->hashName());

        // Assert that the medicine was created in the database
        $this->assertDatabaseHas('medicines', [
            'scientific_name' => 'Test Scientific Name',
            'trade_name' => 'Test Trade Name',
            'photo' => 'photos/'.$photo->hashName(),
        ]);
    }

    public function test_non_admins_can_not_store_medicines()
    {
        // Create a user
        $user = User::factory()->create();

        Sanctum::actingAs($user);
        $manufacturer = Manufacturer::factory()->create(['name' => 'name']);

        // Send request to store the medicine without a photo
        $response = $this->postJson(route('medicines.store'), [
            'type' => 'tablet',
            'scientific_name' => 'Test Scientific Name',
            'trade_name' => 'Test Trade Name',
            'price' => 100,
            'quantity' => 50,
            'manufacturer_id' => $manufacturer->id,
            'days' => 10,
            'months' => 2,
            'years' => 1,
            'discount' => 0,
        ]);

        $response->assertStatus(403);

        // Assert that the medicine was created with the default photo
        $this->assertDatabaseMissing('medicines', [
            'scientific_name' => 'Test Scientific Name',
            'trade_name' => 'Test Trade Name',
            'photo' => 'photos/Untitled.jpeg', // Default photo
        ]);
    }

    public function test_it_can_store_medicine_without_photo()
    {
        // Create a user
        $user = User::factory()->create();
        $user->assignRole('admin');
        Sanctum::actingAs($user);
        $manufactrer = Manufacturer::factory()->create(['name' => 'name']);

        // Send request to store the medicine without a photo
        $response = $this->postJson(route('medicines.store'), [
            'type' => 'tablet',
            'scientific_name' => 'Test Scientific Name',
            'trade_name' => 'Test Trade Name',
            'price' => 100,
            'quantity' => 50,
            'manufacturer_id' => $manufactrer->id,
            'days' => 10,
            'months' => 2,
            'years' => 1,
            'discount' => 0,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['medicine']);

        // Assert that the medicine was created with the default photo
        $this->assertDatabaseHas('medicines', [
            'scientific_name' => 'Test Scientific Name',
            'trade_name' => 'Test Trade Name',
            'photo' => 'photos/Untitled.jpeg', // Default photo
        ]);
    }

    public function test_it_validates_medicine_store_request()
    {
        // Create a user
        $user = User::factory()->create();
        $user->assignRole('admin');

        Sanctum::actingAs($user);
        // Send request with missing required fields
        $response = $this->postJson(route('medicines.store'), []);

        // Assert validation errors
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['type', 'scientific_name', 'trade_name', 'price', 'quantity', 'manufacturer_id', 'days', 'months', 'years', 'discount']);
    }
}
