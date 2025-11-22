<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourItineraryTranslation extends Model
{
    protected $table = 'tour_itinerary_translations';

    protected $fillable = [
        'tour_itinerary_id',
        'locale',
        'title',
        'description',
        'location',
        'duration',
    ];

    public function tourItinerary(): BelongsTo
    {
        return $this->belongsTo(TourItinerary::class);
    }
}
