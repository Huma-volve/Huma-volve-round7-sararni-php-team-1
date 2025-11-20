<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingFlight extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'flight_id',
        'flight_leg_id',
        'flight_seat_id',
        'participant_id',
        'class_id',
        'direction',
        'price'
    ];

    protected $casts = [
        'direction' => 'string'
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function flight(): BelongsTo
    {
        return $this->belongsTo(Flight::class);
    }

    public function flightLeg(): BelongsTo
    {
        return $this->belongsTo(FlightLeg::class);
    }

    public function flightSeat(): BelongsTo
    {
        return $this->belongsTo(FlightSeat::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(BookingParticipant::class, 'participant_id');
    }
}