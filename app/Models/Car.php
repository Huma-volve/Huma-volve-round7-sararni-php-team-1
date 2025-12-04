<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Car extends Model implements HasMedia
{
    use InteractsWithMedia;


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

    public function dropoffLocation()
    {
        return $this->belongsTo(Location::class, 'dropoff_location_id');
    }


    public function priceTiers() {
            return $this->hasMany(CarPriceTier::class, 'car_id', 'id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('car_images')
            ->useDisk('public');
    }


    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(200)
            ->nonQueued();
    }

    public function getImagesAttribute()
    {
        return $this->getMedia('car_images')->map(function ($media) {
            return [
                'id' => $media->id,
                'url' => $media->getUrl(),
                'thumb' => $media->getUrl('thumb'),
            ];
        });
    }


    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'item_id')
            ->where('category', 'car');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class, 'item_id')
            ->where('category', 'car');
    }
}
