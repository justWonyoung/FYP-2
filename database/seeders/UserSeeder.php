<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin Account
        User::create([
            'full_name' => 'System Administrator',
            'username'  => 'admin',
            'email'     => 'admin@beautykasih.com',
            'phone_number' => '0123456789',
            'password'  => Hash::make('password123'),
            'role'      => 'admin',
            'status'    => 'active',
        ]);

        // Staff Account
        User::create([
            'full_name' => 'Nurul Staff',
            'username'  => 'staff',
            'email'     => 'staff@beautykasih.com',
            'phone_number' => '0123456788',
            'password'  => Hash::make('password123'),
            'role'      => 'staff',
            'status'    => 'active',
        ]);

        // Finance Account
        User::create([
            'full_name' => 'Finance Executive',
            'username'  => 'finance',
            'email'     => 'finance@beautykasih.com',
            'phone_number' => '0123456787',
            'password'  => Hash::make('password123'),
            'role'      => 'finance',
            'status'    => 'active',
        ]);
    }
}