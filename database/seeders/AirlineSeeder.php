<?php

namespace Database\Seeders;

use App\Models\Airline;
use Illuminate\Database\Seeder;

class AirlineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $airlines = [
            ['name' => 'Zannora Air', 'code' => 'ZNA', 'description' => 'Maskapai demo untuk sistem reservasi Zannora.'],
            ['name' => 'Archipelago Sky', 'code' => 'ASK', 'description' => 'Rute domestik dan regional Indonesia.'],
        ];

        foreach ($airlines as $airline) {
            Airline::updateOrCreate(['code' => $airline['code']], $airline);
        }
    }
}
