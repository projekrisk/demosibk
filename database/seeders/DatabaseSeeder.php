<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SubscriptionSeeder::class,
        ]);

        $admin = User::firstOrCreate(
            ['email' => 'admin@sibk-digi.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('iskandar1189'),
                'role' => 'super_admin',
            ]
        );
    }
}