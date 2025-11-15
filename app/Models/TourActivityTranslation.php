<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourActivityTranslation extends Model
{
    protected $table = 'tour_activity_translations';

    protected $fillable = [
        'tour_activity_id',
        'locale',
        'name',
        'description',
    ];

    public function tourActivity(): BelongsTo
    {
        return $this->belongsTo(TourActivity::class);
    }
}
