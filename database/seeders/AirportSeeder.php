<?php

namespace Database\Seeders;

use App\Models\Airport;
use Illuminate\Database\Seeder;

class AirportSeeder extends Seeder
{
    public function run(): void
    {
        $airports = [
            [
                'airport_code' => 'CAI',
                'airport_name' => 'Cairo International Airport',
                'city' => 'Cairo',
                'country' => 'Egypt',
                'latitude' => 30.121900,
                'longitude' => 31.405600
            ],
            [
                'airport_code' => 'JFK',
                'airport_name' => 'John F. Kennedy International Airport',
                'city' => 'New York',
                'country' => 'USA',
                'latitude' => 40.641300,
                'longitude' => -73.778100
            ],
            [
                'airport_code' => 'LHR',
                'airport_name' => 'Heathrow Airport',
                'city' => 'London',
                'country' => 'UK',
                'latitude' => 51.470600,
                'longitude' => -0.461941
            ]
        ];

        foreach ($airports as $airport) {
            Airport::create($airport);
        }
    }
}