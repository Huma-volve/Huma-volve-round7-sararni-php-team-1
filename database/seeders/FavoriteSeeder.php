<?php

namespace Database\Seeders;

use App\Models\Favorite;
use App\Models\Tour;
use App\Models\Hotel;
use App\Models\Car;
use App\Models\Flight;
use App\Models\User;
use Illuminate\Database\Seeder;

class FavoriteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customer = User::where('email', 'customer@test.com')->first();

        if (! $customer) {
            return;
        }

        // Favorite Tours
        $tours = Tour::where('status', 'active')->get();
        if ($tours->isNotEmpty()) {
            $favoriteTours = $tours->random(min(rand(3, 5), $tours->count()));
            foreach ($favoriteTours as $tour) {
                Favorite::firstOrCreate([
                    'user_id' => $customer->id,
                    'category' => 'tour',
                    'item_id' => $tour->id,
                ]);
            }
        }

        // Favorite Hotels
        $hotels = Hotel::all();
        if ($hotels->isNotEmpty()) {
            $favoriteHotels = $hotels->random(min(rand(2, 3), $hotels->count()));
            foreach ($favoriteHotels as $hotel) {
                Favorite::firstOrCreate([
                    'user_id' => $customer->id,
                    'category' => 'hotel',
                    'item_id' => $hotel->id,
                ]);
            }
        }

        // Favorite Cars
        $cars = Car::all();
        if ($cars->isNotEmpty()) {
            $favoriteCars = $cars->random(min(rand(2, 3), $cars->count()));
            foreach ($favoriteCars as $car) {
                Favorite::firstOrCreate([
                    'user_id' => $customer->id,
                    'category' => 'car',
                    'item_id' => $car->id,
                ]);
            }
        }

        // Favorite Flights
        $flights = Flight::all();
        if ($flights->isNotEmpty()) {
            $favoriteFlights = $flights->random(min(rand(2, 3), $flights->count()));
            foreach ($favoriteFlights as $flight) {
                Favorite::firstOrCreate([
                    'user_id' => $customer->id,
                    'category' => 'flight',
                    'item_id' => $flight->id,
                ]);
            }
        }
    }
}
