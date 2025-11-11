<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $brands = [
            ['name' => 'Toyota'],
            ['name' => 'BMW'],
            ['name' => 'Mercedes'],
            ['name' => 'Kia'],
            ['name' => 'Hyundai'],
            ['name' => 'Nissan'],
        ];

        foreach ($brands as $data) {
            Brand::create($data);
        }
    }
}
