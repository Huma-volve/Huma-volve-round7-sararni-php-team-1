<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Favorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category',
        'item_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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

    public function scopeByTour($query, int $tourId)
    {
        return $query->where('category', 'tour')->where('item_id', $tourId);
    }
}
