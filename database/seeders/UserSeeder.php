<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->delete();

        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'balance' => 5000.00,
        ]);

        User::factory(10)->create();
    }
}
