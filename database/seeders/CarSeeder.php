<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Brand;
use App\Models\Location;

class CarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $faker = \Faker\Factory::create();

        $brands = Brand::all()->pluck('id')->toArray();
        $locations = Location::all()->pluck('id')->toArray();
        $users = User::all()->pluck('id')->toArray();


        for ($i = 0; $i < 50; $i++) {
            DB::table('cars')->insert([
                'brand_id' => $faker->randomElement($brands),
                'model' => $faker->word(),
                'category' => $faker->randomElement(['SUV', 'Sedan', 'Economy', 'Luxury']),
                'make' => $faker->year(),
                'seats_count' => $faker->numberBetween(2, 7),
                'doors' => $faker->numberBetween(2, 5),
                'fuel_type' => $faker->randomElement(['Gasoline', 'Diesel', 'Electric', 'Hybrid']),
                'transmission' => $faker->randomElement(['Automatic', 'Manual']),
                'luggage_capacity' => $faker->numberBetween(0, 5),
                'air_conditioning' => $faker->boolean(90),
                'features' => json_encode($faker->randomElements(['GPS', 'Bluetooth', 'Sunroof', 'Heated Seats'], 2)),
                'pickup_location_id' => $faker->randomElement($locations),
                'dropoff_location_id' => $faker->randomElement($locations),
                'license_requirements' => $faker->sentence(),
                'availability_calendar' => json_encode([
                    '2025-11-11' => $faker->boolean(),
                    '2025-11-12' => $faker->boolean(),
                ]),
                'cancellation_policy' => $faker->paragraph(),
                'created_by' => $faker->randomElement($users),
                'updated_by' => $faker->randomElement($users),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
