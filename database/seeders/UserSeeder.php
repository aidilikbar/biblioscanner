<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@biblioscanner.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'is_admin' => true, 
        ]);

        User::create([
            'name' => 'Regular User',
            'email' => 'user@biblioscanner.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'is_admin' => false,
        ]);
    }
}