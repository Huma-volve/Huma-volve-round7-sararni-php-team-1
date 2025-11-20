<?php

namespace Database\Seeders;

use App\Models\Carrier;
use Illuminate\Database\Seeder;

class CarrierSeeder extends Seeder
{
    public function run(): void
    {
        $carriers = [
            ['carrier_name' => 'EgyptAir', 'code' => 'MS'],
            ['carrier_name' => 'Emirates', 'code' => 'EK'],
            ['carrier_name' => 'British Airways', 'code' => 'BA'],
            ['carrier_name' => 'Lufthansa', 'code' => 'LH']
        ];

        foreach ($carriers as $carrier) {
            Carrier::create($carrier);
        }
    }
}