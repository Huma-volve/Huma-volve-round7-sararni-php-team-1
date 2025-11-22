<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Tour extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use SoftDeletes;
    use Translatable;

    protected $fillable = [
        'category_id',
        'slug',
        'duration_days',
        'duration_nights',
        'max_participants',
        'min_participants',
        'adult_price',
        'child_price',
        'infant_price',
        'discount_percentage',
        'status',
        'rating',
        'total_reviews',
        'total_bookings',
        'is_featured',
        'sort_order',
        'location_lat',
        'location_lng',
        'included',
        'excluded',
        'languages',
        'difficulty',
        'provider_info',
        'tags',
        'transport_included',
        'pickup_zones',
        'created_by',
        'updated_by',
    ];

    public $translatedAttributes = [
        'name',
        'description',
        'short_description',
        'highlights',
        'meeting_point',
        'cancellation_policy',
        'terms_conditions',
    ];

    protected function casts(): array
    {
        return [
            'included' => 'array',
            'excluded' => 'array',
            'languages' => 'array',
            'tags' => 'array',
            'pickup_zones' => 'array',
            'provider_info' => 'array',
            'is_featured' => 'boolean',
            'transport_included' => 'boolean',
            'rating' => 'decimal:2',
            'adult_price' => 'decimal:2',
            'child_price' => 'decimal:2',
            'infant_price' => 'decimal:2',
            'discount_percentage' => 'decimal:2',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('main_image')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);

        $this->addMediaCollection('gallery')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->sharpen(10)
            ->performOnCollections('main_image', 'gallery');

        $this->addMediaConversion('preview')
            ->width(800)
            ->height(600)
            ->performOnCollections('main_image', 'gallery');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'item_id')
            ->where('category', 'tour');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class, 'item_id')
            ->where('category', 'tour');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function itineraries(): HasMany
    {
        return $this->hasMany(TourItinerary::class)->orderBy('day_number')->orderBy('sort_order');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(TourActivity::class)->orderBy('sort_order');
    }

    public function availability(): HasMany
    {
        return $this->hasMany(TourAvailability::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeAvailable($query, ?string $date = null)
    {
        if ($date) {
            return $query->whereHas('availability', function ($q) use ($date) {
                $q->where('date', $date)
                    ->where('is_active', true)
                    ->whereRaw('available_slots > booked_slots');
            });
        }

        return $query->whereHas('availability', function ($q) {
            $q->where('is_active', true)
                ->whereRaw('available_slots > booked_slots');
        });
    }

    public function scopeByDifficulty($query, string $difficulty)
    {
        return $query->where('difficulty', $difficulty);
    }

    public function scopeByLocation($query, float $lat, float $lng, float $radius = 50)
    {
        // Simple distance calculation (Haversine formula approximation)
        return $query->whereRaw(
            '(6371 * acos(cos(radians(?)) * cos(radians(location_lat)) * cos(radians(location_lng) - radians(?)) + sin(radians(?)) * sin(radians(location_lat)))) <= ?',
            [$lat, $lng, $lat, $radius]
        );
    }

    public function calculatePrice(int $adults, int $children = 0, int $infants = 0, ?string $date = null): array
    {
        $adultPrice = $this->adult_price;
        $childPrice = $this->child_price ?? 0;
        $infantPrice = $this->infant_price ?? 0;

        // Check for date-specific price override
        if ($date) {
            $availability = $this->availability()
                ->where('date', $date)
                ->where('is_active', true)
                ->first();

            if ($availability && $availability->price_override) {
                $adultPrice = $availability->price_override;
            }
        }

        $adultTotal = $adultPrice * $adults;
        $childTotal = $childPrice * $children;
        $infantTotal = $infantPrice * $infants;
        $subtotal = $adultTotal + $childTotal + $infantTotal;

        $discountAmount = 0;
        if ($this->discount_percentage > 0) {
            $discountAmount = ($subtotal * $this->discount_percentage) / 100;
        }

        $total = $subtotal - $discountAmount;

        return [
            'adult_price' => $adultPrice,
            'child_price' => $childPrice,
            'infant_price' => $infantPrice,
            'adults' => $adults,
            'children' => $children,
            'infants' => $infants,
            'adult_total' => $adultTotal,
            'child_total' => $childTotal,
            'infant_total' => $infantTotal,
            'subtotal' => $subtotal,
            'discount_percentage' => $this->discount_percentage,
            'discount_amount' => $discountAmount,
            'total' => $total,
            'currency' => config('app.currency', 'USD'),
        ];
    }

    public function isAvailable(string $date, int $participants): bool
    {
        $availability = $this->availability()
            ->where('date', $date)
            ->where('is_active', true)
            ->first();

        if (! $availability) {
            return false;
        }

        $availableSlots = $availability->available_slots - $availability->booked_slots;

        return $availableSlots >= $participants;
    }

    public function getRating(): float
    {
        return (float) $this->rating;
    }

    public function updateRating(): void
    {
        $approvedReviews = $this->reviews()->where('status', 'approved')->get();

        if ($approvedReviews->isEmpty()) {
            $this->update([
                'rating' => 0,
                'total_reviews' => 0,
            ]);

            return;
        }

        $averageRating = $approvedReviews->avg('rating');
        $this->update([
            'rating' => round($averageRating, 2),
            'total_reviews' => $approvedReviews->count(),
        ]);
    }

    public function getAvailableDates(string $startDate, string $endDate): Collection
    {
        return $this->availability()
            ->whereBetween('date', [$startDate, $endDate])
            ->where('is_active', true)
            ->whereRaw('available_slots > booked_slots')
            ->orderBy('date')
            ->get()
            ->map(function ($availability) {
                return [
                    'date' => $availability->date,
                    'available_slots' => $availability->available_slots - $availability->booked_slots,
                    'price_override' => $availability->price_override,
                ];
            });
    }

    public function getShareUrl(): string
    {
        return url("/tours/{$this->slug}");
    }
}
