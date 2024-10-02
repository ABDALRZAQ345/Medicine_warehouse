<?php

namespace Tests\Feature\Controllers\Medicine;

use App\Models\Medicine;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MedicineIndexTest extends TestCase
{
    public function test_admin_can_filter_expired_and_trashed_medicines()
    {
        // Create an admin user
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Sanctum::actingAs($admin);

        // Create medicines: one expired, one active, and one trashed
        $expiredMedicine = Medicine::factory()->create(['expires_at' => now()->subDays(10)]);
        $activeMedicine = Medicine::factory()->create(['expires_at' => now()->addDays(10)]);
        $trashedMedicine = Medicine::factory()->create();
        $trashedMedicine->delete(); // Soft delete this medicine

        // Test filtering for expired medicines
        $response = $this->getJson(route('medicines.index', ['expired' => 1]));
        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $expiredMedicine->id])
            ->assertJsonMissing(['id' => $activeMedicine->id]);

        // Test filtering for trashed medicines
        $response = $this->getJson(route('medicines.index', ['trashed' => 1]));
        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $trashedMedicine->id])
            ->assertJsonMissing(['id' => $activeMedicine->id]);

        // Test filtering for non-expired and non-trashed medicines
        $response = $this->getJson(route('medicines.index'));
        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $activeMedicine->id])
            ->assertJsonMissing(['id' => $expiredMedicine->id])
            ->assertJsonMissing(['id' => $trashedMedicine->id]);
    }

    public function test_non_admin_users_only_see_active_medicines()
    {
        // Create a regular user
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Create medicines: one expired, one active
        $expiredMedicine = Medicine::factory()->create(['expires_at' => now()->subDays(10)]);
        $activeMedicine = Medicine::factory()->create(['expires_at' => now()->addDays(10)]);

        // Non-admin users should only see active medicines
        $response = $this->getJson(route('medicines.index'));
        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $activeMedicine->id])
            ->assertJsonMissing(['id' => $expiredMedicine->id]);
    }
}
