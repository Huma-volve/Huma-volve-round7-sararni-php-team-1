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
        $luxor = Location::where('city', 'Luxor')->first();
        $aswan = Location::where('city', 'Aswan')->first();
        $dubai = Location::where('city', 'Dubai')->first();
        $paris = Location::where('city', 'Paris')->first();
        $london = Location::where('city', 'London')->first();
        $rome = Location::where('city', 'Rome')->first();
        $barcelona = Location::where('city', 'Barcelona')->first();
        $istanbul = Location::where('city', 'Istanbul')->first();
        $athens = Location::where('city', 'Athens')->first();
        $amsterdam = Location::where('city', 'Amsterdam')->first();
        $berlin = Location::where('city', 'Berlin')->first();
        $madrid = Location::where('city', 'Madrid')->first();
        $riyadh = Location::where('city', 'Riyadh')->first();
        $jeddah = Location::where('city', 'Jeddah')->first();
        $doha = Location::where('city', 'Doha')->first();
        $abudhabi = Location::where('city', 'Abu Dhabi')->first();

        $flights = [
            // International routes
            [
                'flight_number' => 'MS777',
                'carrier_id' => 1, // EgyptAir
                'aircraft_id' => 1, // Boeing 737-800
                'origin_id' => $cairo?->id ?? 1,
                'destination_id' => $dubai?->id ?? 6,
                'departure_time' => now()->addDays(1)->setTime(10, 0),
                'arrival_time' => now()->addDays(1)->setTime(14, 0),
                'duration_minutes' => 240,
            ],
            [
                'flight_number' => 'MS888',
                'carrier_id' => 1,
                'aircraft_id' => 1,
                'origin_id' => $cairo?->id ?? 1,
                'destination_id' => $paris?->id ?? 7,
                'departure_time' => now()->addDays(2)->setTime(8, 0),
                'arrival_time' => now()->addDays(2)->setTime(13, 30),
                'duration_minutes' => 330,
            ],
            [
                'flight_number' => 'MS999',
                'carrier_id' => 1,
                'aircraft_id' => 1,
                'origin_id' => $cairo?->id ?? 1,
                'destination_id' => $london?->id ?? 8,
                'departure_time' => now()->addDays(3)->setTime(9, 30),
                'arrival_time' => now()->addDays(3)->setTime(15, 0),
                'duration_minutes' => 330,
            ],
            [
                'flight_number' => 'EK123',
                'carrier_id' => 2, // Emirates
                'aircraft_id' => 2,
                'origin_id' => $dubai?->id ?? 6,
                'destination_id' => $cairo?->id ?? 1,
                'departure_time' => now()->addDays(1)->setTime(15, 0),
                'arrival_time' => now()->addDays(1)->setTime(17, 0),
                'duration_minutes' => 120,
            ],
            [
                'flight_number' => 'EK456',
                'carrier_id' => 2,
                'aircraft_id' => 2,
                'origin_id' => $dubai?->id ?? 6,
                'destination_id' => $paris?->id ?? 7,
                'departure_time' => now()->addDays(2)->setTime(10, 0),
                'arrival_time' => now()->addDays(2)->setTime(15, 30),
                'duration_minutes' => 330,
            ],
            [
                'flight_number' => 'AF789',
                'carrier_id' => 3, // British Airways
                'aircraft_id' => 3,
                'origin_id' => $paris?->id ?? 7,
                'destination_id' => $cairo?->id ?? 1,
                'departure_time' => now()->addDays(3)->setTime(14, 0),
                'arrival_time' => now()->addDays(3)->setTime(19, 30),
                'duration_minutes' => 330,
            ],
            [
                'flight_number' => 'AF321',
                'carrier_id' => 3,
                'aircraft_id' => 3,
                'origin_id' => $paris?->id ?? 7,
                'destination_id' => $london?->id ?? 8,
                'departure_time' => now()->addDays(1)->setTime(9, 0),
                'arrival_time' => now()->addDays(1)->setTime(10, 15),
                'duration_minutes' => 75,
            ],
            // Domestic routes
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
            // More Egypt domestic flights
            [
                'flight_number' => 'MS555',
                'carrier_id' => 1,
                'aircraft_id' => 1,
                'origin_id' => $cairo?->id ?? 1,
                'destination_id' => $luxor?->id ?? 6,
                'departure_time' => now()->addDays(1)->setTime(7, 0),
                'arrival_time' => now()->addDays(1)->setTime(8, 30),
                'duration_minutes' => 90,
            ],
            [
                'flight_number' => 'MS666',
                'carrier_id' => 1,
                'aircraft_id' => 1,
                'origin_id' => $cairo?->id ?? 1,
                'destination_id' => $aswan?->id ?? 7,
                'departure_time' => now()->addDays(2)->setTime(8, 0),
                'arrival_time' => now()->addDays(2)->setTime(9, 45),
                'duration_minutes' => 105,
            ],
            // More international flights from Cairo
            [
                'flight_number' => 'MS111',
                'carrier_id' => 1,
                'aircraft_id' => 1,
                'origin_id' => $cairo?->id ?? 1,
                'destination_id' => $rome?->id ?? 9,
                'departure_time' => now()->addDays(3)->setTime(10, 0),
                'arrival_time' => now()->addDays(3)->setTime(13, 0),
                'duration_minutes' => 180,
            ],
            [
                'flight_number' => 'MS222',
                'carrier_id' => 1,
                'aircraft_id' => 1,
                'origin_id' => $cairo?->id ?? 1,
                'destination_id' => $istanbul?->id ?? 10,
                'departure_time' => now()->addDays(4)->setTime(11, 0),
                'arrival_time' => now()->addDays(4)->setTime(13, 30),
                'duration_minutes' => 150,
            ],
            [
                'flight_number' => 'MS333',
                'carrier_id' => 1,
                'aircraft_id' => 1,
                'origin_id' => $cairo?->id ?? 1,
                'destination_id' => $athens?->id ?? 11,
                'departure_time' => now()->addDays(5)->setTime(9, 0),
                'arrival_time' => now()->addDays(5)->setTime(11, 30),
                'duration_minutes' => 150,
            ],
            [
                'flight_number' => 'MS444',
                'carrier_id' => 1,
                'aircraft_id' => 1,
                'origin_id' => $cairo?->id ?? 1,
                'destination_id' => $riyadh?->id ?? 12,
                'departure_time' => now()->addDays(6)->setTime(12, 0),
                'arrival_time' => now()->addDays(6)->setTime(15, 0),
                'duration_minutes' => 180,
            ],
            [
                'flight_number' => 'MS555',
                'carrier_id' => 1,
                'aircraft_id' => 1,
                'origin_id' => $cairo?->id ?? 1,
                'destination_id' => $jeddah?->id ?? 13,
                'departure_time' => now()->addDays(7)->setTime(13, 0),
                'arrival_time' => now()->addDays(7)->setTime(16, 0),
                'duration_minutes' => 180,
            ],
            [
                'flight_number' => 'MS666',
                'carrier_id' => 1,
                'aircraft_id' => 1,
                'origin_id' => $cairo?->id ?? 1,
                'destination_id' => $doha?->id ?? 14,
                'departure_time' => now()->addDays(8)->setTime(14, 0),
                'arrival_time' => now()->addDays(8)->setTime(17, 0),
                'duration_minutes' => 180,
            ],
            // More flights from Dubai
            [
                'flight_number' => 'EK789',
                'carrier_id' => 2,
                'aircraft_id' => 2,
                'origin_id' => $dubai?->id ?? 6,
                'destination_id' => $london?->id ?? 8,
                'departure_time' => now()->addDays(3)->setTime(9, 0),
                'arrival_time' => now()->addDays(3)->setTime(13, 0),
                'duration_minutes' => 240,
            ],
            [
                'flight_number' => 'EK101',
                'carrier_id' => 2,
                'aircraft_id' => 2,
                'origin_id' => $dubai?->id ?? 6,
                'destination_id' => $istanbul?->id ?? 10,
                'departure_time' => now()->addDays(4)->setTime(11, 0),
                'arrival_time' => now()->addDays(4)->setTime(13, 30),
                'duration_minutes' => 150,
            ],
            [
                'flight_number' => 'EK202',
                'carrier_id' => 2,
                'aircraft_id' => 2,
                'origin_id' => $dubai?->id ?? 6,
                'destination_id' => $riyadh?->id ?? 12,
                'departure_time' => now()->addDays(5)->setTime(10, 0),
                'arrival_time' => now()->addDays(5)->setTime(11, 30),
                'duration_minutes' => 90,
            ],
            // More flights from Paris
            [
                'flight_number' => 'AF555',
                'carrier_id' => 3,
                'aircraft_id' => 3,
                'origin_id' => $paris?->id ?? 7,
                'destination_id' => $rome?->id ?? 9,
                'departure_time' => now()->addDays(2)->setTime(8, 0),
                'arrival_time' => now()->addDays(2)->setTime(10, 0),
                'duration_minutes' => 120,
            ],
            [
                'flight_number' => 'AF666',
                'carrier_id' => 3,
                'aircraft_id' => 3,
                'origin_id' => $paris?->id ?? 7,
                'destination_id' => $barcelona?->id ?? 15,
                'departure_time' => now()->addDays(3)->setTime(9, 0),
                'arrival_time' => now()->addDays(3)->setTime(11, 0),
                'duration_minutes' => 120,
            ],
            [
                'flight_number' => 'AF777',
                'carrier_id' => 3,
                'aircraft_id' => 3,
                'origin_id' => $paris?->id ?? 7,
                'destination_id' => $amsterdam?->id ?? 16,
                'departure_time' => now()->addDays(4)->setTime(10, 0),
                'arrival_time' => now()->addDays(4)->setTime(11, 30),
                'duration_minutes' => 90,
            ],
            [
                'flight_number' => 'AF888',
                'carrier_id' => 3,
                'aircraft_id' => 3,
                'origin_id' => $paris?->id ?? 7,
                'destination_id' => $berlin?->id ?? 17,
                'departure_time' => now()->addDays(5)->setTime(11, 0),
                'arrival_time' => now()->addDays(5)->setTime(12, 30),
                'duration_minutes' => 90,
            ],
            [
                'flight_number' => 'AF999',
                'carrier_id' => 3,
                'aircraft_id' => 3,
                'origin_id' => $paris?->id ?? 7,
                'destination_id' => $madrid?->id ?? 18,
                'departure_time' => now()->addDays(6)->setTime(12, 0),
                'arrival_time' => now()->addDays(6)->setTime(14, 0),
                'duration_minutes' => 120,
            ],
        ];

        foreach ($flights as $flight) {
            Flight::create($flight);
        }
    }
}