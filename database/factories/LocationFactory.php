<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LocationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->company() . ' ' . $this->faker->randomElement(['Store', 'Warehouse']),
            'type' => $this->faker->randomElement(['warehouse', 'store']),
            'address' => $this->faker->address(),
            'province_id' => $this->faker->numberBetween(1, 34),
            'city_id' => $this->faker->numberBetween(1, 500),
            'subdistrict_id' => $this->faker->numberBetween(1, 5000),
            'postal_code' => $this->faker->postcode(),
            'is_active' => true,
        ];
    }
}