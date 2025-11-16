<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    use HasFactory;
    use Translatable;

    protected $fillable = [
        'user_id',
        'tour_id',
        'status',
        'answered_by',
        'answered_at',
    ];

    public $translatedAttributes = [
        'question',
        'answer',
    ];

    protected function casts(): array
    {
        return [
            'answered_at' => 'datetime',
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

    public function answeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'answered_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAnswered($query)
    {
        return $query->where('status', 'answered');
    }

    public function scopeByTour($query, int $tourId)
    {
        return $query->where('tour_id', $tourId);
    }

    public function answer(int $userId, string $answer): void
    {
        $this->update([
            'status' => 'answered',
            'answered_by' => $userId,
            'answered_at' => now(),
        ]);

        $this->translateOrNew(app()->getLocale())->answer = $answer;
        $this->save();
    }
}
