<?php

namespace Database\Seeders;

use App\Models\RatePlan;
use App\Models\Room;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomSeeder extends Seeder
{
   public function run(): void
    {
        // ---------- ROOM 1 ----------
        $room1 = Room::create([
            'hotel_id'   => 1, // عدلها حسب الفندق الموجود عندك
            'name'       => 'Deluxe Twin Room',
            'code'       => 'DLX-TWIN',
            'price_per_night' =>2000.00 ,
            'description'=> 'A spacious deluxe twin room with modern amenities.',
            'occupancy'  => json_encode(['adults' => 2, 'children' => 1, 'infants' => 1]),
            'area'       => 32.5,
            'room_type'  => 'Deluxe',
            'extras'     => json_encode(['wifi' => true, 'tv' => true, 'mini_bar' => true]),
        ]);

        // Standard Plan
        RatePlan::create([
            'room_id'        => $room1->id,
            'name'           => 'Standard Rate',
            'base_price'     => 1200.00,
            'currency'       => 'EGP',
            'refundable'     => true,
            'cancellation_policy' => 'Free cancellation up to 24 hours before check-in.',

            'extras' => json_encode([
                'breakfast' => false,
                'parking' => true
            ])
        ]);

        // Bed & Breakfast
        RatePlan::create([
            'room_id'    => $room1->id,
            'name'       => 'Bed & Breakfast',
            'base_price' => 1400.00,
            'currency'   => 'EGP',
            'refundable' => true,
            'cancellation_policy' => 'Free cancellation up to 48 hours before check-in.',

            'extras' => json_encode([
                'breakfast' => true,
                'parking'   => true
            ])
        ]);


        // ---------- ROOM 2 ----------
        $room2 = Room::create([
            'hotel_id'   => 1,
            'name'       => 'King Suite',
            'code'       => 'KING-SUITE',
            'price_per_night' =>2500.00 ,
            'description'=> 'Luxury king suite with balcony and sea view.',
            'occupancy'  => json_encode(['adults' => 3, 'children' => 2, 'infants' => 1]),
            'area'       => 55.0,
            'room_type'  => 'Suite',
            'extras'     => json_encode(['jacuzzi' => true, 'wifi' => true, 'sofa_bed' => true]),
        ]);

        // Non-refundable Plan
        RatePlan::create([
            'room_id'    => $room2->id,
            'name'       => 'Non-Refundable Offer',
            'base_price' => 1800.00,
            'currency'   => 'EGP',
            'refundable' => false,
            'cancellation_policy' => 'No refund on cancellation.',

            'extras' => json_encode([
                'breakfast' => true,
                'spa'       => false
            ])
        ]);

        // Family Package
        RatePlan::create([
            'room_id'    => $room2->id,
            'name'       => 'Family Package',
            'base_price' => 2100.00,
            'currency'   => 'EGP',
            'refundable' => true,
            'cancellation_policy' => 'Free cancellation before 72 hours.',

            'extras' => json_encode([
                'breakfast' => true,
                'dinner'    => true,
                'parking'   => true
            ])
        ]);
    }
}
