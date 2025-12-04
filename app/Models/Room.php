<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected  $guarded = [];

    protected $casts = [
         'occupancy' => 'array',
        'extras' => 'array',
        'area' => 'decimal:2',
    ];


    public function hotel(){
        return $this->belongsTo(Hotel::class);
    }


    public function ratePlans(){
        return $this->hasMany(RatePlan::class);
    }


    public function bookings()  {
        return $this->hasMany(Booking::class);
    }


}
