<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Constants\BookingStatus;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const STATUS_PENDING = BookingStatus::PENDING;
    public const STATUS_CONFIRMED = BookingStatus::CONFIRMED;
    public const STATUS_CANCELLED = BookingStatus::CANCELLED;


    protected $fillable = [
        'user_id',
        'booking_reference',
        'category',
        'item_id',
        'room_id',
        'rate_plan_id',
        'status',
        'total_price',
        'currency',
        'payment_status',
        'payment_method',
        'payment_reference',
        'booking_date',
        'booking_time',
        'check_in_date',
        'check_out_date',
        'pickup_date',
        'dropoff_date',
        'special_requests',
        'cancellation_reason',
        'cancelled_at',
        'cancelled_by',
    ];

    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'check_in_date' => 'date',
            'check_out_date' => 'date',
            'pickup_date' => 'date',
            'dropoff_date' => 'date',
            'cancelled_at' => 'datetime',
            'total_price' => 'decimal:2',
        ];
    }

        public function user() :BelongsTo

    {
        return $this->belongsTo(User::class);
    }


    protected $casts = [
    'guest_details' => 'array',
   ];


    public function details(): HasOne
    {
        return $this->hasOne(BookingDetail::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(BookingParticipant::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

     public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the item (tour/hotel/car/flight) based on category
     */
    public function getItemAttribute()
    {
        return match ($this->category) {
            'tour' => Tour::find($this->item_id),
            'hotel' => Hotel::find($this->item_id),
            'car' => Car::find($this->item_id),
            'flight' => Flight::find($this->item_id),
            default => null,
        };
    }

    /**
     * Get tour relationship (for backward compatibility)
     */
    public function tour(): ?BelongsTo
    {
        if ($this->category === 'tour') {
            return $this->belongsTo(Tour::class, 'item_id');
        }

        return null;
    }

    // Scopes
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

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByItem($query, int $itemId)
    {
        return $query->where('item_id', $itemId);
    }

    // Methods
    public function markAsPaid(string $paymentMethod, ?string $paymentReference = null): void
    {
        $this->update([
            'payment_status' => 'paid',
            'payment_method' => $paymentMethod,
            'payment_reference' => $paymentReference,
        ]);
    }

    public function cancel(?string $reason = null, string $cancelledBy = 'user'): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancellation_reason' => $reason,
            'cancelled_at' => now(),
            'cancelled_by' => $cancelledBy,
        ]);

        // Release booked slots for tours
        if ($this->category === 'tour' && $this->item) {
            $tour = $this->item;
            $availability = $tour->availability()
                ->where('date', $this->pickup_date ?? $this->booking_date)
                ->first();

            if ($availability && $this->relationLoaded('details') && $this->details) {
                $meta = $this->details->meta;
                $totalParticipants = ($meta['adults_count'] ?? 0) + ($meta['children_count'] ?? 0) + ($meta['infants_count'] ?? 0);
                $availability->releaseSlots($totalParticipants);
            }
        }
    }

    public function confirm(): void
    {
        $this->update([
            'status' => 'confirmed',
        ]);
    }

    public function generateBookingReference(): string
    {
        $year = now()->format('Y');
        $random = Str::upper(Str::random(6));
        $prefix = match ($this->category ?? 'tour') {
            'tour' => 'TOUR',
            'hotel' => 'HOTEL',
            'car' => 'CAR',
            'flight' => 'FLIGHT',
            default => 'BOOK',
        };

        return "{$prefix}-{$year}-{$random}";
    }

    public function canBeCancelled(): bool
    {
        if ($this->status === 'cancelled' || $this->status === 'completed') {
            return false;
        }

        return in_array($this->status, ['pending', 'confirmed']);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($booking) {
            if (empty($booking->booking_reference)) {
                $booking->booking_reference = $booking->generateBookingReference();
            }
        });
    }


}
