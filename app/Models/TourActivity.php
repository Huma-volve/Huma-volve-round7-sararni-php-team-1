<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class TourActivity extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use Translatable;

    protected $fillable = [
        'tour_id',
        'activity_type',
        'sort_order',
    ];

    public $translatedAttributes = [
        'name',
        'description',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->sharpen(10)
            ->performOnCollections('image');

        $this->addMediaConversion('preview')
            ->width(800)
            ->height(600)
            ->performOnCollections('image');
    }

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }
}
