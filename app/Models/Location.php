<?php

namespace App\Models;

use App\Models\Car;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Location extends Model
{
    //
    use SoftDeletes;

    protected $fillable = ['city','country','latitude','longitude'];

    public function pickupCars()
    {
        return $this->hasMany(Car::class, 'pickup_location_id');
    }

    public function dropoffCars()
    {
        return $this->hasMany(Car::class, 'dropoff_location_id');
    }


        public function hotels()
        {
            return $this->hasMany(Hotel::class);
        }

}
