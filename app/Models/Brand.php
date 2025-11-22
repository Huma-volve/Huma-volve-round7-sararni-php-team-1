<?php

namespace App\Models;

use App\Models\Car;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use SoftDeletes;

    //
    protected $fillable = ['name'];

    public function cars(){
        return $this->hasMany(Car::class);
    }
}
