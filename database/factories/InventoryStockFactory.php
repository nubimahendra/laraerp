<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Location;
use App\Models\ProductVariant;

class InventoryStockFactory extends Factory
{
    public function definition(): array
    {
        return [
            'location_id' => Location::factory(),
            'product_variant_id' => ProductVariant::factory(),
            'qty' => $this->faker->numberBetween(10, 100),
            'min_qty' => $this->faker->numberBetween(5, 10),
        ];
    }
}