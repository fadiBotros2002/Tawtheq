<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'username' => 'admin',
            'name' => 'مدير النظام',
            'email' => 'admin@diwan.local',
            'password' => Hash::make('123123123'),
            'role' => 'admin',
        ]);

        User::factory()->create([
            'username' => 'ahmad',
            'name' => 'أحمد',
            'email' => 'ahmad@diwan.local',
            'password' => Hash::make('123123123'),
            'role' => 'user',
        ]);
    }
}
