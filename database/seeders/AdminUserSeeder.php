<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@zannora.com'],
            [
                'name' => 'Administrator',
                'phone' => '08123456789',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );
    }
}
