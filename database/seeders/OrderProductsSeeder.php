<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (range(1, 20) as $index) {
            DB::table('order_products')->insert([
                'order_id' => rand(1, 10),
                'product_id' => rand(1, 10),
                'quantity' => rand(1, 5),
                'price' => rand(100, 1000) / 100,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
