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

            // Egypt
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

            // International
            ['city' => 'Paris', 'country' => 'France', 'latitude' => 48.8566, 'longitude' => 2.3522],
            ['city' => 'Dubai', 'country' => 'UAE', 'latitude' => 25.2048, 'longitude' => 55.2708],
            ['city' => 'London', 'country' => 'UK', 'latitude' => 51.5074, 'longitude' => -0.1278],
            ['city' => 'New York', 'country' => 'USA', 'latitude' => 40.7128, 'longitude' => -74.0060],
            ['city' => 'Rome', 'country' => 'Italy', 'latitude' => 41.9028, 'longitude' => 12.4964],
            ['city' => 'Barcelona', 'country' => 'Spain', 'latitude' => 41.3851, 'longitude' => 2.1734],
            ['city' => 'Istanbul', 'country' => 'Turkey', 'latitude' => 41.0082, 'longitude' => 28.9784],
            ['city' => 'Athens', 'country' => 'Greece', 'latitude' => 37.9838, 'longitude' => 23.7275],
            ['city' => 'Amsterdam', 'country' => 'Netherlands', 'latitude' => 52.3676, 'longitude' => 4.9041],
            ['city' => 'Berlin', 'country' => 'Germany', 'latitude' => 52.5200, 'longitude' => 13.4050],
            ['city' => 'Madrid', 'country' => 'Spain', 'latitude' => 40.4168, 'longitude' => -3.7038],
            ['city' => 'Vienna', 'country' => 'Austria', 'latitude' => 48.2082, 'longitude' => 16.3738],
            ['city' => 'Prague', 'country' => 'Czech Republic', 'latitude' => 50.0755, 'longitude' => 14.4378],
            ['city' => 'Budapest', 'country' => 'Hungary', 'latitude' => 47.4979, 'longitude' => 19.0402],
            ['city' => 'Warsaw', 'country' => 'Poland', 'latitude' => 52.2297, 'longitude' => 21.0122],
            ['city' => 'Stockholm', 'country' => 'Sweden', 'latitude' => 59.3293, 'longitude' => 18.0686],
            ['city' => 'Copenhagen', 'country' => 'Denmark', 'latitude' => 55.6761, 'longitude' => 12.5683],
            ['city' => 'Oslo', 'country' => 'Norway', 'latitude' => 59.9139, 'longitude' => 10.7522],
            ['city' => 'Helsinki', 'country' => 'Finland', 'latitude' => 60.1699, 'longitude' => 24.9384],
            ['city' => 'Dublin', 'country' => 'Ireland', 'latitude' => 53.3498, 'longitude' => -6.2603],
            ['city' => 'Edinburgh', 'country' => 'UK', 'latitude' => 55.9533, 'longitude' => -3.1883],
            ['city' => 'Lisbon', 'country' => 'Portugal', 'latitude' => 38.7223, 'longitude' => -9.1393],
            ['city' => 'Brussels', 'country' => 'Belgium', 'latitude' => 50.8503, 'longitude' => 4.3517],
            ['city' => 'Zurich', 'country' => 'Switzerland', 'latitude' => 47.3769, 'longitude' => 8.5417],
            ['city' => 'Geneva', 'country' => 'Switzerland', 'latitude' => 46.2044, 'longitude' => 6.1432],
            ['city' => 'Milan', 'country' => 'Italy', 'latitude' => 45.4642, 'longitude' => 9.1900],
            ['city' => 'Venice', 'country' => 'Italy', 'latitude' => 45.4408, 'longitude' => 12.3155],
            ['city' => 'Florence', 'country' => 'Italy', 'latitude' => 43.7696, 'longitude' => 11.2558],
            ['city' => 'Naples', 'country' => 'Italy', 'latitude' => 40.8518, 'longitude' => 14.2681],
            ['city' => 'Santorini', 'country' => 'Greece', 'latitude' => 36.3932, 'longitude' => 25.4615],
            ['city' => 'Mykonos', 'country' => 'Greece', 'latitude' => 37.4467, 'longitude' => 25.3289],
            ['city' => 'Moscow', 'country' => 'Russia', 'latitude' => 55.7558, 'longitude' => 37.6173],
            ['city' => 'Tokyo', 'country' => 'Japan', 'latitude' => 35.6762, 'longitude' => 139.6503],
            ['city' => 'Bangkok', 'country' => 'Thailand', 'latitude' => 13.7563, 'longitude' => 100.5018],
            ['city' => 'Singapore', 'country' => 'Singapore', 'latitude' => 1.3521, 'longitude' => 103.8198],
            ['city' => 'Hong Kong', 'country' => 'China', 'latitude' => 22.3193, 'longitude' => 114.1694],
            ['city' => 'Sydney', 'country' => 'Australia', 'latitude' => -33.8688, 'longitude' => 151.2093],
            ['city' => 'Melbourne', 'country' => 'Australia', 'latitude' => -37.8136, 'longitude' => 144.9631],
            ['city' => 'Los Angeles', 'country' => 'USA', 'latitude' => 34.0522, 'longitude' => -118.2437],
            ['city' => 'San Francisco', 'country' => 'USA', 'latitude' => 37.7749, 'longitude' => -122.4194],
            ['city' => 'Miami', 'country' => 'USA', 'latitude' => 25.7617, 'longitude' => -80.1918],
            ['city' => 'Toronto', 'country' => 'Canada', 'latitude' => 43.6532, 'longitude' => -79.3832],
            ['city' => 'Vancouver', 'country' => 'Canada', 'latitude' => 49.2827, 'longitude' => -123.1207],
            ['city' => 'Mexico City', 'country' => 'Mexico', 'latitude' => 19.4326, 'longitude' => -99.1332],
            ['city' => 'Rio de Janeiro', 'country' => 'Brazil', 'latitude' => -22.9068, 'longitude' => -43.1729],
            ['city' => 'Buenos Aires', 'country' => 'Argentina', 'latitude' => -34.6037, 'longitude' => -58.3816],
            ['city' => 'Cape Town', 'country' => 'South Africa', 'latitude' => -33.9249, 'longitude' => 18.4241],
            ['city' => 'Marrakech', 'country' => 'Morocco', 'latitude' => 31.6295, 'longitude' => -7.9811],
            ['city' => 'Casablanca', 'country' => 'Morocco', 'latitude' => 33.5731, 'longitude' => -7.5898],
            ['city' => 'Tunis', 'country' => 'Tunisia', 'latitude' => 36.8065, 'longitude' => 10.1815],
            ['city' => 'Algiers', 'country' => 'Algeria', 'latitude' => 36.7538, 'longitude' => 3.0588],
            ['city' => 'Beirut', 'country' => 'Lebanon', 'latitude' => 33.8938, 'longitude' => 35.5018],
            ['city' => 'Amman', 'country' => 'Jordan', 'latitude' => 31.9539, 'longitude' => 35.9106],
            ['city' => 'Jerusalem', 'country' => 'Israel', 'latitude' => 31.7683, 'longitude' => 35.2137],
            ['city' => 'Riyadh', 'country' => 'Saudi Arabia', 'latitude' => 24.7136, 'longitude' => 46.6753],
            ['city' => 'Jeddah', 'country' => 'Saudi Arabia', 'latitude' => 21.4858, 'longitude' => 39.1925],
            ['city' => 'Doha', 'country' => 'Qatar', 'latitude' => 25.2854, 'longitude' => 51.5310],
            ['city' => 'Abu Dhabi', 'country' => 'UAE', 'latitude' => 24.4539, 'longitude' => 54.3773],
            ['city' => 'Kuwait City', 'country' => 'Kuwait', 'latitude' => 29.3759, 'longitude' => 47.9774],
            ['city' => 'Manama', 'country' => 'Bahrain', 'latitude' => 26.0667, 'longitude' => 50.5577],
            ['city' => 'Muscat', 'country' => 'Oman', 'latitude' => 23.5859, 'longitude' => 58.4059],
        ];

        foreach ($locations as $location) {
            Location::create($location);
        }
    }
}
