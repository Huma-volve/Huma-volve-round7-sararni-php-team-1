<?php

namespace App\Models;

use App\Models\Car;
use Illuminate\Database\Eloquent\Model;

class CarPriceTier extends Model
{
    //
    protected $table = 'car_price_tires';

    protected $fillable =['car_id', 'from_hours', 'to_hours', 'price_per_hour', 'price_per_day'];
    
    public function car()
    {
        return $this->belongsTo(Car::class);
    }
}
