<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Car extends Model
{
    //
    protected $fillable = [
        'brand_id', 'model', 'category', 'make', 'seats_count', 'doors',
        'fuel_type', 'transmission', 'luggage_capacity', 'air_conditioning',
        'features', 'pickup_location_id', 'dropoff_location_id', 'license_requirements',
        'availability_calendar', 'cancellation_policy', 'created_by', 'updated_by',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function pickupLocation()
    {
        return $this->belongsTo(Location::class, 'pickup_location_id');
    }

    public function dropoff_location_id()
    {
        return $this->belongsTo(Location::class, 'dropoff_location_id');
    }

    public function priceTiers()
    {
        return $this->hasMany(CarPriceTier::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'item_id')
            ->where('category', 'car');
    }
}
