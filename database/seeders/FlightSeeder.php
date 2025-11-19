<?php

namespace Database\Seeders;

use App\Models\Flight;
use Illuminate\Database\Seeder;

class FlightSeeder extends Seeder
{
    public function run(): void
    {
        $flights = [
            [
                'flight_number' => 'MS001',
                'carrier_id' => 1, // EgyptAir
                'aircraft_id' => 1, // Boeing 737-800
            ],
            [
                'flight_number' => 'EK202',
                'carrier_id' => 2, // Emirates
                'aircraft_id' => 2, // Airbus A320
            ],
            [
                'flight_number' => 'BA123',
                'carrier_id' => 3, // British Airways
                'aircraft_id' => 3, // Boeing 777-300ER
            ],
            [
                'flight_number' => 'LH456',
                'carrier_id' => 4, // Lufthansa
                'aircraft_id' => 4, // Airbus A350
            ],
            [
                'flight_number' => 'MS002',
                'carrier_id' => 1,
                'aircraft_id' => 1,
            ],
            [
                'flight_number' => 'EK203',
                'carrier_id' => 2,
                'aircraft_id' => 2,
            ]
        ];

        foreach ($flights as $flight) {
            Flight::create($flight);
        }
    }
}