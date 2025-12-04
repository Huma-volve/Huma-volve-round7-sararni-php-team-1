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
                'location_lat' => 30.123456,
                'location_lng' => 50.789012,
                'stars' => rand(3, 5),
                'rooms_count' => rand(50, 200),
                'recommended' => json_encode(['families', 'business', 'couples']),
                'description' => "This is a sample description for Hotel $i.",
            ]);
    }

        $faker = \Faker\Factory::create();
        $locations = \App\Models\Location::all()->pluck('id')->toArray();

        $hotels = [
            // Egypt Hotels
            ['name' => 'Cairo Marriott Hotel', 'slug' => 'cairo-marriott-hotel', 'stars' => 5, 'rooms_count' => 200, 'city' => 'Cairo'],
            ['name' => 'Nile View Hotel', 'slug' => 'nile-view-hotel', 'stars' => 4, 'rooms_count' => 120, 'city' => 'Cairo'],
            ['name' => 'Pyramids View Hotel', 'slug' => 'pyramids-view-hotel', 'stars' => 4, 'rooms_count' => 100, 'city' => 'Giza'],
            ['name' => 'Cairo Downtown Hotel', 'slug' => 'cairo-downtown-hotel', 'stars' => 3, 'rooms_count' => 80, 'city' => 'Cairo'],
            ['name' => 'Alexandria Sea View Hotel', 'slug' => 'alexandria-sea-view-hotel', 'stars' => 3, 'rooms_count' => 90, 'city' => 'Alexandria'],
            ['name' => 'Alexandria Corniche Hotel', 'slug' => 'alexandria-corniche-hotel', 'stars' => 4, 'rooms_count' => 110, 'city' => 'Alexandria'],
            ['name' => 'Sharm El Sheikh Resort', 'slug' => 'sharm-el-sheikh-resort', 'stars' => 4, 'rooms_count' => 180, 'city' => 'Sharm El-Sheikh'],
            ['name' => 'Sharm Beach Resort', 'slug' => 'sharm-beach-resort', 'stars' => 5, 'rooms_count' => 250, 'city' => 'Sharm El-Sheikh'],
            ['name' => 'Hurghada Beach Hotel', 'slug' => 'hurghada-beach-hotel', 'stars' => 4, 'rooms_count' => 150, 'city' => 'Hurghada'],
            ['name' => 'Hurghada Resort', 'slug' => 'hurghada-resort', 'stars' => 5, 'rooms_count' => 200, 'city' => 'Hurghada'],
            ['name' => 'Luxor Nile Hotel', 'slug' => 'luxor-nile-hotel', 'stars' => 4, 'rooms_count' => 120, 'city' => 'Luxor'],
            ['name' => 'Aswan Cataract Hotel', 'slug' => 'aswan-cataract-hotel', 'stars' => 4, 'rooms_count' => 100, 'city' => 'Aswan'],
            // International Hotels
            ['name' => 'Grand Hotel Paris', 'slug' => 'grand-hotel-paris', 'stars' => 5, 'rooms_count' => 150, 'city' => 'Paris'],
            ['name' => 'Luxury Paris Hotel', 'slug' => 'luxury-paris-hotel', 'stars' => 4, 'rooms_count' => 100, 'city' => 'Paris'],
            ['name' => 'Eiffel Tower View Hotel', 'slug' => 'eiffel-tower-view-hotel', 'stars' => 4, 'rooms_count' => 80, 'city' => 'Paris'],
            ['name' => 'Paris Champs Hotel', 'slug' => 'paris-champs-hotel', 'stars' => 5, 'rooms_count' => 180, 'city' => 'Paris'],
            ['name' => 'Dubai Marina Hotel', 'slug' => 'dubai-marina-hotel', 'stars' => 5, 'rooms_count' => 300, 'city' => 'Dubai'],
            ['name' => 'Burj Khalifa Hotel', 'slug' => 'burj-khalifa-hotel', 'stars' => 5, 'rooms_count' => 250, 'city' => 'Dubai'],
            ['name' => 'Dubai Palm Hotel', 'slug' => 'dubai-palm-hotel', 'stars' => 5, 'rooms_count' => 200, 'city' => 'Dubai'],
            ['name' => 'London Tower Hotel', 'slug' => 'london-tower-hotel', 'stars' => 4, 'rooms_count' => 150, 'city' => 'London'],
            ['name' => 'London Thames Hotel', 'slug' => 'london-thames-hotel', 'stars' => 5, 'rooms_count' => 180, 'city' => 'London'],
            ['name' => 'Rome Colosseum Hotel', 'slug' => 'rome-colosseum-hotel', 'stars' => 4, 'rooms_count' => 120, 'city' => 'Rome'],
            ['name' => 'Rome Vatican Hotel', 'slug' => 'rome-vatican-hotel', 'stars' => 5, 'rooms_count' => 160, 'city' => 'Rome'],
            ['name' => 'Barcelona Beach Hotel', 'slug' => 'barcelona-beach-hotel', 'stars' => 4, 'rooms_count' => 140, 'city' => 'Barcelona'],
            ['name' => 'Istanbul Bosphorus Hotel', 'slug' => 'istanbul-bosphorus-hotel', 'stars' => 5, 'rooms_count' => 200, 'city' => 'Istanbul'],
            ['name' => 'Athens Acropolis Hotel', 'slug' => 'athens-acropolis-hotel', 'stars' => 4, 'rooms_count' => 130, 'city' => 'Athens'],
            ['name' => 'Amsterdam Canal Hotel', 'slug' => 'amsterdam-canal-hotel', 'stars' => 4, 'rooms_count' => 100, 'city' => 'Amsterdam'],
            ['name' => 'Venice Grand Hotel', 'slug' => 'venice-grand-hotel', 'stars' => 5, 'rooms_count' => 120, 'city' => 'Venice'],
            ['name' => 'Berlin Central Hotel', 'slug' => 'berlin-central-hotel', 'stars' => 4, 'rooms_count' => 150, 'city' => 'Berlin'],
            ['name' => 'Madrid Plaza Hotel', 'slug' => 'madrid-plaza-hotel', 'stars' => 5, 'rooms_count' => 180, 'city' => 'Madrid'],
            ['name' => 'Vienna Opera Hotel', 'slug' => 'vienna-opera-hotel', 'stars' => 5, 'rooms_count' => 160, 'city' => 'Vienna'],
            ['name' => 'Prague Castle Hotel', 'slug' => 'prague-castle-hotel', 'stars' => 4, 'rooms_count' => 140, 'city' => 'Prague'],
        ];

        foreach ($hotels as $hotelData) {
            $location = \App\Models\Location::where('city', $hotelData['city'])->first();

            \App\Models\Hotel::firstOrCreate(
                ['slug' => $hotelData['slug']],
                [
                    'name' => $hotelData['name'],
                    'location_id' => $location?->id ?? $faker->randomElement($locations),
                    'stars' => $hotelData['stars'],
                    'rooms_count' => $hotelData['rooms_count'],
                    'description' => $faker->paragraph(3),
                    'recommended' => json_encode($faker->randomElements(['Business travelers', 'Couples', 'Families', 'Solo travelers'], 2)),
                ]
            );
        }
    }
}
