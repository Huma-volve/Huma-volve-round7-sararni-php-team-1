<?php

namespace Database\Seeders;
 use App\Models\Room;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
            AircraftSeeder::class,
            CarSeeder::class,
            CarPriceTireSeeder::class,
            CarrierSeeder::class,
            FlightClasseSeeder::class,
            TourSeeder::class,
            RoomSeeder::class,
            AirportSeeder::class,
            AircraftSeeder::class,
            CarSeeder::class,
            CarPriceTireSeeder::class,
            ClassSeeder::class,
            CarrierSeeder::class,
            FlightLegSeeder::class,
            FlightClassSeeder::class,
            FlightSeatSeeder::class,
            TourSeeder::class,
            BookingSeeder::class,
            PaymentSeeder::class,
            ReviewSeeder::class,
            FavoriteSeeder::class,
            QuestionSeeder::class,

        ]);
    }
}
