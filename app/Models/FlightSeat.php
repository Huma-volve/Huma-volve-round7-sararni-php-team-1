<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FlightSeat extends Model
{
    use HasFactory;

    protected $fillable = [
        'flight_id',
        'class_id',
        'flight_leg_id',
        'seat_number',
        'status',
        'price'
    ];

    protected $casts = [
        'status' => 'string'
    ];

    public function flight(): BelongsTo
    {
        return $this->belongsTo(Flight::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function bookingFlights(): HasMany
    {
        return $this->hasMany(BookingFlight::class);
    }
    public function leg(): BelongsTo
    {
        return $this->belongsTo(FlightLeg::class, 'flight_leg_id');
    }
}
