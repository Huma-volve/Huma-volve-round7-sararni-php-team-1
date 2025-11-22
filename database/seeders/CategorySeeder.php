<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $categories = [
            [
                'title' => 'Tours',
                'slug' => 'tours',
                'image' => 'tours.jpg',
                'description' => 'Discover amazing tours around the world.',
            ],
            [
                'title' => 'Flights',
                'slug' => 'flights',
                'image' => 'flights.jpg',
                'description' => 'Book affordable and comfortable flights.',
            ],
            [
                'title' => 'Cars',
                'slug' => 'cars',
                'image' => 'cars.jpg',
                'description' => 'Rent cars from various brands and models.',
            ],
            [
                'title' => 'Hotels',
                'slug' => 'hotels',
                'image' => 'hotels.jpg',
                'description' => 'Find and book the best hotels.',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
