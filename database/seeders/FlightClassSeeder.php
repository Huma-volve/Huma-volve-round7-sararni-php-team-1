<?php

namespace Database\Seeders;

use App\Models\FlightClass;
use Illuminate\Database\Seeder;

class FlightClassSeeder extends Seeder
{
    public function run(): void
    {
        $flightClasses = [
          
            [
                'flight_id' => 1,
                'class_id' => 1, 
                'price_per_seat' => 2500,
                'seats_available' => 120,
                'price_rules' => ['child_discount' => 10],
                'baggage_rules' => ['kg' => 23],
                'fare_conditions' => ['change_fee' => 500],
                'taxes_fees_breakdown' => ['airport_fee' => 100],
                'refundable' => false
            ],
           
            [
                'flight_id' => 1,
                'class_id' => 2, 
                'price_per_seat' => 5500,
                'seats_available' => 40,
                'price_rules' => ['child_discount' => 15],
                'baggage_rules' => ['kg' => 32],
                'fare_conditions' => ['change_fee' => 200],
                'taxes_fees_breakdown' => ['airport_fee' => 150],
                'refundable' => true
            ],
            
            [
                'flight_id' => 1,
                'class_id' => 3, 
                'price_per_seat' => 12000,
                'seats_available' => 20,
                'price_rules' => ['child_discount' => 20],
                'baggage_rules' => ['kg' => 40],
                'fare_conditions' => ['change_fee' => 0],
                'taxes_fees_breakdown' => ['airport_fee' => 200],
                'refundable' => true
            ],
           
            [
                'flight_id' => 2,
                'class_id' => 1,
                'price_per_seat' => 1800,
                'seats_available' => 150,
                'price_rules' => ['child_discount' => 8],
                'baggage_rules' => ['kg' => 20],
                'fare_conditions' => ['change_fee' => 300],
                'taxes_fees_breakdown' => ['airport_fee' => 80],
                'refundable' => false
            ]
        ];

        foreach ($flightClasses as $flightClass) {
            FlightClass::create($flightClass);
        }
    }
}