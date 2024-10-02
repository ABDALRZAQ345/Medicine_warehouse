<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $Roles = ['admin', 'user'];
        foreach ($Roles as $Role) {
            Role::create([
                'name' => $Role,
            ]);

        }

        $local_admin = User::create([
            'first_name' => 'admin',
            'last_name' => 'admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('secretsecret'),
        ])->assignRole('admin');
        $chang_role_permission = Permission::create(['name' => 'chang_role_permission']);
        $local_admin->givePermissionTo($chang_role_permission);
    }
}
