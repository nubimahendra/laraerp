<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\Location::factory(3)->create();
        \App\Models\User::factory(5)->create();
        \App\Models\Category::factory(2)->create()->each(function ($category) {
            \App\Models\Category::factory(3)->create(['parent_id' => $category->id]);
        });
        
        \App\Models\Product::factory(10)->create()->each(function ($product) {
            \App\Models\ProductVariant::factory(2)->create(['product_id' => $product->id]);
        });

        \App\Models\InventoryStock::factory(20)->create();
        \App\Models\InventoryMovement::factory(30)->create();

        \App\Models\Order::factory(10)->create()->each(function ($order) {
            \App\Models\OrderItem::factory(3)->create(['order_id' => $order->id]);
            \App\Models\Payment::factory(1)->create(['order_id' => $order->id]);
        });
    }
}
