<?php

namespace App\Models;

use App\Models\Brand;
use App\Models\CarPriceTier;
use App\Models\Location;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    
    protected $fillable = [
        'brand_id', 'model', 'category', 'make', 'seats_count', 'doors',
        'fuel_type', 'transmission', 'luggage_capacity', 'air_conditioning',
        'features', 'pickup_location_id', 'dropoff_location_id', 'license_requirements',
        'availability_calendar', 'cancellation_policy', 'created_by', 'updated_by'
    ];

    // protected $appends = ['image_url'];
    // public function getImageUrlAttribute()
    // {
    //     return $this->getFirstMediaUrl('car_images');
    // }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function pickupLocation()
    {
        return $this->belongsTo(Location::class,'pickup_location_id');
    }

    public function dropoffLocation()
    {
        return $this->belongsTo(Location::class,'dropoff_location_id');
    }

    public function priceTiers() {
            return $this->hasMany(CarPriceTier::class, 'car_id', 'id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('car_images')
            ->useDisk('public'); // اختياري: لو عايزة تخزني الصور في storage/app/public
    }

}
