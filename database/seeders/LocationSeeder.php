<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
         $locations = [
            ['city' => 'Cairo', 'country' => 'Egypt', 'latitude' => 30.0444, 'longitude' => 31.2357],
            ['city' => 'Giza', 'country' => 'Egypt', 'latitude' => 29.9765, 'longitude' => 31.1313],
            ['city' => 'Alexandria', 'country' => 'Egypt', 'latitude' => 31.2001, 'longitude' => 29.9187],
            ['city' => 'Sharm El-Sheikh', 'country' => 'Egypt', 'latitude' => 27.9158, 'longitude' => 34.3299],
            ['city' => 'Hurghada', 'country' => 'Egypt', 'latitude' => 27.2579, 'longitude' => 33.8116],
            ['city' => 'Luxor', 'country' => 'Egypt', 'latitude' => 25.6872, 'longitude' => 32.6396],
            ['city' => 'Aswan', 'country' => 'Egypt', 'latitude' => 24.0889, 'longitude' => 32.8998],
            ['city' => 'Marsa Alam', 'country' => 'Egypt', 'latitude' => 25.0673, 'longitude' => 34.8916],
            ['city' => 'Dahab', 'country' => 'Egypt', 'latitude' => 28.5006, 'longitude' => 34.5136],
            ['city' => 'Siwa Oasis', 'country' => 'Egypt', 'latitude' => 29.2041, 'longitude' => 25.5197],
            ['city' => 'Fayoum', 'country' => 'Egypt', 'latitude' => 29.3084, 'longitude' => 30.8428],
            ['city' => 'Tanta', 'country' => 'Egypt', 'latitude' => 30.7885, 'longitude' => 31.0004],
            ['city' => 'Ismailia', 'country' => 'Egypt', 'latitude' => 30.6043, 'longitude' => 32.2723],
            ['city' => 'Port Said', 'country' => 'Egypt', 'latitude' => 31.2653, 'longitude' => 32.3019],
            ['city' => 'Suez', 'country' => 'Egypt', 'latitude' => 29.9668, 'longitude' => 32.5498],
        ];

        foreach ($locations as $location) {
            Location::create($location);
        }
    }
}
