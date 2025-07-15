<?php

namespace Database\Seeders;

use App\Models\Estate;
use App\Models\Tenant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EstateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Estate::create([
            'name' => 'mapo estate',
            'full_address' => 'kilometer 5, lagos',
            'longitude' => '6.2233501',
            'latitude' => '7.0834310'
        ]);
    }
}
