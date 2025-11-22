<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewTranslation extends Model
{
    protected $table = 'review_translations';

    protected $fillable = [
        'review_id',
        'locale',
        'title',
        'comment',
    ];

    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }
}
