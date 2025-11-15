<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'tour_id',
        'booking_number',
        'tour_date',
        'tour_time',
        'adults_count',
        'children_count',
        'infants_count',
        'adult_price',
        'child_price',
        'infant_price',
        'discount_amount',
        'total_amount',
        'paid_amount',
        'status',
        'payment_status',
        'payment_method',
        'payment_reference',
        'special_requests',
        'cancellation_reason',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'tour_date' => 'date',
            'cancelled_at' => 'datetime',
            'adult_price' => 'decimal:2',
            'child_price' => 'decimal:2',
            'infant_price' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByTour($query, int $tourId)
    {
        return $query->where('tour_id', $tourId);
    }

    public function calculateTotal(): float
    {
        $adultTotal = $this->adult_price * $this->adults_count;
        $childTotal = ($this->child_price ?? 0) * $this->children_count;
        $infantTotal = ($this->infant_price ?? 0) * $this->infants_count;

        return $adultTotal + $childTotal + $infantTotal - $this->discount_amount;
    }

    public function markAsPaid(string $paymentMethod, ?string $paymentReference = null): void
    {
        $this->update([
            'payment_status' => 'paid',
            'payment_method' => $paymentMethod,
            'payment_reference' => $paymentReference,
            'paid_amount' => $this->total_amount,
        ]);
    }

    public function cancel(?string $reason = null): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancellation_reason' => $reason,
            'cancelled_at' => now(),
        ]);

        // Release booked slots
        $availability = $this->tour->availability()
            ->where('date', $this->tour_date)
            ->first();

        if ($availability) {
            $totalParticipants = $this->adults_count + $this->children_count + $this->infants_count;
            $availability->releaseSlots($totalParticipants);
        }
    }

    public function confirm(): void
    {
        $this->update([
            'status' => 'confirmed',
        ]);
    }

    public function generateBookingNumber(): string
    {
        $year = now()->format('Y');
        $random = Str::upper(Str::random(6));

        return "TOUR-{$year}-{$random}";
    }

    public function canBeCancelled(): bool
    {
        if ($this->status === 'cancelled' || $this->status === 'completed') {
            return false;
        }

        // Check cancellation policy from tour
        // For now, allow cancellation if status is pending or confirmed
        return in_array($this->status, ['pending', 'confirmed']);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($booking) {
            if (empty($booking->booking_number)) {
                $booking->booking_number = $booking->generateBookingNumber();
            }
        });
    }
}
