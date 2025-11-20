<?php

namespace Database\Seeders;

use App\Models\Flight;
use App\Models\Location;
use Illuminate\Database\Seeder;

class FlightSeeder extends Seeder
{
    public function run(): void
    {
        // Get locations for origin and destination
        $cairo = Location::where('city', 'Cairo')->first();
        $alexandria = Location::where('city', 'Alexandria')->first();
        $sharm = Location::where('city', 'Sharm El-Sheikh')->first();
        $hurghada = Location::where('city', 'Hurghada')->first();

        $flights = [
            [
                'flight_number' => 1001,
                'carrier_id' => 1, // EgyptAir
                'aircraft_id' => 1, // Boeing 737-800
                'origin_id' => $cairo?->id ?? 1,
                'destination_id' => $alexandria?->id ?? 2,
                'departure_time' => now()->addDays(1)->setTime(10, 0),
                'arrival_time' => now()->addDays(1)->setTime(11, 30),
                'duration_minutes' => 90,
            ],
            [
                'flight_number' => 2020,
                'carrier_id' => 2, // Emirates
                'aircraft_id' => 2, // Airbus A320
                'origin_id' => $cairo?->id ?? 1,
                'destination_id' => $sharm?->id ?? 4,
                'departure_time' => now()->addDays(2)->setTime(14, 0),
                'arrival_time' => now()->addDays(2)->setTime(15, 45),
                'duration_minutes' => 105,
            ],
            [
                'flight_number' => 1234,
                'carrier_id' => 3, // British Airways
                'aircraft_id' => 3, // Boeing 777-300ER
                'origin_id' => $cairo?->id ?? 1,
                'destination_id' => $hurghada?->id ?? 5,
                'departure_time' => now()->addDays(3)->setTime(8, 30),
                'arrival_time' => now()->addDays(3)->setTime(10, 15),
                'duration_minutes' => 105,
            ],
            [
                'flight_number' => 4567,
                'carrier_id' => 4, // Lufthansa
                'aircraft_id' => 4, // Airbus A350
                'origin_id' => $alexandria?->id ?? 2,
                'destination_id' => $cairo?->id ?? 1,
                'departure_time' => now()->addDays(4)->setTime(16, 0),
                'arrival_time' => now()->addDays(4)->setTime(17, 30),
                'duration_minutes' => 90,
            ],
            [
                'flight_number' => 1002,
                'carrier_id' => 1,
                'aircraft_id' => 1,
                'origin_id' => $sharm?->id ?? 4,
                'destination_id' => $cairo?->id ?? 1,
                'departure_time' => now()->addDays(5)->setTime(12, 0),
                'arrival_time' => now()->addDays(5)->setTime(13, 45),
                'duration_minutes' => 105,
            ],
            [
                'flight_number' => 2021,
                'carrier_id' => 2,
                'aircraft_id' => 2,
                'origin_id' => $hurghada?->id ?? 5,
                'destination_id' => $cairo?->id ?? 1,
                'departure_time' => now()->addDays(6)->setTime(18, 0),
                'arrival_time' => now()->addDays(6)->setTime(19, 45),
                'duration_minutes' => 105,
            ],
        ];

        foreach ($flights as $flight) {
            Flight::create($flight);
        }
    }
}