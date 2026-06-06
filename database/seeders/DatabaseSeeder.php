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
            'name' => 'creator',
            'email' => 'creator@gmail.com',
            'password' => Hash::make('123123123'),
            'role' => 'creator',
            'email_verified_at' => now(),
        ]);

        User::factory()->create([
            'name' => 'checker',
            'email' => 'checker@gmail.com',
            'password' => Hash::make('123123123'),
            'role' => 'checker',
            'email_verified_at' => now(),
        ]);

        User::factory()->create([
            'name' => 'viewer',
            'email' => 'viewer@gmail.com',
            'password' => Hash::make('123123123'),
            'role' => 'viewer',
            'email_verified_at' => now(),
        ]);
    }
}
