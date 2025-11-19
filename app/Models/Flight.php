<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Flight extends Model
{
    use HasFactory;

    protected $fillable = [
        'flight_number',
        'carrier_id',
        'aircraft_id'
    ];

    public function carrier(): BelongsTo
    {
        return $this->belongsTo(Carrier::class);
    }

    public function aircraft(): BelongsTo
    {
        return $this->belongsTo(Aircraft::class);
    }

    public function flightClasses(): HasMany
    {
        return $this->hasMany(FlightClass::class);
    }

    public function flightLegs(): HasMany
    {
        return $this->hasMany(FlightLeg::class);
    }

    public function flightSeats(): HasMany
    {
        return $this->hasMany(FlightSeat::class);
    }

    public function bookingFlights(): HasMany
    {
        return $this->hasMany(BookingFlight::class);
    }
}

