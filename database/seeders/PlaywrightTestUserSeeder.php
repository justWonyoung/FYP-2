<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PlaywrightTestUserSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['admin', 'Playwright Admin', 'playwright_admin', 'e2e-admin@example.test'],
            ['staff', 'Playwright Staff', 'playwright_staff', 'e2e-staff@example.test'],
            ['finance', 'Playwright Finance', 'playwright_finance', 'e2e-finance@example.test'],
        ] as [$role, $fullName, $username, $email]) {
            User::query()->updateOrCreate(
                ['email' => $email],
                [
                    'full_name' => $fullName,
                    'username' => $username,
                    'phone_number' => null,
                    'password' => Hash::make('Password123!'),
                    'role' => $role,
                    'status' => 'active',
                ]
            );
        }
    }
}
