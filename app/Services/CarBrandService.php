<?php

namespace App\Services;

use App\Models\Brand;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CarBrandService
{
    public function createBrand(array $data)
    {
        return DB::transaction(function () use ($data) {

            // Create Brand
            $brandData = $data;
            unset($brandData['image']);

            $brand = Brand::create($brandData);

            // Upload Single Image
            if (isset($data['image'])) {
                try {
                    $timestamp = now()->format('YmdHis'); 
                    $originalName = pathinfo($data['image']->getClientOriginalName(), PATHINFO_FILENAME);
                    $extension = $data['image']->getClientOriginalExtension();

                    $brand->addMedia($data['image'])
                        ->usingFileName($originalName . '_' . $timestamp . '.' . $extension)
                        ->toMediaCollection('brand_images');

                } catch (\Throwable $th) {
                    Log::error('Error uploading brand image', ['message' => $th->getMessage()]);
                }
            }

            return $brand;
        });
    }
}
