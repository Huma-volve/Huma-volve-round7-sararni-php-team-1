<?php

namespace Database\Seeders;


use App\Models\Aircraft;
use Illuminate\Database\Seeder;

class AircraftSeeder extends Seeder
{
    public function run(): void
    {
        $aircrafts = [
            ['model' => 'Boeing 737-800', 'total_seats' => 189],
            ['model' => 'Airbus A320', 'total_seats' => 180],
            ['model' => 'Boeing 777-300ER', 'total_seats' => 396],
            ['model' => 'Airbus A350', 'total_seats' => 350]
        ];

        foreach ($aircrafts as $aircraft) {
            Aircraft::create($aircraft);
        }
    }
}
 