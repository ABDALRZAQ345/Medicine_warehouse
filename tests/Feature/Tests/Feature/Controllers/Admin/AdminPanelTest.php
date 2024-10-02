<?php

namespace Tests\Feature\Controllers\Auth;

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class adminPanelTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_it_can_change_role_if_admin_have_permission()
    {
        $user = User::factory()->create();
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $admin->givePermissionTo('chang_role_permission');
        Sanctum::actingAs($admin);
        $response = $this->postJson(route('change_role', $user->id), ['role' => 'admin']);
        $response->assertStatus(200);
        $this->assertTrue($user->hasRole('admin'));

    }

    public function test_it_can_not_change_role_if_its_dont_have_permission()
    {
        $user = User::factory()->create();
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Sanctum::actingAs($admin);
        $response = $this->postJson(route('change_role', $user->id), ['role' => 'admin']);
        $response->assertStatus(403);
        $this->assertFalse($user->hasRole('admin'));

    }

    public function test_it_can_not_change_role_if_its_not_admin()
    {
        $user = User::factory()->create();
        $fake_admin = User::factory()->create();
        $fake_admin->givePermissionTo('chang_role_permission');
        Sanctum::actingAs($fake_admin);
        $response = $this->postJson(route('change_role', $user->id), ['role' => 'admin']);
        $response->assertStatus(403);
        $this->assertFalse($user->hasRole('admin'));
    }
}
