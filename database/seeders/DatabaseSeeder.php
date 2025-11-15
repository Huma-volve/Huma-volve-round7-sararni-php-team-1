<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            TestUsersSeeder::class,
            LocationSeeder::class,
            CategorySeeder::class,
            BrandSeeder::class,
            AircraftSeeder::class,
            CarSeeder::class,
            CarPriceTireSeeder::class,
            CarrierSeeder::class,
            FlightClasseSeeder::class,
            FlightSeeder::class,
            HotelSeeder::class,
            TourSeeder::class,
            BookingSeeder::class,
            PaymentSeeder::class,
            ReviewSeeder::class,
            FavoriteSeeder::class,
            QuestionSeeder::class,
        ]);
    }
}
