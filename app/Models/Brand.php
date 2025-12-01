<?php

namespace App\Models;

use App\Models\Car;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Brand extends Model implements HasMedia
{
    use SoftDeletes;
    use InteractsWithMedia;


    //
    protected $fillable = ['name'];
    protected $appends = ['images'];

    public function cars(){
        return $this->hasMany(Car::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('brand_images')
            ->useDisk('public');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(200)
            ->nonQueued(); 
    }

    public function getImagesAttribute()
    {
        return $this->getMedia('brand_images')->map(function ($media) {
            return [
                'id' => $media->id,
                'url' => $media->getUrl(),
                'thumb' => $media->getUrl('thumb'),
            ];
        });
    }
}
