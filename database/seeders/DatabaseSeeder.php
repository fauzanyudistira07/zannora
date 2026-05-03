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
                'role' => 'customer',
            ]
        );

        $airline = Airline::first();
        $departureAirport = Airport::query()->where('code', 'CGK')->first();
        $arrivalAirport = Airport::query()->where('code', 'DPS')->first();

        if (! $airline || ! $departureAirport || ! $arrivalAirport) {
            return;
        }

        $airplaneBlueprints = [
            [
                'registration_number' => 'PK-ZNA-01',
                'model' => 'Airbus A320',
                'capacity' => 24,
                'description' => 'Armada regional untuk rute domestik.',
            ],
            [
                'registration_number' => 'PK-ZNA-02',
                'model' => 'Boeing 737-800',
                'capacity' => 30,
                'description' => 'Armada menengah dengan kabin fleksibel.',
            ],
            [
                'registration_number' => 'PK-ZNA-03',
                'model' => 'Airbus A321neo',
                'capacity' => 36,
                'description' => 'Armada efisien untuk rute populer.',
            ],
            [
                'registration_number' => 'PK-ZNA-04',
                'model' => 'Boeing 737 MAX 8',
                'capacity' => 42,
                'description' => 'Armada high-demand untuk jadwal padat.',
            ],
        ];

        $airplanesByRegistration = collect($airplaneBlueprints)->mapWithKeys(function (array $blueprint) use ($airline) {
            $airplane = Airplane::updateOrCreate(
                ['registration_number' => $blueprint['registration_number']],
                [
                    'airline_id' => $airline->id,
                    'model' => $blueprint['model'],
                    'capacity' => $blueprint['capacity'],
                    'description' => $blueprint['description'],
                ]
            );

            return [$blueprint['registration_number'] => $airplane];
        });

        $seatColumns = ['A', 'B', 'C', 'D', 'E', 'F'];

        foreach ($airplanesByRegistration as $airplane) {
            for ($index = 0; $index < (int) $airplane->capacity; $index++) {
                $row = intdiv($index, count($seatColumns)) + 1;
                $column = $seatColumns[$index % count($seatColumns)];
                $seatNumber = $row.$column;

                Seat::updateOrCreate(
                    [
                        'airplane_id' => $airplane->id,
                        'seat_number' => $seatNumber,
                    ],
                    [
                        'class' => $row <= 2 ? 'business' : 'economy',
                    ]
                );
            }
        }

        $flightBlueprints = [
            ['flight_number' => 'ZN1001', 'registration' => 'PK-ZNA-01', 'offset' => 2, 'departure' => [8, 0], 'arrival' => [10, 0], 'price' => 850000],
            ['flight_number' => 'ZN1002', 'registration' => 'PK-ZNA-02', 'offset' => 2, 'departure' => [13, 30], 'arrival' => [15, 30], 'price' => 920000],
            ['flight_number' => 'ZN1003', 'registration' => 'PK-ZNA-03', 'offset' => 3, 'departure' => [7, 45], 'arrival' => [9, 45], 'price' => 980000],
            ['flight_number' => 'ZN1004', 'registration' => 'PK-ZNA-04', 'offset' => 3, 'departure' => [19, 0], 'arrival' => [21, 0], 'price' => 1050000],
        ];

        foreach ($flightBlueprints as $flight) {
            $airplane = $airplanesByRegistration->get($flight['registration']);
            if (! $airplane) {
                continue;
            }

            Flight::updateOrCreate(
                ['flight_number' => $flight['flight_number']],
                [
                    'airline_id' => $airline->id,
                    'airplane_id' => $airplane->id,
                    'departure_airport_id' => $departureAirport->id,
                    'arrival_airport_id' => $arrivalAirport->id,
                    'departure_time' => now()->addDays($flight['offset'])->setTime($flight['departure'][0], $flight['departure'][1]),
                    'arrival_time' => now()->addDays($flight['offset'])->setTime($flight['arrival'][0], $flight['arrival'][1]),
                    'price' => $flight['price'],
                    'status' => 'scheduled',
                ]
            );
        }
    }
}
