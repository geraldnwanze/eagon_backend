<?php

namespace Database\Seeders;

use App\Models\Estate;
use App\Models\EstateLocation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EstateLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estate = Estate::first();
        
        EstateLocation::create([
            'tenant_key' => $estate->tenant->key,
            'estate_id' => $estate->id,
            'full_address' => fake()->address(),
            'longitude' => fake()->longitude(),
            'latitude' => fake()->latitude()
        ]);
    }
}
