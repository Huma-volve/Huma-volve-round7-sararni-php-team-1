<?php

namespace Database\Seeders;

use App\Models\ClassModel;
use Illuminate\Database\Seeder;

class ClassSeeder extends Seeder
{
    public function run(): void
    {
        $classes = [
            ['class_name' => 'economy'],
            ['class_name' => 'business'],
            ['class_name' => 'first']
        ];

        foreach ($classes as $class) {
            ClassModel::create($class);
        }
    }
}