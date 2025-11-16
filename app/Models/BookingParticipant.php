<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'title',
        'first_name',
        'last_name',
        'date_of_birth',
        'passport_number',
        'passport_expiry',
        'nationality',
        'email',
        'phone',
        'special_requests',
        'seat_number',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'passport_expiry' => 'date',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
