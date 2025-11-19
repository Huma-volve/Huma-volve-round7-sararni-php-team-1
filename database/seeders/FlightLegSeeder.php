<?php

namespace Database\Seeders;

use App\Models\Flight;
use App\Models\FlightLeg;
use App\Models\Airport;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class FlightLegSeeder extends Seeder
{
    public function run(): void
    {
        
        $airports = Airport::all();
        $flights = Flight::all();

        if ($airports->count() < 2) {
            $this->command->warn('there must be two airports');
            return;
        }

        $flightLegs = [];

        foreach ($flights as $flight) {
           
            $availableAirports = $airports->shuffle();
            
            
            if ($availableAirports->count() < 2) {
                continue;
            }

            $origin = $availableAirports->pop();
            $destination = $availableAirports->pop();

            
            $flightLegs[] = [
                'flight_id' => $flight->id,
                'leg_number' => 1,
                'origin_airport_id' => $origin->id,
                'destination_airport_id' => $destination->id,
                'departure_time' => Carbon::now()->addDays(rand(1, 30))->setTime(rand(6, 20), 0),
                'arrival_time' => Carbon::now()->addDays(rand(1, 30))->setTime(rand(8, 22), 0),
                'duration_minutes' => rand(60, 360),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            
            if ($flight->id % 3 == 0 && $availableAirports->count() >= 1) {
                $nextDestination = $availableAirports->pop();
                
                $flightLegs[] = [
                    'flight_id' => $flight->id,
                    'leg_number' => 2,
                    'origin_airport_id' => $destination->id,
                    'destination_airport_id' => $nextDestination->id,
                    'departure_time' => Carbon::now()->addDays(rand(1, 30))->addHours(2)->setTime(rand(10, 22), 0),
                    'arrival_time' => Carbon::now()->addDays(rand(1, 30))->addHours(4)->setTime(rand(12, 24), 0),
                    'duration_minutes' => rand(60, 240),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        
        foreach (array_chunk($flightLegs, 50) as $chunk) {
            FlightLeg::insert($chunk);
        }
    }
}