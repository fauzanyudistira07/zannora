<?php

namespace Database\Seeders;

use App\Models\Airline;
use App\Models\Airplane;
use App\Models\Airport;
use App\Models\Flight;
use App\Models\Seat;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            AirportSeeder::class,
            AirlineSeeder::class,
        ]);

        User::updateOrCreate(
            ['email' => 'user@zannora.com'],
            [
                'name' => 'Demo User',
                'phone' => '08111111111',
                'password' => Hash::make('password'),
                'role' => 'user',
            ]
        );

        $airline = Airline::first();
        $departureAirport = Airport::query()->where('code', 'CGK')->first();
        $arrivalAirport = Airport::query()->where('code', 'DPS')->first();

        if (! $airline || ! $departureAirport || ! $arrivalAirport) {
            return;
        }

        $airplane = Airplane::firstOrCreate(
            ['registration_number' => 'PK-ZNA-01'],
            [
                'airline_id' => $airline->id,
                'model' => 'Airbus A320',
                'capacity' => 12,
                'description' => 'Armada demo untuk pengembangan Zannora.',
            ]
        );

        foreach (range(1, 12) as $number) {
            Seat::firstOrCreate(
                [
                    'airplane_id' => $airplane->id,
                    'seat_number' => 'A'.$number,
                ],
                [
                    'class' => $number <= 2 ? 'business' : 'economy',
                ]
            );
        }

        Flight::firstOrCreate(
            ['flight_number' => 'ZN1001'],
            [
                'airline_id' => $airline->id,
                'airplane_id' => $airplane->id,
                'departure_airport_id' => $departureAirport->id,
                'arrival_airport_id' => $arrivalAirport->id,
                'departure_time' => now()->addDays(2)->setTime(8, 0),
                'arrival_time' => now()->addDays(2)->setTime(10, 0),
                'price' => 850000,
                'status' => 'scheduled',
            ]
        );
    }
}
