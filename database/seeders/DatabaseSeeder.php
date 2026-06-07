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
            'name' => 'System Admin',
            'email' => 'admin@asas.com',
            'password' => Hash::make('admin@1122'),
            'role' => 'admin',
        ]);

        User::factory()->create([
            'username' => 'Fadi',
            'name' => 'fadi',
            'email' => 'fadibotros99@gmail.com',
            'password' => Hash::make('Fadi@1122'),
            'role' => 'user',
        ]);
    }
}
