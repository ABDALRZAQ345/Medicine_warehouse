<?php

namespace Tests\Feature\Controllers\Auth;

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    public function test_authorized_user_can_see_his_profile()
    {

        $user = User::factory()->create();
        $this->actingAs($user);
        Sanctum::actingAs($user);
        $response = $this->getJson('api/profile');
        $response->assertStatus(200)->assertJsonStructure(
            [
                'user' => [
                    'id',
                ],
            ]
        );

    }
}
