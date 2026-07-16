<?php

namespace Database\Seeders;

use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Seeder;

class PlatformAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('PLATFORM_ADMIN_EMAIL');
        $password = env('PLATFORM_ADMIN_PASSWORD');

        if (! $email || ! $password) {
            $this->command?->warn('Platform admin was not seeded: PLATFORM_ADMIN_EMAIL and PLATFORM_ADMIN_PASSWORD are required.');
            return;
        }

        User::query()->updateOrCreate(
            ['email' => mb_strtolower($email)],
            [
                'first_name' => env('PLATFORM_ADMIN_FIRST_NAME', 'Platform'),
                'last_name' => env('PLATFORM_ADMIN_LAST_NAME', 'Administrator'),
                'password' => $password,
                'status' => UserStatus::Active,
                'is_platform_admin' => true,
            ],
        );
    }
}

