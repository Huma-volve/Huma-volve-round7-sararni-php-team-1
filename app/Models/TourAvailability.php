<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourAvailability extends Model
{
    use HasFactory;

    protected $fillable = [
        'tour_id',
        'date',
        'available_slots',
        'booked_slots',
        'price_override',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'is_active' => 'boolean',
            'price_override' => 'decimal:2',
        ];
    }

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->whereRaw('available_slots > booked_slots');
    }

    public function scopeByDate($query, string $date)
    {
        return $query->where('date', $date);
    }

    public function scopeByDateRange($query, string $startDate, string $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function isAvailable(int $participants): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $availableSlots = $this->available_slots - $this->booked_slots;

        return $availableSlots >= $participants;
    }

    public function getAvailableSlots(): int
    {
        return max(0, $this->available_slots - $this->booked_slots);
    }

    public function bookSlots(int $count): bool
    {
        $this->lockForUpdate();

        $availableSlots = $this->available_slots - $this->booked_slots;

        if ($availableSlots < $count) {
            return false;
        }

        $this->increment('booked_slots', $count);

        return true;
    }

    public function releaseSlots(int $count): void
    {
        $this->lockForUpdate();
        $this->decrement('booked_slots', $count);
    }
}
