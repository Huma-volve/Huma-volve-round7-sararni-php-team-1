<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Airport extends Model
{
    use HasFactory;

    protected $fillable = [
        'airport_code',
        'airport_name',
        'city',
        'country',
        'latitude',
        'longitude'
    ];

    public function originFlights(): HasMany
    {
        return $this->hasMany(FlightLeg::class, 'origin_airport_id');
    }

    public function destinationFlights(): HasMany
    {
        return $this->hasMany(FlightLeg::class, 'destination_airport_id');
    }
}