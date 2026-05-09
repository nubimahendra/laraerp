<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Order;
use App\Models\ProductVariant;

class OrderItemFactory extends Factory
{
    public function definition(): array
    {
        $qty = $this->faker->numberBetween(1, 5);
        $price = $this->faker->randomFloat(2, 10000, 200000);
        return [
            'order_id' => Order::factory(),
            'product_variant_id' => ProductVariant::factory(),
            'qty' => $qty,
            'unit_price' => $price,
            'subtotal' => $qty * $price,
        ];
    }
}