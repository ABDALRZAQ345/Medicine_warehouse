<?php

namespace Database\Factories;

use App\Models\Manufacturer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Medicine>
 */
class MedicineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'scientific_name' => fake()->name,
            'trade_name' => fake()->name,
            'type' => fake()->name,
            'quantity' => fake()->numberBetween(0, 1000),
            'price' => fake()->numberBetween(0, 1000),
            'creator_id' => 1,
            'expires_at' => now()->addMonths(6),
            'manufacturer_id' => Manufacturer::factory()->create(['name' => fake()->name])->id,
        ];
    }
}
