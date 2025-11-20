<?php

namespace App\Services;

use App\Models\Car;
use App\Models\CarPriceTier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CarService
{
    

    public function createCar(array $data)
    {
        return DB::transaction(function () use ($data) {

            // Create Car
            $carData = $data;
            unset($carData['images'], $carData['price_tiers']);
            $car = Car::create($carData);

            // Upload Images
             if (isset($data['images']) && is_array($data['images'])) {
            foreach ($data['images'] as $image) {
                try {
                    $car->addMedia($image)->toMediaCollection('car_images');
                } catch (\Throwable $th) {
                    Log::error('Error uploading car image', ['message' => $th->getMessage()]);
                }
            }
        }
        
        // Price Tiers
        if (isset($data['price_tiers']) && is_array($data['price_tiers'])) {
            
            foreach ($data['price_tiers'] as $tier) {
                try {
                    $car->priceTiers()->create([
                        'car_id' => $car->id,
                        'from_hours' => $tier['from_hours'],
                        'to_hours' => $tier['to_hours'],
                        'price_per_hour' => $tier['price_per_hour'],
                        'price_per_day' => $tier['price_per_day'],
                    ]);
                } catch (\Throwable $th) {
                    Log::error('Error creating car price tier', ['message' => $th->getMessage()]);
                }
            }
        }

        return $car;
        });
    }
}
