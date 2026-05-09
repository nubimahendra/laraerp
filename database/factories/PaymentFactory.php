<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Order;

class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'amount' => $this->faker->randomFloat(2, 50000, 1000000),
            'payment_method' => $this->faker->randomElement(['cash', 'qris', 'midtrans_bca_va']),
            'status' => $this->faker->randomElement(['unpaid', 'paid', 'failed', 'refunded']),
            'payment_gateway_ref' => $this->faker->uuid(),
        ];
    }
}