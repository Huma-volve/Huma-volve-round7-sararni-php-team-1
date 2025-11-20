<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassModel extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'class_name'
    ];

    protected $casts = [
        'class_name' => 'string'
    ];

    public function flightClasses(): HasMany
    {
        return $this->hasMany(FlightClass::class, 'class_id');
    }

    public function flightSeats(): HasMany
    {
        return $this->hasMany(FlightSeat::class, 'class_id');
    }

    public function bookingFlights(): HasMany
    {
        return $this->hasMany(BookingFlight::class, 'class_id');
    }
}