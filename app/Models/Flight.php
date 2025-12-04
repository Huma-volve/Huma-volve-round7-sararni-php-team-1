<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Flight extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'flight_number',
        'carrier_id',
        'aircraft_id',
        'destination_id',
        'origin_id',
        'arrival_time',
        'departure_time',
        'duration_minutes',
    ];

    protected function casts(): array
    {
        return [
            'arrival_time' => 'datetime',
            'departure_time' => 'datetime',
        ];
    }

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

    public function origin(): BelongsTo
    {

        return $this->belongsTo(Location::class, 'origin_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'item_id')
            ->where('category', 'flight');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class, 'item_id')
            ->where('category', 'flight');
         return $this->belongsTo(Airport::class, 'origin_id');
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Airport::class, 'destination_id');
     }
}
