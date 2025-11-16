<?php

namespace Database\Seeders;

use App\Models\Favorite;
use App\Models\Tour;
use App\Models\User;
use Illuminate\Database\Seeder;

class FavoriteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tours = Tour::all();
        $customer = User::where('email', 'customer@test.com')->first();

        if ($tours->isEmpty() || ! $customer) {
            $this->command->warn('No tours found or customer user not found. Please run TourSeeder and TestUsersSeeder first.');

            return;
        }

        // Add 3-5 favorite tours for customer user
        $favoriteTours = $tours->random(min(rand(3, 5), $tours->count()));

        foreach ($favoriteTours as $tour) {
            Favorite::firstOrCreate(
                [
                    'user_id' => $customer->id,
                    'tour_id' => $tour->id,
                ]
            );
        }

        $this->command->info('Favorites seeded successfully!');
    }
}
