<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class OrdersSeeder extends Seeder
{
    public function run()
    {
        DB::table('orders')->delete();

        foreach (range(1, 10) as $index) {
            DB::table('orders')->insert([
                'number' => Str::padLeft($index, 3, '0'),
                'status' => $this->getRandomStatus(),
                'user_id' => rand(1, 10),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function getRandomStatus()
    {
        $statuses = ['draft', 'approved', 'cancelled'];
        return $statuses[array_rand($statuses)];
    }
}
