<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;

class ProductVariantFactory extends Factory
{
    public function definition(): array
    {
        $cost = $this->faker->randomFloat(2, 10000, 500000);
        return [
            'product_id' => Product::factory(),
            'sku' => strtoupper($this->faker->unique()->bothify('SKU-####-????')),
            'barcode' => $this->faker->unique()->ean13(),
            'attribute_json' => ['color' => $this->faker->safeColorName(), 'size' => $this->faker->randomElement(['S', 'M', 'L', 'XL'])],
            'cost_price' => $cost,
            'selling_price' => $cost * 1.5,
        ];
    }
}