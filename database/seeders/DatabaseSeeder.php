<?php

namespace Database\Seeders;

use App\Models\EmailVerificationToken;
use App\Models\Manufacturer;
use App\Models\Post;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Manufacturer::factory(100)->create(['name'=> fake()->name]);
        $this->call(MedicineSeeder::class);
        $this->call(RolesAndPermissionsSeeder::class);
    }
}
