<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Location;
use App\Models\ProductVariant;
use App\Models\User;

class InventoryMovementFactory extends Factory
{
    public function definition(): array
    {
        return [
            'location_id' => Location::factory(),
            'product_variant_id' => ProductVariant::factory(),
            'type' => $this->faker->randomElement(['in', 'out', 'transfer', 'adjustment']),
            'qty' => $this->faker->numberBetween(-50, 50),
            'remarks' => $this->faker->sentence(),
            'created_by' => User::factory(),
            'created_at' => now(),
        ];
    }
}