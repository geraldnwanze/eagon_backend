<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Estate;
use App\Models\EstateLocation;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UserLocation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estate = Estate::all();
        $users = User::where('role', RoleEnum::RESIDENT->value)->get();
        $tenant = Tenant::all();
        $estateLocation = EstateLocation::first();

        for ($i =0; $i < count($users); $i++) {
            UserLocation::create([
                'tenant_key' => $tenant[0]->key,
                'estate_id' => $estate[0]->id,
                'user_id' => $users[$i]->id,
                'estate_location_id' => $estateLocation->id,
            ]);
        }
    }
}
