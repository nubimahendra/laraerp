<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Location;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 50000, 1000000);
        return [
            'id' => $this->faker->uuid(),
            'invoice_number' => 'INV-' . strtoupper($this->faker->unique()->bothify('????-####')),
            'user_id' => User::factory(),
            'customer_name' => $this->faker->name(),
            'customer_phone' => $this->faker->phoneNumber(),
            'location_id' => Location::factory(),
            'source' => $this->faker->randomElement(['online', 'pos']),
            'status' => $this->faker->randomElement(['pending', 'processing', 'shipped', 'completed', 'cancelled']),
            'subtotal' => $subtotal,
            'discount_amount' => 0,
            'tax_amount' => $subtotal * 0.11,
            'shipping_cost' => 15000,
            'grand_total' => $subtotal + ($subtotal * 0.11) + 15000,
            'shipping_courier' => 'JNE',
            'shipping_service' => 'REG',
            'tracking_number' => strtoupper($this->faker->bothify('TRACK#####')),
        ];
    }
}