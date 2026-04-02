<?php

namespace Database\Seeders;

use App\Models\Airport;
use Illuminate\Database\Seeder;

class AirportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $airports = [
            ['code' => 'CGK', 'name' => 'Soekarno Hatta', 'city' => 'Jakarta', 'country' => 'Indonesia'],
            ['code' => 'DPS', 'name' => 'Ngurah Rai', 'city' => 'Denpasar', 'country' => 'Indonesia'],
            ['code' => 'SUB', 'name' => 'Juanda', 'city' => 'Surabaya', 'country' => 'Indonesia'],
        ];

        foreach ($airports as $airport) {
            Airport::updateOrCreate(['code' => $airport['code']], $airport);
        }
    }
}
