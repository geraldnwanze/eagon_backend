<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $tenant = Tenant::all();

        $users = [
            [
                'tenant_key' => $tenant[0]->key,
                'full_name' => 'Test User',
                'email' => 'test@example.com',
                'fcm_token' => '1234',
                'is_first_login' => false,
                'password' => '123456',
            ],
            [
                'tenant_key' => $tenant[0]->key,
                'full_name' => 'Another Test User',
                'email' => 'testuser@example.com',
                'fcm_token' => '1111',
                'is_first_login' => false,
                'password' => '123456',
            ],
            [
                'tenant_key' => $tenant[0]->key,
                'full_name' => 'Admin User',
                'email' => 'admin@example.com',
                'fcm_token' => '1111',
                'is_first_login' => false,
                'role' => RoleEnum::ADMIN->value,
                'password' => '123456',
                'email_verified_at' => now()
            ],
            [
                'tenant_key' => $tenant[0]->key,
                'full_name' => 'Super Admin User',
                'email' => 'superadmin@example.com',
                'fcm_token' => '1111',
                'is_first_login' => false,
                'role' => RoleEnum::SUPER_ADMIN->value,
                'password' => '123456',
                'email_verified_at' => now()
            ]
        ];

        for ($i=0; $i < count($users); $i++) {
            User::create($users[$i]);
        }
    }
}
