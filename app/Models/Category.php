<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
      use HasFactory, SoftDeletes;

    protected $fillable = ['title', 'slug', 'image', 'description'];


    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function cars()
    {
        return $this->hasMany(Car::class, 'category_id');
    }

    public function tours()
    {
        return $this->hasMany(Tour::class, 'category_id');
    }

    public function flights()
    {
        return $this->hasMany(Flight::class, 'category_id');
    }

    public function hotels()
    {
        return $this->hasMany(Hotel::class, 'category_id');
    }
}
