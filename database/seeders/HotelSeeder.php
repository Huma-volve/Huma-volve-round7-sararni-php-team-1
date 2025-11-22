<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Hotel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class HotelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      $category = Category::where('slug', 'hotels')->first();

         for ($i = 1; $i <= 5; $i++) {
            Hotel::create([
                 'category_id' => $category->id,
                'name' => "Hotel $i",
                'slug' => Str::slug("Hotel $i"),
                'amenities' => 'WiFi, Parking, Pool, Gym',
                'contact_info' => 'info@hotel' . $i . '.com',
                'policies' => json_encode([
                    'check_in' => '2:00 PM',
                    'check_out' => '12:00 PM',
                    'cancellation' => 'Free cancellation within 24 hours'
                ]),
                'location_id' => $i, // لازم تتأكد إن عندك location_id = 1 في جدول locations
                'stars' => rand(3, 5),
                'rooms_count' => rand(50, 200),
                'recommended' => json_encode(['families', 'business', 'couples']),
                'description' => "This is a sample description for Hotel $i.",
            ]);
    }
    }
}
