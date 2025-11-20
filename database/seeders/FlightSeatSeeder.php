<?php

namespace Database\Seeders;

use App\Models\Flight;
use App\Models\FlightSeat;
use App\Models\ClassModel;
use Illuminate\Database\Seeder;

class FlightSeatSeeder extends Seeder
{
    public function run(): void
    {
        $flights = Flight::with('aircraft')->take(3)->get();
        $classes = ClassModel::all();

        $seatMaps = [
            'Boeing 737-800' => [
                'economy' => $this->generateSeatMap(5, 6, ['A', 'B', 'C', 'D', 'E', 'F']),
                'business' => $this->generateSeatMap(3, 4, ['A', 'B', 'C', 'D']),
                'first' => $this->generateSeatMap(2, 4, ['A', 'B', 'C', 'D'])
            ],
            'Airbus A320' => [
                'economy' => $this->generateSeatMap(4, 6, ['A', 'B', 'C', 'D', 'E', 'F']),
                'business' => $this->generateSeatMap(2, 4, ['A', 'B', 'C', 'D']),
                'first' => $this->generateSeatMap(1, 4, ['A', 'B', 'C', 'D'])
            ],
            'Boeing 777-300ER' => [
                'economy' => $this->generateSeatMap(8, 9, ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I']),
                'business' => $this->generateSeatMap(4, 6, ['A', 'B', 'C', 'D', 'E', 'F']),
                'first' => $this->generateSeatMap(2, 4, ['A', 'B', 'C', 'D'])
            ]
        ];

        $createdCount = 0;
        $updatedCount = 0;

        foreach ($flights as $flight) {
            $aircraftModel = $flight->aircraft->model;
            
            if (!isset($seatMaps[$aircraftModel])) {
                $this->command->info("No seat map for aircraft: $aircraftModel");
                continue;
            }

            $aircraftSeatMap = $seatMaps[$aircraftModel];

            foreach ($classes as $class) {
                $className = $class->class_name;
                
                if (!isset($aircraftSeatMap[$className])) {
                    continue;
                }

                $seats = $aircraftSeatMap[$className];
                $classPrices = [
                    'economy' => rand(1000, 2000),
                    'business' => rand(3000, 6000),
                    'first' => rand(8000, 15000)
                ];

                foreach ($seats as $seatNumber) {
                    $result = FlightSeat::updateOrCreate(
                        [
                            'flight_id' => $flight->id,
                            'seat_number' => $seatNumber
                        ],
                        [
                            'class_id' => $class->id,
                            'status' => 'available',
                            'price' => $classPrices[$className],
                            'updated_at' => now()
                        ]
                    );

                    if ($result->wasRecentlyCreated) {
                        $createdCount++;
                    } else {
                        $updatedCount++;
                    }
                }
            }
        }

        $this->command->info("Flight seats completed! Created: $createdCount, Updated: $updatedCount");
    }

    private function generateSeatMap(int $rows, int $seatsPerRow, array $columns): array
    {
        $seats = [];
        
        for ($row = 1; $row <= $rows; $row++) {
            foreach ($columns as $column) {
                $seats[] = $column . $row;
            }
        }
        
        return $seats;
    }
}