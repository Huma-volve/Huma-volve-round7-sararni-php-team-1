<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RatePlan extends Model
{
       protected  $guarded = [];

        protected $casts = [
        'pricing_rules' => 'array',
        'extras' => 'array',
        'refundable' => 'boolean',
        'base_price' => 'decimal:2',
    ];


    public function room(){
        return $this->belongsTo(Room::class);
    }
}
