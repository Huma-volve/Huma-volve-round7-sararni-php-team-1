<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use \Faker\Factory as Faker;

class AircraftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        for ($i = 0; $i < 10; $i++) {
            $rows = $faker->numberBetween(20, 40);
            $seatsPerRow = $faker->numberBetween(4, 8);

            $seatMap = [];
            for ($r = 1; $r <= $rows; $r++) {
                $rowSeats = [];
                for ($s = 1; $s <= $seatsPerRow; $s++) {
                    $rowSeats[] = ['seat_number' => $r . chr(64 + $s), 'available' => true];
                }
                $seatMap[] = ['row' => $r, 'seats' => $rowSeats];
            }

            DB::table('aircrafts')->insert([
                'model' => $faker->word() . '-' . $faker->numberBetween(100, 999),
                'seat_map' => json_encode($seatMap),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
