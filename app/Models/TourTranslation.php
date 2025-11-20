<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourTranslation extends Model
{
    protected $table = 'tour_translations';

    protected $fillable = [
        'tour_id',
        'locale',
        'name',
        'description',
        'short_description',
        'highlights',
        'meeting_point',
        'cancellation_policy',
        'terms_conditions',
    ];

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }
}
