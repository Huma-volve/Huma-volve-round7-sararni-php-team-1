<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\FlightSeatSeeder;

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
            AirportSeeder::class,
            AircraftSeeder::class,
            CarSeeder::class,


            CarPriceTireSeeder::class,
            ClassSeeder::class,
            CarrierSeeder::class,
            FlightSeeder::class,
            FlightLegSeeder::class,
            FlightClassSeeder::class,
            FlightSeatSeeder::class,

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
